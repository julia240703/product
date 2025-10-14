@extends('layouts.appAdmin')
<title>Simulasi Kredit • {{ $motor->name }}</title>

@section('content')
<div class="container-fluid">
  {{-- Header + breadcrumb --}}
  <div class="title-wrapper pt-30">
    <div class="row align-items-center">
      <div class="col-md-6">
        <div class="title mb-30">
          <h2>Simulasi Kredit: {{ $motor->name }}</h2>
        </div>
      </div>
      <div class="col-md-6">
        <div class="breadcrumb-wrapper mb-30">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="#0">Admin</a></li>
              <li class="breadcrumb-item"><a href="{{ route('admin.motors.published') }}">Motor</a></li>
              <li class="breadcrumb-item active" aria-current="page">Simulasi Kredit</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>

  {{-- Tombol Tambah --}}
  <button type="button" class="btn btn-success mb-3 btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
    Tambah Simulasi
  </button>

  {{-- =================== MODAL TAMBAH =================== --}}
  <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addCreditLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <form class="modal-content" id="addCreditForm" method="POST" action="{{ route('admin.credits.store', $motor->id) }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="addCreditLabel">Tambah Simulasi Kredit</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>

        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label">Tenor (bulan)</label>
            <input type="text" id="add_tenors" class="form-control" value="{{ implode(',', $tenors) }}">
            <div class="form-text">Pisahkan dengan koma (mis: 11,17,23,27,29,33,35,41)</div>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Tabel DP × Tenor</h6>
            <button class="btn btn-sm btn-outline-secondary add-btnAddRow" type="button">+ Tambah baris DP</button>
          </div>

          <div class="table-responsive">
            <table class="table table-bordered align-middle add-matrix"></table>
          </div>
          <small class="text-muted d-block">Isi DP & angsuran/bulan. Boleh format 1.000.000.</small>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
          <button type="submit" class="btn btn-success">Tambahkan</button>
        </div>
      </form>
    </div>
  </div>

  {{-- =================== MODAL EDIT =================== --}}
  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editCreditLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <form class="modal-content" id="editCreditForm" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit_header_id">
        <div class="modal-header">
          <h5 class="modal-title" id="editCreditLabel">Ubah Simulasi Kredit</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>

        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label">Tenor (bulan)</label>
            <input type="text" id="edit_tenors" class="form-control" value="{{ implode(',', $tenors) }}">
            <div class="form-text">Pisahkan dengan koma.</div>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">Tabel DP × Tenor</h6>
            <button class="btn btn-sm btn-outline-secondary edit-btnAddRow" type="button">+ Tambah baris DP</button>
          </div>

          <div class="table-responsive">
            <table class="table table-bordered align-middle edit-matrix"></table>
          </div>
          <small class="text-muted d-block">Isi DP & angsuran/bulan. Boleh format 1.000.000.</small>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
          <button type="submit" class="btn btn-primary">Ubah</button>
        </div>
      </form>
    </div>
  </div>

  {{-- =================== MODAL HAPUS =================== --}}
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteCreditLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form id="deleteForm" method="POST">
        @csrf
        @method('DELETE')
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteCreditLabel">Konfirmasi Hapus Periode</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
          </div>
          <div class="modal-body">
            <p>Hapus periode ini?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- =================== TABLE (ringkas: tenor & DP) =================== --}}
  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="display" id="credits-table" style="width:100%">
          <thead>
            <tr>
              <th>#</th>
              <th>Tenor (bln)</th>
              <th>Daftar DP (Rp)</th>
              <th>Aksi</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- =================== SCRIPTS =================== --}}
