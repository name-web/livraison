<?php

use App\Http\Controllers\Backend\AccidentController;
use App\Http\Controllers\Backend\AccountController;
use App\Http\Controllers\Backend\AccountHeadsController;
use App\Http\Controllers\Backend\ActiveLogController;
use App\Http\Controllers\Backend\AddonController;
use App\Http\Controllers\Backend\AssetcategoryController;
use App\Http\Controllers\Backend\AssetController;
use App\Http\Controllers\Backend\BankController;
use App\Http\Controllers\Backend\BankTransactionController;
use App\Http\Controllers\Backend\CurrencyController;
use App\Http\Controllers\Backend\DatabaseBackupController;
use App\Http\Controllers\Backend\DeliverycategoryController;
use App\Http\Controllers\Backend\DeliveryChargeController;
use App\Http\Controllers\Backend\DeliveryManController;
use App\Http\Controllers\Backend\DeliveryTypeController;
use App\Http\Controllers\Backend\DepartmentController;
use App\Http\Controllers\Backend\DesignationController;
use App\Http\Controllers\Backend\ExpenseController;
use App\Http\Controllers\Backend\FraudController;
use App\Http\Controllers\Backend\FuelController;
use App\Http\Controllers\Backend\FundTransferController;
use App\Http\Controllers\Backend\GeneralSettingsController;
use App\Http\Controllers\Backend\GoogleMapSettingsController;
use App\Http\Controllers\Backend\HubController;
use App\Http\Controllers\Backend\HubInChargeController;
use App\Http\Controllers\Backend\HubPanel\HubPaymentRequestController;
use App\Http\Controllers\Backend\HubPanel\ReceivedFromDeliverymanController;
use App\Http\Controllers\Backend\HubPaymentController;
use App\Http\Controllers\Backend\IncomeController;
use App\Http\Controllers\Backend\MailSettingController;
use App\Http\Controllers\Backend\MaintenanceController;
use App\Http\Controllers\Backend\MerchantController;
use App\Http\Controllers\Backend\MerchantDeliveryChargeController;
use App\Http\Controllers\Backend\MerchantInvoiceController;
use App\Http\Controllers\Backend\MerchantShopsController;
use App\Http\Controllers\Backend\MobileBankController;
use App\Http\Controllers\Backend\NewsOfferController;
use App\Http\Controllers\Backend\NotificationSettingsController;
use App\Http\Controllers\Backend\PackagingController;
use App\Http\Controllers\Backend\ParcelController;
use App\Http\Controllers\Backend\PickupRequestController;
use App\Http\Controllers\Backend\ProfileController;
use App\Http\Controllers\Backend\PushNotificationController;
use App\Http\Controllers\Backend\ReportsController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\SalaryController;
use App\Http\Controllers\Backend\SalaryGenerateController;
use App\Http\Controllers\Backend\SmsSendSettingsController;
use App\Http\Controllers\Backend\SmsSettingsController;
use App\Http\Controllers\Backend\SupportController;
use App\Http\Controllers\Backend\ThemeController;
use App\Http\Controllers\Backend\TodoController;
use App\Http\Controllers\Backend\TotalSummeryReportController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\VehicleController;
use App\Http\Controllers\DashbordController;
use App\Http\Controllers\MerchantmanagePaymentController;
use App\Http\Controllers\MerchantPaymentAccountController;
use Illuminate\Support\Facades\Route;

// ─── Dashboard ────────────────────────────────────
Route::get('/dashboard', [DashbordController::class, 'index'])->name('dashboard.index');
Route::post('search-charts', [DashbordController::class, 'searchCharts'])->name('search-charts');

// ─── Category ─────────────────────────────────────
Route::get('category/index', [App\Http\Controllers\CategoryController::class, 'index'])->name('category.index')->middleware('hasPermission:category_read');
Route::get('category/create', [App\Http\Controllers\CategoryController::class, 'create'])->name('category.create')->middleware('hasPermission:category_create');
Route::post('category/store', [App\Http\Controllers\CategoryController::class, 'store'])->name('category.store')->middleware('hasPermission:category_create');
Route::get('category/edit/{id}', [App\Http\Controllers\CategoryController::class, 'edit'])->name('category.edit')->middleware('hasPermission:category_update');
Route::put('category/update', [App\Http\Controllers\CategoryController::class, 'update'])->name('category.update')->middleware('hasPermission:category_update');
Route::delete('category/delete/{id}', [App\Http\Controllers\CategoryController::class, 'destroy'])->name('category.delete')->middleware('hasPermission:category_delete');

