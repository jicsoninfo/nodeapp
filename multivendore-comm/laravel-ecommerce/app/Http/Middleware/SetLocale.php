<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    private const SUPPORTED = ['en','hi','ar','de','fr','zh','ja','es','pt','ru'];

    public function handle(Request $request, Closure $next)
    {
        $locale = $request->header('Accept-Language', 'en');
        $locale = substr($locale, 0, 2);

        if (in_array($locale, self::SUPPORTED)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
