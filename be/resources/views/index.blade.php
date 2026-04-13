<x-app-layout>
    <x-slot name="header">
        <h2 class="text-white font-semibold text-lg">Expense Tracker OCR</h2>
    </x-slot>

    <style>
        .page-desc { font-size: 13px; color: #64748b; margin-top: 2px; }

        /* Success alert */
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 24px;
        }

        /* Upload card */
        .upload-card {
            background: #0d1f3c;
            border: 1px solid rgba(42, 111, 196, 0.25);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 28px;
        }
        .upload-card .upload-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .upload-card input[type=file] {
            flex: 1;
            background: #0a1628;
            border: 1px solid rgba(42, 111, 196, 0.3);
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 13px;
            color: #94a3b8;
            outline: none;
            cursor: pointer;
        }
        .upload-card input[type=file]::file-selector-button {
            background: rgba(42, 111, 196, 0.2);
            border: 1px solid rgba(42, 111, 196, 0.4);
            border-radius: 6px;
            color: #93c5fd;
            font-size: 12px;
            padding: 4px 10px;
            cursor: pointer;
            margin-right: 10px;
        }
        .btn-upload {
            background: #2a6fc4;
            color: #fff;
            border: none;
            padding: 9px 22px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            white-space: nowrap;
            transition: background 0.15s;
        }
        .btn-upload:hover { background: #1e5aaa; }
        .error-text { color: #f87171; font-size: 12px; margin-top: 8px; }

        /* Table card */
        .table-card {
            background: #0d1f3c;
            border: 1px solid rgba(42, 111, 196, 0.25);
            border-radius: 12px;
            overflow: hidden;
        }
        .table-card table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .table-card thead tr {
            background: rgba(42, 111, 196, 0.15);
            border-bottom: 1px solid rgba(42, 111, 196, 0.25);
        }
        .table-card thead th {
            padding: 12px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #60a5fa;
        }
        .table-card tbody tr {
            border-bottom: 1px solid rgba(42, 111, 196, 0.1);
            transition: background 0.12s;
        }
        .table-card tbody tr:last-child { border-bottom: none; }
        .table-card tbody tr:hover { background: rgba(42, 111, 196, 0.08); }
        .table-card td {
            padding: 12px 16px;
            color: #cbd5e1;
            vertical-align: middle;
        }
        .td-num { color: #64748b; font-size: 12px; }
        .td-price { color: #34d399; font-weight: 600; }
        .td-date { color: #e2e8f0; }
        .td-time { color: #475569; font-size: 11px; }
        .badge-category {
            background: rgba(42, 111, 196, 0.15);
            border: 1px solid rgba(42, 111, 196, 0.3);
            color: #93c5fd;
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 20px;
            font-weight: 500;
        }
        .receipt-img {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid rgba(42, 111, 196, 0.3);
        }
        .empty-state {
            text-align: center;
            padding: 48px 16px;
            color: #475569;
            font-size: 14px;
        }
        .empty-icon { font-size: 32px; margin-bottom: 8px; }
    </style>

    {{-- Success message --}}
    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    {{-- Upload form --}}
    <div class="upload-card">
        <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="upload-row">
                <input type="file" name="image" accept="image/*" />
                <button type="submit" class="btn-upload">Upload</button>
            </div>
            @error('image')
                <p class="error-text">{{ $message }}</p>
            @enderror
        </form>
    </div>

    {{-- Data table --}}
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
                        <td class="td-num">{{ $i + 1 }}</td>

                        <td>
                            <img src="{{ asset('storage/' . $item->image) }}"
                                 class="receipt-img" alt="struk">
                        </td>

                        <td class="td-price">
                            Rp {{ number_format($item->price_output ?? 0, 0, ',', '.') }}
                        </td>

                        <td class="td-date">
                            {{ $item->transaction_date ?? '-' }}
                        </td>

                        <td>
                            <span class="badge-category">{{ $item->category }}</span>
                        </td>

                        <td class="td-time">
                            {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-state">
                            <div class="empty-icon">🧾</div>
                            Belum ada data struk yang diupload
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-app-layout>