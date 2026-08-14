<section id="pricing" class="container-fluid py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-lg-8 m-auto text-center">
                <span class="section-eyebrow d-inline-flex align-items-center gap-2 mb-3">
                    <i class="fas fa-tags"></i>
                    {{ __('levels.pricing_eyebrow') }}
                </span>
                <h3 class="display-6 title text-center mb-3"><span class="section-title">{{ settings()->name }} {{ __('levels.pricing') }}</span></h3>
                <p class="section-subtitle mb-0">{{ __('levels.pricing_subtitle') }}</p>
            </div>
        </div>
        <div class="row py-2 align-items-center">
            <div class="col-12" aria-label="breadcrumb">
                <ul class="nav nav-pills pricing justify-content-center mb-5 breadcrumb" id="pills-tab" role="tablist">
                    <li class="nav-item breadcrumb-item" role="presentation">
                      <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true"><i class="fas fa-bolt me-2"></i>{{ __('levels.same_day') }}</button>
                    </li>
                    <li class="nav-item breadcrumb-item" role="presentation">
                      <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false"><i class="fas fa-calendar-day me-2"></i>{{ __('levels.next_day') }}</button>
                    </li>
                    <li class="nav-item breadcrumb-item" role="presentation">
                      <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact" aria-selected="false"><i class="fas fa-city me-2"></i>{{ __('levels.sub_city') }}</button>
                    </li>
                    <li class="nav-item breadcrumb-item" role="presentation">
                      <button class="nav-link" id="pills-disabled-tab" data-bs-toggle="pill" data-bs-target="#pills-disabled" type="button" role="tab" aria-controls="pills-disabled" aria-selected="false"><i class="fas fa-truck me-2"></i>{{ __('levels.outside_city') }}</button>
                    </li>
                  </ul>
                  <div class="tab-content charge-content" id="pills-tabContent">
                    <div class="tab-pane show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                        <div class="row justify-content-center">
                            @foreach ($pricing as $samedayPrice)
                                <div class="col-6 col-sm-4 col-lg-2 charge-col mb-3">
                                    <div class="text-center charge-item p-3 h-100 reveal" style="transition-delay: {{ $loop->index * 50 }}ms">
                                        <span class="charge-weight">{{ __('levels.up_to') }} {{ $samedayPrice->weight }} kg</span>
                                        <span class="charge-category">{{ @$samedayPrice->category->title }}</span>
                                        <h3 class="charge-price font-weight-bold mb-0">{{ settings()->currency }} {{ $samedayPrice->same_day }}</h3>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="tab-pane" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                        <div class="row justify-content-center">
                            @foreach ($pricing as $nextdayPrice)
                                <div class="col-6 col-sm-4 col-lg-2 charge-col mb-3">
                                    <div class="text-center charge-item p-3 h-100 reveal" style="transition-delay: {{ $loop->index * 50 }}ms">
                                        <span class="charge-weight">{{ __('levels.up_to') }} {{ $nextdayPrice->weight }} kg</span>
                                        <span class="charge-category">{{ @$nextdayPrice->category->title }}</span>
                                        <h3 class="charge-price font-weight-bold mb-0">{{ settings()->currency }} {{ $nextdayPrice->next_day }}</h3>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="tab-pane" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab" tabindex="0">
                        <div class="row justify-content-center">
                            @foreach ($pricing as $subcityPrice)
                                <div class="col-6 col-sm-4 col-lg-2 charge-col mb-3">
                                    <div class="text-center charge-item p-3 h-100 reveal" style="transition-delay: {{ $loop->index * 50 }}ms">
                                        <span class="charge-weight">{{ __('levels.up_to') }} {{ $subcityPrice->weight }} kg</span>
                                        <span class="charge-category">{{ @$subcityPrice->category->title }}</span>
                                        <h3 class="charge-price font-weight-bold mb-0">{{ settings()->currency }} {{ $subcityPrice->sub_city }}</h3>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="tab-pane" id="pills-disabled" role="tabpanel" aria-labelledby="pills-disabled-tab" tabindex="0">
                        <div class="row justify-content-center">
                            @foreach ($pricing as $outsidecityPrice)
                                <div class="col-6 col-sm-4 col-lg-2 charge-col mb-3">
                                    <div class="text-center charge-item p-3 h-100 reveal" style="transition-delay: {{ $loop->index * 50 }}ms">
                                        <span class="charge-weight">{{ __('levels.up_to') }} {{ $outsidecityPrice->weight }} kg</span>
                                        <span class="charge-category">{{ @$outsidecityPrice->category->title }}</span>
                                        <h3 class="charge-price font-weight-bold mb-0">{{ settings()->currency }} {{ $outsidecityPrice->outside_city }}</h3>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                  </div>
            </div>
        </div>
    </div>
</section>