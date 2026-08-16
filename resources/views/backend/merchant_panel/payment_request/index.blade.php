@extends('backend.partials.master')
@section('title')
    {{ __('paymentrequest.title') }} {{ __('levels.list') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">{{ __('paymentrequest.title') }}</h1>
            <p class="wc-page-subtitle">{{ $payments->total() }} {{ __('levels.list') }} · FCFA</p>
        </div>
        <div class="wc-toolbar">
            <a href="{{ route('merchant-panel.payment-request.create') }}" class="wc-btn wc-btn-primary wc-btn-sm">
                <i class="fas fa-plus"></i> {{ __('levels.add') }}
            </a>
        </div>
    </div>

    {{-- Liste des demandes de paiement --}}
    <div class="wc-card">
        <div class="wc-card-header">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div>
                    <h3 class="wc-card-title">{{ __('paymentrequest.title') }} {{ __('levels.list') }}</h3>
                    <p class="text-[12px] text-wc-muted m-0">
                        {{ __('Showing') }} {{ $payments->firstItem() ?? 0 }} {{ __('to') }} {{ $payments->lastItem() ?? 0 }} {{ __('of') }} {{ $payments->total() }} {{ __('results') }}
                    </p>
                </div>
            </div>
        </div>

        @if(count($payments) === 0)
            <div class="wc-empty">
                <div class="wc-empty-icon"><i class="fas fa-credit-card"></i></div>
                <p class="wc-empty-title">{{ __('paymentrequest.title') }}</p>
                <p class="wc-empty-description">Aucune demande de paiement pour le moment.</p>
                <a href="{{ route('merchant-panel.payment-request.create') }}" class="wc-btn wc-btn-primary wc-btn-sm mt-3">
                    <i class="fas fa-plus"></i> {{ __('levels.add') }}
                </a>
            </div>
        @else
            <div class="wc-table-wrap">
                <table class="wc-table">
                    <thead>
                        <tr>
                            <th>{{ __('levels.id') }}</th>
                            <th>{{ __('paymentrequest.account_details') }}</th>
                            <th>{{ __('merchantmanage.transaction_id') }}</th>
                            <th>{{ __('merchantmanage.description') }}</th>
                            <th>{{ __('paymentrequest.request_date') }}</th>
                            <th>{{ __('levels.status') }}</th>
                            <th class="text-right">{{ __('merchantmanage.amount') }}</th>
                            <th class="text-right">{{ __('levels.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i=1; @endphp
                        @foreach($payments as $payment)
                        <tr>
                            <td class="text-wc-muted-2 wc-tabular">{{$i++}}</td>
                            <td>
                                @if ($payment->merchantAccount->payment_method == 'bank')
                                    <div class="font-bold text-wc-ink text-[13px]">{{ $payment->merchantAccount->holder_name }}</div>
                                    <div class="text-[12px] text-wc-muted">{{ optional($payment->merchantAccount->mobileBank)->name }} · {{ $payment->merchantAccount->account_no }}</div>
                                    <div class="text-[11.5px] text-wc-muted-2">{{ $payment->merchantAccount->branch_name }} · {{ $payment->merchantAccount->routing_no }}</div>
                                @elseif ($payment->merchantAccount->payment_method == 'mobile')
                                    <div class="font-bold text-wc-ink text-[13px]">{{ optional($payment->merchantAccount->mobileBank)->name }}</div>
                                    <div class="text-[12px] text-wc-muted">{{ $payment->merchantAccount->mobile_no }} · {{ $payment->merchantAccount->account_type }}</div>
                                @endif
                            </td>
                            <td class="wc-tabular">{{$payment->transaction_id}}</td>
                            <td class="text-wc-muted">{{$payment->description}}</td>
                            <td class="text-wc-muted-2 whitespace-nowrap">{{ date('d M Y H:i:s a',strtotime($payment->created_at)) }}</td>
                            <td>
                                @if($payment->status == \App\Enums\ApprovalStatus::REJECT)
                                <span class="wc-badge wc-badge-error">{{trans('approvalstatus.'.\App\Enums\ApprovalStatus::REJECT) }}</span>
                                @elseif($payment->status == \App\Enums\ApprovalStatus::PENDING)
                                <span class="wc-badge wc-badge-warning">{{trans('approvalstatus.'.\App\Enums\ApprovalStatus::PENDING) }}</span>
                                @elseif($payment->status == \App\Enums\ApprovalStatus::PROCESSED)
                                <span class="wc-badge wc-badge-success">{{trans('approvalstatus.'.\App\Enums\ApprovalStatus::PROCESSED) }}</span>
                                @endif
                            </td>
                            <td class="wc-tabular font-bold text-wc-ink text-right">{{ formatPrice($payment->amount) }}</td>
                            <td>
                                <div class="flex items-center justify-end gap-2">
                                    @if($payment->created_by == \App\Enums\UserType::MERCHANT && $payment->status == \App\Enums\ApprovalStatus::PENDING)
                                        <div class="dropdown">
                                            <button tabindex="-1" data-toggle="dropdown" type="button" class="wc-btn wc-btn-outline wc-btn-sm dropdown-toggle dropdown-toggle-split"><i class="fas fa-ellipsis-v"></i></button>
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
                                    ...
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-3 flex-wrap px-4 py-3 border-t border-wc-border">
                <p class="m-0 text-[12.5px] text-wc-muted">
                    {!! __('Showing') !!}
                    <span class="font-bold text-wc-ink">{{ $payments->firstItem() }}</span>
                    {!! __('to') !!}
                    <span class="font-bold text-wc-ink">{{ $payments->lastItem() }}</span>
                    {!! __('of') !!}
                    <span class="font-bold text-wc-ink">{{ $payments->total() }}</span>
                    {!! __('results') !!}
                </p>
                <span class="flex items-center gap-1">{{ $payments->links() }}</span>
            </div>
        @endif
    </div>
</div>
@endsection()