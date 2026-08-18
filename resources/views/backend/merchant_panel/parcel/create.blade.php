@extends('backend.partials.master')
@section('title')
    Créer un colis
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- En-tête --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">
                <i class="fas fa-box-open text-[18px] mr-2 text-wc-primary"></i>
                Créer un colis
            </h1>
            <p class="wc-page-subtitle">
                Saisissez les informations ci-dessous puis validez.
            </p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('merchant-panel.parcel.index') }}" class="wc-btn wc-btn-outline">
                <i class="fas fa-arrow-left text-[12px]"></i> Annuler
            </a>
            <button type="submit" form="basicform" class="wc-btn wc-btn-primary">
                <i class="fas fa-plus text-[12px]"></i> Ajouter un colis
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 items-start">

        {{-- Formulaire --}}
        <div class="xl:col-span-2">
            <form action="{{ route('merchant-panel.parcel.store') }}" method="POST"
                enctype="multipart/form-data" id="basicform">
                @csrf

                {{-- Hidden fields --}}
                <input type="hidden" id="merchant_id" name="merchant_id" value="{{ $merchant->id }}" />

                <div class="wc-card overflow-hidden">
                    <div class="wc-card-header">
                        <div class="wc-card-icon bg-[#eef1f5] text-[#475569]">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <div>
                            <h3 class="wc-card-title">Nouveau colis</h3>
                            <p class="text-[12px] text-wc-muted m-0">Informations d'expédition et du destinataire</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="wc-sheet">
                            <thead>
                                <tr>
                                    <th class="wc-sheet-head wc-sheet-label">Champ</th>
                                    <th class="wc-sheet-head">Valeur</th>
                                </tr>
                            </thead>
                            <tbody>

                                {{-- ── Expédition ── --}}
                                <tr class="wc-sheet-group">
                                    <td colspan="2"><i class="fas fa-truck"></i>Expédition</td>
                                </tr>

                                <tr>
                                    <th class="wc-sheet-label" scope="row">Boutique <span class="text-danger">*</span></th>
                                    <td>
                                        <select style="width: 100%" id="shopID" class="form-control" name="shop_id"
                                            data-url="{{ route('merchant-panel.parcel.merchant.shops') }}">
                                            <option value=""> {{ __('menus.select') }} {{ __('parcel.shop') }}</option>
                                            @foreach ($shops as $shop)
                                                <option value="{{ $shop->id }}"
                                                    {{ old('shop_id', $merchantShop->id) == $shop->id ? 'selected' : '' }}>
                                                    {{ $shop->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('shop_id')
                                            <small class="text-danger mt-2 d-block">{{ $message }}</small>
                                        @enderror
                                    </td>
                                </tr>

                                <tr>
                                    <th class="wc-sheet-label" scope="row">{{ __('parcel.pickup_phone') }}</th>
                                    <td>
                                        <input id="pickup_phone" type="text" name="pickup_phone"
                                            data-parsley-trigger="change" placeholder="{{ __('parcel.pickup_phone') }}"
                                            autocomplete="off" class="form-control"
                                            value="{{ old('pickup_phone', $merchantShop->contact_no) }}" required="">
                                        @error('pickup_phone')
                                            <small class="text-danger mt-2 d-block">{{ $message }}</small>
                                        @enderror
                                    </td>
                                </tr>

                                <tr>
                                    <th class="wc-sheet-label" scope="row">{{ __('parcel.pickup_address') }}</th>
                                    <td>
                                        <input id="pickup_address" type="text" name="pickup_address"
                                            data-parsley-trigger="change" placeholder="{{ __('parcel.pickup_address') }}"
                                            autocomplete="off" class="form-control"
                                            value="{{ old('pickup_address', $merchantShop->address) }}" required="">
                                        <input type="hidden" id="pickup_lat" name="pickup_lat" required=""
                                            value="{{ $merchantShop->merchant_lat }}">
                                        <input type="hidden" id="pickup_long" name="pickup_long" required=""
                                            value="{{ $merchantShop->merchant_long }}">
                                        @error('pickup_address')
                                            <small class="text-danger mt-2 d-block">{{ $message }}</small>
                                        @enderror
                                    </td>
                                </tr>

                                {{-- ── Destinataire ── --}}
                                <tr class="wc-sheet-group">
                                    <td colspan="2"><i class="fas fa-user"></i>Destinataire</td>
                                </tr>

                                <tr>
                                    <th class="wc-sheet-label" scope="row">{{ __('parcel.customer_name') }} <span class="text-danger">*</span></th>
                                    <td>
                                        <input id="customer_name" type="text" name="customer_name"
                                            data-parsley-trigger="change" placeholder="{{ __('parcel.customer_name') }}"
                                            autocomplete="off" class="form-control" value="{{ old('customer_name') }}"
                                            required="">
                                        @error('customer_name')
                                            <small class="text-danger mt-2 d-block">{{ $message }}</small>
                                        @enderror
                                    </td>
                                </tr>

                                <tr>
                                    <th class="wc-sheet-label" scope="row">{{ __('parcel.customer_phone') }} <span class="text-danger">*</span></th>
                                    <td>
                                        <input id="phone" type="text" name="customer_phone"
                                            data-parsley-trigger="change" placeholder="{{ __('parcel.customer_phone') }}"
                                            autocomplete="off" class="form-control" value="{{ old('customer_phone') }}"
                                            required="">
                                        @error('customer_phone')
                                            <small class="text-danger mt-2 d-block">{{ $message }}</small>
                                        @enderror
                                    </td>
                                </tr>

                                <tr>
                                    <th class="wc-sheet-label" scope="row">{{ __('parcel.customer_address') }} <span class="text-danger">*</span></th>
                                    <td>
                                        <input type="hidden" id="lat" name="lat" required="" value="">
                                        <input type="hidden" id="long" name="long" required="" value="">
                                        <div class="main-search-input-item location location-search">
                                            <div id="autocomplete-container" class="form-group random-search mb-0">
                                                <input id="autocomplete-input" type="text" name="customer_address"
                                                    class="recipe-search2 form-control" placeholder="Location Here!"
                                                    required="">
                                                <a href="javascript:void(0)" class="submit-btn btn current-location"
                                                    id="locationIcon" onclick="getLocation()">
                                                    <i class="fa fa-crosshairs"></i>
                                                </a>
                                                @error('customer_address')
                                                    <small class="text-danger mt-2 d-block">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="wc-sheet-map mt-2">
                                            <div id="googleMap" class="custom-map"></div>
                                        </div>
                                    </td>
                                </tr>

                                {{-- ── Paiement ── --}}
                                <tr class="wc-sheet-group">
                                    <td colspan="2"><i class="fas fa-coins"></i>Paiement</td>
                                </tr>

                                <tr>
                                    <th class="wc-sheet-label" scope="row">{{ __('parcel.cash_collection') }} <span class="text-danger">*</span></th>
                                    <td>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control cash-collection" id="cash_collection"
                                                value="{{ old('cash_collection') }}" name="cash_collection"
                                                placeholder="{{ __('parcel.Cash_amount_including_delivery_charge') }}"
                                                required="">
                                            @error('cash_collection')
                                                <small class="text-danger mt-2 d-block">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th class="wc-sheet-label" scope="row">{{ __('parcel.selling_price') }}</th>
                                    <td>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control cash-collection" id="selling_price"
                                                value="{{ old('selling_price') }}" name="selling_price"
                                                placeholder="{{ __('parcel.Selling_price_of_parcel') }}">
                                            @error('selling_price')
                                                <small class="text-danger mt-2 d-block">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th class="wc-sheet-label" scope="row">{{ __('parcel.invoice') }}</th>
                                    <td>
                                        <input id="invoice_no" type="text" name="invoice_no"
                                            data-parsley-trigger="change"
                                            placeholder="{{ __('parcel.enter_invoice_number') }}" autocomplete="off"
                                            class="form-control" value="{{ old('invoice_no') }}">
                                        @error('invoice_no')
                                            <small class="text-danger mt-2 d-block">{{ $message }}</small>
                                        @enderror
                                    </td>
                                </tr>

                                <tr>
                                    <th class="wc-sheet-label" scope="row">Méthode de paiement <span class="text-danger">*</span></th>
                                    <td>
                                        @error('parcel_payment_method')
                                            <small class="text-danger d-block mb-2">{{ $message }}</small>
                                        @enderror
                                        <div class="d-flex gap-3 flex-wrap">
                                            <div>
                                                <input class="methodInput" type="radio" name="parcel_payment_method" id="cod" value="{{ App\Enums\ParcelPaymentMethod::COD }}" checked />
                                                <label class="payment-method-box text-center d-block" for="cod">
                                                    <i class="fa fa-hand-holding-dollar" style="font-size:34px"></i>
                                                    <div class="mt-1">{{ __('ParcelPaymentMethod.'.App\Enums\ParcelPaymentMethod::COD) }}</div>
                                                </label>
                                            </div>
                                            @if(auth()->user()->merchant->wallet_use_activation == App\Enums\Status::ACTIVE)
                                                <div>
                                                    <input class="methodInput" type="radio" name="parcel_payment_method" id="prepaid" value="{{ App\Enums\ParcelPaymentMethod::PREPAID }}"/>
                                                    <label class="payment-method-box text-center d-block" for="prepaid">
                                                        <i class="fa fa-wallet" style="font-size:34px"></i>
                                                        <div class="mt-1">{{ __('ParcelPaymentMethod.'.App\Enums\ParcelPaymentMethod::PREPAID) }}</div>
                                                    </label>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                    <div class="px-5 py-4 bg-[#fafbfc] border-t border-wc-border flex items-center justify-end gap-2">
                        <a href="{{ route('merchant-panel.parcel.index') }}" class="wc-btn wc-btn-outline">
                            <i class="fas fa-times text-[12px]"></i> Annuler
                        </a>
                        <button type="submit" class="wc-btn wc-btn-primary">
                            <i class="fas fa-plus text-[12px]"></i> Ajouter un colis
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Sidebar — montant à encaisser --}}
        <div class="xl:col-span-1">
            <div class="wc-card overflow-hidden xl:sticky xl:top-5">
                <div class="wc-card-header">
                    <div class="wc-card-icon bg-[#eef1f5] text-[#475569]">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div>
                        <h3 class="wc-card-title">Récapitulatif</h3>
                        <p class="text-[12px] text-wc-muted m-0">Montant à encaisser</p>
                    </div>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item profile-list-group-item">
                        <span class="font-weight-bold">{{ __('levels.title') }}</span>
                        <span class="float-right font-weight-bold">{{ __('levels.amount') }}</span>
                    </li>
                    <li class="list-group-item profile-list-group-item">
                        <span class="font-weight-bold">{{ __('parcel.Cash_Collection') }}</span>
                        <span class="float-right wc-tabular" id="totalCashCollection">{{ __('0.00') }}</span>
                    </li>
                    <li class="list-group-item profile-list-group-item">
                        <span class="font-weight-bold">{{ __('parcel.Current_payable') }}</span>
                        <span class="float-right wc-tabular" id="currentPayable">{{ __('0.00') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection()

@push('styles')
    <style>
        .main-search-input-item {
            flex: 1;
            margin-top: 3px;
            position: relative;
        }

        #autocomplete-container,
        #autocomplete-input {
            position: relative;
            z-index: 101;
        }

        .main-search-input input,
        .main-search-input input:focus {
            font-size: 16px;
            border: none;
            background: #fff;
            margin: 0;
            padding: 0;
            height: 44px;
            line-height: 44px;
            box-shadow: none;
        }

        .input-with-icon i,
        .main-search-input-item.location a {
            padding: 5px 10px;
            z-index: 101;
        }

        .main-search-input-item.location a {
            position: absolute;
            right: -50px;
            top: 40%;
            transform: translateY(-50%);
            color: #999;
            padding: 10px;
        }

        .current-location {
            margin-right: 50px;
            margin-top: 5px;
            color: #FFCC00 !important;
        }

        .custom-map {
            width: 100%;
            height: 100%;
            min-height: 210px;
        }

        .pac-container {
            width: 295px;
            position: absolute;
            left: 0px !important;
            top: 28px !important;
        }

        .wc-sheet-map { height: 210px; }

        .wc-sheet .payment-method-box {
            min-width: 130px;
            padding: 12px 16px;
            border: 1px solid #dfe3e9;
            border-radius: 10px;
            background: #fff;
            cursor: pointer;
            transition: all .15s ease;
            margin-bottom: 0;
        }
        .wc-sheet .payment-method-box:hover { border-color: #94a3b8; background: #fafbfc; }
        .wc-sheet .methodInput:checked + .payment-method-box {
            border-color: var(--wc-primary);
            background: var(--wc-primary-soft);
            box-shadow: 0 0 0 3px rgba(5, 150, 105, .12);
        }
        .wc-sheet .methodInput { position: absolute; opacity: 0; }
    </style>
@endpush
@push('scripts')
    <script type="text/javascript" src="{{ static_asset('backend/js/parcel/map-current.js') }}"></script>
    <script async
        src="https://maps.googleapis.com/maps/api/js?key={{ googleMapSettingKey() }}&libraries=places&callback=initMap">
    </script>
    <script src="{{ static_asset('backend/js/merchant_panel/parcel/create.js') }}"></script>
@endpush
