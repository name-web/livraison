<?php

/*
|--------------------------------------------------------------------------
| Laravel WeCourier App — Routes Web
|--------------------------------------------------------------------------
|
| Les routes sont organisées en fichiers modulaires :
|   - routes/frontend.php  → Pages publiques
|   - routes/admin.php     → Panel administrateur
|   - routes/merchant.php  → Panel marchand
|
*/

use App\Http\Controllers\AamarpayController;
use App\Http\Controllers\Backend\BkashController;
use App\Http\Controllers\Backend\SkrillController;
use App\Http\Controllers\Backend\SslCommerzPaymentController as UserSslCommerzPaymentController;
use App\Http\Controllers\Backend\WebNotificationController;
use App\Http\Controllers\InstallerController;
use App\Http\Controllers\MapParcelController;
use Illuminate\Support\Facades\Route;

// ─── Installer ────────────────────────────────────
Route::middleware(['XSS', 'IsNotInstalled'])->group(function () {
    Route::get('install', [InstallerController::class, 'index']);
});

Route::middleware(['XSS'])->group(function () {
    Route::post('installing', [InstallerController::class, 'installing'])->name('installing');
    Route::get('finish', [InstallerController::class, 'finish'])->name('final');
});

// ─── Application ──────────────────────────────────
Route::middleware(['XSS', 'IsInstalled'])->group(function () {
    Auth::routes();

    // Frontend public + auth pages
    require __DIR__.'/frontend.php';

    Route::group(['middleware' => 'auth'], function () {
        Route::group(['middleware' => 'XSS'], function () {

            // Admin panel
            require __DIR__.'/admin.php';

            // Merchant panel
            require __DIR__.'/merchant.php';

            // SSL Commerz (user)
            Route::post('/pay-via-ajax', [UserSslCommerzPaymentController::class, 'payViaAjax']);
            Route::post('/success', [UserSslCommerzPaymentController::class, 'success']);
            Route::post('/fail', [UserSslCommerzPaymentController::class, 'fail']);
            Route::post('/cancel', [UserSslCommerzPaymentController::class, 'cancel']);
            Route::post('/ipn', [UserSslCommerzPaymentController::class, 'ipn']);

            // Skrill (user)
            Route::get('skrill', [SkrillController::class, 'index'])->name('skrill.index');
            Route::get('skrill-make-payment', [SkrillController::class, 'makePayment'])->name('skrill.make.payment');
            Route::get('payment-completed', [SkrillController::class, 'paymentCompleted'])->name('skrill.payment.completed');
            Route::get('payment-cancelled', [SkrillController::class, 'PaymentCancelled']);

            // Bkash (user)
            Route::get('/online-payment/bkash', [BkashController::class, 'index'])->name('online.payment.bkash.index');
            Route::get('bkash/redirect', [BkashController::class, 'bkashRedirect'])->name('bkash.redirect');
            Route::get('bkash/execute', [BkashController::class, 'bkashExecute'])->name('bkash.execute');

            // Aamarpay (user)
            Route::get('/aamarpay-payment', [AamarpayController::class, 'payment'])->name('aamarpay.payment');
            Route::post('/aamarpay-success', [AamarpayController::class, 'success'])->name('aamarpay.payment.success');
            Route::post('/aamarpay-fail', [AamarpayController::class, 'fail'])->name('aamarpay.payment.fail');
        });

        // FCM Token
        Route::post('/store-token', [WebNotificationController::class, 'store'])->name('notification-store.token');
    });

    // Deliveryman map
    Route::get('/deliveryMan/parcel/map/{id}/{lat}/{long}/{status}', [MapParcelController::class, 'parcelMap']);
});
