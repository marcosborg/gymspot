<!-- Section Menu Start -->
<!-- Header Start -->
<nav class="navbar navbar-expand-lg navigation fixed-top" id="navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="\">
            <img src="/assets/website/images/logo-dark-v3.png?v2" alt="gymspot" class="logo">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsid"
            aria-controls="navbarsid" aria-expanded="false" aria-label="Toggle navigation">
            <span class="ti-view-list"></span>
        </button>
        <div class="collapse text-center navbar-collapse" id="navbarsid">
            <ul class="navbar-nav mx-auto">
                @foreach ($menus as $menu)
                <li class="nav-item active">
                    <a class="nav-link" href="{{ $menu->link }}">{{ $menu->name }} <span class="sr-only">(current)</span></a>
                </li>
                @endforeach
            </ul>
            <div class="my-md-0 ml-lg-4 mt-4 mt-lg-0 ml-auto text-lg-right mb-3 mb-lg-0">
                <a href="mailto:pm@gymspot.pt">
                    <h4 class="text-color mb-0">pm@gymspot.pt</h4>
                </a>
            </div>
        </div>
    </div>
</nav>
<!-- Header Close -->