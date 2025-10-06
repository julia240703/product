@extends('layouts.appPublic')

@section('content')
  <div class="row">
    <div class="col-12 mb-4">
      @if($banners->isNotEmpty())
        {{-- ===== Banner ===== --}}
        <div id="bannerCarousel" class="carousel slide" data-bs-interval="false">
          <div class="carousel-indicators">
            @foreach($banners as $index => $banner)
              <button type="button"
                      data-bs-target="#bannerCarousel"
                      data-bs-slide-to="{{ $index }}"
                      class="{{ $index == 0 ? 'active' : '' }}"
                      aria-current="{{ $index == 0 ? 'true' : 'false' }}"
                      aria-label="Slide {{ $index + 1 }}">
              </button>
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

        <div class="text-center mt-3">
          <h2>Yuk, Pilih Motor Honda Favorit Versimu!</h2>
        </div>

        {{-- ===== Kategori ===== --}}
<div class="category-container mt-4">
  <div class="d-flex justify-content-center flex-wrap">
    <a href="{{ route('home') }}"
       class="{{ empty($categoryName) ? 'btn btn-danger' : 'btn custom-category-btn' }} me-2 mb-2">
       SEMUA
    </a>
    @foreach($categories as $category)
      <a href="{{ route('home.category', $category->name) }}"
         class="{{ $categoryName === $category->name ? 'btn btn-danger' : 'btn custom-category-btn' }} me-2 mb-2">
         {{ $category->name }}
      </a>
    @endforeach
  </div>
</div>

{{-- ===== Produk (sudah ter-limit di controller) ===== --}}
<div class="row mt-4 product-grid">
  @forelse($motors as $motor)
    <div class="col-md-6 mb-4 product-col">
      <div class="product-card d-flex" style="position:relative;">
        @if($motor->is_new)
          <div style="position:absolute; right:12px; top:12px; background:#dc2626; color:#fff; padding:4px 10px; border-radius:9999px; font-size:12px; font-weight:700; z-index:5; pointer-events:none;">
            New !
          </div>
        @endif

        <div class="product-image-left">
          <img
            src="{{ method_exists($motor,'getThumbUrlAttribute') ? $motor->thumb_url : asset('storage/' . ($motor->thumbnail ?? '')) }}"
            alt="{{ $motor->name }}" class="img-fluid"
            style="max-height: 180px; object-fit: contain;">
        </div>

        <div class="product-info-right">
          <h5 class="product-title">{{ $motor->name }}</h5>
          <p class="product-subtitle"><em>Harga Mulai</em></p>
          <p class="product-price">Rp {{ number_format($motor->price ?? 0, 0, ',', '.') }}</p>
          <div class="d-flex justify-content-end gap-2 mt-2 me-3">
            <a href="{{ route('motor.detail', $motor->id) }}" class="btn btn-outline-danger">Detail</a>
            {{-- langsung ke menu bandingkan --}}
            <a href="{{ route('compare.menu') }}" class="btn btn-dark">Bandingkan</a>
          </div>
        </div>
      </div>
    </div>
  @empty
    <p class="text-center">Tidak ada produk yang tersedia.</p>
  @endforelse
</div>

{{-- ===== Lihat Selengkapnya ===== --}}
<div class="see-more-wrapper text-center">
  <a href="{{ $seeMoreUrl }}" class="btn-see-more">Lihat Selengkapnya</a>
</div>
      @else
        <p class="text-center">Tidak ada banner yang tersedia.</p>
      @endif
    </div>
  </div>
@endsection