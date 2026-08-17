<aside class="gc-sidebar" id="gcSidebar">
    {{-- Brand --}}
    <a href="{{ url('/dashboard') }}" class="gc-brand" aria-label="{{ settings()->name }}">
        <img src="{{ settings()->logo_image }}" alt="{{ settings()->name }}" class="gc-brand-logo">
    </a>

    {{-- Activité --}}
    <div class="gc-section-title">{{ __('sidebar.activity') }}</div>
    <div class="gc-nav">
        <a href="{{ url('/dashboard') }}" class="gc-navlink {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
            <span class="gc-navicon"><i class="fas fa-th-large"></i></span>
            <span class="gc-navlabel">{{ __('dashboard.title') }}</span>
        </a>
        <a href="{{ route('merchant-panel.parcel.index') }}" class="gc-navlink {{ request()->routeIs('merchant-panel.parcel.index') || request()->routeIs('merchant-panel.parcel.filter') ? 'active' : '' }}">
            <span class="gc-navicon"><i class="fas fa-box"></i></span>
            <span class="gc-navlabel">{{ __('menus.parcel') }}</span>
            @php
                try {
                    $gcTotalParcels = \App\Models\Backend\Parcel::where('merchant_id', Auth::user()->merchant->id)->count();
                } catch (\Throwable $e) {
                    $gcTotalParcels = 0;
                }
            @endphp
            @if ($gcTotalParcels > 0)
                <span class="gc-count">{{ $gcTotalParcels > 999 ? '999+' : $gcTotalParcels }}</span>
            @endif
        </a>
    </div>

    {{-- Gestion --}}
    <div class="gc-section-title">{{ __('sidebar.management') }}</div>
    <div class="gc-nav">
        <a href="{{ route('merchant-panel.shops.index') }}" class="gc-navlink {{ request()->routeIs('merchant-panel.shops.*') ? 'active' : '' }}">
            <span class="gc-navicon"><i class="fas fa-store"></i></span>
            <span class="gc-navlabel">{{ __('parcel.shop') }}</span>
        </a>
        <a href="{{ route('merchant.panel.invoice.index') }}" class="gc-navlink {{ request()->routeIs('merchant.panel.invoice.*') ? 'active' : '' }}">
            <span class="gc-navicon"><i class="fas fa-file-invoice"></i></span>
            <span class="gc-navlabel">{{ __('menus.invoice') }}</span>
        </a>
        <a href="{{ route('merchant-panel.payment-request.index') }}" class="gc-navlink {{ request()->routeIs('merchant-panel.payment-request.*') ? 'active' : '' }}">
            <span class="gc-navicon"><i class="fas fa-credit-card"></i></span>
            <span class="gc-navlabel">{{ __('menus.payout') }}</span>
        </a>
    </div>

    {{-- Finances --}}
    <div class="gc-section-title">{{ __('sidebar.finances') }}</div>
    <div class="gc-nav">
        <a href="{{ route('merchant-panel.my.wallet.index') }}" class="gc-navlink {{ request()->routeIs('merchant-panel.my.wallet.*') ? 'active' : '' }}">
            <span class="gc-navicon"><i class="fas fa-wallet"></i></span>
            <span class="gc-navlabel">{{ __('parcel.my_wallet') }}</span>
        </a>
        <a href="{{ route('merchant.accounts.account-transaction.index') }}" class="gc-navlink {{ request()->routeIs('merchant.accounts.account-transaction.*') ? 'active' : '' }}">
            <span class="gc-navicon"><i class="fas fa-exchange-alt"></i></span>
            <span class="gc-navlabel">{{ __('menus.account_transaction') }}</span>
        </a>
        <a href="{{ route('merchant.accounts.statements.index') }}" class="gc-navlink {{ request()->routeIs('merchant.accounts.statements.*') ? 'active' : '' }}">
            <span class="gc-navicon"><i class="fas fa-receipt"></i></span>
            <span class="gc-navlabel">{{ __('menus.statements') }}</span>
        </a>
    </div>

    {{-- Autres --}}
    <div class="gc-section-title">{{ __('sidebar.others') }}</div>
    <div class="gc-nav">
        <a href="{{ route('merchant-panel.support.index') }}" class="gc-navlink {{ request()->routeIs('merchant-panel.support.*') ? 'active' : '' }}">
            <span class="gc-navicon"><i class="fas fa-headset"></i></span>
            <span class="gc-navlabel">{{ __('menus.support') }}</span>
        </a>
        <a href="{{ route('merchant-panel.news-offer.index') }}" class="gc-navlink {{ request()->routeIs('merchant-panel.news-offer.*') ? 'active' : '' }}">
            <span class="gc-navicon"><i class="fas fa-newspaper"></i></span>
            <span class="gc-navlabel">{{ __('sidebar.news') }}</span>
            @if (notifications())
                <span class="gc-dot"></span>
            @endif
        </a>
        <a href="{{ route('merchant-panel.news-offer.index') }}" class="gc-navlink">
            <span class="gc-navicon"><i class="fas fa-star"></i></span>
            <span class="gc-navlabel">{{ __('news_offer.title') }}</span>
        </a>
    </div>

    <div class="gc-sidebar-footer">
        © {{ date('Y') }} {{ settings()->name }}
    </div>
</aside>

{{-- Backdrop mobile --}}
<div class="fixed inset-0 bg-black/40 z-[1049] hidden" id="wcBackdrop" onclick="document.body.classList.remove('wc-drawer-open');this.classList.add('hidden')"></div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var body = document.body;
    var sidebar = document.getElementById('gcSidebar');
    if (!sidebar) return;

    sidebar.querySelectorAll('.gc-navlink').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth <= 700) {
                body.classList.remove('wc-drawer-open');
                var backdrop = document.getElementById('wcBackdrop');
                if (backdrop) backdrop.classList.add('hidden');
            }
        });
    });
});
</script>
