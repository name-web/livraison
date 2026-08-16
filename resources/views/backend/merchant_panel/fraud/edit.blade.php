@extends('backend.partials.master')
@section('title')
    {{ __('fraud.title') }} {{ __('levels.edit') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">{{ __('fraud.edit_fraud') }}</h1>
            <p class="wc-page-subtitle">{{ __('fraud.title') }} · modifier le signalement</p>
        </div>
    </div>

    <div class="wc-card !max-w-[860px]">
        <div class="wc-card-header">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div>
                    <h3 class="wc-card-title">{{ __('fraud.edit_fraud') }}</h3>
                    <p class="text-[12px] text-wc-muted m-0">Les champs marqués d'un <span class="text-wc-danger">*</span> sont obligatoires.</p>
                </div>
            </div>
        </div>
        <div class="p-4 sm:p-6">
            <form action="{{route('merchant-panel.fraud.update',['id'=>$fraud->id])}}" method="POST" enctype="multipart/form-data" id="basicform">
                @csrf
                @if (isset($fraud))
                    @method('PUT')
                @endif
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="wc-form-group m-0">
                        <label class="wc-label" for="phone">{{ __('levels.phone') }} <span class="text-wc-danger">*</span></label>
                        <input id="phone" type="text" name="phone" data-parsley-trigger="change" placeholder="{{ __('merchantPlaceholder.phone') }}" autocomplete="off" class="wc-input @error('phone') is-invalid @enderror" value="{{old('phone',$fraud->phone)}}" required>
                        @error('phone')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                    </div>
                    <div class="wc-form-group m-0">
                        <label class="wc-label" for="name">{{ __('levels.name') }} <span class="text-wc-danger">*</span></label>
                        <input id="name" type="text" name="name" data-parsley-trigger="change" placeholder="{{ __('merchantPlaceholder.name') }}" autocomplete="off" class="wc-input @error('name') is-invalid @enderror" value="{{old('name',$fraud->name)}}" required>
                        @error('name')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                    </div>
                    <div class="wc-form-group m-0 md:col-span-2">
                        <label class="wc-label" for="tracking_id">{{ __('levels.track_id') }}</label>
                        <input id="tracking_id" type="text" name="tracking_id" placeholder="{{ __('merchantPlaceholder.tracking_id') }}" autocomplete="off" class="wc-input @error('tracking_id') is-invalid @enderror" value="{{old('tracking_id',$fraud->tracking_id)}}">
                        @error('tracking_id')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                    </div>
                    <div class="wc-form-group m-0 md:col-span-2">
                        <label class="wc-label" for="details">{{ __('levels.details') }} <span class="text-wc-danger">*</span></label>
                        <textarea name="details" id="details" class="wc-input !h-auto min-h-[120px] resize-y" rows="6" required>{{old('details',$fraud->details)}}</textarea>
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-5">
                    <button type="submit" class="wc-btn wc-btn-primary"><i class="fa fa-check text-[12px]"></i> {{ __('levels.save_change') }}</button>
                    <a href="{{ route('merchant-panel.fraud.index') }}" class="wc-btn wc-btn-outline">{{ __('levels.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection()