@extends('layouts.appPublic')

@section('content')
  {{-- Back bar (samakan seperti detail apparel) --}}
  @php
    $prev     = url()->previous();
    $current  = url()->current();
    // fallback ke daftar produk kalau previous == current (misal refresh/landing langsung)
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

  <section class="cs-hero mb-4">
    <h1 class="cs-title mb-2">Simulasi Kredit</h1>
    <p class="cs-sub mb-0">
      Gunakan fitur Simulasi Kredit untuk menghitung cicilan motor Honda impianmu.
    </p>
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
              <option value="{{ $c->id }}" {{ (int)$selectedCategory === (int)$c->id ? 'selected' : '' }}>
                {{ $c->name }}
              </option>
            @endforeach
          </select>

          <label class="form-label fw-600 mb-1">Tipe Motor</label>
          <select id="motorSelect" class="form-select cs-select mb-3">
            <option value="">– Pilih Tipe Motor –</option>
            @foreach($motors->where('category_id',$selectedCategory) as $m)
              <option value="{{ $m->id }}"
                      data-name="{{ $m->name }}"
                      data-thumb="{{ $m->thumbnail ? asset('storage/'.$m->thumbnail) : asset('placeholder.png') }}"
                      {{ optional($selectedMotor)->id === $m->id ? 'selected' : '' }}>
                {{ $m->name }}
              </option>
            @endforeach
          </select>

          <label class="form-label fw-600 mb-1">Varian Motor</label>
          <select id="variantSelect" class="form-select cs-select mb-3">
            <option value="" selected>– Pilih Varian Produk –</option>
            @foreach($variants as $v)
              <option value="{{ (float)$v->price }}" data-variant="{{ $v->motor_type }}">{{ $v->motor_type }}</option>
            @endforeach
          </select>

          {{-- preview dibungkus supaya bisa show/hide --}}
          <div id="previewBox" class="d-flex align-items-start gap-3 mt-4 d-none">
            <img id="motorImg"
                 src="{{ $selectedMotor && $selectedMotor->thumbnail ? asset('storage/'.$selectedMotor->thumbnail) : asset('placeholder.png') }}"
                 alt="Motor" class="cs-motor-img">
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
          <label class="form-label fw-600 mb-1">Pilih Jumlah DP*</label>
          <div class="input-group mb-3">
            <span class="input-group-text">Rp</span>
            <input id="dpInput" type="text" class="form-control cs-input" value="">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">%</button>
            <ul class="dropdown-menu dropdown-menu-end">
              @foreach([10,15,20,25,30] as $p)
                <li><a class="dropdown-item js-dp-percent" href="#" data-p="{{ $p }}">{{ $p }}%</a></li>
              @endforeach
            </ul>
          </div>
          <div class="small text-muted mb-3">
            Minimal DP {{ $defaults->min_dp_percent }}% dari OTR.
          </div>

          <label class="form-label fw-600 mb-1">Jangka Waktu</label>
          <div class="d-flex align-items-center w-100 mb-3">
            {{-- select di kiri, lebar tetap --}}
            <select id="tenorSelect"
                    class="form-select cs-select me-3"
                    style="width: 550px; max-width: 100%;">
              @foreach($defaults->tenor_options as $t)
                <option value="{{ $t }}">{{ $t }}</option>
              @endforeach
            </select>
            <span class="fw-600 ms-auto">Bulan</span>
          </div>

          <button id="btnSimulasi" class="btn btn-danger cs-cta w-100">Simulasikan Kredit</button>
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

  {{-- data helper untuk JS (langsung dari controller) --}}
  <script>
    window.CS = {
      defaults: {
        minDpPercent: {{ (float)$defaults->min_dp_percent }},
        interestYear: {{ (float)$defaults->interest_year }},
      },
      dataset: @json($dataset)
    };
  </script>

  <script>
    // util
    const rupiah = n => (new Intl.NumberFormat('id-ID',{maximumFractionDigits:0})).format(Math.round(n||0));
    const onlyNum = s => Number((s||'').toString().replace(/[^0-9]/g,'')||0);

    function fillMotors(catId){
      const el = document.getElementById('motorSelect');
      const all = (window.CS.dataset.motors||[]).filter(m=>m.category_id==catId);
      el.innerHTML = '<option value="">– Pilih Tipe Motor –</option>';
      all.forEach(m=>{
        const o=document.createElement('option');
        o.value=m.id; o.textContent=m.name; o.dataset.name=m.name; o.dataset.thumb=m.thumb;
        el.appendChild(o);
      });
      return all[0]?.id || null;
    }

    function fillVariants(motorName, fallbackOtr){
      const el = document.getElementById('variantSelect');
      const list = (window.CS.dataset.priceList||[]).filter(p=>p.motorcycle_name===motorName);

      // reset ke placeholder saja
      el.innerHTML = '<option value="">– Pilih Varian Produk –</option>';

      if(list.length === 0){
        // Tidak ada varian: simpan harga fallback kalau perlu dipakai saat simulasi.
        el.dataset.fallbackOtr = String(fallbackOtr || 0);
        return;
      }

      // Ada varian: render
      delete el.dataset.fallbackOtr;
      list.forEach(p=>{
        const o=document.createElement('option');
        o.value=Number(p.price||0);
        o.textContent=p.motor_type;
        o.dataset.variant=p.motor_type;
        el.appendChild(o);
      });
    }

    function setOTRDisplay(v){
      const box = document.getElementById('previewBox');
      const txt = document.getElementById('otrText');
      if(!v){
        box.classList.add('d-none');
        txt.textContent = 'Rp 0';
      }else{
        txt.textContent = 'Rp ' + rupiah(v);
        box.classList.remove('d-none');
      }
    }
    function setImg(src){ document.getElementById('motorImg').src = src || '{{ asset('placeholder.png') }}'; }

    (function(){
      // === penting: izinkan dropdown Bootstrap "keluar" dari area scroll ===
      // layout <main> punya id="scroll". Kita tambahkan class marker untuk CSS overflow fix.
      const mainEl = document.getElementById('scroll');
      if (mainEl) mainEl.classList.add('page-has-native-dd');

      const catSel = document.getElementById('catSelect');
      const motorSel = document.getElementById('motorSelect');
      const varSel = document.getElementById('variantSelect');
      const dpInput = document.getElementById('dpInput');

      dpInput.addEventListener('input', (e)=>{
        const n = onlyNum(e.target.value);
        e.target.value = rupiah(n);
      });

      catSel.addEventListener('change', ()=>{
        const newMotorId = fillMotors(catSel.value);
        const opt = document.querySelector(`#motorSelect option[value="${newMotorId}"]`);
        setImg(opt?.dataset.thumb);
        fillVariants(opt?.dataset.name || '', 0);
        setOTRDisplay(0); // reset preview
        dpInput.value = '';
      });

      motorSel.addEventListener('change', ()=>{
        const opt = motorSel.options[motorSel.selectedIndex];
        setImg(opt?.dataset.thumb);
        fillVariants(opt?.dataset.name || '', 0);
        setOTRDisplay(0); // reset preview
        dpInput.value='';
      });

      // varian: pilih placeholder -> hide, pilih berharga -> show
      varSel.addEventListener('change', ()=>{
        const val = Number(varSel.value||0);
        setOTRDisplay(val);
      });

      document.querySelectorAll('.js-dp-percent').forEach(a=>{
        a.addEventListener('click',(ev)=>{
          ev.preventDefault();
          const p = Number(a.dataset.p||0);
          const otr = Number(document.getElementById('variantSelect').value||0);
          dpInput.value = rupiah(Math.round(otr * p / 100));
        });
      });

      document.getElementById('btnSimulasi').addEventListener('click', ()=>{
        const otr   = Number(document.getElementById('variantSelect').value||0);
        let   dp    = onlyNum(dpInput.value);
        const tenor = Number(document.getElementById('tenorSelect').value||0);

        const minDp = Math.round(otr * (window.CS.defaults.minDpPercent/100));
        if(dp < minDp) dp = minDp;

        const pokok      = Math.max(0, otr - dp);
        const bungaTahun = (window.CS.defaults.interestYear||0)/100;
        const bungaTotal = pokok * bungaTahun * (tenor/12);
        const totalBayar = pokok + bungaTotal;
        const angsuran   = Math.ceil(totalBayar / tenor / 100) * 100;

        document.getElementById('angsuranText').textContent = 'Rp ' + rupiah(angsuran);
        dpInput.value = rupiah(dp);
        if (otr>0) setOTRDisplay(otr);
      });

      // keadaan awal
      setOTRDisplay(0);
    })();
  </script>
@endsection