<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\Seller\StoreController as SellerStoreController;
use App\Http\Controllers\Seller\BookController as SellerBookController;
use App\Http\Controllers\Seller\ReportController as SellerReportController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminSellerController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\RedeemCodeController as AdminRedeemCodeController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Seller\OrderController as SellerOrderController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RedeemCodeController;
use App\Http\Controllers\Seller\UserOrderSummaryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\ChatHistoryController;


// Homepage publik
Route::get('/homepage', [UserController::class, 'homepage'])->name('user.homepage');

Route::middleware(['auth'])->group(function () {
    // Profile
    Route::get('/homepage', [UserController::class, 'homepage'])->name('user.homepage');
    Route::get('/profile', [ProfileController::class, 'showProfile'])->name('user.profile');
    Route::post('/become-seller', [UserController::class, 'becomeSeller'])->name('user.becomeSeller');
    Route::post('/profile/update', [UserController::class, 'updateProfile'])->name('user.updateProfile');
    Route::post('/profile/upload-photo', [ProfileController::class, 'uploadPhoto'])->name('profile.updatePhoto');
    Route::get('/profile/edit/{field}', [UserController::class, 'editField'])->name('profile.edit');
    Route::post('/profile/update/{field}', [UserController::class, 'updateField'])->name('profile.updateField');
    Route::get('/profile/change-password', [UserController::class, 'showChangePasswordForm'])->name('profile.changePassword');
    Route::post('/profile/change-password', [UserController::class, 'changePasswordSubmit'])->name('profile.changePassword.submit');
    Route::post('/alamat', [AddressController::class, 'store'])->name('address.store');
    Route::get('/alamat/{id}/edit', [AddressController::class, 'edit'])->name('address.edit');
    Route::put('/alamat/{id}', [AddressController::class, 'update'])->name('address.update');
    Route::delete('/address/{id}', [AddressController::class, 'destroy'])->name('address.delete');

    // Route biasa
    Route::get('/store/{id}', [StoreController::class, 'show'])->name('user.store.show');
    Route::get('/stores', [StoreController::class, 'index'])->name('user.store.index');


    // untuk user buka toko
    Route::get('/buka-toko', [SellerController::class, 'create'])->name('seller.store.create');
    Route::post('/buka-toko', [SellerController::class, 'store'])->name('seller.register');

    // cart
    Route::get('/cart', [CartController::class, 'index'])->name('user.cart.index');
    Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::put('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
    // Clear semua item di keranjang
    Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');


    // COmment 
    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy');


    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('user.wishlist.index');
    Route::post('/wishlist', [WishlistController::class, 'store'])->name('user.wishlist.store');
    Route::post('/wishlist/toggle/{book}', [WishlistController::class, 'toggleWishlist'])->name('user.wishlist.toggle');
    Route::delete('/wishlist/{book_id}', [WishlistController::class, 'destroy'])->name('user.wishlist.destroy');

    
    // book
    Route::resource('books', BookController::class)->only(['index', 'show'])->names('books');
    
    // Transaction
    Route::get('/transactions', [TransactionController::class, 'index'])->name('user.transaction.index');
    Route::get('/transactions/{id}', [TransactionController::class, 'show'])->name('user.transaction.show');
    Route::post('/transactions/{id}/cancel', [TransactionController::class, 'cancel'])->name('transactions.cancel');
    Route::post('/transactions/{id}/confirm', [TransactionController::class, 'confirmPayment'])->name('transactions.confirm');
    
    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/buy-now/{book}', [CheckoutController::class, 'buyNowForm'])->name('checkout.buyNowForm');
    Route::post('/checkout/buy-now/process', [CheckoutController::class, 'processBuyNow'])->name('checkout.buyNowProcess');
    
    // Payment Routes
    Route::get('/payment/{orderId}', [PaymentController::class, 'createPayment'])->name('payment.create');
    Route::get('/payment/finish', [PaymentController::class, 'finish'])->name('payment.finish');
    Route::get('/payment/error', [PaymentController::class, 'error'])->name('payment.error');
    Route::get('/payment/pending', [PaymentController::class, 'pending'])->name('payment.pending');
    
    // Order
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('user.orders.show');
    Route::patch('user/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('user.orders.cancel');

    Route::get('reports', [ReportController::class, 'index'])->name('user.reports.index');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('user.reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('user.reports.store');

    // Redeem Code
    Route::post('/redeem-code/validate', [RedeemCodeController::class, 'validateCode'])->name('redeem_code.validate');
    Route::post('/redeem-code/use', [RedeemCodeController::class, 'useCode'])->name('redeem_code.use');
    Route::get('/redeem-code/available', [RedeemCodeController::class, 'getAvailableCodes'])->name('redeem_code.available');




    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/login');
    })->middleware('auth')->name('logout');    
});


// AUTH route
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});


Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Resource route untuk manajemen pengguna
    Route::resource('/users', AdminUserController::class)->names('admin.users');

    Route::patch('/admin/users/{user}/toggle', [AdminUserController::class, 'toggleStatus'])->name('admin.users.toggle');
    Route::post('/admin/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('admin.users.reset-password');
    Route::patch('/admin/users/{user}/change-role', [AdminUserController::class, 'changeRole'])->name('admin.users.change-role');


    // Resource route untuk manajemen penjual
    Route::resource('/sellers', AdminSellerController::class)->names('admin.seller');

    Route::post('sellers/{id}/activate', [AdminSellerController::class, 'activate'])->name('admin.seller.activate');
    Route::post('sellers/{id}/deactivate', [AdminSellerController::class, 'deactivate'])->name('admin.seller.deactivate');
    Route::post('sellers/{id}/verify', [AdminSellerController::class, 'verify'])->name('admin.seller.verify');


    // Resource route untuk manajemen buku
    Route::resource('/books', AdminBookController::class)->names('admin.books');

     // Tambahkan route toggle-status di sini
    Route::post('/books/{book}/toggle-status', [AdminBookController::class, 'toggleStatus'])->name('admin.books.toggleStatus');

    // Resource route untuk manajemen pesanan
    Route::resource('/orders', AdminOrderController::class)->names('admin.orders');

    // Resource route untuk manajemen kode redeem
    Route::resource('/redeem_code', AdminRedeemCodeController::class)->names('admin.redeem_code');
    Route::post('/redeem_code/{redeemCode}/toggle-status', [AdminRedeemCodeController::class, 'toggleStatus'])->name('admin.redeem_code.toggleStatus');
    Route::post('/redeem_code/generate-multiple', [AdminRedeemCodeController::class, 'generateMultiple'])->name('admin.redeem_code.generateMultiple');

    Route::get('/transaction', [AdminTransactionController::class, 'index'])->name('admin.transaction.index');
    Route::get('transactions/{id}', [AdminTransactionController::class, 'show'])->name('admin.transaction.show');

    Route::get('/reports', [AdminReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/reports/{id}', [AdminReportController::class, 'show'])->name('admin.reports.show');
    Route::patch('/reports/{id}/status', [AdminReportController::class, 'updateStatus'])->name('admin.reports.updateStatus');
    Route::delete('/reports/{id}', [AdminReportController::class, 'destroy'])->name('admin.reports.destroy');

    
    
});


// Setelah jadi penjual (role:penjual)
Route::middleware(['auth', 'role:penjual', 'checkStoreActive'])->prefix('seller')->group(function () {
    Route::get('/dashboard', [SellerController::class, 'dashboard'])->name('seller.dashboard');
    Route::resource('books', SellerBookController::class)->names('seller.books');
    Route::get('/store/edit', [SellerStoreController::class, 'edit'])->name('seller.store.edit');
    Route::put('/store/update', [SellerStoreController::class, 'update'])->name('seller.store.update');
    Route::post('/store/report', [SellerStoreController::class, 'reportIssue'])->name('seller.store.report');
    
    Route::resource('orders', SellerOrderController::class)->names('seller.orders');
    Route::patch('/seller/orders/{order}/cancel', [SellerOrderController::class, 'cancel'])->name('seller.orders.cancel');
    Route::get('/user/sales-summary', [UserOrderSummaryController::class, 'index'])->name('seller.details.index');

    
    Route::get('/reports', [SellerReportController::class, 'index'])->name('seller.reports.index');
    Route::get('/reports/{id}', [SellerReportController::class, 'show'])->name('seller.reports.show');
    Route::patch('/reports/{id}/status', [SellerReportController::class, 'updateStatus'])->name('seller.reports.updateStatus');
    Route::delete('/reports/{id}', [SellerReportController::class, 'destroy'])->name('seller.reports.destroy');
        
});

 

Route::get('/', [UserController::class, 'homepage']);
Route::get('/homepage', [UserController::class, 'homepage'])->name('user.homepage');


// Payment notification from Midtrans (no auth required)
Route::post('/payment/notification', [PaymentController::class, 'notification'])->name('payment.notification');

Route::view('/chat-ollama', 'chat-ollama');
Route::get('/chat', function () {
    return view('chat');
})->name('chat');


Route::get('/admin/chat-histories', [ChatHistoryController::class, 'index'])->middleware('auth');
