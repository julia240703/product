<ul>
    {{-- General --}}
    <li class="nav-item nav-item-has-children">
    <a class="collapsed nav-link" href="#0" data-bs-toggle="collapse" data-bs-target="#ddmenu_general"
        aria-controls="ddmenu_general" aria-expanded="false" aria-label="Toggle navigation">
        <span class="icon"><i class="fa-solid fa-layer-group"></i></span>
        <span class="text" style="vertical-align: middle; line-height: 22px; display: inline-block;">General</span>
    </a>
    <ul id="ddmenu_general" class="dropdown-nav collapse @if(
            request()->routeIs('admin.banner') ||
            request()->routeIs('admin.price_list.index') ||
            request()->routeIs('admin.accessories.general.*')   {{-- <-- wildcard --}}
        ) show @endif">

        <li class="nav-item">
        <a href="{{ route('admin.banner') }}" class="@if(request()->routeIs('admin.banner')) active @endif">
            Banner
        </a>
        </li>

        <li class="nav-item">
        <a href="{{ route('admin.price_list.index') }}" class="@if(request()->routeIs('admin.price_list.index')) active @endif">
            Price List
        </a>
        </li>

        <li class="nav-item">
        <a href="{{ route('admin.accessories.general.index') }}"
           class="@if(request()->routeIs('admin.accessories.general.*')) active @endif">
            Aksesoris (General)
        </a>
        </li>
    </ul>
    </li>

    {{-- Data Motor --}}
    <li class="nav-item nav-item-has-children">
        <a class="collapsed nav-link" href="#0" data-bs-toggle="collapse" data-bs-target="#ddmenu_motor"
           aria-controls="ddmenu_motor" aria-expanded="false" aria-label="Toggle navigation">
            <span class="icon"><i class="fa-solid fa-motorcycle"></i></span>
            <span class="text" style="vertical-align: middle; line-height: 22px; display: inline-block;">Data Motor</span>
        </a>
        <ul id="ddmenu_motor" class="dropdown-nav collapse @if(request()->routeIs('admin.motors.index') || request()->routeIs('admin.motor-type.index') || request()->routeIs('admin.motor-color*') || request()->routeIs('admin.categories.index')) show @endif">
            <li class="nav-item">
                <a href="{{ route('admin.categories.index', ['type' => 'motor']) }}" class="@if(request()->routeIs('admin.categories.index')) active @endif">
                    <span class="text">Kategori Motor</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.motor-type.index') }}" class="@if(request()->routeIs('admin.motor-type.index')) active @endif">Tipe Motor</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.motors.index') }}" class="@if(request()->routeIs('admin.motors.index')) active @endif">Kelola Motor</a>
            </li>
        </ul>
    </li>

    {{-- Apparel --}}
    <li class="nav-item nav-item-has-children">
        <a class="collapsed nav-link" href="#0" data-bs-toggle="collapse" data-bs-target="#ddmenu_apparel"
           aria-controls="ddmenu_apparel" aria-expanded="false" aria-label="Toggle navigation">
            <span class="icon"><i class="fa-solid fa-tshirt"></i></span>
            <span class="text" style="vertical-align: middle; line-height: 22px; display: inline-block;">Apparel</span>
        </a>
        <ul id="ddmenu_apparel" class="dropdown-nav collapse @if(request()->is('admin/apparel-categories*') || request()->is('admin/apparels*')) show @endif">
            <li class="nav-item">
                <a href="{{ route('admin.apparel-categories.index') }}" class="@if(request()->routeIs('admin.apparel-categories.index')) active @endif">
                    <span class="text">Kategori Apparel</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.apparels.index') }}" class="@if(request()->routeIs('admin.apparels.index')) active @endif">
                    <span class="text">Semua Apparel</span>
                </a>
            </li>
        </ul>
    </li>

    <!-- Dealer -->
    <li class="nav-item nav-item-has-children">
        <a class="collapsed nav-link" href="#0" data-bs-toggle="collapse" data-bs-target="#ddmenu_dealer"
           aria-controls="ddmenu_dealer" aria-expanded="false" aria-label="Toggle navigation">
            <span class="icon"><i class="fa-solid fa-store"></i></span>
            <span class="text" style="vertical-align: middle; line-height: 22px; display: inline-block;">Dealer</span>
        </a>
        <ul id="ddmenu_dealer" class="dropdown-nav collapse @if(request()->routeIs('admin.branch-areas.index') || request()->routeIs('admin.branch-cities.index') || request()->routeIs('admin.branches.index')) show @endif">
            <li class="nav-item">
                <a href="{{ route('admin.branch-areas.index') }}" class="@if(request()->routeIs('admin.branch-areas.index')) active @endif">
                    <span class="text">Area</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.branch-cities.index') }}" class="@if(request()->routeIs('admin.branch-cities.index')) active @endif">
                    <span class="text">Kota</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.branches.index') }}" class="@if(request()->routeIs('admin.branches.index')) active @endif">
                    <span class="text">List Cabang</span>
                </a>
            </li>
        </ul>
    </li>