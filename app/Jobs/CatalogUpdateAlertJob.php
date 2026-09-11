<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class CatalogUpdateAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $title;
    protected $body;
    protected $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($title, $body, $type = 'product')
    {
        $this->title = $title;
        $this->body = $body;
        $this->type = $type; // 'product' atau 'category'
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $messaging = Firebase::messaging();

        $message = CloudMessage::withTarget('topic', 'catalog_updates')
            ->withNotification(Notification::create($this->title, $this->body))
            ->withData([
                'action' => 'catalog_sync_required',
                'type' => $this->type,
            ]);

        try {
            $messaging->send($message);
            \Log::info("Catalog update broadcast sent: " . $this->title);
        } catch (\Exception $e) {
            \Log::error("FCM Error (CatalogUpdate): " . $e->getMessage());
        }
    }
}
