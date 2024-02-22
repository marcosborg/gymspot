@foreach ($calls as $call)
<!-- section Call To action start -->
<section class="section cta" style="background: url({{ $call->image->getUrl() }}) fixed 50% 50% no-repeat;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-12 col-sm-12">
                <div class="text-center">
                    <span class="h6 letter-spacing text-white">{{ $call->subtitle }}</span>
                    <h2 class="text-lg mt-4 mb-5 text-white">{{ $call->title }}</h2>

                    <a href="{{ $call->link }}" class="btn btn-main text-white">{{ $call->button }}</a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- section Call To action start -->
@endforeach