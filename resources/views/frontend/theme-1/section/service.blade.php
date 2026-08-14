<section class="container-fluid py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-lg-8 m-auto text-center">
                <span class="section-eyebrow d-inline-flex align-items-center gap-2 mb-3">
                    <i class="fas fa-truck-fast"></i>
                    {{ __('levels.services_eyebrow') }}
                </span>
                <h3 class="display-6 title text-center mb-3"><span class="section-title">{{ __('levels.our_services') }}</span></h3>
                <p class="section-subtitle mb-0">{{ __('levels.services_subtitle') }}</p>
            </div>
        </div>
        <div class="row py-2">
            @foreach ( $services as $service)
                <div class="col-lg-3 col-sm-6 mb-4 text-center">
                    <div class="service-item p-4 h-100 reveal" style="transition-delay: {{ $loop->index * 70 }}ms">
                        <div class="service-item-box d-block">
                            <div class="my-3">
                                <div class="service-box">
                                    <div class="icon-box d-flex">
                                        <img src="{{ $service->image }}" width="100%"/>
                                    </div>
                                </div>
                            </div>
                            <h5 class="mb-3 font-weight-bold">{{ $service->title }}</h5>
                            <p class="mb-3">{!! \Str::limit(strip_tags($service->description), 120) !!}</p>
                            <a href="{{ route('service.details', $service->id) }}" class="service-link">
                                {{ __('levels.read_more') }} <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>