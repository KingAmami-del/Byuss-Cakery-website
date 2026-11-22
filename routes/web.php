<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Halaman berikut akan dikembangkan pada tahap berikutnya.
Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog');
Route::view('/birthday-cake', 'birthday-cake')->name('birthday-cake');
Route::redirect('/cara-pesan', '/birthday-cake')->name('how-to-order.legacy');
Route::view('/keranjang', 'coming-soon', ['title' => 'Keranjang'])->name('cart');

Route::get('/lacak-pesanan', [OrderTrackingController::class, 'create'])->name('orders.track.create');
Route::post('/lacak-pesanan', [OrderTrackingController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('orders.track.store');
Route::get('/lacak-pesanan/{orderNumber}', [OrderTrackingController::class, 'show'])
    ->middleware('throttle:5,1')
    ->name('orders.track.show');
Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:10,1')->name('checkout.store');
Route::get('/pesanan/{order:order_number}/pembayaran/{token}', [PaymentController::class, 'show'])->name('orders.payment');
Route::post('/pesanan/{order:order_number}/bukti/{token}', [PaymentController::class, 'storeProof'])
    ->middleware('throttle:10,1')
    ->name('orders.payment-proof');

require __DIR__.'/admin.php';
