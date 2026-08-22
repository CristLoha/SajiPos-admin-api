<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MenuAnalyticsController extends Controller
{
    /**
     * GET /api/menu-terlaris
     */
    public function getTopMenus(Request $request)
    {
        $range = $request->query('range', 'today'); // 'today', '7d', '30d', 'custom'
        $startDateStr = $request->query('start');
        $endDateStr = $request->query('end');

        $today = Carbon::today();
        $startDate = $today->copy()->startOfDay();
        $endDate = $today->copy()->endOfDay();

        if ($range === '7d') {
            $startDate = Carbon::now()->subDays(6)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } elseif ($range === '30d') {
            $startDate = Carbon::now()->subDays(29)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } elseif ($range === 'custom') {
            if ($startDateStr && $endDateStr) {
                $startDate = Carbon::parse($startDateStr)->startOfDay();
                $endDate = Carbon::parse($endDateStr)->endOfDay();
            }
        }

        $topProducts = OrderItem::with('product')
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(price * quantity) as total_omzet'))
            ->whereHas('order', function ($query) use ($startDate, $endDate) {
                $query->whereDate('transaction_time', '>=', $startDate)
                      ->whereDate('transaction_time', '<=', $endDate);
            })
            ->groupBy('product_id')
            ->orderBy('total_qty', 'desc')
            ->get()
            ->map(function($item, $index) {
                return [
                    'rank' => $index + 1,
                    'menu_id' => $item->product_id,
                    'nama_menu' => $item->product->name ?? 'Menu Terhapus',
                    'total_qty' => (int) $item->total_qty,
                    'total_omzet' => (int) $item->total_omzet,
                ];
            });

        return response()->json([
            'success' => true,
            'range' => $range,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'data' => $topProducts
        ]);
    }

    /**
     * GET /api/menu-terlaris/harian
     */
    public function getTopMenusDaily(Request $request)
    {
        $dateStr = $request->query('date');
        
        $date = $dateStr ? Carbon::parse($dateStr) : Carbon::today();
        $startDate = $date->copy()->startOfDay();
        $endDate = $date->copy()->endOfDay();

        $topProducts = OrderItem::with('product')
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(price * quantity) as total_omzet'))
            ->whereHas('order', function ($query) use ($startDate, $endDate) {
                $query->whereDate('transaction_time', '>=', $startDate)
                      ->whereDate('transaction_time', '<=', $endDate);
            })
            ->groupBy('product_id')
            ->orderBy('total_qty', 'desc')
            ->get()
            ->map(function($item, $index) {
                return [
                    'rank' => $index + 1,
                    'menu_id' => $item->product_id,
                    'nama_menu' => $item->product->name ?? 'Menu Terhapus',
                    'total_qty' => (int) $item->total_qty,
                    'total_omzet' => (int) $item->total_omzet,
                ];
            });

        return response()->json([
            'success' => true,
            'date' => $startDate->format('Y-m-d'),
            'data' => $topProducts
        ]);
    }
}
