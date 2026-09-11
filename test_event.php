<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('roles', 'admin')->first();
$request = \Illuminate\Http\Request::create('/login', 'POST', ['password' => 'password']);
app()->instance('request', $request);

event(new \Illuminate\Auth\Events\Login('web', $user, false));

echo $user->fresh()->password;
