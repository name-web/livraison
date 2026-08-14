<section class="container-fluid py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-lg-8 m-auto text-center">
                <span class="section-eyebrow d-inline-flex align-items-center gap-2 mb-3">
                    <i class="fas fa-star"></i>
                    {{ __('levels.why_eyebrow') }}
                </span>
                <h3 class="display-6 title text-center mb-3"><span class="section-title">{{ __('levels.why') }} {{ settings()->name }} ?</span></h3>
                <p class="section-subtitle mb-0">{{ __('levels.why_subtitle') }}</p>
            </div>
        </div>
        <div class="row py-2">
            @foreach ($whycouriers as $whycourier)
                <div class="col-lg-4 col-sm-6 mb-4 text-center why-courier-col">
                    <div class="whycourier-item p-4 h-100 reveal" style="transition-delay: {{ $loop->index * 70 }}ms">
                        <div class="text-center whycourier-box mb-0">
                            <div class="whycourier-icon">
                                <img src="{{ $whycourier->image }}" width="100%"/>
                            </div>
                        </div>
                        <h5 class="mt-4 mb-0 font-weight-bold">{{ $whycourier->title }}</h5>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>