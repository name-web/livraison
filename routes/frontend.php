<?php

use App\Http\Controllers\Backend\MerchantController;
use App\Http\Controllers\Backend\SocialLoginController;
use App\Http\Controllers\Frontend\FrontendController;
use App\Http\Controllers\LocalizationController;
use Illuminate\Support\Facades\Route;

// ─── Frontend public ──────────────────────────────
Route::controller(FrontendController::class)->group(function () {
    Route::get('/', 'index')->name('home');
    Route::get('/tracking', 'tracking')->name('tracking.index');
    Route::get('/about-us', 'aboutUs')->name('aboutus.index');
    Route::get('/privacy-and-policy', 'privacyPolicy')->name('privacy.policy.index');
    Route::get('/terms-of-condition', 'termsOfCondition')->name('termsof.condition.index');
    Route::get('/faq-list', 'faq')->name('get.faq.index');
    Route::post('subscribe-store', 'subscribe')->name('subscribe.store');
    Route::get('contact-send', 'contactSendPage')->name('contact.send.page');
    Route::post('contact-message-send', 'contactMessageSend')->name('contact.message.send');
    Route::get('blog-details/{id}', 'blogDetails')->name('blog.details');
    Route::get('get-blogs', 'blogs')->name('get.blogs');
    Route::get('service-details/{id}', 'serviceDetails')->name('service.details');
});

// ─── Inscription marchand ─────────────────────────
Route::get('merchant/sign-up', [MerchantController::class, 'signUp'])->name('merchant.sign-up');
Route::post('merchant/sign-up-store', [MerchantController::class, 'signUpStore'])->name('merchant.sign-up-store');
Route::post('merchant/otp-verification', [MerchantController::class, 'otpVerification'])->name('merchant.otp-verification');
Route::get('merchant/otp-verification-form', [MerchantController::class, 'otpVerificationForm'])->name('merchant.otp-verification-form');
Route::post('merchant/resend-otp', [MerchantController::class, 'resendOTP'])->name('merchant.resend-otp');

// ─── Social authentication ────────────────────────
Route::get('/login/{social}', [SocialLoginController::class, 'socialRedirect'])->name('social.login');
Route::get('/google/login', [SocialLoginController::class, 'authGoogleLogin']);
Route::get('/facebook/login', [SocialLoginController::class, 'authFacebookLogin']);

// ─── Localisation ─────────────────────────────────
Route::get('localization/{language}', [LocalizationController::class, 'setLocalization'])->name('setlocalization');
