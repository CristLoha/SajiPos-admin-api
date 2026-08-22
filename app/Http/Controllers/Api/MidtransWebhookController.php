<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $serverKey = config('services.midtrans.server_key');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            $orderIdFull = $request->order_id;
            // Format order_id dari backend adalah ORD-0001-timestamp
            $parts = explode('-', $orderIdFull);
            
            if (count($parts) >= 2) {
                $orderId = (int)$parts[1]; // Mengambil angka order ID-nya saja, misal '0001' menjadi 1
                $order = Order::find($orderId);
                
                if ($order) {
                    if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                        $order->status = 'success';
                    } else if ($request->transaction_status == 'cancel' || $request->transaction_status == 'deny' || $request->transaction_status == 'expire') {
                        $order->status = 'failed';
                    } else if ($request->transaction_status == 'pending') {
                        $order->status = 'pending';
                    }
                    $order->save();
                }
            }
            return response()->json(['status' => 'success', 'message' => 'Webhook received']);
        }

        return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 403);
    }
}
