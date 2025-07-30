<ul>
    <li class="nav-item @if(request()->routeIs('admin.branch')) active @endif">
    <a href="{{ route('admin.branch') }}">
        <span class="icon">
            <svg width="22" height="22" viewBox="0 0 22 22">
                <path
                    d="M17.4167 4.58333V6.41667H13.75V4.58333H17.4167ZM8.25 4.58333V10.0833H4.58333V4.58333H8.25ZM17.4167 11.9167V17.4167H13.75V11.9167H17.4167ZM8.25 15.5833V17.4167H4.58333V15.5833H8.25ZM19.25 2.75H11.9167V8.25H19.25V2.75ZM10.0833 2.75H2.75V11.9167H10.0833V2.75ZM19.25 10.0833H11.9167V19.25H19.25V10.0833ZM10.0833 13.75H2.75V19.25H10.0833V13.75Z"
                />
            </svg>
        </span>
        <span class="text">Dashboard</span>
    </a>
</li>

    {{-- Kelola Banner --}}
    <li class="nav-item @if(request()->routeIs('admin.banner')) active @endif">
        <a href="{{ route('admin.banner') }}">
            <span class="icon"><i class="fa-solid fa-image"></i></span>
            <span class="text">Kelola Banner</span>
        </a>
    </li>

    {{-- Kategori --}}
    <li class="nav-item nav-item-has-children">
        <a class="collapsed" href="#0" data-bs-toggle="collapse" data-bs-target="#ddmenu_kategori"
           aria-controls="ddmenu_kategori" aria-expanded="false" aria-label="Toggle navigation">
            <span class="icon"><i class="fa-solid fa-folder-tree"></i></span>
            <span class="text">Kategori</span>
        </a>
        @if(request()->is('admin/motor-category*') || request()->is('admin/accessory-category*') || request()->is('admin/apparel-category*'))
            <ul id="ddmenu_kategori" class="dropdown-nav collapse show">
        @else
            <ul id="ddmenu_kategori" class="dropdown-nav collapse">
        @endif
            <li class="nav-item">
                <a href="{{ route('admin.motor-categories.index') }}" class="@if(request()->routeIs('admin.motor-categories.index')) active @endif">
                    <span class="text">Kategori Motor</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.accessories-categories.index') }}" class="@if(request()->routeIs('admin.accessories-categories.index')) active @endif">
                    <span class="text">Kategori Aksesoris</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.parts-categories.index') }}" class="@if(request()->routeIs('admin.parts-categories.index')) active @endif">
                    <span class="text">Kategori Parts</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.apparel-categories.index') }}" class="@if(request()->routeIs('admin.apparel-categories.index')) active @endif">
                    <span class="text">Kategori Apparel</span>
                </a>
            </li>
        </ul>
    </li>

    {{-- Data Motor --}}
    <li class="nav-item nav-item-has-children">
        <a class="collapsed" href="#0" data-bs-toggle="collapse" data-bs-target="#ddmenu_motor"
           aria-controls="ddmenu_motor" aria-expanded="false" aria-label="Toggle navigation">
            <span class="icon"><i class="fa-solid fa-motorcycle"></i></span>
            <span class="text">Data Motor</span>
        </a>
        @if(request()->is('admin/motor*') || request()->is('admin/motor-color*') || request()->is('admin/motor-feature*') || request()->is('admin/motor-spec*'))
            <ul id="ddmenu_motor" class="dropdown-nav collapse show">
        @else
            <ul id="ddmenu_motor" class="dropdown-nav collapse">
        @endif
            <li class="nav-item">
                <a href="{{ route('admin.manager') }}" class="@if(request()->routeIs('admin.manager')) active @endif">Kelola Motor</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.motor-color.index') }}" class="@if(request()->routeIs('admin.motor-color.index')) active @endif">Kelola Warna</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.specs.index') }}" class="@if(request()->routeIs('admin.motor-spec.index')) active @endif">Kelola Spesifikasi</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.features.index') }}" class="@if(request()->routeIs('admin.motor-feature.index')) active @endif">Kelola Fitur</a>
            </li>
        </ul>
    </li>

    {{-- Kelola Aksesoris --}}
    <li class="nav-item @if(request()->routeIs('manage.exam')) active @endif">
        <a href="{{ route('manage.exam') }}">
            <span class="icon"><i class="fa-solid fa-rectangle-list"></i></span>
            <span class="text">Kelola Aksesoris</span>
        </a>
    </li>

    {{-- Kelola Parts --}}
    <li class="nav-item @if(request()->routeIs('admin.parts.store')) active @endif">
        <a href="{{ route('admin.parts.store') }}">
            <span class="icon"><i class="fa-solid fa-gears"></i></span>
            <span class="text">Kelola Parts</span>
        </a>
    </li>

    {{-- Kelola Apparel --}}
    <li class="nav-item @if(request()->routeIs('admin.apparels.store')) active @endif">
        <a href="{{ route('admin.apparels.store') }}">
            <span class="icon"><i class="fa-solid fa-shirt"></i></span>
            <span class="text">Kelola Apparel</span>
        </a>
    </li>

    {{-- Kelola Cabang / Dealer --}}
    <li class="nav-item @if(request()->routeIs('admin.branch')) active @endif">
        <a href="{{ route('admin.branch') }}">
            <span class="icon"><i class="fa-solid fa-store"></i></span>
            <span class="text">Kelola Dealer</span>
        </a>
    </li>