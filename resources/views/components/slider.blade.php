@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
    .swiper {
        width: 100%;
        height: 80vh;
        background: #000;
    }

    .swiper-button-next,
    .swiper-button-prev {
        color: #ffffff;
    }

    .swiper-pagination-bullet {
        background: #ffffff;
    }

</style>
@endsection
@section('scripts')
<script type="module">
    import Swiper from 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.mjs'
    const swiper = new Swiper('.swiper', {
        // Optional parameters
        loop: true,
        autoplay: true,
        speed: 400,
        spaceBetween: 5,
        effect: "fade",
        // If we need pagination
        pagination: {
            el: '.swiper-pagination',
            dynamicBullets: true,
        },

        // Navigation arrows
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },

        // And if we need scrollbar
        scrollbar: {
            el: '.swiper-scrollbar',
        },
    });

</script>
@endsection
<div class="swiper">
    <div class="swiper-wrapper">
        @foreach ($sliders as $slider)
        <div class="swiper-slide">
            <section class="slider" style="background: url('{{ $slider->image->getUrl() }}') no-repeat center center">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <span class="h6 d-inline-block mb-4 subhead">O seu ginásio privado</span>
                            <h1 class="text-uppercase text-white mb-5">{{ $slider->title }}<br><span class="text-color">{{
                                    $slider->subtitle }}</span></h1>
                            <div>
                                <a href="https://play.google.com/store/apps/details?id=pt.gymspot.app" class="btn btn-main ">Google Play <i class="ti-angle-right ml-3"></i></a>
                                <a href="https://apps.apple.com/pt/app/gymspot/id6479336982" class="btn btn-main ">App Store <i class="ti-angle-right ml-3"></i></a>
                                <img src="/assets/website/images/logos-stores.png" width="450" class="pl-5 img-responsive float-right">
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        @endforeach
    </div>
    <!-- If we need pagination -->
    <div class="swiper-pagination"></div>

    <!-- If we need navigation buttons -->
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>

    <!-- If we need scrollbar -->
    <div class="swiper-scrollbar"></div>
</div>
