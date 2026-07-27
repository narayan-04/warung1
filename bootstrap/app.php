<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

// Ensure storage and bootstrap/cache permissions on boot
$basePath = dirname(__DIR__);
$dirs = [
    $basePath . '/storage',
    $basePath . '/storage/app',
    $basePath . '/storage/app/public',
    $basePath . '/storage/framework',
    $basePath . '/storage/framework/cache',
    $basePath . '/storage/framework/cache/data',
    $basePath . '/storage/framework/sessions',
    $basePath . '/storage/framework/views',
    $basePath . '/storage/logs',
    $basePath . '/bootstrap/cache',
];
foreach ($dirs as $dir) {
    if (!file_exists($dir)) {
        @mkdir($dir, 0775, true);
    }
    if (file_exists($dir) && is_writable($dir)) {
        @chmod($dir, 0775);
    }
}

// Auto-generate APP_KEY if missing in environment variables and .env
if (empty(env('APP_KEY'))) {
    $envPath = $basePath . '/.env';
    if (!file_exists($envPath)) {
        @file_put_contents($envPath, '');
    }
    $envContent = @file_get_contents($envPath);
    if ($envContent !== false && !str_contains($envContent, 'APP_KEY=')) {
        $key = 'base64:' . base64_encode(random_bytes(32));
        @file_put_contents($envPath, $envContent . PHP_EOL . "APP_KEY={$key}" . PHP_EOL);
        $_ENV['APP_KEY'] = $key;
        $_SERVER['APP_KEY'] = $key;
        putenv("APP_KEY={$key}");
    }
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
