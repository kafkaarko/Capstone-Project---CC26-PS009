<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class OCRController extends Controller
{
    // =========================================================
    // VIEW — Upload Page
    // =========================================================
public function index(Request $request)
{
    $userId = auth()->id();
    if (!$userId) abort(403);

    $query = Transaction::query()
        ->where('user_id', $userId);

    // FILTER
    if ($request->filled('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    if ($request->filled('category')) {
        $query->where('category', $request->category);
    }

    // SELECT ONLY WHAT YOU NEED 🔥
    $key = 'transactions_' . $userId . '_' . md5(json_encode($request->all()));

    $data = Cache::remember($key, 30, function () use ($query) {
        return $query->latest('created_at')
            ->paginate(20)
            ->withQueryString();
    });

    return view('index', compact('data'));
}

    // =========================================================
    // UPLOAD + OCR PIPELINE
    // =========================================================
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096'
        ]);

        // Simpan gambar original
        $path = $request->file('image')->store('images', 'public');
        $fullPath = storage_path('app/public/' . $path);

        // Preprocess image
        $processedPath = storage_path('app/public/processed_' . time() . '_' . uniqid() . '.jpg');

        try {
            Image::make($fullPath)
                ->resize(null, 1200, function ($c) { $c->aspectRatio(); })
                ->greyscale()
                ->contrast(40)
                ->brightness(10)
                ->sharpen(15)
                ->save($processedPath);
        } catch (\Exception $e) {
            Log::error('Image preprocess failed: ' . $e->getMessage());
            // fallback ke original
            $processedPath = $fullPath;
        }

        // OCR
        $ocrText = '';
        try {
            $ocr = new TesseractOCR($processedPath);
            $ocrText = $ocr
                ->lang('eng', 'ind')
                ->psm(6)
                ->config('tessedit_char_whitelist', '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz:.,/ RpIDR-')
                ->run();
        } catch (\Exception $e) {
            Log::error('OCR failed: ' . $e->getMessage());
        }

        // Cleanup processed file
        if ($processedPath !== $fullPath && file_exists($processedPath)) {
            @unlink($processedPath);
        }

        $cleanText = strtoupper($ocrText);

        // Extract total
        $total = 0;
        if (strpos($cleanText, 'IDR') !== false) {
            if (preg_match('/IDR\s?([\d,\.]+)/', $cleanText, $m)) {
                $total = $this->normalizeNumber($m[1]);
            }
        }
        if ($total == 0) {
            $total = $this->extractTotal($cleanText);
        }

        // Extract date
        $date = $this->extractDate($cleanText);

        // Auto category
        $category = $this->detectCategory($cleanText);

        Log::info('=== OCR RESULT ===', [
            'raw'      => $ocrText,
            'total'    => $total,
            'date'     => $date,
            'category' => $category,
        ]);

        Transaction::create([
            'user_id'          => auth()->id() ?? 1,
            'image'            => $path,
            'raw_text'         => $ocrText,
            'price_output'     => $total,
            'transaction_date' => $date ?? now(),
            'category'         => $category,
        ]);

        return redirect()->route('upload.page')->with('success', 'Struk berhasil diupload!');
    }

    // =========================================================
    // NORMALIZE NUMBER
    // =========================================================
    private function normalizeNumber(string $value): int
    {
        $value = trim($value);

        // Format Indo: 89.000,00
        if (preg_match('/^\d{1,3}(\.\d{3})+(,\d+)?$/', $value)) {
            $value = str_replace('.', '', $value);
            $value = explode(',', $value)[0];
        }
        // Format US: 89,000.00
        elseif (preg_match('/^\d{1,3}(,\d{3})+(\.\d+)?$/', $value)) {
            $value = str_replace(',', '', $value);
            $value = explode('.', $value)[0];
        }
        // Fallback: strip semua non-digit
        else {
            $value = preg_replace('/[^0-9]/', '', $value);
        }

        return (int) $value;
    }

    // =========================================================
    // EXTRACT TOTAL
    // =========================================================
    private function extractTotal(string $text): int
    {
        $keywords = [
            'TOTAL BAYAR',
            'TOTAL PEMBAYARAN',
            'JUMLAH BAYAR',
            'JUMLAH LUNAS',
            'GRAND TOTAL',
            'TOTAL',
            'LUNAS',
            'PAYMENT',
            'AMOUNT',
        ];

        foreach ($keywords as $key) {
            // cari keyword lalu angka setelahnya (dalam 60 char)
            if (preg_match('/' . preg_quote($key, '/') . '[^0-9]{0,30}([\d]{1,3}(?:[.,]\d{3})+(?:[.,]\d{2})?|\d{4,})/i', $text, $m)) {
                $result = $this->normalizeNumber($m[1]);
                if ($result > 0) return $result;
            }
        }

        // Fallback: ambil angka terbesar yang kelihatan seperti uang
        preg_match_all('/\d{1,3}(?:[.,]\d{3})+(?:[.,]\d{2})?/', $text, $matches);
        if (!empty($matches[0])) {
            $numbers = array_map(fn($n) => $this->normalizeNumber($n), $matches[0]);
            return max($numbers);
        }

        return 0;
    }

    // =========================================================
    // EXTRACT DATE
    // =========================================================
    private function extractDate(string $text): ?string
    {
        $months = 'JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC';

        // Format: 10 APR 2025 atau 10 APR 25 12:34
        if (preg_match('/(\d{1,2})\s+(' . $months . ')[A-Z]*[,\s]+(\d{2,4})(?:\s+(\d{2}:\d{2}))?/i', $text, $m)) {
            $day   = str_pad($m[1], 2, '0', STR_PAD_LEFT);
            $month = $this->monthToNumber(strtoupper(substr($m[2], 0, 3)));
            $year  = strlen($m[3]) == 2 ? '20' . $m[3] : $m[3];
            $time  = $m[4] ?? '00:00';
            return "$year-$month-$day $time:00";
        }

        // Format: YYYY-MM-DD
        if (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $text, $m)) {
            return "$m[1]-$m[2]-$m[3]";
        }

        // Format: DD/MM/YYYY
        if (preg_match('/(\d{2})\/(\d{2})\/(\d{4})/', $text, $m)) {
            return "$m[3]-$m[2]-$m[1]";
        }

        // Format: MM/DD/YYYY (US)
        if (preg_match('/(\d{2})\/(\d{2})\/(\d{4})/', $text, $m)) {
            // ambiguous — asumsikan DD/MM
            return "$m[3]-$m[2]-$m[1]";
        }

        return null;
    }

    // =========================================================
    // AUTO DETECT CATEGORY
    // =========================================================
    private function detectCategory(string $text): string
    {
        $map = [
            'makanan'   => ['MCDONALD', 'KFC', 'BURGER', 'PIZZA', 'RESTO', 'RESTAURANT', 'CAFE', 'KOPI', 'COFFEE', 'FOOD', 'MAKAN', 'WARUNG', 'BAKSO', 'MIE', 'NASI', 'INDOMARET FRESH'],
            'transport' => ['GRAB', 'GOJEK', 'GOCAR', 'GRABCAR', 'TAXI', 'OJEK', 'BENSIN', 'BBM', 'PERTAMINA', 'SHELL', 'TOLL', 'PARKIR', 'TRANSJAKARTA', 'BUS', 'KERETA'],
            'belanja'   => ['INDOMARET', 'ALFAMART', 'SUPERMARKET', 'HYPERMART', 'CARREFOUR', 'LOTTEMART', 'GIANT', 'MINIMARKET', 'TOKO', 'SHOP'],
            'kesehatan' => ['APOTEK', 'APOTIK', 'PHARMACY', 'KLINIK', 'KLINIC', 'RUMAH SAKIT', 'RS ', 'DOKTER', 'OBAT', 'VITAMIN'],
            'hiburan'   => ['CINEMA', 'BIOSKOP', 'NETFLIX', 'SPOTIFY', 'GAME', 'STEAM', 'PLAYSTATION'],
            'tagihan'   => ['PLN', 'PDAM', 'TELKOM', 'INDIHOME', 'WIFI', 'INTERNET', 'LISTRIK', 'AIR', 'GAS'],
        ];

        foreach ($map as $category => $keywords) {
            foreach ($keywords as $kw) {
                if (strpos($text, $kw) !== false) {
                    return $category;
                }
            }
        }

        return 'lainnya';
    }

    // =========================================================
    // MONTH HELPER
    // =========================================================
    private function monthToNumber(string $month): string
    {
        return match($month) {
            'JAN' => '01', 'FEB' => '02', 'MAR' => '03',
            'APR' => '04', 'MAY' => '05', 'JUN' => '06',
            'JUL' => '07', 'AUG' => '08', 'SEP' => '09',
            'OCT' => '10', 'NOV' => '11', 'DEC' => '12',
            default => '01',
        };
    }

    // =========================================================
    // DASHBOARD
    // =========================================================
    public function dashboard()
    {
        $userId = auth()->id() ?? 1;
        $user   = auth()->user();

        $total = Transaction::where('user_id', $userId)->sum('price_output');

        $transactions = Transaction::where('user_id', $userId)
            ->orderBy('transaction_date')
            ->get()
            ->groupBy(fn($item) => \Carbon\Carbon::parse($item->transaction_date)->format('Y-m-d'));

        $chartLabels = [];
        $chartData   = [];

        foreach ($transactions as $date => $items) {
            $chartLabels[] = \Carbon\Carbon::parse($date)->isoFormat('D MMM');
            $chartData[]   = (int) $items->sum('price_output');
        }

        // Breakdown per kategori
        $categoryBreakdown = Transaction::where('user_id', $userId)
            ->selectRaw('category, SUM(price_output) as subtotal, COUNT(*) as count')
            ->groupBy('category')
            ->orderByDesc('subtotal')
            ->get();

        $budget = $user->budget ?? 0;

        return view('dashboard-expense', compact(
            'total',
            'chartLabels',
            'chartData',
            'budget',
            'categoryBreakdown',
        ));
    }

    // =========================================================
    // SET BUDGET
    // =========================================================
    public function setBudget(Request $request)
    {
        $request->validate([
            'budget' => 'required|numeric|min:0|max:999999999'
        ]);

        $user = auth()->user();
        $user->budget = (int) $request->budget;
        $user->save();

        return back()->with('success', 'Budget berhasil diupdate!');
    }
}