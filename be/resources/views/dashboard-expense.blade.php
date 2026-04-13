<x-app-layout>
    <x-slot name="header">
        <h2 class="text-white font-semibold text-lg">Dashboard Pengeluaran</h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* Metric cards */
        .metric-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .metric-card {
            background: #0d1f3c;
            border: 1px solid rgba(42, 111, 196, 0.25);
            border-radius: 12px;
            padding: 20px 24px;
        }
        .metric-card .label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #64748b;
            margin-bottom: 8px;
        }
        .metric-card .value {
            font-size: 26px;
            font-weight: 600;
            color: #f87171;
        }
        .metric-card .value.safe { color: #34d399; }
        .metric-card .value.neutral { color: #60a5fa; }

        /* Budget card */
        .section-card {
            background: #0d1f3c;
            border: 1px solid rgba(42, 111, 196, 0.25);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }
        .section-card .card-title {
            font-size: 13px;
            font-weight: 600;
            color: #94a3b8;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .budget-row {
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .budget-row input[type=number] {
            flex: 1;
            background: #0a1628;
            border: 1px solid rgba(42, 111, 196, 0.3);
            border-radius: 8px;
            padding: 9px 14px;
            font-size: 14px;
            color: #e2e8f0;
            outline: none;
        }
        .budget-row input[type=number]:focus {
            border-color: #2a6fc4;
        }
        .btn-save {
            background: #2a6fc4;
            color: #fff;
            border: none;
            padding: 9px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            white-space: nowrap;
            transition: background 0.15s;
        }
        .btn-save:hover { background: #1e5aaa; }

        /* Over budget warning */
        .over-budget {
            margin-top: 14px;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 500;
        }

        /* Progress bar */
        .budget-progress {
            margin-top: 14px;
        }
        .progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #64748b;
            margin-bottom: 6px;
        }
        .progress-track {
            background: rgba(42, 111, 196, 0.15);
            border-radius: 99px;
            height: 6px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            border-radius: 99px;
            transition: width 0.4s ease;
        }
        .progress-fill.safe { background: #34d399; }
        .progress-fill.warning { background: #fbbf24; }
        .progress-fill.danger { background: #f87171; }

        /* Chart */
        .chart-wrapper {
            position: relative;
            height: 280px;
        }
    </style>

    {{-- Metric Cards --}}
    <div class="metric-grid">
        <div class="metric-card">
            <div class="label">Total Pengeluaran</div>
            <div class="value">
                Rp {{ number_format($total, 0, ',', '.') }}
            </div>
        </div>

        @if($budget > 0)
        <div class="metric-card">
            <div class="label">Budget</div>
            <div class="value neutral">
                Rp {{ number_format($budget, 0, ',', '.') }}
            </div>
        </div>
        <div class="metric-card">
            <div class="label">Sisa Budget</div>
            @php $sisa = $budget - $total; @endphp
            <div class="value {{ $sisa >= 0 ? 'safe' : '' }}">
                Rp {{ number_format(abs($sisa), 0, ',', '.') }}
                {{ $sisa < 0 ? '(lebih)' : '' }}
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
                       placeholder="Masukkan budget..." />
                <button type="submit" class="btn-save">Simpan</button>
            </div>
        </form>

        @if($budget > 0)
            @php
                $pct = min(($total / $budget) * 100, 100);
                $fillClass = $pct < 70 ? 'safe' : ($pct < 100 ? 'warning' : 'danger');
            @endphp
            <div class="budget-progress">
                <div class="progress-label">
                    <span>{{ number_format($pct, 1) }}% terpakai</span>
                    <span>Rp {{ number_format($budget, 0, ',', '.') }}</span>
                </div>
                <div class="progress-track">
                    <div class="progress-fill {{ $fillClass }}"
                         style="width: {{ $pct }}%"></div>
                </div>
            </div>

            @if($total > $budget)
                <div class="over-budget">
                    ⚠️ Over Budget! Pengeluaran melebihi batas yang ditentukan.
                </div>
            @endif
        @endif
    </div>

    {{-- Chart --}}
    <div class="section-card">
        <div class="card-title">Pengeluaran Harian</div>
        <div class="chart-wrapper">
            <canvas id="chart"></canvas>
        </div>
    </div>

    <script>
    const ctx = document.getElementById('chart');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Pengeluaran Harian',
                data: @json($chartData),
                borderColor: '#2a6fc4',
                backgroundColor: 'rgba(42, 111, 196, 0.1)',
                borderWidth: 2,
                pointBackgroundColor: '#60a5fa',
                pointRadius: 4,
                tension: 0.4,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: { color: '#94a3b8', font: { size: 12 } }
                }
            },
            scales: {
                x: {
                    ticks: { color: '#64748b', font: { size: 11 } },
                    grid: { color: 'rgba(42, 111, 196, 0.1)' }
                },
                y: {
                    ticks: {
                        color: '#64748b',
                        font: { size: 11 },
                        callback: v => 'Rp ' + v.toLocaleString('id-ID')
                    },
                    grid: { color: 'rgba(42, 111, 196, 0.1)' }
                }
            }
        }
    });
    </script>

</x-app-layout>