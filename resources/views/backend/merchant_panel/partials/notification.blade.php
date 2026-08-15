@php $notifList = notifications(); @endphp
@if(count($notifList) === 0)
    <div class="text-center py-12 px-6">
        <div class="w-14 h-14 rounded-full mx-auto mb-3 flex items-center justify-center text-[22px] bg-wc-bg text-wc-muted-2">
            <i class="fas fa-bell-slash"></i>
        </div>
        <p class="text-[13.5px] font-bold text-wc-ink m-0">Aucune notification</p>
        <p class="text-[12.5px] text-wc-muted-2 m-0 mt-1">Vous serez prévenu ici dès qu'une actualité arrive.</p>
    </div>
@else
    @foreach($notifList as $notify)
        <a href="@if ($notify['type'] === 'support') {{ route('merchant-panel.support.view', $notify['support_id']) }} @elseif($notify['type'] === 'newsoffer') {{ route('merchant-panel.news-offer.index') }} @endif"
           class="wc-notif-item">
            <div class="wc-notif-dot">
                @if ($notify['type'] === 'support')
                    <i class="fas fa-headset"></i>
                @elseif($notify['type'] === 'newsoffer')
                    <i class="fas fa-bullhorn"></i>
                @else
                    <i class="fas fa-bell"></i>
                @endif
            </div>
            <div class="min-w-0">
                <p class="wc-notif-subject">{{ $notify['subject'] }}</p>
                <div class="wc-notif-date">
                    {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notify['created_at'])->diffForHumans() }}
                </div>
            </div>
        </a>
    @endforeach
@endif