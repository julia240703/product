<aside class="sidebar">
  <button class="sidebar-toggle sidebar-toggle--inside js-sidebar-toggle" type="button" aria-label="Toggle menu">
    <span class="ico"></span><span class="lbl">Menu</span>
  </button>

  {{-- layouts/sidebarPublic.blade --}}
<a href="{{ route('home') }}" class="sidebar-logo">
  <img src="{{ asset('logooo.png') }}" alt="Logo" class="img-fluid">
</a>

  <div class="divider"></div>

  <nav class="nav flex-column w-100">
    <a class="nav-link {{ request()->routeIs('produk*') ? 'active' : '' }}" href="{{ route('produk') }}">
      <i class="fas fa-motorcycle"></i> Produk
    </a>
    <a class="nav-link {{ request()->routeIs('accessories') ? 'active' : '' }}" href="{{ route('accessories') }}">
      <i class="fas fa-tools"></i> Aksesoris
    </a>
    <a class="nav-link {{ request()->routeIs('parts') ? 'active' : '' }}" href="{{ route('parts') }}">
      <i class="fas fa-cogs"></i> Parts
    </a>
    <a class="nav-link {{ request()->routeIs('apparels') ? 'active' : '' }}" href="{{ route('apparels') }}">
      <i class="fas fa-tshirt"></i> Apparel
    </a>
    <a class="nav-link {{ request()->routeIs('compare.*') ? 'active' : '' }}" href="{{ route('compare.menu') }}">
      <i class="fas fa-balance-scale"></i> Bandingkan Motor
    </a>
    <a class="nav-link {{ request()->routeIs('branches') ? 'active' : '' }}" href="{{ route('branches') }}">
      <i class="fas fa-map-marker-alt"></i> Dealer
    </a>
  </nav>
</aside>