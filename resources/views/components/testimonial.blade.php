@if ($testimonials->count() > 0)
<!-- Section Testimonial Start -->
<section class="section textimonial position-relative bg-3">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="row justify-content-center">
                    <div class="col-lg-12 text-center">
                        <div class="section-title">
                            <div class="divider mb-3"></div>
                            <h2 class="text-white">What People say</h2>
                        </div>
                    </div>
                </div>

                <div class="testimonial-slider">
                    @foreach ($testimonials as $testimonial)
                    <div class="text-center mb-4 ">
                        <i class="ti-quote-left text-lg text-color"></i>
                        <h3 class="mt-4 text-white letter-spacing">{{ $testimonial->title }}</h3>
                        <p class="my-4 text-white-50">{{ $testimonial->text }}</p>

                        <div>
                            <h4 class="mb-0 text-capitalize text-white font-weight-normal">{{ $testimonial->name }}</h4>
                            <span class="text-white-50">{{ $testimonial->position }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Section Testimonial END -->
@endif