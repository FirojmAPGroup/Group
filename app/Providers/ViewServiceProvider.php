<?php
namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Use a closure to share the notifications with the 'particles.header' view
        View::composer('particles.header', function ($view) {
            if (Auth::check()) {
                $view->with('notifications', Auth::user()->notifications);
            }
        });
    }

    public function register()
    {
        //
    }
}


