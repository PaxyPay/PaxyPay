<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->session()->has('locale')) {
            app()->setLocale($request->session()->get('locale'));
        } else {
            app()->setLocale(config('app.locale'));
        }

        return $next($request);
    }
}