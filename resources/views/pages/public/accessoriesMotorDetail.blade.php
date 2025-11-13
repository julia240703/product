@extends('layouts.appPublic')

@section('content')
  {{-- Back bar --}}
  @php
    use Illuminate\Support\Str;

    $prevPath = parse_url(url()->previous(), PHP_URL_PATH) ?? '';
    $isAccessoriesIndex =
      Str::startsWith($prevPath, '/accessories') &&
      !Str::startsWith($prevPath, ['/accessories/detail', '/accessories/motor/']);

    // Pakai previous() hanya kalau benar-benar dari /accessories, selain itu pakai route accessories
    $backUrl = $isAccessoriesIndex ? url()->previous() : route('accessories');
  @endphp

  <div class="accd-back">
    <a href="{{ $backUrl }}" class="accd-back-link">
      <span class="accd-back-ico">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#111" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="11"/>
          <line x1="15" y1="12" x2="8" y2="12"/>
          <polyline points="12 16 8 12 12 8"/>
        </svg>
      </span>
      <span class="accd-back-txt">Kembali</span>
    </a>
    <div class="accd-back-rule"></div>
  </div>

  {{-- Stage: gambar motor + hotspot --}}
  <section class="accd-stage">
    <div class="accd-stage-wrap">
      <img src="{{ $stageImage }}" alt="{{ $motor->name }}" class="accd-stage-img">

      @foreach($hotspots as $hp)
        <button
          class="accd-hotspot js-hotspot"
          aria-label="{{ $hp['name'] ?? 'Hotspot' }}"
          title="{{ $hp['name'] ?? 'Hotspot' }}"
          style="top: {{ $hp['y'] }}%; left: {{ $hp['x'] }}%; transform: translate(-50%, -50%);"
          data-name="{{ $hp['name'] ?? '' }}"
          data-image="{{ $hp['image'] ?? '' }}"
          data-desc="{{ $hp['description'] ?? '' }}"
          data-side="{{ $hp['side'] ?? 'auto' }}"
        ></button>
      @endforeach
    </div>

    {{-- flyout card (satu saja, diisi ulang saat klik) --}}
    <div id="accd-pop" class="accd-pop" hidden>
      <div class="accd-pop-img"><img id="accd-pop-img" alt=""></div>
      <div class="accd-pop-body">
        <h5 id="accd-pop-title"></h5>
        <p id="accd-pop-desc"></p>
      </div>
    </div>
  </section>

  {{-- Grid kartu aksesoris --}}
  <section class="accd-grid page-acc-motor">
    @forelse($accessories as $acc)
      <article class="accd-card">
        <div class="accd-thumb">
          <img src="{{ $acc->image_url }}" alt="{{ $acc->name }}">
        </div>
        <div class="accd-body">
          <h3 class="accd-title">{{ $acc->name }}</h3>
          <div class="accd-price">
            <span>Harga</span>

            @if(is_null($acc->display_price) || $acc->display_price == 0)
              <strong class="accd-price-call">Hubungi dealer</strong>
            @else
              <strong>Rp {{ number_format($acc->display_price, 0, ',', '.') }}</strong>
            @endif
          </div>
          <a href="{{ route('accessory.detail', $acc->id) }}" class="accd-cta">
            <span>Detail</span>
            <i class="fas fa-chevron-right" aria-hidden="true"></i>
          </a>
        </div>
      </article>
    @empty
      <p class="text-center">Belum ada aksesoris untuk motor ini.</p>
    @endforelse
  </section>

  <div style="height:32px"></div>

  {{-- Script (tetap gaya inline seperti sebelumnya) --}}
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const pop  = document.getElementById('accd-pop');
    const imgE = document.getElementById('accd-pop-img');
    const ttlE = document.getElementById('accd-pop-title');
    const descE= document.getElementById('accd-pop-desc');

    function showPop(btn){
      ttlE.textContent = btn.dataset.name || 'Aksesoris';
      descE.textContent = btn.dataset.desc || '';
      if (btn.dataset.image) { imgE.src = btn.dataset.image; imgE.parentElement.style.display='block'; }
      else { imgE.removeAttribute('src'); imgE.parentElement.style.display='none'; }

      pop.style.visibility = 'hidden';
      pop.hidden = false;

      const stageRect = document.querySelector('.accd-stage').getBoundingClientRect();
      const btnRect   = btn.getBoundingClientRect();
      const gap       = 12;
      const sidePref  = (btn.dataset.side || 'auto').toLowerCase();
      const popW      = pop.offsetWidth;

      let left = (btnRect.right - stageRect.left) + gap;
      let top  = (btnRect.top   - stageRect.top)  - 10;
      if (top < 0) top = 0;

      if (sidePref === 'left' || (left + popW > stageRect.width)) {
        left = (btnRect.left - stageRect.left) - popW - gap;
      }

      pop.style.left = left + 'px';
      pop.style.top  = top  + 'px';
      pop.style.visibility = '';
    }

    function hidePop(){ pop.hidden = true; }

    document.querySelectorAll('.js-hotspot').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        showPop(btn);
      });
    });

    document.addEventListener('click', (e) => {
      if (!pop.hidden && !pop.contains(e.target)) hidePop();
    });

    window.addEventListener('resize', () => {
      if (pop.hidden) return;
      const active = document.querySelector('.js-hotspot.active');
      if (active) showPop(active);
    });
  });
  </script>
@endsection