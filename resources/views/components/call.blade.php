@foreach ($calls as $call)
<!-- section Call To action start -->
<section class="section cta" style="background: url({{ $call->image->getUrl() }}) fixed 50% 50% no-repeat;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-12 col-sm-12">
                <div class="text-center">
                    <h2 class="text-lg mt-4 mb-3 text-white">{{ $call->title }}</h2>
                    <span class="h3 letter-spacing text-white">{{ $call->subtitle }}</span><br>
                    <a href="https://play.google.com/store/apps/details?id=pt.gymspot.app" class="btn btn-main text-white mt-4">Google Play</a>
                    <a href="https://apps.apple.com/pt/app/gymspot/id6479336982" class="btn btn-main text-white mt-4">App Store</a><br>
                    <img src="/assets/website/images/logos-stores.png" width="450" class="mt-4 img-responsive">
                </div>
            </div>
        </div>
    </div>
</section>
<!-- section Call To action start -->
@endforeach