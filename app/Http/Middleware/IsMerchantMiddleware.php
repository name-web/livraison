<?php

namespace App\Http\Middleware;

use App\Enums\UserType;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsMerchantMiddleware
{
    /**
     * Restreindre le panneau marchand aux seuls utilisateurs de type MERCHANT.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->user_type == UserType::MERCHANT) {
            return $next($request);
        }

        abort(403);
    }
}
