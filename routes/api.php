<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\BookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/books', function (Request $request) {
    $query = \App\Models\Book::query();
    
    if ($request->has('search')) {
        $search = $request->get('search');
        $query->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('author', 'like', "%{$search}%");
    }
    
    $books = $query->limit(10)->get(['id', 'title', 'author', 'description', 'price', 'stock']);
    
    return response()->json([
        'success' => true,
        'data' => $books,
        'count' => $books->count()
    ]);
});
