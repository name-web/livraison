<aside class="wc-sidebar" id="wcSidebar">
    {{-- Brand --}}
    <div class="wc-sidebar-brand">
        <a href="{{ url('/dashboard') }}" class="no-underline flex items-center">
            <img src="{{ settings()->logo_image }}" alt="{{ settings()->name }}" class="wc-sidebar-brand-logo" style="max-height:34px">
        </a>
    </div>

    {{-- Marchand --}}
    <div class="wc-sidebar-merchant">
        <div class="wc-sidebar-merchant-avatar">
            {{ strtoupper(substr(Auth::user()->merchant->business_name, 0, 1)) }}
        </div>
        <div class="overflow-hidden min-w-0">
            <div class="font-bold text-[13.5px] text-white truncate leading-tight wc-sidebar-merchant-name">{{ Auth::user()->merchant->business_name }}</div>
            <div class="wc-sidebar-merchant-role">Marchand</div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto overflow-x-hidden px-3.5 pb-5 pt-1 scrollbar-thin">
        {{-- Activité --}}
        <div class="wc-sidebar-group-label">Activité</div>

        <a href="{{ url('/dashboard') }}" data-tooltip="Dashboard" class="wc-sidebar-item {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i>
            <span>{{ __('dashboard.title') }}</span>
        </a>
        <a href="{{ route('merchant-panel.parcel.index') }}" data-tooltip="Colis" class="wc-sidebar-item {{ request()->routeIs('merchant-panel.parcel.index') || request()->routeIs('merchant-panel.parcel.filter') ? 'active' : '' }}">
            <i class="fas fa-box"></i>
            <span>{{ __('menus.parcel') }}</span>
        </a>
        <a href="{{ route('merchant-panel.parcel-bank.index') }}" data-tooltip="Banque de colis" class="wc-sidebar-item {{ request()->routeIs('merchant-panel.parcel-bank.index') ? 'active' : '' }}">
            <i class="fas fa-warehouse"></i>
            <span>{{ __('menus.parcel_bank') }}</span>
        </a>

        {{-- Gestion --}}
        <div class="wc-sidebar-group-label">Gestion</div>

        <a href="{{ route('merchant-panel.shops.index') }}" data-tooltip="Boutiques" class="wc-sidebar-item {{ request()->routeIs('merchant-panel.shops.*') ? 'active' : '' }}">
            <i class="fas fa-store"></i>
            <span>{{ __('parcel.shop') }}</span>
        </a>
        <a href="{{ route('merchant.panel.invoice.index') }}" data-tooltip="Factures" class="wc-sidebar-item {{ request()->routeIs('merchant.panel.invoice.*') ? 'active' : '' }}">
            <i class="fas fa-file-invoice"></i>
            <span>{{ __('menus.invoice') }}</span>
        </a>
        <a href="{{ route('merchant-panel.payment-request.index') }}" data-tooltip="Paiements" class="wc-sidebar-item {{ request()->routeIs('merchant-panel.payment-request.*') ? 'active' : '' }}">
            <i class="fas fa-credit-card"></i>
            <span>{{ __('menus.payout') }}</span>
        </a>

        {{-- Finances --}}
        <div class="wc-sidebar-group-label">Finances</div>

        <a href="{{ route('merchant-panel.my.wallet.index') }}" data-tooltip="Wallet" class="wc-sidebar-item {{ request()->routeIs('merchant-panel.my.wallet.*') ? 'active' : '' }}">
            <i class="fas fa-wallet"></i>
            <span>{{ __('parcel.my_wallet') }}</span>
        </a>
        <a href="{{ route('merchant.accounts.account-transaction.index') }}" data-tooltip="Transactions" class="wc-sidebar-item {{ request()->routeIs('merchant.accounts.account-transaction.*') ? 'active' : '' }}">
            <i class="fas fa-exchange-alt"></i>
            <span>{{ __('menus.account_transaction') }}</span>
        </a>
        <a href="{{ route('merchant.accounts.statements.index') }}" data-tooltip="Relevés" class="wc-sidebar-item {{ request()->routeIs('merchant.accounts.statements.*') ? 'active' : '' }}">
            <i class="fas fa-receipt"></i>
            <span>{{ __('menus.statements') }}</span>
        </a>

        {{-- Autres --}}
        <div class="wc-sidebar-group-label">Autres</div>

        <a href="{{ route('merchant-panel.support.index') }}" data-tooltip="Support" class="wc-sidebar-item {{ request()->routeIs('merchant-panel.support.*') ? 'active' : '' }}">
            <i class="fas fa-headset"></i>
            <span>{{ __('menus.support') }}</span>
        </a>
        <a href="{{ route('merchant-panel.news-offer.index') }}" data-tooltip="Actualités & Offres" class="wc-sidebar-item {{ request()->routeIs('merchant-panel.news-offer.*') ? 'active' : '' }}">
            <i class="fas fa-newspaper"></i>
            <span>{{ __('news_offer.title') }}</span>
        </a>

        {{-- Réduire (desktop) --}}
        <button type="button" data-sidebar-collapse class="wc-sidebar-collapse-btn" title="Réduire / agrandir le menu" aria-label="Réduire ou agrandir la barre latérale">
            <i class="fas fa-chevron-left text-[13px]" id="collapseIcon"></i>
            <span>Réduire</span>
        </button>
    </nav>

    {{-- Footer --}}
    <div class="wc-sidebar-footer">
        © {{ date('Y') }} {{ settings()->name }}
    </div>
</aside>

{{-- Backdrop mobile --}}
<div class="fixed inset-0 bg-black/40 z-[1049] hidden" id="wcBackdrop" onclick="document.body.classList.remove('wc-drawer-open');this.classList.add('hidden')"></div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var body = document.body;
    var icon = document.getElementById('collapseIcon');

    function applyCollapsed(state) {
        body.classList.toggle('wc-sidebar-collapsed', state);
        if (icon) icon.style.transform = state ? 'rotate(180deg)' : '';
        try { localStorage.setItem('wc-sidebar-collapsed', state ? '1' : '0'); } catch (e) {}
    }

    document.querySelectorAll('[data-sidebar-collapse]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            applyCollapsed(!body.classList.contains('wc-sidebar-collapsed'));
        });
    });

    try {
        if (localStorage.getItem('wc-sidebar-collapsed') === '1' && window.innerWidth >= 992) {
            applyCollapsed(true);
        }
    } catch (e) {}
});
</script>