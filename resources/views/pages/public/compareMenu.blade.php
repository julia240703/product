@extends('layouts.appPublic')

@section('content')
<div class="cmp-wrap cmp-wrap--fluid">

  {{-- Header abu-abu --}}
  <section class="cmp-hero">
    <h1 class="cmp-ttl">Perbandingan Motor</h1>
    <p class="cmp-sub">
      Anda perlu bantuan untuk memutuskan? Sekarang Anda dapat
      membandingkan motor favorit Anda satu sama lain.
    </p>
  </section>

  {{-- 6 slot pilih model --}}
  <div class="cmp-grid">
    @for ($i = 0; $i < 6; $i++)
      <a class="cmp-slot" href="{{ route('compare.pick', ['slot' => $i]) }}">
        <span class="cmp-plus">+</span>
        Pilih Model
      </a>

      @if($i === 2)
        <div class="cmp-row-break"></div>
      @endif
    @endfor
  </div>

  {{-- Notice --}}
  <div class="d-flex justify-content-center">
    <div class="cmp-note">
      <span class="ico"><i class="fas fa-exclamation-triangle"></i></span>
      Anda dapat membandingkan 6 model sekaligus.
    </div>
  </div>

</div>
@endsection