<?php

use App\Http\Controllers\Backend\FrontWeb\BlogController;
use App\Http\Controllers\Backend\FrontWeb\FaqController;
use App\Http\Controllers\Backend\FrontWeb\PageController;
use App\Http\Controllers\Backend\FrontWeb\PartnerController;
use App\Http\Controllers\Backend\FrontWeb\SectionController;
use App\Http\Controllers\Backend\FrontWeb\ServiceController;
use App\Http\Controllers\Backend\FrontWeb\SocialLinkController;
use App\Http\Controllers\Backend\FrontWeb\WhyCourierController;
use Illuminate\Support\Facades\Route;

Route::prefix('front-web')->group(function () {
    // Social Link
    Route::prefix('social-link')->name('social.link.')->controller(SocialLinkController::class)->group(function () {
        Route::get('/', 'index')->name('index')->middleware('hasPermission:social_link_read');
        Route::get('create', 'create')->name('create')->middleware('hasPermission:social_link_create');
        Route::post('store', 'store')->name('store')->middleware('hasPermission:social_link_create');
        Route::get('edit/{id}', 'edit')->name('edit')->middleware('hasPermission:social_link_update');
        Route::put('update/{id}', 'update')->name('update')->middleware('hasPermission:social_link_update');
        Route::delete('delete/{id}', 'delete')->name('delete')->middleware('hasPermission:social_link_delete');
    });

    // Service
    Route::prefix('service')->name('service.')->controller(ServiceController::class)->group(function () {
        Route::get('/', 'index')->name('index')->middleware('hasPermission:service_read');
        Route::get('create', 'create')->name('create')->middleware('hasPermission:service_create');
        Route::post('store', 'store')->name('store')->middleware('hasPermission:service_create');
        Route::get('edit/{id}', 'edit')->name('edit')->middleware('hasPermission:service_update');
        Route::put('update/{id}', 'update')->name('update')->middleware('hasPermission:service_update');
        Route::delete('delete/{id}', 'delete')->name('delete')->middleware('hasPermission:service_delete');
    });

    // Why Courier
    Route::prefix('why-courier')->name('why.courier.')->controller(WhyCourierController::class)->group(function () {
        Route::get('/', 'index')->name('index')->middleware('hasPermission:why_courier_read');
        Route::get('create', 'create')->name('create')->middleware('hasPermission:why_courier_create');
        Route::post('store', 'store')->name('store')->middleware('hasPermission:why_courier_create');
        Route::get('edit/{id}', 'edit')->name('edit')->middleware('hasPermission:why_courier_update');
        Route::put('update/{id}', 'update')->name('update')->middleware('hasPermission:why_courier_update');
        Route::delete('delete/{id}', 'delete')->name('delete')->middleware('hasPermission:why_courier_delete');
    });

    // FAQ
    Route::prefix('faq')->name('faq.')->controller(FaqController::class)->group(function () {
        Route::get('/', 'index')->name('index')->middleware('hasPermission:faq_read');
        Route::get('create', 'create')->name('create')->middleware('hasPermission:faq_create');
        Route::post('store', 'store')->name('store')->middleware('hasPermission:faq_create');
        Route::get('edit/{id}', 'edit')->name('edit')->middleware('hasPermission:faq_update');
        Route::put('update/{id}', 'update')->name('update')->middleware('hasPermission:faq_update');
        Route::delete('delete/{id}', 'delete')->name('delete')->middleware('hasPermission:faq_delete');
    });

    // Partner
    Route::prefix('partner')->name('partner.')->controller(PartnerController::class)->group(function () {
        Route::get('/', 'index')->name('index')->middleware('hasPermission:partner_read');
        Route::get('create', 'create')->name('create')->middleware('hasPermission:partner_create');
        Route::post('store', 'store')->name('store')->middleware('hasPermission:partner_create');
        Route::get('edit/{id}', 'edit')->name('edit')->middleware('hasPermission:partner_update');
        Route::put('update/{id}', 'update')->name('update')->middleware('hasPermission:partner_update');
        Route::delete('delete/{id}', 'delete')->name('delete')->middleware('hasPermission:partner_delete');
    });

    // Blogs
    Route::prefix('blogs')->name('blogs.')->controller(BlogController::class)->group(function () {
        Route::get('/', 'index')->name('index')->middleware('hasPermission:blogs_read');
        Route::get('create', 'create')->name('create')->middleware('hasPermission:blogs_create');
        Route::post('store', 'store')->name('store')->middleware('hasPermission:blogs_create');
        Route::get('edit/{id}', 'edit')->name('edit')->middleware('hasPermission:blogs_update');
        Route::put('update/{id}', 'update')->name('update')->middleware('hasPermission:blogs_update');
        Route::delete('delete/{id}', 'delete')->name('delete')->middleware('hasPermission:blogs_delete');
    });

    // Pages
    Route::prefix('pages')->name('pages.')->controller(PageController::class)->group(function () {
        Route::get('/', 'index')->name('index')->middleware('hasPermission:pages_read');
        Route::get('edit/{id}', 'edit')->name('edit')->middleware('hasPermission:pages_update');
        Route::put('update/{id}', 'update')->name('update')->middleware('hasPermission:pages_update');
    });

    // Sections
    Route::prefix('section')->name('section.')->controller(SectionController::class)->group(function () {
        Route::get('/', 'index')->name('index')->middleware('hasPermission:section_read');
        Route::get('edit/{id}', 'edit')->name('edit')->middleware('hasPermission:section_update');
        Route::put('update/{id}', 'update')->name('update')->middleware('hasPermission:section_update');
    });
});