<script>
$(function(){

  // ====== Tabel ringkas: 1 DP = 1 baris ======
  const table = $('#credits-table').DataTable({
    responsive: true, processing: true, serverSide: true,
    ajax: "{{ route('admin.credits.headers', $motor->id) }}",
    columns: [
      { data:'DT_RowIndex', name:'DT_RowIndex', orderable:false, searchable:false },
      { data:'tenors_text', name:'tenors_text', defaultContent:'-' },
      { data:'dp_text',      name:'dp_text',     defaultContent:'-' },
      { data:'aksi',         name:'aksi',        orderable:false, searchable:false }
    ]
  });

  // ====== util: builder matriks ======
  function parseTenors($input){
    const raw = $input.val() || '';
    return raw.split(',').map(x=>parseInt(x.trim(),10)).filter(n=>!isNaN(n)&&n>0);
  }
  function renderMatrix($table, $tenorInput, rows=[]) {
    const tenors = parseTenors($tenorInput);
    let thead = '<thead><tr><th style="width:160px">DP (Rp)</th>';
    tenors.forEach(t=> thead += `<th>${t}</th>`);
    thead += '<th style="width:60px"></th></tr></thead>';

    if(!rows.length) rows=[{dp:'', cols:{}}];

    let tbody = '<tbody>';
    rows.forEach(r=>{
      tbody += `<tr class="dp-row">
        <td><input type="text" class="form-control form-control-sm dp" value="${r.dp??''}"></td>`;
      tenors.forEach(t=>{
        const v = (r.cols && (r.cols[t] ?? r.cols[String(t)])) ? (r.cols[t] ?? r.cols[String(t)]) : '';
        tbody += `<td><input type="text" class="form-control form-control-sm tenor" data-tenor="${t}" value="${v}"></td>`;
      });
      tbody += `<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger rm-row">&times;</button></td></tr>`;
    });
    tbody += '</tbody>';

    $table.html(thead+tbody);
  }
  function collectRows($table){
    const rows=[];
    $table.find('tbody tr.dp-row').each(function(){
      const dp = $(this).find('input.dp').val();
      const cols={};
      $(this).find('input.tenor').each(function(){
        cols[$(this).data('tenor')] = $(this).val();
      });
      const obj = { dp };
      Object.keys(cols).forEach(k=> obj[k]=cols[k]);
      rows.push(obj);
    });
    return rows;
  }

  // ====== ADD ======
  const $addMatrix = $('.add-matrix');
  const $addTenors = $('#add_tenors');
  renderMatrix($addMatrix, $addTenors, []);
  $addTenors.on('change', function(){
    const rows=[];
    $addMatrix.find('tbody tr.dp-row').each(function(){
      const dp = $(this).find('input.dp').val();
      const cols={};
      $(this).find('input.tenor').each(function(){ cols[$(this).data('tenor')] = $(this).val(); });
      rows.push({dp, cols});
    });
    renderMatrix($addMatrix, $addTenors, rows);
  });
  $(document).on('click','.add-btnAddRow', function(){
    const rows = collectRows($addMatrix).map(r=>{
      const cols={}; Object.keys(r).forEach(k=>{ if(k!=='dp') cols[k]=r[k]; });
      return { dp:r.dp, cols };
    });
    rows.push({dp:'', cols:{}});
    renderMatrix($addMatrix, $addTenors, rows);
  });
  $(document).on('click','.add-matrix .rm-row', function(){ $(this).closest('tr').remove(); });

  // ====== EDIT ======
  const $editMatrix = $('.edit-matrix');
  const $editTenors = $('#edit_tenors');

  function loadHeaderToEdit(headerId, dpClicked){
    const url = "{{ route('admin.credits.edit', ['motor'=>$motor->id, 'header'=>'HID']) }}".replace('HID', headerId);
    $.get(url, function(r){
      $('#edit_header_id').val(r.header.id);

      // Susun tenor (kolom) dari seluruh rows
      const set = new Set();
      (r.rows||[]).forEach(row => Object.keys(row.cols||{}).forEach(k => set.add(parseInt(k,10))));
      if(set.size) $editTenors.val(Array.from(set).sort((a,b)=>a-b).join(','));

      // === FILTER: cuma baris DP yang diklik ===
      let rows = r.rows || [];
      if (dpClicked !== null) {
        rows = rows.filter(row => {
          const n = parseInt(String(row.dp).replace(/[^\d]/g,''), 10) || 0;
          return n === dpClicked;
        });
        if (!rows.length) rows = r.rows || []; // fallback
      }

      renderMatrix($editMatrix, $editTenors, rows);
      $('#editModal').modal('show');
    });
  }

  $editTenors.on('change', function(){
    const rows=[];
    $editMatrix.find('tbody tr.dp-row').each(function(){
      const dp = $(this).find('input.dp').val();
      const cols={};
      $(this).find('input.tenor').each(function(){ cols[$(this).data('tenor')] = $(this).val(); });
      rows.push({dp, cols});
    });
    renderMatrix($editMatrix, $editTenors, rows);
  });
  $(document).on('click','.edit-btnAddRow', function(){
    const rows = collectRows($editMatrix).map(r=>{
      const cols={}; Object.keys(r).forEach(k=>{ if(k!=='dp') cols[k]=r[k]; });
      return { dp:r.dp, cols };
    });
    rows.push({dp:'', cols:{}});
    renderMatrix($editMatrix, $editTenors, rows);
  });
  $(document).on('click','.edit-matrix .rm-row', function(){ $(this).closest('tr').remove(); });

  // ====== ACTION: Edit/Hapus dari tabel ======
  $(document).on('click', '.js-edit', function(){
    const headerId = $(this).data('id');
    const dpClicked = parseInt($(this).attr('data-dp'), 10) || null; // <-- DP mentah dari tombol

    const action = "{{ route('admin.credits.update', ['motor'=>$motor->id, 'header'=>'HID']) }}".replace('HID', headerId);
    $('#editCreditForm').attr('action', action);

    loadHeaderToEdit(headerId, dpClicked);
  });

  $(document).on('click', '.js-del', function(){
    const headerId = $(this).data('id');
    const delUrl   = "{{ route('admin.credits.delete', ['motor'=>$motor->id, 'header'=>'HID']) }}".replace('HID', headerId);
    $('#deleteForm').attr('action', delUrl);
    $('#deleteModal').modal('show');
  });

  // ====== SUBMIT ======
  $('#addCreditForm').on('submit', function(e){
    e.preventDefault();
    const payload = {
      _token : '{{ csrf_token() }}',
      tenors : parseTenors($('#add_tenors')),
      rows   : collectRows($addMatrix)
    };
    $.post($(this).attr('action'), payload)
      .done(()=> { $('#addModal').modal('hide'); table.ajax.reload(null,false); })
      .fail(()=> alert('Gagal menambah simulasi. Periksa input Anda.'));
  });

  $('#editCreditForm').on('submit', function(e){
    e.preventDefault();
    const payload = {
      _token : '{{ csrf_token() }}',
      _method: 'PUT',
      tenors : parseTenors($('#edit_tenors')),
      rows   : collectRows($editMatrix)
    };
    $.ajax({url: $(this).attr('action'), method:'POST', data: payload})
      .done(()=> { $('#editModal').modal('hide'); table.ajax.reload(null,false); })
      .fail(()=> alert('Gagal mengubah simulasi.'));
  });

  // ====== Reset modal saat ditutup ======
  $('.modal').on('hidden.bs.modal', function () {
    const form = this.querySelector('form');
    if (form) form.reset?.();
    if (this.id === 'addModal') {
      $('#add_tenors').val("{{ implode(',', $tenors) }}");
      renderMatrix($addMatrix, $addTenors, []);
    }
  });

});
</script>
@endsection