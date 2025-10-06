@extends('layouts.appPublic')

@section('content')
  {{-- Back bar --}}
  @php
    $prev    = url()->previous();
    $current = url()->current();
    $backUrl = $prev && $prev !== $current ? $prev : route('compare.pick');
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

  {{-- Hero --}}
  <section class="cmp-hero mb-4">
    <h2 class="cmp-ttl">Perbandingan Motor</h2>
    <p class="cmp-sub">Anda perlu bantuan untuk memutuskan? Sekarang Anda dapat
      membandingkan motor favorit Anda satu sama lain.</p>
  </section>

  {{-- Grid ringkas motor terpilih + 1 tombol pilih model (selalu di urutan selanjutnya) --}}
  @php
    $selectedOnly = ($motors->count() ?? 0);
    $gridMod      = in_array($selectedOnly, [2,4]) ? 'is-2' : 'is-3';

    // Cari slot kosong pertama dari kiri (0..5)
    $buttonIndex = null;
    if ($selectedOnly < 6) {
      for ($j = 0; $j < 6; $j++) {
        $mid = $slots[$j] ?? null;
        if (!$mid || !isset($motorMap[$mid])) { $buttonIndex = $j; break; }
      }
    }
  @endphp

  <div class="cmp-fig-grid cmp-fig-grid--{{ $gridMod }} mb-5">
    @for ($i = 0; $i < 6; $i++)
      @php $mid = $slots[$i] ?? null; @endphp

      @if($mid && isset($motorMap[$mid]))
        @php $m = $motorMap[$mid]; @endphp
        <div class="cmp-fig-item">
          <article class="cmp-fig-card">
            <form action="{{ route('compare.remove') }}" method="POST" class="cmp-fig-close">
              @csrf @method('DELETE')
              <input type="hidden" name="motor_id" value="{{ $m->id }}">
              <button type="submit" aria-label="Hapus">×</button>
            </form>

            <div class="cmp-fig-img"><img src="{{ $m->image_url }}" alt="{{ $m->name }}"></div>
            <h3 class="cmp-fig-name">{{ $m->name }}</h3>
            <div class="cmp-fig-price">Rp {{ number_format($m->price ?? 0,0,',','.') }}</div>
            <a class="cmp-fig-detail" href="{{ $m->detail_url }}">Produk Detail</a>
          </article>
        </div>

      @elseif(!is_null($buttonIndex) && $i === $buttonIndex)
        {{-- HANYA 1 tombol "Pilih Model" di slot kosong pertama --}}
        <div class="cmp-fig-item">
          <article class="cmp-fig-card" style="display:flex;align-items:center;justify-content:center;min-height:360px;">
            <a class="cmp-slot" href="{{ route('compare.pick', ['slot' => $i]) }}">
              <span class="cmp-plus">+</span> Pilih Model
            </a>
          </article>
        </div>
      @endif
    @endfor
  </div>

  {{-- Tabel spesifikasi (accordion) --}}
  @foreach($categories as $cat)
    <section class="cmp-spec-block">
      <div class="cmp-spec-head" data-acc role="button" tabindex="0"
           aria-controls="spec-body-{{ Str::slug($cat) }}" aria-expanded="false">
        <h4 class="m-0">{{ $cat }}</h4>
        <span class="ico" aria-hidden="true">+</span>
      </div>

      <div id="spec-body-{{ Str::slug($cat) }}" class="cmp-spec-body" hidden>
        <div class="cmp-spec-table-wrap">
          @php
            $attrW = 25;
            $n     = max(1, $motors->count());
            $eachW = (100 - $attrW) / $n;
          @endphp

          <table class="cmp-spec-table">
            <colgroup>
              <col style="width: {{ $attrW }}%">
              @foreach($motors as $m)
                <col style="width: {{ $eachW }}%">
              @endforeach
            </colgroup>

            <thead>
              <tr>
                <th>Atribut</th>
                @foreach($motors as $m)
                  <th>
                    <div class="cmp-hcell cmp-hcell--left">
                      <span class="cmp-spec-name" title="{{ $m->name }}">{{ $m->name }}</span>
                      <span class="cmp-spec-price">Rp {{ number_format($m->price ?? 0, 0, ',', '.') }}</span>

                      @php $detailUrl = $m->detail_url ?? null; @endphp
                      @if($detailUrl)
                        <a href="{{ $detailUrl }}" class="cmp-spec-cta" aria-label="Detail {{ $m->name }}">
                          Detail
                        </a>
                      @else
                        <span class="cmp-spec-cta is-disabled" aria-disabled="true">Detail</span>
                      @endif
                    </div>
                  </th>
                @endforeach
              </tr>
            </thead>

            <tbody>
              @foreach(($specs[$cat] ?? collect()) as $row)
                <tr>
                  <td class="attr">{{ $row['atribut'] }}</td>
                  @foreach($row['cells'] as $val)
                    <td>{{ $val ?: '—' }}</td>
                  @endforeach
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </section>
  @endforeach

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  document.querySelectorAll('[data-acc]').forEach(function(head){
    const body = document.getElementById(head.getAttribute('aria-controls'));
    const icon = head.querySelector('.ico');

    function toggle(open){
      head.classList.toggle('is-open', open);
      head.setAttribute('aria-expanded', open ? 'true' : 'false');
      if (open) { body.removeAttribute('hidden'); body.classList.add('open'); }
      else      { body.setAttribute('hidden','');   body.classList.remove('open'); }
      if (icon) icon.textContent = open ? '–' : '+';
    }

    head.addEventListener('click', () => toggle(body.hasAttribute('hidden')));
    head.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggle(body.hasAttribute('hidden')); }
    });
  });
});
</script>
@endpush
@endsection