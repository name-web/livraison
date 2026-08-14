@php
    $galleryImages = [];
    $galleryEmpty  = true;
    for ($i = 1; $i <= 7; $i++) {
        $galleryImage = section(\App\Enums\SectionType::GALLERY, 'gallery_image_'.$i);
        if($galleryImage) {
            $galleryImages[] = $galleryImage;
            $galleryEmpty = false;
        }
    }
    if ($galleryEmpty) {
        $galleryImages = [];
        for ($i = 1; $i <= 7; $i++) {
            $galleryImages[] = static_asset('frontend/images/gallery/gallery-'.$i.'.jpg');
        }
    }
@endphp
@if(count($galleryImages))
<section class="container-fluid pb-5">
    <div class="container pt-5 pb-5">
        <div class="row justify-content-center mb-4">
            <div class="col-lg-8 text-center">
                <span class="section-eyebrow d-inline-flex align-items-center gap-2 mb-3">
                    <i class="fas fa-images"></i>
                    {{ section(\App\Enums\SectionType::GALLERY,'gallery_badge') ?: 'Galerie' }}
                </span>
                <h3 class="display-6 title text-center mb-3"><span class="section-title">{{ section(\App\Enums\SectionType::GALLERY,'gallery_title') ?: 'Notre galerie de livraisons' }}</span></h3>
                <p class="section-subtitle mb-0">{{ section(\App\Enums\SectionType::GALLERY,'gallery_description') ?: 'Découvrez nos livraisons réussies, nos opérations de coursier et nos moments d\'expédition réels.' }}</p>
            </div>
        </div>
        <div class="row g-4 mt-3">
            @foreach($galleryImages as $galleryImage)
                <div class="col-lg-4 col-sm-6 reveal" style="transition-delay: {{ $loop->index * 60 }}ms">
                    <div class="gallery-item">
                        <img src="{{ $galleryImage }}" alt="{{ settings()->name }}" class="w-100">
                        <div class="gallery-overlay">
                            <span class="gallery-zoom"><i class="fas fa-search-plus"></i></span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif