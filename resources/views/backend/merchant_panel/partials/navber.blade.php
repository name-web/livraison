<header class="gc-topbar" id="gcTopbar">
    {{-- Burger mobile --}}
    <button type="button" onclick="document.body.classList.toggle('wc-drawer-open');var b=document.getElementById('wcBackdrop');if(b)b.classList.toggle('hidden')" class="gc-burger" aria-label="Ouvrir le menu">
        <i class="fas fa-bars"></i>
    </button>

    {{-- Titre de page --}}
    <div class="gc-page-title">
        <small>{{ __('sidebar.merchant_space') }}</small>
        <strong>@yield('title')</strong>
    </div>

    {{-- Actions --}}
    <div class="gc-top-actions">

        {{-- Solde wallet --}}
        @php
            $walletBalance = Auth::user()->merchant->wallet_balance ?? 0;
            $currency = settings()->currency ?? 'FCFA';
        @endphp
        <a href="{{ route('merchant-panel.my.wallet.index') }}" class="gc-header-wallet" title="{{ __('parcel.my_wallet') }}">
            <i class="fas fa-wallet"></i>
            <span class="wc-tabular">{{ formatPrice($walletBalance, $currency) }}</span>
        </a>

        {{-- Nouveau colis --}}
        <a href="{{ route('merchant-panel.parcel.create') }}" class="gc-btn-new">
            <i class="fas fa-plus"></i> <span>Nouveau colis</span>
        </a>

        {{-- Notifications --}}
        <div class="dropdown">
            <button type="button" class="gc-notification" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-label="Notifications" aria-expanded="false">
                <i class="fas fa-bell"></i>
                <span class="gc-notif-badge" @if(count(notifications()) === 0) style="display:none" @endif></span>
            </button>
            <div class="dropdown-menu dropdown-menu-end gc-notif-menu">
                <div class="flex items-center justify-between px-4 py-3 border-b border-wc-border">
                    <span class="text-[14px] font-extrabold text-wc-ink">Notifications</span>
                    <span class="text-[11.5px] font-bold text-wc-muted-2">{{ count(notifications()) }}</span>
                </div>
                <div class="max-h-[330px] overflow-y-auto scrollbar-thin">
                    @include('backend.merchant_panel.partials.notification')
                </div>
            </div>
        </div>

        {{-- Profil --}}
        <div class="dropdown">
            <button type="button" class="gc-profile" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menu utilisateur">
                <div class="gc-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="gc-profile-info">
                    <strong>{{ Auth::user()->name }}</strong>
                    <span>Marchand</span>
                </div>
                <div class="gc-profile-arrow">
                    <i class="fas fa-chevron-down"></i>
                </div>
            </button>
            <div class="dropdown-menu dropdown-menu-end">
                <a href="{{ route('merchant-profile.index', Auth::id()) }}" class="dropdown-item">
                    <i class="fas fa-user"></i> Mon profil
                </a>
                <a href="{{ route('merchant.accounts.payment-account.index') }}" class="dropdown-item">
                    <i class="fas fa-credit-card"></i> Comptes de paiement
                </a>
                @if (Auth::user()->facebook_id == null && Auth::user()->google_id == null)
                    <a href="{{ route('merchant-password.change', Auth::id()) }}" class="dropdown-item">
                        <i class="fas fa-key"></i> Mot de passe
                    </a>
                @endif
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item text-[#b91c1c] hover:bg-[#fef2f2] hover:text-[#b91c1c]">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

{{-- Modals pickup (conservés) --}}
@include('backend.merchant_panel.pickup_request.pickup_request_modal')
@include('backend.merchant_panel.pickup_request.regular_modal')
@include('backend.merchant_panel.pickup_request.express_modal')
