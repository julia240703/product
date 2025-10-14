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

      {{-- ===== List Tipe ===== --}}
      <div class="row mt-4 product-grid">
        @if($types->isNotEmpty())

          {{-- Jika kategori = SEMUA, tampilkan dikelompokkan per kategori (urutan mengikuti tombol di atas) --}}
          @if(empty($categoryName))
            @php
              $typesByCat = $types->groupBy('category_id');
            @endphp

            @foreach($categories as $cat)
              @php $items = $typesByCat->get($cat->id, collect()); @endphp
              @if($items->isNotEmpty())
                {{-- Judul kategori --}}
                <div class="col-12 mb-3">
                  <h3 class="category-heading">{{ $cat->name }}</h3>
                  <hr class="category-divider">
                </div>

                @foreach($items as $type)
                  <div class="col-md-6 mb-4 product-col">
                    <div class="product-card d-flex" style="position:relative;">
                      <div class="product-image-left">
                        <img
                          src="{{ $type->image_url }}"
                          alt="{{ $type->name }}"
                          class="img-fluid"
                          style="max-height: 180px; object-fit: contain;">
                      </div>

                      <div class="product-info-right">
                        <h5 class="product-title">{{ $type->name }}</h5>
                        <p class="product-subtitle"><em>Harga Mulai</em></p>

                        {{-- di semua tempat yang sebelumnya pakai display_price_from --}}
@if(!empty($type->display_price_from_fmt))
  <p class="product-price">{{ $type->display_price_from_fmt }}</p>
@else
  <p class="product-price">Hubungi dealer</p>
@endif

                        <div class="d-flex justify-content-end gap-2 mt-2 me-3">
                          {{-- Gate: varian=1 langsung ke detail; >=2 ke halaman pilih varian --}}
                          <a href="{{ route('type.show', $type->id) }}" class="btn btn-outline-danger">Detail</a>
                          <a href="{{ route('compare.menu') }}" class="btn btn-dark">Bandingkan</a>
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
              @endif
            @endforeach

            {{-- tipe tanpa kategori terdaftar (fallback) --}}
            @php
              $knownIds = $categories->pluck('id');
              $others   = $types->whereNotIn('category_id', $knownIds);
            @endphp
            @if($others->isNotEmpty())
              <div class="col-12 mb-3">
                <h3 class="category-heading">Lainnya</h3>
                <hr class="category-divider">
              </div>
              @foreach($others as $type)
                <div class="col-md-6 mb-4 product-col">
                  <div class="product-card d-flex" style="position:relative;">
                    <div class="product-image-left">
                      <img
                        src="{{ $type->image_url }}"
                        alt="{{ $type->name }}"
                        class="img-fluid"
                        style="max-height: 180px; object-fit: contain;">
                    </div>

                    <div class="product-info-right">
                      <h5 class="product-title">{{ $type->name }}</h5>
                      <p class="product-subtitle"><em>Harga Mulai</em></p>

                      {{-- di semua tempat yang sebelumnya pakai display_price_from --}}
@if(!empty($type->display_price_from_fmt))
  <p class="product-price">{{ $type->display_price_from_fmt }}</p>
@else
  <p class="product-price">Hubungi dealer</p>
@endif

                      <div class="d-flex justify-content-end gap-2 mt-2 me-3">
                        <a href="{{ route('type.show', $type->id) }}" class="btn btn-outline-danger">Detail</a>
                        <a href="{{ route('compare.menu') }}" class="btn btn-dark">Bandingkan</a>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            @endif

          @else
            {{-- Jika kategori spesifik dipilih --}}
            @foreach($types as $type)
              <div class="col-md-6 mb-4 product-col">
                <div class="product-card d-flex" style="position:relative;">
                  <div class="product-image-left">
                    <img
                      src="{{ $type->image_url }}"
                      alt="{{ $type->name }}"
                      class="img-fluid"
                      style="max-height: 180px; object-fit: contain;">
                  </div>

                  <div class="product-info-right">
                    <h5 class="product-title">{{ $type->name }}</h5>
                    <p class="product-subtitle"><em>Harga Mulai</em></p>

                    {{-- di semua tempat yang sebelumnya pakai display_price_from --}}
@if(!empty($type->display_price_from_fmt))
  <p class="product-price">{{ $type->display_price_from_fmt }}</p>
@else
  <p class="product-price">Hubungi dealer</p>
@endif

                    <div class="d-flex justify-content-end gap-2 mt-2 me-3">
                      <a href="{{ route('type.show', $type->id) }}" class="btn btn-outline-danger">Detail</a>
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