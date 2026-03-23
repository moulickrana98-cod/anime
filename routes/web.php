<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\ProductController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ─── Authentication ───────────────────────────────────────────────────────────
Auth::routes();

// ─── Shop (Public) ────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [ProductController::class, 'index'])->name('shop.index');
Route::get('/shop/category/{category:slug}', [ProductController::class, 'byCategory'])->name('shop.category');
Route::get('/shop/{product:slug}', [ProductController::class, 'show'])->name('shop.show');

// ─── Cart ─────────────────────────────────────────────────────────────────────
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add/{product}', [CartController::class, 'add'])->name('add');
    Route::patch('/update/{rowId}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{rowId}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
});

// ─── Checkout (Requires Auth) ─────────────────────────────────────────────────
Route::middleware('auth')->prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/process', [CheckoutController::class, 'process'])->name('process');
    Route::get('/success', [CheckoutController::class, 'success'])->name('success');
    Route::get('/cancel', [CheckoutController::class, 'cancel'])->name('cancel');
});

// ─── Stripe Webhook (No CSRF) ─────────────────────────────────────────────────
Route::post('/stripe/webhook', [CheckoutController::class, 'webhook'])
    ->name('stripe.webhook')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// ─── Admin Panel ──────────────────────────────────────────────────────────────
Route::middleware(['auth', 'is.admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Products CRUD
    Route::resource('products', AdminProductController::class);

    // Categories CRUD
    Route::resource('categories', CategoryController::class);

    // Orders (View + Status Update)
    Route::resource('orders', AdminOrderController::class)->only(['index', 'show', 'update']);
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');

    // Users CRUD
    Route::resource('users', UserController::class);

    // Instagram settings
    Route::get('/settings/instagram', [\App\Http\Controllers\Admin\SettingsController::class, 'instagram'])
        ->name('settings.instagram');
    Route::post('/settings/instagram', [\App\Http\Controllers\Admin\SettingsController::class, 'updateInstagram'])
        ->name('settings.instagram.update');
});

// ─── User Account ─────────────────────────────────────────────────────────────
Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
    Route::get('/orders', [\App\Http\Controllers\Shop\AccountController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [\App\Http\Controllers\Shop\AccountController::class, 'showOrder'])->name('orders.show');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
