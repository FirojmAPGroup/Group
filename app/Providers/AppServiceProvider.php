<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\GeocodingService;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        
    $this->app->singleton(GeocodingService::class, function ($app) {
        return new GeocodingService();
    });
    }




    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('particles.header', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $unreadCount = $user->unreadNotifications()->count();
                $view->with('unreadCount', $unreadCount);
            } else {
                $view->with('unreadCount', 0);
            }
        });
    }
}
