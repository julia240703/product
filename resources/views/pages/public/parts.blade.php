@extends('layouts.appPublic')

@section('content')
<div class="page-parts">
  <div class="row">
    <div class="col-12 mb-4">

      {{-- Banner (template "Parts" → fallback "Home") --}}
      @if($banners->isNotEmpty())
        <div id="bannerCarousel"
             class="carousel slide"
             data-bs-interval="false"
             @if($banners->count() <= 1) data-bs-touch="false" data-bs-wrap="false" @endif>

          {{-- Indikator hanya kalau > 1 --}}
          @if($banners->count() > 1)
            <div class="carousel-indicators">
              @foreach($banners as $index => $banner)
                <button type="button" data-bs-target="#bannerCarousel"
                        data-bs-slide-to="{{ $index }}"
                        class="{{ $index == 0 ? 'active' : '' }}"
                        aria-current="{{ $index == 0 ? 'true' : 'false' }}"
                        aria-label="Slide {{ $index+1 }}"></button>
              @endforeach
            </div>
          @endif

          <div class="carousel-inner">
            @foreach($banners as $index => $banner)
              <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                <img src="{{ $banner->image_path }}"
                     class="d-block w-100 parts-banner"
                     alt="{{ $banner->title ?? 'Banner Parts' }}">
              </div>
            @endforeach
          </div>

          {{-- Panah hanya kalau > 1 --}}
          @if($banners->count() > 1)
            <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
              <span class="carousel-control-prev-icon bg-dark rounded-circle p-2"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
              <span class="carousel-control-next-icon bg-dark rounded-circle p-2"></span>
            </button>
          @endif
        </div>
      @endif

      {{-- Heading --}}
      <div class="text-center mt-4">
        <h2 class="section-title">Pilih Katalog Parts</h2>
        <p class="text-muted fs-5">Temukan Sparepart Sesuai dengan Varian Motor</p>
      </div>

      {{-- Tabs kategori (tanpa "General Item") --}}
      <div class="category-container mt-4">
        <div class="d-flex justify-content-center flex-wrap">
          @foreach($tabs as $t)
            <a href="{{ route('parts', ['key' => $t['key']]) }}"
               class="{{ $activeKey === $t['key'] ? 'btn btn-danger' : 'btn custom-category-btn' }} me-2 mb-2">
               {{ $t['name'] }}
            </a>
          @endforeach
        </div>
      </div>

      {{-- Grid motor per kategori --}}
      <div class="row mt-4 product-grid">
        @forelse($motors as $motor)
          <div class="col-md-6 mb-4 product-col">
            <div class="acc-motor-card">
              <div class="acc-motor-img">
                <img
                  src="{{ $motor->thumbnail ? asset('storage/'.$motor->thumbnail) : asset('placeholder.png') }}"
                  alt="{{ $motor->name }}">
              </div>

              <div class="acc-motor-info">
                <h5 class="acc-motor-title">{{ $motor->name }}</h5>

                {{-- Selalu tampilkan tombol Detail, meskipun belum ada PDF --}}
                <a href="{{ route('parts.detail', $motor->id) }}"
                   class="acc-cta acc-motor-cta">
                  <span>Detail</span>
                  <i class="fas fa-chevron-right"></i>
                </a>
              </div>
            </div>
          </div>
        @empty
          <p class="text-center">Motor pada kategori ini belum tersedia.</p>
        @endforelse
      </div>

    </div>
  </div>
</div>
@endsection