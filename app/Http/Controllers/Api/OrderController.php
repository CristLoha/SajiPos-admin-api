<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Calculate total price including discount.
     * POST /api/orders/hitung-total
     */
    public function calculateTotal(Request $request)
    {
        $request->validate([
            'order_items' => 'required|array',
            'order_items.*.quantity' => 'required|integer|min:1',
            'order_items.*.price' => 'required|numeric',
            'discount_id' => 'nullable|exists:discounts,id',
        ]);

        $total_harga_item = 0;
        foreach ($request->order_items as $item) {
            $total_harga_item += $item['price'] * $item['quantity'];
        }

        $discount_amount = 0;
        if ($request->has('discount_id') && $request->discount_id) {
            $discount = \App\Models\Discount::find($request->discount_id);
            if ($discount) {
                if ($discount->type === 'percentage') {
                    $discount_amount = $total_harga_item * ($discount->value / 100);
                } else {
                    $discount_amount = $discount->value;
                }
            }
        }

        $discount_amount = min($discount_amount, $total_harga_item);
        $subtotal = $total_harga_item - $discount_amount;

        $setting = \App\Models\Setting::first();
        $shipping_fee = $setting ? (float)$setting->shipping_fee : 0;
        $service_fee = $setting ? (float)$setting->service_fee : 0;
        
        $tax_base = $subtotal;
        if ($setting && $setting->include_shipping_in_tax) {
            $tax_base += $shipping_fee;
        }
        if ($setting && $setting->include_service_fee_in_tax) {
            $tax_base += $service_fee;
        }

        $tax_percentage = $setting ? (float)$setting->tax_percentage : 0;
        $tax_amount = $tax_base * ($tax_percentage / 100);
        
        $grand_total = $subtotal + $shipping_fee + $service_fee + $tax_amount;

        return response()->json([
            'success' => true,
            'data' => [
                'subtotal' => $subtotal, // as per request, subtotal after discount
                'shipping_fee' => $shipping_fee,
                'service_fee' => $service_fee,
                'tax_amount' => $tax_amount,
                'discount_amount' => $discount_amount,
                'grand_total' => $grand_total,
            ]
        ], 200);
    }

    /**
     */
    public function store(Request $request)
    {
        $request->validate([
            'cashier_id' => 'required|exists:users,id',
            'transaction_time' => 'required|string',
            'sub_total' => 'required|numeric',
            'discount_id' => 'nullable|exists:discounts,id',
            'discount_amount' => 'nullable|numeric',
            'shipping_cost' => 'nullable|numeric',
            'service_charge' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
            'total' => 'required|numeric',
            'payment_method' => 'required|string',
            'order_items' => 'required|array',
            'order_items.*.product_id' => 'required|exists:products,id',
            'order_items.*.quantity' => 'required|integer|min:1',
            'order_items.*.price' => 'required|numeric',
            'order_items.*.note' => 'nullable|string|max:255',
        ]);

        try {
            // 1. Validasi Stok Terlebih Dahulu
            foreach ($request->order_items as $item) {
                $product = \App\Models\Product::find($item['product_id']);
                if ($product && $product->stock < $item['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok tidak cukup untuk produk: ' . $product->name . '. (Sisa: ' . $product->stock . ')'
                    ], 400);
                }
            }

            DB::beginTransaction();

            // Hitung total harga item riil
            $total_harga_item = 0;
            foreach ($request->order_items as $item) {
                $total_harga_item += $item['price'] * $item['quantity'];
            }

            // Validasi & Hitung Diskon di Backend
            $final_discount_amount = 0;
            $discount_name = null;
            if ($request->has('discount_id') && $request->discount_id) {
                $discount = \App\Models\Discount::find($request->discount_id);
                if ($discount) {
                    $discount_name = $discount->name;
                    if ($discount->type === 'percentage') {
                        $final_discount_amount = $total_harga_item * ($discount->value / 100);
                    } else {
                        $final_discount_amount = $discount->value;
                    }
                }
            } else if ($request->has('discount_amount') && $request->discount_amount > 0) {
                // Mendukung diskon manual
                $final_discount_amount = $request->discount_amount;
                $discount_name = 'Diskon Manual';
            }

            $final_discount_amount = min($final_discount_amount, $total_harga_item);
            $subtotal = $total_harga_item - $final_discount_amount;

            // Hitung berdasarkan setting
            $setting = \App\Models\Setting::first();
            $shipping_fee = $setting ? (float)$setting->shipping_fee : 0;
            $service_fee = $setting ? (float)$setting->service_fee : 0;
            
            $tax_base = $subtotal;
            if ($setting && $setting->include_shipping_in_tax) {
                $tax_base += $shipping_fee;
            }
            if ($setting && $setting->include_service_fee_in_tax) {
                $tax_base += $service_fee;
            }

            $tax_percentage = $setting ? (float)$setting->tax_percentage : 0;
            $tax_amount = $tax_base * ($tax_percentage / 100);
            
            $calculated_total = $subtotal + $shipping_fee + $service_fee + $tax_amount;

            $order = Order::create([
                'cashier_id' => $request->cashier_id,
                'transaction_time' => $request->transaction_time,
                'sub_total' => $total_harga_item, // save the original item sub_total in DB as designed earlier
                'discount_id' => $request->discount_id,
                'discount_name' => $discount_name,
                'discount_amount' => $final_discount_amount,
                'shipping_cost' => $shipping_fee,
                'service_charge' => $service_fee,
                'tax' => $tax_amount,
                'total' => $calculated_total,
                'payment_method' => $request->payment_method,
                'status' => strtolower($request->payment_method) === 'cash' ? 'success' : 'pending',
            ]);

            // 2. Simpan Item-Item Pesanan
            foreach ($request->order_items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'note' => $item['note'] ?? null,
                ]);

                // Opsional: Kurangi stok produk
                $product = \App\Models\Product::find($item['product_id']);
                if ($product) {
                    $product->stock = max(0, $product->stock - $item['quantity']);
                    $product->save();
                }
            }

            DB::commit();

            $paymentDetails = null;

            $paymentMethod = strtolower($request->payment_method);
            if (in_array($paymentMethod, ['qris', 'transfer', 'bank_transfer'])) {
                $invoiceNumber = 'ORD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT);
                $serverKey = config('services.midtrans.server_key');
                $midtransOrderId = $invoiceNumber . '-' . time();
                
                if ($paymentMethod === 'qris') {
                    // Panggil API Midtrans CORE API khusus untuk QRIS
                    $response = \Illuminate\Support\Facades\Http::withBasicAuth($serverKey, '')
                        ->withHeaders([
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                            'X-Override-Notification' => url('/api/midtrans/webhook')
                        ])
                        ->post('https://api.sandbox.midtrans.com/v2/charge', [
                            'payment_type' => 'qris',
                            'transaction_details' => [
                                'order_id' => $midtransOrderId,
                                'gross_amount' => (int) $order->total
                            ],
                            'qris' => [
                                'acquirer' => 'gopay'
                            ]
                        ]);

                    if ($response->successful()) {
                        $coreData = $response->json();
                        
                        // Ekstrak QR String atau Image URL dari response
                        $qrString = $coreData['qr_string'] ?? null;
                        
                        // Cari action url untuk gambar QRIS (opsional jika qr_string tidak mau digambar manual)
                        $qrImageUrl = null;
                        if (isset($coreData['actions']) && is_array($coreData['actions'])) {
                            foreach ($coreData['actions'] as $action) {
                                if ($action['name'] === 'generate-qr-code') {
                                    $qrImageUrl = $action['url'];
                                    break;
                                }
                            }
                        }

                        // Simpan ke database
                        $order->midtrans_order_id = $midtransOrderId;
                        // Simpan qr_string ke payment_token (bisa di-reuse field-nya) atau biarkan kosong
                        $order->payment_token = $qrString;
                        $order->save();

                        $paymentDetails = [
                            'transaction_id' => $midtransOrderId,
                            'payment_type' => 'qris',
                            'qr_string' => $qrString,
                            'qr_image_url' => $qrImageUrl,
                        ];
                    } else {
                        throw new \Exception("Gagal meng-generate QRIS via Core API: " . $response->body());
                    }
                } else {
                    // VA / Transfer masih pakai SNAP
                    $enabledPayments = ['bca_va', 'bni_va', 'bri_va', 'permata_va', 'cimb_va', 'other_va', 'echannel'];
                    
                    // Panggil API Midtrans SNAP
                    $response = \Illuminate\Support\Facades\Http::withBasicAuth($serverKey, '')
                        ->withHeaders([
                            'X-Override-Notification' => url('/api/midtrans/webhook')
                        ])
                        ->post('https://app.sandbox.midtrans.com/snap/v1/transactions', [
                            'transaction_details' => [
                                'order_id' => $midtransOrderId,
                                'gross_amount' => (int) $order->total
                            ],
                            'enabled_payments' => $enabledPayments
                        ]);

                    if ($response->successful()) {
                        $snapData = $response->json();
                        
                        // Simpan ke database
                        $order->midtrans_order_id = $midtransOrderId;
                        $order->payment_token = $snapData['token'] ?? null;
                        $order->save();

                        $paymentDetails = [
                            'transaction_id' => $midtransOrderId,
                            'payment_type' => 'bank_transfer',
                            'snap_token' => $snapData['token'] ?? null,
                            'snap_redirect_url' => $snapData['redirect_url'] ?? null,
                        ];
                    } else {
                        throw new \Exception("Gagal mengambil token Midtrans Snap: " . $response->body());
                    }
                }
            }

            $responseData = $order->load('items.product')->toArray();
            $responseData['order_id'] = $order->id;
            $responseData['invoice_number'] = 'ORD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT);
            $responseData['payment_details'] = $paymentDetails;

            // Phase 3: Update response order/struk
            // Menambahkan breakdown lengkap
            $responseData['subtotal'] = $total_harga_item - $final_discount_amount; // After discount
            $responseData['shipping_fee'] = $shipping_fee;
            $responseData['service_fee'] = $service_fee;
            $responseData['tax_amount'] = $tax_amount;
            $responseData['discount_amount'] = $final_discount_amount;
            $responseData['grand_total'] = $calculated_total;

            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => 'Transaksi Berhasil Disimpan!',
                'data' => $responseData
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a list of order history.
     * GET /api/orders
     */
    public function index(Request $request)
    {
        $query = Order::with('items.product', 'cashier')->orderBy('created_at', 'desc');

        // Filter by cashier/user
        if ($request->has('cashier_id')) {
            $query->where('cashier_id', $request->cashier_id);
        }

        $orders = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'List Riwayat Transaksi',
            'data' => $orders
        ], 200);
    }

    /**
     * Cek status pembayaran Midtrans secara manual (Sync)
     * GET /api/orders/{id}/check-status
     */
    public function checkStatus($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        if (!in_array(strtolower($order->payment_method), ['qris', 'transfer', 'bank_transfer'])) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan ini tidak menggunakan metode pembayaran Midtrans'
            ], 400);
        }

        if (!$order->midtrans_order_id) {
            // Jika untuk data lama yang belum punya midtrans_order_id (sebelum fitur ini dibuat)
            // Kita tidak bisa mengecek ke Midtrans karena order_id Midtrans-nya mengandung timestamp yang tidak kita simpan.
            // Solusi: Kita otomatis anggap failed/expired (karena ini pasti data testing lama).
            $order->status = 'failed';
            $order->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Data lama (tanpa ID Midtrans) otomatis dibatalkan karena tidak dapat dilacak.',
                'data' => [
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'midtrans_status' => 'expire'
                ]
            ], 200);
        }

        $serverKey = config('services.midtrans.server_key');
        
        // Panggil API Get Status Midtrans (Sandbox/Production tergantung URL)
        // Kita gunakan sandbox sesuai implementasi SNAP sebelumnya
        $response = \Illuminate\Support\Facades\Http::withBasicAuth($serverKey, '')
            ->get("https://api.sandbox.midtrans.com/v2/{$order->midtrans_order_id}/status");

        if ($response->successful()) {
            $data = $response->json();
            $transactionStatus = $data['transaction_status'] ?? 'pending';

            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                $order->status = 'success';
            } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
                $order->status = 'failed';
            } else if ($transactionStatus == 'pending') {
                $order->status = 'pending';
            }

            $order->save();

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil disinkronisasi',
                'data' => [
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'midtrans_status' => $transactionStatus,
                ]
            ], 200);
        }

        // Jika tidak ditemukan di Midtrans (misalnya belum dibayar atau expire/terhapus)
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengecek status ke Midtrans',
            'error' => $response->json()
        ], 500);
    }

    /**
     * Get single order status
     * GET /api/orders/{id}
     */
    public function show($id)
    {
        $order = Order::with('items.product', 'cashier')->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail Pesanan',
            'data' => $order
        ], 200);
    }

    /**
     * Delete an order
     * DELETE /api/orders/{id}
     */
    public function destroy($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        // Jika ingin mengembalikan stok produk sebelum dihapus
        foreach ($order->items as $item) {
            $product = \App\Models\Product::find($item->product_id);
            if ($product) {
                $product->stock += $item->quantity;
                $product->save();
            }
        }

        // Hapus item terkait (kalau tidak pakai onDelete cascade di migration)
        $order->items()->delete();

        // Hapus order
        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Pesanan berhasil dihapus'
        ], 200);
    }
}
