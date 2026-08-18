<?php

use App\Http\Controllers\Backend\ParcelController;
use Illuminate\Support\Facades\Route;

// ─── Parcel CRUD ──────────────────────────────────
Route::get('parcel/index', [ParcelController::class, 'index'])->name('parcel.index')->middleware('hasPermission:parcel_read');
Route::get('parcel/details/{id}', [ParcelController::class, 'details'])->name('parcel.details')->middleware('hasPermission:parcel_read');
Route::get('parcel/logs/{id}', [ParcelController::class, 'logs'])->name('parcel.logs')->middleware('hasPermission:parcel_read');
Route::get('parcel/clone/{id}', [ParcelController::class, 'duplicate'])->name('parcel.clone');
Route::get('parcel/create', [ParcelController::class, 'create'])->name('parcel.create')->middleware('hasPermission:parcel_create');
Route::post('parcel/store', [ParcelController::class, 'store'])->name('parcel.store')->middleware('hasPermission:parcel_create');
Route::post('parcel/clone-store', [ParcelController::class, 'duplicateStore'])->name('parcel.clone-store');
Route::get('parcel/edit/{id}', [ParcelController::class, 'edit'])->name('parcel.edit')->middleware('hasPermission:parcel_update');
Route::put('parcel/update/{id}', [ParcelController::class, 'update'])->name('parcel.update')->middleware('hasPermission:parcel_update');
Route::get('parcel/status-update/{id}/{status_id}', [ParcelController::class, 'statusUpdate'])->name('parcel.status-update')->middleware('hasPermission:parcel_status_update');
Route::delete('parcel/delete/{id}', [ParcelController::class, 'destroy'])->name('parcel.delete')->middleware('hasPermission:parcel_delete');
Route::get('parcel/print/{id}', [ParcelController::class, 'parcelPrint'])->name('parcel.print')->middleware('hasPermission:parcel_read');
Route::get('parcel/print/{id}/label', [ParcelController::class, 'parcelPrintLabel'])->name('parcel.print-label')->middleware('hasPermission:parcel_read');
Route::get('parcel/multiple/print/label', [ParcelController::class, 'parcelMultiplePrintLabel'])->name('parcel.multiple.print-label');

// ─── Parcel Status Updates ────────────────────────
Route::post('parcel/deliveryman/search', [ParcelController::class, 'deliverymanSearch'])->name('parcel.deliveryman.search');
Route::post('parcel/pickup-man/assigned', [ParcelController::class, 'PickupManAssigned'])->name('parcel.pickup.man-assigned')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/pickup-man/assigned/cancel', [ParcelController::class, 'PickupManAssignedCancel'])->name('parcel.pickup.man-assigned-cancel')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/pickup/re-schedule', [ParcelController::class, 'PickupReSchedule'])->name('parcel.pickup.re.schedule')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/pickup-reschedule/cancel', [ParcelController::class, 'PickupReScheduleCancel'])->name('parcel.pickup.re-schedule-cancel')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/pickup/received', [ParcelController::class, 'receivedBypickupman'])->name('parcel.received.by.pickup')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/pickup/received/cancel', [ParcelController::class, 'receivedBypickupmanCancel'])->name('parcel.pickup.man-received-cancel')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/received-warehouse', [ParcelController::class, 'receivedWarehouse'])->name('parcel.received.warehouse')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/received-warehouse/cancel', [ParcelController::class, 'receivedWarehouseCancel'])->name('parcel.received-warehouse-cancel')->middleware('hasPermission:parcel_status_update');
Route::get('parcel/filter', [ParcelController::class, 'filter'])->name('parcel.filter');
Route::post('parcel/search', [ParcelController::class, 'search'])->name('parcel.search');
Route::post('parcel/search-delivery-man-assing-multiple-parcel', [ParcelController::class, 'searchDeliveryManAssingMultipleParcel'])->name('parcel.search-delivery-man-assing-multiple-parcel');
Route::post('parcel/search-expense', [ParcelController::class, 'searchExpense'])->name('parcel.search-expense');
Route::post('parcel/search-income', [ParcelController::class, 'searchIncome'])->name('parcel.search-income');
Route::post('parcel/transfer-to-hub-multiple-parcel', [ParcelController::class, 'transferToHubMultipleParcel'])->name('parcel.transfer-to-hub-multiple-parcel')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/delivery-man-assign-multiple-parcel', [ParcelController::class, 'deliveryManAssignMultipleParcel'])->name('parcel.delivery-man-assign-multiple-parcel')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/transfer-to-hub', [ParcelController::class, 'transfertohub'])->name('parcel.transfer-to-hub')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/transfer-hub', [ParcelController::class, 'transferHub'])->name('parcel.transferHub')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/transfer-to-hub/cancel', [ParcelController::class, 'transfertoHubCancel'])->name('parcel.transfer-to-hub-cancel')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/received-by-hub', [ParcelController::class, 'receivedByHub'])->name('parcel.received-by.hub')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/received-by-hub/cancel', [ParcelController::class, 'receivedByHubCancel'])->name('parcel.received-by-hub-cancel')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/received-warehouse-hub-selected', [ParcelController::class, 'warehouseHubSelected'])->name('parcel.received.warehouse.hub.select');
Route::post('parcel/delivery-man-assign', [ParcelController::class, 'deliverymanAssign'])->name('parcel.delivery-man-assign')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/delivery-man/assign/cancel', [ParcelController::class, 'deliverymanAssignCancel'])->name('parcel.delivery-man-assign-cancel')->middleware('hasPermission:parcel_status_update');
Route::get('parcel/bulkassign/print', [ParcelController::class, 'ParcelBulkAssignPrint'])->name('parcel.parcel-bulkassign-print');
Route::post('parcel/delivery-reschedule', [ParcelController::class, 'deliveryReschedule'])->name('parcel.delivery.reschedule')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/delivery-re-scheule/cancel', [ParcelController::class, 'deliveryReScheduleCancel'])->name('parcel.delivery-re-schedule-cancel')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/return-to-qourier', [ParcelController::class, 'returntoQourier'])->name('parcel.return-to-qourier')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/return-to-qourier-cancel', [ParcelController::class, 'returntoQourierCancel'])->name('parcel.return-to-courier-cancel')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/return-assign-to-merchant', [ParcelController::class, 'returnAssignToMerchant'])->name('parcel.return-assign-to-merchant')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/return-assign-to-merchant/cancel', [ParcelController::class, 'returnAssignToMerchantCancel'])->name('parcel.return-assign-to-merchant-cancel')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/return-assign-to-merchant-reschedule', [ParcelController::class, 'returnAssignToMerchantReschedule'])->name('parcel.return-assign-to-merchant.reschedule')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/return-assign-re-schedule-to-merchant/cancel', [ParcelController::class, 'returnAssignToMerchantRescheduleCancel'])->name('parcel.return-assign-re-schedule-to-merchant-cancel')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/return-received-by-merchant', [ParcelController::class, 'returnReceivedByMerchant'])->name('parcel.return-received-by-merchant')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/return-received-by-merchant/cancel', [ParcelController::class, 'returnReceivedByMerchantCancel'])->name('parcel.return-received-by-merchant-cancel')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/delivered', [ParcelController::class, 'parcelDelivered'])->name('parcel.delivered')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/delivered/cancel', [ParcelController::class, 'parcelDeliveredCancel'])->name('parcel.delivered-cancel')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/partial-delivered', [ParcelController::class, 'parcelPartialDelivered'])->name('parcel.partial-delivered')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/partial-delivered/cancel', [ParcelController::class, 'parcelPartialDeliveredCancel'])->name('parcel.partial-delivered-cancel')->middleware('hasPermission:parcel_status_update');
Route::post('/transertohub-selected-hub', [ParcelController::class, 'transfertohubSelectedHub'])->name('transertohub.selected.hub');
Route::post('/parcel/received-by-multiple-hub', [ParcelController::class, 'parcelReceivedByMultipleHub'])->name('parcel.received-by-mulbiple-hub')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/recived-by-hub/search', [ParcelController::class, 'parcelRecivedByHubSearch'])->name('parcel.received-by-hub-search');
Route::post('assign-pickup/parcel/search', [ParcelController::class, 'AssignPickupParcelSearch'])->name('assign-pickup.parcel.search');
Route::post('assign-pickup/bulk', [ParcelController::class, 'AssignPickupBulk'])->name('parcel.assign-pickup-bulk')->middleware('hasPermission:parcel_status_update');
Route::post('assign-return-to-merchant/parcel/search', [ParcelController::class, 'AssignReturnToMerchantParcelSearch'])->name('assign-return-to-merchant.parcel.search');
Route::post('parcel/assign-return-to-merchant-bulk', [ParcelController::class, 'AssignReturnToMerchantBulk'])->name('parcel.assign-return-to-merchant-bulk')->middleware('hasPermission:parcel_status_update');
Route::post('parcel/priority/update', [ParcelController::class, 'priorityUpdate'])->name('parcel.priority.status');
Route::get('parcel/deliveryMan/show', [ParcelController::class, 'parcelDeliveryMan'])->name('parcel.parcelDeliveryMan');
Route::get('parcel/delivered/logs/info/{id}', [ParcelController::class, 'deliveredInfo'])->name('parcel.deliveredInfo');

// ─── Parcel Data Fetch ────────────────────────────
Route::post('parcel/merchant', [ParcelController::class, 'getMerchant'])->name('parcel.merchant.get');
Route::post('parcel/hub', [ParcelController::class, 'getHub'])->name('parcel.hub.get');
Route::post('parcel/merchant/shops', [ParcelController::class, 'merchantShops'])->name('parcel.merchant.shops');
Route::post('parcel/delivery-category', [ParcelController::class, 'deliveryWeight'])->name('parcel.deliveryCategory.deliveryWeight');
Route::post('parcel/delivery-charge', [ParcelController::class, 'deliveryCharge'])->name('parcel.deliveryCharge.get');

// ─── Parcel Import/Export ─────────────────────────
Route::get('parcel/import-parcel', [ParcelController::class, 'parcelImportExport'])->name('parcel.parcel-import')->middleware('hasPermission:parcel_create');
Route::post('parcel/file-import', [ParcelController::class, 'parcelImport'])->name('parcel.file-import')->middleware('hasPermission:parcel_create');
Route::get('parcel/file-export', [ParcelController::class, 'parcelExport'])->name('parcel.file-export');
Route::post('parcel/import/merchant', [ParcelController::class, 'getImportMerchant'])->name('parcel.import.merchant.get');
Route::post('get-merchant-cod', [ParcelController::class, 'getMerchantCod'])->name('get.merchant.cod');
