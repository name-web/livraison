@php
    $statusKey = (int) $payment->status;
    $account = optional($payment->merchantAccount);
    $accountName = $account->payment_method == 'bank'
        ? $account->holder_name
        : optional($account->mobileBank)->name;
    $initials = strtoupper(mb_substr(trim((string) $accountName), 0, 2));
    $canEdit = $payment->created_by == \App\Enums\UserType::MERCHANT && $statusKey == \App\Enums\ApprovalStatus::PENDING;
    $accountDetail = $account->payment_method == 'bank'
        ? ($account->branch_name.' · '.$account->routing_no)
        : ($account->mobile_no.' · '.$account->account_type);
@endphp
<div class="wc-pay-card animate-wcRowIn" data-col="{{ $col }}" data-status="{{ $col }}"
    data-search="{{ mb_strtolower(($payment->transaction_id ?? '').' '.($payment->description ?? '').' '.($accountName ?? '').' '.($account->account_no ?? '').' '.($account->mobile_no ?? '').' '.number_format((float) $payment->amount, 0, ',', '')) }}">
    <div class="wc-pay-card-top">
        <div class="flex items-center gap-2.5 min-w-0">
            <div class="wc-avatar {{ $col == 'rejected' ? '!bg-[#fef2f2] !text-[#dc2626]' : '!bg-[#ecfdf5] !text-[#059669]' }} flex-shrink-0">{{ $initials ?: '—' }}</div>
            <div class="min-w-0">
                <p class="wc-pay-card-title truncate">{{ $accountName ?? '—' }}</p>
                <p class="wc-pay-card-txn truncate">{{ $payment->transaction_id }}</p>
            </div>
        </div>
        <span class="wc-pay-card-amount">{{ formatPrice($payment->amount) }}</span>
    </div>
    @if($payment->description)
        <p class="wc-pay-card-desc" title="{{ $payment->description }}">{{ $payment->description }}</p>
    @endif
    <div class="wc-pay-card-meta">
        <span><i class="far fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($payment->created_at)->format('d M Y, H:i') }}</span>
        @if($accountDetail)
            <span class="sep">•</span>
            <span class="truncate">{{ $accountDetail }}</span>
        @endif
    </div>
    <div class="wc-pay-card-foot">
        <span class="wc-badge {{ $col == 'processed' ? 'wc-badge-success' : ($col == 'rejected' ? 'wc-badge-error' : 'wc-badge-warning') }}">
            <i class="fas fa-circle text-[6px] mr-1.5"></i>{{ trans('approvalstatus.'.$statusKey) }}
        </span>
        @if($canEdit)
            <div class="dropdown">
                <button tabindex="-1" data-toggle="dropdown" type="button" class="wc-btn wc-btn-outline wc-btn-sm dropdown-toggle dropdown-toggle-split" title="{{ __('levels.actions') }}"><i class="fas fa-ellipsis-v"></i></button>
                <div class="dropdown-menu dropdown-menu-end">
                    <a href="{{route('merchant-panel.payment-request.edit',$payment->id)}}" class="dropdown-item"><i class="fas fa-edit"></i> {{ __('levels.edit') }}</a>
                    <form id="delete" value="Test" action="{{route('merchant-panel.payment-request.delete',$payment->id)}}" method="POST" data-title="{{ __('delete.payment_request') }}">
                        @method('DELETE')
                        @csrf
                        <input type="hidden" name="" value="Payment" id="deleteTitle">
                        <button type="submit" class="dropdown-item text-[#b91c1c]"><i class="fa fa-trash"></i> {{ __('levels.delete') }}</button>
                    </form>
                </div>
            </div>
        @else
            <span class="text-[11px] text-wc-muted-2"><i class="fas fa-lock text-[9px] mr-1"></i> Lecture seule</span>
        @endif
    </div>
</div>