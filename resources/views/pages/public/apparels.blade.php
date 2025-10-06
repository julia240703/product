@extends('layouts.appPublic')

@section('content')
<div class="page-apparels">
  <div class="row">
    <div class="col-12 mb-4">

      {{-- Banner --}}
      @if(isset($banners) && $banners->isNotEmpty())
        <div id="bannerCarousel" class="carousel slide" data-bs-interval="false">
          <div class="carousel-indicators">
            @foreach($banners as $index => $banner)
              <button type="button"
                      data-bs-target="#bannerCarousel"
                      data-bs-slide-to="{{ $index }}"
                      class="{{ $index == 0 ? 'active' : '' }}"
                      aria-current="{{ $index == 0 ? 'true' : 'false' }}"
                      aria-label="Slide {{ $index+1 }}"></button>
            @endforeach
          </div>
          <div class="carousel-inner">
            @foreach($banners as $index => $banner)
              <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                <img src="{{ $banner->image_path }}"
                     class="d-block w-100"
                     alt="{{ $banner->title ?? 'Banner' }}">
              </div>
            @endforeach
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
          </button>
        </div>
      @endif

      {{-- Heading --}}
      <div class="text-center mt-4">
        <h2 class="section-title">Pilih Apparel</h2>
        <p class="text-muted fs-5">Tampil maksimal dengan apparel Honda!</p>
      </div>

      {{-- Tabs kategori --}}
      <div class="category-container mt-4">
        <div class="d-flex justify-content-center flex-wrap">
          @foreach($tabs as $t)
            <a href="{{ route('apparels', ['key' => $t['key']]) }}"
               class="{{ ($activeKey ?? '') === $t['key'] ? 'btn btn-danger' : 'btn custom-category-btn' }} me-2 mb-2">
               {{ $t['name'] }}
            </a>
          @endforeach
        </div>
      </div>

      {{-- Grid 2 kolom --}}
      <div class="row mt-4 product-grid">
        @forelse($apparels as $ap)
          @php
            $name  = $ap->display_name ?? ($ap->name_apparel ?? '-');
            $img   = $ap->image_url ?? asset('placeholder.png');
            $price = $ap->display_price ?? ($ap->price ?? 0);
          @endphp

          <div class="col-md-6 mb-4 product-col">
            <div class="acc-motor-card" style="position:relative;">
              @if(!empty($ap->is_new))
                <div style="position:absolute; right:12px; top:12px; background:#dc2626; color:#fff; padding:4px 10px; border-radius:9999px; font-size:12px; font-weight:700; z-index:5; pointer-events:none;">
                  New !
                </div>
              @endif

              <div class="acc-motor-img">
                <img src="{{ $img }}" alt="{{ $name }}" loading="lazy">
              </div>

              <div class="acc-motor-info">
                <h5 class="acc-motor-title">{{ $name }}</h5>

                <div class="acc-price mb-2">
                  <span>Harga</span>
                  <strong>Rp {{ number_format($price, 0, ',', '.') }}</strong>
                </div>

                <a href="{{ route('apparel.detail', $ap->id) }}" class="apr-cta acc-cta acc-motor-cta">
                  <span>Detail</span><i class="fas fa-chevron-right"></i>
                </a>
              </div>
            </div>
          </div>
        @empty
          <p class="text-center">Belum ada apparel pada kategori ini.</p>
        @endforelse
      </div>

    </div>
  </div>
</div>
@endsection