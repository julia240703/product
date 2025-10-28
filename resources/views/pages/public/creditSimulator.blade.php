@extends('layouts.appPublic')

@section('content')
@php
  $prev = url()->previous(); $current = url()->current();
  $backUrl = $prev && $prev !== $current ? $prev : route('produk');
@endphp

<div class="cs-page">
  {{-- Back --}}
  <div class="accd-back mb-3">
    <a href="{{ $backUrl }}" class="accd-back-link">
      <span class="accd-back-ico">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
             stroke="#111" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="11"/><line x1="15" y1="12" x2="8" y2="12"/><polyline points="12 16 8 12 12 8"/>
        </svg>
      </span>
      <span class="accd-back-txt">Kembali</span>
    </a>
    <div class="accd-back-rule"></div>
  </div>

  {{-- HERO --}}
  <section class="cs-hero mb-4">
    <h1 class="cs-title mb-2">Simulasi Kredit</h1>
    <p class="cs-sub mb-0">Gunakan fitur Simulasi Kredit untuk menghitung cicilan motor Honda impianmu.</p>
  </section>

  <div class="row g-4">
    {{-- Langkah 1 --}}
    <div class="col-12 col-lg-6">
      <div class="cs-section">
        <div class="cs-section-head">Langkah 1 - Produk Yang Diminati</div>
        <div class="cs-section-body">
          <label class="form-label fw-600 mb-1">Kategori Motor</label>
          <select id="catSelect" class="form-select cs-select mb-3">
            <option value="">– Pilih Kategori Motor –</option>
            @foreach($categories as $c)
              <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
          </select>

          <label class="form-label fw-600 mb-1">Tipe Motor</label>
          <select id="typeSelect" class="form-select cs-select mb-3" disabled>
            <option value="">– Pilih Tipe Motor –</option>
          </select>

          <label class="form-label fw-600 mb-1">Varian Motor</label>
          <select id="variantSelect" class="form-select cs-select mb-3" disabled>
            <option value="">– Pilih Varian Produk –</option>
          </select>

          <div id="previewBox" class="d-flex align-items-start gap-3 mt-4 d-none">
            <img id="motorImg" src="{{ asset('placeholder.png') }}" alt="Motor" class="cs-motor-img">
            <div class="flex-fill">
              <div class="cs-otr-label">Harga OTR</div>
              <div class="cs-otr-price" id="otrText">Rp 0</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Langkah 2 --}}
    <div class="col-12 col-lg-6">
      <div class="cs-section">
        <div class="cs-section-head">Langkah 2 - Informasi Pembelian</div>
        <div class="cs-section-body">
          <label class="form-label fw-600 mb-1">Pilih DP</label>
          <select id="dpSelect" class="form-select cs-select mb-3" disabled>
            <option value="">– Pilih DP –</option>
          </select>

          <label class="form-label fw-600 mb-1">Jangka Waktu</label>
          <div class="cs-tenor-row mb-3">
            <select id="tenorSelect" class="form-select cs-select cs-tenor-select" disabled>
              <option value="">– Pilih Tenor –</option>
            </select>
            <span class="fw-600 cs-tenor-unit">Bulan</span>
          </div>

          <button id="btnSimulasi" class="btn btn-danger cs-cta w-100" disabled>Simulasikan Kredit</button>

          {{-- CTA Miliki Sekarang -> hanya muncul SETELAH klik Simulasikan dengan hasil valid --}}
          <button id="btnOrder" class="btn btn-dark w-100 mt-2 py-2 cs-cta d-none">
            Miliki Sekarang
          </button>
        </div>
      </div>

      <div class="cs-result mt-4">
        <div class="cs-result-head">Simulasi Cicilan</div>
        <div class="cs-result-body">
          <div class="cs-est-title">Estimasi angsuran per bulan</div>
          <div id="angsuranText" class="cs-est-value">Rp 0</div>
          <ul class="mt-3 mb-0 ps-3 small">
            <li>Harga OTR berlaku untuk wilayah yang ditentukan dealer.</li>
            <li>Simulasi hanya gambaran angsuran. Hubungi tim kami untuk mendapatkan skema terbaik.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Modal QR minimal (hanya barcode) --}}
