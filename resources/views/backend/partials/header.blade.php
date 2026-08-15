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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css">
    <link href="{{static_asset('backend')}}/plugins/fonts/circular-std/style.css" rel="stylesheet">
    <link rel="stylesheet" href="{{static_asset('backend')}}/libs/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="{{static_asset('backend')}}/plugins/fonts/fontawesome/css/fontawesome-all.css">
    <link rel="stylesheet" href="{{static_asset('backend')}}/plugins/charts/chartist-bundle/chartist.css">
    <link rel="stylesheet" href="{{static_asset('backend')}}/plugins/charts/morris-bundle/morris.css">
    <link rel="stylesheet" href="{{static_asset('backend')}}/plugins/fonts/material-design-iconic-font/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="{{static_asset('backend')}}/plugins/charts/c3charts/c3.css">
    <link rel="stylesheet" href="{{static_asset('backend')}}/plugins/fonts/flag-icon-css/flag-icon.min.css">
    <link rel="stylesheet" href="{{static_asset('backend')}}/libs/css/datepicker.min.css">
    <link rel="stylesheet" href="{{static_asset('backend')}}/libs/css/custom.css">
    <link rel="stylesheet" href="{{static_asset('backend')}}/css/custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.5.1/css/flag-icons.min.css" /> 
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
                $merchantCss = $manifest['resources/css/merchant.css']['file'] ?? null;
            @endphp
            @if($merchantCss)
                <link rel="stylesheet" href="{{ asset('build/' . $merchantCss) }}">
            @endif
        @endif
        <style>
            /* Fallback layout minimal (appliqué tant que merchant.css n'est pas chargé) */
            body.wc-merchant { background: #f5f6f8; color: #111827; font-size: 15px; -webkit-font-smoothing: antialiased; }
            .wc-sidebar { position: fixed; top: 0; left: 0; bottom: 0; width: 264px; background: #fff; border-right: 1px solid #e8eaee; z-index: 1050; display: flex; flex-direction: column; transition: width .3s ease, transform .3s ease; }
            .wc-sidebar-collapsed .wc-sidebar { width: 80px; }
            .wc-sidebar-collapsed main.dashboard-ecommerce { padding-left: 80px !important; }
            .wc-header { position: fixed; top: 0; right: 0; height: 64px; background: rgba(255,255,255,.92); backdrop-filter: blur(8px); border-bottom: 1px solid #e8eaee; display: flex; align-items: center; gap: 12px; padding: 0 24px; z-index: 1040; left: 264px; transition: left .3s ease; }
            .wc-sidebar-collapsed .wc-header { left: 80px; }
            main.dashboard-ecommerce { padding-left: 264px; transition: padding-left .3s ease; margin-top: 64px; }
            @media (max-width: 991.98px) {
                .wc-sidebar { transform: translateX(-105%); width: 280px; }
                .wc-sidebar-collapsed .wc-sidebar { width: 280px; transform: translateX(-105%); }
                body.wc-drawer-open .wc-sidebar { transform: translateX(0); }
                .wc-header { left: 0 !important; }
                main.dashboard-ecommerce { padding-left: 0 !important; }
            }
        </style>
    @endif
    <!-- push target to head -->
    @stack('styles')
    <title>@yield('title')</title>
</head>
<body @if(Auth::check() && Auth::user()->user_type == \App\Enums\UserType::MERCHANT) class="wc-merchant" @endif>
    <!-- main wrapper -->
    <div class="dashboard-main-wrapper login-dashboard-main-wrapper">

