<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

// HALAMAN HOME
Route::get('/', function () {
    $products = Product::latest()->take(8)->get();
    return view('user.home', compact('products'));
})->name('home');

// DASHBOARD ADMIN
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'admin'])->name('dashboard');

// ROUTE YANG MEMBUTUHKAN LOGIN
Route::middleware('auth')->group(function () {

    // PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // CATEGORY & PRODUCT
    Route::resource('/category', CategoryController::class);
    Route::resource('/products', ProductController::class);

    // KERANJANG PESANAN
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update/{product}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');

    // CHECKOUT
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/sukses', [CheckoutController::class, 'sukses'])->name('checkout.sukses');
    Route::put('/checkout/{order}/bukti-pembayaran', [CheckoutController::class, 'updatePaymentProof'])->name('checkout.updatePaymentProof');

    // ORDERS / RIWAYAT PESANAN
    Route::get('/orders/history', [OrderController::class, 'history'])->name('orders.history');

});

// AUTH ROUTES
require __DIR__ . '/auth.php';
