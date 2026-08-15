@extends('backend.partials.master')
@section('title')
    {{ __('parcel.title') }} {{ __('levels.list') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">
    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">{{ __('parcel.title') }}</h1>
            <p class="wc-page-subtitle">
                {{ $parcels->total() }} colis au total
                @if ($request->parcel_date || $request->parcel_status || $request->parcel_customer || $request->parcel_customer_phone || $request->invoice_id)
                    · filtre actif
                @endif
            </p>
        </div>
        <div class="wc-toolbar">
            <form action="{{ route('parcel.multiple.print-label') }}" method="get" target="_blank" id="print_label_form" class="d-inline">
                @csrf
                <div id="print_label_content"></div>
                <button type="submit" class="wc-btn wc-btn-outline wc-btn-sm multiplelabelprint" data-parcels='' style="display: none">
                    <i class="fas fa-print"></i> {{ __('levels.print_label') }}
                </button>
            </form>
            <a href="{{ route('merchant-panel.parcel.parcel-import') }}" class="wc-btn wc-btn-soft wc-btn-sm">
                <i class="fas fa-file-import"></i> {{ __('parcel.import_parcel') }}
            </a>
            <div class="dropdown">
                <button type="button" class="wc-btn wc-btn-outline wc-btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-download"></i> {{ __('levels.export') }} <i class="fas fa-chevron-down text-[10px]"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="{{ route('merchant-panel.parcel.file-export', ['parcel_date' => $request->parcel_date, 'parcel_status' => $request->parcel_status, 'parcel_customer' => $request->parcel_customer, 'parcel_customer_phone' => $request->parcel_customer_phone]) }}">
                        <i class="fas fa-file-excel"></i> {{ __('parcel.export_xlsx') }}
                    </a>
                    <a class="dropdown-item" href="{{ route('merchant-panel.parcel.file-export', ['parcel_date' => $request->parcel_date, 'parcel_status' => $request->parcel_status, 'parcel_customer' => $request->parcel_customer, 'parcel_customer_phone' => $request->parcel_customer_phone, 'type' => 'csv']) }}">
                        <i class="fas fa-file-csv"></i> {{ __('parcel.export_csv') }}
                    </a>
                </div>
            </div>
            <a href="{{ route('merchant-panel.parcel.create') }}" class="wc-btn wc-btn-primary wc-btn-sm">
                <i class="fas fa-plus"></i> {{ __('levels.add') }}
            </a>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="wc-filter">
        <form action="{{ route('merchant-panel.parcel.filter') }}" method="GET" class="m-0">
            @csrf
            <div class="wc-filter-grid">
                <div class="form-group m-0">
                    <label class="wc-label" for="date">{{ __('parcel.date') }}</label>
                    <input type="text" autocomplete="off" id="date" name="parcel_date" class="form-control date_range_picker" value="{{ old('parcel_date', $request->parcel_date) }}" placeholder="{{ __('merchantPlaceholder.date') }}">
                    @error('parcel_date')
                    <small class="text-danger mt-1 d-block">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group m-0">
                    <label class="wc-label" for="parcelStatus">{{ __('parcel.status') }}</label>
                    <select id="parcelStatus" name="parcel_status" class="form-control @error('parcel_status') is-invalid @enderror">
                        <option value="" selected>{{ __('menus.select') }} {{ __('parcel.status') }}</option>
                        @foreach (trans('merchantParcelStatusFilter') as $key => $status)
                            <option value="{{ $key }}" {{ (old('parcel_status', $request->parcel_status) == $key) ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                    @error('parcel_status')
                    <small class="text-danger mt-1 d-block">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group m-0">
                    <label class="wc-label" for="parcel_customer">{{ __('parcel.customer_name') }}</label>
                    <input id="parcel_customer" type="text" name="parcel_customer" placeholder="{{ __('parcel.customer_name') }}" autocomplete="off" class="form-control" value="{{ old('parcel_customer', $request->parcel_customer) }}">
                    @error('parcel_customer')
                    <small class="text-danger mt-1 d-block">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group m-0">
                    <label class="wc-label" for="parcel_customer_phone">{{ __('parcel.customer_phone') }}</label>
                    <input id="parcel_customer_phone" type="text" name="parcel_customer_phone" placeholder="{{ __('parcel.customer_phone') }}" autocomplete="off" class="form-control" value="{{ old('parcel_customer_phone', $request->parcel_customer_phone) }}">
                    @error('parcel_customer_phone')
                    <small class="text-danger mt-1 d-block">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group m-0">
                    <label class="wc-label" for="invoice_id">{{ __('parcel.invoice_id') }}</label>
                    <input id="invoice_id" type="text" name="invoice_id" placeholder="{{ __('parcel.invoice_id') }}" autocomplete="off" class="form-control" value="{{ old('invoice_id', $request->invoice_id) }}">
                    @error('invoice_id')
                    <small class="text-danger mt-1 d-block">{{ $message }}</small>
                    @enderror
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="wc-btn wc-btn-primary">
                        <i class="fas fa-filter text-[12px]"></i> {{ __('levels.filter') }}
                    </button>
                    <a href="{{ route('merchant-panel.parcel.index') }}" class="wc-btn wc-btn-outline">
                        <i class="fas fa-eraser text-[12px]"></i> {{ __('levels.clear') }}
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="wc-card overflow-hidden">
        <div class="wc-card-header justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                    <i class="fas fa-box"></i>
                </div>
                <div>
                    <h3 class="wc-card-title">{{ __('parcel.title') }}</h3>
                    <p class="text-[12px] text-wc-muted m-0">
                        {{ __('Showing') }} {{ $parcels->firstItem() ?? 0 }} {{ __('to') }} {{ $parcels->lastItem() ?? 0 }} {{ __('of') }} {{ $parcels->total() }} {{ __('results') }}
                    </p>
                </div>
            </div>
        </div>

        @if ($parcels->count() === 0)
            <div class="wc-empty">
                <div class="wc-empty-icon">
                    <i class="fas fa-box-open"></i>
                </div>
                <h2 class="text-[19px] font-extrabold text-wc-ink m-0 mb-2">Aucun colis trouvé</h2>
                <p class="text-[14px] text-wc-muted mb-6 max-w-md mx-auto">
                    Aucun colis ne correspond à votre recherche. Modifiez vos filtres ou enregistrez un nouveau colis.
                </p>
                <a href="{{ route('merchant-panel.parcel.create') }}" class="wc-btn wc-btn-primary inline-flex">
                    <i class="fas fa-plus text-[13px]"></i> {{ __('levels.add') }}
                </a>
            </div>
        @else
            <div class="wc-table-wrap">
                <table id="table" class="wc-table" style="width:100%">
                    <thead>
                        <tr>
                            <th class="permission-check-box">
                                <input type="checkbox" id="tick-all" class="wc-checkbox form-check-input"/>
                            </th>
                            <th>{{ __('###') }}</th>
                            <th>{{ __('parcel.tracking_id') }}</th>
                            <th>{{ __('parcel.recipient_info') }}</th>
                            <th>{{ __('parcel.amount') }}</th>
                            <th>{{ __('parcel.status') }}</th>
                            <th>{{ __('parcel.payment') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i = 1; @endphp
                        @foreach ($parcels as $parcel)
                            <tr>
                                <td class="permission-check-box wc-td-control wc-td-check" data-label="">
                                    <input type="checkbox" name="parcels[][{{ $parcel->id }}]" value="{{ $parcel->id }}" class="common-key wc-checkbox form-check-input" />
                                </td>
                                <td class="wc-td-control wc-td-actions" data-label="">
                                    <div class="dropdown">
                                        <button tabindex="-1" data-bs-toggle="dropdown" type="button" class="wc-btn wc-btn-ghost wc-btn-sm !px-2.5" aria-label="Actions">
                                            <i class="fas fa-ellipsis-v text-[14px]"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="{{ route('merchant-panel.parcel.details', $parcel->id) }}" class="dropdown-item">
                                                <i class="fa fa-eye" aria-hidden="true"></i> {{ __('levels.view') }}
                                            </a>
                                            <a href="{{ route('merchant-panel.parcel.logs', $parcel->id) }}" class="dropdown-item">
                                                <i class="fas fa-history" aria-hidden="true"></i> {{ __('levels.parcel_logs') }}
                                            </a>
                                            <a href="{{ route('merchant-parcel.clone', $parcel->id) }}" class="dropdown-item">
                                                <i class="fas fa-clone" aria-hidden="true"></i> {{ __('levels.clone') }}
                                            </a>
                                            @if (\App\Enums\ParcelStatus::DELIVERED !== $parcel->status)
                                                @if ($parcel->status == App\Enums\ParcelStatus::PENDING)
                                                    <div class="dropdown-divider"></div>
                                                    <a href="{{ route('merchant-panel.parcel.edit', $parcel->id) }}" class="dropdown-item">
                                                        <i class="fas fa-edit" aria-hidden="true"></i> {{ __('levels.edit') }}
                                                    </a>
                                                    <form id="delete" value="Test" action="{{ route('merchant-panel.parcel.delete', $parcel->id) }}" method="POST" data-title="{{ __('delete.parcel') }}">
                                                        @method('DELETE')
                                                        @csrf
                                                        <input type="hidden" name="" value="Parcel" id="deleteTitle">
                                                        <button type="submit" class="dropdown-item text-[#b91c1c] hover:bg-[#fef2f2] hover:text-[#b91c1c]">
                                                            <i class="fa fa-trash" aria-hidden="true"></i> {{ __('levels.delete') }}
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Référence" class="wc-td-tracking">
                                    <a href="{{ route('merchant-panel.parcel.details', $parcel->id) }}" class="font-bold text-wc-primary-dark wc-tabular text-[13.5px] no-underline hover:text-wc-primary">
                                        {{ $parcel->tracking_id }}
                                    </a>
                                </td>
                                <td data-label="Destinataire">
                                    <div class="flex items-start gap-2.5">
                                        <div class="wc-avatar !w-8 !h-8 !text-[12px] !rounded-[9px] mt-0.5">
                                            {{ strtoupper(substr($parcel->customer_name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-semibold text-wc-ink text-[13.5px] truncate max-w-[180px]">{{ $parcel->customer_name }}</div>
                                            <div class="flex items-center gap-1.5 text-[12px] text-wc-muted-2 mt-0.5">
                                                <i class="fas fa-phone text-[9px]"></i>
                                                <span class="wc-tabular">{{ $parcel->customer_phone }}</span>
                                            </div>
                                            <div class="flex items-start gap-1.5 text-[12px] text-wc-muted-2 mt-0.5">
                                                <i class="fas fa-map-marker-alt text-[9px] mt-1"></i>
                                                <span class="line-clamp-2">{{ $parcel->customer_address }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Montant">
                                    <div class="text-[13px] leading-relaxed">
                                        <div class="flex justify-between gap-4">
                                            <span class="text-wc-muted">{{ __('levels.cod') }} :</span>
                                            <span class="font-bold text-wc-ink wc-tabular">{{ settings()->currency }} {{ number_format($parcel->cash_collection, 2) }}</span>
                                        </div>
                                        @if ($parcel->return_to_courier == App\Enums\BooleanStatus::YES)
                                            <div class="flex justify-between gap-4">
                                                <span class="text-wc-muted">{{ __('levels.return_charges') }} :</span>
                                                <span class="font-semibold text-wc-ink wc-tabular">{{ settings()->currency }} {{ number_format($parcel->return_charges, 2) }}</span>
                                            </div>
                                        @else
                                            <div class="flex justify-between gap-4">
                                                <span class="text-wc-muted">{{ __('levels.total_delivery_amount') }} :</span>
                                                <span class="font-semibold text-wc-ink wc-tabular">{{ settings()->currency }} {{ number_format($parcel->total_delivery_amount, 2) }}</span>
                                            </div>
                                            <div class="flex justify-between gap-4">
                                                <span class="text-wc-muted">{{ __('levels.vat_amount') }} :</span>
                                                <span class="font-semibold text-wc-ink wc-tabular">{{ settings()->currency }} {{ number_format($parcel->vat_amount, 2) }}</span>
                                            </div>
                                        @endif
                                        <div class="flex justify-between gap-4 pt-1 mt-1 border-t border-dashed border-wc-border">
                                            <span class="text-wc-muted">{{ __('levels.current_payable') }} :</span>
                                            <b class="text-wc-primary-dark wc-tabular">{{ settings()->currency }} {{ number_format($parcel->current_payable, 2) }}</b>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Statut">
                                    <div>{!! $parcel->parcel_status !!}</div>
                                    <div class="text-[11.5px] text-wc-muted-2 mt-1.5">{{ __('parcel.updated_on') }}: {{ \Carbon\Carbon::parse($parcel->updated_at)->format('d/m/Y H:i') }}</div>
                                </td>
                                <td data-label="Paiement">
                                    @if ($parcel->invoice)
                                        <div class="flex items-center gap-2 flex-wrap">
                                            @if ($parcel->invoice->status == App\Enums\InvoiceStatus::PAID)
                                                <span class="wc-badge wc-badge-delivered">{{ __('invoice.' . @$parcel->invoice->status) }}</span>
                                            @else
                                                <span class="wc-badge wc-badge-pending">{{ __('invoice.' . @$parcel->invoice->status) }}</span>
                                            @endif
                                            <span class="text-[12.5px] font-bold text-wc-ink wc-tabular">{{ @$parcel->invoice->invoice_id }}</span>
                                        </div>
                                        @if ($parcel->invoice->status == App\Enums\InvoiceStatus::PAID)
                                            <div class="text-[11.5px] text-wc-muted-2 mt-1.5">
                                                Paid At: {{ dateFormat(@$parcel->invoice->updated_at) }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="wc-badge wc-badge-neutral">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-4 py-4 border-t border-wc-border flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <p class="m-0 text-[12.5px] text-wc-muted">
                    {{ __('Showing') }}
                    <span class="font-bold text-wc-ink">{{ $parcels->firstItem() }}</span>
                    {{ __('to') }}
                    <span class="font-bold text-wc-ink">{{ $parcels->lastItem() }}</span>
                    {{ __('of') }}
                    <span class="font-bold text-wc-ink">{{ $parcels->total() }}</span>
                    {{ __('results') }}
                </p>
                <div class="flex justify-end">
                    {{ $parcels->appends($request->all())->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="{{ static_asset('backend/js/date-range-picker/date-range-picker-custom.js') }}"></script>
    <script>
        var dateParcel = '{{ $request->parcel_date }}';
    </script>
    <script src="{{ static_asset('backend/js/merchant_panel/parcel/filter.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            // impression d'étiquettes multiples
            $('#tick-all').on('change', function () {
                if (!$(this).is(':checked')) {
                    $('td').closest('tr').find('.common-key').prop('checked', false);
                } else {
                    $('td').closest('tr').find('.common-key').prop('checked', true);
                }
                showPrintBtn();
            });

            $('.common-key').on('click', function () {
                showPrintBtn();
            });

            function showPrintBtn() {
                if ($('.common-key:checked').length > 0) {
                    $('.multiplelabelprint').show();
                    var inputs = '';
                    $('.common-key:checked').each(function () {
                        inputs += '<input type="hidden" name="parcels[]" value="' + $(this).val() + '"/>';
                    });
                    $('#print_label_content').html(inputs);
                } else {
                    $('.multiplelabelprint').hide();
                    $('#tick-all').prop('checked', false);
                    $('#print_label_content').html('');
                }
            }
        });
    </script>
@endpush