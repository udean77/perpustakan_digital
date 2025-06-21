<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckStoreActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $store = Store::where('user_id', $user->id)->first();

        if (!$store || $store->status !== 'active') {
            return redirect()->route('user.homepage')->with('warning', 'Toko Anda sedang dinonaktifkan oleh admin');
        }

        return $next($request);
    }

}
