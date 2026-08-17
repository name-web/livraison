@extends('backend.partials.master')
@section('title')
    {{ __('parcel.title') }} {{ __('levels.list') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content gc-shell gc-page">
    <div class="gc-page-inner">
        {{-- Page header --}}
        <div class="gc-page-header">
            <div>
                <h1>{{ __('parcel.title') }}</h1>
                <p>
                    {{ $parcels->total() }} colis au total
                    @if ($request->parcel_date || $request->parcel_status || $request->parcel_customer || $request->parcel_customer_phone || $request->invoice_id)
                        · filtre actif
                    @endif
                </p>
            </div>
            <div class="gc-toolbar">
                <form action="{{ route('parcel.multiple.print-label') }}" method="get" target="_blank" id="print_label_form" class="d-inline">
                    @csrf
                    <div id="print_label_content"></div>
                </form>
                <a href="{{ route('merchant-panel.parcel.parcel-import') }}" class="gc-btn gc-btn-soft">
                    <i class="fas fa-file-import"></i> {{ __('parcel.import_parcel') }}
                </a>
                <div class="dropdown">
                    <button type="button" class="gc-btn gc-btn-soft" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-download"></i> {{ __('levels.export') }} <i class="fas fa-chevron-down" style="font-size:9px"></i>
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
                <a href="{{ route('merchant-panel.parcel.create') }}" class="gc-btn gc-btn-new">
                    <i class="fas fa-plus"></i> {{ __('levels.add') }}
                </a>
            </div>
        </div>

        {{-- Statuts rapides --}}
        <div class="gc-chips" id="gcChips">
            <a href="{{ route('merchant-panel.parcel.index') }}"
               class="gc-chip {{ empty($request->parcel_status) ? 'active' : '' }}">
                <i class="fas fa-boxes"></i>
                <span>Tous</span>
                <b>{{ $statusCounts['total'] ?? 0 }}</b>
            </a>
            <a href="{{ route('merchant-panel.parcel.filter', ['parcel_status' => \App\Enums\ParcelStatus::PENDING]) }}"
               class="gc-chip {{ (string) $request->parcel_status === (string) \App\Enums\ParcelStatus::PENDING ? 'active' : '' }}">
                <i class="fas fa-clock"></i>
                <span>{{ trans('merchantParcelStatusFilter')[App\Enums\ParcelStatus::PENDING] ?? 'En attente' }}</span>
                <b>{{ $statusCounts['pending'] ?? 0 }}</b>
            </a>
            <a href="{{ route('merchant-panel.parcel.filter', ['parcel_status' => \App\Enums\ParcelStatus::DELIVERY_MAN_ASSIGN]) }}"
               class="gc-chip {{ (string) $request->parcel_status === (string) \App\Enums\ParcelStatus::DELIVERY_MAN_ASSIGN ? 'active' : '' }}">
                <i class="fas fa-truck"></i>
                <span>{{ __('merchant.en_cours') }}</span>
                <b>{{ $statusCounts['transit'] ?? 0 }}</b>
            </a>
            <a href="{{ route('merchant-panel.parcel.filter', ['parcel_status' => \App\Enums\ParcelStatus::DELIVERED]) }}"
               class="gc-chip {{ (string) $request->parcel_status === (string) \App\Enums\ParcelStatus::DELIVERED ? 'active' : '' }}">
                <i class="fas fa-check-circle"></i>
                <span>{{ trans('merchantParcelStatusFilter')[App\Enums\ParcelStatus::DELIVERED] ?? 'Livré' }}</span>
                <b>{{ $statusCounts['delivered'] ?? 0 }}</b>
            </a>
            <a href="{{ route('merchant-panel.parcel.filter', ['parcel_status' => \App\Enums\ParcelStatus::RETURN_ASSIGN_TO_MERCHANT]) }}"
               class="gc-chip {{ (string) $request->parcel_status === (string) \App\Enums\ParcelStatus::RETURN_ASSIGN_TO_MERCHANT ? 'active' : '' }}">
                <i class="fas fa-undo"></i>
                <span>{{ trans('merchantParcelStatusFilter')[App\Enums\ParcelStatus::RETURN_ASSIGN_TO_MERCHANT] ?? 'Retours' }}</span>
                <b>{{ $statusCounts['returned'] ?? 0 }}</b>
            </a>
        </div>

        {{-- Filtres --}}
        <div class="gc-filters">
            <form action="{{ route('merchant-panel.parcel.filter') }}" method="GET" class="m-0" id="gcParcelFilter">
                @csrf
                <div class="gc-filter-grid">
                    <div class="gc-field">
                        <label class="gc-label" for="date">{{ __('parcel.date') }}</label>
                        <input type="text" autocomplete="off" id="date" name="parcel_date" class="gc-input date_range_picker" value="{{ old('parcel_date', $request->parcel_date) }}" placeholder="{{ __('merchantPlaceholder.date') }}">
                        @error('parcel_date')
                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="gc-field">
                        <label class="gc-label" for="parcelStatus">{{ __('parcel.status') }}</label>
                        <select id="parcelStatus" name="parcel_status" class="gc-select @error('parcel_status') is-invalid @enderror">
                            <option value="" selected>{{ __('menus.select') }} {{ __('parcel.status') }}</option>
                            @foreach (trans('merchantParcelStatusFilter') as $key => $status)
                                <option value="{{ $key }}" {{ (old('parcel_status', $request->parcel_status) == $key) ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                        @error('parcel_status')
                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="gc-field">
                        <label class="gc-label" for="parcel_customer">{{ __('parcel.customer_name') }}</label>
                        <input id="parcel_customer" type="text" name="parcel_customer" placeholder="{{ __('parcel.customer_name') }}" autocomplete="off" class="gc-input" value="{{ old('parcel_customer', $request->parcel_customer) }}">
                        @error('parcel_customer')
                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="gc-field">
                        <label class="gc-label" for="parcel_customer_phone">{{ __('parcel.customer_phone') }}</label>
                        <input id="parcel_customer_phone" type="text" name="parcel_customer_phone" placeholder="{{ __('parcel.customer_phone') }}" autocomplete="off" class="gc-input" value="{{ old('parcel_customer_phone', $request->parcel_customer_phone) }}">
                        @error('parcel_customer_phone')
                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="gc-field">
                        <label class="gc-label" for="invoice_id">{{ __('parcel.invoice_id') }}</label>
                        <input id="invoice_id" type="text" name="invoice_id" placeholder="{{ __('parcel.invoice_id') }}" autocomplete="off" class="gc-input" value="{{ old('invoice_id', $request->invoice_id) }}">
                        @error('invoice_id')
                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="gc-field" style="justify-content: flex-end">
                        <div class="flex items-center gap-2">
                            <button type="submit" class="gc-btn gc-btn-new">
                                <i class="fas fa-filter"></i> {{ __('levels.filter') }}
                            </button>
                            <a href="{{ route('merchant-panel.parcel.index') }}" class="gc-btn gc-btn-soft">
                                <i class="fas fa-eraser"></i> {{ __('levels.clear') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="gc-list-card">
            <div class="gc-list-head">
                <div>
                    <h2>{{ __('parcel.title') }}</h2>
                    <p>
                        {{ __('Showing') }} {{ $parcels->firstItem() ?? 0 }} {{ __('to') }} {{ $parcels->lastItem() ?? 0 }} {{ __('of') }} {{ $parcels->total() }} {{ __('results') }}
                    </p>
                </div>
            </div>

            @if ($parcels->count() === 0)
                <div class="gc-empty" style="border:0;border-radius:0">
                    <div class="gc-empty-icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h2>Aucun colis trouvé</h2>
                    <p>
                        Aucun colis ne correspond à votre recherche. Modifiez vos filtres ou enregistrez un nouveau colis.
                    </p>
                    <div class="gc-empty-actions">
                        <a href="{{ route('merchant-panel.parcel.create') }}" class="gc-btn gc-btn-new">
                            <i class="fas fa-plus"></i> {{ __('levels.add') }}
                        </a>
                    </div>
                </div>
            @else
                <div class="gc-table-wrap">
                    <table id="table" class="gc-table" style="width:100%">
                        <thead>
                            <tr>
                                <th class="gc-check">
                                    <input type="checkbox" id="tick-all" class="gc-checkbox form-check-input"/>
                                </th>
                                <th class="gc-actions-cell"></th>
                                <th>{{ __('parcel.tracking_id') }}</th>
                                <th>{{ __('parcel.recipient_info') }}</th>
                                <th>{{ __('parcel.amount') }}</th>
                                <th>{{ __('parcel.status') }}</th>
                                <th>{{ __('parcel.payment') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($parcels as $parcel)
                                <tr data-url="{{ route('merchant-panel.parcel.details', $parcel->id) }}">
                                    <td class="gc-check">
                                        <input type="checkbox" name="parcels[][{{ $parcel->id }}]" value="{{ $parcel->id }}" class="common-key gc-checkbox form-check-input" />
                                    </td>
                                    <td class="gc-actions-cell">
                                        <div class="dropdown">
                                            <button tabindex="-1" data-bs-toggle="dropdown" type="button" class="gc-icon-btn" aria-label="Actions">
                                                <i class="fas fa-ellipsis-v"></i>
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
                                    <td>
                                        <span class="gc-tracking-wrap">
                                            <a href="{{ route('merchant-panel.parcel.details', $parcel->id) }}" class="gc-tracking">
                                                {{ $parcel->tracking_id }}
                                            </a>
                                            <button type="button" class="gc-copy" data-copy="{{ $parcel->tracking_id }}" title="Copier l'identifiant">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="gc-cust">
                                            <div class="gc-av">
                                                {{ strtoupper(substr($parcel->customer_name, 0, 1)) }}
                                            </div>
                                            <div style="min-width:0">
                                                <div class="gc-cust-name">{{ $parcel->customer_name }}</div>
                                                <div class="gc-cust-sub">
                                                    <i class="fas fa-phone"></i>
                                                    <span>{{ $parcel->customer_phone }}</span>
                                                </div>
                                                <div class="gc-cust-sub">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:170px">{{ $parcel->customer_address }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="gc-money">
                                            <div class="gc-money-row">
                                                <span>{{ __('levels.cod') }} :</span>
                                                <b>{{ settings()->currency }} {{ number_format($parcel->cash_collection, 2) }}</b>
                                            </div>
                                            @if ($parcel->return_to_courier == App\Enums\BooleanStatus::YES)
                                                <div class="gc-money-row">
                                                    <span>{{ __('levels.return_charges') }} :</span>
                                                    <b>{{ settings()->currency }} {{ number_format($parcel->return_charges, 2) }}</b>
                                                </div>
                                            @else
                                                <div class="gc-money-row">
                                                    <span>{{ __('levels.total_delivery_amount') }} :</span>
                                                    <b>{{ settings()->currency }} {{ number_format($parcel->total_delivery_amount, 2) }}</b>
                                                </div>
                                                <div class="gc-money-row">
                                                    <span>{{ __('levels.vat_amount') }} :</span>
                                                    <b>{{ settings()->currency }} {{ number_format($parcel->vat_amount, 2) }}</b>
                                                </div>
                                            @endif
                                            <div class="gc-money-total">
                                                <span>{{ __('levels.current_payable') }} :</span>
                                                <b>{{ settings()->currency }} {{ number_format($parcel->current_payable, 2) }}</b>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>{!! $parcel->parcel_status !!}</div>
                                        <span class="gc-when">{{ __('parcel.updated_on') }}: {{ \Carbon\Carbon::parse($parcel->updated_at)->format('d/m/Y H:i') }}</span>
                                    </td>
                                    <td>
                                        @if ($parcel->invoice)
                                            <div class="gc-inv">
                                                @if ($parcel->invoice->status == App\Enums\InvoiceStatus::PAID)
                                                    <span class="badge badge-pill badge-success">{{ __('invoice.' . @$parcel->invoice->status) }}</span>
                                                @else
                                                    <span class="badge badge-pill badge-warning">{{ __('invoice.' . @$parcel->invoice->status) }}</span>
                                                @endif
                                                <span class="gc-inv-id">{{ @$parcel->invoice->invoice_id }}</span>
                                            </div>
                                            @if ($parcel->invoice->status == App\Enums\InvoiceStatus::PAID)
                                                <span class="gc-when">Paid At: {{ dateFormat(@$parcel->invoice->updated_at) }}</span>
                                            @endif
                                        @else
                                            <span class="badge badge-pill badge-secondary">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="gc-pagination">
                    <p class="gc-count">
                        {{ __('Showing') }}
                        <b>{{ $parcels->firstItem() }}</b>
                        {{ __('to') }}
                        <b>{{ $parcels->lastItem() }}</b>
                        {{ __('of') }}
                        <b>{{ $parcels->total() }}</b>
                        {{ __('results') }}
                    </p>
                    <div class="flex justify-end">
                        {{ $parcels->appends($request->all())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Barre flottante de sélection --}}
    <div class="gc-select-bar" id="gcSelectBar">
        <span class="gc-select-info">
            <i class="fas fa-check-circle"></i>
            <b id="gcSelCount">0</b> colis sélectionné(s)
        </span>
        <span class="gc-select-actions">
            <button type="submit" form="print_label_form" class="gc-btn gc-btn-new multiplelabelprint">
                <i class="fas fa-print"></i> {{ __('levels.print_label') }}
            </button>
            <button type="button" id="gcSelClear" class="gc-btn gc-btn-soft">
                <i class="fas fa-times"></i> {{ __('levels.clear') }}
            </button>
        </span>
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
            var $all = $('#tick-all');
            var $selectBar = $('#gcSelectBar');
            var $selCount = $('#gcSelCount');

            function showPrintBtn() {
                var n = $('.common-key:checked').length;
                if (n > 0) {
                    $selectBar.addClass('visible');
                    $selCount.text(n);
                    var inputs = '';
                    $('.common-key:checked').each(function () {
                        inputs += '<input type="hidden" name="parcels[]" value="' + $(this).val() + '"/>';
                    });
                    $('#print_label_content').html(inputs);
                } else {
                    $selectBar.removeClass('visible');
                    $all.prop('checked', false);
                    $('#print_label_content').html('');
                }
                $all.prop('indeterminate', n > 0 && n < $('.common-key').length);
            }

            $all.on('change', function () {
                $('.common-key').prop('checked', this.checked);
                showPrintBtn();
            });
            $('.common-key').on('change', showPrintBtn);

            $('#gcSelClear').on('click', function () {
                $('.common-key').prop('checked', false);
                showPrintBtn();
            });

            /* clic sur une ligne -> détails (hors éléments interactifs) */
            $('.gc-table tbody tr').on('click', function (e) {
                if ($(e.target).closest('a, button, input, form, .dropdown').length) return;
                var url = $(this).data('url');
                if (url) window.location.href = url;
            });

            /* copie de l'identifiant de suivi */
            $('.gc-copy').on('click', function (e) {
                e.stopPropagation();
                var $btn = $(this);
                var txt = $btn.data('copy') || '';
                if (!txt) return;
                function done() {
                    $btn.addClass('copied').find('i').removeClass('fa-copy').addClass('fa-check');
                    setTimeout(function () {
                        $btn.removeClass('copied').find('i').removeClass('fa-check').addClass('fa-copy');
                    }, 1500);
                }
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(txt).then(done);
                } else {
                    var ta = document.createElement('textarea');
                    ta.value = txt;
                    document.body.appendChild(ta);
                    ta.select();
                    try { document.execCommand('copy'); done(); } catch (err) {}
                    document.body.removeChild(ta);
                }
            });

            /* auto-filtre : statut immédiat, champs texte avec debounce */
            $('#parcelStatus').on('change', function () {
                $('#gcParcelFilter').submit();
            });
            var gcDebounce;
            $('#gcParcelFilter input[name="parcel_customer"], #gcParcelFilter input[name="parcel_customer_phone"], #gcParcelFilter input[name="invoice_id"]').on('input', function () {
                clearTimeout(gcDebounce);
                gcDebounce = setTimeout(function () { $('#gcParcelFilter').submit(); }, 700);
            });
        });
    </script>
@endpush
