<!-- footer Start -->
<footer class="footer bg-black-50">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-5 mb-lg-0">
                <img src="/assets/website/images/logo-dark-v2.png" alt="gymspot" class="logo mb-4">
                <p>No Gym Spot, desfrute de ginásios privados únicos abertos 24/7, para treinar à sua maneira, quando
                    quiser. </p>
                <p>Priorizamos privacidade e comodidade, promovendo um estilo de vida ativo e saudável.</p>
                <p>Experimente um treino personalizado e acessível em vários locais.</p>
            </div>

            <div class="col-lg-5 col-md-6 mb-5 mb-lg-0">
                <div class="footer-widget">
                    <h4 class="mb-4 text-white letter-spacing text-uppercase">Úteis</h4>
                    <ul class="list-unstyled footer-menu lh-40 mb-0">
                        @foreach ($uteis as $util)
                        <li><a href="/cms/{{ $util->id }}/{{ Str::slug($util->title) }}"><i class="ti-angle-double-right mr-2"></i>{{ $util->title }}</a></li>    
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-md-5">
                <div class="footer-widget">
                    <h4 class="mb-4 text-white letter-spacing text-uppercase">Localização</h4>
                    <p>Via do Oriente, n.º 14 Parque das Nações<br>Lisboa | Porto</p>
                    
                    <span class="text-white">pm@gymspot.pt</span>
                </div>
            </div>
        </div>

        <div class="row align-items-center mt-5 px-3 bg-black mx-1">
            <div class="col-lg-4">
                <p class="text-white mt-3">GymSpot © {{ date('Y') }} , Developed by <a href="https://netlook.pt"
                        class="text-color">Netlook.pt</a></p>
            </div>
            <div class="col-lg-6 ml-auto text-right">
                <ul class="list-inline mb-0 footer-socials">
                    <li class="list-inline-item"><a href="#"><i class="ti-facebook"></i></a></li>
                    <li class="list-inline-item"><a href="#"><i class="ti-twitter"></i></a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>
<!-- Section Footer End -->