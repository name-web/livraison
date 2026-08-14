<section class="container-fluid py-3 pb-0">
    <div class="mb-5">
        <div class="mb-3">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <span class="section-eyebrow d-inline-flex align-items-center gap-2 mb-3">
                        <i class="fas fa-newspaper"></i>
                        {{ __('levels.blogs_eyebrow') }}
                    </span>
                    <h3 class="display-6 title text-center mb-3">
                        <span class="section-title">{{ __('levels.blogs') }}</span>
                    </h3>
                    <p class="section-subtitle mb-0">{{ __('levels.blogs_subtitle') }}</p>
                </div>
            </div>
        </div>
        <div class="container py-4">
            <div class="row row-cols-1 row-cols-md-3 g-4 blogs">
                 
                @foreach ($blogs as $key=>$blog)  
                    <div class="col-lg-4 reveal" style="transition-delay: {{ $loop->index * 70 }}ms">
                        <div class="card h-100 blog-card">
                            <a href="{{ route('blog.details',$blog->id) }}" class="blog-image-wrap">
                                @php $blogImage = $blog->upload ? static_asset($blog->upload->original) : static_asset('frontend/images/blog/blog-'.($key+1).'.jpg'); @endphp
                                <img src="{{ $blogImage }}" class="card-img-top" alt="{{ $blog->title }}">
                                <span class="blog-date"><i class="fas fa-calendar-alt me-1"></i>{{ $blog->updated_at->format('d M Y') }}</span>
                            </a>
                            <div class="card-body">
                                <a href="{{ route('blog.details',$blog->id) }}" class="text-decoration-none"><h4 class="card-title">{{ $blog->title }}</h4></a> 
                            </div>
                            <div class="card-footer pb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="card-text mb-0">
                                        <i class="fa fa-user me-2"></i><small class="text-body-secondary">{{ $blog->user->name }}</small>
                                    </p>
                                    <p class="card-text mb-0">
                                        <i class="fa fa-eye me-2"></i><small class="text-body-secondary me-2">{{ $blog->views}}</small>
                                        <i class="fa fa-calendar me-2"></i><small class="text-body-secondary d-none">{{ $blog->updated_at->format('d M Y')}}</small>
                                    </p>
                                </div>
                                <a href="{{ route('blog.details',$blog->id) }}" class="blog-read-more">Lire la suite <i class="fas fa-arrow-right ms-1"></i></a>
                            </div>
                        </div> 
                    </div>  
                @endforeach 
             </div>
        </div>
    </div>
</section>