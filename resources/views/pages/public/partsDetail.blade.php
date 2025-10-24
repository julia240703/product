@extends('layouts.appPublic')
<title>Katalog Part — {{ $motor->name }}</title>

@section('content')
@php
  use Illuminate\Support\Str;

  $backUrl   = route('parts', ['key' => optional($motor->category)->id]);
  $heroImg   = $imageUrl ?? asset('placeholder.png');

  // URL PDF absolut
  $pdfAbsUrl = $pdfUrl
    ? (Str::startsWith($pdfUrl, ['http://','https://']) ? $pdfUrl : url($pdfUrl))
    : null;

  // PDF.js viewer (same-origin)
  $viewerUrl = $pdfAbsUrl
    ? url('/pdfjs/web/viewer.html') . '?file=' . urlencode($pdfAbsUrl)
    : null;
@endphp

<div class="accd-detail">

  {{-- Back bar --}}
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

  {{-- HERO --}}
  <div class="card parts-hero border-0 shadow-sm text-center mb-3">
    <div class="card-body">
      <h1 class="parts-hero-title">{{ $motor->name }}</h1>
      <div class="text-muted text-uppercase mb-3" style="letter-spacing:.5px;">
        {{ strtoupper(optional($motor->category)->name ?? '-') }}
      </div>
      <img src="{{ $heroImg }}" alt="{{ $motor->name }}" class="parts-hero-img mx-auto d-block">
    </div>
  </div>

  {{-- Copy --}}
  <div class="text-center mb-3">
    <h2 class="h5 fw-bold mb-1">Katalog Part</h2>
    <p class="text-muted mb-0">
      @if($viewerUrl)
        Gunakan kontrol toolbar di bawah ini untuk menelusuri katalog.
      @else
        Katalog part belum tersedia untuk model ini.
      @endif
    </p>
  </div>

  {{-- PDF viewer (PDF.js via iframe) --}}
  @if($viewerUrl)
    <div class="card pdfjs-wrap shadow-sm" id="pdfJsCard">
      <iframe id="pdfJsFrame" class="pdfjs-frame" src="{{ $viewerUrl }}"
              title="Katalog Part {{ $motor->name }}"></iframe>
    </div>
  @endif

  <div class="mb-5"></div>
</div>

{{-- ===== On-Screen Keyboard (pakai gaya dealer) ===== --}}
<div id="osk-backdrop" class="osk-backdrop" hidden></div>
<div id="osk" class="osk" hidden aria-hidden="true" role="dialog" aria-label="Keyboard">
  <div class="osk-head">
    <span class="osk-title">Keyboard</span>
    <button type="button" class="osk-close" aria-label="Tutup">×</button>
  </div>
  <div class="osk-keys" aria-live="polite"></div>
</div>

