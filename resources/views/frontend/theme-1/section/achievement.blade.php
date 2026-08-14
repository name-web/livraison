<section class="py-5">
    <div class="container mb-4">
        <div class="row">
            <div class="col-lg-8 m-auto text-center">
                <span class="section-eyebrow d-inline-flex align-items-center gap-2 mb-3">
                    <i class="fas fa-chart-line"></i>
                    {{ __('levels.achievement_eyebrow') }}
                </span>
                <h3 class="display-6 title text-center mb-3">
                    <span class="section-title">{{ __('levels.happy_achievement') }}</span>
                </h3>
                <p class="section-subtitle mb-0">{{ __('levels.achievement_subtitle') }}</p>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="bg-primary achievement-band">
            <div class="container-fluid">
                <div class="container">
                    <div class="row py-4 align-items-center">
                        <div class="col-lg-3 col-sm-6 text-center reveal" style="transition-delay: 0ms">
                            <div class="d-flex align-items-center justify-content-center achievement">
                                <div class="text-center mb-0 icon">
                                     <i class="{{ section(\App\Enums\SectionType::ACHIEVEMENT,'branch_icon') }}"></i>
                                </div>
                                <div class="icon-text">
                                    <h2 class="mb-0 font-weight-bold branch-title"><span class="odometer" data-count="{{ section(\App\Enums\SectionType::ACHIEVEMENT,'branch_count') }}">0</span><span class="odometer-position"></span></h2>
                                    <p class="branch-title mb-0">{{ section(\App\Enums\SectionType::ACHIEVEMENT,'branch_title') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 text-center reveal" style="transition-delay: 100ms">
                            <div class="d-flex align-items-center justify-content-center achievement">
                                <div class="text-center mb-0 icon">
                                     <i class="{{ section(\App\Enums\SectionType::ACHIEVEMENT,'parcel_icon') }}"></i>
                                </div>
                                <div class="icon-text">
                                    <h2 class="mb-0 font-weight-bold branch-title">
                                        <span class="odometer" data-count="{{ section(\App\Enums\SectionType::ACHIEVEMENT,'parcel_count') }}">0</span><span class="odometer-position"></span>
                                    </h2>
                                    <p class="branch-title mb-0">{{ section(\App\Enums\SectionType::ACHIEVEMENT,'parcel_title') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 text-center reveal" style="transition-delay: 200ms">
                            <div class="d-flex align-items-center justify-content-center achievement">
                                <div class="text-center mb-0 icon">
                                     <i class="{{ section(\App\Enums\SectionType::ACHIEVEMENT,'merchant_icon') }}"></i>
                                </div>
                                <div class="icon-text">
                                    <h2 class="mb-0 font-weight-bold branch-title">
                                        <span class="odometer" data-count="{{ section(\App\Enums\SectionType::ACHIEVEMENT,'merchant_count') }}">0</span><span class="odometer-position"></span>
                                    </h2>
                                    <p class="branch-title mb-0">{{ section(\App\Enums\SectionType::ACHIEVEMENT,'merchant_title') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 text-center reveal" style="transition-delay: 300ms">
                            <div class="d-flex align-items-center justify-content-center achievement">
                                <div class="text-center mb-0 icon">
                                     <i class="{{ section(\App\Enums\SectionType::ACHIEVEMENT,'reviews_icon') }}"></i>
                                </div>
                                <div class="icon-text">
                                    <h2 class="mb-0 font-weight-bold branch-title">
                                        <span class="odometer" data-count="{{ section(\App\Enums\SectionType::ACHIEVEMENT,'reviews_count') }}">0</span>
                                        <span class="odometer-position"></span>
                                    </h2>
                                    <p class="branch-title mb-0">{{ section(\App\Enums\SectionType::ACHIEVEMENT,'reviews_title') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>