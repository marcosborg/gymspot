<div id="course"></div>
<!-- Section Course Start -->
<section class="section course bg-gray">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="section-title">
                    <div class="divider mb-3"></div>
                    <h2>Onde estamos?</h2>
                    <p>Queremos chegar à tua localidade.</p>
                </div>
            </div>
        </div>

        <div class="row">
            @foreach ($locations as $location)
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 rounded-0 p-0 mb-5 mb-lg-0 shadow-sm">
                    @if ($location->image)
                    <img src="{{ $location->image->getUrl() }}" alt="" class="img-fluid">
                    @endif
                    <div class="card-body">
                        <a href="course-single.html">
                            <h4 class="font-secondary mb-0">{{ $location->title }}</h4>
                        </a>
                        <p class=" mb-2">{{ $location->subtitle }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
<!-- Section Course ENd -->