@extends('layouts.appPublic')

@section('content')
  <div class="row">
    <div class="col-12 mb-4">
      <div class="text-center mt-3">
        <h2>Yuk, Pilih Motor Honda Favorit Versimu!</h2>
      </div>

      {{-- ===== Kategori Motor ===== --}}
      <div class="category-container mt-4">
        <div class="d-flex justify-content-center flex-wrap">
          <a href="{{ route('produk') }}"
             class="{{ empty($categoryName) ? 'btn btn-danger' : 'btn custom-category-btn' }} me-2 mb-2">
             SEMUA
          </a>
          @foreach($categories as $category)
            <a href="{{ route('produk.category', $category->name) }}"
               class="{{ $categoryName === $category->name ? 'btn btn-danger' : 'btn custom-category-btn' }} me-2 mb-2">
               {{ $category->name }}
            </a>
          @endforeach
        </div>
      </div>

      {{-- ===== List Produk ===== --}}
      <div class="row mt-4 product-grid">
        @if($motors->isNotEmpty())
          {{-- Jika kategori = SEMUA, tampilkan per kategori mengikuti urutan $categories (tombol di atas) --}}
          @if(empty($categoryName))
            @php
              $motorsByCat = $motors->groupBy('category_id');
            @endphp

            @foreach($categories as $cat)
              @php $items = $motorsByCat->get($cat->id, collect()); @endphp
              @if($items->isNotEmpty())
                {{-- Judul kategori --}}
                <div class="col-12 mb-3">
                  <h3 class="category-heading">{{ $cat->name }}</h3>
                  <hr class="category-divider">
                </div>

                @foreach($items as $motor)
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
                          alt="{{ $motor->name }}"
                          class="img-fluid"
                          style="max-height: 180px; object-fit: contain;">
                      </div>

                      <div class="product-info-right">
                        <h5 class="product-title">{{ $motor->name }}</h5>
                        <p class="product-subtitle"><em>Harga Mulai</em></p>
                        <p class="product-price">Rp {{ number_format($motor->price ?? 0, 0, ',', '.') }}</p>
                        <div class="d-flex justify-content-end gap-2 mt-2 me-3">
                          <a href="{{ route('motor.detail', $motor->id) }}" class="btn btn-outline-danger">Detail</a>
                          <a href="{{ route('compare.menu') }}" class="btn btn-dark">Bandingkan</a>
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
              @endif
            @endforeach

            {{-- Opsi: produk tanpa kategori / kategori tak terdaftar → taruh paling akhir --}}
            @php
              $knownIds = $categories->pluck('id');
              $others   = $motors->whereNotIn('category_id', $knownIds);
            @endphp
            @if($others->isNotEmpty())
              <div class="col-12 mb-3">
                <h3 class="category-heading">Lainnya</h3>
                <hr class="category-divider">
              </div>
              @foreach($others as $motor)
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
                        alt="{{ $motor->name }}"
                        class="img-fluid"
                        style="max-height: 180px; object-fit: contain;">
                    </div>

                    <div class="product-info-right">
                      <h5 class="product-title">{{ $motor->name }}</h5>
                      <p class="product-subtitle"><em>Harga Mulai</em></p>
                      <p class="product-price">Rp {{ number_format($motor->price ?? 0, 0, ',', '.') }}</p>
                      <div class="d-flex justify-content-end gap-2 mt-2 me-3">
                        <a href="{{ route('motor.detail', $motor->id) }}" class="btn btn-outline-danger">Detail</a>
                        <a href="{{ route('compare.menu') }}" class="btn btn-dark">Bandingkan</a>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            @endif

          @else
            {{-- Jika kategori spesifik dipilih --}}
            @foreach($motors as $motor)
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
                      alt="{{ $motor->name }}"
                      class="img-fluid"
                      style="max-height: 180px; object-fit: contain;">
                  </div>

                  <div class="product-info-right">
                    <h5 class="product-title">{{ $motor->name }}</h5>
                    <p class="product-subtitle"><em>Harga Mulai</em></p>
                    <p class="product-price">Rp {{ number_format($motor->price ?? 0, 0, ',', '.') }}</p>
                    <div class="d-flex justify-content-end gap-2 mt-2 me-3">
                      <a href="{{ route('motor.detail', $motor->id) }}" class="btn btn-outline-danger">Detail</a>
                      <a href="{{ route('compare.menu') }}" class="btn btn-dark">Bandingkan</a>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          @endif
        @else
          <p class="text-center">Tidak ada produk yang tersedia.</p>
        @endif
      </div>
    </div>
  </div>
@endsection