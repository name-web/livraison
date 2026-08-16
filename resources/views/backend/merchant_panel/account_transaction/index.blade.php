@extends('backend.partials.master')
@section('title')
    {{ __('menus.account_transaction') }} {{ __('levels.list') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">{{ __('menus.account_transaction') }}</h1>
            <p class="wc-page-subtitle">Historique des transactions de votre compte</p>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="wc-filter">
        <form action="{{route('merchant.accounts.account-transaction.filter')}}" method="POST" class="m-0">
            @csrf
            <div class="wc-filter-grid">
                <div class="wc-form-group m-0">
                    <label class="wc-label" for="date">{{ __('parcel.date') }}</label>
                    <input type="text" autocomplete="off" id="date" name="date" class="wc-input date_range_picker" value="{{ isset($request->date) ? $request->date : old('date') }}" placeholder="{{ __('merchantPlaceholder.date') }}">
                    @error('date')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                </div>
                <div class="wc-form-group m-0">
                    <label class="wc-label" for="type">{{ __('levels.type') }}</label>
                    <select name="type" class="wc-select @error('type') is-invalid @enderror">
                        <option value="" selected>{{ __('merchantPlaceholder.type') }}</option>
                        @foreach(\config('rxcourier.approval_status') as $key => $value)
                            <option value="{{ $value }}" {{ (isset($request->type) ? $request->type : old('type')) == $value ? 'selected' : '' }}>{{ __('Approvalstatus.'.$value)}}</option>
                        @endforeach
                    </select>
                    @error('type')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                </div>
                <div class="wc-form-group m-0">
                    <label class="wc-label" for="account">{{ __('levels.account') }}</label>
                    <select name="account" class="wc-select @error('account') is-invalid @enderror">
                        <option value="" selected>{{ __('merchantPlaceholder.account') }}</option>
                        @foreach ($accounts as $account)
                            @if ($account->payment_method == 'bank')
                                <option value="{{$account->id}}" {{ ((isset($request->account) ? $request->account : old('account')) == $account->id) ? 'selected' : '' }}>{{$account->branch_name}} ({{$account->account_no}})</option>
                            @elseif ($account->payment_method == 'mobile')
                                <option value="{{$account->id}}" {{ ((isset($request->account) ? $request->account : old('account')) == $account->id) ? 'selected' : '' }}>{{$account->mobile_company}} ({{$account->mobile_no}})</option>
                            @endif
                        @endforeach
                    </select>
                    @error('account')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="wc-btn wc-btn-primary"><i class="fa fa-filter text-[12px]"></i> {{ __('levels.filter') }}</button>
                    <a href="{{ route('merchant.accounts.account-transaction.index') }}" class="wc-btn wc-btn-outline"><i class="fa fa-eraser text-[12px]"></i> {{ __('levels.clear') }}</a>
                </div>
            </div>
        </form>
    </div>

    <div class="wc-card">
        <div class="wc-card-header">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div>
                    <h3 class="wc-card-title">{{ __('menus.account_transaction') }}</h3>
                    <p class="text-[12px] text-wc-muted m-0">Mouvements liés à vos comptes bancaires et mobiles.</p>
                </div>
            </div>
        </div>

        @if(count($transactions) === 0)
            <div class="wc-empty">
                <div class="wc-empty-icon"><i class="fas fa-exchange-alt"></i></div>
                <p class="wc-empty-title">Aucune transaction</p>
            </div>
        @else
            <div class="wc-table-wrap">
                <table class="wc-table">
                    <thead>
                        <tr>
                            <th>{{ __('levels.id') }}</th>
                            <th>{{ __('paymentrequest.account_details') }}</th>
                            <th>{{ __('merchantmanage.transaction_id') }}</th>
                            <th>{{ __('paymentrequest.request_date') }}</th>
                            <th>{{ __('levels.status') }}</th>
                            <th class="text-right">{{ __('merchantmanage.amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i=1; @endphp
                        @foreach($transactions as $transaction)
                        <tr>
                            <td class="text-wc-muted-2 wc-tabular">{{$i++}}</td>
                            <td>
                                @if ($transaction->merchantAccount->payment_method == 'bank')
                                    <div class="text-[12.5px] leading-relaxed text-wc-ink-2">
                                        <span class="font-bold text-wc-ink">{{ $transaction->merchantAccount->holder_name }}</span><br/>
                                        {{ optional($transaction->merchantAccount->bank)->name }} · {{ $transaction->merchantAccount->account_no }}<br/>
                                        {{ $transaction->merchantAccount->branch_name }} · RIB {{ $transaction->merchantAccount->routing_no }}
                                    </div>
                                @elseif ($transaction->merchantAccount->payment_method == 'mobile')
                                    <div class="text-[12.5px] leading-relaxed text-wc-ink-2">
                                        <span class="font-bold text-wc-ink">{{ optional($transaction->merchantAccount->mobileBank)->name }}</span><br/>
                                        {{ $transaction->merchantAccount->mobile_no }} · {{ $transaction->merchantAccount->account_type }}
                                    </div>
                                @endif
                            </td>
                            <td class="wc-tabular text-wc-ink font-bold text-[13px]">{{$transaction->transaction_id}}</td>
                            <td class="text-wc-muted-2 whitespace-nowrap">{{ date('d M Y H:i:s a',strtotime($transaction->created_at)) }}</td>
                            <td>
                                @if($transaction->status == \App\Enums\ApprovalStatus::REJECT)
                                <span class="wc-badge wc-badge-danger">{{trans('approvalstatus.'.\App\Enums\ApprovalStatus::REJECT) }}</span>
                                @elseif($transaction->status == \App\Enums\ApprovalStatus::PENDING)
                                <span class="wc-badge wc-badge-warning">{{trans('approvalstatus.'.\App\Enums\ApprovalStatus::PENDING) }}</span>
                                @elseif($transaction->status == \App\Enums\ApprovalStatus::PROCESSED)
                                <span class="wc-badge wc-badge-success">{{trans('approvalstatus.'.\App\Enums\ApprovalStatus::PROCESSED) }}</span>
                                @endif
                            </td>
                            <td class="text-right font-bold text-wc-ink wc-tabular">{{ formatPrice($transaction->amount) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-3 flex-wrap px-4 py-3 border-t border-wc-border">
                <p class="m-0 text-[12.5px] text-wc-muted">
                    {!! __('Showing') !!} <span class="font-bold text-wc-ink">{{ $transactions->firstItem() }}</span>
                    {!! __('to') !!} <span class="font-bold text-wc-ink">{{ $transactions->lastItem() }}</span>
                    {!! __('of') !!} <span class="font-bold text-wc-ink">{{ $transactions->total() }}</span> {!! __('results') !!}
                </p>
                <span class="flex items-center gap-1">{{ $transactions->links() }}</span>
            </div>
        @endif
    </div>
</div>
@endsection()

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="{{ static_asset('backend/js/date-range-picker/date-range-picker-custom.js') }}"></script>
@endpush