@extends('backend.partials.master')
@section('title')
    {{ __('support.supprot') }} {{ __('levels.add') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">{{ __('support.supprot_add') }}</h1>
            <p class="wc-page-subtitle">{{ __('support.supprot') }} · ouvrir un nouveau ticket</p>
        </div>
    </div>

    <div class="wc-card !max-w-[960px]">
        <div class="wc-card-header">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                    <i class="fas fa-headset"></i>
                </div>
                <div>
                    <h3 class="wc-card-title">{{ __('support.supprot_add') }}</h3>
                    <p class="text-[12px] text-wc-muted m-0">Les champs marqués d'un <span class="text-wc-danger">*</span> sont obligatoires.</p>
                </div>
            </div>
        </div>
        <div class="p-4 sm:p-6">
            <form action="{{ route('merchant-panel.support.store') }}" method="POST" enctype="multipart/form-data" id="basicform">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="wc-form-group m-0">
                        <label class="wc-label" for="service">{{ __('support.service') }} <span class="text-wc-danger">*</span></label>
                        <select name="service" class="wc-select @error('service') is-invalid @enderror">
                            <option disabled selected>{{ __('merchantPlaceholder.service') }}</option>
                            @foreach (trans('SalaryService') as $key => $value)
                                <option value="{{ $key }}"{{ old('service') == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('service')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                    </div>
                    <div class="wc-form-group m-0">
                        <label class="wc-label" for="status">{{ __('support.priority') }} <span class="text-wc-danger">*</span></label>
                        <select name="priority" class="wc-select @error('priority') is-invalid @enderror">
                            <option disabled selected>{{ __('merchantPlaceholder.priority') }}</option>
                            <option value="low"{{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium"{{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high"{{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                        </select>
                        @error('status')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                    </div>
                    <div class="wc-form-group m-0">
                        <label class="wc-label" for="date">{{ __('support.date') }} <span class="text-wc-danger">*</span></label>
                        <input type="date" name="date" class="wc-input" value="{{ old('date', date('Y-m-d')) }}">
                    </div>
                    <div class="wc-form-group m-0">
                        <label class="wc-label" for="department_id">{{ __('support.department_id') }}</label>
                        <select class="wc-select" id="department_id" name="department_id">
                            <option disabled selected>{{ __('merchantPlaceholder.department') }}</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->title }}</option>
                            @endforeach
                        </select>
                        @error('department_id')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                    </div>
                    <div class="wc-form-group m-0">
                        <label class="wc-label" for="subject">{{ __('support.subject') }} <span class="text-wc-danger">*</span></label>
                        <input id="subject" type="text" name="subject" data-parsley-trigger="change" placeholder="{{ __('merchantPlaceholder.subject') }}" autocomplete="off" class="wc-input" value="{{ old('subject') }}" required>
                        @error('subject')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                    </div>
                    <div class="wc-form-group m-0">
                        <label class="wc-label" for="image">{{ __('support.attached') }}</label>
                        <input id="attached_file" type="file" name="attached_file" data-parsley-trigger="change" autocomplete="off" class="wc-input wc-input-file" value="{{ old('attached_file ') }}">
                        @error('attached_file')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                    </div>
                    <div class="wc-form-group m-0 md:col-span-2">
                        <label class="wc-label" for="description">{{ __('support.description') }}</label>
                        <textarea class="wc-input !h-auto resize-y" name="description" rows="5" id="description">{{ old('description') }}</textarea>
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-5">
                    <button type="submit" class="wc-btn wc-btn-primary"><i class="fa fa-check text-[12px]"></i> {{ __('levels.save') }}</button>
                    <a href="{{ route('merchant-panel.support.index') }}" class="wc-btn wc-btn-outline">{{ __('levels.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection()

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.9.1/summernote-lite.min.css" />
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.9.1/summernote-lite.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#description').summernote({
                height: 182
            });
        });
    </script>
@endpush