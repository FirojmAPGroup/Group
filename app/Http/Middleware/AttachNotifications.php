<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AttachNotifications
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            // Share notifications ordered by creation date descending
            view()->share('notifications', Auth::user()->notifications()->orderBy('created_at', 'desc')->get());
        }

        return $next($request);
    }
}
