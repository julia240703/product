@extends('layouts.appPublic')

@section('content')
  @php
    use Illuminate\Support\Str;

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

  <section class="cmp-hero mb-4">
    <h2 class="cmp-ttl">Perbandingan Motor</h2>
    <p class="cmp-sub">Anda perlu bantuan untuk memutuskan? Sekarang Anda dapat
      membandingkan motor favorit Anda satu sama lain.</p>
  </section>

  @php
    $selectedOnly = ($motors->count() ?? 0);
    $gridMod      = in_array($selectedOnly, [2,4]) ? 'is-2' : 'is-3';

    // Slot kosong pertama → untuk FAB “+”
    $nextSlot = null;
    if ($selectedOnly < 6) {
      for ($i = 0; $i < 6; $i++) {
        $mid = $slots[$i] ?? null;
        if (!$mid || !isset($motorMap[$mid])) { $nextSlot = $i; break; }
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
            <div class="cmp-fig-price">{{ $m->display_price ?? '—' }}</div>
            <a class="cmp-fig-detail" href="{{ $m->detail_url }}">Produk Detail</a>
          </article>
        </div>
      @endif
    @endfor
  </div>

  {{-- Tabel spesifikasi --}}
  @foreach($categories as $cat)
    @php
      $slug  = Str::slug($cat) ?: 'kategori-'.$loop->index;
      $bodyId = 'spec-body-'.$slug;
    @endphp
    <section class="cmp-spec-block">
      <div class="cmp-spec-head is-open" data-acc role="button" tabindex="0"
           aria-controls="{{ $bodyId }}" aria-expanded="true">
        <h4 class="m-0">{{ $cat }}</h4>
        <span class="ico" aria-hidden="true">–</span>
      </div>

      <div id="{{ $bodyId }}" class="cmp-spec-body open">
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
                      <span class="cmp-spec-price">{{ $m->display_price ?? '—' }}</span>

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

  {{-- FAB “+” tengah kanan (muncul jika masih ada slot kosong) --}}
  @if(!is_null($nextSlot))
    <a href="{{ route('compare.pick', ['slot' => $nextSlot]) }}"
       class="cmp-fab cmp-fab--mid"
       aria-label="Tambah model">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
           stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5"  x2="12" y2="19"/>
        <line x1="5"  y1="12" x2="19" y2="12"/>
      </svg>
    </a>
  @endif

@push('styles')
<style>
  .cmp-fab{
    position: fixed;
    right: 24px;
    width: 56px; height: 56px;
    border-radius: 9999px;
    background: #111; color: #fff;
    display: inline-flex; align-items: center; justify-content: center;
    box-shadow: 0 10px 24px rgba(0,0,0,.18);
    z-index: 1030;
    text-decoration: none;
    transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
  }
  .cmp-fab--mid{ top: 50%; transform: translateY(-50%); }
  .cmp-fab:hover{ background:#e11d48; transform: translateY(-50%) scale(1.02); }
  .cmp-fab:active{ transform: translateY(-50%) scale(.98); }
  @media (max-width: 768px){
    .cmp-fab{ right: 16px; width: 52px; height: 52px; }
    .cmp-fab--mid{ top: auto; bottom: 20px; transform: none; }
  }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  document.querySelectorAll('[data-acc]').forEach(function(head){
    const body = document.getElementById(head.getAttribute('aria-controls'));
    const icon = head.querySelector('.ico');

    function setState(open){
      head.classList.toggle('is-open', open);
      head.setAttribute('aria-expanded', open ? 'true' : 'false');
      if (open) { body.classList.add('open'); body.removeAttribute('hidden'); }
      else      { body.classList.remove('open'); body.setAttribute('hidden',''); }
      if (icon) icon.textContent = open ? '–' : '+';
    }

    const initiallyOpen = !body.hasAttribute('hidden');
    setState(initiallyOpen);

    head.addEventListener('click', () => setState(body.hasAttribute('hidden')));
    head.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); setState(body.hasAttribute('hidden')); }
    });
  });
});
</script>
@endpush
@endsection
