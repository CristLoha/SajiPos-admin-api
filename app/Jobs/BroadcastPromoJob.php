<?php

namespace App\Jobs;

use App\Models\Campaign;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;

class BroadcastPromoJob implements ShouldQueue
{
    use Queueable;

    public $campaign;

    /**
     * Create a new job instance.
     */
    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $messaging = Firebase::messaging();
            
            $messageBody = $this->campaign->discount_type == 'percent' 
                ? "Diskon {$this->campaign->discount_value}% menantimu!" 
                : "Potongan harga Rp " . number_format($this->campaign->discount_value, 0, ',', '.') . "!";
            
            $message = CloudMessage::new()
                ->withTopic('promo_broadcast')
                ->withNotification(Notification::create('Ada Promo Baru: ' . $this->campaign->name, $messageBody))
                ->withData(['campaign_id' => $this->campaign->id, 'action' => 'open_promo']);
            
            $result = $messaging->send($message);
            // KITA NGE-CHEAT BUAT NGETES: LANGSUNG TAMPILIN DI LAYAR
            dd('🔥 YESS FCM BERHASIL DIKIRIM! INI BUKTINYA:', $result);
        } catch (\Exception $e) {
            dd('❌ YAH GAGAL KIRIM FCM. ERRORNYA:', $e->getMessage());
        }
    }
}
