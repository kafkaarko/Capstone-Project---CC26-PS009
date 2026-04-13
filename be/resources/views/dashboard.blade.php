<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Expense</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 p-6">

<div class="max-w-5xl mx-auto">

    <!-- HEADER -->
    <h1 class="text-3xl font-bold mb-6">📊 Dashboard Pengeluaran</h1>

    <!-- TOTAL -->
    <div class="bg-white p-6 rounded-xl shadow mb-6">
        <h2 class="text-gray-500">Total Pengeluaran</h2>
        <p class="text-3xl font-bold text-red-500">
            Rp {{ number_format($total, 0, ',', '.') }}
        </p>
    </div>

    <!-- BUDGET -->
    <div class="bg-white p-6 rounded-xl shadow mb-6">
        <form method="POST" action="{{ route('set.budget') }}">
            @csrf
            <label class="block mb-2 font-semibold">Set Budget</label>

            <div class="flex gap-3">
                <input type="number" name="budget"
                    value="{{ $budget }}"
                    class="border p-2 w-full rounded">

                <button class="bg-blue-600 text-white px-4 rounded">
                    Save
                </button>
            </div>
        </form>

        <!-- WARNING -->
        @if($budget > 0 && $total > $budget)
            <div class="mt-4 text-red-600 font-bold">
                ⚠️ Over Budget! Santai bro… tapi dompet nangis 😭
            </div>
        @endif
    </div>

    <!-- CHART -->
    <div class="bg-white p-6 rounded-xl shadow">
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
            borderWidth: 2
        }]
    }
});
</script>

</body>
</html>