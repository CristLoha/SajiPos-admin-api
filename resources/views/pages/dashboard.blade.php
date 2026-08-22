@extends('layouts.app')

@section('title', 'Dashboard')

@push('style')
<style>
    /* Greeting */
    .sp-greeting {
        margin-bottom: 0.25rem;
    }
    .sp-greeting h2 {
        font-size: 1.55rem;
        font-weight: 700;
        color: var(--sp-heading, #1a1a2e);
        margin: 0;
    }
    .sp-greeting p {
        color: #6c757d;
        font-size: 0.925rem;
        margin: 0;
    }

    /* Stat Cards */
    .sp-stat-card {
        display: flex;
        align-items: center;
        gap: 1.1rem;
        background: #fff;
        border-radius: 1rem;
        padding: 1.35rem 1.25rem;
        box-shadow: 0 2px 12px rgba(0,0,0,.06);
        transition: transform .2s ease, box-shadow .2s ease;
        height: 100%;
    }
    .sp-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,.10);
    }
    .sp-stat-icon {
        width: 56px;
        height: 56px;
        min-width: 56px;
        border-radius: .85rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        color: #fff;
    }
    .sp-stat-info {
        flex: 1;
        min-width: 0;
    }
    .sp-stat-value {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--sp-heading, #1a1a2e);
        line-height: 1.25;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .sp-stat-label {
        font-size: .8rem;
        color: #6c757d;
        font-weight: 500;
        margin-top: 2px;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    /* Chart Card */
    .sp-chart-card {
        background: #fff;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 2px 12px rgba(0,0,0,.06);
    }
    .sp-chart-card .sp-card-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--sp-heading, #1a1a2e);
        margin-bottom: 1.25rem;
    }
    .sp-chart-card .sp-card-title i {
        margin-right: .45rem;
        opacity: .7;
    }

    /* Orders Table */
    .sp-table-card {
        background: #fff;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 2px 12px rgba(0,0,0,.06);
    }
    .sp-table-card .sp-card-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--sp-heading, #1a1a2e);
        margin-bottom: 1.25rem;
    }
    .sp-table-card .sp-card-title i {
        margin-right: .45rem;
        opacity: .7;
    }
    .sp-order-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .sp-order-table thead th {
        background: #f8f9fd;
        color: #6c757d;
        font-size: .75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .5px;
        padding: .75rem 1rem;
        border: none;
    }
    .sp-order-table thead th:first-child {
        border-radius: .6rem 0 0 .6rem;
    }
    .sp-order-table thead th:last-child {
        border-radius: 0 .6rem .6rem 0;
    }
    .sp-order-table tbody td {
        padding: .85rem 1rem;
        font-size: .9rem;
        color: #3a3a4e;
        border-bottom: 1px solid #f0f0f5;
        vertical-align: middle;
    }
    .sp-order-table tbody tr:last-child td {
        border-bottom: none;
    }
    .sp-order-table tbody tr {
        transition: background .15s ease;
    }
    .sp-order-table tbody tr:hover {
        background: #f8f9fd;
    }
    .sp-order-no {
        font-weight: 600;
        color: var(--sp-heading, #1a1a2e);
    }

    /* Status Badges */
    .sp-badge {
        display: inline-block;
        padding: .3rem .7rem;
        border-radius: 50rem;
        font-size: .75rem;
        font-weight: 600;
        letter-spacing: .3px;
    }
    .sp-badge-success {
        background: rgba(67, 233, 123, .15);
        color: #1fa34d;
    }
    .sp-badge-warning {
        background: rgba(255, 186, 0, .15);
        color: #c48800;
    }
    .sp-badge-info {
        background: rgba(79, 172, 254, .15);
        color: #1a7fd4;
    }
    .sp-badge-danger {
        background: rgba(245, 87, 108, .15);
        color: #d63050;
    }

    /* Menu Terlaris */
    .sp-menu-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .sp-menu-rank {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: .85rem 0;
        border-bottom: 1px solid #f0f0f5;
        transition: background .15s ease;
    }
    .sp-menu-rank:last-child {
        border-bottom: none;
    }
    .sp-menu-rank:hover {
        background: #f8f9fd;
        border-radius: .5rem;
        padding-left: .5rem;
        padding-right: .5rem;
    }
    .sp-rank-num {
        width: 32px;
        height: 32px;
        min-width: 32px;
        border-radius: .5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: .85rem;
        color: #fff;
    }
    .sp-rank-num.rank-1 { background: linear-gradient(135deg, #f7971e, #ffd200); }
    .sp-rank-num.rank-2 { background: linear-gradient(135deg, #a8a8a8, #c0c0c0); color: #fff; }
    .sp-rank-num.rank-3 { background: linear-gradient(135deg, #cd7f32, #e6a858); }
    .sp-rank-num.rank-4,
    .sp-rank-num.rank-5 { background: #eef0f8; color: #6c757d; }
    .sp-rank-info {
        flex: 1;
        min-width: 0;
    }
    .sp-rank-name {
        font-weight: 600;
        font-size: .925rem;
        color: var(--sp-heading, #1a1a2e);
    }
    .sp-rank-sold {
        font-size: .8rem;
        color: #6c757d;
    }
    .sp-rank-count {
        font-weight: 700;
        font-size: 1rem;
        color: var(--sp-heading, #1a1a2e);
    }

    @media (max-width: 767.98px) {
        .sp-stat-value { font-size: 1.1rem; }
        .sp-order-table { font-size: .82rem; }
    }
</style>
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Dashboard</h1>
        </div>

        {{-- Greeting --}}
        <div class="sp-greeting mb-4">
            <h2>Selamat datang kembali! 👋</h2>
            <p>Berikut ringkasan aktivitas restoran hari ini.</p>
        </div>

        {{-- Stat Cards --}}
        <div class="row">
            <!-- Pendapatan Hari Ini -->
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="sp-stat-card">
                    <div class="sp-stat-icon" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="sp-stat-info">
                        <div class="sp-stat-value">Rp {{ number_format($revenueToday, 0, ',', '.') }}</div>
                        <div class="sp-stat-label">Pendapatan Hari Ini</div>
                        @if($revenueToday == 0)
                            <div style="font-size: 0.7rem; color: #a0a0a0; margin-top: 4px; font-weight: 500;"><i class="fas fa-info-circle"></i> Belum ada transaksi</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Total Pesanan Hari Ini -->
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="sp-stat-card">
                    <div class="sp-stat-icon" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="sp-stat-info">
                        <div class="sp-stat-value">{{ $ordersTodayCount }}</div>
                        <div class="sp-stat-label">Pesanan Hari Ini</div>
                        @if($ordersTodayCount == 0)
                            <div style="font-size: 0.7rem; color: #a0a0a0; margin-top: 4px; font-weight: 500;"><i class="fas fa-info-circle"></i> Belum ada pesanan</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Menu Aktif -->
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="sp-stat-card">
                    <div class="sp-stat-icon" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="sp-stat-info">
                        <div class="sp-stat-value">{{ $activeProductsCount }}</div>
                        <div class="sp-stat-label">Menu Aktif</div>
                    </div>
                </div>
            </div>

            <!-- Jumlah Karyawan -->
            <div class="col-lg-3 col-md-6 col-12 mb-4">
                <div class="sp-stat-card">
                    <div class="sp-stat-icon" style="background: linear-gradient(135deg, #43e97b, #38f9d7);">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="sp-stat-info">
                        <div class="sp-stat-value">{{ $employeesCount }}</div>
                        <div class="sp-stat-label">Jumlah Karyawan</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Revenue Chart --}}
        <div class="row">
            <div class="col-12 mb-4">
                <div class="sp-chart-card">
                    <div class="sp-card-title">
                        <i class="fas fa-chart-line"></i> Pendapatan 7 Hari Terakhir
                    </div>
                    <div style="position: relative; width: 100%; height: 320px; overflow: hidden;">
                        <canvas id="spRevenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pesanan Terbaru & Menu Terlaris --}}
        <div class="row">
            <!-- Recent Orders -->
            <div class="col-lg-8 col-12 mb-4">
                <div class="sp-table-card">
                    <div class="sp-card-title d-flex justify-content-between align-items-center mb-4">
                        <div><i class="fas fa-receipt"></i> Pesanan Terbaru</div>
                        <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary" style="font-size: 0.8rem; font-weight: 600; padding: 0.25rem 0.75rem;">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
                    </div>
                    <div class="table-responsive">
                        <table class="sp-order-table">
                            <thead>
                                <tr>
                                    <th>No Pesanan</th>
                                    <th>Tipe / Meja</th>
                                    <th>Menu</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentOrders as $order)
                                    <tr>
                                        <td><span class="sp-order-no">#ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span></td>
                                        <td>
                                            @if($order->shipping_cost > 0)
                                                <span class="badge badge-light" style="border: 1px solid var(--sp-border);">Take Away</span>
                                            @else
                                                <span class="badge badge-light" style="border: 1px solid var(--sp-border);">Dine In (Meja)</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $itemNames = $order->items->map(function($item) {
                                                    return $item->product->name ?? 'Menu Terhapus';
                                                })->join(', ');
                                            @endphp
                                            <span data-toggle="tooltip" title="{{ $itemNames }}" style="cursor: help; border-bottom: 1px dotted #999;">
                                                {{ Str::limit($itemNames, 40) }}
                                            </span>
                                        </td>
                                        <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="sp-badge sp-badge-success">Selesai</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Belum ada pesanan masuk.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Top Menu Items -->
            <div class="col-lg-4 col-12 mb-4" id="top-menu">
                <div class="sp-table-card">
                    <div class="sp-card-title d-flex justify-content-between align-items-center">
                        <div><i class="fas fa-fire text-warning"></i> Menu Terlaris</div>
                        <div class="d-flex align-items-center" style="gap: 10px;">
                            <span class="badge badge-light d-none d-sm-inline-block" style="font-size: 0.7rem; font-weight: 600; border: 1px solid #f0f0f5;" data-toggle="tooltip" title="{{ $topProductsTimeline }}">{{ Str::limit($topProductsTimeline, 15) }}</span>
                            @if(!request('menu_date'))
                                <select class="form-control form-control-sm" style="width: auto; height: 28px; padding: 2px 10px; font-size: 0.75rem; border-radius: 4px; box-shadow: none;" onchange="window.location.href='?menu_range=' + this.value + '#top-menu'">
                                    <option value="today" {{ request('menu_range') == 'today' || !request('menu_range') ? 'selected' : '' }}>Hari Ini</option>
                                    <option value="7d" {{ request('menu_range') == '7d' ? 'selected' : '' }}>7 Hari</option>
                                    <option value="30d" {{ request('menu_range') == '30d' ? 'selected' : '' }}>30 Hari</option>
                                </select>
                            @endif
                            @if(request('menu_date') || request('menu_range') && request('menu_range') != 'today')
                                <a href="?" class="text-danger ml-1" data-toggle="tooltip" title="Reset Filter"><i class="fas fa-times-circle"></i></a>
                            @endif
                        </div>
                    </div>
                    <ul class="sp-menu-list">
                        @forelse ($topProductsToday as $top)
                            <li class="sp-menu-rank">
                                <div class="sp-rank-num rank-{{ $loop->iteration }}">{{ $loop->iteration }}</div>
                                <div class="sp-rank-info">
                                    <div class="sp-rank-name">{{ $top->product->name ?? 'Menu Terhapus' }}</div>
                                    <div class="sp-rank-sold">Total penjualan</div>
                                </div>
                                <div class="sp-rank-count">{{ $top->qty_sold }}</div>
                            </li>
                        @empty
                            <li class="text-center text-muted py-4">Belum ada data menu terlaris.</li>
                        @endforelse
                    </ul>
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
    // Initialize tooltips
    if (typeof $ !== 'undefined' && $.fn.tooltip) {
        $('[data-toggle="tooltip"]').tooltip();
    }

    const ctx = document.getElementById('spRevenueChart');
    if (!ctx) return;

    const labels = {!! json_encode($labels) !!};
    const revenues = {!! json_encode($revenues) !!};
    const rawDates = {!! json_encode($rawDates) !!};

    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 340);
    gradient.addColorStop(0, 'rgba(102, 126, 234, 0.25)');
    gradient.addColorStop(1, 'rgba(102, 126, 234, 0.01)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: revenues,
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
            onClick: (e, activeEls) => {
                if (activeEls.length > 0) {
                    const dataIndex = activeEls[0].index;
                    const dateClicked = rawDates[dataIndex];
                    window.location.href = '?menu_date=' + dateClicked + '#top-menu';
                }
            },
            onHover: (e, activeEls) => {
                e.native.target.style.cursor = activeEls.length > 0 ? 'pointer' : 'default';
            },
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1a1a2e',
                    titleFont: { size: 13, weight: '600' },
                    bodyFont: { size: 13 },
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
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 12, weight: '500' }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,.04)' },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 12 },
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
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
});
</script>
@endpush
