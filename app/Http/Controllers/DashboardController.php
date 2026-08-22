<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();

        // 1. Pendapatan Hari Ini
        $revenueToday = Order::whereDate('transaction_time', $today)->sum('total');

        // 2. Jumlah Pesanan Hari Ini
        $ordersTodayCount = Order::whereDate('transaction_time', $today)->count();

        // 3. Menu Aktif (Produk ready)
        $activeProductsCount = Product::where('status', true)->count();

        // 4. Jumlah Karyawan (Hanya staff/kasir, admin bukan karyawan)
        $employeesCount = User::whereIn('roles', ['staff', 'user'])->count();

        // 5. Riwayat Pesanan Terbaru (5 Transaksi Terakhir)
        $recentOrders = Order::with('items.product')->orderBy('transaction_time', 'desc')->limit(5)->get();

        // 6. Menu Terlaris (Timeline Support)
        $menuRange = $request->query('menu_range', 'today'); // 'today', '7d', '30d', 'custom'
        $menuDate = $request->query('menu_date'); // drill-down specific date
        
        $startDate = $today->copy()->startOfDay();
        $endDate = $today->copy()->endOfDay();
        $topProductsTimeline = 'Hari Ini';

        if ($menuDate) {
            $startDate = Carbon::parse($menuDate)->startOfDay();
            $endDate = Carbon::parse($menuDate)->endOfDay();
            $topProductsTimeline = 'Tanggal ' . $startDate->format('d M Y');
        } else {
            if ($menuRange === '7d') {
                $startDate = Carbon::now()->subDays(6)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                $topProductsTimeline = '7 Hari Terakhir';
            } elseif ($menuRange === '30d') {
                $startDate = Carbon::now()->subDays(29)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                $topProductsTimeline = '30 Hari Terakhir';
            } elseif ($menuRange === 'custom') {
                $startDate = Carbon::parse($request->query('menu_start', $today))->startOfDay();
                $endDate = Carbon::parse($request->query('menu_end', $today))->endOfDay();
                $topProductsTimeline = $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');
            }
        }

        $topProductsToday = OrderItem::with('product')
            ->select('product_id', DB::raw('SUM(quantity) as qty_sold'))
            ->whereHas('order', function ($query) use ($startDate, $endDate) {
                $query->whereDate('transaction_time', '>=', $startDate)
                      ->whereDate('transaction_time', '<=', $endDate);
            })
            ->groupBy('product_id')
            ->orderBy('qty_sold', 'desc')
            ->limit(5)
            ->get();

        // 7. Grafik Pendapatan 7 Hari Terakhir
        $sevenDaysAgo = Carbon::now()->subDays(6)->startOfDay();
        $chartData = Order::where('transaction_time', '>=', $sevenDaysAgo)
            ->select(
                DB::raw('DATE(transaction_time) as date'),
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy('date');

        $labels = [];
        $rawDates = [];
        $revenues = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateString = Carbon::now()->subDays($i)->format('Y-m-d');
            $rawDates[] = $dateString;
            $labels[] = Carbon::now()->subDays($i)->format('d M');
            $revenues[] = isset($chartData[$dateString]) ? (float)$chartData[$dateString]->revenue : 0.0;
        }

        return view('pages.dashboard', compact(
            'revenueToday',
            'ordersTodayCount',
            'activeProductsCount',
            'employeesCount',
            'recentOrders',
            'topProductsToday',
            'topProductsTimeline',
            'labels',
            'rawDates',
            'revenues'
        ));
    }
}
