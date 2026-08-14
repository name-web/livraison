@extends(active_theme() . '.layouts.master')
@section('title')
    {{ @$page->title }} | {{ settings()->name}}
@endsection
@section('content') 
<section class="container-fluid pb-5">
    <div class="container pt-5 pb-5">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <span class="section-eyebrow d-inline-flex align-items-center gap-2 mb-3">
                    <i class="fas fa-circle-question"></i>
                    {{ __('levels.faq_eyebrow') }}
                </span>
                <h3 class="display-6 title text-center mb-3"><span class="section-title">{{ @$page->title }}</span></h3>
                <p class="section-subtitle mb-0">{{ @$page->description }}</p>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10">
                <div class="faq accordion accordion-flush faq-modern" id="accordionPanelsStayOpenExample">

                    @foreach ($faqs as $key=>$faq)     
                        <div class="accordion-item faq-modern-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-{{ $key }}" aria-expanded="false" aria-controls="panelsStayOpen-{{ $key }}">
                                    <span class="faq-number">{{ str_pad($key + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                    <span class="faq-question">{{ @$faq->question }}</span>
                                </button>
                            </h2>
                        <div id="panelsStayOpen-{{ $key }}" class="accordion-collapse collapse" data-bs-parent="#accordionPanelsStayOpenExample">
                            <div class="accordion-body"> 
                                {!! $faq->answer !!}
                            </div>
                        </div>
                        </div>
                    @endforeach 
                    <div class="mt-5">
                         {{ $faqs->links() }}
                    </div>
                  </div>
            </div>
        </div>
    </div>
</section>  
@endsection