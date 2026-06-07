<?php

use Illuminate\Support\Facades\Route;

Route::middleware([\App\Http\Middleware\SecurityHeaders::class])
    ->get('/{any}', function () {
        $path = public_path('index.html');
        if (!file_exists($path)) {
            return response("Frontend build not found.", 404);
        }
        return response(file_get_contents($path))
            ->header('Content-Type', 'text/html; charset=utf-8');
    })->where('any', '^(?!assets|logojtik\.png).*$');