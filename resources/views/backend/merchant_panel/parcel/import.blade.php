@extends('backend.partials.master')
@section('title')
    {{ __('parcel.title') }} {{ __('levels.import') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">
                <i class="fas fa-file-import text-[18px] mr-2 text-wc-primary"></i>
                {{ __('parcel.parcel_import') }}
            </h1>
            <p class="wc-page-subtitle">Importez vos colis en masse depuis un fichier Excel.</p>
        </div>
        <div>
            <a href="{{ static_asset('sample-parcel/merchantParcel/import-parcel.xlsx') }}" download class="wc-btn wc-btn-outline" data-toggle="tooltip" data-placement="top" title="download">
                <i class="fas fa-download text-[12px]"></i> {{ __('parcel.sample') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">
        <div class="lg:col-span-2">
            <div class="wc-card">
                <div class="wc-card-header">
                    <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                        <i class="fas fa-file-excel"></i>
                    </div>
                    <div>
                        <h3 class="wc-card-title">{{ __('parcel.parcel_import') }}</h3>
                        <p class="text-[12px] text-wc-muted m-0">Consignes et téléversement du fichier</p>
                    </div>
                </div>
                <div class="p-5 sm:p-6">
                    <p class="text-[13.5px] text-wc-ink-2 mb-3">{{ __('merchantImport.note') }}</p>
                    <ul class="space-y-2 text-[13.5px] text-wc-ink-2 list-disc list-inside">
                        <li>{{ __('merchantImport.01') }}</li>
                        <li>{{ __('merchantImport.02') }}</li>
                        <li>{{ __('merchantImport.03') }}</li>
                        <li>{{ __('merchantImport.04') }}: @foreach($deliveryCategories as $category) @if($loop->last){{ $category->id }}={{ $category->title }} @else {{ $category->id }}={{ $category->title }},@endif  @endforeach</li>
                        <li>{{ __('merchantImport.05') }}: @foreach(trans('deliveryType') as $key => $status) @if($loop->last){{ $key }}={{ $status }} @else {{ $key}}={{ $status }},@endif  @endforeach</li>
                        <li>{{ __('merchantImport.06') }}</li>
                    </ul>

                    <form action="{{ route('merchant-panel.parcel.file-import') }}" method="POST" enctype="multipart/form-data" class="mt-6">
                        @csrf
                        <div class="flex items-center gap-3 flex-wrap">
                            <div class="flex-1 min-w-[260px]">
                                <input type="file" name="file" class="form-control" id="customFile">
                                @error('file')
                                    <div class="text-danger text-[12.5px] mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <button class="wc-btn wc-btn-primary">
                                <i class="fas fa-upload text-[12px]"></i> {{ __('parcel.import') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if(session()->has('importErrors'))
            <div class="lg:col-span-1">
                <div class="wc-card overflow-hidden">
                    <div class="wc-card-header">
                        <div class="wc-card-icon bg-[#fef2f2] text-[#dc2626]">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <h3 class="wc-card-title">{{ __('parcel.validation_log') }}</h3>
                            <p class="text-[12px] text-wc-muted m-0">Erreurs détectées lors de l'import</p>
                        </div>
                    </div>
                    <div class="p-4 space-y-4 max-h-[420px] overflow-y-auto">
                        @foreach(session()->get('importErrors') as $key => $values)
                            <div>
                                <p class="text-[12.5px] font-bold text-wc-primary mb-1">{{ __('parcel.in_row_number') }} : {{ $key }}</p>
                                @foreach($values as $value)
                                    <p class="text-[12.5px] text-[#dc2626] mb-0.5"><i class="fas fa-circle-xmark text-[10px] mr-1"></i>{{ $value }}</p>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection()
@push('scripts')
    <script src="{{ static_asset('backend/js/custom.js') }}"></script>
@endpush