@extends('backend.partials.master')
@section('title')
    {{ __('reports.title') }} {{ __('reports.parcel_reports') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">{{ __('reports.parcel_reports') }}</h1>
            <p class="wc-page-subtitle">{{ __('reports.title') }} · statistiques par statut</p>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="wc-filter">
        <form action="{{route('merchant-panel.parcel.filter.reports')}}" method="GET" class="m-0">
            @csrf
            <div class="wc-filter-grid">
                <div class="wc-form-group m-0">
                    <label class="wc-label" for="parcel_date">{{ __('parcel.date') }}</label>
                    <input type="text" autocomplete="off" id="date" name="parcel_date" class="wc-input date_range_picker" value="{{ old('parcel_date',$request->parcel_date) }}">
                    @error('parcel_date')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                </div>
                <div class="wc-form-group m-0">
                    <label class="wc-label" for="parcelStatus">{{ __('parcel.status') }}</label>
                    <select id="parcelStatus" name="parcel_status[]" class="wc-select @error('parcel_status') is-invalid @enderror" multiple="multiple">
                        @foreach (trans('parcelStatusFilter') as $key => $status)
                            <option value="{{ $key }}" @if($request->parcel_status !== null && in_array($key,$request->parcel_status)) selected @endif>{{ $status }}</option>
                        @endforeach
                    </select>
                    @error('parcel_status')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="wc-btn wc-btn-primary"><i class="fa fa-filter text-[12px]"></i> {{ __('levels.filter') }}</button>
                    <a href="{{ route('merchant-panel.parcel.reports') }}" class="wc-btn wc-btn-outline"><i class="fa fa-eraser text-[12px]"></i> {{ __('levels.clear') }}</a>
                </div>
            </div>
        </form>
    </div>

    @if(isset($print))
    <div class="wc-card">
        <div class="wc-card-header">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div>
                    <h3 class="wc-card-title">{{ __('reports.parcel_reports') }}</h3>
                    <p class="text-[12px] text-wc-muted m-0">Répartition des colis par statut.</p>
                </div>
            </div>
            @if(!blank($parcel_ids))
            <div class="flex items-center gap-2">
                <button type="button" id="exportTable" data-title="Parcel Status Reports" data-filename="ParcelStatusReports" class="wc-btn wc-btn-soft wc-btn-sm"><i class="fas fa-file-excel"></i> {{ __('menus.export') }}</button>
                <a href="{{ route('merchant-panel.parcel.reports.print.page',$parcel_ids) }}" class="wc-btn wc-btn-primary wc-btn-sm" target="_blank"><i class="fas fa-print"></i> {{ __('reports.print') }}</a>
            </div>
            @endif
        </div>
        <div class="wc-table-wrap">
            <table class="wc-table">
                <thead>
                    <tr>
                        <th>{{ __('###') }}</th>
                        <th>{{ __('parcel.status') }}</th>
                        <th class="text-right">{{ __('reports.count') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i=1; @endphp
                    @foreach($parcels as $key=>$parcel)
                        <tr>
                            <td class="text-wc-muted-2 wc-tabular">{{ $i++ }}</td>
                            <td>{!! StatusParcel($key) !!}</td>
                            <td class="text-right font-bold text-wc-ink wc-tabular">{{ $parcel->count() }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td></td>
                        <td class="font-extrabold text-wc-ink">{{ __('reports.total_cash_collection') }}</td>
                        <td class="text-right font-extrabold text-wc-primary wc-tabular">{{ formatPrice(totalParcelsCashcollection($parcels)) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @else
        <div class="wc-card">
            <div class="wc-empty">
                <div class="wc-empty-icon"><i class="fas fa-chart-bar"></i></div>
                <p class="wc-empty-title">Aucun rapport généré</p>
                <p class="wc-empty-description">Appliquez un filtre de date ou de statut pour générer le rapport.</p>
            </div>
        </div>
    @endif
</div>
@endsection()

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="{{ static_asset('backend/js/date-range-picker/date-range-picker-custom.js') }}"></script>
    <script>
        var merchantUrl = '{{ route('parcel.merchant.get') }}';
        var merchantID = '{{ $request->parcel_merchant_id }}';
        var deliveryManID = '{{ $request->parcel_deliveryman_id }}';
        var pickupManID = '{{ $request->parcel_pickupman_id }}';
        var dateParcel = '{{ $request->parcel_date }}';
    </script>
    <script src="{{ static_asset('backend/js/parcel/filter.js') }}"></script>
    <script src="{{ static_asset('backend/js/reports/print.js') }}"></script>
    <script src="{{ static_asset('backend/js/reports/jquery.table2excel.min.js') }}"></script>
    <script src="{{ static_asset('backend/js/reports/reports.js') }}"></script>
@endpush