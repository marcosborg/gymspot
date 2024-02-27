<div id="intro"></div>
<!-- Section Intro Start -->
<section class="mt-80px">
    <div class="container">
        <div class="row">
            @foreach ($steps as $step)
            <div class="col-lg-4 col-md-6">
                <div class="card p-5 border-0 rounded-top border-bottom position-relative hover-style-1">
                    <span class="number">{{ str_pad($step->number, 2, '0', STR_PAD_LEFT) }}</span>
                    <h3 class="mt-3">{{ $step->title }}</h3>
                    <p class="mt-3 mb-4">{{ $step->text }}</p>
                    <a href="{{ $step->link }}"
                        class="text-color text-uppercase font-size-13 letter-spacing font-weight-bold"><i
                            class="ti-minus mr-2 "></i>{{ $step->button }}</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
<!-- Section Intro End -->