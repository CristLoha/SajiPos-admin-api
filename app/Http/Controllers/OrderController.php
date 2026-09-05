<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $orders = Order::with('cashier')
            ->when($request->date, function ($query) use ($request) {
                $query->whereDate('transaction_time', $request->date);
            })
            ->orderBy('transaction_time', 'desc')
            ->paginate(10);

        return view('pages.orders.index', compact('orders'));
    }

    /**
     * Display the specified resource (Order details with items).
     */
    public function show($id)
    {
        $order = Order::with(['cashier', 'items.product'])->findOrFail($id);
        return view('pages.orders.show', compact('order'));
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        // Kembalikan stok produk
        foreach ($order->items as $item) {
            $product = \App\Models\Product::find($item->product_id);
            if ($product) {
                $product->stock += $item->quantity;
                $product->save();
            }
        }

        $order->items()->delete();
        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dihapus dan stok telah dikembalikan.');
    }

    /**
     * Remove all resources from storage (Delete All).
     */
    public function destroyAll()
    {
        // Pastikan hanya admin yang bisa (tambahan security meski sudah di middleware)
        if (auth()->user()->roles !== 'admin') {
            return redirect()->route('orders.index')->with('error', 'Anda tidak memiliki akses untuk menghapus semua pesanan.');
        }

        $orders = Order::with('items')->get();

        foreach ($orders as $order) {
            // Kembalikan stok produk
            foreach ($order->items as $item) {
                $product = \App\Models\Product::find($item->product_id);
                if ($product) {
                    $product->stock += $item->quantity;
                    $product->save();
                }
            }
            $order->items()->delete();
            $order->delete();
        }

        return redirect()->route('orders.index')->with('success', 'Semua pesanan berhasil dihapus dan stok telah dikembalikan.');
    }

    /**
     * Remove multiple selected resources from storage (Bulk Delete).
     */
    public function bulkDelete(Request $request)
    {
        // Pastikan hanya admin yang bisa
        if (auth()->user()->roles !== 'admin') {
            return redirect()->route('orders.index')->with('error', 'Anda tidak memiliki akses untuk aksi ini.');
        }

        $orderIds = json_decode($request->input('order_ids'), true);

        if (empty($orderIds) || !is_array($orderIds)) {
            return redirect()->route('orders.index')->with('error', 'Tidak ada data pesanan yang dipilih.');
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $orders = Order::with('items')->whereIn('id', $orderIds)->get();

            foreach ($orders as $order) {
                // Kembalikan stok produk
                foreach ($order->items as $item) {
                    $product = \App\Models\Product::find($item->product_id);
                    if ($product) {
                        $product->stock += $item->quantity;
                        $product->save();
                    }
                }
            }

            // Hapus items kemudian orders secara massal (sesuai dokumen)
            \App\Models\OrderItem::whereIn('order_id', $orderIds)->delete();
            Order::whereIn('id', $orderIds)->delete();

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('orders.index')->with('success', count($orderIds) . ' pesanan berhasil dihapus secara massal.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->route('orders.index')->with('error', 'Terjadi kesalahan saat menghapus: ' . $e->getMessage());
        }
    }
}
