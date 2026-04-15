<?php

namespace App\Jobs;

use App\Models\Transaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Log;

class ProcessOCR implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $transactionId;
    protected $imagePath;

    public function __construct($transactionId, $imagePath)
    {
        $this->transactionId = $transactionId;
        $this->imagePath = $imagePath;
    }

    public function handle()
    {
        $transaction = Transaction::find($this->transactionId);
        if (!$transaction) return;
        Log::info('OCR JOB START', [
    'transaction_id' => $this->transactionId
]);

        // update status → processing
    

        $fullPath = $this->imagePath;
        $processedPath = storage_path('app/public/processed_' . time() . '_' . uniqid() . '.jpg');

        // =========================================================
        // PREPROCESS IMAGE
        // =========================================================
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
            $processedPath = $fullPath;
        }

        // =========================================================
        // OCR PROCESS
        // =========================================================
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

        // cleanup
        if ($processedPath !== $fullPath && file_exists($processedPath)) {
            unlink($processedPath);
        }

        $cleanText = strtoupper($ocrText);

        // =========================================================
        // EXTRACT DATA
        // =========================================================
        $total = $this->extractTotal($cleanText);
        $date = $this->extractDate($cleanText);
        $category = $this->detectCategory($cleanText);

        Log::info('=== OCR RESULT ===', [
            'raw'      => $ocrText,
            'total'    => $total,
            'date'     => $date,
            'category' => $category,
        ]);

        // =========================================================
        // UPDATE DB
        // =========================================================
        $transaction->update([
            'raw_text'         => $ocrText,
            'price_output'     => $total,
            'transaction_date' => $date ?? now(),
            'category'         => $category,

        ]);
    }

    // =========================================================
    // NORMALIZE NUMBER
    // =========================================================
    private function normalizeNumber(string $value): int
    {
        $value = trim($value);

        if (preg_match('/^\d{1,3}(\.\d{3})+(,\d+)?$/', $value)) {
            $value = str_replace('.', '', $value);
            $value = explode(',', $value)[0];
        } elseif (preg_match('/^\d{1,3}(,\d{3})+(\.\d+)?$/', $value)) {
            $value = str_replace(',', '', $value);
            $value = explode('.', $value)[0];
        } else {
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
            'TOTAL', 'GRAND TOTAL', 'JUMLAH', 'BAYAR',
            'PAYMENT', 'AMOUNT', 'LUNAS'
        ];

        foreach ($keywords as $key) {
            if (preg_match('/' . preg_quote($key, '/') . '[^0-9]{0,30}([\d.,]+)/i', $text, $m)) {
                $result = $this->normalizeNumber($m[1]);
                if ($result > 0) return $result;
            }
        }

        preg_match_all('/\d{1,3}(?:[.,]\d{3})+/', $text, $matches);

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
        if (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $text, $m)) {
            return "$m[1]-$m[2]-$m[3]";
        }

        if (preg_match('/(\d{2})\/(\d{2})\/(\d{4})/', $text, $m)) {
            return "$m[3]-$m[2]-$m[1]";
        }

        return null;
    }

    // =========================================================
    // DETECT CATEGORY
    // =========================================================
    private function detectCategory(string $text): string
    {
        $map = [
            'makanan'   => ['MCD', 'KFC', 'RESTO', 'CAFE'],
            'transport' => ['GRAB', 'GOJEK', 'PERTAMINA'],
            'belanja'   => ['INDOMARET', 'ALFAMART'],
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

    public function failed(\Throwable $e)
{
    Log::error('OCR job failed: ' . $e->getMessage());

    Transaction::find($this->transactionId)
        ?->update(['status' => 'failed']);
}
}