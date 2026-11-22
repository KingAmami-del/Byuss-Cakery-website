<?php

use Illuminate\Support\Facades\Route;

/*
| Rute admin Byuss Cakery.
| Dimuat dari routes/web.php lewat satu baris di paling bawah:
|     require __DIR__ . '/admin.php';
| Nama kelas ditulis lengkap, jadi tidak perlu baris "use" apa pun.
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [\App\Http\Controllers\Admin\AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login.submit');

    Route::middleware(\App\Http\Middleware\EnsureAdmin::class)->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AuthController::class, 'dashboard'])->name('dashboard');
        Route::get('orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
        Route::get('orders/{order}/proof', [\App\Http\Controllers\Admin\OrderController::class, 'proof'])->name('orders.proof');
        Route::patch('orders/{order}/payment', [\App\Http\Controllers\Admin\OrderController::class, 'review'])->name('orders.review');
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class)->except(['show']);
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->except(['show']);
        Route::post('logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');
    });
});
