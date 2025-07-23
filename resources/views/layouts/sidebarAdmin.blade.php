<ul>
    <li class="nav-item nav-item-has-children">
        <a class="collapsed" href="#0" class="" data-bs-toggle="collapse" data-bs-target="#ddmenu_1"
           aria-controls="ddmenu_1" aria-expanded="false" aria-label="Toggle navigation">
           <span class="icon">
                <svg width="22" height="22" viewBox="0 0 22 22">
                  <path
                          d="M17.4167 4.58333V6.41667H13.75V4.58333H17.4167ZM8.25 4.58333V10.0833H4.58333V4.58333H8.25ZM17.4167 11.9167V17.4167H13.75V11.9167H17.4167ZM8.25 15.5833V17.4167H4.58333V15.5833H8.25ZM19.25 2.75H11.9167V8.25H19.25V2.75ZM10.0833 2.75H2.75V11.9167H10.0833V2.75ZM19.25 10.0833H11.9167V19.25H19.25V10.0833ZM10.0833 13.75H2.75V19.25H10.0833V13.75Z"
                  />
                </svg>
              </span>
            <span class="text">Dashboard</span>
        </a>
        @if(request()->routeIs('admin.home'))
            <ul id="ddmenu_1" class="dropdown-nav collapse show">
        @elseif(request()->routeIs('admin.branch'))
            <ul id="ddmenu_1" class="dropdown-nav collapse show">
        @else
            <ul id="ddmenu_1" class="dropdown-nav collapse">
        @endif
        <li class="nav-item ">
            <a href="{{ route('admin.home') }}" class="@if(request()->routeIs('admin.home')) active @endif">

                <span class="text">{{ __('Dashboard Utama') }}</span>
            </a>
        </li>
        <li class="nav-item ">
            <a href="{{ route('admin.branch') }}" class="@if(request()->routeIs('admin.branch')) active @endif">

                <span class="text">{{ __('Dashboard Cabang') }}</span>
            </a>
        </li>
        </ul>
    </li>



    <li class="nav-item @if(request()->routeIs('admin.users')) active @endif">
        <a href="{{ route('admin.users') }}">
              <span class="icon">
                    <i class="fa-solid fa-users"></i>
              </span>
            <span class="text">{{ __('Data Peserta') }}</span>
        </a>
    </li>

    <li class="nav-item @if(request()->routeIs('admin.manager')) active @endif">
        <a href="{{ route('admin.manager') }}">
              <span class="icon">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
              </span>
            <span class="text">{{ __('Data Sub-Admin') }}</span>
        </a>
    </li>

    <li class="nav-item @if(request()->routeIs('manage.exam')) active @endif">
        <a href="{{ route('manage.exam') }}">
            <span class="icon">
                <i class="fa-solid fa-rectangle-list"></i>            
            </span>
            <span class="text">{{ __('Kelola Psikotes') }}</span>
        </a>
    </li>

    <li class="nav-item @if(request()->routeIs('manage.examQuestion')) active @endif">
        <a href="{{ route('manage.examQuestion') }}">
            <span class="icon">
                <i class="fa-solid fa-list"></i>            
            </span>
            <span class="text">{{ __('Kelola Soal Psikotes') }}</span>
        </a>
    </li>

    <li class="nav-item @if(request()->routeIs('user.results')) active @endif">
        <a href="{{ route('user.results') }}">
            <span class="icon">
                <svg width="22" height="22" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                </svg>
            </span>
            <span class="text">{{ __('Hasil Psikotes') }}</span>
        </a>
    </li>

</ul>