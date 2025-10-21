@extends('layouts.appPublic')

@section('content')
<div class="row g-0">
  <div class="col-12 accd-detail">

    {{-- Back bar --}}
    @php
      $backUrl = isset($accessory->motor) && $accessory->motor
        ? route('accessories.motor', $accessory->motor->id)
        : route('accessories', ['key' => 'general']);
    @endphp

    <div class="accd-back">
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

    {{-- Header: Gambar besar + info kanan --}}
    <div class="accd-header">
      <div class="accd-left">
        @php
          $hero   = $gallery[0] ?? asset('placeholder.png');
          $thumbs = collect($gallery ?? []);
          if ($thumbs->isEmpty()) { $thumbs = collect([$hero]); }
        @endphp

        {{-- HERO --}}
        <figure class="accd-hero">
          <img
            src="{{ $hero }}"
            alt="{{ $accessory->name }}"
            class="accd-hero-img"
            id="accdHeroImg">
        </figure>

        {{-- THUMBNAILS --}}
        <div class="accd-thumbs">
          @foreach($thumbs as $i => $img)
            <button
              type="button"
              class="accd-thumb {{ $i === 0 ? 'is-active' : '' }}"
              data-src="{{ $img }}"
              aria-pressed="{{ $i === 0 ? 'true' : 'false' }}"
              title="Gambar {{ $i+1 }}">
              <img src="{{ $img }}" alt="Thumbnail {{ $i+1 }}">
            </button>
          @endforeach
        </div>
      </div>

      <div class="accd-side">
        <h1 class="display-6 fw-bold mb-3">{{ $accessory->name }}</h1>

        <div class="accd-specs">
          <div class="mb-3">
            <div class="accd-spec-title">Fungsi</div>
            <div class="accd-spec-text">{{ $accessory->function }}</div>
          </div>

          <div class="mb-3">
            <div class="accd-spec-title">Warna</div>
            <div class="accd-spec-text">{{ $accessory->color }}</div>
          </div>

          <div class="mb-3">
            <div class="accd-spec-title">Material</div>
            <div class="accd-spec-text">{{ $accessory->material }}</div>
          </div>

          <div class="mb-3">
            <div class="accd-spec-title">Parts Number</div>
            <div class="accd-spec-text">{{ $accessory->part_number }}</div>
          </div>

          <div class="mb-3">
            <div class="accd-spec-title">Harga</div>
            <div class="accd-price-red">Rp {{ number_format($accessory->price ?? 0, 0, ',', '.') }}</div>
          </div>

          @if(!is_null($accessory->stock))
            <div class="mb-4">
              <div class="accd-spec-title">Stok Tersedia</div>
              <div class="accd-spec-text">{{ $accessory->stock }}</div>
            </div>
          @endif
        </div>

        <a href="#"
           class="btn btn-dark btn-lg w-100 py-3 fw-bold accd-order"
           style="border-radius:14px;">Buat Pesanan</a>
      </div>
    </div>

    <div class="my-4"></div>

    {{-- Aksesoris Lainnya --}}
    <section class="motor-accessories mb-5">
      <h2 class="section-title">Aksesoris Lainnya</h2>

      <div class="acc-track">
        @forelse($otherAccs as $o)
          <article class="acc-card">
            <div class="acc-img">
              <img src="{{ $o->image_url }}" alt="{{ $o->name }}">
            </div>
            <div class="acc-body">
              <h5 class="acc-title">{{ $o->name }}</h5>

              <div class="acc-price">
                <span>Harga</span>
                <strong>Rp {{ number_format($o->display_price ?? 0, 0, ',', '.') }}</strong>
              </div>

              <a class="acc-cta" href="{{ route('accessory.detail', $o->id) }}">
                <span>Detail</span>
                <i class="fas fa-chevron-right"></i>
              </a>
            </div>
          </article>
        @empty
          <p class="text-muted">Belum ada aksesoris lain.</p>
        @endforelse
      </div>
    </section>

    <div class="mb-5"></div>
  </div>
</div>

{{-- ===== QR ORDER MODAL (untuk "Buat Pesanan") ===== --}}
<div id="qrOrderModal" class="qrmm" hidden>
  <div class="qrmm__backdrop" data-close></div>
  <div class="qrmm__box" role="dialog" aria-modal="true" aria-labelledby="qrOrderTitle">
    <button class="qrmm__close" type="button" aria-label="Tutup" data-close>×</button>
    <h3 id="qrOrderTitle" class="qrmm__title">Scan untuk Buat Pesanan</h3>
    <p class="qrmm__tag">
      Scan QR ini untuk menuju <strong>website resmi Wahana Ritelindo</strong>.
      Lanjutkan pemesananmu di website resmi kami—cepat & mudah!
    </p>
    <div id="qrOrderCanvas" class="qrmm__canvas" aria-live="polite"></div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  (function(){
    const hero = document.getElementById('accdHeroImg');
    const thumbs = document.querySelectorAll('.accd-thumb');
    if(!hero || !thumbs.length) return;

    thumbs.forEach(btn=>{
      btn.addEventListener('click', ()=>{
        const src = btn.getAttribute('data-src');
        if(src) hero.src = src;

        thumbs.forEach(b=>{ b.classList.remove('is-active'); b.setAttribute('aria-pressed','false'); });
        btn.classList.add('is-active');
        btn.setAttribute('aria-pressed','true');
      });
    });
  })();
</script>

{{-- Library QR --}}
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js" defer></script>
<script>
  (function(){
    const ORDER_URL = 'https://www.wahanaritelindo.com/home';
    const modal  = document.getElementById('qrOrderModal');
    const canvas = document.getElementById('qrOrderCanvas');

    if(!modal || !canvas) return;

    function openQR(){
      canvas.innerHTML = '';
      const render = () => {
        if (window.QRCode) {
          new QRCode(canvas, {text: ORDER_URL, width: 300, height: 300, correctLevel: QRCode.CorrectLevel.M});
        } else {
          setTimeout(render, 30);
        }
      };
      render();
      modal.hidden = false;
    }
    function closeQR(){ modal.hidden = true; canvas.innerHTML=''; }

    // Klik "Buat Pesanan" -> tampil QR
    document.addEventListener('click', function(e){
      const btn = e.target.closest('.accd-order');
      if(!btn) return;
      e.preventDefault();
      openQR();
    }, true);

    // Tutup modal
    modal.addEventListener('click', function(e){
      if (e.target.hasAttribute('data-close')) closeQR();
    });
    document.addEventListener('keydown', function(e){
      if (e.key === 'Escape' && !modal.hidden) closeQR();
    });
  })();
</script>
@endpush