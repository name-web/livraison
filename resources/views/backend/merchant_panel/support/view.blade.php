@extends('backend.partials.master')
@section('title')
    {{ __('support.supprot') }} {{ __('levels.view') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">{{ $singleSupport->subject }}</h1>
            <p class="wc-page-subtitle">{{ __('support.supprot_list') }} · conversation du ticket</p>
        </div>
        <div class="wc-toolbar">
            <a href="{{ route('merchant-panel.support.index') }}" class="wc-btn wc-btn-outline wc-btn-sm"><i class="fas fa-arrow-left text-[12px]"></i> {{ __('levels.back') }}</a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        {{-- Infos ticket --}}
        <div class="wc-card self-start">
            <div class="wc-card-header">
                <div class="flex items-center gap-3">
                    <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div>
                        <h3 class="wc-card-title">{{ __('levels.details') }}</h3>
                        <p class="text-[12px] text-wc-muted m-0">{{ __('support.user_info') }}</p>
                    </div>
                </div>
            </div>
            <div class="p-4 space-y-2.5">
                <div class="flex justify-between gap-3 text-[12.5px]">
                    <span class="text-wc-muted shrink-0">{{ __('support.user_name') }}</span>
                    <span class="font-bold text-wc-ink text-right">{{ $singleSupport->user->name }}</span>
                </div>
                <div class="flex justify-between gap-3 text-[12.5px] border-t border-wc-border pt-2.5">
                    <span class="text-wc-muted shrink-0">{{ __('support.user_email') }}</span>
                    <span class="font-bold text-wc-ink text-right break-all">{{ $singleSupport->user->email }}</span>
                </div>
                <div class="flex justify-between gap-3 text-[12.5px] border-t border-wc-border pt-2.5">
                    <span class="text-wc-muted shrink-0">{{ __('support.service') }}</span>
                    <span class="font-bold text-wc-ink text-right">{{ $singleSupport->service }}</span>
                </div>
                <div class="flex justify-between gap-3 text-[12.5px] border-t border-wc-border pt-2.5">
                    <span class="text-wc-muted shrink-0">{{ __('support.department_id') }}</span>
                    <span class="font-bold text-wc-ink text-right">{{ $singleSupport->department->title }}</span>
                </div>
                <div class="flex justify-between gap-3 text-[12.5px] border-t border-wc-border pt-2.5">
                    <span class="text-wc-muted shrink-0">{{ __('support.priority') }}</span>
                    <span class="font-bold text-wc-ink text-right">{{ $singleSupport->priority }}</span>
                </div>
                <div class="flex justify-between gap-3 text-[12.5px] border-t border-wc-border pt-2.5">
                    <span class="text-wc-muted shrink-0">{{ __('support.date') }}</span>
                    <span class="font-bold text-wc-ink text-right">{{ dateFormat($singleSupport->date) }}</span>
                </div>
            </div>
        </div>

        {{-- Conversation --}}
        <div class="xl:col-span-2 space-y-3">
            {{-- Réponse --}}
            <div id="accordion" class="wc-card">
                <div class="wc-card-header !min-h-[54px] !py-3">
                    <a href="#" class="text-wc-primary font-bold text-[13px]" data-toggle="collapse" data-target="#collapseOne"
                        aria-expanded="@if ($errors->has('message')) true @else false @endif"
                        aria-controls="collapseOne"><i class="fa fa-reply mr-2"></i>{{ __('support.reply') }}</a>
                </div>
                <div id="collapseOne" class="collapse @error('message') show @enderror" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="p-4 sm:p-5 border-t border-wc-border">
                        <form action="{{ route('merchant-panel.support.reply', ['support_id' => $singleSupport->id]) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="wc-form-group m-0">
                                <label class="wc-label" for="message">{{ __('support.message') }} <span class="text-wc-danger">*</span></label>
                                <textarea class="wc-input !h-auto resize-y @error('message') is-invalid @enderror" name="message" rows="5" id="message" placeholder="Enter message">{{ old('message') }}</textarea>
                                @error('message')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                            </div>
                            <div class="wc-form-group m-0 mt-3">
                                <label class="wc-label" for="attached_file">{{ __('support.attached_file') }}</label>
                                <input id="attached_file" type="file" name="attached_file" data-parsley-trigger="change" autocomplete="off" class="wc-input wc-input-file" value="{{ old('attached_file ') }}">
                                @error('attached_file')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                            </div>
                            <div class="mt-4 flex justify-end">
                                <button type="submit" class="wc-btn wc-btn-primary"><i class="fa fa-paper-plane text-[12px]"></i> {{ __('levels.send') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Messages --}}
            @foreach ($chats as $chat)
            <div class="wc-card">
                <div class="p-4 sm:p-5">
                    <div class="flex items-start gap-3">
                        <img src="{{ @$chat->user->image }}" alt="user" class="rounded-full w-10 h-10 object-cover shrink-0 border border-wc-border">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <strong class="text-wc-ink text-[13px]">{{ @$chat->user->name }}</strong>
                                @if ($chat->user->id == 1)
                                    <span class="wc-badge wc-badge-info-soft">{{ __('userType.' . @$chat->user->user_type) }}</span>
                                @elseif ($chat->user->user_type == \App\Enums\UserType::MERCHANT)
                                    <span class="wc-badge wc-badge-success-soft">{{ __('userType.' . App\Enums\UserType::MERCHANT) }}</span>
                                @else
                                    <span class="wc-badge wc-badge-neutral">{{ __('user.title') }}</span>
                                @endif
                                <small class="text-wc-muted-2 ml-auto text-[11.5px]">{{ \Carbon\Carbon::parse($chat->created_at)->format('d M Y h:i A') }}</small>
                            </div>
                            <div class="mt-2.5 text-[13px] text-wc-ink-2 leading-relaxed">{!! $chat->message !!}</div>
                            @if (@$chat->file)
                                <div class="mt-3 flex items-center gap-2 p-2.5 rounded-lg bg-wc-surface-soft border border-wc-border max-w-[240px]">
                                    <i class="fa fa-file text-wc-primary text-[22px]"></i>
                                    <a href="{{ static_asset(@$chat->file->original) }}" download="" class="text-[12.5px] font-bold text-wc-primary">Download File</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            {{-- Message d'origine --}}
            <div class="wc-card border-l-4 !border-l-wc-primary">
                <div class="p-4 sm:p-5">
                    <div class="flex items-start gap-3">
                        <img src="{{ @$singleSupport->user->image }}" alt="user" class="rounded-full w-10 h-10 object-cover shrink-0 border border-wc-border">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <strong class="text-wc-ink text-[13px]">{{ @$singleSupport->user->name }}</strong>
                                <span class="wc-badge wc-badge-success-soft">{{ __('UserType.' . @$singleSupport->user->user_type) }}</span>
                                <small class="text-wc-muted-2 ml-auto text-[11.5px]">{{ \Carbon\Carbon::parse($singleSupport->created_at)->format('d M Y h:i A') }}</small>
                            </div>
                            <div class="mt-2.5 text-[13px] text-wc-ink-2 leading-relaxed">{!! $singleSupport->description !!}</div>
                            @if (@$singleSupport->file)
                                <div class="mt-3 flex items-center gap-2 p-2.5 rounded-lg bg-wc-surface-soft border border-wc-border max-w-[240px]">
                                    <i class="fa fa-file text-wc-primary text-[22px]"></i>
                                    <a href="{{ static_asset(@$singleSupport->file->original) }}" download="" class="text-[12.5px] font-bold text-wc-primary">Download File</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
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
            $('#message').summernote({
                height: 182
            });
        });
    </script>
@endpush