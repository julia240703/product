@extends('layouts.appPublic')

@section('content')
<div class="container py-3">

  {{-- Filter kategori optional --}}
  @if(isset($categories) && $categories->count())
    <div class="d-flex flex-wrap justify-content-center mb-3 category-container">
      @foreach($categories as $cat)
        @php $active = ($categoryName === $cat->name); @endphp
        <a href="{{ route('produk', $cat->name) }}"
           class="btn custom-category-btn {{ $active ? 'active' : '' }}">
          {{ $cat->name }}
        </a>
      @endforeach
      @if($categoryName)
        <a href="{{ route('produk') }}" class="btn btn-outline-danger">Semua</a>
      @endif
    </div>
  @endif

  {{-- GRID/LIST tipe --}}
  <div class="row g-3">
    @forelse($types as $t)
      <div class="col-12">
        <div class="product-card">
          <div class="product-image-left">
            <img src="{{ $t->image_url }}" alt="{{ $t->name }}">
          </div>

          <div class="product-info-right">
            <h3 class="product-title">{{ $t->name }}</h3>
            <p class="product-subtitle">Harga Mulai</p>
            <p class="product-price">Rp {{ number_format($t->price_from,0,',','.') }}</p>

            <div class="buttons">
              <a href="{{ route('type.show', $t->id) }}" class="btn btn-danger">Detail</a>
              <a href="{{ route('type.show', $t->id) }}" class="btn btn-dark">Bandingkan</a>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12 text-center py-5">Belum ada tipe.</div>
    @endforelse
  </div>
</div>
@endsection