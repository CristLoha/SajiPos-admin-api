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

            $paymentDetails = null;

            $paymentMethod = strtolower($request->payment_method);
            if (in_array($paymentMethod, ['qris', 'transfer', 'bank_transfer'])) {
                $invoiceNumber = 'ORD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT);
                $secretKey = config('services.xendit.secret_key');
                $externalId = $invoiceNumber . '-' . time();
                
                if ($paymentMethod === 'qris') {
                    // Panggil API Xendit untuk QRIS
                    $response = \Illuminate\Support\Facades\Http::withBasicAuth($secretKey, '')
                        ->withHeaders([
                            'Content-Type' => 'application/json',
                            'api-version' => '2022-07-31'
                        ])
                        ->post('https://api.xendit.co/qr_codes', [
                            'reference_id' => $externalId,
                            'type' => 'DYNAMIC',
                            'currency' => 'IDR',
                            'amount' => (int) $order->total,
                            'expires_at' => now()->addMinutes(30)->toIso8601ZuluString(),
                        ]);

                    if ($response->successful()) {
                        $qrData = $response->json();
                        
                        $qrString = $qrData['qr_string'] ?? null;
                        
                        // Simpan ke database
                        $order->midtrans_order_id = $externalId; // Tetap menggunakan kolom ini atau bisa diubah namanya
                        $order->payment_token = $qrString;
                        $order->save();

                        $paymentDetails = [
                            'transaction_id' => $externalId,
                            'payment_type' => 'qris',
                            'qr_string' => $qrString,
                            'qr_image_url' => $qrString ? 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrString) : null,
                            'expires_at' => $qrData['expires_at'] ?? null,
                        ];
                    } else {
                        throw new \Exception("Gagal meng-generate QRIS via Xendit: " . $response->body());
                    }
                } else {
                    // Panggil API Xendit Virtual Accounts (Native VA)
                    $bankCode = strtoupper($request->bank_code ?? 'BCA'); // Default BCA jika frontend belum ngirim
                    $response = \Illuminate\Support\Facades\Http::withBasicAuth($secretKey, '')
                        ->withHeaders([
                            'Content-Type' => 'application/json',
                        ])
                        ->post('https://api.xendit.co/callback_virtual_accounts', [
                            'external_id' => $externalId,
                            'bank_code' => $bankCode,
                            'name' => 'Pesanan ' . $invoiceNumber,
                            'expected_amount' => (int) $order->total,
                            'is_closed' => true,
                            'is_single_use' => true,
                            'expiration_date' => now()->addMinutes(30)->toIso8601ZuluString(),
                        ]);

                    if ($response->successful()) {
                        $vaData = $response->json();
                        
                        // Simpan ke database
                        $order->midtrans_order_id = $externalId;
                        $order->payment_token = $vaData['id'] ?? null; // Simpan VA ID
                        $order->save();

                        $paymentDetails = [
                            'transaction_id' => $externalId,
                            'payment_type' => 'transfer',
                            'bank_code' => $vaData['bank_code'] ?? $bankCode,
                            'va_number' => $vaData['account_number'] ?? null,
                            'expires_at' => $vaData['expiration_date'] ?? null,
                        ];
                    } else {
                        throw new \Exception("Gagal membuat Virtual Account Xendit: " . $response->body());
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

            DB::commit();

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

        $secretKey = config('services.xendit.secret_key');
        
        $paymentMethod = strtolower($order->payment_method);
        
        try {
            if ($paymentMethod === 'qris') {
                // Untuk QRIS, kita bisa cek dari Endpoint Xendit QR Codes menggunakan reference_id / external_id (midtrans_order_id)
                $response = \Illuminate\Support\Facades\Http::withBasicAuth($secretKey, '')
                    ->get("https://api.xendit.co/qr_codes/{$order->midtrans_order_id}");
                
                if ($response->successful()) {
                    $data = $response->json();
                    $transactionStatus = $data['status'] ?? 'PENDING';
                    
                    if ($transactionStatus == 'COMPLETED' || $transactionStatus == 'PAID') {
                        $order->status = 'success';
                    } else if ($transactionStatus == 'FAILED' || $transactionStatus == 'VOID') {
                        $order->status = 'failed';
                    } else {
                        $order->status = 'pending';
                    }
                    
                    $order->save();
                    
                    $qrString = $data['qr_string'] ?? $order->payment_token;
                    $paymentDetails = [
                        'transaction_id' => $order->midtrans_order_id,
                        'payment_type' => 'qris',
                        'qr_string' => $qrString,
                        'qr_image_url' => $qrString ? 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrString) : null,
                        'expires_at' => $data['expires_at'] ?? null,
                    ];

                    return response()->json([
                        'success' => true,
                        'message' => 'Status berhasil disinkronisasi',
                        'data' => [
                            'order_id' => $order->id,
                            'status' => $order->status,
                            'xendit_status' => $transactionStatus,
                            'payment_details' => $paymentDetails,
                        ]
                    ], 200);
                }
            } else {
                // Untuk Transfer/VA, cek menggunakan Virtual Account ID yang disimpan di payment_token
                $vaId = $order->payment_token;
                if ($vaId) {
                    $response = \Illuminate\Support\Facades\Http::withBasicAuth($secretKey, '')
                        ->get("https://api.xendit.co/callback_virtual_accounts/{$vaId}");
                        
                    if ($response->successful()) {
                        $data = $response->json();
                        $transactionStatus = $data['status'] ?? 'PENDING';
                        
                        if ($transactionStatus == 'COMPLETED' || $transactionStatus == 'PAID' || $transactionStatus == 'SETTLED') {
                            $order->status = 'success';
                        } else if ($transactionStatus == 'INACTIVE' || $transactionStatus == 'EXPIRED') {
                            $order->status = 'failed';
                        } else {
                            $order->status = 'pending';
                        }
                        
                        $order->save();
                        
                        // Buat ulang payment_details
                        $paymentDetails = [
                            'transaction_id' => $order->midtrans_order_id,
                            'payment_type' => 'transfer',
                            'bank_code' => $data['bank_code'] ?? null,
                            'va_number' => $data['account_number'] ?? null,
                            'expires_at' => $data['expiration_date'] ?? null,
                        ];

                        return response()->json([
                            'success' => true,
                            'message' => 'Status berhasil disinkronisasi',
                            'data' => [
                                'order_id' => $order->id,
                                'status' => $order->status,
                                'xendit_status' => $transactionStatus,
                                'payment_details' => $paymentDetails,
                            ]
                        ], 200);
                    }
                }
            }
        } catch (\Exception $e) {
            // Lanjut ke bawah
        }

        // Jika tidak ditemukan di Xendit
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengecek status ke Xendit',
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

    public function getPaymentChannels()
    {
        $channels = [
            [
                'payment_method' => 'qris',
                'bank_code' => 'QRIS',
                'name' => 'QRIS',
                'description' => 'Bayar menggunakan scan QRIS',
                'logo_url' => asset('img/banks/qris.png'), // Atau sesuaikan path-nya
            ],
            [
                'payment_method' => 'transfer',
                'bank_code' => 'BCA',
                'name' => 'BCA',
                'description' => 'Transfer via BCA Virtual Account',
                'logo_url' => asset('img/banks/bca.png'),
            ],
            [
                'payment_method' => 'transfer',
                'bank_code' => 'BNI',
                'name' => 'BNI',
                'description' => 'Transfer via BNI Virtual Account',
                'logo_url' => asset('img/banks/bni.png'),
            ],
            [
                'payment_method' => 'transfer',
                'bank_code' => 'MANDIRI',
                'name' => 'MANDIRI',
                'description' => 'Transfer via Mandiri Virtual Account',
                'logo_url' => asset('img/banks/mandiri.png'),
            ],
            [
                'payment_method' => 'transfer',
                'bank_code' => 'BRI',
                'name' => 'BRI',
                'description' => 'Transfer via BRI Virtual Account',
                'logo_url' => asset('img/banks/bri.png'),
            ],
        ];

        return response()->json([
            'status' => 'success',
            'data' => $channels
        ]);
    }
}
