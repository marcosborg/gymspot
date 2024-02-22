@if ($galleries)
<!-- Section Gallery Start -->
<section class="gallery">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="section-title">
                    <div class="divider mb-3"></div>
                    <h2>Our gallery</h2>
                    <p>We offer more than 35 group exercis, aerobic classes each week.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid p-0">
        <div class="row no-gutters portfolio-gallery">
            @foreach ($galleries as $gallery)
            <div class="col-lg-3 col-md-6 col-sm-6">
                <a href="{{ $gallery->image->getUrl() }}" class="popup-gallery">
                    <img src="{{ $gallery->image->getUrl() }}" alt="" class="img-fluid">
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Section Gallery END -->
@endif