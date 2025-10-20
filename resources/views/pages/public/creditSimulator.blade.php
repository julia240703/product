@extends('layouts.appPublic')

@section('content')
@php
  $prev = url()->previous(); $current = url()->current();
  $backUrl = $prev && $prev !== $current ? $prev : route('produk');
@endphp

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

<script>
  // ===== dataset dari controller
  window.CS = @json($dataset);
  const FALLBACK_MIN_DP    = {{ (float)$defaults->min_dp_percent }};
  const FALLBACK_YEAR_RATE = {{ (float)$defaults->interest_year }};
  // kalau datang dari halaman detail: ?motor_id=xx
  const PRESELECT_ID = {{ (int) request('motor_id', 0) }};

  // ===== helpers format
  const rupiah0 = n => new Intl.NumberFormat('id-ID',{minimumFractionDigits:0,maximumFractionDigits:0}).format(Math.round(n||0));
  const rupiah2 = n => new Intl.NumberFormat('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2}).format(Math.round(n||0));

  function setDisabled(ids, v){ ids.forEach(id => document.getElementById(id).disabled = !!v); }
  function setImg(src){ document.getElementById('motorImg').src = src || '{{ asset('placeholder.png') }}'; }
  function setOTRDisplay(v){
    const box=document.getElementById('previewBox'), txt=document.getElementById('otrText');
    if(!v){ box.classList.add('d-none'); txt.textContent='Rp 0'; }
    else { txt.textContent='Rp '+rupiah0(v); box.classList.remove('d-none'); }
  }
  // reset angka simulasi -> Rp 0
  function resetAngsuran(){ const el=document.getElementById('angsuranText'); if(el) el.textContent='Rp 0'; }

  // ===== state
  let CURRENT_MOTOR = null; // object varian
  let CREDIT_MATRIX = { tenors:[], rows:[], dp_list:[] };

  // ===== dropdown helpers
  function fillTypes(catId){
    const el = document.getElementById('typeSelect');
    el.innerHTML = '<option value="">– Pilih Tipe Motor –</option>';
    const list = (window.CS.types||[]).filter(t=> String(t.category_id) === String(catId));
    list.forEach(t=>{
      const o=document.createElement('option');
      o.value=t.id; o.textContent=t.name;
      el.appendChild(o);
    });
    setDisabled(['typeSelect'], !list.length);
    return list;
  }

  function fillVariants(typeId){
    const el = document.getElementById('variantSelect');
    el.innerHTML = '<option value="">– Pilih Varian Produk –</option>';
    const list = (window.CS.motors||[]).filter(m=> String(m.type_id) === String(typeId));
    list.forEach(m=>{
      const o=document.createElement('option');
      o.value=m.id; o.textContent=m.name; o.dataset.thumb=m.thumb;
      el.appendChild(o);
    });
    setDisabled(['variantSelect'], !list.length);
    return list;
  }

  // Deteksi apakah DP dari BO masih “ribuan” (6500) → tampilkan sebagai 6.500.000,00.
  function scaleDpList(rawList){
    const arr = (rawList||[]).map(Number).filter(n=>n>0);
    if(!arr.length) return { scaled:[], factor:1 };
    const factor = Math.max(...arr) < 1_000_000 ? 1000 : 1;
    return { scaled: arr.map(n=>n*factor), factor };
  }

  function fillDp(list){
    const el = document.getElementById('dpSelect');
    el.innerHTML = '<option value="">– Pilih DP –</option>';

    const { scaled, factor } = scaleDpList(list);
    el.dataset.dpFactor = String(factor);

    (list||[]).forEach((raw, i)=>{
      const rupiahAmt = scaled[i] || Number(raw) || 0;
      const o = document.createElement('option');
      o.value = String(raw);
      o.dataset.amountRp = String(rupiahAmt);
      o.textContent = 'Rp ' + rupiah2(rupiahAmt);
      el.appendChild(o);
    });

    setDisabled(['dpSelect'], !(list||[]).length);
  }

  function fillTenor(list){
    const el = document.getElementById('tenorSelect');
    el.innerHTML = '<option value="">– Pilih Tenor –</option>';
    (list||[]).forEach(n=>{
      const o=document.createElement('option');
      o.value=String(n); o.textContent=n;
      el.appendChild(o);
    });
    setDisabled(['tenorSelect'], !(list||[]).length);
  }

  // ===== fetch matrix dari BE
  async function fetchMatrix(motorId){
    CREDIT_MATRIX = { tenors:[], rows:[], dp_list:[] };
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

  // ===== interactions
  (function(){
    const catSel = document.getElementById('catSelect');
    const typeSel = document.getElementById('typeSelect');
    const varSel  = document.getElementById('variantSelect');
    const dpSel   = document.getElementById('dpSelect');
    const tenSel  = document.getElementById('tenorSelect');
    const btn     = document.getElementById('btnSimulasi');

    catSel.addEventListener('change', ()=>{
      CURRENT_MOTOR = null;
      CREDIT_MATRIX = { tenors:[], rows:[], dp_list:[] };

      setOTRDisplay(0); setImg(null);
      fillTypes(catSel.value);
      fillVariants(null);
      fillDp([]); fillTenor([]);
      setDisabled(['btnSimulasi'], true);

      resetAngsuran(); // langsung balik ke Rp 0
    });

    typeSel.addEventListener('change', ()=>{
      CURRENT_MOTOR = null;
      CREDIT_MATRIX = { tenors:[], rows:[], dp_list:[] };

      setOTRDisplay(0); setImg(null);
      fillVariants(typeSel.value);
      fillDp([]); fillTenor([]);
      setDisabled(['btnSimulasi'], true);

      resetAngsuran(); // konsisten reset
    });

    varSel.addEventListener('change', async ()=>{
      const id = varSel.value;
      CURRENT_MOTOR = (window.CS.motors||[]).find(m=> String(m.id) === String(id)) || null;
      setImg(varSel.options[varSel.selectedIndex]?.dataset.thumb || CURRENT_MOTOR?.thumb || null);
      setOTRDisplay(CURRENT_MOTOR?.otr || 0);
      await fetchMatrix(id);
    });

    btn.addEventListener('click', ()=>{
      const dpRaw     = Number(dpSel.value||0);
      const dpRupiah  = Number(dpSel.selectedOptions[0]?.dataset.amountRp || 0);
      const factor    = Number(dpSel.dataset.dpFactor || 1);
      const tenor     = Number(tenSel.value||0);
      const otr       = Number(CURRENT_MOTOR?.otr || 0);

      if (!otr || !tenor || !dpRaw) {
        document.getElementById('angsuranText').textContent = 'Lengkapi pilihan';
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

      document.getElementById('angsuranText').textContent = 'Rp ' + rupiah2(angsuran);
      setOTRDisplay(otr);
    });

    // initial state
    setDisabled(['typeSelect','variantSelect','dpSelect','tenorSelect','btnSimulasi'], true);
    setOTRDisplay(0);
    resetAngsuran();

    // ==== PRESELECT dari ?motor_id= ====
    (async function preselectMotor(motorId){
      if(!motorId) return;
      const m = (window.CS.motors||[]).find(x => String(x.id) === String(motorId));
      if(!m) return;

      catSel.value = String(m.category_id);
      fillTypes(m.category_id);

      typeSel.value = String(m.type_id);
      fillVariants(m.type_id);

      varSel.value = String(m.id);
      CURRENT_MOTOR = m;
      setImg(varSel.options[varSel.selectedIndex]?.dataset.thumb || m.thumb || null);
      setOTRDisplay(m.otr || 0);

      await fetchMatrix(m.id);

      const hasDp   = dpSel.options.length > 1;
      const hasTen  = tenSel.options.length > 1;
      setDisabled(['btnSimulasi'], !(hasDp && hasTen));
    })(PRESELECT_ID);
  })();
</script>
@endsection