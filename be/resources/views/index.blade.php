<x-app-layout>
    <x-slot name="header">
        <h2 class="text-white font-semibold text-lg">Expense Tracker OCR</h2>
    </x-slot>

    <style>
        .alert { padding: 11px 16px; border-radius: 8px; font-size: 13px; margin-bottom: 22px; }
        .alert-success { background: rgba(62,201,122,0.1); border: 1px solid rgba(62,201,122,0.3); color: #6ee7b7; }
        .alert-error   { background: rgba(248,113,113,0.1); border: 1px solid rgba(248,113,113,0.3); color: #fca5a5; }

        .upload-card {
            background: #0d1f3c;
            border: 1px solid rgba(26,160,120,0.25);
            border-radius: 12px;
            padding: 22px;
            margin-bottom: 18px;
        }
        .upload-row { display: flex; align-items: center; gap: 12px; }
        .upload-card input[type=file] {
            flex: 1;
            background: #0a1628;
            border: 1px solid rgba(26,160,120,0.25);
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 13px;
            color: #94a3b8;
            cursor: pointer;
        }
        .upload-card input[type=file]::file-selector-button {
            background: rgba(26,160,120,0.15);
            border: 1px solid rgba(26,160,120,0.35);
            border-radius: 6px;
            color: #3ec97a;
            font-size: 12px;
            padding: 4px 10px;
            cursor: pointer;
            margin-right: 10px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #1a6fc4, #1aa078);
            color: #fff; border: none;
            padding: 9px 22px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            white-space: nowrap;
        }
        .btn-primary:hover { opacity: 0.9; }
        .error-text { color: #f87171; font-size: 12px; margin-top: 8px; }

        /* ── Filter Card ── */
        .filter-card {
            background: #0d1f3c;
            border: 1px solid rgba(26,160,120,0.2);
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 18px;
        }
        .filter-row {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            flex-wrap: wrap;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .filter-group label {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #64748b;
        }
        .filter-input {
            background: #0a1628;
            border: 1px solid rgba(26,160,120,0.25);
            border-radius: 8px;
            padding: 7px 12px;
            font-size: 13px;
            color: #e2e8f0;
            outline: none;
            height: 36px;
            /* style native date picker */
            color-scheme: dark;
        }
        .filter-input:focus { border-color: #1aa078; }
        .filter-select {
            background: #0a1628;
            border: 1px solid rgba(26,160,120,0.25);
            border-radius: 8px;
            padding: 7px 12px;
            font-size: 13px;
            color: #e2e8f0;
            outline: none;
            height: 36px;
            cursor: pointer;
        }
        .filter-select:focus { border-color: #1aa078; }
        .btn-filter {
            background: linear-gradient(135deg, #1a6fc4, #1aa078);
            color: #fff; border: none;
            padding: 0 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            height: 36px;
            white-space: nowrap;
        }
        .btn-filter:hover { opacity: 0.9; }
        .btn-reset {
            background: rgba(248,113,113,0.1);
            border: 1px solid rgba(248,113,113,0.25);
            color: #f87171;
            padding: 0 16px;
            border-radius: 8px;
            font-size: 13px;
            cursor: pointer;
            height: 36px;
            white-space: nowrap;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        .btn-reset:hover { background: rgba(248,113,113,0.18); }

        /* active filter badge */
        .filter-active-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 12px;
            flex-wrap: wrap;
        }
        .filter-tag {
            background: rgba(26,160,120,0.1);
            border: 1px solid rgba(26,160,120,0.25);
            color: #3ec97a;
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 20px;
        }
        .filter-result-count {
            font-size: 11px;
            color: #475569;
            margin-left: auto;
        }

        /* ── Table ── */
        .table-card {
            background: #0d1f3c;
            border: 1px solid rgba(26,160,120,0.2);
            border-radius: 12px;
            overflow: hidden;
        }
        .table-card table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .table-card thead tr {
            background: rgba(26,160,120,0.1);
            border-bottom: 1px solid rgba(26,160,120,0.2);
        }
        .table-card thead th {
            padding: 11px 15px;
            text-align: left;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #3ec97a;
        }
        .table-card tbody tr { border-bottom: 1px solid rgba(26,160,120,0.08); transition: background 0.1s; }
        .table-card tbody tr:last-child { border-bottom: none; }
        .table-card tbody tr:hover { background: rgba(26,160,120,0.06); }
        .table-card td { padding: 11px 15px; color: #cbd5e1; vertical-align: middle; }
        .td-num { color: #475569; font-size: 11px; }
        .td-price { color: #3ec97a; font-weight: 600; }
        .td-muted { color: #475569; font-size: 11px; }
        .badge-category {
            background: rgba(26,160,120,0.12);
            border: 1px solid rgba(26,160,120,0.3);
            color: #3ec97a;
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 20px;
            font-weight: 500;
            text-transform: capitalize;
        }
        .receipt-img {
            width: 44px; height: 44px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid rgba(26,160,120,0.2);
        }
        .empty-state { text-align: center; padding: 48px 16px; color: #475569; font-size: 14px; }

        /* total bawah tabel */
        .table-footer {
            padding: 12px 15px;
            border-top: 1px solid rgba(26,160,120,0.12);
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #64748b;
        }
        .table-footer .total-val {
            color: #3ec97a;
            font-weight: 600;
            font-size: 15px;
        }
    </style>

{{-- ALERT --}}
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

{{-- UPLOAD --}}
<div class="upload-card">
    <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="upload-row">
            <input type="file" name="image" accept="image/*" required />
            <button type="submit" class="btn-primary">Upload Struk</button>
        </div>
        @error('image')
            <p class="error-text">{{ $message }}</p>
        @enderror
    </form>
</div>

{{-- FILTER --}}
<div class="filter-card">
    <form method="GET" action="{{ route('upload.page') }}">
        <div class="filter-row">

            <div class="filter-group">
                <label>Upload Dari</label>
                <input type="date" name="date_from" class="filter-input"
                    value="{{ request('date_from') }}">
            </div>

            <div class="filter-group">
                <label>Upload Sampai</label>
                <input type="date" name="date_to" class="filter-input"
                    value="{{ request('date_to') }}">
            </div>

            <div class="filter-group">
                <label>Kategori</label>
                <select name="category" class="filter-select">
                    <option value="">Semua Kategori</option>
                    @foreach(['makanan','transport','belanja','kesehatan','hiburan','tagihan','lainnya'] as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                            {{ ucfirst($cat) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn-filter">Filter</button>

            @if(request()->hasAny(['date_from','date_to','category']))
                <a href="{{ route('upload.page') }}" class="btn-reset">Reset</a>
            @endif
        </div>

        {{-- FILTER INFO --}}
        @if(request()->hasAny(['date_from','date_to','category']))
            <div class="filter-active-bar">
                @if(request('date_from'))
                    <span class="filter-tag">
                        Dari: {{ \Carbon\Carbon::parse(request('date_from'))->isoFormat('D MMM YYYY') }}
                    </span>
                @endif

                @if(request('date_to'))
                    <span class="filter-tag">
                        Sampai: {{ \Carbon\Carbon::parse(request('date_to'))->isoFormat('D MMM YYYY') }}
                    </span>
                @endif

                @if(request('category'))
                    <span class="filter-tag">
                        Kategori: {{ ucfirst(request('category')) }}
                    </span>
                @endif

                <span class="filter-result-count">
                    {{ $data->total() }} total • halaman {{ $data->currentPage() }}
                </span>
            </div>
        @endif
    </form>
</div>

{{-- TABLE --}}
<div class="table-card">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Gambar</th>
                <th>Total</th>
                <th>Tanggal Transaksi</th>
                <th>Kategori</th>
                <th>Upload Time</th>
            </tr>
        </thead>

        <tbody>
            @forelse($data as $i => $item)
                <tr>
                    <td class="td-num">
                        {{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}
                    </td>

                    <td>
                        <img 
  src="{{ asset('storage/' . $item->image) }}"
  loading="lazy"
  class="receipt-img"
/>
                    </td>

                    <td class="td-price">
                        Rp {{ number_format($item->price_output ?? 0, 0, ',', '.') }}
                    </td>

                    <td>
                        {{ $item->transaction_date
                            ? \Carbon\Carbon::parse($item->transaction_date)->isoFormat('D MMM YYYY')
                            : '-' }}
                    </td>

                    <td>
                        <span class="badge-category">{{ $item->category }}</span>
                    </td>

                    <td class="td-muted">
                        {{ \Carbon\Carbon::parse($item->created_at)->isoFormat('D MMM YYYY, HH:mm') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-state">
                        🧾 Belum ada struk yang diupload
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- TOTAL --}}
    @if($data->count())
        <div class="table-footer">
            <span>Total ditampilkan:</span>
            <span class="total-val">
                Rp {{ number_format($data->sum('price_output'), 0, ',', '.') }}
            </span>
        </div>
    @endif

    {{-- PAGINATION --}}
    <div style="padding:15px;">
        {{ $data->onEachSide(1)->links() }}
    </div>
</div>



</x-app-layout>