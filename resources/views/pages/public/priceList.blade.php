@extends('layouts.appPublic')

@section('content')
  {{-- Back bar --}}
  @php
    $prev     = url()->previous();
    $current  = url()->current();
    $backUrl  = $prev && $prev !== $current ? $prev : route('produk');
  @endphp

  <div class="accd-back mb-3">
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
  <section class="pl-hero mb-4">
    <h1 class="pl-title mb-2">Price List</h1>
    <p class="pl-sub mb-0">
      Cari tahu harga terkini semua model motor Honda lewat fitur price list yang praktis ini.
    </p>
  </section>

  {{-- Kategori (tanpa "Semua") --}}
  @if($categories->isNotEmpty())
    <div class="category-container mt-4 mb-4">
      <div class="d-flex justify-content-center flex-wrap">
        @foreach($categories as $cat)
          <a href="{{ route('price.list', ['category' => $cat->id]) }}"
             class="{{ $activeCatId === $cat->id ? 'btn btn-danger' : 'btn custom-category-btn' }} me-2 mb-2">
             {{ strtoupper($cat->name) }}
          </a>
        @endforeach
      </div>
    </div>
  @endif

  {{-- GRID daftar motor (2 kolom besar) --}}
  @if($groups->isEmpty())
    <p class="text-center text-muted">Belum ada data price list untuk kategori ini.</p>
  @else
    <div class="row g-4">
      @foreach($groups as $g)
        <div class="col-12 col-lg-6">
          <article class="pl-card">
            <header class="mb-2">
              <h5 class="mb-1 fw-bold">{{ $g->motor_name }}</h5>
              <div class="d-flex align-items-center gap-2 text-muted">
                <span class="fw-semibold">Lihat Produk :</span>
                @if($g->motor_id)
                  <a class="pl-chip" href="{{ route('motor.detail', $g->motor_id) }}">
                    {{ $g->motor_name }}
                  </a>
                @else
                  <span class="pl-chip disabled">{{ $g->motor_name }}</span>
                @endif
              </div>
            </header>

            {{-- Tabel tipe & harga --}}
            <div class="pl-table">
              <div class="pl-table-head">
                <div class="pl-th">Tipe</div>
                <div class="pl-th pl-price">Harga</div>
              </div>

              <div class="pl-table-body">
                @foreach($g->types as $idx => $t)
                  <div class="pl-tr {{ $idx % 2 === 1 ? 'alt' : '' }}">
                    <div class="pl-td">{{ $t->type }}</div>
                    <div class="pl-td pl-price">Rp. {{ number_format($t->price, 0, ',', '.') }}</div>
                  </div>
                @endforeach
              </div>
            </div>
          </article>
        </div>
      @endforeach
    </div>
  @endif
@endsection