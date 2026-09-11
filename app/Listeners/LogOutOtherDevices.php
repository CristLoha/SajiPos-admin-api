<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;

class LogOutOtherDevices
{
    public function handle(Login $event)
    {
        $request = request();
        if ($request->has('password') && $event->guard === 'web') {
            Auth::logoutOtherDevices($request->password);
        }
    }
}
