@extends('layouts.appPublic')

@section('content')
<div class="row g-0">
  <div class="col-12 accd-detail">

  {{-- Back --}}
  <div class="accd-back mb-3">
    <a href="{{ route('accessories', ['key' => 'general']) }}" class="accd-back-link">
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

  {{-- Header: Gambar & Info --}}
  <div class="accd-header">
    <div class="accd-left">
      <figure class="accd-hero">
        <img src="{{ $hero }}" alt="{{ $acc->name }}" class="accd-hero-img" id="accHeroImg">
      </figure>

      @if($thumbs->isNotEmpty())
      <div class="accd-thumbs">
        @foreach($thumbs as $i => $img)
          <button type="button"
                  class="accd-thumb {{ $i === 0 ? 'is-active' : '' }}"
                  data-src="{{ $img }}"
                  aria-pressed="{{ $i === 0 ? 'true' : 'false' }}"
                  title="Gambar {{ $i+1 }}">
            <img src="{{ $img }}" alt="Thumbnail {{ $i+1 }}">
          </button>
        @endforeach
      </div>
      @endif
    </div>

    <div class="accd-side">
      <h1 class="display-6 fw-bold mb-3">{{ $acc->name }}</h1>

      <div class="accd-specs">
        @if(!empty($acc->function))
          <div class="mb-3">
            <div class="accd-spec-title">Fungsi</div>
            <div class="accd-spec-text">{{ $acc->function }}</div>
          </div>
        @endif

        @if(!empty($acc->color))
          <div class="mb-3">
            <div class="accd-spec-title">Warna</div>
            <div class="accd-spec-text">{{ $acc->color }}</div>
          </div>
        @endif

        @if(!empty($acc->variant))
          <div class="mb-3">
            <div class="accd-spec-title">Varian</div>
            <div class="accd-spec-text">{{ $acc->variant }}</div>
          </div>
        @endif

        @if(!empty($acc->material))
          <div class="mb-3">
            <div class="accd-spec-title">Material</div>
            <div class="accd-spec-text">{{ $acc->material }}</div>
          </div>
        @endif

        @if(!empty($acc->part_number))
          <div class="mb-3">
            <div class="accd-spec-title">Parts Number</div>
            <div class="accd-spec-text">{{ $acc->part_number }}</div>
          </div>
        @endif

        <div class="mb-3">
          <div class="accd-spec-title">Harga</div>
          <div class="accd-price-red">Rp {{ number_format($acc->price, 0, ',', '.') }}</div>
        </div>

        @if(!is_null($acc->stock))
          <div class="mb-4">
            <div class="accd-spec-title">Stok Tersedia</div>
            <div class="accd-spec-text">{{ $acc->stock }}</div>
          </div>
        @endif
      </div>

      <a href="#" class="btn btn-dark btn-lg w-100 py-3 fw-bold accd-order" style="border-radius:14px;">
        Buat Pesanan
      </a>
    </div>
  </div>

  <div class="my-4"></div>

  {{-- Aksesoris Lainnya --}}
  <section class="motor-accessories mb-5">
    <h2 class="section-title">Aksesoris Lainnya</h2>
    <div class="acc-track">
      @forelse($otherList as $o)
        <article class="acc-card">
          <div class="acc-img">
            <img src="{{ $o->image_url }}" alt="{{ $o->display_name }}">
          </div>
          <div class="acc-body">
            <h5 class="acc-title">{{ $o->display_name }}</h5>
            <div class="acc-price">
              <span>Harga</span>
              <strong>Rp {{ number_format($o->display_price ?? 0, 0, ',', '.') }}</strong>
            </div>
            <a class="acc-cta" href="{{ route('accessories.general.detail', $o->id) }}">
              <span>Detail</span><i class="fas fa-chevron-right"></i>
            </a>
          </div>
        </article>
      @empty
        <p class="text-muted">Belum ada aksesoris lain.</p>
      @endforelse
    </div>
  </section>

</div>
@endsection

@push('scripts')
<script>
  // Klik thumbnail → ganti gambar hero
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.accd-thumb');
    if (!btn) return;

    const hero = document.getElementById('accHeroImg');
    if (!hero) return;

    const src = btn.getAttribute('data-src');
    if (src) hero.src = src;

    document.querySelectorAll('.accd-thumb').forEach(el => {
      el.classList.remove('is-active');
      el.setAttribute('aria-pressed','false');
    });
    btn.classList.add('is-active');
    btn.setAttribute('aria-pressed','true');
  });
</script>
@endpush