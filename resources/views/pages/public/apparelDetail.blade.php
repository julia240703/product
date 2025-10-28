@extends('layouts.appPublic')

@section('content')
<div class="accd-detail">

  {{-- Back bar --}}
  <div class="accd-back">
    @php
      $backUrl = isset($apparel->category_id)
        ? route('apparels', ['key' => $apparel->category_id])
        : route('apparels');

      // URL dari controller; TIDAK fallback ke home
      $orderUrl = $apparel->order_url; 
    @endphp
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

  {{-- Header --}}
  <div class="accd-header">
    <div class="accd-left">
      @php
        $heroImg = $hero ?? ($apparel->image_url ?? asset('placeholder.png'));
        $thumbList = collect($thumbs ?? []);
        if ($thumbList->isEmpty()) { $thumbList = collect([$heroImg]); }
      @endphp

      <figure class="accd-hero">
        <img src="{{ $heroImg }}" alt="{{ $apparel->name }}" class="accd-hero-img" id="appHeroImg">
      </figure>

      <div class="accd-thumbs">
        @foreach($thumbList as $i => $img)
          <button type="button"
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
      <h1 class="display-6 fw-bold mb-3">{{ $apparel->name }}</h1>

      <div class="accd-specs">
        @if(!empty($apparel->description))
          <div class="mb-3">
            <div class="accd-spec-title">Deskripsi</div>
            <div class="accd-spec-text">{{ $apparel->description }}</div>
          </div>
        @endif

        <div class="mb-3">
          <div class="accd-spec-title">Ukuran</div>
          @if(!empty($apparel->sizes))
            <select class="form-select" style="max-width:520px">
              @foreach($apparel->sizes as $s)
                <option value="{{ $s }}">{{ $s }}</option>
              @endforeach
            </select>
          @else
            <div class="accd-spec-text">-</div>
          @endif
        </div>

        <div class="mb-3">
          <div class="accd-spec-title">Material</div>
          @if(!empty($apparel->material_list))
            <ul class="mb-0" style="padding-left:1.1rem">
              @foreach($apparel->material_list as $m)
                <li class="accd-spec-text">{{ $m }}</li>
              @endforeach
            </ul>
          @else
            <div class="accd-spec-text">-</div>
          @endif
        </div>

        @if(!empty($apparel->color))
          <div class="mb-3">
            <div class="accd-spec-title">Available Color</div>
            <div class="accd-spec-text">{{ $apparel->color }}</div>
          </div>
        @endif

        <div class="mb-3">
          <div class="accd-spec-title">Harga</div>
          <div class="accd-price-red">Rp {{ number_format($apparel->price ?? 0, 0, ',', '.') }}</div>
        </div>

        @if(!is_null($apparel->stock))
          <div class="mb-4">
            <div class="accd-spec-title">Stok Tersedia</div>
            <div class="accd-spec-text">{{ $apparel->stock }}</div>
          </div>
        @endif
      </div>

      {{-- tombol buat pesanan -> QR, URL dari back office --}}
      <a href="#"
         class="btn btn-dark btn-lg w-100 py-3 fw-bold accd-order"
         data-order-url="{{ $orderUrl }}"
         style="border-radius:14px;">Buat Pesanan</a>
    </div>
  </div>

  <div class="my-4"></div>

  {{-- Apparel Lainnya --}}
  <section class="motor-accessories mb-5">
    <h2 class="section-title">Apparel Lainnya</h2>

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
            <a class="acc-cta" href="{{ route('apparel.detail', $o->id) }}">
              <span>Detail</span>
              <i class="fas fa-chevron-right"></i>
            </a>
          </div>
        </article>
      @empty
        <p class="text-muted">Belum ada apparel lain pada kategori ini.</p>
      @endforelse
    </div>
  </section>

  <div class="mb-5"></div>
</div>

{{-- ===== QR ORDER MODAL ===== --}}
<div id="qrOrderModal" class="qrmm" hidden>
  <div class="qrmm__backdrop" data-close></div>
  <div class="qrmm__box" role="dialog" aria-modal="true" aria-labelledby="qrOrderTitle">
    <button class="qrmm__close" type="button" aria-label="Tutup" data-close>×</button>
    <h3 id="qrOrderTitle" class="qrmm__title">Scan untuk Buat Pesanan</h3>
    <p class="qrmm__tag">
      Scan QR ini untuk menuju <strong>website resmi Wahana Ritelindo</strong>. 
      Lanjutkan pemesananmu di website resmi kami—cepat &amp; mudah!
    </p>
    <div id="qrOrderCanvas" class="qrmm__canvas" aria-live="polite"></div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // ganti hero saat klik thumbnail
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.accd-thumb');
    if (!btn) return;
    const hero = document.getElementById('appHeroImg');
    if (!hero) return;

    const src = btn.getAttribute('data-src');
    if (src) {
      hero.src = src;
      const alt = btn.querySelector('img')?.getAttribute('alt') || hero.alt || 'Gambar';
      hero.setAttribute('alt', alt);
    }
    document.querySelectorAll('.accd-thumb').forEach(el => {
      el.classList.remove('is-active');
      el.setAttribute('aria-pressed', 'false');
    });
    btn.classList.add('is-active');
    btn.setAttribute('aria-pressed', 'true');
  });
</script>

{{-- QR library --}}
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js" defer></script>
<script>
  (function(){
    const modal  = document.getElementById('qrOrderModal');
    const canvas = document.getElementById('qrOrderCanvas');
    if(!modal || !canvas) return;

    function openQR(url){
      canvas.innerHTML = '';
      const render = () => {
        if (window.QRCode) {
          new QRCode(canvas, {text: url, width: 300, height: 300, correctLevel: QRCode.CorrectLevel.M});
        } else {
          setTimeout(render, 30);
        }
      };
      render();
      modal.hidden = false;
    }
    function closeQR(){ modal.hidden = true; canvas.innerHTML=''; }

    // klik "Buat Pesanan" -> QR ke URL dari back office
    document.addEventListener('click', function(e){
      const btn = e.target.closest('.accd-order');
      if(!btn) return;
      e.preventDefault();

      const url = (btn.getAttribute('data-order-url') || '').trim();
      if (!url) {
        alert('Link pemesanan belum tersedia.');
        return;
      }
      openQR(url);
    }, true);

    // close modal
    modal.addEventListener('click', function(e){
      if (e.target.hasAttribute('data-close')) closeQR();
    });
    document.addEventListener('keydown', function(e){
      if (e.key === 'Escape' && !modal.hidden) closeQR();
    });
  })();
</script>
@endpush