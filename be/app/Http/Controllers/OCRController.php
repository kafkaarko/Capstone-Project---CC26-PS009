<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Jobs\ProcessOCR;
use Illuminate\Support\Facades\Cache;

class OCRController extends Controller
{
    // =========================================================
    // VIEW + FILTER + PAGINATION
    // =========================================================
    public function index(Request $request)
    {
        $userId = auth()->id();
        if (!$userId) abort(403);

        $query = Transaction::query()
            ->where('user_id', $userId);

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $key = 'transactions_' . $userId . '_' . md5(json_encode($request->only(['date_from', 'date_to', 'category', 'page'])));

        $data = Cache::remember($key, 0, function () use ($query) {
            return $query->latest('created_at')
                ->paginate(20)
                ->withQueryString();
        });

        return view('index', compact('data'));
    }

    // =========================================================
    // UPLOAD (NO OCR HERE 🔥)
    // =========================================================
    public function upload(Request $request)
{
    $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096'
    ]);

    $userId = auth()->id();
    if (!$userId) abort(403);

    $path = $request->file('image')->store('images', 'public');

    $transaction = Transaction::create([
        'user_id'          => $userId,
        'image'            => $path,
        'raw_text'         => null,
        'price_output'     => 0,
        'transaction_date' => now(),
        'category'         => 'lainnya',
    
    ]);

    (new ProcessOCR(
    $transaction->id,
    storage_path('app/public/' . $path)
))->handle();

    return redirect()->route('upload.page')
        ->with('success', 'Upload berhasil! OCR sedang diproses 🔥');
}

    // =========================================================
    // DASHBOARD
    // =========================================================
    public function dashboard()
    {
        $userId = auth()->id();
        if (!$userId) abort(403);

        $user = auth()->user();

        $total = Transaction::where('user_id', $userId)
    // ->where('status', 'done') // ← tambahkan
    ->sum('price_output');
       $transactions = Transaction::where('user_id', $userId)
    // ->where('status', 'done') // ← tambahkan
    ->orderBy('transaction_date')
    ->get()
    ->groupBy(fn($item) => \Carbon\Carbon::parse($item->transaction_date)->format('Y-m-d'));

  

        $chartLabels = [];
        $chartData   = [];  

        foreach ($transactions as $date => $items) {
            $chartLabels[] = \Carbon\Carbon::parse($date)->isoFormat('D MMM');
            $chartData[]   = (int) $items->sum('price_output');
        }

$categoryBreakdown = Transaction::where('user_id', $userId)
    // ->where('status', 'done') // ← tambahkan
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
            'categoryBreakdown'
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