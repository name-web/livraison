<!-- top bar -->
<section class="container-fluid top-bar d-none d-md-block">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 col-md-6">
                <ul class="top-bar-info mb-0">
                    <li><i class="fas fa-map-marker-alt"></i> {{ @settings()->address }}</li>
                    <li><a href="tel:{{ @settings()->phone }}" class="top-bar-link"><i class="fas fa-phone-alt"></i> {{ @settings()->phone }}</a></li>
                    <li><a href="mailto:{{ @settings()->email }}" class="top-bar-link"><i class="fas fa-envelope"></i> {{ @settings()->email }}</a></li>
                </ul>
            </div>
            <div class="col-lg-5 col-md-6 text-end">
                <div class="top-bar-social">
                    @foreach ($social_links as $social)
                        <a href="{{ $social->link }}" title="{{ $social->name }}" target="_blank"><i class="{{ $social->icon }}"></i></a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
<!-- // top bar -->

<!-- navigation -->
<section class="container-fluid border-bottom navbar-sticky">
    <nav class="navbar navbar-expand-lg center-nav transparent navbar-light p-3">
        <div class="container flex-lg-row flex-nowrap align-items-center">
            <div class="navbar-brand pt-0 me-lg-4">
                <a href="{{ url('/') }}">
                    <img class="logo logo-green" src="{{ static_asset('frontend/images/logo-green.png') }}" width="200" alt="{{ settings()->name }}">
                </a>
            </div>
            <div class=" navbar-collapse offcanvas offcanvas-nav offcanvas-start text-bg-dark "  tabindex="-1" id="offcanvasDarkNavbar" aria-labelledby="offcanvasDarkNavbarLabel">
                <div class="offcanvas-header w-90 ">
                    <div class="offcanvas-brand d-flex align-items-center gap-2">
                        <img class="offcanvas-logo" src="{{ static_asset('frontend/images/logo-green.png') }}" alt="{{ settings()->name }}">
                        <h3 class="text-white fs-30 mb-0"> {{ settings()->name }}</h3>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="menu offcanvas-body mx-lg-auto d-flex flex-column h-100 w-90">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link  @if(request()->is('/')) active @endif " href="{{ url('/') }}">{{ __('levels.home') }}</a></li>
                        <li class="nav-item"><a class="nav-link " href="{{ url('/') }}#pricing"  >{{ __('levels.pricing') }}</a></li>
                        <li class="nav-item"><a class="nav-link @if(request()->is('tracking')) active @endif " href="{{ route('tracking.index') }}"  >{{ __('levels.tracking') }}</a></li>
                        <li class="nav-item"><a class="nav-link @if(request()->is('get-blogs')) active @endif " href="{{ route('get.blogs') }}"  >{{ __('levels.blogs') }}</a></li>
                        <li class="nav-item"><a class="nav-link @if(request()->is('about-us')) active @endif " href="{{route('aboutus.index')}}" >{{ __('levels.about') }}</a></li>
                        <li class="nav-item"><a class="nav-link @if(request()->is('contact-send')) active @endif " href="{{ route('contact.send.page') }}">{{ __('levels.contact') }}</a></li>
                    </ul>
                    <div class="offcanvas-footer d-lg-none mt-3">
                        <div>
                            <a href="mailto:{{ @settings()->email }}" class="link-inverse text-white"><i class="fas fa-envelope me-2"></i>{{ settings()->email }}</a>
                            <br> <a href="tel:{{ @settings()->phone }}" class="link-inverse text-white"><i class="fas fa-phone-alt me-2"></i>{{ settings()->phone }}</a> <br>
                            <nav class="nav social social-white mt-4">
                                @foreach ($social_links as $social)
                                    <a href="{{ $social->link }}"><i class="{{ $social->icon }} text-white"></i></a>
                                @endforeach
                            </nav>
                        </div>
                        <div class="offcanvas-auth mt-4">
                            @if(Auth::check())
                                <a href="{{ route('dashboard.index') }}" class="btn btn-primary w-100 mb-2">
                                    <i class="fa fa-home me-2"></i>{{ __('levels.dashboard') }}
                                </a>
                                <a href="{{ route('logout') }}" class="btn btn-outline-primary w-100"
                                    onclick="event.preventDefault();
                                    document.getElementById('offcanvas-logout-form').submit();">
                                    <i class="fas fa-power-off me-2"></i>{{ __('menus.logout') }}
                                </a>
                                <form id="offcanvas-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-sign-in-alt me-2"></i>{{ __('levels.login') }}
                                </a>
                                <a href="{{ route('merchant.sign-up') }}" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-user-plus me-2"></i>{{ __('levels.register') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="navbar-other d-flex ms-auto auth-info">
                <ul class="navbar-nav flex-row align-items-center ms-auto">
                    <li class="nav-item me-2">
                        @include('frontend.partials.language-dropdown')
                    </li>
                    @if(Auth::check())
                        <li class="nav-item ">
                            <div class="dropdown">
                                <a class="dropdown-toggle nav-link auth-btn" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                 {{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu user-dropdown">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('dashboard.index') }}">
                                            <i class="fa fa-home"></i>
                                            {{ __('levels.dashboard') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                            <i class="fas fa-power-off mr-2"></i>
                                            {{ __('menus.logout') }}
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @else
                        <li class="nav-item login-button me-2 d-none d-lg-inline-block">
                            <a href="{{ route('login') }}" class="nav-link auth-btn btn btn-outline-primary rounded-pill" >{{ __('levels.login') }}</a>
                        </li>
                        <li class="nav-item d-none d-lg-inline-block">
                            <a href="{{ route('merchant.sign-up') }}" class="btn btn-primary auth-btn rounded-pill" >{{ __('levels.register') }}</a>
                        </li>
                    @endif
                    <li class="nav-item d-lg-none ms-2">
                        <button class="offcanvas-nav-btn " type="button"  data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar" ><span class="navbar-toggler-icon"></span></button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</section>
<!-- // navigation -->
