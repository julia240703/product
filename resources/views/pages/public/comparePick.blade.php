@extends('layouts.appPublic')

@section('content')
<div id="cmpPickPage">
  @php
    $prev    = url()->previous();
    $current = url()->current();
    $backUrl = $prev && $prev !== $current ? $prev : route('compare.menu');
  @endphp

  <div class="accd-back mb-3 js-cmp-head">
    <a href="{{ $backUrl }}" class="accd-back-link">
      <span class="accd-back-ico">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
             stroke="#111" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="11"/>
          <line x1="15" y1="12" x2="8" y2="12"/>
          <polyline points="12 16 8 12 12 8"/>
        </svg>
      </span>
      <span class="accd-back-txt">Kembali</span>
    </a>
    <div class="accd-back-rule"></div>
  </div>

  <h1 class="fw-bold text-center m-0 js-cmp-title" style="font-size:40px; color:#111;">Pilih Model</h1>
  <p class="text-center js-cmp-sub" style="font-size:22px; color:#2b2f36;">
    Temukan motor terbaik! Bandingkan hingga 6 model sekaligus
  </p>

  <div class="row gx-4 mt-1">
    {{-- FILTER KIRI --}}
    <div class="col-lg-3">
      <div class="cmp-filter-stick d-flex flex-column p-3">
        <h5 class="cmp-filter-title mb-3">Model Yang Tersedia</h5>
        <div class="cmp-filter-subtitle mb-3">Model</div>

        <label class="cmp-radio mb-2">
          <input type="radio" name="cat" value=""
                 {{ !$activeCatId ? 'checked' : '' }}
                 onclick="window.location='{{ route('compare.pick') }}'">
          <span>SEMUA</span>
        </label>

        @foreach($categories as $cat)
          <label class="cmp-radio mb-2">
            <input type="radio" name="cat" value="{{ $cat->id }}"
                   {{ (int)$activeCatId === (int)$cat->id ? 'checked' : '' }}
                   onclick="window.location='{{ route('compare.pick', ['category'=>$cat->id]) }}'">
            <span>{{ ucfirst($cat->name) }}</span>
          </label>
        @endforeach

        @php $canCompare = ($selectedCount ?? 0) >= 2; @endphp
        <a href="{{ $canCompare ? route('compare.result') : '#' }}"
           class="btn btn-dark w-100 py-3 fw-bold cmp-stick-cta mt-auto {{ $canCompare ? '' : 'disabled' }}"
           style="border-radius:14px; {{ $canCompare ? '' : 'opacity:.6; pointer-events:none;' }}">
          Mulai Bandingkan ({{ $selectedCount }}/6)
        </a>
      </div>
    </div>

    {{-- LIST MOTOR KANAN --}}
    <div class="col-lg-9">
      <div class="cmp-right-scroll">
        @if(!$showAll)
          @php
            $activeCatName = optional($categories->firstWhere('id',(int)$activeCatId))->name ?? 'Semua';
          @endphp

          <div class="text-center">
            <div class="cmp-cat-ttl" style="display:inline-block; font-size:32px; color:#111;">
              {{ $activeCatName }}
            </div>
          </div>

          <div class="cmp-card-grid mt-3">
            @forelse($motors as $m)
              @php
                $isSelected = in_array($m->id, $selectedIds ?? []);
                $isFull     = ($selectedCount ?? 0) >= 6;
              @endphp

              <article class="cmp-card {{ $isSelected ? 'is-selected' : '' }}">
                <div class="cmp-card-img">
                  <img src="{{ $m->image_url }}" alt="{{ $m->name }}">
                </div>

                <div>
                  <h3 class="cmp-card-title">{{ $m->name }}</h3>
                  <div class="cmp-card-price-hint"><em>Harga Mulai</em></div>
                  <div class="cmp-card-price">Rp {{ number_format($m->display_price ?? 0, 0, ',', '.') }}</div>
                </div>

                @if($isSelected)
                  <form action="{{ route('compare.remove') }}" method="POST" class="mt-auto">
                    @csrf @method('DELETE')
                    <input type="hidden" name="motor_id" value="{{ $m->id }}">
                    <button type="submit" class="cmp-choose btn btn-outline-danger w-100">
                      <i class="fas fa-times me-2"></i>
                      Hapus dari perbandingan
                    </button>
                  </form>
                @else
                  <form action="{{ route('compare.store') }}" method="POST" class="mt-auto">
                    @csrf
                    <input type="hidden" name="motor_id" value="{{ $m->id }}">
                    <button type="submit" class="cmp-choose btn btn-outline-dark w-100" {{ $isFull ? 'disabled' : '' }}>
                      <i class="fas fa-arrows-rotate me-2"></i>
                      Tambah Untuk Membandingkan
                    </button>
                  </form>
                @endif
              </article>
            @empty
              <p class="text-muted">Belum ada motor untuk kategori ini.</p>
            @endforelse
          </div>

          @if(method_exists($motors, 'links'))
            <div class="mt-4">
              {{ $motors->withQueryString()->links() }}
            </div>
          @endif

        @else
          @php
            $grouped = $motors->groupBy(fn($m) => optional($m->category)->name ?? 'Lainnya');
            $order   = $categories->pluck('name')->values()->all();
            $grouped = $grouped->sortBy(fn($items, $cat) => ($i = array_search($cat, $order, true)) !== false ? $i : 999);
          @endphp

          @foreach($grouped as $catName => $items)
            <section class="cmp-cat-block mb-5">
              <div class="text-center">
                <div class="cmp-cat-ttl" style="display:inline-block; font-size:32px; color:#111;">
                  {{ $catName }}
                </div>
              </div>

              <div class="cmp-card-grid mt-3">
                @foreach($items as $m)
                  @php
                    $isSelected = in_array($m->id, $selectedIds ?? []);
                    $isFull     = ($selectedCount ?? 0) >= 6;
                  @endphp

                  <article class="cmp-card {{ $isSelected ? 'is-selected' : '' }}">
                    <div class="cmp-card-img">
                      <img src="{{ $m->image_url }}" alt="{{ $m->name }}">
                    </div>

                    <div>
                      <h3 class="cmp-card-title">{{ $m->name }}</h3>
                      <div class="cmp-card-price-hint"><em>Harga Mulai</em></div>
                      <div class="cmp-card-price">Rp {{ number_format($m->display_price ?? 0, 0, ',', '.') }}</div>
                    </div>

                    @if($isSelected)
                      <form action="{{ route('compare.remove') }}" method="POST" class="mt-auto">
                        @csrf @method('DELETE')
                        <input type="hidden" name="motor_id" value="{{ $m->id }}">
                        <button type="submit" class="cmp-choose btn btn-outline-danger w-100 cmp-remove">
                          <i class="fas fa-times me-2"></i>
                          Hapus dari perbandingan
                        </button>
                      </form>
                    @else
                      <form action="{{ route('compare.store') }}" method="POST" class="mt-auto">
                        @csrf
                        <input type="hidden" name="motor_id" value="{{ $m->id }}">
                        <button type="submit" class="cmp-choose btn btn-outline-dark w-100" {{ $isFull ? 'disabled' : '' }}>
                          <i class="fas fa-arrows-rotate me-2"></i>
                          Tambah Untuk Membandingkan
                        </button>
                      </form>
                    @endif
                  </article>
                @endforeach
              </div>
            </section>
          @endforeach
        @endif
      </div>
    </div>
  </div>
