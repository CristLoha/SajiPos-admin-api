<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function transactions(Request $request)
    {
        $user = $request->user();
        $date = $request->query('date', Carbon::today()->toDateString());
        $limit = $request->query('limit', 15);

        $orders = Order::with('items')
            ->where('cashier_id', $user->id)
            ->whereDate('created_at', $date)
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        $transactions = $orders->map(function ($order) {
            $formattedId = "TRX-" . $order->created_at->format('Ymd') . "-" . str_pad($order->id, 4, '0', STR_PAD_LEFT);
            
            return [
                'id' => $formattedId,
                'time' => $order->created_at->format('H:i:s'),
                'customer_name' => 'Customer', // Tidak ada field customer_name di database saat ini
                'payment_method' => strtoupper($order->payment_method),
                'total_items' => $order->items->sum('quantity'),
                'grand_total' => (int) $order->total,
                'status' => $order->status ?? 'completed',
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil mengambil riwayat transaksi',
            'data' => [
                'transactions' => $transactions,
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'total_pages' => $orders->lastPage(),
                    'total_items' => $orders->total(),
                ]
            ]
        ]);
    }

    public function summary(Request $request)
    {
        $user = $request->user();
        $date = $request->query('date', Carbon::today()->toDateString());

        $query = Order::whereDate('transaction_time', $date)
                      ->where('status', 'success');

        // Jika kasir, hanya lihat datanya sendiri. Jika admin, lihat semua.
        if ($user->roles === 'user') {
            $query->where('cashier_id', $user->id);
        }

        $orders = $query->get();

        // 1. Total Omset Bersih & Pajak
        $total_omzet = $orders->sum('total');
        $total_tax = $orders->sum('tax');
        $total_discount = $orders->sum('discount_amount');
        $total_transactions = $orders->count();

        // 2. Rincian Pembayaran
        $payment_summary = [
            'cash' => [
                'count' => $orders->where('payment_method', 'CASH')->count(),
                'total' => $orders->where('payment_method', 'CASH')->sum('total')
            ],
            'qris' => [
                'count' => $orders->where('payment_method', 'qris')->count() + $orders->where('payment_method', 'QRIS')->count(),
                'total' => $orders->where('payment_method', 'qris')->sum('total') + $orders->where('payment_method', 'QRIS')->sum('total')
            ],
            'transfer' => [
                'count' => $orders->where('payment_method', 'transfer')->count() + $orders->where('payment_method', 'TRANSFER')->count(),
                'total' => $orders->where('payment_method', 'transfer')->sum('total') + $orders->where('payment_method', 'TRANSFER')->sum('total')
            ],
        ];

        // 3. Grafik Penjualan (Per Jam)
        $hourly_sales = array_fill(0, 24, 0);
        foreach ($orders as $order) {
            $hour = (int) Carbon::parse($order->transaction_time)->format('H');
            $hourly_sales[$hour] += $order->total;
        }

        $chart_data = [];
        foreach ($hourly_sales as $hour => $total) {
            if ($total > 0) { // Hanya kirim jam yang ada transaksinya untuk efisiensi
                $chart_data[] = [
                    'time' => str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00',
                    'total' => $total
                ];
            }
        }

        // 4. Menu Terlaris (Top 3)
        $top_menus = [];
        $orderIds = $orders->pluck('id');
        if ($orderIds->isNotEmpty()) {
            $top_menus = \Illuminate\Support\Facades\DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->whereIn('order_id', $orderIds)
                ->select('products.name', \Illuminate\Support\Facades\DB::raw('SUM(order_items.quantity) as total_sold'))
                ->groupBy('products.id', 'products.name')
                ->orderBy('total_sold', 'desc')
                ->limit(3)
                ->get();
        }

        return response()->json([
            'success' => true,
            'message' => 'Laporan Ringkasan Penjualan Harian',
            'data' => [
                'date' => Carbon::parse($date)->format('d M Y'),
                'total_omzet' => $total_omzet,
                'total_transactions' => $total_transactions,
                'total_tax' => $total_tax,
                'total_discount' => $total_discount,
                'payments' => $payment_summary,
                'top_menus' => $top_menus,
                'chart_data' => $chart_data
            ]
        ], 200);
    }
}
