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
}