<script>
/* ===== Parent ↔ PDF.js + OSK ===== */
(function(){
  const frame  = document.getElementById('pdfJsFrame');
  if(!frame) return;

  // OSK elemen (reuse CSS dealer)
  const osk    = document.getElementById('osk');
  const keysEl = osk?.querySelector('.osk-keys');
  const backd  = document.getElementById('osk-backdrop');
  const btnX   = osk?.querySelector('.osk-close');
  const title  = osk?.querySelector('.osk-title');

  if(!osk || !keysEl) return;

  // State
  let mode = 'text';      // 'text' | 'num'
  let buffer = '';        // teks yg sedang diketik
  let maxPage = 0;        // untuk numpad
  let debTimer = null;

  // Layout OSK
  const rowsText = [
    ['1','2','3','4','5','6','7','8','9','0','-','_','⌫'],
    ['q','w','e','r','t','y','u','i','o','p','@','.','/'],
    ['Caps','a','s','d','f','g','h','j','k','l','Clear'],
    ['z','x','c','v','b','n','m','Space','Enter']
  ];
  const rowsNum  = [
    ['7','8','9','⌫'],
    ['4','5','6','Clear'],
    ['1','2','3','Enter'],
    ['0']
  ];
  const spans = { '⌫':2,'Caps':2,'Clear':2,'Space':5,'Enter':3 };

  function build(layout){
    keysEl.innerHTML = '';
    layout.forEach(row=>{
      row.forEach(label=>{
        const b=document.createElement('button');
        b.type='button';
        b.className='osk-key'+(spans[label]?(' wide'+spans[label]):'')+(['⌫','Caps','Clear','Space','Enter'].includes(label)?' fn':'');
        if(label==='Enter') b.classList.add('accent');
        b.textContent=label; b.dataset.key=label;
        keysEl.appendChild(b);
      });
    });
  }

  function openOSKText(){
    mode='text'; buffer='';
    title.textContent='Cari (Find)';
    build(rowsText);
    showOSK();
  }
  function openOSKNum(current, max){
    mode='num';
    buffer = String(current||'').replace(/\D+/g,'');
    maxPage = Number(max||0) || 0;
    title.textContent = maxPage ? `Lompat Halaman (1–${maxPage})` : 'Lompat Halaman';
    build(rowsNum);
    showOSK();
  }

  function showOSK(){
    osk.hidden=false; backd.hidden=false; osk.setAttribute('aria-hidden','false');
    document.body.classList.add('osk-open');
  }
  function closeOSK(){
    osk.hidden=true; backd.hidden=true; osk.setAttribute('aria-hidden','true');
    document.body.classList.remove('osk-open');
  }

  // Kirim ke iframe
  function sendToViewer(type, payload){
    try{ frame.contentWindow.postMessage({ type, payload }, '*'); }catch(_){}
  }

  // Text: kirim eksekusi pencarian (debounce biar halus)
  function pushFind(){
    clearTimeout(debTimer);
    debTimer = setTimeout(()=> sendToViewer('PDF_FIND_EXECUTE', { term: buffer }), 120);
  }

  // Numpad: validasi range
  function clampPage(v){
    let n = parseInt(v||'0', 10);
    if(!Number.isFinite(n) || n<=0) n = 1;
    if(maxPage>0) n = Math.min(n, maxPage);
    return n;
  }

  // Handle tombol OSK
  let caps=false;
  keysEl.addEventListener('mousedown', e=>{
    const key = e.target.closest('.osk-key'); if(!key) return;
    const k = key.dataset.key; e.preventDefault();

    if(k==='Caps'){ caps=!caps; key.classList.toggle('muted',caps); return; }
    if(k==='Clear'){ buffer=''; if(mode==='text') pushFind(); return; }
    if(k==='⌫'){
      buffer = buffer.slice(0,-1);
      if(mode==='text') pushFind();
      return;
    }
    if(k==='Space' && mode==='text'){ buffer += ' '; pushFind(); return; }
    if(k==='Enter'){
      if(mode==='text'){ sendToViewer('PDF_FIND_EXECUTE', { term: buffer }); }
      else{
        const page = clampPage(buffer || '1');
        sendToViewer('PDF_PAGE_JUMP', { page });
      }
      closeOSK();
      return;
    }

    // karakter biasa
    const char = (mode==='text') ? (caps ? k.toUpperCase() : k) : (/\d/.test(k) ? k : '');
    if(!char) return;
    buffer += char;
    if(mode==='text') pushFind();
  });

  backd.addEventListener('click', closeOSK);
  document.querySelector('.osk-close')?.addEventListener('click', closeOSK);

  // Terima sinyal dari viewer.html
  window.addEventListener('message', (e)=>{
    const data = e.data || {};
    switch (data.type) {
      case 'PDF_OSK_OPEN_TEXT':
        openOSKText();
        break;
      case 'PDF_OSK_OPEN_NUM':
        openOSKNum(data?.payload?.current, data?.payload?.max);
        break;
      case 'PDF_FIND_UPDATE': {
        // opsional: tampilkan counter di title
        const cur = data?.payload?.cur || 0, total = data?.payload?.total || 0;
        if(!osk.hidden && mode==='text') title.textContent = `Cari ( ${cur}/${total} )`;
        break;
      }
      case 'PDF_FIND_CLOSE':
        if(!osk.hidden && mode==='text') closeOSK();
        break;
    }
  });
})();
</script>
@endsection