<div id="qrOrderModal" class="qrmm" hidden>
  <div class="qrmm__backdrop" data-close></div>
  <div class="qrmm__box" role="dialog" aria-modal="true" aria-label="QR Pemesanan">
    <button class="qrmm__close" type="button" aria-label="Tutup" data-close>×</button>
    <h3 id="qrOrderTitle" class="qrmm__title">Scan untuk Buat Pesanan</h3>
    <p class="qrmm__tag">
      Scan QR ini untuk menuju <strong>website resmi Wahana Ritelindo</strong>. 
      Lanjutkan pemesananmu di website resmi kami—cepat &amp; mudah!
    </p>
    <div id="qrOrderCanvas" class="qrmm__canvas" aria-live="polite"></div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js" defer></script>

@push('scripts')
<script>
  // ===== dataset dari controller
  window.CS = @json($dataset);
  const FALLBACK_MIN_DP    = {{ (float)$defaults->min_dp_percent }};
  const FALLBACK_YEAR_RATE = {{ (float)$defaults->interest_year }};
  const PRESELECT_ID       = {{ (int) request('motor_id', 0) }};

  // Helpers
  const rupiah0 = n => new Intl.NumberFormat('id-ID',{maximumFractionDigits:0}).format(Math.round(n||0));
  const rupiah2 = n => new Intl.NumberFormat('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2}).format(Math.round(n||0));
  const el = id => document.getElementById(id);
  const hideOrderCTA = () => el('btnOrder').classList.add('d-none');
  const showOrderCTA = () => el('btnOrder').classList.remove('d-none');

  function setDisabled(ids, v){ ids.forEach(id => el(id).disabled = !!v); }
  function setImg(src){ el('motorImg').src = src || '{{ asset('placeholder.png') }}'; }
  function setOTRDisplay(v){
    const box=el('previewBox'), txt=el('otrText');
    if(!v){ box.classList.add('d-none'); txt.textContent='Rp 0'; }
    else { txt.textContent='Rp '+rupiah0(v); box.classList.remove('d-none'); }
  }
  function resetAngsuran(){ const t = el('angsuranText'); if(t) t.textContent='Rp 0'; }

  // ===== state
  let CURRENT_MOTOR = null; // {id, name, type_id, category_id, otr, thumb, order_url}
  let CREDIT_MATRIX = { tenors:[], rows:[], dp_list:[] };
  let IS_SIMULATED  = false; // <— kunci: CTA muncul hanya setelah ini true

  // Dropdown helpers
  function fillTypes(catId){
    const target = el('typeSelect');
    target.innerHTML = '<option value="">– Pilih Tipe Motor –</option>';
    const list = (window.CS.types||[]).filter(t=> String(t.category_id) === String(catId));
    list.forEach(t=>{
      const o=document.createElement('option'); o.value=t.id; o.textContent=t.name; target.appendChild(o);
    });
    setDisabled(['typeSelect'], !list.length);
    return list;
  }
  function fillVariants(typeId){
    const target = el('variantSelect');
    target.innerHTML = '<option value="">– Pilih Varian Produk –</option>';
    const list = (window.CS.motors||[]).filter(m=> String(m.type_id) === String(typeId));
    list.forEach(m=>{
      const o=document.createElement('option'); o.value=m.id; o.textContent=m.name; o.dataset.thumb=m.thumb; target.appendChild(o);
    });
    setDisabled(['variantSelect'], !list.length);
    return list;
  }

  function scaleDpList(rawList){
    const arr = (rawList||[]).map(Number).filter(n=>n>0);
    if(!arr.length) return { scaled:[], factor:1 };
    const factor = Math.max(...arr) < 1_000_000 ? 1000 : 1;
    return { scaled: arr.map(n=>n*factor), factor };
  }
  function fillDp(list){
    const target = el('dpSelect');
    target.innerHTML = '<option value="">– Pilih DP –</option>';
    const { scaled, factor } = scaleDpList(list);
    target.dataset.dpFactor = String(factor);
    (list||[]).forEach((raw,i)=>{
      const rupiahAmt = scaled[i] || Number(raw) || 0;
      const o=document.createElement('option');
      o.value=String(raw); o.dataset.amountRp = String(rupiahAmt);
      o.textContent = 'Rp ' + rupiah2(rupiahAmt);
      target.appendChild(o);
    });
    setDisabled(['dpSelect'], !(list||[]).length);
  }
  function fillTenor(list){
    const target = el('tenorSelect');
    target.innerHTML = '<option value="">– Pilih Tenor –</option>';
    (list||[]).forEach(n=>{
      const o=document.createElement('option'); o.value=String(n); o.textContent=n; target.appendChild(o);
    });
    setDisabled(['tenorSelect'], !(list||[]).length);
  }

  // Fetch matrix kredit
  async function fetchMatrix(motorId){
    CREDIT_MATRIX = { tenors:[], rows:[], dp_list:[] };
    IS_SIMULATED = false; hideOrderCTA();
    fillDp([]); fillTenor([]); setDisabled(['btnSimulasi'], true);

    if(!motorId){ resetAngsuran(); return; }
    try {
      const url = "{{ route('credit.sim') }}" + "?mode=matrix&motor_id=" + encodeURIComponent(motorId);
      const res = await fetch(url);
      const data = await res.json();
      CREDIT_MATRIX = data || { tenors:[], rows:[], dp_list:[] };
    } catch(e) {
      CREDIT_MATRIX = { tenors:[], rows:[], dp_list:[] };
    }

    fillDp(CREDIT_MATRIX.dp_list||[]);
    fillTenor(CREDIT_MATRIX.tenors||[]);
    const hasMatrix = (CREDIT_MATRIX.dp_list||[]).length && (CREDIT_MATRIX.tenors||[]).length;
    setDisabled(['btnSimulasi'], !CURRENT_MOTOR || (!hasMatrix && !CURRENT_MOTOR.otr));
    if (!hasMatrix) { setDisabled(['dpSelect','tenorSelect'], false); }
  }

  // ====== interactions ======
  (function(){
    const catSel = el('catSelect');
    const typeSel= el('typeSelect');
    const varSel = el('variantSelect');
    const dpSel  = el('dpSelect');
    const tenSel = el('tenorSelect');
    const btnSim = el('btnSimulasi');
    const btnOrd = el('btnOrder');

    function invalidateSimulation(){
      IS_SIMULATED = false;
      hideOrderCTA();
      resetAngsuran();
    }

    catSel.addEventListener('change', ()=>{
      CURRENT_MOTOR = null;
      setOTRDisplay(0); setImg(null);
      fillTypes(catSel.value); fillVariants(null); fillDp([]); fillTenor([]);
      setDisabled(['btnSimulasi'], true);
      invalidateSimulation();
    });

    typeSel.addEventListener('change', ()=>{
      CURRENT_MOTOR = null;
      setOTRDisplay(0); setImg(null);
      fillVariants(typeSel.value); fillDp([]); fillTenor([]);
      setDisabled(['btnSimulasi'], true);
      invalidateSimulation();
    });

    varSel.addEventListener('change', async ()=>{
      const id = varSel.value;
      CURRENT_MOTOR = (window.CS.motors||[]).find(m=> String(m.id) === String(id)) || null;
      setImg(varSel.options[varSel.selectedIndex]?.dataset.thumb || CURRENT_MOTOR?.thumb || null);
      setOTRDisplay(CURRENT_MOTOR?.otr || 0);
      await fetchMatrix(id);
      invalidateSimulation();
    });

    // Perubahan DP/Tenor membatalkan hasil simulasi sebelumnya
    dpSel.addEventListener('change', invalidateSimulation);
    tenSel.addEventListener('change', invalidateSimulation);

    // Hitung simulasi -> baru tampilkan CTA
    btnSim.addEventListener('click', ()=>{
      const dpRaw     = Number(dpSel.value||0);
      const dpRupiah  = Number(dpSel.selectedOptions[0]?.dataset.amountRp || 0);
      const factor    = Number(dpSel.dataset.dpFactor || 1);
      const tenor     = Number(tenSel.value||0);
      const otr       = Number(CURRENT_MOTOR?.otr || 0);

      if (!otr || !tenor || !dpRaw) {
        el('angsuranText').textContent = 'Lengkapi pilihan';
        hideOrderCTA();
        return;
      }

      let angsuran = 0;
      const row  = (CREDIT_MATRIX.rows||[]).find(r => Number(r.dp) === dpRaw);
      const cell = row?.cols?.[String(tenor)] ? Number(row.cols[String(tenor)]) : 0;

      if (cell > 0) {
        angsuran = cell * factor;
      } else {
        const minDp = Math.round(otr * (FALLBACK_MIN_DP/100));
        const dpUse = Math.max(dpRupiah, minDp);
        const pokok = Math.max(0, otr - dpUse);
        const bungaTotal = pokok * (FALLBACK_YEAR_RATE/100) * (tenor/12);
        angsuran = Math.ceil((pokok + bungaTotal) / tenor / 100) * 100;
      }

      el('angsuranText').textContent = 'Rp ' + rupiah2(angsuran);
      setOTRDisplay(otr);

      // === Munculkan CTA hanya jika ada URL dari BO
      if (CURRENT_MOTOR && CURRENT_MOTOR.order_url) {
        IS_SIMULATED = true;
        showOrderCTA();
      } else {
        hideOrderCTA();
      }
    });

    // ==== QR ====
    const modal  = document.getElementById('qrOrderModal');
    const canvas = document.getElementById('qrOrderCanvas');
    function openQR(url){
      if(!url) return;
      canvas.innerHTML = '';
      const render = () => {
        if (window.QRCode) {
          new QRCode(canvas, {text: url, width: 300, height: 300, correctLevel: QRCode.CorrectLevel.M});
        } else { setTimeout(render, 30); }
      };
      render();
      modal.hidden = false;
    }
    function closeQR(){ modal.hidden = true; canvas.innerHTML=''; }

    btnOrd.addEventListener('click', (e)=>{
      e.preventDefault();
      if (!IS_SIMULATED) return;  // safety
      const url = CURRENT_MOTOR?.order_url || '';
      if(!url) return;
      openQR(url);
    });

    modal.addEventListener('click', function(e){
      if (e.target.hasAttribute('data-close')) closeQR();
    });
    document.addEventListener('keydown', function(e){
      if (e.key === 'Escape' && !modal.hidden) closeQR();
    });

    // init
    setDisabled(['typeSelect','variantSelect','dpSelect','tenorSelect','btnSimulasi'], true);
    setOTRDisplay(0); resetAngsuran(); hideOrderCTA();

    // Preselect dari ?motor_id=
    (async function preselect(motorId){
      if(!motorId) return;
      const m = (window.CS.motors||[]).find(x => String(x.id) === String(motorId));
      if(!m) return;
      el('catSelect').value = String(m.category_id);
      fillTypes(m.category_id);
      el('typeSelect').value = String(m.type_id);
      fillVariants(m.type_id);
      el('variantSelect').value = String(m.id);
      CURRENT_MOTOR = m;
      setImg(el('variantSelect').options[el('variantSelect').selectedIndex]?.dataset.thumb || m.thumb || null);
      setOTRDisplay(m.otr || 0);
      await fetchMatrix(m.id);
      hideOrderCTA();
    })(PRESELECT_ID);
  })();
</script>
@endpush
@endsection