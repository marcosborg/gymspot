<div id="about"></div>
@foreach ($abouts as $about)
<!-- Section About start -->
<section class="section about">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <img src="{{ $about->image ? $about->image->getUrl() : 'assets/website/images/bg/bg-5.jpg' }}" alt=""
                    class="img-fluid rounded shadow w-100">
            </div>

            <div class="col-lg-6">
                <div class="pl-3 mt-5 mt-lg-0">
                    <h2 class="mt-1 mb-3">{{ $about->title }}</h2>
                    <p class="mb-4 text-justify">{{ $about->text }}</p>
                    <a href="/{{ $about->link }}" class="btn btn-main">{{ $about->button }}<i
                            class="fa fa-angle-right ml-2"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Section About End -->
@endforeach