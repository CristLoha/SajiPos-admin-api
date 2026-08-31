<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terlalu banyak percobaan login. Silakan tunggu beberapa saat dan coba lagi.'
                ], 429);
            }
        });

        // Tambahkan fallback untuk semua error 500 di API agar user-friendly
        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                // Biarkan Exception bawaan Laravel (seperti 404, 422, 401) ditangani seperti biasa
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface || 
                    $e instanceof \Illuminate\Validation\ValidationException || 
                    $e instanceof \Illuminate\Auth\AuthenticationException) {
                    return null; // Biarkan default handler laravel yang urus
                }

                // Untuk error lainnya (misal DB error, syntax error, dll), kita sembunyikan detailnya
                return response()->json([
                    'success' => false,
                    'message' => 'Oops, server sedang bermasalah atau sedang dalam perbaikan. Coba lagi nanti ya!'
                ], 500);
            }
        });
    })->create();
