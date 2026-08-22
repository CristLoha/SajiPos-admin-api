<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display a dashboard reporting view.
     */
    public function index(Request $request)
    {
        // Default range: 30 hari terakhir
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        // 1. Rekap Ringkas Keuangan
        $summary = Order::whereBetween('transaction_time', [$startDate, $endDate])
            ->selectRaw('
                COUNT(id) as total_transactions,
                SUM(sub_total) as total_sub_total,
                SUM(discount_amount) as total_discounts,
                SUM(shipping_cost) as total_shipping,
                SUM(service_charge) as total_service_charges,
                SUM(tax) as total_taxes,
                SUM(total) as total_revenue
            ')->first();

        // 2. Statistik Metode Pembayaran
        $paymentMethods = Order::whereBetween('transaction_time', [$startDate, $endDate])
            ->select('payment_method', DB::raw('COUNT(id) as count'), DB::raw('SUM(total) as revenue'))
            ->groupBy('payment_method')
            ->get();

        // 3. Rekap Menu Terlaris (Top 5)
        $topProducts = OrderItem::with('product.category')
            ->select('product_id', DB::raw('SUM(quantity) as qty_sold'), DB::raw('SUM(quantity * price) as revenue'))
            ->whereHas('order', function($query) use ($startDate, $endDate) {
                $query->whereBetween('transaction_time', [$startDate, $endDate]);
            })
            ->groupBy('product_id')
            ->orderBy('qty_sold', 'desc')
            ->limit(5)
            ->get();

        // 4. Data Harian untuk Chart (Range Pilihan)
        $chartRaw = Order::whereBetween('transaction_time', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(transaction_time) as date'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(id) as transactions')
            )
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $revenues = [];
        $transactions = [];

        $currentChartDate = clone $startDate;
        while ($currentChartDate <= $endDate) {
            $dateStr = $currentChartDate->format('Y-m-d');
            $labels[] = $currentChartDate->format('d M');
            
            if ($chartRaw->has($dateStr)) {
                $revenues[] = (float) $chartRaw[$dateStr]->revenue;
                $transactions[] = (int) $chartRaw[$dateStr]->transactions;
            } else {
                $revenues[] = 0;
                $transactions[] = 0;
            }
            
            $currentChartDate->addDay();
        }

        // 5. Data untuk GitHub-style Heatmap (90 Hari terakhir)
        $heatmapStartDate = Carbon::now()->subDays(89)->startOfDay();
        $heatmapEndDate = Carbon::now()->endOfDay();
        
        $heatmapRaw = Order::whereBetween('transaction_time', [$heatmapStartDate, $heatmapEndDate])
            ->select(DB::raw('DATE(transaction_time) as date'), DB::raw('COUNT(id) as count'))
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $heatmapData = [];
        $currentDate = clone $heatmapStartDate;
        
        while ($currentDate <= $heatmapEndDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $count = $heatmapRaw[$dateStr] ?? 0;
            
            // Tentukan level warna (0: tidak ada, 1: sedikit, 2: sedang, 3: banyak, 4: sangat banyak)
            $level = 0;
            if ($count > 0) $level = 1;
            if ($count > 5) $level = 2;
            if ($count > 15) $level = 3;
            if ($count > 30) $level = 4;

            $heatmapData[] = [
                'date' => $dateStr,
                'count' => $count,
                'level' => $level,
                'formatted' => $currentDate->format('d M Y')
            ];
            $currentDate->addDay();
        }

        return view('pages.reports.index', compact(
            'summary', 
            'paymentMethods', 
            'topProducts', 
            'labels', 
            'revenues', 
            'transactions',
            'startDate',
            'endDate',
            'heatmapData'
        ));
    }
}
