<section class="container-fluid py-3 pb-0">
    <div class="mb-0">
        <div class="mb-3">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <span class="section-eyebrow d-inline-flex align-items-center gap-2 mb-3">
                        <i class="fas fa-handshake"></i>
                        {{ __('levels.partners_eyebrow') }}
                    </span>
                    <h3 class="display-6 title text-center mb-3">
                        <span class="section-title">{{ __('levels.our_partner') }}</span>
                    </h3>
                    <p class="section-subtitle mb-0">{{ __('levels.partners_subtitle') }}</p>
                </div>
            </div>
        </div>
        <div class="container partner py-4">
            <div class="swiper">
                <div class="swiper-wrapper ">
                    <!-- Slides -->
                    @foreach ($partners as $partner)    
                        <div class="swiper-slide">
                            <div class="partner-card">
                                <a href="{{ @$partner->link }}" class="d-inline-block" target="_blank">
                                    <img src="{{ @$partner->image }}" class="partner-logo" alt="{{ @$partner->name }}" />
                                </a>
                            </div>
                        </div>    
                    @endforeach
                </div>
                <div class="swiper-pagination position-relative mt-5"></div>
            </div>
        </div> 
    </div>
</section>