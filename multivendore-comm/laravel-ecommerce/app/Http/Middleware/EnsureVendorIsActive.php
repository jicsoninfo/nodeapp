<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureVendorIsActive
{
    public function handle(Request $request, Closure $next)
    {
        $vendor = $request->user()?->vendor;

        if (! $vendor || ! $vendor->canSell()) {
            return response()->json([
                'message' => 'Your vendor account is not active.',
            ], 403);
        }

        $request->merge(['current_vendor' => $vendor]);

        return $next($request);
    }
}
