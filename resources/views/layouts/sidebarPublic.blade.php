<div class="sidebar">
    <div class="logo text-center p-3">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" style="max-width:150px;">
    </div>
    <ul class="menu list-unstyled">
        <li class="nav-item @if(request()->routeIs('home')) active @endif">
            <a href="{{ route('home') }}">
                <span class="icon"><i class="fa fa-motorcycle"></i></span>
                <span class="text">Produk</span>
            </a>
        </li>
        <li class="nav-item @if(request()->routeIs('accessories')) active @endif">
            <a href="{{ route('accessories') }}">
                <span class="icon"><i class="fa fa-star"></i></span>
                <span class="text">Aksesoris</span>
            </a>
        </li>
        <li class="nav-item @if(request()->routeIs('parts')) active @endif">
            <a href="{{ route('parts') }}">
                <span class="icon"><i class="fa fa-cogs"></i></span>
                <span class="text">Parts</span>
            </a>
        </li>
        <li class="nav-item @if(request()->routeIs('apparel')) active @endif">
            <a href="{{ route('apparel') }}">
                <span class="icon"><i class="fa fa-shirt"></i></span>
                <span class="text">Apparel</span>
            </a>
        </li>
        <li class="nav-item @if(request()->routeIs('compare')) active @endif">
            <a href="{{ route('compare') }}">
                <span class="icon"><i class="fa fa-random"></i></span>
                <span class="text">Bandingkan Motor</span>
            </a>
        </li>
        <li class="nav-item @if(request()->routeIs('dealer')) active @endif">
            <a href="{{ route('dealer') }}">
                <span class="icon"><i class="fa fa-map-marker-alt"></i></span>
                <span class="text">Dealer</span>
            </a>
        </li>
    </ul>
    <div class="footer text-center p-2 small">
        © 2025 PT WAHANAARTHA RITELINDO
    </div>
</div>