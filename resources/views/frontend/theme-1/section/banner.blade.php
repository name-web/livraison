<!-- banner -->
@php
    $bannerBg = section(\App\Enums\SectionType::BANNER,'banner_theme_1') ?: static_asset('frontend/images/hero-abidjan.jpg');
    $heroBg   = "linear-gradient(100deg, rgba(15,23,42,0.90) 0%, rgba(15,23,42,0.75) 45%, rgba(15,23,42,0.35) 80%, rgba(15,23,42,0.05) 100%), url('{$bannerBg}')";
@endphp
<section class="hero-horizontal" style="background-image: {{ $heroBg }};">
    <div class="container pt-5">
        <div class="row align-items-center g-4 g-lg-5">
            <div class="col-lg-6 text-center text-lg-start">
                <span class="hero-badge d-inline-flex align-items-center gap-2 mb-4">
                    <i class="fas fa-truck-fast"></i>
                    {{ __('levels.hero_badge') }}
                </span>
                <h1 class="banner-hilight display-3 text-uppercase text-white">
                    <span>{{ section(\App\Enums\SectionType::BANNER,'title_1') }}</span><br/>
                    <span class="hero-accent">{{ section(\App\Enums\SectionType::BANNER,'title_2') }}</span><br/>
                    <span>{{ section(\App\Enums\SectionType::BANNER,'title_3') }}</span>
                </h1>
                <p class="fs-5 banner-subtitle text-white mt-4 mb-0">{{ section(\App\Enums\SectionType::BANNER,'sub_title') }}</p>
            </div>
            <div class="col-lg-6">
                <div class="hero-track-card">
                    <div class="hero-track-card-head">
                        <span class="hero-track-icon"><i class="fas fa-box-open"></i></span>
                        <div>
                            <strong>{{ __('levels.hero_track_title') }}</strong>
                            <small>{{ __('levels.hero_track_hint') }}</small>
                        </div>
                    </div>
                    <form action="{{ route('tracking.index') }}" method="get" class="hero-tracking-form">
                        <div class="input-group">
                            <i class="fas fa-hashtag"></i>
                            <input type="text" class="form-control" placeholder="{{ __('levels.enter_tracking_id') }}" name="tracking_id" aria-label="{{ __('levels.enter_tracking_id') }}">
                            <button type="submit" class="btn btn-primary">{{ __('levels.track_now') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="hero-strip">
        <div class="container">
            <div class="row">
                <div class="col-md-4 hero-strip-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <strong>{{ __('levels.hero_trust_area_title') }}</strong>
                        <span>{{ __('levels.hero_trust_area_text') }}</span>
                    </div>
                </div>
                <div class="col-md-4 hero-strip-item">
                    <i class="fas fa-headset"></i>
                    <div>
                        <strong>{{ __('levels.hero_trust_support_title') }}</strong>
                        <span>{{ settings()->phone }}</span>
                    </div>
                </div>
                <div class="col-md-4 hero-strip-item">
                    <i class="fas fa-shield-alt"></i>
                    <div>
                        <strong>{{ __('levels.hero_trust_safe_title') }}</strong>
                        <span>{{ __('levels.hero_trust_safe_text') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>