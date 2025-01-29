<div id="services"></div>
<!-- Section Services Start -->
<section class="section mb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="section-title">
                    <div class="divider mb-3"></div>
                    <h2>Preços e benefícios</h2>
                    <p>Os preços indicados são para uma pessoa. As sessões são em blocos de 30 minutos.</p>
                </div>
            </div>
        </div>

        <div class="row no-gutters">
            @foreach ($services as $service)
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="text-center  px-4 py-5 hover-style-1">
                    <ion-icon name="{{ $service->icon }}" class="text-lg text-color"></ion-icon>
                    <h4 class="mt-3 mb-4 text-uppercase">{{ $service->title }}</h4>
                    <h1>{{ $service->text }}</h1>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
<!-- Section Services End -->