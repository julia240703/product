@extends('layouts.appPublic')

@section('content')
  {{-- Backbar (opsional) --}}
  @if(!empty($showBack) && $showBack)
    <div class="backbar d-flex align-items-center gap-3 pb-2 mb-3">
      <a href="{{ $backUrl }}" class="accd-back-link">
        <span class="accd-back-ico" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
               stroke="#111" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
            <circle cx="12" cy="12" r="11"/>
            <line x1="15" y1="12" x2="8" y2="12"/>
            <polyline points="12 16 8 12 12 8"/>
          </svg>
        </span>
        <span class="fw-semibold">Kembali</span>
      </a>
    </div>
  @endif

  <div class="row">
    <div class="col-12">

      {{-- Judul --}}
      <h2 class="text-center mb-4">{{ $motor->name }}</h2>

      {{-- Banner --}}
      <section class="motor-banner mb-4">
        @if($banner && $banner->image_path)
          <img src="{{ $banner->image_path }}" alt="Banner {{ $motor->name }}" class="img-fluid w-100" style="border-radius:12px;">
        @else
          <p class="text-center text-danger">Gambar banner belum tersedia untuk motor ini.</p>
        @endif
      </section>

      {{-- 360° VIEW (GIF) – muncul di bawah banner --}}
      @if(!empty($spinUrl))
        <section class="motor-360 mb-5">
          <div class="text-center mb-2">
            {{-- pakai ikon yang sama seperti di admin --}}
            <img src="{{ asset('icon_360.png') }}" alt="360° View" style="height:56px;">
          </div>
          <div class="text-center">
            <img src="{{ $spinUrl }}" alt="Tampilan 360 {{ $motor->name }}" class="img-fluid"
                 style="max-width:1000px; width:100%; height:auto;">
          </div>
        </section>
      @endif

      {{-- Varian Warna --}}
      @php $colors = $motor->colors->sortBy('created_at')->values(); @endphp
      <section class="motor-colors mb-5 text-center">
        <h3 class="section-title">Varian Warna</h3>

        <div class="variant-viewport" id="variantViewport">
          <div class="variant-track" id="variantTrack">
            @foreach($colors as $i => $color)
              <div class="variant-item {{ $i===0 ? 'active' : '' }}"
                   data-index="{{ $i }}" data-color-id="{{ $color->id }}">
                <img src="{{ asset('storage/'.$color->image) }}" alt="{{ $color->name }}" class="variant-img">
                <p class="variant-name">{{ strtoupper($color->name) }}</p>
              </div>
            @endforeach
          </div>
        </div>

        <div class="color-selector mt-3">
          <h5 class="me-3 m-0">Pilih Warna</h5>
          @foreach($colors as $i => $color)
            @php
              $hex = $color->color_code ?? $color->hex_code ?? '#dddddd';
              if (!\Illuminate\Support\Str::startsWith($hex, '#')) $hex = '#'.$hex;
            @endphp
            <div class="color-dot {{ $i===0 ? 'active' : '' }}"
                 data-index="{{ $i }}" data-color-id="{{ $color->id }}"
                 title="{{ $color->name }}" style="--clr: {{ $hex }};"></div>
          @endforeach
        </div>
      </section>

      {{-- Spesifikasi --}}
      <section class="motor-specs mb-5">
        <h3 class="section-title">Spesifikasi {{ $motor->name }}</h3>
        @php $groups = $motor->specifications->sortBy('order')->groupBy('category'); @endphp
        @if($groups->isEmpty())
          <p class="text-muted text-center">Belum ada spesifikasi untuk motor ini.</p>
        @else
          <div class="accordion" id="specsAccordion">
  @foreach($groups as $cat => $rows)
    @php
      $headingId  = 'heading_'.$loop->index;
      $collapseId = 'collapse_'.$loop->index;
      $open = true; // default kebuka
    @endphp

    <div class="accordion-item">
      <h2 class="accordion-header" id="{{ $headingId }}">
        <button class="accordion-button {{ $open ? '' : 'collapsed' }}" type="button"
                data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}"
                aria-expanded="{{ $open ? 'true' : 'false' }}" aria-controls="{{ $collapseId }}">
          {{ $cat }}
        </button>
      </h2>

      <div id="{{ $collapseId }}" class="accordion-collapse collapse {{ $open ? 'show' : '' }}"
           aria-labelledby="{{ $headingId }}">
        <div class="accordion-body spec-body">
          <table class="table table-striped mb-0 spec-table">
            <tbody>
              @foreach($rows->sortBy('order') as $spec)
                <tr>
                  <td style="width:40%">{{ $spec->atribut }}</td>
                  <td>{{ $spec->detail }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  @endforeach
</div>
        @endif
      </section>

      {{-- Fitur + Hotspot --}}
      <section class="motor-features mb-5">
        <h3 class="section-title">Fitur {{ $motor->name }}</h3>
        <div id="hotspotStage" class="hotspot-stage position-relative d-inline-block">
          <img id="featureBase"
               src="{{ asset('storage/' . ($motor->feature_image ?? $motor->feature_thumbnail)) }}"
               alt="Fitur {{ $motor->name }}" class="img-fluid" style="border-radius:12px;">
          @foreach($motor->features as $f)
            <button class="hotspot"
                    data-id="{{ $f->id }}" data-side="{{ $f->preferred_side ?? 'auto' }}"
                    style="top: {{ $f->y_position }}%; left: {{ $f->x_position }}%; transform: translate(-50%,-50%);"
                    aria-label="{{ $f->name }}"></button>

            <div class="feature-card" id="card-{{ $f->id }}">
              <div class="d-flex">
                @if($f->image)
                  <img class="fc-img" src="{{ asset('storage/'.$f->image) }}" alt="{{ $f->name }}">
                @endif
                <div>
                  <h5 class="mb-2">{{ $f->name }}</h5>
                  <p class="mb-0 small text-muted">{!! nl2br(e($f->description)) !!}</p>
                </div>
              </div>
            </div>
          @endforeach
        </div>

        <div class="feature-hint-wrap">
          <div class="feature-hint" role="note">
            <span class="hint-ico"><span class="lens"></span><span class="dot"></span></span>
            <span class="hint-text">Ketuk titik pada motor untuk melihat fitur lengkap.</span>
          </div>
        </div>
      </section>

      {{-- Aksesoris --}}
      @if(isset($accessories) && $accessories->count())
        @php $accCount = $accessories->count(); @endphp
        <section class="motor-accessories page-product-detail mb-5">
          <h3 class="section-title">Aksesoris</h3>

          <div class="acc-wrap {{ $accCount <= 3 ? 'no-nav' : '' }}">
            @if($accCount > 3)
              <button class="acc-nav acc-prev" type="button" aria-label="Sebelumnya">
                <i class="fas fa-chevron-left"></i>
              </button>
            @endif

            <div class="acc-viewport {{ $accCount <= 3 ? 'is-static' : '' }}" id="accViewport">
              <div class="acc-track {{ $accCount <= 3 ? 'acc-center' : '' }}" id="accTrack">
                @foreach($accessories as $acc)
                  <article class="acc-card">
                    <div class="acc-img">
                      <img src="{{ $acc->image ? asset('storage/'.$acc->image) : asset('placeholder.png') }}"
                           alt="{{ $acc->name }}">
                    </div>
                    <div class="acc-body">
                      <h5 class="acc-title">{{ $acc->name }}</h5>

                      <div class="acc-price">
                        <span>Harga</span>
                        <strong>Rp 0</strong>
                      </div>

                      <a class="acc-cta" href="{{ route('accessories') }}#acc-{{ $acc->id }}">
                        <span>Detail</span><i class="fas fa-chevron-right"></i>
                      </a>
                    </div>
                  </article>
                @endforeach
              </div>
            </div>

            @if($accCount > 3)
              <button class="acc-nav acc-next" type="button" aria-label="Berikutnya">
                <i class="fas fa-chevron-right"></i>
              </button>
            @endif
          </div>

          <div class="acc-actions">
          <a href="{{ route('branches') }}" class="acc-action-btn">Dealer</a>
          <a href="{{ route('compare.menu') }}" class="acc-action-btn">Bandingkan</a>
          <a href="{{ route('price.list', ['return' => url()->current()]) }}" class="acc-action-btn">
            Price List
          </a>
          <a href="{{ route('credit.sim', ['motor_id' => $motor->id]) }}" class="acc-action-btn">Simulasi Kredit</a>
        </div>
        </section>
      @endif

      {{-- Rekomendasi --}}
@if(isset($recommended) && $recommended->count())
  <section class="rec-section">
    <h3 class="rec-title">Rekomendasi Varian Sesuai Pilihanmu</h3>
    <div class="rec-grid">
      @foreach($recommended as $r)
        <article class="product-card">
          <div class="product-image-left">
            <img
              src="{{ method_exists($r,'getThumbUrlAttribute') ? $r->thumb_url : ($r->image_url ?? asset('storage/' . ($r->thumbnail ?? ''))) }}"
              alt="{{ $r->name }}"
              class="img-fluid"
              style="max-height: 180px; object-fit: contain;">
          </div>
          <div class="product-info-right text-center">
            <h4 class="product-title">{{ $r->name }}</h4>
           <p class="product-subtitle">Harga Mulai</p>

            @if(!empty($r->rec_price_from_fmt))
              <div class="product-price">{{ $r->rec_price_from_fmt }}</div>
            @else
              <div class="product-price">Hubungi dealer</div>
            @endif

            <div class="buttons d-flex justify-content-center gap-2">
              <a href="{{ route('motor.detail', $r->id) }}" class="btn btn-outline-danger">Detail</a>
              <a href="{{ route('motors.compare') }}" class="btn btn-dark">Bandingkan</a>
            </div>
          </div>
        </article>
      @endforeach
    </div>

    <div class="see-more-wrapper">
      <a href="{{ route('produk') }}" class="btn-see-more">Lihat Selengkapnya</a>
    </div>
  </section>
@endif

    </div>
  </div>
@endsection

{{-- ====== JS: slider warna, hotspot, carousel aksesoris ====== --}}
@push('scripts')
<script>
/* ========== Varian Warna ========== */
document.addEventListener("DOMContentLoaded", function(){
  const viewport = document.getElementById("variantViewport");
  const track    = document.getElementById("variantTrack");
  const items    = Array.from(document.querySelectorAll(".variant-item"));
  const dots     = Array.from(document.querySelectorAll(".color-dot"));
  if(!viewport || !track || items.length===0) return;

  let currentIndex = 0;
  function applyLayout(i){
    viewport.classList.remove("layout-2","layout-3");
    if(items.length<=2){ viewport.classList.add("layout-2"); return 2; }
    const atEdge = (i===0 || i===items.length-1);
    viewport.classList.add(atEdge ? "layout-2" : "layout-3");
    return atEdge ? 2 : 3;
  }
  function markNeighbors(i){
    items.forEach(el=>el.classList.remove("active","is-left","is-right"));
    items[i]?.classList.add("active");
    items[i-1]?.classList.add("is-left");
    items[i+1]?.classList.add("is-right");
  }
  function centerTo(i){
    currentIndex = Math.max(0, Math.min(i, items.length-1));
    applyLayout(currentIndex);
    const vpW = viewport.clientWidth;
    const active = items[currentIndex];
    const aLeft = active.offsetLeft;
    const aWidth = active.offsetWidth;
    const trackWidth = track.scrollWidth;
    let targetX = aLeft + (aWidth/2) - (vpW/2);
    const maxScroll = trackWidth - vpW;
    targetX = Math.max(0, Math.min(targetX, maxScroll));
    if(currentIndex===0) targetX = 0;
    else if(currentIndex===items.length-1) targetX = maxScroll;
    track.style.transform = `translateX(${-targetX}px)`;
    markNeighbors(currentIndex);
    dots.forEach((d,idx)=>d.classList.toggle("active", idx===currentIndex));
  }
  centerTo(0);
  dots.forEach(d=>d.addEventListener("click",()=>centerTo(parseInt(d.dataset.index,10))));
  items.forEach(it=>it.addEventListener("click",()=>centerTo(parseInt(it.dataset.index,10))));
  let tid; window.addEventListener("resize",()=>{ clearTimeout(tid); tid=setTimeout(()=>centerTo(currentIndex),150); });
  window.addEventListener("load",()=>centerTo(currentIndex));
});

/* ========== Hotspot Fitur ========== */
document.addEventListener('DOMContentLoaded', () => {
  const stage = document.getElementById('hotspotStage');
  if(!stage) return;
  const cards = Array.from(stage.querySelectorAll('.feature-card'));
  const closeAll = () => cards.forEach(c => c.classList.remove('show'));

  function positionCard(dot, card){
    const stageRect = stage.getBoundingClientRect();
    const dotRect = dot.getBoundingClientRect();
    const gap = 12;
    card.style.display='block';
    const rect = card.getBoundingClientRect();
    const leftDot = dotRect.left - stageRect.left;
    const topDot  = dotRect.top  - stageRect.top;
    const clampY = t => Math.max(0, Math.min(t, stageRect.height - rect.height));
    const clampX = l => Math.max(0, Math.min(l, stageRect.width  - rect.width));
    const pref = (dot.dataset.side || 'auto').toLowerCase();
    const placeRight = ()=>{ const l=leftDot+16+gap; if(l+rect.width<=stageRect.width) return {left:l, top:clampY(topDot-rect.height/2)}; };
    const placeLeft  = ()=>{ const l=leftDot-rect.width-16-gap; if(l>=0) return {left:l, top:clampY(topDot-rect.height/2)}; };
    const placeTop   = ()=>{ const t=topDot-rect.height-16-gap; if(t>=0) return {left:clampX(leftDot-rect.width/2), top:t}; };
    const placeBottom= ()=>{ const t=topDot+16+gap; if(t+rect.height<=stageRect.height) return {left:clampX(leftDot-rect.width/2), top:t}; };
    const order = pref==='right'?[placeRight,placeLeft,placeTop,placeBottom]
                : pref==='left' ?[placeLeft,placeRight,placeTop,placeBottom]
                :                [placeRight,placeLeft,placeBottom,placeTop];
    let pos=null; for(const fn of order){ pos=fn(); if(pos)break; }
    if(!pos) pos={left:clampX(leftDot-rect.width/2), top:clampY(topDot-rect.height/2)};
    card.style.left=pos.left+'px'; card.style.top=pos.top+'px';
  }

  document.addEventListener('click',(e)=>{
    const isCard=e.target.closest('.feature-card');
    const isHotspot=e.target.closest('.hotspot');
    if(isHotspot){
      const id=isHotspot.dataset.id; const card=stage.querySelector('#card-'+id);
      if(card.classList.contains('show')) card.classList.remove('show');
      else { closeAll(); card.classList.add('show'); positionCard(isHotspot,card); }
      return;
    }
    if(!isCard) closeAll();
  });
  document.addEventListener('keydown',e=>{ if(e.key==='Escape') closeAll(); });
  window.addEventListener('resize',()=>{
    const open=stage.querySelector('.feature-card.show'); if(!open) return;
    const id=open.id.replace('card-',''); const btn=stage.querySelector(`.hotspot[data-id="${id}"]`);
    if(btn) positionCard(btn, open);
  });
});

/* ========== Carousel Aksesoris ========== */
document.addEventListener('DOMContentLoaded', () => {
  const vp   = document.getElementById('accViewport');
  const tr   = document.getElementById('accTrack');
  const prev = document.querySelector('.acc-prev');
  const next = document.querySelector('.acc-next');
  if (!vp || !tr) return;

  const cards = Array.from(tr.querySelectorAll('.acc-card'));
  const cardCount = cards.length;
  const arrowsNeeded = cardCount > 3;

  const stepPx = () => {
    const card = cards[0];
    if (!card) return 320;
    const cs = getComputedStyle(card);
    const margin = parseFloat(cs.marginLeft||0) + parseFloat(cs.marginRight||0);
    return Math.round(card.getBoundingClientRect().width + margin + 22);
  };

  if (!arrowsNeeded) {
    document.querySelectorAll('.acc-nav').forEach(el => el?.classList.add('is-hidden'));
    vp.classList.add('is-static');
    tr.classList.add('acc-center');
    tr.style.justifyContent = 'center';
  } else {
    function scrollByDir(dir){ vp.scrollBy({ left: dir * stepPx(), behavior: 'smooth' }); }
    prev?.addEventListener('click', () => scrollByDir(-1));
    next?.addEventListener('click', () => scrollByDir(1));

    let isDown=false, startX=0, startLeft=0;
    vp.addEventListener('pointerdown', e => {
      isDown=true; startX=e.clientX; startLeft=vp.scrollLeft; vp.setPointerCapture?.(e.pointerId);
    });
    vp.addEventListener('pointermove', e => { if(!isDown) return; vp.scrollLeft = startLeft - (e.clientX - startX); });
    const stop=()=>isDown=false; vp.addEventListener('pointerup',stop); vp.addEventListener('pointerleave',stop);
  }

  let rid; window.addEventListener('resize', () => { cancelAnimationFrame(rid); rid=requestAnimationFrame(() => stepPx()); });
});
</script>
@endpush