@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Laporan Penjualan</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
                    <div class="breadcrumb-item">Laporan</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Laporan & Analisis POS</h2>
                <p class="section-lead">Pantau performa keuangan, omzet, metode pembayaran terpopuler, dan menu terlaris.</p>

                <!-- Filter Tanggal -->
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary">
                            <div class="card-body py-3">
                                <form method="GET" action="{{ route('reports.index') }}" class="row align-items-center">
                                    <div class="form-group col-md-4 mb-0">
                                        <label class="d-block">Tanggal Mulai</label>
                                        <input type="date" class="form-control" name="start_date" value="{{ $startDate->format('Y-m-d') }}" max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                    </div>
                                    <div class="form-group col-md-4 mb-0">
                                        <label class="d-block">Tanggal Akhir</label>
                                        <input type="date" class="form-control" name="end_date" value="{{ $endDate->format('Y-m-d') }}" max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                    </div>
                                    <div class="form-group col-md-4 mb-0 pt-4 text-right">
                                        <button class="btn btn-primary btn-lg px-4" type="submit"><i class="fas fa-filter mr-1"></i>Terapkan Filter</button>
                                        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-lg px-3 ml-1"><i class="fas fa-sync"></i> Reset</a>
                                    </div>
                                    <div class="form-group col-md-12 mt-3 mb-0 border-top pt-3">
                                        <span class="mr-2 text-muted" style="font-size: 0.9rem;"><i class="fas fa-bolt text-warning mr-1"></i> Filter Cepat:</span>
                                        <button type="button" class="btn btn-sm btn-outline-primary mr-1" onclick="setFilter('today')">Hari Ini</button>
                                        <button type="button" class="btn btn-sm btn-outline-primary mr-1" onclick="setFilter('week')">7 Hari Terakhir</button>
                                        <button type="button" class="btn btn-sm btn-outline-primary mr-1" onclick="setFilter('month')">Bulan Ini</button>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="setFilter('year')">Tahun Ini</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GitHub-Style Heatmap (Aktivitas Transaksi) -->
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-header pb-0 border-bottom-0">
                                <h4><i class="fas fa-calendar-check text-primary mr-2"></i> Intensitas Transaksi (90 Hari Terakhir)</h4>
                            </div>
                            <div class="card-body">
                                <style>
                                    .heatmap-wrapper {
                                        overflow-x: auto;
                                        padding-bottom: 10px;
                                    }
                                    .heatmap-grid {
                                        display: grid;
                                        grid-template-rows: repeat(7, 1fr);
                                        grid-auto-flow: column;
                                        gap: 4px;
                                        width: max-content;
                                    }
                                    .heatmap-cell {
                                        width: 14px;
                                        height: 14px;
                                        border-radius: 3px;
                                        position: relative;
                                        cursor: pointer;
                                        transition: transform 0.1s;
                                    }
                                    .heatmap-cell:hover {
                                        transform: scale(1.2);
                                        z-index: 10;
                                        box-shadow: 0 0 5px rgba(0,0,0,0.2);
                                    }
                                    /* Color scale */
                                    .heatmap-level-0 { background-color: #ebedf0; }
                                    .heatmap-level-1 { background-color: #9be9a8; }
                                    .heatmap-level-2 { background-color: #40c463; }
                                    .heatmap-level-3 { background-color: #30a14e; }
                                    .heatmap-level-4 { background-color: #216e39; }
                                    
                                    /* Dark Mode support adjustments if needed */
                                    body.dark-mode .heatmap-level-0 { background-color: #2d333b; }
                                    body.dark-mode .heatmap-level-1 { background-color: #0e4429; }
                                    body.dark-mode .heatmap-level-2 { background-color: #006d32; }
                                    body.dark-mode .heatmap-level-3 { background-color: #26a641; }
                                    body.dark-mode .heatmap-level-4 { background-color: #39d353; }
                                </style>
                                
                                <div class="heatmap-wrapper">
                                    <div class="heatmap-grid">
                                        @foreach($heatmapData as $cell)
                                            <div class="heatmap-cell heatmap-level-{{ $cell['level'] }}" 
                                                 title="{{ $cell['formatted'] }}: {{ $cell['count'] }} Transaksi"
                                                 data-toggle="tooltip" 
                                                 data-placement="top">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-end mt-3" style="font-size: 12px; color: #6c757d;">
                                    <span class="mr-2">Sedikit</span>
                                    <div class="heatmap-cell heatmap-level-0 mx-1" style="width: 12px; height: 12px;"></div>
                                    <div class="heatmap-cell heatmap-level-1 mx-1" style="width: 12px; height: 12px;"></div>
                                    <div class="heatmap-cell heatmap-level-2 mx-1" style="width: 12px; height: 12px;"></div>
                                    <div class="heatmap-cell heatmap-level-3 mx-1" style="width: 12px; height: 12px;"></div>
                                    <div class="heatmap-cell heatmap-level-4 mx-1" style="width: 12px; height: 12px;"></div>
                                    <span class="ml-2">Banyak</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rekap Ringkas Keuangan -->
                <div class="row mt-3">
                    <!-- Total Pendapatan -->
                    <div class="col-lg-3 col-md-6 col-12 mb-4">
                        <div class="card card-statistic-1 mb-0 shadow-sm" style="border-radius: 10px;">
                            <div class="card-icon bg-primary" style="border-radius: 10px 0 0 10px;">
                                <i class="fas fa-wallet text-white" style="font-size: 1.5rem;"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header py-2">
                                    <h4>Total Omzet Bersih</h4>
                                </div>
                                <div class="card-body">
                                    Rp {{ number_format($summary->total_revenue ?? 0, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Transaksi -->
                    <div class="col-lg-3 col-md-6 col-12 mb-4">
                        <div class="card card-statistic-1 mb-0 shadow-sm" style="border-radius: 10px;">
                            <div class="card-icon bg-success" style="border-radius: 10px 0 0 10px;">
                                <i class="fas fa-shopping-bag text-white" style="font-size: 1.5rem;"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header py-2">
                                    <h4>Total Transaksi</h4>
                                </div>
                                <div class="card-body">
                                    {{ number_format($summary->total_transactions ?? 0, 0) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Pajak Terkumpul -->
                    <div class="col-lg-3 col-md-6 col-12 mb-4">
                        <div class="card card-statistic-1 mb-0 shadow-sm" style="border-radius: 10px;">
                            <div class="card-icon bg-warning" style="border-radius: 10px 0 0 10px;">
                                <i class="fas fa-university text-white" style="font-size: 1.5rem;"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header py-2">
                                    <h4>Pajak (PPN) Terkumpul</h4>
                                </div>
                                <div class="card-body">
                                    Rp {{ number_format($summary->total_taxes ?? 0, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Diskon -->
                    <div class="col-lg-3 col-md-6 col-12 mb-4">
                        <div class="card card-statistic-1 mb-0 shadow-sm" style="border-radius: 10px;">
                            <div class="card-icon bg-danger" style="border-radius: 10px 0 0 10px;">
                                <i class="fas fa-percent text-white" style="font-size: 1.5rem;"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header py-2">
                                    <h4>Diskon Terpakai</h4>
                                </div>
                                <div class="card-body">
                                    Rp {{ number_format($summary->total_discounts ?? 0, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grafik Performa & Detail Pembayaran -->
                <div class="row">
                    <!-- Revenue Chart -->
                    <div class="col-lg-8 col-12 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h4>Grafik Performa Penjualan</h4>
                            </div>
                            <div class="card-body">
                                <div style="position: relative; width: 100%; height: 320px;">
                                    <canvas id="reportingRevenueChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="col-lg-4 col-12 mb-4">
                        <div class="card shadow-sm" style="height: 100%;">
                            <div class="card-header">
                                <h4>Metode Pembayaran</h4>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    @forelse ($paymentMethods as $payment)
                                        @php
                                            $totalRev = $summary->total_revenue ?? 0;
                                            $percentage = $totalRev > 0 ? round(($payment->revenue / $totalRev) * 100) : 0;
                                        @endphp
                                        <li class="list-group-item px-0 py-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div>
                                                    <span class="badge badge-light text-uppercase mr-2" style="border: 1px solid var(--sp-border);">
                                                        {{ $payment->payment_method }}
                                                    </span>
                                                    <span class="text-muted" style="font-size: 0.85rem;">({{ $payment->count }}x Transaksi)</span>
                                                </div>
                                                <strong>Rp {{ number_format($payment->revenue, 0, ',', '.') }}</strong>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1" style="height: 6px; border-radius: 10px;">
                                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <span class="ml-2 text-muted" style="font-size: 0.75rem; font-weight: 700;">{{ $percentage }}%</span>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="list-group-item text-center text-muted px-0 py-4">
                                            Belum ada data pembayaran.
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Products / Menu Terlaris -->
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h4><i class="fas fa-fire mr-1 text-warning"></i> 5 Menu Terlaris</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 50px;">Rank</th>
                                                <th>Gambar</th>
                                                <th>Nama Menu</th>
                                                <th>Kategori</th>
                                                <th class="text-center">Jumlah Terjual</th>
                                                <th class="text-right">Total Pemasukan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($topProducts as $top)
                                                <tr>
                                                    <td class="text-center">
                                                        <span class="badge badge-{{ $loop->iteration <= 3 ? 'warning' : 'light' }}" style="font-weight: 700;">
                                                            #{{ $loop->iteration }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if ($top->product && $top->product->image)
                                                            <img src="{{ asset('storage/' . $top->product->image) }}" 
                                                                alt="{{ $top->product->name }}" 
                                                                class="rounded" 
                                                                style="width: 50px; height: 50px; object-fit: cover; border: 1.5px solid var(--sp-border);">
                                                        @else
                                                            <div class="rounded bg-light d-flex align-items-center justify-content-center"
                                                                style="width: 50px; height: 50px; border: 1.5px solid var(--sp-border); font-size: 0.75rem; color: var(--sp-text-muted);">
                                                                <i class="fas fa-utensils fa-lg"></i>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td><strong>{{ $top->product->name ?? 'Menu Terhapus' }}</strong></td>
                                                    <td>
                                                        <span class="badge badge-light" style="border: 1px solid var(--sp-border); font-weight: 500;">
                                                            {{ $top->product->category->name ?? '-' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center"><strong>{{ $top->qty_sold }}</strong> Porsi / Item</td>
                                                    <td class="text-right"><strong>Rp {{ number_format($top->revenue, 0, ',', '.') }}</strong></td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4">
                                                        Belum ada data penjualan menu terlaris.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi Tooltip Bootstrap untuk Heatmap
    if (typeof $ !== 'undefined' && $.fn.tooltip) {
        $('[data-toggle="tooltip"]').tooltip();
    }

    const ctx = document.getElementById('reportingRevenueChart');
    if (!ctx) return;

    const labels = {!! json_encode($labels) !!};
    const revenues = {!! json_encode($revenues) !!};

    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(102, 126, 234, 0.25)');
    gradient.addColorStop(1, 'rgba(102, 126, 234, 0.01)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels.length ? labels : ['No Data'],
            datasets: [{
                label: 'Pemasukan (Rp)',
                data: revenues.length ? revenues : [0],
                borderColor: '#667eea',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointHoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1a1a2e',
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function (context) {
                            return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#9ca3af', font: { size: 11 } }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,.03)' },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 11 },
                        callback: function (value) {
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + ' jt';
                            } else if (value >= 1000) {
                                return 'Rp ' + (value / 1000).toFixed(0) + ' k';
                            }
                            return 'Rp ' + value;
                        }
                    }
                }
            }
        }
    });
});

function setFilter(type) {
    const startInput = document.querySelector('input[name="start_date"]');
    const endInput = document.querySelector('input[name="end_date"]');
    
    // Gunakan zona waktu lokal browser untuk menghindari pergeseran hari
    const today = new Date();
    let start = new Date(today);
    let end = new Date(today);
    
    if (type === 'today') {
        // start dan end tetap hari ini
    } else if (type === 'week') {
        start.setDate(today.getDate() - 6); // 7 Hari terakhir
    } else if (type === 'month') {
        start = new Date(today.getFullYear(), today.getMonth(), 1); // Tanggal 1 bulan ini
        end = new Date(today.getFullYear(), today.getMonth() + 1, 0); // Tanggal terakhir bulan ini
    } else if (type === 'year') {
        start = new Date(today.getFullYear(), 0, 1); // 1 Jan tahun ini
        end = new Date(today.getFullYear(), 11, 31); // 31 Des tahun ini
    }
    
    // Format YYYY-MM-DD dengan menyesuaikan timezone lokal
    const formatDate = (date) => {
        const d = new Date(date);
        let month = '' + (d.getMonth() + 1);
        let day = '' + d.getDate();
        const year = d.getFullYear();

        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;

        return [year, month, day].join('-');
    };

    startInput.value = formatDate(start);
    endInput.value = formatDate(end);
    
    // Auto-submit form
    startInput.closest('form').submit();
}
</script>
@endpush
