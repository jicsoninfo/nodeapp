<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Priority: URL segment → session → browser → default
        $locale = $request->segment(1);

        // Check if first URL segment is a valid active language code
        $validCodes = Language::active()->pluck('code')->toArray();

        if ($locale && in_array($locale, $validCodes, true)) {
            App::setLocale($locale);
            session(['locale' => $locale]);
        } elseif (session('locale') && in_array(session('locale'), $validCodes, true)) {
            App::setLocale(session('locale'));
        } else {
            // Try browser preference
            $browser = substr($request->getPreferredLanguage($validCodes) ?? '', 0, 2);
            App::setLocale(in_array($browser, $validCodes, true) ? $browser : config('app.locale'));
        }

        return $next($request);
    }
}
