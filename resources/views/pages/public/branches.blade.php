@extends('layouts.appPublic')

@section('content')
  <div class="dealer-page">
    {{-- ===== HERO ===== --}}
    <div class="dealer-hero text-center dealer-top">
      <h1 class="dealer-h1">Cari Dealer</h1>
      <div class="dealer-redline"></div>
      <h2 class="dealer-tagline">Motor Honda Idaman Anda Ada di Sini – Temukan Dealernya!</h2>
    </div>

    {{-- ===== FILTER BAR ===== --}}
    <form id="dealerForm" class="dealer-filter dealer-flex dealer-top" action="{{ route('branches') }}" method="get">
      <div class="dealer-field">
        <label class="dealer-label">Pilih Area</label>
        <select name="area" class="dealer-control dealer-select" onchange="this.form.submit()">
          <option value="">Area</option>
          @foreach($areas as $a)
            <option value="{{ $a->id }}" @selected($active['area']==$a->id)>{{ $a->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="dealer-field">
        <label class="dealer-label">Pilih Cabang</label>
        <select name="cabang" class="dealer-control dealer-select" onchange="this.form.submit()">
          <option value="">Cabang</option>
          @foreach($cities as $c)
            <option value="{{ $c->id }}" @selected($active['cabang']==$c->id)>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="dealer-field">
        <label class="dealer-label">Pilih Layanan</label>
        <select name="layanan" class="dealer-control dealer-select" onchange="this.form.submit()">
          <option value="">Layanan</option>
          @foreach($serviceTokens as $s)
            @php $label = trim(preg_replace('/^dan\s+/i', '', rtrim($s, '.'))); @endphp
            <option value="{{ $s }}" @selected($active['layanan']==$s)>{{ $label }}</option>
          @endforeach
        </select>
      </div>

      <div class="dealer-field">
        <label class="dealer-label">Cari Nama Dealer</label>
        <input id="dealerSearch" type="text" name="q" value="{{ $active['q'] }}" class="dealer-control"
               placeholder="Nama Dealer" inputmode="none" autocomplete="off">
      </div>

      <div class="dealer-btn-wrap">
        <div class="dealer-btn-row dealer-btn-row--right">
          <button class="dealer-search-btn" type="submit" aria-label="Cari dealer">Cari</button>
          <a href="{{ route('branches') }}" class="dealer-clear-btn" aria-label="Reset filter">Clear</a>
        </div>
      </div>
    </form>

    {{-- ===== LIST & MAP ===== --}}
    <div class="dealer-split">
      {{-- LIST dealer --}}
      <div class="dealer-list-pane" id="dealerList">
        @forelse($branches as $b)
          @php
            // deteksi layanan ➜ H-code
            $svcRaw = strtolower(trim(implode(' ', array_filter([
              $b->services ?? null,
              $b->service ?? null,
              $b->service_tokens ?? null,
              $b->token ?? null,
              $b->tags ?? null
            ]))));
            $h1 = (!empty($b->h1)) || str_contains($svcRaw,'sales') || str_contains($svcRaw,'penjualan');
            $h2 = (!empty($b->h2)) || str_contains($svcRaw,'service') || str_contains($svcRaw,'perawatan');
            $h3 = (!empty($b->h3)) || str_contains($svcRaw,'part')   || str_contains($svcRaw,'suku');

            $flags=[]; if($h1)$flags[]='1'; if($h2)$flags[]='2'; if($h3)$flags[]='3';
            $hcode = $flags ? 'H'.implode('', $flags) : null;

            $lat = $b->latitude; $lng = $b->longitude;
            $key = ($lat && $lng) ? ($lat.','.$lng) : '';
          @endphp

          <div class="dealer-item dealer-card js-dealer"
               data-lat="{{ $lat }}" data-lng="{{ $lng }}"
               data-key="{{ $key }}"
               data-name="{{ $b->name }}" data-addr="{{ $b->address }}"
               data-phone="{{ $b->phone ?? $b->phone2 ?? $b->phone3 }}">
            <div class="dealer-card-body">
              {{-- Kiri: info dealer --}}
              <div class="dealer-info">
                <div class="dealer-name">{{ $b->name }}</div>

                <div class="dealer-row">
                  <i class="fas fa-map-marker-alt ico" style="color:#d32b2b"></i>
                  <div class="dealer-text">{{ $b->address }}</div>
                </div>

                @php $telp = $b->phone ?? $b->phone2 ?? $b->phone3; @endphp
                @if($telp)
                  <div class="dealer-row">
                    <i class="fas fa-phone ico" style="color:#d32b2b"></i>
                    <div class="dealer-text">{{ $telp }}</div>
                  </div>
                @endif

                @php
                  $maps = $b->url ?: (($lat && $lng) ? "https://www.google.com/maps?q={$lat},{$lng}"
                         : 'https://www.google.com/maps/search/?api=1&query='.rawurlencode(($b->name ?? '').' '.$b->address));
                @endphp
                <a href="{{ $maps }}" target="_blank" class="btn btn-danger dealer-cta" onclick="event.stopPropagation()">
                  Lihat Lokasi <span aria-hidden="true">➤</span>
                </a>
              </div>

              {{-- Kanan: H123 + label layanan --}}
              <aside class="dealer-meta">
                @if($hcode)<div class="dealer-hcode">{{ $hcode }}</div>@endif
                @php
                  $labels = [];
                  if ($h1) $labels[] = 'Penjualan';
                  if ($h2) $labels[] = 'Perawatan';
                  if ($h3) $labels[] = 'Suku cadang';
                @endphp
                <div class="dealer-meta-lines">
                  @if(count($labels) === 3)
                    <div>Penjualan,</div>
                    <div>Perawatan,</div>
                    <div>dan Suku</div><div>cadang.</div>
                  @elseif(count($labels) === 2)
                    <div>{{ $labels[0] }},</div>
                    @if($labels[1] === 'Suku cadang')
                      <div>dan Suku</div><div>cadang.</div>
                    @else
                      <div>dan {{ $labels[1] }}.</div>
                    @endif
                  @elseif(count($labels) === 1)
                    <div>{{ $labels[0] }}</div>
                  @endif
                </div>
              </aside>
            </div>
          </div>

          @if(!$loop->last)
            <hr class="dealer-sep">
          @endif
        @empty
          <div class="text-muted p-3">Tidak ada dealer yang cocok dengan filter.</div>
        @endforelse
      </div>

      {{-- MAP (JS API jika ada key; kalau tidak, pakai EMBED) --}}
      <div class="dealer-map-pane">
        @php
          $first = $markers->first();
          if ($first && !empty($first['lat']) && !empty($first['lng'])) {
            $q = $first['lat'].','.$first['lng'];
          } elseif ($first) {
            $q = rawurlencode(($first['name'] ?? '').' '.$first['address']);
          } else {
            $q = ($center['lat'] ?? -6.2).','.($center['lng'] ?? 106.8);
          }
          $embedUrl = 'https://www.google.com/maps?output=embed&q='.$q.'&z=12';
        @endphp

        @if(!empty($mapsKey))
          <div id="dealerMap" class="dealer-map-canvas"></div>
          {{-- Panel info pojok kiri --}}
          <div id="mapCornerCard" class="map-corner-card hidden" aria-live="polite"></div>
        @else
          <iframe id="dealerEmbed" class="dealer-map-canvas" src="{{ $embedUrl }}" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>
        @endif
      </div>
    </div>
  </div>

  {{-- ===== ON-SCREEN KEYBOARD (fixed) ===== --}}
  <div id="osk-backdrop" class="osk-backdrop" hidden></div>
  <div id="osk" class="osk" hidden aria-hidden="true" role="dialog" aria-label="Keyboard">
    <div class="osk-head">
      <span class="osk-title">Keyboard</span>
      <button type="button" class="osk-close" aria-label="Tutup">×</button>
    </div>
    <div class="osk-keys" aria-live="polite"></div>
  </div>

  {{-- ===== QR MAP MODAL (tanpa tombol link) ===== --}}
  <div id="qrMapModal" class="qrmm" hidden>
    <div class="qrmm__backdrop" data-close></div>
    <div class="qrmm__box" role="dialog" aria-modal="true" aria-labelledby="qrmmTitle">
      <button class="qrmm__close" type="button" aria-label="Tutup" data-close>×</button>
      <h3 id="qrmmTitle" class="qrmm__title">Buka Lokasi via QR</h3>
      <p class="qrmm__tag">
        Scan QR ini untuk melihat <strong>rute, detail lokasi, &amp; jam operasional</strong> di Google Maps.
        Cukup arahkan kamera ponsel kamu!
      </p>
      <div id="qrmmCanvas" class="qrmm__canvas" aria-live="polite"></div>
    </div>
  </div>
@endsection

@push('scripts')
  {{-- Aktifkan fix dropdown native + jaga sidebar tetap fixed --}}
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      document.querySelector('.app-shell')?.classList.add('layout-has-native-dd');
    });
  </script>

  {{-- ===== On-Screen Keyboard ===== --}}
  <script>
  (function(){
    const form   = document.getElementById('dealerForm');
    const input  = document.getElementById('dealerSearch');
    const osk    = document.getElementById('osk');
    const keysEl = osk?.querySelector('.osk-keys');
    const backd  = document.getElementById('osk-backdrop');
    const btnX   = osk?.querySelector('.osk-close');

    if (!osk || !keysEl || !input) return;

    const rows = [
      ['1','2','3','4','5','6','7','8','9','0','-','_','⌫'],
      ['q','w','e','r','t','y','u','i','o','p','@','.','/'],
      ['Caps','a','s','d','f','g','h','j','k','l','Clear'],
      ['z','x','c','v','b','n','m','Space','Enter']
    ];
    const spans = { '⌫':2, 'Caps':2, 'Clear':2, 'Space':5, 'Enter':3 };

    let caps = false, activeInput = null, holdTimer = null;

    function build(){
      keysEl.innerHTML = '';
      rows.forEach(row=>{
        row.forEach(label=>{
          const b = document.createElement('button');
          b.type = 'button';
          b.className = 'osk-key' + (spans[label] ? (' wide'+spans[label]) : '');
          if (['⌫','Caps','Clear','Space','Enter'].includes(label)) b.classList.add('fn');
          if (label === 'Enter') b.classList.add('accent');
          b.textContent = label;
          b.dataset.key = label;
          keysEl.appendChild(b);
        });
      });
    }
    build();

    function insertAtCaret(el, txt){
      const s = el.selectionStart ?? el.value.length;
      const e = el.selectionEnd ?? el.value.length;
      const before = el.value.slice(0, s), after = el.value.slice(e);
      el.value = before + txt + after;
      const pos = s + txt.length;
      el.setSelectionRange(pos, pos);
      el.dispatchEvent(new Event('input', {bubbles:true}));
    }
    function backspaceAtCaret(el){
      const s = el.selectionStart ?? 0, e = el.selectionEnd ?? 0;
      if (s !== e){
        const before = el.value.slice(0, s), after = el.value.slice(e);
        el.value = before + after; el.setSelectionRange(s, s);
      } else if (s > 0){
        const before = el.value.slice(0, s-1), after = el.value.slice(e);
        el.value = before + after; el.setSelectionRange(s-1, s-1);
      }
      el.dispatchEvent(new Event('input', {bubbles:true}));
    }

    function open(el){
      activeInput = el || input;
      document.body.classList.add('osk-open');
      osk.hidden = false; backd.hidden = false;
      osk.setAttribute('aria-hidden','false');
      requestAnimationFrame(()=>{
        activeInput.focus();
        const n = activeInput.value.length;
        activeInput.setSelectionRange(n, n);
      });
    }
    function close(){
      osk.hidden = true; backd.hidden = true;
      osk.setAttribute('aria-hidden','true');
      document.body.classList.remove('osk-open');
      activeInput && activeInput.blur();
      activeInput = null; caps = false;
      keysEl.querySelectorAll('.osk-key').forEach(k=>{
        if (k.dataset.key === 'Caps') k.classList.remove('muted');
      });
    }

    input.addEventListener('focus', ()=> open(input));
    input.addEventListener('click', ()=> open(input));
    backd.addEventListener('click', close);
    btnX.addEventListener('click', close);

    keysEl.addEventListener('mousedown', e=>{
      const key = e.target.closest('.osk-key'); if (!key || !activeInput) return;
      const k = key.dataset.key;
      e.preventDefault();

      if (k === '⌫'){
        backspaceAtCaret(activeInput);
        holdTimer = setTimeout(function rep(){ backspaceAtCaret(activeInput); holdTimer=setTimeout(rep, 50); }, 350);
        return;
      }
      if (k === 'Caps'){ caps = !caps; key.classList.toggle('muted', caps); return; }
      if (k === 'Clear'){ activeInput.value=''; activeInput.dispatchEvent(new Event('input', {bubbles:true})); return; }
      if (k === 'Space'){ insertAtCaret(activeInput, ' '); return; }
      if (k === 'Enter'){ close(); form?.submit(); return; }

      insertAtCaret(activeInput, caps ? k.toUpperCase() : k);
    });
    document.addEventListener('mouseup', ()=>{ if (holdTimer){ clearTimeout(holdTimer); holdTimer=null; } });
  })();
  </script>

  {{-- ===== Sinkron list <-> map ===== --}}
  <script>
  (function(){
    const list = document.getElementById('dealerList');

    function selectDealer(card) {
      if (!card) return;
      document.querySelectorAll('.js-dealer.is-active').forEach(el=>el.classList.remove('is-active'));
      card.classList.add('is-active');

      const lat   = parseFloat(card.dataset.lat);
      const lng   = parseFloat(card.dataset.lng);
      const key   = card.dataset.key || (lat+','+lng);

      if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;

      if (window._gmap && window._gmap.byKey) {
        const mk = _gmap.byKey.get(key);
        if (mk) {
          google.maps.event.trigger(mk, 'click');
          _gmap.map.panTo({lat, lng});
          if (_gmap.map.getZoom() < 15) _gmap.map.setZoom(15);
        }
      } else {
        const ifr = document.getElementById('dealerEmbed');
        if (ifr) ifr.src = `https://www.google.com/maps?output=embed&q=${lat},${lng}&z=15`;
      }
    }

    if (list) {
      list.addEventListener('click', function(e){
        if (e.target.closest('a,button')) return;
        const card = e.target.closest('.js-dealer');
        if (card) selectDealer(card);
      });
    }

    window.__selectDealerCardByKey = function(key){
      const card = document.querySelector(`.js-dealer[data-key="${key}"]`);
      if (card) {
        document.querySelectorAll('.js-dealer.is-active').forEach(el=>el.classList.remove('is-active'));
        card.classList.add('is-active');
        const pane = document.querySelector('.dealer-list-pane');
        if (pane) pane.scrollTo({top: card.offsetTop - 16, behavior:'smooth'});
      }
    };
  })();
  </script>

  @if(!empty($mapsKey))
    <script>
      window.DEALER_MARKERS = @json($markers);
      window.DEALER_CENTER  = @json($center);

      function initDealerMap(){
        if(!window.google || !google.maps) return;

        const map = new google.maps.Map(document.getElementById('dealerMap'), {
          center: window.DEALER_CENTER || {lat:-6.2, lng:106.8}, zoom: 11,
          mapTypeControl:true, streetViewControl:false, fullscreenControl:true
        });

        class BelowPopup extends google.maps.OverlayView {
          constructor(map){ super(); this.map = map; this.position = null; this.div = null; this.setMap(map); }
          onAdd(){ this.div = document.createElement('div'); this.div.className = 'gm-below'; this.div.style.display = 'none'; this.getPanes().floatPane.appendChild(this.div); }
          onRemove(){ if (this.div && this.div.parentNode) this.div.parentNode.removeChild(this.div); this.div = null; }
          draw(){ if (!this.position || !this.div) return; const proj = this.getProjection(); const pix = proj.fromLatLngToDivPixel(this.position); if (!pix) return; this.div.style.left = pix.x + 'px'; this.div.style.top = pix.y + 'px'; this.div.style.display = 'block'; }
          openAt(latlng, html){ this.position = (latlng instanceof google.maps.LatLng) ? latlng : new google.maps.LatLng(latlng.lat, latlng.lng); if (this.div) { this.div.innerHTML = `<div class="gm-below-inner">${html}<span class="gm-below-arrow" aria-hidden="true"></span></div>`; } this.draw(); }
          hide(){ if (this.div) this.div.style.display = 'none'; }
        }

        const bounds   = new google.maps.LatLngBounds();
        const byKey    = new Map();
        const markers  = [];
        const cornerEl = document.getElementById('mapCornerCard');
        const below    = new BelowPopup(map);

        function ensureMapsUrl(m){
          if (m.mapsUrl) return m.mapsUrl;
          if (m.lat && m.lng) return `https://www.google.com/maps?q=${m.lat},${m.lng}`;
          const q = encodeURIComponent(`${m.name ?? ''} ${m.address ?? ''}`);
          return `https://www.google.com/maps/search/?api=1&query=${q}`;
        }
        function renderCornerCard(m){
          if (!cornerEl) return;
          const url = ensureMapsUrl(m);
          cornerEl.innerHTML = `
            <div class="mcc-title">${m.name ?? ''}</div>
            <div class="mcc-addr">${m.address ?? ''}</div>
            ${m.phone ? `<div class="mcc-phone">${m.phone}</div>` : ''}
            <a class="mcc-link" href="${url}" target="_blank" rel="noopener">Buka di Google Maps</a>
          `;
          cornerEl.classList.remove('hidden');
        }
        function renderBelowHTML(m){
          return `
            <div class="gm-title">${m.name ?? ''}</div>
            <div class="gm-addr">${m.address ?? ''}</div>
            ${m.phone ? `<div class="gm-phone">Telp: ${m.phone}</div>` : ''}
          `;
        }

        (window.DEALER_MARKERS || []).forEach(m=>{
          if(!m.lat || !m.lng) return;
          const pos = {lat:Number(m.lat), lng:Number(m.lng)};
          const key = `${pos.lat},${pos.lng}`;

          const marker = new google.maps.Marker({map, position: pos});
          byKey.set(key, marker);
          markers.push(marker);
          bounds.extend(pos);

          marker.addListener('click', ()=>{
            below.openAt(marker.getPosition(), renderBelowHTML(m));
            renderCornerCard(m);
            if (window.__selectDealerCardByKey) window.__selectDealerCardByKey(key);
            map.panTo(pos);
            if (map.getZoom() < 15) map.setZoom(15);
          });
        });

        if(!bounds.isEmpty()) map.fitBounds(bounds);

        window._gmap = {map, byKey, below};

        if (window.MarkerClusterer) new MarkerClusterer({ map, markers });
      }

      window.initDealerMap = initDealerMap;
    </script>
    <script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js" defer></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $mapsKey }}&callback=initDealerMap" async defer></script>
  @endif

  {{-- ===== QR modal: lib + handler (mencegat klik, tak ubah HTML lama) ===== --}}
  <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js" defer></script>
  <script>
    (function(){
      const modal  = document.getElementById('qrMapModal');
      const canvas = document.getElementById('qrmmCanvas');
      const title  = document.getElementById('qrmmTitle');
      if(!modal || !canvas || !title) return;

      function openQR(name, url){
        title.textContent = name ? `Scan untuk Buka: ${name}` : 'Buka Lokasi via QR';
        canvas.innerHTML = '';
        const render = () => {
          if (window.QRCode) {
            new QRCode(canvas, { text: url || location.href, width: 300, height: 300, correctLevel: QRCode.CorrectLevel.M });
          } else {
            setTimeout(render, 30);
          }
        };
        render();
        modal.hidden = false;
      }
      function closeQR(){ modal.hidden = true; canvas.innerHTML=''; }

      // Cegat klik "Lihat Lokasi" supaya tidak buka tab baru
      document.addEventListener('click', function(e){
        const a = e.target.closest('a.dealer-cta[href]');
        if(!a) return;
        e.preventDefault();
        e.stopPropagation();
        if (typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation();

        const card = a.closest('.js-dealer');
        const name = card?.dataset?.name ||
                     a.closest('.dealer-info')?.querySelector('.dealer-name')?.textContent?.trim() || '';
        const url  = a.getAttribute('href');
        openQR(name, url);
      }, true); // capture

      modal.addEventListener('click', (e)=>{ if (e.target.hasAttribute('data-close')) closeQR(); });
      document.addEventListener('keydown', (e)=>{ if (e.key === 'Escape' && !modal.hidden) closeQR(); });
    })();
  </script>
@endpush