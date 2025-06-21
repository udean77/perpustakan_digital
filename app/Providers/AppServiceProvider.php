<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Cart;
use App\Models\Category;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */

    public function boot()
    {
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $userId = auth()->id();
                $carts = Cart::with('book')->where('user_id', $userId)->get();

                $view->with('cartItems', $carts);
                $view->with('cartCount', $carts->count());
            } else {
                $view->with('cartItems', collect());
                $view->with('cartCount', 0);
            }
        });

            Relation::morphMap([
            'book' => \App\Models\Book::class,
            'user' => \App\Models\User::class,
            'order' => \App\Models\Order::class,
        ]);
    }

}
