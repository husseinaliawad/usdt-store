<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $resendGlobalClass = base_path('vendor/resend/resend-php/src/Resend.php');

        if (! class_exists('Resend') && file_exists($resendGlobalClass)) {
            require_once $resendGlobalClass;
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(120)->by($request->user()?->id ?: $request->ip()));
        RateLimiter::for('otp', fn (Request $request) => Limit::perMinute(3)->by($request->input('phone', $request->ip())));
    }
}
