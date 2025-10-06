<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>

  <link rel="icon" href="{{ asset('favicon2.png') }}">
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/lineicons.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/main.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/public.css') }}" />
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  @stack('styles')
</head>
<body class="bg-light">

  {{-- TOGGLE GLOBAL (mengapung kiri-atas saat sidebar ditutup) --}}
  <button class="sidebar-toggle sidebar-toggle--floating js-sidebar-toggle"
          type="button" aria-label="Toggle menu" aria-expanded="true">
    <span class="ico"></span><span class="lbl">Menu</span>
  </button>

  {{-- SHELL UTAMA: Sidebar + Konten --}}
  <div class="d-flex flex-row min-vh-100 app-shell">
    {{-- Sidebar satu file --}}
    @include('layouts.sidebarPublic')

    {{-- Konten halaman --}}
    <main class="main-wrapper flex-fill p-4 overflow-auto" id="scroll">
      @yield('content')
    </main>
  </div>

  {{-- Footer --}}
  <footer class="footer">
    <p>© 2025 PT WAHANAARTHA RITELINDO</p>
  </footer>

  <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('js/main.js') }}"></script>

  <script>
    /* === Sidebar Toggle + Persist (localStorage) — berlaku untuk SEMUA tombol === */
    (function(){
      const KEY  = 'sidebar-collapsed';
      const btns = Array.from(document.querySelectorAll('.js-sidebar-toggle'));

      function applyState(collapsed){
        document.body.classList.toggle('sidebar-collapsed', collapsed);
        btns.forEach(b => b.setAttribute('aria-expanded', (!collapsed).toString()));
      }

      // initial
      const saved = localStorage.getItem(KEY);
      const collapsed = saved === '1';
      applyState(collapsed);

      // bind all toggles
      btns.forEach(b => b.addEventListener('click', () => {
        const now = !document.body.classList.contains('sidebar-collapsed');
        applyState(now);
        localStorage.setItem(KEY, now ? '1' : '0');
      }));

      // keep on resize/orientation
      ['resize','orientationchange'].forEach(ev =>
        window.addEventListener(ev, () =>
          applyState(document.body.classList.contains('sidebar-collapsed'))
        )
      );
    })();
  </script>

  {{-- === Flag 1920×1080 @ 100% scale → tambahkan class .is-1920-100 ke <html> === --}}
  <script>
    (function () {
      function set1920Flag() {
        const ok =
          window.screen.width === 1920 &&
          window.screen.height === 1080 &&
          Math.abs(window.devicePixelRatio - 1) < 0.01 &&
          window.matchMedia('(orientation: landscape)').matches;

        document.documentElement.classList.toggle('is-1920-100', ok);
      }
      set1920Flag();
      window.addEventListener('resize', set1920Flag);
      window.matchMedia('(orientation: landscape)').addEventListener?.('change', set1920Flag);
    })();
  </script>

  <script src="{{ asset('js/custom.js') }}"></script>

  {{-- Tempat script halaman --}}
  @stack('scripts')
</body>
</html>