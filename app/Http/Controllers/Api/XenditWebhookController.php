<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class XenditWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Xendit Webhook Verification Token (Opsional, dari header x-callback-token)
        $xenditToken = config('services.xendit.webhook_token'); 
        $incomingToken = $request->header('x-callback-token');

        if ($xenditToken && $incomingToken !== $xenditToken) {
            return response()->json(['status' => 'error', 'message' => 'Invalid callback token'], 403);
        }

        // Webhook untuk Invoices / VA 
        if ($request->has('external_id') && $request->has('status')) {
            $externalId = $request->input('external_id');
            $status = $request->input('status');

            $order = Order::where('midtrans_order_id', $externalId)->first();

            if ($order) {
                if ($status == 'PAID' || $status == 'SETTLED') {
                    $order->status = 'success';
                } else if ($status == 'EXPIRED') {
                    $order->status = 'failed';
                }
                $order->save();
            }

            return response()->json(['status' => 'success', 'message' => 'Invoice Webhook received']);
        }

        // Webhook untuk QR Code (Pembayaran berhasil)
        if ($request->has('qr_code') && $request->has('status') && $request->input('event') === 'qr.payment') {
            $data = $request->all();
            $externalId = $data['qr_code']['reference_id'] ?? null;
            $status = $data['status'] ?? null;

            if ($externalId) {
                $order = Order::where('midtrans_order_id', $externalId)->first();
                if ($order) {
                    if ($status == 'COMPLETED') {
                        $order->status = 'success';
                    }
                    $order->save();
                }
            }

            return response()->json(['status' => 'success', 'message' => 'QR Webhook received']);
        }

        return response()->json(['status' => 'success', 'message' => 'Webhook received but not handled']);
    }
}