// ─── Admin Routes (prefix admin) ──────────────────
Route::group(['prefix' => 'admin'], function () {

    // Addons
    Route::resource('addons', AddonController::class);
    Route::post('/addons/activation', [AddonController::class, 'activation'])->name('addons.activation');

    // Logs
    Route::get('logs', [ActiveLogController::class, 'index'])->name('logs.index')->middleware('hasPermission:log_read');
    Route::get('log-activity-view/{id}', [ActiveLogController::class, 'view'])->name('log-activity-view');

    // Roles
    Route::get('roles', [RoleController::class, 'index'])->name('roles.index')->middleware('hasPermission:role_read');
    Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create')->middleware('hasPermission:role_create');
    Route::post('roles/store', [RoleController::class, 'store'])->name('roles.store')->middleware('hasPermission:role_create');
    Route::get('roles/edit/{id}', [RoleController::class, 'edit'])->name('roles.edit')->middleware('hasPermission:role_update');
    Route::put('roles/update', [RoleController::class, 'update'])->name('roles.update')->middleware('hasPermission:role_update');
    Route::delete('role/delete/{id}', [RoleController::class, 'destroy'])->name('role.delete')->middleware('hasPermission:role_delete');

    // Hubs
    Route::get('hubs', [HubController::class, 'index'])->name('hubs.index')->middleware('hasPermission:hub_read');
    Route::get('hubs/filter', [HubController::class, 'filter'])->name('hubs.filter')->middleware('hasPermission:hub_read');
    Route::get('hubs/create', [HubController::class, 'create'])->name('hubs.create')->middleware('hasPermission:hub_create');
    Route::post('hubs/store', [HubController::class, 'store'])->name('hubs.store')->middleware('hasPermission:hub_create');
    Route::get('hubs/edit/{id}', [HubController::class, 'edit'])->name('hubs.edit')->middleware('hasPermission:hub_update');
    Route::put('hubs/update', [HubController::class, 'update'])->name('hubs.update')->middleware('hasPermission:hub_update');
    Route::delete('hub/delete/{id}', [HubController::class, 'destroy'])->name('hub.delete')->middleware('hasPermission:hub_delete');
    Route::get('hub/view/{id}', [HubController::class, 'view'])->name('hub.view')->middleware('hasPermission:hub_view');
    Route::get('hub/map/show', [HubController::class, 'parcelHubs'])->name('parcel.parcel.hubs');

    // Hub Payment
    Route::get('request/hub/payment/index', [HubPaymentController::class, 'index'])->name('hub.hub-payment.index')->middleware('hasPermission:hub_payment_read');
    Route::get('request/hub/payment/create', [HubPaymentController::class, 'create'])->name('hub.hub-payment.create')->middleware('hasPermission:hub_payment_create');
    Route::post('request/hub/payment/store', [HubPaymentController::class, 'paymentStore'])->name('hub.hub-payment.store')->middleware('hasPermission:hub_payment_create');
    Route::get('request/hub/payment/edit/{id}', [HubPaymentController::class, 'edit'])->name('hub.hub-payment.edit')->middleware('hasPermission:hub_payment_update');
    Route::put('request/hub/payment/update/{id}', [HubPaymentController::class, 'update'])->name('hub.hub-payment.update')->middleware('hasPermission:hub_payment_update');
    Route::delete('request/hub/payment/delete/{id}', [HubPaymentController::class, 'destroy'])->name('hub.hub-payment.delete')->middleware('hasPermission:hub_payment_delete');
    Route::get('hub-payment/reject/{id}', [HubPaymentController::class, 'reject'])->name('hub-payment.reject')->middleware('hasPermission:hub_payment_reject');
    Route::get('hub-payment/cancel-reject/{id}', [HubPaymentController::class, 'cancelReject'])->name('hub-payment.cancel-reject')->middleware('hasPermission:hub_payment_reject');
    Route::get('hub-payment/process/{id}', [HubPaymentController::class, 'process'])->name('hub-payment.process')->middleware('hasPermission:hub_payment_process');
    Route::get('hub-payment/cancel-process/{id}', [HubPaymentController::class, 'cancelProcess'])->name('hub-payment.cancel-process')->middleware('hasPermission:hub_payment_process');
    Route::put('hub-payment/processed', [HubPaymentController::class, 'processed'])->name('hub-payment.processed')->middleware('hasPermission:hub_payment_process');

    // Hub Panel Payment Request
    Route::get('hub/payment-request/index', [HubPaymentRequestController::class, 'index'])->name('hub-panel.payment-request.index')->middleware('hasPermission:hub_payment_request_read');
    Route::get('hub/payment-request/create', [HubPaymentRequestController::class, 'create'])->name('hub-panel.payment-request.create')->middleware('hasPermission:hub_payment_request_create');
    Route::post('hub/payment-request/store', [HubPaymentRequestController::class, 'store'])->name('hub-panel.payment-request.store')->middleware('hasPermission:hub_payment_request_create');
    Route::get('hub/payment-request/edit/{id}', [HubPaymentRequestController::class, 'edit'])->name('hub-panel.payment-request.edit')->middleware('hasPermission:hub_payment_request_update');
    Route::put('hub/payment-request/update/{id}', [HubPaymentRequestController::class, 'update'])->name('hub-panel.payment-request.update')->middleware('hasPermission:hub_payment_request_update');
    Route::delete('hub/payment-request/delete/{id}', [HubPaymentRequestController::class, 'delete'])->name('hub-panel.payment-request.delete')->middleware('hasPermission:hub_payment_request_delete');

    // Hub In Charges
    Route::get('hub/incharge/{hubID}/index', [HubInChargeController::class, 'index'])->name('hub-incharge.index')->middleware('hasPermission:hub_incharge_read');
    Route::get('hub/incharge/{hubID}/create', [HubInChargeController::class, 'create'])->name('hub-incharge.create')->middleware('hasPermission:hub_incharge_create');
    Route::post('hub/incharge/{hubID}/store', [HubInChargeController::class, 'store'])->name('hub-incharge.store')->middleware('hasPermission:hub_incharge_create');
    Route::get('hub/incharge/{hubID}/edit/{id}', [HubInChargeController::class, 'edit'])->name('hub-incharge.edit')->middleware('hasPermission:hub_incharge_update');
    Route::put('hub/incharge/{hubID}/update/{id}', [HubInChargeController::class, 'update'])->name('hub-incharge.update')->middleware('hasPermission:hub_incharge_update');
    Route::delete('hub/incharge/{hubID}/delete/{id}', [HubInChargeController::class, 'destroy'])->name('hub-incharge.destroy')->middleware('hasPermission:hub_incharge_delete');
    Route::get('hub/incharge/{hubID}/assigned/{id}', [HubInChargeController::class, 'assigned'])->name('hub-incharge.assigned')->middleware('hasPermission:hub_incharge_assigned');

    // Users
    Route::get('users', [UserController::class, 'index'])->name('users.index')->middleware('hasPermission:user_read');
    Route::get('users/filter', [UserController::class, 'filter'])->name('users.filter')->middleware('hasPermission:user_read');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create')->middleware('hasPermission:user_create');
    Route::post('users/store', [UserController::class, 'store'])->name('users.store')->middleware('hasPermission:user_create');
    Route::get('users/edit/{id}', [UserController::class, 'edit'])->name('users.edit')->middleware('hasPermission:user_update');
    Route::put('users/update', [UserController::class, 'update'])->name('users.update')->middleware('hasPermission:user_update');
    Route::get('users/permissions/{id}', [UserController::class, 'permission'])->name('users.permission')->middleware('hasPermission:permission_update');
    Route::put('users/permissions/update', [UserController::class, 'permissionsUpdate'])->name('users.permissions.update')->middleware('hasPermission:permission_update');
    Route::delete('user/delete/{id}', [UserController::class, 'destroy'])->name('user.delete')->middleware('hasPermission:user_delete');

    // Income
    Route::get('income', [IncomeController::class, 'index'])->name('income.index')->middleware('hasPermission:income_read');
    Route::get('income/filter', [IncomeController::class, 'filter'])->name('income.filter')->middleware('hasPermission:income_read');
    Route::get('income/create', [IncomeController::class, 'create'])->name('income.create')->middleware('hasPermission:income_create');
    Route::post('income/search-account/{id}', [IncomeController::class, 'searchAccount'])->name('income.search-account');
    Route::post('income/store', [IncomeController::class, 'store'])->name('income.store')->middleware('hasPermission:income_create');
    Route::get('income/edit/{id}', [IncomeController::class, 'edit'])->name('income.edit')->middleware('hasPermission:income_update');
    Route::put('income/update/{id}', [IncomeController::class, 'update'])->name('income.update')->middleware('hasPermission:income_update');
    Route::delete('income/delete/{id}', [IncomeController::class, 'destroy'])->name('income.delete')->middleware('hasPermission:income_delete');
    Route::post('income/balance-check', [IncomeController::class, 'balanceCheck'])->name('income.balance.check');
    Route::post('income/hub-user-accounts', [IncomeController::class, 'hubUserAccounts'])->name('income.hub-user-accounts');
    Route::post('income/users', [IncomeController::class, 'IncomeUsers'])->name('income.users');

    // Expense
    Route::get('expense', [ExpenseController::class, 'index'])->name('expense.index')->middleware('hasPermission:expense_read');
    Route::get('expense/filter', [ExpenseController::class, 'filter'])->name('expense.filter')->middleware('hasPermission:expense_read');
    Route::get('expense/create', [ExpenseController::class, 'create'])->name('expense.create')->middleware('hasPermission:expense_create');
    Route::post('expense/search-account/{id}', [ExpenseController::class, 'searchAccount'])->name('expense.search-account');
    Route::post('expense/store', [ExpenseController::class, 'store'])->name('expense.store')->middleware('hasPermission:expense_create');
    Route::get('expense/edit/{id}', [ExpenseController::class, 'edit'])->name('expense.edit')->middleware('hasPermission:expense_update');
    Route::put('expense/update/{id}', [ExpenseController::class, 'update'])->name('expense.update')->middleware('hasPermission:expense_update');
    Route::delete('expense/delete/{id}', [ExpenseController::class, 'destroy'])->name('expense.delete')->middleware('hasPermission:expense_delete');
    Route::post('expense/users', [ExpenseController::class, 'ExpenseUsers'])->name('expense.users');

    // Salary
    Route::get('salarys', [SalaryController::class, 'index'])->name('salary.index')->middleware('hasPermission:salary_read');
    Route::get('salarys/filter', [SalaryController::class, 'salaryFilter'])->name('salary.filter')->middleware('hasPermission:salary_read');
    Route::get('salarys/create', [SalaryController::class, 'create'])->name('salary.create')->middleware('hasPermission:salary_create');
    Route::post('salary/users', [SalaryController::class, 'Users'])->name('salary.users');
    Route::post('salary/store', [SalaryController::class, 'store'])->name('salary.store')->middleware('hasPermission:salary_create');
    Route::get('salarys/edit/{id}', [SalaryController::class, 'edit'])->name('salary.edit')->middleware('hasPermission:salary_update');
    Route::put('salary/update', [SalaryController::class, 'update'])->name('salary.update')->middleware('hasPermission:salary_update');
    Route::delete('salary/delete/{id}', [SalaryController::class, 'delete'])->name('salary.delete')->middleware('hasPermission:salary_delete');
    Route::post('salary/search-account', [SalaryController::class, 'salaryGet'])->name('salary.account.search');
    Route::get('salary/pay-slip/{id}', [SalaryController::class, 'paySlip'])->name('salary.pay.slip')->middleware('hasPermission:salary_read');

    // Bank Transaction
    Route::get('bank-transaction', [BankTransactionController::class, 'index'])->name('bank-transaction.index')->middleware('hasPermission:bank_transaction_read');
    Route::post('bank-transaction/filter', [BankTransactionController::class, 'filter'])->name('bank-transaction.filter')->middleware('hasPermission:bank_transaction_read');
    Route::get('bank-transaction/specific/search', [BankTransactionController::class, 'bankTransactionSpecificSearch'])->name('bank.transaction.specific.search');
    Route::get('bank-transaction/filter/print', [BankTransactionController::class, 'bankTransactionPrint'])->name('bank.transaction.filter.print');

    // Cash Received from Deliveryman
    Route::get('hub/cash-received-deliveryman', [ReceivedFromDeliverymanController::class, 'index'])->name('cash.received.deliveryman.index')->middleware('hasPermission:cash_received_from_delivery_man_read');
    Route::get('hub/cash-received-deliveryman/create', [ReceivedFromDeliverymanController::class, 'create'])->name('cash.received.deliveryman.create')->middleware('hasPermission:cash_received_from_delivery_man_create');
    Route::post('hub/cash-received-deliveryman/store', [ReceivedFromDeliverymanController::class, 'store'])->name('cash.received.deliveryman.store')->middleware('hasPermission:cash_received_from_delivery_man_create');
    Route::get('hub/cash-received-deliveryman/edit/{id}', [ReceivedFromDeliverymanController::class, 'edit'])->name('cash.received.deliveryman.edit')->middleware('hasPermission:cash_received_from_delivery_man_update');
    Route::put('hub/cash-received-deliveryman/update', [ReceivedFromDeliverymanController::class, 'update'])->name('cash.received.deliveryman.update')->middleware('hasPermission:cash_received_from_delivery_man_update');
    Route::delete('hub/cash-received-deliveryman/delete/{id}', [ReceivedFromDeliverymanController::class, 'delete'])->name('cash.received.deliveryman.delete')->middleware('hasPermission:cash_received_from_delivery_man_delete');

    // Profile
    Route::get('profile/{id}', [ProfileController::class, 'view'])->name('profile.index');
    Route::get('profile/update/{id}', [ProfileController::class, 'create'])->name('profile.edit');
    Route::get('profile/change-password/{id}', [ProfileController::class, 'changePassword'])->name('password.change');
    Route::put('profile/update/{id}', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/update-password/{id}', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Merchant
    Route::get('merchant/index', [MerchantController::class, 'index'])->name('merchant.index')->middleware('hasPermission:merchant_read');
    Route::get('merchant/create', [MerchantController::class, 'create'])->name('merchant.create')->middleware('hasPermission:merchant_create');
    Route::post('merchant/store', [MerchantController::class, 'store'])->name('merchant.store')->middleware('hasPermission:merchant_create');
    Route::get('merchant/edit/{id}', [MerchantController::class, 'edit'])->name('merchant.edit')->middleware('hasPermission:merchant_update');
    Route::put('merchant/update/{id}', [MerchantController::class, 'update'])->name('merchant.update')->middleware('hasPermission:merchant_update');
    Route::delete('merchant/delete/{id}', [MerchantController::class, 'destroy'])->name('merchant.delete')->middleware('hasPermission:merchant_delete');
    Route::get('merchant/view/{id}', [MerchantController::class, 'view'])->name('merchant.view')->middleware('hasPermission:merchant_view');
    Route::get('merchant/invoice-generate/{id}', [MerchantController::class, 'invoiceGenerate'])->name('merchant.invoice.generate')->middleware('hasPermission:merchant_view');

    // Merchant Delivery Charge
    Route::post('merchant/delivery-charge/info', [MerchantDeliveryChargeController::class, 'deliveryChargeInfo'])->name('merchant.deliveryCharge.deliveryChargeInfo');
    Route::get('merchant/{merchant}/delivery-charge/index', [MerchantDeliveryChargeController::class, 'index'])->name('merchant.deliveryCharge.index')->middleware('hasPermission:merchant_delivery_charge_read');
    Route::get('merchant/{merchant}/delivery-charge/create', [MerchantDeliveryChargeController::class, 'create'])->name('merchant.deliveryCharge.create')->middleware('hasPermission:merchant_delivery_charge_create');
    Route::post('merchant/{merchant}/delivery-charge/store', [MerchantDeliveryChargeController::class, 'store'])->name('merchant.deliveryCharge.store')->middleware('hasPermission:merchant_delivery_charge_create');
    Route::get('merchant/{merchant}/delivery-charge/edit/{id}', [MerchantDeliveryChargeController::class, 'edit'])->name('merchant.deliveryCharge.edit')->middleware('hasPermission:merchant_delivery_charge_update');
    Route::put('merchant/{merchant}/delivery-charge/update/{id}', [MerchantDeliveryChargeController::class, 'update'])->name('merchant.deliveryCharge.update')->middleware('hasPermission:merchant_delivery_charge_update');
    Route::delete('merchant/{merchant}/delivery-charge/delete/{id}', [MerchantDeliveryChargeController::class, 'delete'])->name('merchant.deliveryCharge.delete')->middleware('hasPermission:merchant_delivery_charge_delete');

    // Merchant Shops
    Route::get('merchant/{id}/shops/index', [MerchantShopsController::class, 'index'])->name('merchant.shops.index')->middleware('hasPermission:merchant_shop_read');
    Route::get('merchant/shops/create/{id}', [MerchantShopsController::class, 'create'])->name('merchant.shops.create')->middleware('hasPermission:merchant_shop_create');
    Route::post('merchant/shops/store', [MerchantShopsController::class, 'store'])->name('merchant.shops.store')->middleware('hasPermission:merchant_shop_create');
    Route::get('merchant/shops/edit/{id}', [MerchantShopsController::class, 'edit'])->name('merchant.shops.edit')->middleware('hasPermission:merchant_shop_update');
    Route::put('merchant/shops/update', [MerchantShopsController::class, 'update'])->name('merchant.shops.update')->middleware('hasPermission:merchant_shop_update');
    Route::delete('merchant/shops/delete/{id}', [MerchantShopsController::class, 'delete'])->name('merchant.shops.delete')->middleware('hasPermission:merchant_shop_delete');
    Route::get('merchant/shops/default/{merchant_id}/{id}', [MerchantShopsController::class, 'defaultShop'])->name('merchant.shops.default');

    // Merchant Payment Account
    Route::get('merchant/{id}/payment/index', [MerchantPaymentAccountController::class, 'index'])->name('merchant.paymentaccount.index')->middleware('hasPermission:merchant_payment_read');
    Route::get('merchant/{id}/payment/add', [MerchantPaymentAccountController::class, 'paymentAdd'])->name('merchant.payment.add')->middleware('hasPermission:merchant_payment_create');
    Route::post('merchant/paymentmethod/change', [MerchantPaymentAccountController::class, 'paymentChange'])->name('merchant.paymentmethod.change');
    Route::post('merchant/paymentinfo/bank/store', [MerchantPaymentAccountController::class, 'bankStore'])->name('merchant.paymentinfo.bank.store')->middleware('hasPermission:merchant_payment_create');
    Route::post('merchant/paymentinfo/mobile/store', [MerchantPaymentAccountController::class, 'mobileStore'])->name('merchant.paymentinfo.mobile.store')->middleware('hasPermission:merchant_payment_create');
    Route::get('merchant/{mid}/payment/edit/{id}', [MerchantPaymentAccountController::class, 'paymentEdit'])->name('merchant.payment.edit')->middleware('hasPermission:merchant_payment_update');
    Route::put('merchant/paymentinfo/bank/update', [MerchantPaymentAccountController::class, 'bankUpdate'])->name('merchant.payment.bank.update')->middleware('hasPermission:merchant_payment_update');
    Route::put('merchant/paymentinfo/mobile/update', [MerchantPaymentAccountController::class, 'mobileUpdate'])->name('merchant.payment.mobile.update')->middleware('hasPermission:merchant_payment_update');
    Route::delete('merchant/paymentinfo/delete/{id}', [MerchantPaymentAccountController::class, 'destroy'])->name('merchant.payment.delete')->middleware('hasPermission:merchant_payment_delete');

    // Merchant Manage Payment
    Route::get('payment/index', [MerchantmanagePaymentController::class, 'index'])->name('merchant.manage.payment.index')->middleware('hasPermission:payment_read');
    Route::get('payment/create', [MerchantmanagePaymentController::class, 'create'])->name('merchant-manage.payment.create')->middleware('hasPermission:payment_create');
    Route::post('merchant/account', [MerchantmanagePaymentController::class, 'merchantAccount'])->name('merchant-manage.merchant.account');
    Route::post('merchant/invoices', [MerchantmanagePaymentController::class, 'merchantInvoices'])->name('merchant-manage.merchant.invoices');
    Route::post('merchant/invoices/totalamount', [MerchantmanagePaymentController::class, 'merchantInvoicesTotalamount'])->name('merchant-manage.merchant.invoices.totalamount');
    Route::post('merchant/search', [MerchantmanagePaymentController::class, 'merchantSearch'])->name('merchant-manage.merchant-search');
    Route::post('payment/store', [MerchantmanagePaymentController::class, 'paymentStore'])->name('merchantmanage.payment.store')->middleware('hasPermission:payment_create');
    Route::get('payment/edit/{id}', [MerchantmanagePaymentController::class, 'edit'])->name('merchatmanage.payment.edit')->middleware('hasPermission:payment_update');
    Route::put('payment/update', [MerchantmanagePaymentController::class, 'update'])->name('merchantmanage.payment.update')->middleware('hasPermission:payment_update');
    Route::delete('payment/delete/{id}', [MerchantmanagePaymentController::class, 'destroy'])->name('merchantmanage.payment.delete')->middleware('hasPermission:payment_delete');
    Route::get('payment/reject/{id}', [MerchantmanagePaymentController::class, 'reject'])->name('merchantmanage.payment.reject')->middleware('hasPermission:payment_reject');
    Route::get('payment/cancel-reject/{id}', [MerchantmanagePaymentController::class, 'cancelReject'])->name('merchantmanage.payment.cancel-reject')->middleware('hasPermission:payment_reject');
    Route::get('payment/process/{id}', [MerchantmanagePaymentController::class, 'process'])->name('merchantmanage.payment.process')->middleware('hasPermission:payment_process');
    Route::get('payment/cancel-process/{id}', [MerchantmanagePaymentController::class, 'cancelProcess'])->name('merchantmanage.payment.cancel-process')->middleware('hasPermission:payment_process');
    Route::put('payment/processed', [MerchantmanagePaymentController::class, 'processed'])->name('merchantmanage.payment.processed')->middleware('hasPermission:payment_process');
    Route::get('payment/merchant/filter', [MerchantmanagePaymentController::class, 'merchantpaymentFilter'])->name('merchantmanage.payment.filter');

    // Merchant Invoice
    Route::prefix('merchant/{merchant_id}/invoice')->name('merchant.invoice.')->group(function () {
        Route::get('/', [MerchantInvoiceController::class, 'index'])->name('index')->middleware('hasPermission:invoice_read');
        Route::get('/{invoice_id}', [MerchantInvoiceController::class, 'InvoiceDetails'])->name('details')->middleware('hasPermission:invoice_read');
        Route::get('/status/update', [MerchantInvoiceController::class, 'StatusUpdate'])->name('status.update')->middleware('hasPermission:invoice_status_update');
        Route::get('/pdf/{invoice_id}', [MerchantInvoiceController::class, 'InvoicePdf'])->name('pdf')->middleware('hasPermission:invoice_read');
        Route::get('/csv/{invoice_id}', [MerchantInvoiceController::class, 'InvoiceCSV'])->name('csv')->middleware('hasPermission:invoice_read');
    });
    Route::get('paid/invoice', [MerchantInvoiceController::class, 'PaidInvoice'])->name('paid.invoice.index');

    // Liquid Fragile
    Route::get('liquid-fragile/index', [App\Http\Controllers\Backend\LiquidFragileController::class, 'index'])->name('liquid-fragile.index')->middleware('hasPermission:liquid_fragile_read');
    Route::get('liquid-fragile/edit', [App\Http\Controllers\Backend\LiquidFragileController::class, 'edit'])->name('liquid.fragile.edit')->middleware('hasPermission:liquid_fragile_update');
    Route::put('liquid-fragile/update', [App\Http\Controllers\Backend\LiquidFragileController::class, 'update'])->name('liquid.fragile.update')->middleware('hasPermission:liquid_fragile_update');
    Route::post('liquid-fragile/status', [App\Http\Controllers\Backend\LiquidFragileController::class, 'status'])->name('liquid-fragile.status')->middleware('hasPermission:liquid_status_change');

    // ─── Parcel Routes (complet) ──────────────
    require __DIR__.'/admin/parcel.php';

    // Deliveryman
    Route::get('deliveryman', [DeliveryManController::class, 'index'])->name('deliveryman.index')->middleware('hasPermission:delivery_man_read');
    Route::get('deliveryman/filter', [DeliveryManController::class, 'filter'])->name('deliveryman.filter')->middleware('hasPermission:delivery_man_read');
    Route::get('deliveryman/create', [DeliveryManController::class, 'create'])->name('deliveryman.create')->middleware('hasPermission:delivery_man_create');
    Route::post('deliveryman/store', [DeliveryManController::class, 'store'])->name('deliveryman.store')->middleware('hasPermission:delivery_man_create');
    Route::get('deliveryman/edit/{id}', [DeliveryManController::class, 'edit'])->name('deliveryman.edit')->middleware('hasPermission:delivery_man_update');
    Route::put('deliveryman/update', [DeliveryManController::class, 'update'])->name('deliveryman.update')->middleware('hasPermission:delivery_man_update');
    Route::delete('deliveryman/delete/{id}', [DeliveryManController::class, 'destroy'])->name('deliveryman.delete')->middleware('hasPermission:delivery_man_delete');

    // Delivery Category
    Route::get('delivery-category/index', [DeliverycategoryController::class, 'index'])->name('delivery-category.index')->middleware('hasPermission:delivery_category_read');
    Route::get('delivery-category/create', [DeliverycategoryController::class, 'create'])->name('delivery-category.create')->middleware('hasPermission:delivery_category_create');
    Route::post('delivery-category/store', [DeliverycategoryController::class, 'store'])->name('delivery-category.store')->middleware('hasPermission:delivery_category_create');
    Route::get('delivery-category/edit/{id}', [DeliverycategoryController::class, 'edit'])->name('delivery-category.edit')->middleware('hasPermission:delivery_category_update');
    Route::get('delivery-category/view/{id}', [DeliverycategoryController::class, 'view'])->name('delivery-category.view');
    Route::put('delivery-category/update', [DeliverycategoryController::class, 'update'])->name('delivery-category.update')->middleware('hasPermission:delivery_category_update');
    Route::delete('delivery-category/delete/{id}', [DeliverycategoryController::class, 'destroy'])->name('delivery-category.delete')->middleware('hasPermission:delivery_category_delete');

    // Delivery Charges
    Route::get('delivery-charge/index', [DeliveryChargeController::class, 'index'])->name('delivery-charge.index')->middleware('hasPermission:delivery_charge_read');
    Route::get('delivery-charge/filter', [DeliveryChargeController::class, 'filter'])->name('delivery-charge.filter')->middleware('hasPermission:delivery_charge_read');
    Route::get('delivery-charge/create', [DeliveryChargeController::class, 'create'])->name('delivery-charge.create')->middleware('hasPermission:delivery_charge_create');
    Route::post('delivery-charge/store', [DeliveryChargeController::class, 'store'])->name('delivery-charge.store')->middleware('hasPermission:delivery_charge_create');
    Route::get('delivery-charge/edit/{id}', [DeliveryChargeController::class, 'edit'])->name('delivery-charge.edit')->middleware('hasPermission:delivery_charge_update');
    Route::get('delivery-charge/view/{id}', [DeliveryChargeController::class, 'view'])->name('delivery-charge.view');
    Route::put('delivery-charge/update', [DeliveryChargeController::class, 'update'])->name('delivery-charge.update')->middleware('hasPermission:delivery_charge_update');
    Route::delete('delivery-charge/delete/{id}', [DeliveryChargeController::class, 'destroy'])->name('delivery-charge.delete')->middleware('hasPermission:delivery_charge_delete');

    // Delivery Type
    Route::get('delivery-type/index', [DeliveryTypeController::class, 'index'])->name('delivery-type.index')->middleware('hasPermission:delivery_type_read');
    Route::post('delivery-type/status', [DeliveryTypeController::class, 'status'])->name('delivery-type.status')->middleware('hasPermission:delivery_type_status_change');

    // Packaging
    Route::get('packaging/index', [PackagingController::class, 'index'])->name('packaging.index')->middleware('hasPermission:packaging_read');
    Route::get('packaging/create', [PackagingController::class, 'create'])->name('packaging.create')->middleware('hasPermission:packaging_create');
    Route::post('packaging/store', [PackagingController::class, 'store'])->name('packaging.store')->middleware('hasPermission:packaging_create');
    Route::get('packaging/edit/{id}', [PackagingController::class, 'edit'])->name('packaging.edit')->middleware('hasPermission:packaging_update');
    Route::get('packaging/view/{id}', [PackagingController::class, 'view']);
    Route::put('packaging/update', [PackagingController::class, 'update'])->name('packaging.update')->middleware('hasPermission:packaging_update');
    Route::delete('packaging/delete/{id}', [PackagingController::class, 'destroy'])->name('packaging.delete')->middleware('hasPermission:packaging_delete');

    // Accounts
    Route::get('accounts/index', [AccountController::class, 'index'])->name('accounts.index')->middleware('hasPermission:account_read');
    Route::get('accounts/filter', [AccountController::class, 'filter'])->name('accounts.filter')->middleware('hasPermission:account_read');
    Route::get('accounts/create', [AccountController::class, 'create'])->name('accounts.create')->middleware('hasPermission:account_create');
    Route::post('accounts/store', [AccountController::class, 'store'])->name('accounts.store')->middleware('hasPermission:account_create');
    Route::get('accounts/edit/{id}', [AccountController::class, 'edit'])->name('accounts.edit')->middleware('hasPermission:account_update');
    Route::get('accounts/view/{id}', [AccountController::class, 'view'])->name('accounts.view');
    Route::put('accounts/update/{id}', [AccountController::class, 'update'])->name('accounts.update')->middleware('hasPermission:account_update');
    Route::delete('accounts/delete/{id}', [AccountController::class, 'destroy'])->name('accounts.delete')->middleware('hasPermission:account_delete');
    Route::post('accounts/current-balance', [AccountController::class, 'currentBalance'])->name('accounts.current-balance');

    // Fund Transfer
    Route::get('fund-transfer/index', [FundTransferController::class, 'index'])->name('fund-transfer.index')->middleware('hasPermission:fund_transfer_read');
    Route::get('fund-transfer/create', [FundTransferController::class, 'create'])->name('fund-transfer.create')->middleware('hasPermission:fund_transfer_create');
    Route::post('fund-transfer/store', [FundTransferController::class, 'store'])->name('fund-transfer.store')->middleware('hasPermission:fund_transfer_create');
    Route::get('fund-transfer/edit/{id}', [FundTransferController::class, 'edit'])->name('fund-transfer.edit')->middleware('hasPermission:fund_transfer_update');
    Route::get('fund-transfer/view/{id}', [FundTransferController::class, 'view'])->name('fund-transfer.view');
    Route::put('fund-transfer/update/{id}', [FundTransferController::class, 'update'])->name('fund-transfer.update')->middleware('hasPermission:fund_transfer_update');
    Route::delete('fund-transfer/delete/{id}', [FundTransferController::class, 'destroy'])->name('fund-transfer.delete')->middleware('hasPermission:fund_transfer_delete');
    Route::get('fund-transfer/specific/search', [FundTransferController::class, 'fundTransferSpecificSearch'])->name('fund.transfer.specific.search')->middleware('hasPermission:fund_transfer_read');
    Route::get('fund-transfer/search/flter/print', [FundTransferController::class, 'fundTransferSearchFilterPrint'])->name('fund.transfer.search.filter.print')->middleware('hasPermission:fund_transfer_read');
    Route::get('fund-transfer/filter', [FundTransferController::class, 'fundTransferFilter'])->name('fund.transfer.filter')->middleware('hasPermission:fund_transfer_read');

    // Designation
    Route::get('designations', [DesignationController::class, 'index'])->name('designations.index')->middleware('hasPermission:designation_read');
    Route::get('designations/create', [DesignationController::class, 'create'])->name('designations.create')->middleware('hasPermission:designation_create');
    Route::post('designations/store', [DesignationController::class, 'store'])->name('designations.store')->middleware('hasPermission:designation_create');
    Route::get('designations/edit/{id}', [DesignationController::class, 'edit'])->name('designations.edit')->middleware('hasPermission:designation_update');
    Route::put('designations/update', [DesignationController::class, 'update'])->name('designations.update')->middleware('hasPermission:designation_update');
    Route::delete('designation/delete/{id}', [DesignationController::class, 'destroy'])->name('designation.delete')->middleware('hasPermission:designation_delete');

    // Department
    Route::get('departments', [DepartmentController::class, 'index'])->name('departments.index')->middleware('hasPermission:department_read');
    Route::get('departments/create', [DepartmentController::class, 'create'])->name('departments.create')->middleware('hasPermission:department_create');
    Route::post('departments/store', [DepartmentController::class, 'store'])->name('departments.store')->middleware('hasPermission:department_create');
    Route::get('departments/edit/{id}', [DepartmentController::class, 'edit'])->name('departments.edit')->middleware('hasPermission:department_update');
    Route::put('departments/update', [DepartmentController::class, 'update'])->name('departments.update')->middleware('hasPermission:department_update');
    Route::delete('department/delete/{id}', [DepartmentController::class, 'destroy'])->name('department.delete')->middleware('hasPermission:department_delete');

    // Fraud
    Route::get('fraud', [FraudController::class, 'index'])->name('fraud.index')->middleware('hasPermission:fraud_read');
    Route::get('fraud/create', [FraudController::class, 'create'])->name('fraud.create')->middleware('hasPermission:fraud_create');
    Route::post('fraud/store', [FraudController::class, 'store'])->name('fraud.store')->middleware('hasPermission:fraud_create');
    Route::get('fraud/edit/{id}', [FraudController::class, 'edit'])->name('fraud.edit')->middleware('hasPermission:fraud_update');
    Route::put('fraud/update', [FraudController::class, 'update'])->name('fraud.update')->middleware('hasPermission:fraud_update');
    Route::delete('fraud/delete/{id}', [FraudController::class, 'destroy'])->name('fraud.delete')->middleware('hasPermission:fraud_delete');

    // Todo
    Route::get('todo/todo_list', [TodoController::class, 'index'])->name('todo.index')->middleware('hasPermission:todo_read');
    Route::post('todo/todo_add', [TodoController::class, 'store'])->name('todo.store')->middleware('hasPermission:todo_create');
    Route::post('todo/momal', [TodoController::class, 'todoModal'])->name('todo.modal');
    Route::post('todo/processing', [TodoController::class, 'todoProcessing'])->name('todo.processing')->middleware('hasPermission:todo_update');
    Route::post('todo/completed', [TodoController::class, 'todoComplete'])->name('todo.completed')->middleware('hasPermission:todo_update');
    Route::put('todo/update', [TodoController::class, 'update'])->name('todo.update')->middleware('hasPermission:todo_update');
    Route::delete('todo/delete/{id}', [TodoController::class, 'destroy'])->name('todo.delete')->middleware('hasPermission:todo_delete');

    // Support
    Route::get('support/index', [SupportController::class, 'index'])->name('support.index')->middleware('hasPermission:support_read');
    Route::get('support/create', [SupportController::class, 'create'])->name('support.add')->middleware('hasPermission:support_create');
    Route::post('support/store', [SupportController::class, 'store'])->name('support.store')->middleware('hasPermission:support_create');
    Route::get('support/edit/{id}', [SupportController::class, 'edit'])->name('support.edit')->middleware('hasPermission:support_update');
    Route::put('support/update', [SupportController::class, 'update'])->name('support.update')->middleware('hasPermission:support_update');
    Route::delete('support/delete/{id}', [SupportController::class, 'destroy'])->name('support.delete')->middleware('hasPermission:support_delete');
    Route::get('support/view/{id}', [SupportController::class, 'view'])->name('support.view');
    Route::post('support/reply', [SupportController::class, 'supportReply'])->name('support.reply')->middleware('hasPermission:support_reply');
    Route::get('support/status-update/{id}', [SupportController::class, 'statusUpdate'])->name('support.status.update')->middleware('hasPermission:support_status_update');

    // Account Heads
    Route::get('/account-heads', [AccountHeadsController::class, 'index'])->name('account.heads.index')->middleware('hasPermission:account_heads_read');

    // SMS Settings
    Route::get('sms-settings/index', [SmsSettingsController::class, 'index'])->name('sms-settings.index')->middleware('hasPermission:sms_settings_read');
    Route::get('sms-settings/create', [SmsSettingsController::class, 'create'])->name('sms-settings.create')->middleware('hasPermission:sms_settings_create');
    Route::post('sms-settings/store', [SmsSettingsController::class, 'store'])->name('sms-settings.store')->middleware('hasPermission:sms_settings_create');
    Route::get('sms-settings/edit/{id}', [SmsSettingsController::class, 'edit'])->name('sms-settings.edit')->middleware('hasPermission:sms_settings_update');
    Route::put('sms-settings/update/{id}', [SmsSettingsController::class, 'update'])->name('sms-settings.update')->middleware('hasPermission:sms_settings_update');
    Route::delete('sms-settings/delete/{id}', [SmsSettingsController::class, 'delete'])->name('sms-settings.delete')->middleware('hasPermission:sms_settings_delete');
    Route::post('sms-settings/status', [SmsSettingsController::class, 'status'])->name('sms-settings.status')->middleware('hasPermission:sms_settings_status_change');

    Route::get('sms-send-settings/index', [SmsSendSettingsController::class, 'index'])->name('sms-send-settings.index')->middleware('hasPermission:sms_send_settings_read');
    Route::post('sms-send-settings/status', [SmsSendSettingsController::class, 'status'])->name('sms-send-settings.status')->middleware('hasPermission:sms_send_settings_status_change');

    // General Settings
    Route::get('general-settings/index', [GeneralSettingsController::class, 'index'])->name('general-settings.index')->middleware('hasPermission:general_settings_read');
    Route::put('general-settings/update', [GeneralSettingsController::class, 'update'])->name('general-settings.update')->middleware('hasPermission:general_settings_update');

    // Currency
    Route::get('currency', [CurrencyController::class, 'index'])->name('currency.index')->middleware('hasPermission:currency_read');
    Route::get('currency/create', [CurrencyController::class, 'create'])->name('currency.create')->middleware('hasPermission:currency_create');
    Route::post('currency/store', [CurrencyController::class, 'store'])->name('currency.store')->middleware('hasPermission:currency_create');
    Route::get('currency/edit/{id}', [CurrencyController::class, 'edit'])->name('currency.edit')->middleware('hasPermission:currency_update');
    Route::put('currency/update', [CurrencyController::class, 'update'])->name('currency.update')->middleware('hasPermission:currency_update');
    Route::delete('currency/delete/{id}', [CurrencyController::class, 'delete'])->name('currency.delete')->middleware('hasPermission:currency_delete');

    // Asset Category
    Route::get('asset-category/index', [AssetcategoryController::class, 'index'])->name('asset-category.index')->middleware('hasPermission:asset_category_read');
    Route::get('asset-category/create', [AssetcategoryController::class, 'create'])->name('asset-category.create')->middleware('hasPermission:asset_category_create');
    Route::post('asset-category/store', [AssetcategoryController::class, 'store'])->name('asset-category.store')->middleware('hasPermission:asset_category_create');
    Route::get('asset-category/edit/{id}', [AssetcategoryController::class, 'edit'])->name('asset-category.edit')->middleware('hasPermission:asset_category_update');
    Route::get('asset-category/view/{id}', [AssetcategoryController::class, 'view'])->name('asset-category.view')->middleware('hasPermission:asset_category_read');
    Route::put('asset-category/update', [AssetcategoryController::class, 'update'])->name('asset-category.update')->middleware('hasPermission:asset_category_update');
    Route::delete('asset-category/delete/{id}', [AssetcategoryController::class, 'destroy'])->name('asset-category.delete')->middleware('hasPermission:asset_category_delete');

    // News & Offer
    Route::get('news-offer', [NewsOfferController::class, 'index'])->name('news-offer.index')->middleware('hasPermission:news_offer_read');
    Route::get('news-offer/create', [NewsOfferController::class, 'create'])->name('news-offer.create')->middleware('hasPermission:news_offer_create');
    Route::post('news-offer/store', [NewsOfferController::class, 'store'])->name('news-offer.store')->middleware('hasPermission:news_offer_create');
    Route::get('news-offer/edit/{id}', [NewsOfferController::class, 'edit'])->name('news-offer.edit')->middleware('hasPermission:news_offer_update');
    Route::put('news-offer/update/{id}', [NewsOfferController::class, 'update'])->name('news-offer.update')->middleware('hasPermission:news_offer_update');
    Route::delete('news-offer/delete/{id}', [NewsOfferController::class, 'destroy'])->name('news-offer.delete')->middleware('hasPermission:news_offer_delete');

    // Asset
    Route::get('assets/index', [AssetController::class, 'index'])->name('asset.index')->middleware('hasPermission:assets_read');
    Route::get('assets/create', [AssetController::class, 'create'])->name('asset.create')->middleware('hasPermission:assets_create');
    Route::post('assets/store', [AssetController::class, 'store'])->name('asset.store')->middleware('hasPermission:assets_create');
    Route::get('assets/edit/{id}', [AssetController::class, 'edit'])->name('asset.edit')->middleware('hasPermission:assets_update');
    Route::get('assets/view/{id}', [AssetController::class, 'view'])->name('asset.view')->middleware('hasPermission:assets_read');
    Route::put('assets/update', [AssetController::class, 'update'])->name('asset.update')->middleware('hasPermission:assets_update');
    Route::delete('assets/delete/{id}', [AssetController::class, 'destroy'])->name('asset.delete')->middleware('hasPermission:assets_delete');
    Route::get('assets/assign/driver/{id}', [AssetController::class, 'assignDriver'])->name('asset.assign.driver')->middleware('hasPermission:assets_read');
    Route::post('assets/assign/driver/store', [AssetController::class, 'assignedDriverStore'])->name('asset.assign.driver.store')->middleware('hasPermission:assets_create');
    Route::delete('assets/assign/driver/delete/{id}', [AssetController::class, 'assignedDriverDelete'])->name('asset.assign.driver.delete')->middleware('hasPermission:assets_delete');
    Route::get('assets/assign/driver/edit/{id}', [AssetController::class, 'assignDriverEdit'])->name('asset.assign.edit')->middleware('hasPermission:assets_read');
    Route::put('assets/assign/driver/update', [AssetController::class, 'assignedDriverUpdate'])->name('asset.assign.driver.update')->middleware('hasPermission:assets_create');

    // Reports
    Route::get('reports/parcel-total-summery', [TotalSummeryReportController::class, 'parcelTotalSummery'])->name('parcel.total.summery.index')->middleware('hasPermission:parcel_total_summery');
    Route::get('reports/parcel-filter-total-summery', [TotalSummeryReportController::class, 'parcelTotalSummeryFilter'])->name('parcel.filter.total.summery')->middleware('hasPermission:parcel_total_summery');
    Route::get('reports/parcel-reports', [ReportsController::class, 'parcelReports'])->name('parcel.reports')->middleware('hasPermission:parcel_status_reports');
    Route::get('reports/parcel-filter-reports', [ReportsController::class, 'parcelSReports'])->name('parcel.filter.reports')->middleware('hasPermission:parcel_status_reports');
    Route::get('parcel-reports-print-page/{array}', [ReportsController::class, 'parcelReportsPrint'])->name('parcel.reports.print.page')->middleware('hasPermission:parcel_status_reports');
    Route::get('reports/parcel-wise-reports', [ReportsController::class, 'parcelWiseReports'])->name('parcel.wise.profit.index')->middleware('hasPermission:parcel_wise_profit');
    Route::post('reports-tracking-parcels', [ReportsController::class, 'reportsTrackingParcels'])->name('reports-tracking-parcels')->middleware('hasPermission:parcel_wise_profit');
    Route::get('reports/parcel-wise-profit-reports', [ReportsController::class, 'ParcelWiseProfitReports'])->name('parcel.wise.profit.reports')->middleware('hasPermission:parcel_wise_profit');
    Route::get('parcel-wise-profit-print-page/{array}', [ReportsController::class, 'parcelWiseProfitPrint'])->name('parcel.wise.profit.print.page')->middleware('hasPermission:parcel_wise_profit');
    Route::get('reports/salary-reports', [ReportsController::class, 'salaryReports'])->name('salary.reports')->middleware('hasPermission:salary_reports');
    Route::get('reports/reports-salary-reports', [ReportsController::class, 'ReportssalaryReports'])->name('reports.salary.reports')->middleware('hasPermission:salary_reports');
    Route::get('reports/salary-report-print', [ReportsController::class, 'SalaryReportPrint'])->name('salary.reports.print.page')->middleware('hasPermission:salary_reports');
    Route::get('reports/merchant-hub-deliveryman', [ReportsController::class, 'MerchantHubDeliverymanReports'])->name('merchant.hub.deliveryman.reports')->middleware('hasPermission:merchant_hub_deliveryman');
    Route::get('reports/mhd-reports', [ReportsController::class, 'MHDreports'])->name('reports.mhd.reports')->middleware('hasPermission:merchant_hub_deliveryman');
    Route::get('reports/merchnat-hub-delivery-reports-print-page', [ReportsController::class, 'MerchantHubDeliveryReportsPrintPage'])->name('merchant.hub.deliveryman.reports.print-page')->middleware('hasPermission:merchant_hub_deliveryman');
    Route::get('reports/mhd-pdf', [ReportsController::class, 'mhdPDF'])->name('merchant.hub.deliveryman.pdf');

    // Database Backup
    Route::get('/database-backup', [DatabaseBackupController::class, 'index'])->name('database.backup.index')->middleware('hasPermission:database_backup_read');
    Route::get('database-backup/download', [DatabaseBackupController::class, 'databaseBackup'])->name('database.backup.download')->middleware('hasPermission:database_backup_read');

    // Invoice Generate
    Route::get('settings/invoice-generate-menually/index', [MerchantInvoiceController::class, 'InvoiceGenerateMenuallyIndex'])->name('invoice.generate.menually.index')->middleware('hasPermission:invoice_generate_menually');
    Route::get('settings/invoice-generate-menually', [MerchantInvoiceController::class, 'InvoiceGenerateMenually'])->name('invoice.generate.menually')->middleware('hasPermission:invoice_generate_menually');

    // Salary Generate
    Route::get('salary/salary-generate', [SalaryGenerateController::class, 'index'])->name('salary.generate.index')->middleware('hasPermission:salary_generate_read');
    Route::post('salary/salary-auto-generate', [SalaryGenerateController::class, 'salaryAutoGenerate'])->name('salary.auto.generate')->middleware('hasPermission:salary_generate_create');
    Route::get('salary/salary-generate/create', [SalaryGenerateController::class, 'create'])->name('salary.generate.create')->middleware('hasPermission:salary_generate_create');
    Route::post('salary/salary-generate/store', [SalaryGenerateController::class, 'store'])->name('salary.generate.store')->middleware('hasPermission:salary_generate_create');
    Route::get('salary/salary-generate/edit/{id}', [SalaryGenerateController::class, 'edit'])->name('salary.generate.edit')->middleware('hasPermission:salary_generate_update');
    Route::put('salary/salary-generate/update', [SalaryGenerateController::class, 'update'])->name('salary.generate.update')->middleware('hasPermission:salary_generate_update');
    Route::delete('salary/salary-generate/delete/{id}', [SalaryGenerateController::class, 'salaryGenerateDelete'])->name('salary-generate.delete')->middleware('hasPermission:salary_generate_delete');
    Route::get('subscribe', [SalaryGenerateController::class, 'subscribe'])->name('subscribe.index');

    // Pickup Request
    Route::prefix('pickup-request')->name('pickup.request.')->group(function () {
        Route::get('regular', [PickupRequestController::class, 'regular'])->name('regular')->middleware('hasPermission:pickup_request_regular');
        Route::get('express', [PickupRequestController::class, 'express'])->name('express')->middleware('hasPermission:pickup_request_express');
    });

    // Parcel Search
    Route::get('parcel/specific/search', [ParcelController::class, 'ParcelSearchs'])->name('parcel.specific.search');

    // GoogleMap Settings
    Route::get('googlemap-settings/index', [GoogleMapSettingsController::class, 'index'])->name('googlemap-settings.index');
    Route::put('googlemap-settings/update', [GoogleMapSettingsController::class, 'update'])->name('googlemap-settings.update');

    // Mail Settings
    Route::get('mail-settings/index', [MailSettingController::class, 'index'])->name('mail-settings.index');
    Route::put('mail-settings/update', [MailSettingController::class, 'update'])->name('mail-settings.update');
    Route::get('mail-settings/test-mail', [MailSettingController::class, 'sendTestMail'])->name('mail-settings.test-mail');

    // Notification Settings
    Route::get('notification-settings/index', [NotificationSettingsController::class, 'index'])->name('notification-settings.index')->middleware('hasPermission:notification_settings_read');
    Route::put('notification-settings/update', [NotificationSettingsController::class, 'update'])->name('notification-settings.update')->middleware('hasPermission:notification_settings_update');

    // Push Notification
    Route::get('push-notification', [PushNotificationController::class, 'index'])->name('push-notification.index')->middleware('hasPermission:push_notification_read');
    Route::get('push-notification/create', [PushNotificationController::class, 'create'])->name('push-notification.create')->middleware('hasPermission:push_notification_create');
    Route::post('push-notification/store', [PushNotificationController::class, 'store'])->name('push-notification.store')->middleware('hasPermission:push_notification_create');
    Route::delete('push-notification/delete/{id}', [PushNotificationController::class, 'destroy'])->name('push-notification.delete')->middleware('hasPermission:push_notification_delete');
    Route::post('push-notification/users', [PushNotificationController::class, 'Users'])->name('push-notification.users');

    // Social Login Settings
    Route::get('social-login-settings', [App\Http\Controllers\Backend\SocialLoginController::class, 'socialLoginSettingsIndex'])->name('social.login.settings.index')->middleware('hasPermission:social_login_settings_read');
    Route::put('social-login-settings/update/{social}', [App\Http\Controllers\Backend\SocialLoginController::class, 'socialLoginSettingsUpdate'])->name('social.login.settings.update')->middleware('hasPermission:social_login_settings_update');

    // Payout
    require __DIR__.'/admin/payout.php';

    // Wallet Request
    Route::prefix('wallet-request')
        ->controller(\App\Http\Controllers\Backend\MerchantPanel\WalletController::class)
        ->name('wallet.request.')
        ->group(function () {
            Route::get('/', 'requestIndex')->name('index')->middleware('hasPermission:wallet_request_read');
            Route::post('/recharge', 'adminstore')->name('recharge')->middleware('hasPermission:wallet_request_create');
            Route::delete('/delete/{id}', 'delete')->name('delete')->middleware('hasPermission:wallet_request_delete');
            Route::put('/approve/{id}', 'approve')->name('approve')->middleware('hasPermission:wallet_request_approve');
            Route::put('/reject/{id}', 'reject')->name('reject')->middleware('hasPermission:wallet_request_reject');
        });

    // ─── Front Web ────────────────────────────
    require __DIR__.'/admin/frontweb.php';

    // Bank
    Route::resource('bank', BankController::class)->except(['show']);
    Route::get('bank/filter', [BankController::class, 'filter'])->name('bank.filter');

    // Mobile Bank
    Route::resource('mobile-bank', MobileBankController::class)->except(['show']);
    Route::get('mobile-bank/filter', [MobileBankController::class, 'filter'])->name('mobile-bank.filter');

    // Theme
    Route::get('theme', [ThemeController::class, 'index'])->name('theme.index');
    Route::post('theme/activate', [ThemeController::class, 'activate'])->name('theme.activate');

    // Vehicles
    Route::prefix('assets/vehicles')->name('vehicles.')->controller(VehicleController::class)->group(function () {
        Route::get('/', 'index')->name('index')->middleware('hasPermission:vehicles_read');
        Route::get('create', 'create')->name('create')->middleware('hasPermission:vehicles_create');
        Route::post('store', 'store')->name('store')->middleware('hasPermission:vehicles_create');
        Route::get('edit/{id}', 'edit')->name('edit')->middleware('hasPermission:vehicles_update');
        Route::put('update', 'update')->name('update')->middleware('hasPermission:vehicles_update');
        Route::delete('delete/{id}', 'delete')->name('delete')->middleware('hasPermission:vehicles_delete');
        Route::get('view/{id}', 'view')->name('view')->middleware('hasPermission:vehicles_read');
    });

    // Fuels
    Route::prefix('assets/fuels')->name('fuels.')->controller(FuelController::class)->group(function () {
        Route::get('/', 'index')->name('index')->middleware('hasPermission:fuels_read');
        Route::get('create', 'create')->name('create')->middleware('hasPermission:fuels_create');
        Route::post('store', 'store')->name('store')->middleware('hasPermission:fuels_create');
        Route::get('edit/{id}', 'edit')->name('edit')->middleware('hasPermission:fuels_update');
        Route::put('update', 'update')->name('update')->middleware('hasPermission:fuels_update');
        Route::delete('delete/{id}', 'delete')->name('delete')->middleware('hasPermission:fuels_delete');
    });

    // Maintenance
    Route::prefix('assets/maintenance')->name('maintenance.')->controller(MaintenanceController::class)->group(function () {
        Route::get('/', 'index')->name('index')->middleware('hasPermission:maintenance_read');
        Route::get('create', 'create')->name('create')->middleware('hasPermission:maintenance_create');
        Route::post('store', 'store')->name('store')->middleware('hasPermission:maintenance_create');
        Route::get('edit/{id}', 'edit')->name('edit')->middleware('hasPermission:maintenance_update');
        Route::put('update', 'update')->name('update')->middleware('hasPermission:maintenance_update');
        Route::delete('delete/{id}', 'delete')->name('delete')->middleware('hasPermission:maintenance_delete');
    });

    // Accident
    Route::prefix('assets/accident')->name('accident.')->controller(AccidentController::class)->group(function () {
        Route::get('/', 'index')->name('index')->middleware('hasPermission:accidents_read');
        Route::get('create', 'create')->name('create')->middleware('hasPermission:accidents_create');
        Route::post('store', 'store')->name('store')->middleware('hasPermission:accidents_create');
        Route::get('edit/{id}', 'edit')->name('edit')->middleware('hasPermission:accidents_update');
        Route::put('update', 'update')->name('update')->middleware('hasPermission:accidents_update');
        Route::delete('delete/{id}', 'delete')->name('delete')->middleware('hasPermission:accidents_delete');
    });

    // Asset Reports
    Route::prefix('assets/reports')->name('assets.')->controller(AssetController::class)->group(function () {
        Route::get('/', 'reports')->name('reports')->middleware('hasPermission:assets_reports');
    });

    // ─── Collection (Collecte & Livraison) ──────
    Route::prefix('collection')->name('admin.collection.')->group(function () {
        Route::get('/', [App\Http\Controllers\Backend\AdminCollectionController::class, 'index'])->name('index');
        Route::get('/{collection}', [App\Http\Controllers\Backend\AdminCollectionController::class, 'show'])->name('show');
        Route::post('/{collection}/assign', [App\Http\Controllers\Backend\AdminCollectionController::class, 'assign'])->name('assign');
        Route::put('/{collection}/status', [App\Http\Controllers\Backend\AdminCollectionController::class, 'updateStatus'])->name('status');
        Route::get('/map/live', [App\Http\Controllers\Backend\AdminCollectionController::class, 'map'])->name('map');
        Route::get('/deliveryman/{deliveryman}/location', [App\Http\Controllers\Backend\AdminCollectionController::class, 'deliverymanLocation'])->name('deliveryman.location');
        Route::get('/stats/realtime', [App\Http\Controllers\Backend\AdminCollectionController::class, 'stats'])->name('stats');
    });

});