</div> {{-- /#cmpPickPage --}}

@push('scripts')
<script>
(function(){
  const head   = document.querySelector('.js-cmp-head');
  const title  = document.querySelector('.js-cmp-title');
  const sub    = document.querySelector('.js-cmp-sub');
  const footer = document.querySelector('.footer, .app-footer, footer, .copyright, .copyright-bar');
  const H = el => el ? Math.round(el.getBoundingClientRect().height) : 0;

  function applyVars(){
    const stickyTop = H(head) + H(title) + H(sub) + 16;
    const footerH   = H(footer);
    document.documentElement.style.setProperty('--cmpStickyTop', stickyTop + 'px');
    document.documentElement.style.setProperty('--cmpFooterH',  (footerH || 60) + 'px');
  }

  applyVars();
  window.addEventListener('resize', applyVars);

  // Nonaktifkan scroll di <main> khusus halaman ini (biar nggak ada double scrollbar)
  const main = document.querySelector('.main-wrapper');
  main && main.classList.add('cmp-no-scroll');

  // optional: bersihkan kelas saat keluar halaman (mis. via Turbolinks/Hotwire)
  window.addEventListener('beforeunload', () => {
    main && main.classList.remove('cmp-no-scroll');
  });
})();

/* ================================================
   INGAT SCROLL HANYA UNTUK AKSI ADD/REMOVE
   (kalau ke menu lain lalu balik → reset ke atas)
   ================================================ */
(function(){
  const scroller = document.querySelector('.cmp-right-scroll');
  if (!scroller) return;

  // key unik per halaman + query
  const BASE = 'cmpPick:' + location.pathname + location.search;
  const SCROLL_KEY = BASE + ':scroll';
  const FLAG_KEY   = BASE + ':shouldRestore';

  // --- RESTORE: hanya jika flag disetel oleh submit ADD/REMOVE ---
  if (sessionStorage.getItem(FLAG_KEY) === '1') {
    const saved = sessionStorage.getItem(SCROLL_KEY);
    if (saved !== null) {
      let tries = 14;
      const restore = () => {
        scroller.scrollTop = parseInt(saved, 10) || 0;
        if (--tries > 0) requestAnimationFrame(restore);
        else {
          sessionStorage.removeItem(SCROLL_KEY);
          sessionStorage.removeItem(FLAG_KEY);
        }
      };
      requestAnimationFrame(restore);
    } else {
      sessionStorage.removeItem(FLAG_KEY);
    }
  } else {
    // datang dari halaman lain → bersihkan sisa data
    sessionStorage.removeItem(SCROLL_KEY);
  }

  // --- SAVE: hanya saat submit form di dalam kartu (Tambah/Hapus) ---
  document.addEventListener('submit', function(e){
    const form = e.target;
    if (form.closest('.cmp-card')) {
      try {
        sessionStorage.setItem(SCROLL_KEY, scroller.scrollTop);
        sessionStorage.setItem(FLAG_KEY, '1');
      } catch(err) {}
    }
  }, true);
})();
</script>
@endpush
@endsection