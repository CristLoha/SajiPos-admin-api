<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$secretKey = env('XENDIT_SECRET_KEY');

$response = Illuminate\Support\Facades\Http::withBasicAuth($secretKey, '')
    ->post("https://api.xendit.co/pool_virtual_accounts/simulate_payment", [
        'bank_code' => 'MANDIRI',
        'bank_account_number' => '8890817841404',
        'amount' => 25000
    ]);
echo json_encode($response->json(), JSON_PRETTY_PRINT);
