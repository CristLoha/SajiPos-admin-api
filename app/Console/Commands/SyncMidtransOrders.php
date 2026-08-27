<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

#[Signature('midtrans:sync-pending')]
#[Description('Sync status of pending Midtrans orders')]
class SyncMidtransOrders extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Cari order yang pending dan punya midtrans_order_id
        // Filter order yang dibuat minimal 1 jam yang lalu agar tidak mengganggu transaksi yang baru saja dibuat
        $orders = Order::where('status', 'pending')
            ->whereNotNull('midtrans_order_id')
            ->where('created_at', '<=', Carbon::now()->subHours(1))
            ->get();

        $this->info("Ditemukan {$orders->count()} pesanan pending untuk disinkronisasi.");

        $serverKey = config('services.midtrans.server_key');

        foreach ($orders as $order) {
            $response = Http::withBasicAuth($serverKey, '')
                ->get("https://api.sandbox.midtrans.com/v2/{$order->midtrans_order_id}/status");

            if ($response->successful()) {
                $data = $response->json();
                $transactionStatus = $data['transaction_status'] ?? 'pending';

                if (in_array($transactionStatus, ['capture', 'settlement'])) {
                    $order->status = 'success';
                } else if (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                    $order->status = 'failed';
                } else {
                    $order->status = 'pending';
                }

                $order->save();
                $this->info("Order ID {$order->id} status diupdate menjadi: {$order->status}");
            } else if ($response->status() == 404) {
                // Jika 404 (transaksi tidak ditemukan di Midtrans) dan umurnya lebih dari 24 jam, 
                // anggap saja pembeli batal/gagal (hangus sebelum terdaftar di sistem core midtrans)
                if ($order->created_at <= Carbon::now()->subDays(1)) {
                    $order->status = 'failed';
                    $order->save();
                    $this->info("Order ID {$order->id} ditandai sebagai failed (404 di Midtrans & umur > 24 jam).");
                } else {
                    $this->warn("Order ID {$order->id} tidak ditemukan di Midtrans (mungkin belum dibayar).");
                }
            } else {
                $this->error("Gagal sinkronisasi Order ID {$order->id}: " . $response->body());
            }
        }

        $this->info("Sinkronisasi selesai.");
    }
}
