<!doctype html>
<html lang="en" @if(app()->getLocale() == 'ar') dir="rtl"@endif>
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,  minimum-scale=0.8, maximum-scale = 0.8, user-scalable = no , shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="shortcut icon" href="{{ settings()->favicon_image }}" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{static_asset('backend')}}/plugins/bootstrap-five/bootstrap.min.css">
    <link href="{{static_asset('backend')}}/plugins/fonts/circular-std/style.css" rel="stylesheet">
    <link rel="stylesheet" href="{{static_asset('backend')}}/libs/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="{{static_asset('backend')}}/plugins/charts/chartist-bundle/chartist.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{static_asset('backend')}}/plugins/charts/morris-bundle/morris.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{static_asset('backend')}}/plugins/fonts/material-design-iconic-font/css/materialdesignicons.min.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{static_asset('backend')}}/plugins/charts/c3charts/c3.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{static_asset('backend')}}/plugins/fonts/flag-icon-css/flag-icon.min.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{static_asset('backend')}}/libs/css/datepicker.min.css" media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="{{static_asset('backend')}}/plugins/charts/chartist-bundle/chartist.css">
        <link rel="stylesheet" href="{{static_asset('backend')}}/plugins/charts/morris-bundle/morris.css">
        <link rel="stylesheet" href="{{static_asset('backend')}}/plugins/fonts/material-design-iconic-font/css/materialdesignicons.min.css">
        <link rel="stylesheet" href="{{static_asset('backend')}}/plugins/charts/c3charts/c3.css">
        <link rel="stylesheet" href="{{static_asset('backend')}}/plugins/fonts/flag-icon-css/flag-icon.min.css">
        <link rel="stylesheet" href="{{static_asset('backend')}}/libs/css/datepicker.min.css">
    </noscript>
    <link rel="stylesheet" href="{{static_asset('backend')}}/libs/css/custom.css">
    <link rel="stylesheet" href="{{static_asset('backend')}}/css/custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.5.1/css/flag-icons.min.css" media="print" onload="this.media='all'" /> 
    <link rel="stylesheet" href="{{ static_asset('backend/plugins') }}/toastr/toastr.min.css">
    <style>
        :root {
            --primary-color: {{ settings()->primary_color }};
            --primary-text-color: {{ settings()->text_color }};
            --warningcolor: {{ settings()->primary_color }};
        }
    </style>
    @if (Auth::check() && Auth::user()->user_type == \App\Enums\UserType::MERCHANT)
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        @if(file_exists(public_path('build/manifest.json')))
            @php
                $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
                $merchantSystemCss = $manifest['resources/sass/merchant.scss']['file'] ?? null;
                $merchantCss = $manifest['resources/css/merchant.css']['file'] ?? null;
            @endphp
            @if($merchantSystemCss)
                <link rel="stylesheet" href="{{ asset('build/' . $merchantSystemCss) }}">
            @endif
            @if($merchantCss)
                <link rel="stylesheet" href="{{ asset('build/' . $merchantCss) }}">
            @endif
        @endif
        {{-- WeCourier Green Command — chargé en dernier pour écraser l'ancien thème --}}
        <link rel="stylesheet" href="{{ asset('backend/css/merchant-green.css') }}?v={{ file_exists(public_path('backend/css/merchant-green.css')) ? filemtime(public_path('backend/css/merchant-green.css')) : time() }}">
    @endif
    <!-- push target to head -->
    @stack('styles')
    <title>@yield('title')</title>
</head>
<body @if(Auth::check() && Auth::user()->user_type == \App\Enums\UserType::MERCHANT) class="wc-merchant" @endif>
    <!-- main wrapper -->
    <div class="dashboard-main-wrapper login-dashboard-main-wrapper">

