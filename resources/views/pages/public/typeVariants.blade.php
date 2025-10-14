@extends('layouts.appPublic')

@section('content')
<div class="row">
  <div class="col-12">
    {{-- Back bar: sama persis dengan halaman aksesoris --}}
    @php
      $prev = url()->previous();
      $backUrl = (str_contains($prev, request()->getHost()) ? $prev : route('produk'));
    @endphp

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

    <h2 class="section-title">Pilih Varian Motor</h2>
<p class="text-muted fs-5 text-center">
  Tersedia dalam beberapa varian — pilih yang paling sesuai dengan gaya dan kebutuhan Anda!
</p>

    

    <div class="row mt-4 product-grid">
      @foreach($motors as $m)
        <div class="col-md-6 mb-4 product-col">
          <div class="product-card d-flex" style="position:relative;">
            <div class="product-image-left">
              <img src="{{ $m->image_url }}" alt="{{ $m->name }}" class="img-fluid"
                   style="max-height:200px; object-fit:cover;">
            </div>

            <div class="product-info-right">
              <h5 class="product-title">{{ $m->name }}</h5>
              <p class="product-subtitle"><em>Harga Mulai</em></p>

              @if(!empty($m->display_price))
                <p class="product-price">{{ $m->display_price }}</p>
              @else
                <p class="product-price">Hubungi dealer</p>
              @endif

              <div class="d-flex justify-content-end gap-2 mt-2">
                <a href="{{ route('motor.detail', $m->id) }}" class="btn btn-outline-danger">Detail</a>
                <a href="{{ route('compare.menu', ['category' => $m->category_id]) }}" class="btn btn-dark">Bandingkan</a>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

  </div>
</div>
@endsection
