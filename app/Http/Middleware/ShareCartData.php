<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;

class ShareCartData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $cartCount = 0;
        $cartItems = collect();

        if (Auth::check()) {
            $cartItems = Cart::with('book')->where('user_id', Auth::id())->get();
            $cartCount = $cartItems->count();
        }

        // Share cart data with all views
        view()->share('cartCount', $cartCount);
        view()->share('cartItems', $cartItems);

        return $next($request);
    }
} 