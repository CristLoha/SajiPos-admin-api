<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$secretKey = env('XENDIT_SECRET_KEY');

$response = Illuminate\Support\Facades\Http::withBasicAuth($secretKey, '')->get("https://api.xendit.co/callback_virtual_accounts");
echo json_encode($response->json());
