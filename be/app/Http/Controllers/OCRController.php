<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Intervention\Image\ImageManagerStatic as Image;

class OCRController extends Controller
{
    // =========================================================
    // 🔥 VIEW
    // =========================================================
    public function index()
    {
        $data = Transaction::latest()->get();
        return view('index', compact('data'));
    }

    // =========================================================
    // 🔥 UPLOAD + OCR PIPELINE
    // =========================================================
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048'
        ]);

        // ======================
        // 🖼️ SIMPAN GAMBAR
        // ======================
        $path = $request->file('image')->store('images', 'public');
        $fullPath = storage_path('app/public/' . $path);

        // ======================
        // 🔥 PREPROCESS IMAGE
        // ======================
        $processedPath = storage_path('app/public/processed_' . time() . '.jpg');

        Image::make($fullPath)
            ->resize(null, 1000, function ($constraint) {
                $constraint->aspectRatio();
            })
            ->greyscale()
            ->contrast(50)
            ->brightness(15)
            ->sharpen(20)
            ->save($processedPath);

        // ======================
        // 🔥 OCR PROCESS
        // ======================
        $ocr = new TesseractOCR($processedPath);

        // $ocr->executable("C:\\Program Files\\Tesseract-OCR\\tesseract.exe");

        $ocrText = $ocr
            ->lang('eng')
            ->config('tessedit_char_whitelist', '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz:., RpIDR')
            ->run();

        $cleanText = strtoupper($ocrText);

        // ======================
        // 💰 EXTRACT TOTAL
        // ======================
        $total = 0;

        // PRIORITAS: format IDR (mobile banking)
        if (strpos($cleanText, 'IDR') !== false) {
            if (preg_match('/IDR\s?([\d,\.]+)/', $cleanText, $m)) {
                $total = $this->normalizeNumber($m[1]);
            }
        }

        // fallback ke regex umum
        if ($total == 0) {
            $total = $this->extractTotal($cleanText);
        }

        // ======================
        // 📅 EXTRACT DATE
        // ======================
        $date = $this->extractDate($cleanText);

        // ======================
        // 🧠 DEBUG LOG
        // ======================
        \Log::info('OCR RAW:', [$ocrText]);
        \Log::info('OCR CLEAN:', [$cleanText]);
        \Log::info('TOTAL:', [$total]);
        \Log::info('DATE:', [$date]);

        // ======================
        // 💾 SAVE
        // ======================
        Transaction::create([
            'user_id' => auth()->id() ?? 1,
            'image' => $path,
            'raw_text' => $ocrText,
            'price_output' => $total,
            'transaction_date' => $date ?? now(),
            'category' => 'lainnya'
        ]);

        return redirect('/')->with('success', 'Upload berhasil 🔥');
    }

    // =========================================================
    // 💰 NORMALIZE NUMBER (ANTI NGACO)
    // =========================================================
    private function normalizeNumber($value)
{
    $value = trim($value);

    // CASE 1: format Indo → 89.000,00
    if (preg_match('/\d+\.\d{3},\d{2}/', $value)) {
        $value = str_replace('.', '', $value); // hapus ribuan
        $value = explode(',', $value)[0];      // buang desimal
    }

    // CASE 2: format US → 67,100.00
    elseif (preg_match('/\d+,\d{3}\.\d{2}/', $value)) {
        $value = str_replace(',', '', $value); // hapus ribuan
        $value = explode('.', $value)[0];      // buang desimal
    }

    // CASE 3: hanya ribuan → 37.500 atau 37,500
    else {
        $value = preg_replace('/[^0-9]/', '', $value);
    }

    return (int) $value;
}

    // =========================================================
    // 💰 EXTRACT TOTAL
    // =========================================================
    private function extractTotal($text)
    {
        $text = strtoupper($text);

        $keywords = [
            'TOTAL',
            'TOTAL BAYAR',
            'JUMLAH LUNAS',
            'LUNAS',
            'PAYMENT'
        ];

        foreach ($keywords as $key) {
            if (preg_match("/$key.*?(\d{1,3}([.,]\d{3})+)/", $text, $m)) {
                return $this->normalizeNumber($m[1]);
            }
        }

        // fallback ambil angka terbesar
        preg_match_all('/\d{1,3}([.,]\d{3})+/', $text, $matches);

        $numbers = array_map(function ($n) {
            return $this->normalizeNumber($n);
        }, $matches[0]);

        return !empty($numbers) ? max($numbers) : 0;
    }

    // =========================================================
    // 📅 EXTRACT DATE
    // =========================================================
    private function extractDate($text)
    {
        $text = strtoupper($text);

        if (preg_match('/(\d{1,2})\s+(JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)[,\s]*(\d{2,4})?\s*(\d{2}:\d{2})?/', $text, $m)) {

            $day = str_pad($m[1], 2, '0', STR_PAD_LEFT);
            $month = $this->monthToNumber($m[2]);
            $year = $m[3] ?? date('Y');

            if (strlen($year) == 2) {
                $year = '20' . $year;
            }

            $time = $m[4] ?? '00:00';

            return "$year-$month-$day $time:00";
        }

        if (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $text, $m)) {
            return "$m[1]-$m[2]-$m[3]";
        }

        if (preg_match('/(\d{2})\/(\d{2})\/(\d{4})/', $text, $m)) {
            return "$m[3]-$m[2]-$m[1]";
        }

        return null;
    }

    // =========================================================
    // 📆 MONTH HELPER
    // =========================================================
    private function monthToNumber($month)
    {
        $map = [
            'JAN' => '01',
            'FEB' => '02',
            'MAR' => '03',
            'APR' => '04',
            'MAY' => '05',
            'JUN' => '06',
            'JUL' => '07',
            'AUG' => '08',
            'SEP' => '09',
            'OCT' => '10',
            'NOV' => '11',
            'DEC' => '12',
        ];

        return $map[$month] ?? '01';
    }

    // =========================================================
// 📊 DASHBOARD
// =========================================================
public function dashboard()
{
    $user = auth()->user();

    // total spending
    $total = Transaction::where('user_id', $user->id)->sum('price_output');

    // data chart per hari
    $transactions = Transaction::where('user_id', $user->id)
        ->orderBy('transaction_date')
        ->get()
        ->groupBy(function ($item) {
            return date('Y-m-d', strtotime($item->transaction_date));
        });

    $chartLabels = [];
    $chartData = [];

    foreach ($transactions as $date => $items) {
        $chartLabels[] = $date;
        $chartData[] = $items->sum('price_output');
    }

    $budget = $user->budget ?? 0;

    return view('dashboard-expense', compact(
        'total',
        'chartLabels',
        'chartData',
        'budget'
    ));
}

// =========================================================
// 💰 SET BUDGET
// =========================================================
public function setBudget(Request $request)
{
    $request->validate([
        'budget' => 'required|numeric|min:0'
    ]);

    $user = auth()->user();
    $user->budget = $request->budget;
    $user->save();

    return back()->with('success', 'Budget updated 🔥');
}
}