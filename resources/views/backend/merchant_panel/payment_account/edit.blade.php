@extends('backend.partials.master')
@section('title')
    {{ __('merchant.payment_accounts') }} {{ __('levels.edit') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">

    {{-- Page header --}}
    <div class="wc-page-header">
        <div>
            <h1 class="wc-page-title">{{ __('merchant.edit_account') }}</h1>
            <p class="wc-page-subtitle">{{ __('merchant.payment_accounts') }} · modifier le compte de paiement</p>
        </div>
    </div>

    <div class="wc-card !max-w-[960px]">
        <div class="wc-card-header">
            <div class="flex items-center gap-3">
                <div class="wc-card-icon bg-wc-primary-soft text-wc-primary">
                    <i class="fas fa-wallet"></i>
                </div>
                <div>
                    <h3 class="wc-card-title">{{ __('merchant.edit_account') }}</h3>
                    <p class="text-[12px] text-wc-muted m-0">Les champs marqués d'un <span class="text-wc-danger">*</span> sont obligatoires.</p>
                </div>
            </div>
        </div>
        <div class="p-4 sm:p-6">
            <form action="{{route('payment.account.update',['id'=>$editaccount->id])}}" method="POST" enctype="multipart/form-data" id="basicform">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="wc-form-group m-0 md:col-span-2">
                        <label class="wc-label" for="payment_method">{{ __('merchant.payment_method') }}</label>
                        <select id="payment_method" name="payment_method" class="wc-select @error('payment_method') is-invalid @enderror">
                            <option>{{ __('merchant.select_payment_method') }}</option>
                            @foreach (\Config::get('merchantpayment.payment_method') as $value)
                                <option value="{{ $value }}"
                                    @if(
                                            $errors->has('bank_name')   ||
                                            $errors->has('holder_name') ||
                                            $errors->has('account_no')  ||
                                            $errors->has('branch_name') ||
                                            $errors->has('routing_no')
                                        )
                                        @if ($value == 'bank')
                                            selected
                                        @endif
                                    @elseif(
                                            $errors->has('mobile_company')  ||
                                            $errors->has('mobile_no')       ||
                                            $errors->has('account_type')
                                            )
                                        @if ($value == 'mobile')
                                            selected
                                        @endif
                                    @elseif ($value == old('payment_method',$editaccount->payment_method))
                                        selected
                                    @endif
                                >{{ __('merchant.'.$value) }}</option>
                            @endforeach
                        </select>
                        @error('payment_method')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                    </div>

                    {{-- bank --}}
                    <div class="wc-form-group m-0 bank @if($errors->has('mobile_no') || $errors->has('mobile_holder_name')) d-none @endif"
                        @if(
                            $errors->has('holder_name') ||
                            $errors->has('branch_name') ||
                            $errors->has('bank_name')   ||
                            $errors->has('account_no')  ||
                            $errors->has('routing_no')
                        )
                        @else
                            @if($editaccount->payment_method == 'bank' ) @else style="display:none" @endif
                        @endif>
                        <label class="wc-label" for="holder_name">{{ __('merchant.holder_name') }} <span class="text-wc-danger">*</span></label>
                        <input id="holder_name" type="text" name="holder_name" data-parsley-trigger="change" placeholder="{{ __('merchantPlaceholder.holder_name') }}" autocomplete="off" class="wc-input" value="{{old('holder_name',isset($editaccount->payment_method)? $editaccount->payment_method == 'bank'?$editaccount->holder_name:'':'')}}" required>
                        @error('holder_name')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                    </div>
                    <div class="wc-form-group m-0 bank @if($errors->has('mobile_no') || $errors->has('mobile_holder_name')) d-none @endif"
                        @if(
                            $errors->has('holder_name') ||
                            $errors->has('branch_name') ||
                            $errors->has('bank_name') ||
                            $errors->has('account_no') ||
                            $errors->has('routing_no')
                            )
                            @else
                                @if($editaccount->payment_method == 'bank' ) @else style="display:none" @endif
                            @endif>
                        <label class="wc-label" for="branch_name">{{ __('merchant.branch_name') }} <span class="text-wc-danger">*</span></label>
                        <input id="branch_name" type="text" name="branch_name" data-parsley-trigger="change" placeholder="{{ __('merchantPlaceholder.branch_name') }}" autocomplete="off" class="wc-input" value="{{old('branch_name',$editaccount->branch_name)}}" required>
                        @error('branch_name')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                    </div>

                    <div class="wc-form-group m-0 mobile @if(
                        $errors->has('holder_name') ||
                        $errors->has('branch_name') ||
                        $errors->has('bank_name') ||
                        $errors->has('account_no') ||
                        $errors->has('routing_no')
                        )
                        d-none
                        @endif" @if($errors->has('mobile_no') || $errors->has('mobile_holder_name')) @else
                            @if($editaccount->payment_method == 'mobile' ) @else style="display:none" @endif
                        @endif>
                        <label class="wc-label" for="mobile_holder_name">{{ __('merchant.holder_name') }} <span class="text-wc-danger">*</span></label>
                        <input id="mobile_holder_name" type="text" name="mobile_holder_name" data-parsley-trigger="change" placeholder="{{ __('merchantPlaceholder.holder_name') }}" autocomplete="off" class="wc-input" value="{{old('mobile_holder_name', isset($editaccount->payment_method)? $editaccount->payment_method == 'mobile'? $editaccount->holder_name:'':'')}}" required>
                        @error('mobile_holder_name')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                    </div>
                    <div class="wc-form-group m-0 mobile @if(
                        $errors->has('holder_name') ||
                        $errors->has('branch_name') ||
                        $errors->has('bank_name') ||
                        $errors->has('account_no') ||
                        $errors->has('routing_no')
                        )
                        d-none
                        @endif" @if($errors->has('mobile_no') || $errors->has('mobile_holder_name')) @else
                            @if($editaccount->payment_method == 'mobile' ) @else style="display:none" @endif
                        @endif>
                        <label class="wc-label" for="mobile_no">{{ __('merchant.mobile_no') }} <span class="text-wc-danger">*</span></label>
                        <input id="mobile_no" type="text" name="mobile_no" data-parsley-trigger="change" placeholder="{{ __('merchantPlaceholder.mobile_number') }}" autocomplete="off" class="wc-input" value="{{old('mobile_no',$editaccount->mobile_no)}}" required>
                        @error('mobile_no')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                    </div>
                    {{-- end mobile --}}

                    {{-- bank --}}
                    <div class="wc-form-group m-0 bank @if($errors->has('mobile_no') || $errors->has('mobile_holder_name')) d-none @endif"
                        @if(
                        $errors->has('bank_name') ||
                        $errors->has('account_no') ||
                        $errors->has('routing_no')
                        )
                        @else
                            @if($editaccount->payment_method == 'bank' ) @else style="display:none" @endif
                        @endif>
                        <label class="wc-label" for="bank_name">{{ __('merchant.select_bank') }} <span class="text-wc-danger">*</span></label>
                        <select id="bank_name" name="bank_name" class="wc-select @error('bank_name') is-invalid @enderror">
                            @foreach ($banks as $bank)
                                <option value="{{$bank->id}}">{{ $bank->name }}</option>
                            @endforeach
                        </select>
                        @error('bank_name')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                    </div>
                    <div class="wc-form-group m-0 bank @if($errors->has('mobile_no') || $errors->has('mobile_holder_name')) d-none @endif"
                        @if(
                        $errors->has('bank_name') ||
                        $errors->has('account_no') ||
                        $errors->has('routing_no')
                        )
                        @else
                            @if($editaccount->payment_method == 'bank' ) @else style="display:none" @endif
                        @endif>
                        <label class="wc-label" for="account_no">{{ __('merchant.account_no') }} <span class="text-wc-danger">*</span></label>
                        <input id="account_no" type="text" name="account_no" data-parsley-trigger="change" placeholder="{{ __('merchantPlaceholder.account_number') }}" autocomplete="off" class="wc-input" value="{{old('account_no',$editaccount->account_no)}}" required>
                        @error('account_no')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                    </div>
                    <div class="wc-form-group m-0 bank md:col-span-2 @if($errors->has('mobile_no') || $errors->has('mobile_holder_name')) d-none @endif"
                        @if(
                        $errors->has('bank_name') ||
                        $errors->has('account_no') ||
                        $errors->has('routing_no')
                        )
                        @else
                            @if($editaccount->payment_method == 'bank' ) @else style="display:none" @endif
                        @endif>
                        <label class="wc-label" for="routing_no">{{ __('merchant.routing_no') }} <span class="text-wc-danger">*</span></label>
                        <input id="routing_no" type="text" name="routing_no" data-parsley-trigger="change" placeholder="{{ __('merchantPlaceholder.routing_number') }}" autocomplete="off" class="wc-input" value="{{old('routing_no',$editaccount->routing_no)}}" required>
                        @error('routing_no')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                    </div>
                    {{-- mobile --}}
                    <div class="wc-form-group m-0 mobile
                        @if(
                            $errors->has('holder_name') ||
                            $errors->has('branch_name') ||
                            $errors->has('bank_name') ||
                            $errors->has('account_no') ||
                            $errors->has('routing_no')
                            )
                        d-none
                        @endif"
                        @if($errors->has('mobile_no') || $errors->has('mobile_holder_name'))
                        @else
                            @if($editaccount->payment_method == 'mobile' )
                            @else
                                style="display:none"
                            @endif
                        @endif>
                        <label class="wc-label" for="mobile_company">{{ __('merchant.select_mobile_company') }} <span class="text-wc-danger">*</span></label>
                        <select id="mobile_company" name="mobile_company" class="wc-select @error('mobile_company') is-invalid @enderror">
                            @foreach ($mobile_banks as $bank)
                                <option value="{{$bank->id}}">{{ $bank->name }}</option>
                            @endforeach
                        </select>
                        @error('mobile_company')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                    </div>
                    <div class="wc-form-group m-0 mobile
                        @if(
                            $errors->has('holder_name') ||
                            $errors->has('branch_name') ||
                            $errors->has('bank_name') ||
                            $errors->has('account_no') ||
                            $errors->has('routing_no')
                            )
                        d-none
                        @endif"
                        @if($errors->has('mobile_no') || $errors->has('mobile_holder_name'))
                        @else
                            @if($editaccount->payment_method == 'mobile' )
                            @else
                                style="display:none"
                            @endif
                        @endif>
                        <label class="wc-label" for="account_type">{{ __('merchant.account_type') }} <span class="text-wc-danger">*</span></label>
                        <select id="account_type" name="account_type" class="wc-select @error('account_type') is-invalid @enderror">
                            @foreach (\Config::get('merchantpayment.account_types') as $value)
                                <option value="{{ __('merchant.'.$value) }}" @if (__('merchant.'.$value) == $editaccount->account_type)
                                    selected
                                @endif>{{ __('merchant.'.$value) }}</option>
                            @endforeach
                        </select>
                        @error('account_type')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                    </div>
                </div>

                <div class="bank-mobile" @if ($errors->any()) @else @if($editaccount->payment_method) @else style="display:none" @endif @endif>
                    <div class="flex items-center gap-2 mt-5">
                        <button type="submit" class="wc-btn wc-btn-primary"><i class="fa fa-check text-[12px]"></i> {{ __('levels.save_change') }}</button>
                        <a href="{{ route('merchant.accounts.payment-account.index') }}" class="wc-btn wc-btn-outline">{{ __('levels.cancel') }}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection()

@push('scripts')
<script src="{{ static_asset('backend/js/merchant_panel/payment_account.js') }}"></script>
@endpush