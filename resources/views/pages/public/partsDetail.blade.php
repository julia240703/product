@extends('layouts.appPublic')
<title>Katalog Part — {{ $motor->name }}</title>

@section('content')
@php
  use Illuminate\Support\Str;

  $backUrl   = route('parts', ['key' => optional($motor->category)->id]);
  $heroImg   = $imageUrl ?? asset('placeholder.png');

  // URL PDF harus absolute biar aman di PDF.js
  $pdfAbsUrl = $pdfUrl
    ? (Str::startsWith($pdfUrl, ['http://','https://']) ? $pdfUrl : url($pdfUrl))
    : null;

  // Pakai PDF.js viewer.html (full toolbar) + matikan Download & Print
  // Pastikan file ini ada di public/pdfjs/web/viewer.html
  $viewerBase = asset('pdfjs/web/viewer.html');
  $viewerUrl  = $pdfAbsUrl
    ? $viewerBase.'?file='.urlencode($pdfAbsUrl).'#disableDownload=true&disablePrint=true&view=FitH'
    : null;
@endphp

<div class="accd-detail">

  {{-- Back bar (konsisten dengan halaman lain) --}}
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

  {{-- HERO: title + category (center), image di bawah (center) --}}
  <div class="card parts-hero border-0 shadow-sm text-center mb-3">
    <div class="card-body">
      <h1 class="parts-hero-title">{{ $motor->name }}</h1>
      <div class="text-muted text-uppercase mb-3" style="letter-spacing:.5px;">
        {{ strtoupper(optional($motor->category)->name ?? '-') }}
      </div>

      <img src="{{ $heroImg }}" alt="{{ $motor->name }}" class="parts-hero-img mx-auto d-block">
    </div>
  </div>

  {{-- Copy di bawah gambar --}}
  <div class="text-center mb-3">
    <h2 class="h5 fw-bold mb-1">Katalog Part</h2>
    <p class="text-muted mb-0">
      @if($viewerUrl)
        Gunakan kontrol di bawah untuk menelusuri halaman katalog.
      @else
        Katalog part belum tersedia untuk model ini.
      @endif
    </p>
  </div>

  {{-- PDF viewer / Empty state --}}
  @if($viewerUrl)
    <div class="card pdf-viewer shadow-sm">
      <iframe
      src="{{ $viewerUrl }}"
      class="pdf-frame"
      title="Katalog Part {{ $motor->name }}"
      scrolling="no"                                   
      style="width:100%;height:80vh;border:0;overflow:hidden;"
    ></iframe>
    </div>
  @else
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center py-5">
        <div class="mb-3">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="10" stroke="#e11d48" stroke-width="1.5"/>
            <path d="M9 9l6 6M15 9l-6 6" stroke="#e11d48" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
        </div>
        <h5 class="mb-1 fw-bold">Belum Ada Katalog</h5>
        <p class="text-muted mb-0">Silakan kembali ke daftar dan pilih model lain.</p>
      </div>
    </div>
  @endif

  <div class="mb-5"></div>
</div>

<script>
(function(){
  function sizePdfViewer(){
    const main  = document.querySelector('main');
    const wrap  = document.querySelector('.accd-detail');
    const head  = document.querySelector('.accd-back');
    const hero  = document.querySelector('.parts-hero');
    const copy  = document.querySelector('.accd-detail .text-center.mb-3');
    const card  = document.querySelector('.card.pdf-viewer');
    const frame = document.querySelector('.pdf-frame');
    if (!main || !wrap || !card || !frame) return;

    // total tinggi yang dipakai elemen di atas viewer
    const usedTop =
      (head?.offsetHeight || 0) +
      (hero?.offsetHeight || 0) +
      (copy?.offsetHeight || 0) +
      24; // padding/margin kecil

    // tinggi yang tersedia di dalam <main>
    const H = main.clientHeight;
    const avail = Math.max(300, H - usedTop);

    // set tinggi untuk card + iframe agar pas
    card.style.height  = avail + 'px';
    frame.style.height = '100%';
  }

  window.addEventListener('load', sizePdfViewer);
  window.addEventListener('resize', sizePdfViewer);
})();
</script>
@endsection