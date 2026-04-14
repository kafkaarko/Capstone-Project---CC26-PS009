<x-app-layout>
    <x-slot name="header">
        <h2 class="text-white font-semibold text-lg">Dashboard Pengeluaran</h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        .metric-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
            margin-bottom: 22px;
        }
        .metric-card {
            background: #0d1f3c;
            border: 1px solid rgba(26,160,120,0.2);
            border-radius: 12px;
            padding: 18px 20px;
        }
        .metric-card .lbl {
            font-size: 10px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.6px;
            color: #64748b; margin-bottom: 8px;
        }
        .metric-card .val { font-size: 22px; font-weight: 600; }
        .val-danger  { color: #f87171; }
        .val-teal    { color: #3ec97a; }
        .val-blue    { color: #60b8fa; }

        .section-card {
            background: #0d1f3c;
            border: 1px solid rgba(26,160,120,0.2);
            border-radius: 12px;
            padding: 22px;
            margin-bottom: 20px;
        }
        .card-title {
            font-size: 10px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.6px;
            color: #94a3b8; margin-bottom: 14px;
        }

        .budget-row { display: flex; gap: 10px; align-items: center; }
        .budget-row input[type=number] {
            flex: 1;
            background: #0a1628;
            border: 1px solid rgba(26,160,120,0.25);
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 13px;
            color: #e2e8f0;
            outline: none;
        }
        .budget-row input[type=number]:focus { border-color: #1aa078; }
        .btn-primary {
            background: linear-gradient(135deg, #1a6fc4, #1aa078);
            color: #fff; border: none;
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
        }
        .btn-primary:hover { opacity: 0.9; }

        .alert-success {
            background: rgba(62,201,122,0.1);
            border: 1px solid rgba(62,201,122,0.3);
            color: #6ee7b7;
            padding: 11px 16px; border-radius: 8px;
            font-size: 13px; margin-bottom: 20px;
        }

        .over-budget {
            margin-top: 12px;
            background: rgba(248,113,113,0.08);
            border: 1px solid rgba(248,113,113,0.25);
            color: #fca5a5;
            border-radius: 8px;
            padding: 9px 14px;
            font-size: 13px;
        }

        .progress-wrap { margin-top: 14px; }
        .progress-label {
            display: flex; justify-content: space-between;
            font-size: 11px; color: #64748b; margin-bottom: 6px;
        }
        .progress-track {
            background: rgba(26,160,120,0.1);
            border-radius: 99px; height: 6px; overflow: hidden;
        }
        .progress-fill {
            height: 100%; border-radius: 99px;
            background: linear-gradient(90deg, #1a6fc4, #3ec97a);
            transition: width 0.4s ease;
        }
        .progress-fill.warning { background: linear-gradient(90deg, #f59e0b, #ef4444); }

        .chart-wrap { position: relative; height: 260px; }

        /* Category breakdown */
        .cat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 10px;
        }
        .cat-item {
            background: rgba(26,160,120,0.06);
            border: 1px solid rgba(26,160,120,0.15);
            border-radius: 8px;
            padding: 12px 14px;
        }
        .cat-name {
            font-size: 11px; color: #94a3b8;
            text-transform: capitalize; margin-bottom: 4px;
        }
        .cat-val { font-size: 15px; font-weight: 600; color: #3ec97a; }
        .cat-count { font-size: 10px; color: #475569; margin-top: 2px; }
    </style>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    {{-- Metric Cards --}}
    <div class="metric-grid">
        <div class="metric-card">
            <div class="lbl">Total Pengeluaran</div>
            <div class="val val-danger">Rp {{ number_format($total, 0, ',', '.') }}</div>
        </div>
        @if($budget > 0)
        <div class="metric-card">
            <div class="lbl">Budget</div>
            <div class="val val-blue">Rp {{ number_format($budget, 0, ',', '.') }}</div>
        </div>
        @php $sisa = $budget - $total; @endphp
        <div class="metric-card">
            <div class="lbl">Sisa Budget</div>
            <div class="val {{ $sisa >= 0 ? 'val-teal' : 'val-danger' }}">
                Rp {{ number_format(abs($sisa), 0, ',', '.') }}
                @if($sisa < 0) <span style="font-size:13px">(lebih)</span> @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Budget Form --}}
    <div class="section-card">
        <div class="card-title">Set Budget</div>
        <form method="POST" action="{{ route('set.budget') }}">
            @csrf
            <div class="budget-row">
                <input type="number" name="budget"
                       value="{{ $budget }}"
                       placeholder="Masukkan budget..." min="0" />
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>

        @if($budget > 0)
            @php
                $pct      = min(($total / $budget) * 100, 100);
                $fillWarn = $pct >= 80 ? 'warning' : '';
            @endphp
            <div class="progress-wrap">
                <div class="progress-label">
                    <span>{{ number_format($pct, 1) }}% terpakai</span>
                    <span>Rp {{ number_format($budget, 0, ',', '.') }}</span>
                </div>
                <div class="progress-track">
                    <div class="progress-fill {{ $fillWarn }}" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @if($total > $budget)
                <div class="over-budget">⚠️ Over Budget! Pengeluaran melebihi batas yang ditentukan.</div>
            @endif
        @endif
    </div>

    {{-- Chart --}}
    <div class="section-card">
        <div class="card-title">Pengeluaran Harian</div>
        <div class="chart-wrap">
            <canvas id="chart"></canvas>
        </div>
    </div>

    {{-- Category Breakdown --}}
    @if($categoryBreakdown->isNotEmpty())
    <div class="section-card">
        <div class="card-title">Breakdown Kategori</div>
        <div class="cat-grid">
            @foreach($categoryBreakdown as $cat)
            <div class="cat-item">
                <div class="cat-name">{{ $cat->category }}</div>
                <div class="cat-val">Rp {{ number_format($cat->subtotal, 0, ',', '.') }}</div>
                <div class="cat-count">{{ $cat->count }} transaksi</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <script>
    const ctx = document.getElementById('chart');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Pengeluaran Harian',
                data: @json($chartData),
                borderColor: '#1aa078',
                backgroundColor: 'rgba(26,160,120,0.08)',
                borderWidth: 2,
                pointBackgroundColor: '#3ec97a',
                pointBorderColor: '#0d1f3c',
                pointBorderWidth: 2,
                pointRadius: 5,
                tension: 0.4,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { color: '#94a3b8', font: { size: 11 } } },
                tooltip: {
                    callbacks: {
                        label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                    }
                }
            },
            scales: {
                x: {
                    ticks: { color: '#64748b', font: { size: 10 } },
                    grid: { color: 'rgba(26,160,120,0.07)' }
                },
                y: {
                    ticks: {
                        color: '#64748b', font: { size: 10 },
                        callback: v => 'Rp ' + v.toLocaleString('id-ID')
                    },
                    grid: { color: 'rgba(26,160,120,0.07)' }
                }
            }
        }
    });
    </script>
</x-app-layout>