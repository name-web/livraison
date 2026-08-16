@extends('backend.partials.master')
@section('title')
    {{ __('merchantshops.title') }} {{ __('levels.edit') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">{{ __('merchantshops.edit_shops') }}</h1>
            <p class="wc-page-subtitle">{{ __('merchantshops.title') }} · modifier le point de dépôt</p>
        </div>
    </div>

    <div class="wc-card !max-w-[860px]">
        <div class="wc-card-header">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                    <i class="fas fa-store"></i>
                </div>
                <div>
                    <h3 class="wc-card-title">{{ __('merchantshops.edit_shops') }}</h3>
                    <p class="text-[12px] text-wc-muted m-0">Les champs marqués d'un <span class="text-wc-danger">*</span> sont obligatoires.</p>
                </div>
            </div>
        </div>
        <div class="p-4 sm:p-6">
            <form action="{{route('merchant-panel.shops.update',$shop->id)}}" method="POST" enctype="multipart/form-data" id="basicform">
                @method('PUT')
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="wc-form-group m-0">
                        <label class="wc-label" for="name">{{ __('levels.name') }} <span class="text-wc-danger">*</span></label>
                        <input id="name" type="text" name="name" data-parsley-trigger="change" placeholder="{{ __('merchantPlaceholder.name') }}" autocomplete="off" class="wc-input" value="{{$shop->name}}" required>
                        @error('name')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                    </div>
                    <div class="wc-form-group m-0">
                        <label class="wc-label" for="contact">{{ __('merchantshops.contact') }} <span class="text-wc-danger">*</span></label>
                        <input id="contact" type="phone" name="contact_no" data-parsley-trigger="change" placeholder="{{ __('merchantPlaceholder.phone') }}" autocomplete="off" class="wc-input" value="{{$shop->contact_no}}" required>
                        @error('contact_no')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                    </div>
                    <div class="wc-form-group m-0 md:col-span-2">
                        <label class="wc-label" for="autocomplete-input">{{ __('levels.address') }} <span class="text-wc-danger">*</span></label>
                        <input type="hidden" id="lat" name="lat" required="" value="{{ $shop->merchant_lat }}">
                        <input type="hidden" id="long" name="long" required="" value="{{ $shop->merchant_long }}">
                        <div class="main-search-input-item location location-search">
                            <div id="autocomplete-container" class="random-search">
                                <div class="flex items-center gap-2">
                                    <input id="autocomplete-input" type="text" name="address" value="{{$shop->address}}" class="recipe-search2 wc-input flex-1" placeholder="Location Here!" required="">
                                    <a href="javascript:void(0)" class="submit-btn btn current-location wc-btn wc-btn-outline wc-btn-sm !shrink-0" id="locationIcon" onclick="getLocation()">
                                        <i class="fa fa-crosshairs"></i>
                                    </a>
                                </div>
                                @error('address')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="mt-3">
                            <div id="googleMap" class="custom-map rounded-lg border border-wc-border"></div>
                        </div>
                    </div>
                    <div class="wc-form-group m-0 md:col-span-2">
                        <label class="wc-label" for="status">{{__('levels.status')}} <span class="text-wc-danger">*</span></label>
                        <select name="status" class="wc-select @error('status') is-invalid @enderror">
                            @foreach(trans('status') as $key => $status)
                                <option value="{{ $key }}" {{ (old('status',$shop->status) == $key) ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                        @error('status')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-5">
                    <button type="submit" class="wc-btn wc-btn-primary"><i class="fa fa-check text-[12px]"></i> {{ __('levels.save_change') }}</button>
                    <a href="{{ route('merchant-panel.shops.index') }}" class="wc-btn wc-btn-outline">{{ __('levels.cancel') }}</a>
                </div>
            </form>
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
            height: 17rem;
        }
        .pac-container {
            width: 295px;
            position: absolute;
            left: 0px !important;
            top: 28px !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        var mapLat = '{{ $shop->merchant_lat }}'
        var mapLong = '{{ $shop->merchant_long }}'
    </script>
    <script type="text/javascript" src="{{ static_asset('backend/js/map/map-current.js') }}"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ googleMapSettingKey() }}&libraries=places&callback=initMap">
    </script>
@endpush