@extends('backend.partials.master')
@section('title')
    {{ __('merchant.payment_accounts') }} {{ __('levels.list') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">{{ __('merchant.payment_accounts') }}</h1>
            <p class="wc-page-subtitle">{{ __('merchant.create_account') }} · comptes bancaires et mobiles</p>
        </div>
        <div class="wc-toolbar">
            <a href="{{ route('payment.account.create') }}" class="wc-btn wc-btn-primary wc-btn-sm" data-toggle="tooltip" data-placement="top" title="{{ __('levels.add') }}"><i class="fa fa-plus"></i> {{ __('levels.add') }}</a>
        </div>
    </div>

    <div class="wc-card">
        <div class="wc-card-header">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                    <i class="fas fa-wallet"></i>
                </div>
                <div>
                    <h3 class="wc-card-title">{{ __('merchant.payment_accounts') }}</h3>
                    <p class="text-[12px] text-wc-muted m-0">Vos moyens de paiement enregistrés.</p>
                </div>
            </div>
        </div>

        @if(count($accounts) === 0)
            <div class="wc-empty">
                <div class="wc-empty-icon"><i class="fas fa-wallet"></i></div>
                <p class="wc-empty-title">Aucun compte de paiement</p>
                <p class="wc-empty-description">Ajoutez un compte bancaire ou mobile pour recevoir vos paiements.</p>
            </div>
        @else
            <div class="wc-table-wrap">
                <table class="wc-table">
                    <thead>
                        <tr>
                            <th>{{ __('levels.id') }}</th>
                            <th>{{ __('merchant.payment_method') }}</th>
                            <th>{{ __('merchant.account_info') }}</th>
                            <th class="text-right">{{ __('levels.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i=1; @endphp
                        @foreach($accounts as $account)
                            <tr>
                                <td class="text-wc-muted-2 wc-tabular">{{$i++}}</td>
                                <td>
                                    @if ($account->payment_method == \App\Enums\Merchant_panel\PaymentMethod::bank)
                                        <span class="wc-badge wc-badge-info-soft"><i class="fas fa-university text-[10px]"></i> {{__('merchant.bank')}}</span>
                                    @elseif ($account->payment_method == \App\Enums\Merchant_panel\PaymentMethod::mobile)
                                        <span class="wc-badge wc-badge-success-soft"><i class="fas fa-mobile-alt text-[10px]"></i> {{__('merchant.mobile')}}</span>
                                    @else
                                        <span class="wc-badge wc-badge-neutral"><i class="fas fa-money-bill text-[10px]"></i> {{__('merchant.cash')}}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($account->payment_method == \App\Enums\Merchant_panel\PaymentMethod::bank)
                                        <div class="text-[12.5px] leading-relaxed text-wc-ink-2">
                                            <span class="font-bold text-wc-ink">{{ $account->holder_name }}</span> · {{ optional($account->bank)->name }}<br/>
                                            {{ __('merchant.account_no') }} {{ $account->account_no }}<br/>
                                            {{ $account->branch_name }} · RIB {{ $account->routing_no }}
                                        </div>
                                    @elseif ($account->payment_method == \App\Enums\Merchant_panel\PaymentMethod::mobile)
                                        <div class="text-[12.5px] leading-relaxed text-wc-ink-2">
                                            <span class="font-bold text-wc-ink">{{ optional($account->mobileBank)->name }}</span><br/>
                                            {{ $account->mobile_no }} · {{ $account->account_type }}
                                        </div>
                                    @else
                                        <span class="text-[12.5px] text-wc-ink-2">{{__('merchant.cash')}}</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{route('payment.account.edit',$account->id)}}" class="wc-btn wc-btn-outline wc-btn-sm" data-toggle="tooltip" data-placement="top" title="{{ __('levels.edit') }}"><i class="fas fa-edit"></i> {{ __('levels.edit') }}</a>
                                        <form id="delete" value="Test" action="{{route('payment.account.delete',$account->id)}}" method="POST" data-title="{{ __('delete.payment_account') }}" class="m-0">
                                            @method('DELETE')
                                            @csrf
                                            <input type="hidden" name="" value="Account" id="deleteTitle">
                                            <button type="submit" class="wc-btn wc-btn-danger-soft wc-btn-sm" data-toggle="tooltip" data-placement="top" title="{{ __('levels.delete') }}"><i class="fa fa-trash"></i> {{ __('levels.delete') }}</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-3 flex-wrap px-4 py-3 border-t border-wc-border">
                <p class="m-0 text-[12.5px] text-wc-muted">
                    {!! __('Showing') !!} <span class="font-bold text-wc-ink">{{ $accounts->firstItem() }}</span>
                    {!! __('to') !!} <span class="font-bold text-wc-ink">{{ $accounts->lastItem() }}</span>
                    {!! __('of') !!} <span class="font-bold text-wc-ink">{{ $accounts->total() }}</span> {!! __('results') !!}
                </p>
                <span class="flex items-center gap-1">{{ $accounts->links() }}</span>
            </div>
        @endif
    </div>
</div>
@endsection()