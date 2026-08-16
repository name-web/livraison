@extends('backend.partials.master')
@section('title')
    {{ __('menus.statements') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">{{ __('menus.statements') }}</h1>
            <p class="wc-page-subtitle">{{ __('statements.details') }} · historique des mouvements</p>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="wc-filter">
        <form action="{{route('merchant.accounts.statements.filter')}}" method="POST" class="m-0">
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
                        <option value="{{ \App\Enums\AccountHeads::INCOME }}" {{ (isset($request->type) ? $request->type : old('type')) == \App\Enums\AccountHeads::INCOME ? 'selected' : '' }}>{{ __('AccountHeads.'.\App\Enums\AccountHeads::INCOME)}}</option>
                        <option value="{{ \App\Enums\AccountHeads::EXPENSE }}" {{ (isset($request->type) ? $request->type : old('type')) == \App\Enums\AccountHeads::EXPENSE ? 'selected' : '' }}>{{ __('AccountHeads.'.\App\Enums\AccountHeads::EXPENSE)}}</option>
                    </select>
                    @error('type')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                </div>
                <div class="wc-form-group m-0">
                    <label class="wc-label" for="parcel_tracking_id">{{ __('levels.track_id')}}</label>
                    <input id="parcel_tracking_id" type="text" name="parcel_tracking_id" placeholder="{{ __('merchantPlaceholder.tracking_id') }}" class="wc-input" value="{{ old('parcel_tracking_id',isset($request->parcel_tracking_id) ? $request->parcel_tracking_id:'') }}">
                    @error('parcel_tracking_id')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="wc-btn wc-btn-primary"><i class="fa fa-filter text-[12px]"></i> {{ __('levels.filter') }}</button>
                    <a href="{{ route('merchant.accounts.statements.index') }}" class="wc-btn wc-btn-outline"><i class="fa fa-eraser text-[12px]"></i> {{ __('levels.clear') }}</a>
                </div>
            </div>
        </form>
    </div>

    <div class="wc-card">
        <div class="wc-card-header">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <h3 class="wc-card-title">{{ __('menus.statements') }}</h3>
                    <p class="text-[12px] text-wc-muted m-0">Revenus et dépenses de votre compte.</p>
                </div>
            </div>
        </div>

        @if(count($statements) === 0)
            <div class="wc-empty">
                <div class="wc-empty-icon"><i class="fas fa-receipt"></i></div>
                <p class="wc-empty-title">Aucun mouvement</p>
            </div>
        @else
            <div class="wc-table-wrap">
                <table class="wc-table">
                    <thead>
                        <tr>
                            <th>{{ __('levels.id') }}</th>
                            <th>{{ __('statements.details') }}</th>
                            <th>{{ __('statements.date') }}</th>
                            <th>{{ __('levels.type') }}</th>
                            <th class="text-right">{{ __('statements.amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i=1; @endphp
                        @foreach($statements as $statement)
                        <tr>
                            <td class="text-wc-muted-2 wc-tabular">{{$i++}}</td>
                            <td class="text-[13px] text-wc-ink">{{ $statement->note }}</td>
                            <td class="text-wc-muted-2 whitespace-nowrap">{{ dateFormat($statement->date) }}</td>
                            <td>
                                @if($statement->type == \App\Enums\AccountHeads::INCOME)
                                <span class="wc-badge wc-badge-success">{{trans('AccountHeads.'.\App\Enums\AccountHeads::INCOME) }}</span>
                                @elseif($statement->type == \App\Enums\AccountHeads::EXPENSE)
                                <span class="wc-badge wc-badge-danger">{{trans('AccountHeads.'.\App\Enums\AccountHeads::EXPENSE) }}</span>
                                @endif
                            </td>
                            <td class="text-right font-bold wc-tabular @if($statement->type == \App\Enums\AccountHeads::INCOME) text-wc-success @else text-wc-danger @endif">
                                {{ $statement->type == \App\Enums\AccountHeads::INCOME ? '+' : '-' }} {{ formatPrice($statement->amount) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between gap-3 flex-wrap px-4 py-3 border-t border-wc-border">
                <p class="m-0 text-[12.5px] text-wc-muted">
                    {!! __('Showing') !!} <span class="font-bold text-wc-ink">{{ $statements->firstItem() }}</span>
                    {!! __('to') !!} <span class="font-bold text-wc-ink">{{ $statements->lastItem() }}</span>
                    {!! __('of') !!} <span class="font-bold text-wc-ink">{{ $statements->total() }}</span> {!! __('results') !!}
                </p>
                <span class="flex items-center gap-1">{{ $statements->links() }}</span>
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