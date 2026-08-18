<?php

use App\Http\Controllers\Backend\AdminAamarpayController;
use App\Http\Controllers\Backend\AdminBkashController;
use App\Http\Controllers\Backend\AdminPaystackController;
use App\Http\Controllers\Backend\AdminSkrillController;
use App\Http\Controllers\Backend\AdminSslCommerzController;
use App\Http\Controllers\Backend\PaymobController;
use App\Http\Controllers\Backend\PayoutController;
use App\Http\Controllers\Backend\PayoutSetupController;
use Illuminate\Support\Facades\Route;

Route::prefix('payout')->name('payout.')->group(function () {
    Route::get('/', [PayoutController::class, 'index'])->name('index');
    Route::get('/merchant/payout', [PayoutController::class, 'merchantPayout'])->name('merchant.payout');
    Route::get('/stripe', [PayoutController::class, 'stripe'])->name('merchant.stripe');
    Route::post('/stripe/post', [PayoutController::class, 'stripePost'])->name('merchant.stripe.post');
    Route::get('/razorpay', [PayoutController::class, 'razorpay'])->name('merchant.razorpay');
    Route::get('/razorpay/payment', [PayoutController::class, 'razorpayPost'])->name('merchant.razorpay.post');
    Route::get('paypal-index', [PayoutController::class, 'paypalIndex'])->name('paypal.index');
    Route::post('paypal-payment', [PayoutController::class, 'paypalpayment'])->name('paypal');

    // SSLCOMMERZ
    Route::get('/sslcommerz', [AdminSslCommerzController::class, 'sslcommerzIndex'])->name('sslcommerz.index');
    Route::post('/pay-via-ajax', [AdminSslCommerzController::class, 'payViaAjax'])->name('pay.via.ajax');
    Route::post('/success', [AdminSslCommerzController::class, 'success']);
    Route::post('/fail', [AdminSslCommerzController::class, 'fail']);
    Route::post('/cancel', [AdminSslCommerzController::class, 'cancel']);
    Route::post('/ipn', [AdminSslCommerzController::class, 'ipn']);

    // Skrill
    Route::get('skrill', [AdminSkrillController::class, 'index'])->name('skrill.index');
    Route::get('skrill-make-payment', [AdminSkrillController::class, 'makePayment'])->name('skrill.make.payment');
    Route::get('payment-completed', [AdminSkrillController::class, 'paymentCompleted'])->name('skrill.payment.completed');
    Route::get('payment-cancelled', [AdminSkrillController::class, 'PaymentCancelled']);

    // Aamarpay
    Route::get('/aamarpay', [AdminAamarpayController::class, 'aamarpayIndex'])->name('aamarpay.index');
    Route::get('/aamarpay-payment', [AdminAamarpayController::class, 'payment'])->name('aamarpay.payment');
    Route::post('/aamarpay-success', [AdminAamarpayController::class, 'success'])->name('aamarpay.payment.success');
    Route::post('/aamarpay-fail', [AdminAamarpayController::class, 'fail'])->name('aamarpay.payment.fail');

    // Bkash
    Route::get('/online-payment/bkash', [AdminBkashController::class, 'index'])->name('bkash.index');
    Route::get('bkash/redirect', [AdminBkashController::class, 'bkashRedirect'])->name('bkash.redirect');
    Route::get('bkash/execute', [AdminBkashController::class, 'bkashExecute'])->name('bkash.execute');

    // Paystack
    Route::get('/paystack', [AdminPaystackController::class, 'index'])->name('paystack.index');
    Route::get('/paystack/initialize-payment', [AdminPaystackController::class, 'initializePayment'])->name('paystack.initialize.payment');
    Route::get('/paystack/verify-payment', [AdminPaystackController::class, 'verifyPayment'])->name('paystack.verify.payment');

    // PAYMOB
    Route::group(['prefix' => 'merchant/paymob', 'as' => 'merchant.panel.paymob.'], function () {
        Route::any('pay', [PaymobController::class, 'credit'])->name('pay');
        Route::any('callback', [PaymobController::class, 'callback'])->name('callback');
    });
});

Route::get('online-payment-list', [PayoutSetupController::class, 'onlinePaymentList'])->name('online.payment.list')->middleware('hasPermission:online_payment_read');
Route::get('/settings/pay-out/setup', [PayoutSetupController::class, 'index'])->name('payout.setup.settings.index')->middleware('hasPermission:payout_setup_settings_read');
Route::put('/settings/pay-out/setup/update/{paymentmethod}', [PayoutSetupController::class, 'PayoutSetupUpdate'])->name('payout.setup.settings.update')->middleware('hasPermission:payout_setup_settings_update');
