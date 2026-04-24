<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\PaymentController;

Route::get('/', [CashierController::class, 'index'])->name('cashier.index');

Route::resource('menus', MenuController::class);

Route::post('/checkout', [PaymentController::class, 'checkout'])->name('checkout');
Route::post('/checkout/cash', [PaymentController::class, 'checkoutCash'])->name('checkout.cash');
Route::post('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/receipt/{order_number}', [PaymentController::class, 'receipt'])->name('receipt');
