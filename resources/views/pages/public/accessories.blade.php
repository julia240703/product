@extends('layouts.appPublic')

@section('content')
<div class="page-accessories">
  <div class="row">
    <div class="col-12 mb-4">

      {{-- Banner (sama seperti home) --}}
      @if($banners->isNotEmpty())
        <div id="bannerCarousel" class="carousel slide" data-bs-interval="false">
          <div class="carousel-indicators">
            @foreach($banners as $index => $banner)
              <button type="button" data-bs-target="#bannerCarousel"
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
                     alt="{{ $banner->title ?? 'Banner' }}"
                     style="object-fit: cover; height: 400px; border-radius: 12px;">
              </div>
            @endforeach
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon bg-dark rounded-circle p-2"></span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon bg-dark rounded-circle p-2"></span>
          </button>
        </div>
      @endif

      {{-- Heading --}}
      <div class="text-center mt-4">
        <h2 class="section-title">Pilih Aksesoris</h2>
        <p class="text-muted fs-5">Pilih aksesoris berdasarkan varian motor</p>
      </div>

      {{-- Tabs kategori --}}
      <div class="category-container mt-4">
        <div class="d-flex justify-content-center flex-wrap">
          @foreach($tabs as $t)
            <a href="{{ route('accessories', ['key' => $t['key']]) }}"
               class="{{ $activeKey === $t['key'] ? 'btn btn-danger' : 'btn custom-category-btn' }} me-2 mb-2">
               {{ $t['name'] }}
            </a>
          @endforeach
        </div>
      </div>

      {{-- Konten tiap tab --}}
      @if ($activeKey === 'general')
        {{-- === GENERAL ITEM — kartu disamakan persis dengan APPAREL === --}}
        <div class="row mt-4 product-grid">
          @forelse($generalAccessories as $acc)
            <div class="col-md-6 mb-4 product-col">
              <div class="acc-motor-card">
                <div class="acc-motor-img">
                  <img src="{{ $acc->image_url }}" alt="{{ $acc->name }}" loading="lazy">
                </div>

                <div class="acc-motor-info">
                  <h5 class="acc-motor-title">{{ $acc->name }}</h5>

                  <div class="acc-price mb-2">
                    <span>Harga</span>
                    <strong>Rp {{ number_format($acc->display_price, 0, ',', '.') }}</strong>
                  </div>

                  <a href="{{ route('accessories.general.detail', $acc->id) }}"
                     class="apr-cta acc-cta acc-motor-cta">
                    <span>Detail</span><i class="fas fa-chevron-right"></i>
                  </a>
                </div>
              </div>
            </div>
          @empty
            <p class="text-center mt-3">Belum ada aksesori umum.</p>
          @endforelse
        </div>
      @else
        {{-- Grid motor per kategori (kartu sesuai mockup) --}}
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

                  <a href="{{ route('accessories.motor', $motor->id) }}#accessories" class="acc-cta acc-motor-cta">
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
      @endif

    </div>
  </div>
</div>
@endsection