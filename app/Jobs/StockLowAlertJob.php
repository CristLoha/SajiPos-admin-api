<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;

class StockLowAlertJob implements ShouldQueue
{
    use Queueable;

    public $product;

    /**
     * Create a new job instance.
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $messaging = Firebase::messaging();
            
            $messageBody = $this->product->stock > 0 
                ? "Peringatan! Stok {$this->product->name} sisa {$this->product->stock} porsi/item."
                : "Gawat! Stok {$this->product->name} sudah habis (Sold Out).";
            
            $message = CloudMessage::new()
                ->withTopic('stock_alerts')
                ->withNotification(Notification::create('Peringatan Stok Menipis ⚠️', $messageBody))
                ->withData([
                    'product_id' => (string) $this->product->id, 
                    'action' => 'stock_alert'
                ]);
            
            $result = $messaging->send($message);
            Log::info('FCM Stock Alert Berhasil Dikirim! Target Topic: stock_alerts', (array) $result);
        } catch (\Exception $e) {
            Log::error('FCM Stock Alert Job Error: ' . $e->getMessage());
        }
    }
}
