<header class="wc-header" id="wcHeader">
    {{-- Burger mobile --}}
    <button type="button" onclick="document.body.classList.toggle('wc-drawer-open');document.getElementById('wcBackdrop').classList.toggle('hidden')" class="wc-header-burger" aria-label="Ouvrir le menu">
        <i class="fas fa-bars"></i>
        <span>Menu</span>
    </button>

    {{-- Titre de page --}}
    <h1 class="wc-header-title">@yield('title')</h1>

    {{-- Actions --}}
    <div class="ml-auto flex items-center gap-2">

        {{-- Solde wallet --}}
        @php
            $walletBalance = Auth::user()->merchant->wallet_balance ?? 0;
            $currency = settings()->currency ?? 'FCFA';
        @endphp
        <a href="{{ route('merchant-panel.my.wallet.index') }}" class="wc-header-wallet" title="{{ __('parcel.my_wallet') }}">
            <i class="fas fa-wallet text-[12px]"></i>
            <span class="wc-tabular">{{ formatPrice($walletBalance, $currency) }}</span>
        </a>

        {{-- Nouveau colis --}}
        <a href="{{ route('merchant-panel.parcel.create') }}" class="wc-header-create">
            <i class="fas fa-plus text-[12px]"></i>
            <span>Nouveau colis</span>
        </a>

        {{-- Notifications --}}
        <div class="dropdown">
            <button type="button" class="wc-header-icon" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-label="Notifications" aria-expanded="false">
                <i class="fas fa-bell"></i>
                <span>Notifications</span>
                <span class="wc-notif-badge" @if(count(notifications()) === 0) style="display:none" @endif></span>
            </button>
            <div class="dropdown-menu dropdown-menu-end wc-notif-menu">
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
            <button type="button" class="wc-header-profile" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menu utilisateur">
                <div class="wc-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <span class="hidden lg:block text-[13.5px] font-bold text-wc-ink max-w-[110px] truncate">{{ Auth::user()->name }}</span>
                <i class="fas fa-chevron-down text-[10px] text-wc-muted-2 hidden lg:block"></i>
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