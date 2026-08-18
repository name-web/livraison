<?php

use App\Http\Controllers\Backend\MerchantInvoiceController;
use App\Http\Controllers\Backend\MerchantPanel\AccountTransactionController;
use App\Http\Controllers\Backend\MerchantPanel\FraudController as MerchantPanelFraudController;
use App\Http\Controllers\Backend\MerchantPanel\InvoiceController;
use App\Http\Controllers\Backend\MerchantPanel\MerchantOnlinePaymentSetupController;
use App\Http\Controllers\Backend\MerchantPanel\MerchantParcelController;
use App\Http\Controllers\Backend\MerchantPanel\MerchantReportsController;
use App\Http\Controllers\Backend\MerchantPanel\NewsOfferController as MerchantNewsOfferController;
use App\Http\Controllers\Backend\MerchantPanel\OnlinePaymentController;
use App\Http\Controllers\Backend\MerchantPanel\PaymentAccountController;
use App\Http\Controllers\Backend\MerchantPanel\PaymentRequestController;
use App\Http\Controllers\Backend\MerchantPanel\PickupRequestController as MerchantPanelPickupRequestController;
use App\Http\Controllers\Backend\MerchantPanel\ReportsController as MerchantPanelReportsController;
use App\Http\Controllers\Backend\MerchantPanel\SettingsController;
use App\Http\Controllers\Backend\MerchantPanel\ShopsController;
use App\Http\Controllers\Backend\MerchantPanel\StatementsController;
use App\Http\Controllers\Backend\MerchantPanel\SupportController as MerchantPanelSupportController;
use App\Http\Controllers\Backend\MerchantPanel\WalletController;
use App\Http\Controllers\Backend\MerchantProfileController;
use App\Http\Controllers\DashbordController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'merchant', 'middleware' => 'merchant'], function () {

    // Dashboard
    Route::post('/dashboard/filter', [DashbordController::class, 'merchantDashboardFilter'])->name('merchant-panel.dashboard.filter');

    // Accounts
    Route::get('/accounts/payment-accounts', [PaymentAccountController::class, 'index'])->name('merchant.accounts.payment-account.index');
    Route::get('/accounts/payment-accounts/create', [PaymentAccountController::class, 'create'])->name('payment.account.create');
    Route::post('/accounts/payment-account/store', [PaymentAccountController::class, 'store'])->name('payment.account.store');
    Route::get('/accounts/payment-account/edit/{id}', [PaymentAccountController::class, 'edit'])->name('payment.account.edit');
    Route::put('/accounts/payment-account/update', [PaymentAccountController::class, 'update'])->name('payment.account.update');
    Route::delete('/accounts/payment-account/delete/{id}', [PaymentAccountController::class, 'delete'])->name('payment.account.delete');

    // Account Transaction
    Route::get('/accounts/account-transaction', [AccountTransactionController::class, 'index'])->name('merchant.accounts.account-transaction.index');
    Route::post('/accounts/account-transaction-filter', [AccountTransactionController::class, 'filter'])->name('merchant.accounts.account-transaction.filter');

    // Statements
    Route::get('/accounts/statements', [StatementsController::class, 'index'])->name('merchant.accounts.statements.index');
    Route::post('/accounts/statements-filter', [StatementsController::class, 'filter'])->name('merchant.accounts.statements.filter');

    // Settings
    Route::get('/settings/cod-charges', [SettingsController::class, 'CODcharges'])->name('merchant.cod-charges.index');
    Route::get('/settings/delivery-charges', [SettingsController::class, 'deliveryCharges'])->name('merchant.delivery-charges.index');

    // Merchant Profile
    Route::get('profile/{id}', [MerchantProfileController::class, 'view'])->name('merchant-profile.index');
    Route::get('profile/update/{id}', [MerchantProfileController::class, 'create'])->name('merchant-profile.edit');
    Route::get('profile/change-password/{id}', [MerchantProfileController::class, 'changePassword'])->name('merchant-password.change');
    Route::put('profile/update/{id}', [MerchantProfileController::class, 'update'])->name('merchant-profile.update');
    Route::put('profile/update-password/{id}', [MerchantProfileController::class, 'updatePassword'])->name('merchant-profile.password.update');

    // Shops
    Route::get('shops/index', [ShopsController::class, 'index'])->name('merchant-panel.shops.index');
    Route::get('shops/create', [ShopsController::class, 'create'])->name('merchant-panel.shops.create');
    Route::post('shops/store', [ShopsController::class, 'store'])->name('merchant-panel.shops.store');
    Route::get('shops/edit/{id}', [ShopsController::class, 'edit'])->name('merchant-panel.shops.edit');
    Route::put('shops/update/{id}', [ShopsController::class, 'update'])->name('merchant-panel.shops.update');
    Route::delete('shops/delete/{id}', [ShopsController::class, 'delete'])->name('merchant-panel.shops.delete');

    // Parcel
    Route::get('parcel/filter', [MerchantParcelController::class, 'filter'])->name('merchant-panel.parcel.filter');
    Route::get('parcel/index', [MerchantParcelController::class, 'index'])->name('merchant-panel.parcel.index');
    Route::get('parcel/create', [MerchantParcelController::class, 'create'])->name('merchant-panel.parcel.create');
    Route::post('parcel/store', [MerchantParcelController::class, 'store'])->name('merchant-panel.parcel.store');
    Route::get('parcel/clone/{id}', [MerchantParcelController::class, 'duplicate'])->name('merchant-parcel.clone');
    Route::post('parcel/clone-store', [MerchantParcelController::class, 'duplicateStore'])->name('merchant-parcel.clone-store');
    Route::get('parcel/edit/{id}', [MerchantParcelController::class, 'edit'])->name('merchant-panel.parcel.edit');
    Route::get('parcel/details/{id}', [MerchantParcelController::class, 'details'])->name('merchant-panel.parcel.details');
    Route::get('parcel/logs/{id}', [MerchantParcelController::class, 'logs'])->name('merchant-panel.parcel.logs');
    Route::put('parcel/update/{id}', [MerchantParcelController::class, 'update'])->name('merchant-panel.parcel.update');
    Route::get('parcel/status-update/{id}/{status_id}', [MerchantParcelController::class, 'statusUpdate'])->name('merchant-panel.parcel.status-update');
    Route::delete('parcel/delete/{id}', [MerchantParcelController::class, 'destroy'])->name('merchant-panel.parcel.delete');
    Route::post('parcel/merchant', [MerchantParcelController::class, 'getMerchant'])->name('merchant-panel.parcel.merchant.get');
    Route::post('parcel/merchant/shops', [MerchantParcelController::class, 'merchantShops'])->name('merchant-panel.parcel.merchant.shops');
    Route::post('parcel/delivery-category', [MerchantParcelController::class, 'deliveryWeight'])->name('merchant-panel.parcel.deliveryCategory.deliveryWeight');
    Route::post('parcel/delivery-charge', [MerchantParcelController::class, 'deliveryCharge'])->name('merchant-panel.parcel.deliveryCharge.get');

    // Import/Export
    Route::get('parcel/import-parcel', [MerchantParcelController::class, 'parcelImportExport'])->name('merchant-panel.parcel.parcel-import');
    Route::post('parcel/file-import', [MerchantParcelController::class, 'parcelImport'])->name('merchant-panel.parcel.file-import');
    Route::get('parcel/file-export', [MerchantParcelController::class, 'parcelExport'])->name('merchant-panel.parcel.file-export');

    // Reports
    Route::get('reports/parcel-reports', [MerchantReportsController::class, 'parcelReports'])->name('merchant-panel.parcel.reports');
    Route::get('reports/parcel-filter-reports', [MerchantReportsController::class, 'parcelSReports'])->name('merchant-panel.parcel.filter.reports');
    Route::get('parcel-reports-print-page/{array}', [MerchantReportsController::class, 'parcelReportsPrint'])->name('merchant-panel.parcel.reports.print.page');

    // Payment Request
    Route::get('payment-request/index', [PaymentRequestController::class, 'index'])->name('merchant-panel.payment-request.index');
    Route::get('payment-request/create', [PaymentRequestController::class, 'create'])->name('merchant-panel.payment-request.create');
    Route::post('payment-request/store', [PaymentRequestController::class, 'store'])->name('merchant-panel.payment-request.store');
    Route::get('payment-request/edit/{id}', [PaymentRequestController::class, 'edit'])->name('merchant-panel.payment-request.edit');
    Route::put('payment-request/update', [PaymentRequestController::class, 'update'])->name('merchant-panel.payment-request.update');
    Route::delete('payment-request/delete/{id}', [PaymentRequestController::class, 'delete'])->name('merchant-panel.payment-request.delete');

    // News & Offer
    Route::get('news-offer/index', [MerchantNewsOfferController::class, 'index'])->name('merchant-panel.news-offer.index');

    // Support
    Route::get('support/index', [MerchantPanelSupportController::class, 'index'])->name('merchant-panel.support.index');
    Route::get('support/create', [MerchantPanelSupportController::class, 'create'])->name('merchant-panel.support.add');
    Route::post('support/store', [MerchantPanelSupportController::class, 'store'])->name('merchant-panel.support.store');
    Route::get('support/edit/{id}', [MerchantPanelSupportController::class, 'edit'])->name('merchant-panel.support.edit');
    Route::put('support/update/{id}', [MerchantPanelSupportController::class, 'update'])->name('merchant-panel.support.update');
    Route::delete('support/delete/{id}', [MerchantPanelSupportController::class, 'destroy'])->name('merchant-panel.support.delete');
    Route::get('support/view/{id}', [MerchantPanelSupportController::class, 'view'])->name('merchant-panel.support.view');
    Route::post('support/reply', [MerchantPanelSupportController::class, 'supportReply'])->name('merchant-panel.support.reply');

    // Fraud
    Route::get('fraud', [MerchantPanelFraudController::class, 'index'])->name('merchant-panel.fraud.index');
    Route::get('fraud/create', [MerchantPanelFraudController::class, 'create'])->name('merchant-panel.fraud.create');
    Route::post('fraud/store', [MerchantPanelFraudController::class, 'store'])->name('merchant-panel.fraud.store');
    Route::get('fraud/edit/{id}', [MerchantPanelFraudController::class, 'edit'])->name('merchant-panel.fraud.edit');
    Route::put('fraud/update', [MerchantPanelFraudController::class, 'update'])->name('merchant-panel.fraud.update');
    Route::delete('fraud/delete/{id}', [MerchantPanelFraudController::class, 'destroy'])->name('merchant-panel.fraud.delete');
    Route::get('fraud/filter', [MerchantPanelFraudController::class, 'filter'])->name('merchant-panel.fraud.filter');
    Route::post('fraud/check', [MerchantPanelFraudController::class, 'check'])->name('merchant-panel.fraud.check');

    // Total Summary
    Route::get('reports/total-summery', [MerchantPanelReportsController::class, 'TotalSummeryReports'])->name('merchant.total.summery');
    Route::get('reports/total-summery-filter', [MerchantPanelReportsController::class, 'TotalSummeryReportsFilter'])->name('merchant.parcel.filter.total.summery');

    // Pickup Request
    Route::prefix('pickup-request')->name('merchant.panel.pickup.request.')->group(function () {
        Route::post('regular', [MerchantPanelPickupRequestController::class, 'regularStore'])->name('regular.store');
        Route::post('express', [MerchantPanelPickupRequestController::class, 'expressStore'])->name('express.store');
    });

    // Invoice
    Route::prefix('invoice')->name('merchant.panel.invoice.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/{invoice_id}', [InvoiceController::class, 'InvoiceDetails'])->name('details');
        Route::get('/pdf/{merchant_id}/{invoice_id}', [MerchantInvoiceController::class, 'InvoicePdf'])->name('pdf');
        Route::get('/csv/{merchant_id}/{invoice_id}', [MerchantInvoiceController::class, 'InvoiceCSV'])->name('csv');
    });

    // Online Payment Setup
    Route::get('/settings/online-payment-setup', [MerchantOnlinePaymentSetupController::class, 'index'])->name('merchant.online.payment.setup.index');
    Route::put('/settings/online-payment-setup/update/{paymentmethod}', [MerchantOnlinePaymentSetupController::class, 'paymentReceivedSetupUpdate'])->name('merchant.online.payment.setup.update');
    Route::get('online-payment-received-list', [MerchantOnlinePaymentSetupController::class, 'onlinePaymentReceivedList'])->name('merchant.online.payment.list');

    // Online Payment Module
    Route::get('/payment/received', [OnlinePaymentController::class, 'merchantPaymentReceived'])->name('online.payment.received');
    Route::prefix('online-payment')->name('online.payment.')->group(function () {
        Route::get('/', [OnlinePaymentController::class, 'index'])->name('index');
        Route::get('/stripe', [OnlinePaymentController::class, 'stripe'])->name('stripe');
        Route::post('/stripe/post', [OnlinePaymentController::class, 'stripePost'])->name('stripe.post');
        Route::get('paypal-index', [OnlinePaymentController::class, 'paypalIndex'])->name('paypal.index');
        Route::post('paypal-payment', [OnlinePaymentController::class, 'paypalpayment'])->name('paypal');
        Route::get('/sslcommerz', [OnlinePaymentController::class, 'sslcommerzIndex'])->name('sslcommerz.index');
        Route::get('/aamarpay', [OnlinePaymentController::class, 'aamarpayIndex'])->name('aamarpay.index');
        Route::get('/paystack', [OnlinePaymentController::class, 'paystackIndex'])->name('paystack.index');
        Route::get('/paystack/initialize-payment', [OnlinePaymentController::class, 'initializePayment'])->name('paystack.initialize.payment');
        Route::get('/paystack/verify-payment', [OnlinePaymentController::class, 'verifyPayment'])->name('paystack.verify.payment');
    });

    // Wallet
    Route::prefix('my-wallet')
        ->controller(WalletController::class)
        ->name('merchant-panel.my.wallet.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/recharge', 'recharge')->name('recharge');
            Route::post('/recharge-add', 'rechargeAdd')->name('recharge.add');
            Route::post('/recharge-status', 'rechargeStatus')->name('recharge.status');
        });
});
