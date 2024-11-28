@if ($galleries->count() > 0)
<!-- Section Gallery Start -->
<section class="gallery mt-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="section-title">
                    <div class="divider mb-3"></div>
                    <h2>Os nossos espaços</h2>
                    <p>Venha conhecer tudo o que temos para oferecer.</p>
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