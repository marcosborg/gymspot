<!-- Section Services Start -->
<section class="section services ">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="section-title">
                    <div class="divider mb-3"></div>
                    <h2>Our Services</h2>
                    <p>We offer more than 35 group exercis, aerobic classes each week.</p>
                </div>
            </div>
        </div>

        <div class="row no-gutters">
            @foreach ($services as $service)
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="text-center  px-4 py-5 hover-style-1">
                    <i class="{{ $service->icon }} text-lg text-color"></i>
                    <h4 class="mt-3 mb-4 text-uppercase">{{ $service->title }}</h4>
                    <p>{{ $service->text }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
<!-- Section Services End -->