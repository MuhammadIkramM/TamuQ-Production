<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Vite;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Vite::useScriptTagAttributes([
            'nonce' => function () {
                return app()->bound('csp-nonce') ? app('csp-nonce') : '';
            },
        ]);
        
        Vite::useStyleTagAttributes([
            'nonce' => function () {
                return app()->bound('csp-nonce') ? app('csp-nonce') : '';
            },
        ]);
    }
}
