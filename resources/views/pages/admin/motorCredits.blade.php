@extends('layouts.appAdmin')
<title>Simulasi Kredit • {{ $motor->name }}</title>

@section('content')
<div class="container-fluid">
  {{-- Header + breadcrumb --}}
  <div class="title-wrapper pt-30">
    <div class="row align-items-center">
      <div class="col-md-6">
        <div class="title mb-30">
          <h2>Data Simulasi Kredit: {{ $motor->name }}</h2>
        </div>
      </div>
      <div class="col-md-6">
        <div class="breadcrumb-wrapper mb-30">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="#0">Admin</a></li>
              <li class="breadcrumb-item"><a href="{{ route('admin.motors.published') }}">Motor</a></li>
              <li class="breadcrumb-item active" aria-current="page">Credit Simulation</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>

  {{-- Tombol bulk upload + template + hapus semua --}}
  <div class="d-flex gap-2 mb-3 align-items-center">
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#bulkModal">
        Bulk Upload (Excel)
      </button>

      <a href="{{ route('admin.credits.template', $motor->id) }}" class="btn btn-outline-secondary btn-sm" download>
        Unduh Template
      </a>
    </div>

    <div class="ms-auto">
      <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAllModal">
        Hapus Seluruh Data
      </button>
    </div>
  </div>

  {{-- =================== MODAL BULK UPLOAD =================== --}}
  <div class="modal fade" id="bulkModal" tabindex="-1" aria-labelledby="bulkLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form id="bulkForm" class="modal-content" method="POST" action="{{ route('admin.credits.import', $motor->id) }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="bulkLabel">Bulk Upload Simulasi Kredit ({{ $motor->name }})</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">File Excel (.xlsx / .xls / .csv)</label>
            <input type="file" name="file" id="excel_file" class="form-control" accept=".xlsx,.xls,.csv" required>
          </div>

          {{-- kompat --}}
          <input type="hidden" name="provider_id" value="">
          <input type="hidden" name="valid_from" value="">
          <input type="hidden" name="valid_to" value="">
          <input type="hidden" name="note" value="">

          <div class="mt-3">
            <label class="form-label">Jika ada DP yang sama di file</label>
            <select class="form-select" name="mode">
              <option value="skip">Lewati (pakai yang sudah ada)</option>
              <option value="overwrite">Timpa isinya</option>
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
          <button type="submit" class="btn btn-success" id="btnBulkSubmit"
                  onclick="this.disabled=true;this.innerText='Menyimpan…';this.form.submit();">
            Simpan
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- =================== MODAL EDIT BARIS =================== --}}
  <div class="modal fade" id="rowModal" tabindex="-1" aria-labelledby="rowLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <form id="rowForm" class="modal-content" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="header_id" id="row_header_id">
        <input type="hidden" name="old_dp" id="row_old_dp">

        <div class="modal-header">
          <h5 class="modal-title" id="rowLabel">Edit Baris Simulasi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-sm-4">
              <label class="form-label">DP (ribuan/jutaan)</label>
              <input type="text" class="form-control js-money" name="dp" id="row_dp" placeholder="mis. 6.500" required>
              <div class="form-text">
                Ketik dalam satuan <em>ribuan</em>. Contoh: <code>6.500</code> = <strong>6.500.000</strong>.
              </div>
            </div>
            <div class="col-sm-8">
              <label class="form-label">Tenor &amp; Angsuran</label>
              <div id="row_tenors" class="row g-2"></div>
              <div class="form-text">
                Angsuran juga tanpa “Rp”. Contoh: <code>750</code> = <strong>750.000</strong>.
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary" id="btnRowSave"
                  onclick="this.disabled=true;this.innerText='Menyimpan…';this.form.submit();">
            Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- =================== CARD CATATAN =================== --}}
  <div class="card mb-3">
    <div class="card-body py-3">
      <div class="d-flex">
        <div class="me-3">
          <span class="d-inline-block rounded-circle bg-danger" style="width:10px;height:10px;margin-top:7px;"></span>
        </div>
        <div>
          <div class="fw-bold mb-1">Catatan</div>
          <div class="mb-0">
            Angka pada kolom <strong>Daftar DP</strong> menggunakan satuan
            <em>ribuan = jutaan rupiah</em>. Contoh:
            <code>6.500</code> dibaca <strong>6.500.000</strong>.
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- =================== TABEL =================== --}}
  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="display" id="credits-table" style="width:100%">
          <thead>
            <tr>
              <th>#</th>
              <th>Tenor (bln)</th>
              <th>Daftar DP</th>
              <th>Aksi</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>

  {{-- =============== MODAL HAPUS 1 BARIS DP =============== --}}
  <div class="modal fade" id="deleteRowModal" tabindex="-1" aria-labelledby="deleteRowLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form id="deleteRowForm" method="POST">
        @csrf
        @method('DELETE')
        <input type="hidden" id="del_row_header_id">
        <input type="hidden" id="del_row_dp" name="dp">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteRowLabel">Hapus 1 Baris DP</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
          </div>
          <div class="modal-body">
            <p>
              Hapus baris DP <strong id="del_row_dp_text"></strong> pada
              <strong>Simulasi Kredit – {{ $motor->name }}</strong>?
            </p>
            <div class="text-muted small">Tenor terkait baris DP ini juga ikut terhapus.</div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger" id="btnRowDeleteYes">
              Ya, Hapus Baris
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- =============== MODAL HAPUS SEMUA HEADER/ITEM =============== --}}
  <div class="modal fade" id="deleteAllModal" tabindex="-1" aria-labelledby="deleteAllLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form id="deleteAllForm" method="POST" action="{{ route('admin.credits.deleteAll', $motor->id) }}">
        @csrf
        @method('DELETE')
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteAllLabel">Hapus Seluruh Data</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
          </div>
          <div class="modal-body">
            <div class="alert alert-warning">
              Tindakan ini akan menghapus <strong>seluruh periode/header & item</strong>
              Simulasi Kredit untuk <strong>{{ $motor->name }}</strong>. Tidak bisa dibatalkan.
            </div>
            <p class="mb-0">Yakin ingin melanjutkan?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger" id="btnDeleteAllYes"
                    onclick="this.disabled=true;this.innerText='Menghapus…';this.form.submit();">
              Ya, Hapus Semua
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- =================== SCRIPTS =================== --}}
<script>
$(function () {
  // === Flash ala appAdmin (pojok kanan atas, ijo)
  function flashSuccess(message) {
    const box = document.createElement('div');
    box.className = 'alert alert-success position-fixed top-0 end-0 m-3';
    box.style.maxWidth = '300px';
    box.style.zIndex = '1050';
    box.innerHTML = '<p class="mb-0">'+ message +'</p>';
    document.body.appendChild(box);
    setTimeout(()=> box.classList.add('show'), 10);   // (opsi) animasi kecil
    setTimeout(()=> { box.remove(); }, 2500);
  }

  // DataTables
  const table = $('#credits-table').DataTable({
    responsive: true, processing: true, serverSide: true,
    ajax: "{{ route('admin.credits.headers', $motor->id) }}",
    columns: [
      { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
      { data: 'tenors_text', name: 'tenors_text', defaultContent: '-' },
      { data: 'dp_text',     name: 'dp_text',     defaultContent: '-' },
      { data: 'aksi',        name: 'aksi',        orderable: false, searchable: false }
    ]
  });

  // Style tombol aksi & grupkan
  $('#credits-table').on('draw.dt', function(){
    $('#credits-table .js-edit')
      .addClass('btn btn-sm btn-primary me-1')
      .attr('title','Edit baris DP')
      .each(function(){ if($(this).find('i.fa-solid.fa-pen-to-square').length === 0){ $(this).html('<i class="fa-solid fa-pen-to-square"></i>'); } });

    $('#credits-table .js-del-row')
      .addClass('btn btn-sm btn-danger')
      .attr('title','Hapus 1 baris DP')
      .each(function(){ if($(this).find('i.fa-solid fa-trash').length === 0){ $(this).html('<i class="fa-solid fa-trash"></i>'); } });

    $('#credits-table td:has(.js-edit, .js-del-row)').each(function(){
      const cell = $(this);
      if(cell.find('.btn-group').length === 0){
        const e = cell.find('.js-edit').detach();
        const r = cell.find('.js-del-row').detach();
        cell.empty().append($('<div class="btn-group"></div>').append(e).append(r));
      }
    });
  });

  // Reset form saat modal ditutup
  $('.modal').on('hidden.bs.modal', function () {
    this.querySelector('form')?.reset();
  });

  // Open edit baris
  $(document).on('click', '.js-edit', function(){
    const headerId = $(this).data('id');
    const dpRaw    = $(this).data('dp');

    $('#row_header_id').val(headerId);
    $('#row_old_dp').val(dpRaw);
    $('#row_tenors').empty();
    $('#row_dp').val(new Intl.NumberFormat('id-ID').format(dpRaw));

    const urlShow   = "{{ route('admin.credits.row.show', ['motor'=>$motor->id, 'header'=>'HID']) }}".replace('HID', headerId) + '?dp=' + dpRaw;
    const urlUpdate = "{{ route('admin.credits.row.update', ['motor'=>$motor->id, 'header'=>'HID']) }}".replace('HID', headerId);
    $('#rowForm').attr('action', urlUpdate);

    $.getJSON(urlShow, function(res){
      const wrap = $('#row_tenors');
      (res.tenors || []).forEach(function(t){
        const val = res.values && res.values[String(t)] ? res.values[String(t)] : '';
        const txt = val ? new Intl.NumberFormat('id-ID').format(val) : '';
        wrap.append(`
          <div class="col-6 col-md-4">
            <div class="input-group">
              <span class="input-group-text">${t} bln</span>
              <input type="text" class="form-control js-money" name="tenor[${t}]" value="${txt}" placeholder="0">
            </div>
          </div>
        `);
      });
      $('#rowModal').modal('show');
    }).fail(function(){
      alert('Gagal memuat data baris.');
    });
  });

  // ===== Hapus 1 BARIS DP (AJAX)
  let __delRowHeaderId = null;
  let __delRowDp = null;

  $(document).on('click', '.js-del-row', function(){
    __delRowHeaderId = $(this).data('id');
    __delRowDp = parseInt($(this).data('dp') || '0', 10);

    const urlDelete = "{{ route('admin.credits.row.delete', ['motor'=>$motor->id, 'header'=>'HID']) }}".replace('HID', __delRowHeaderId);
    $('#deleteRowForm').attr('action', urlDelete);

    $('#del_row_header_id').val(__delRowHeaderId);
    $('#del_row_dp').val(__delRowDp);
    $('#del_row_dp_text').text(new Intl.NumberFormat('id-ID').format(__delRowDp));

    $('#deleteRowModal').modal('show');
  });

  // INTERCEPT submit → AJAX DELETE + flash ijo pojok kanan
  $('#deleteRowForm').on('submit', function(e){
    e.preventDefault();
    const url = $(this).attr('action');
    const dp  = $('#del_row_dp').val();

    $('#btnRowDeleteYes').prop('disabled', true).text('Menghapus…');

    $.ajax({
      url: url,
      method: 'POST',
      data: { _method:'DELETE', _token:'{{ csrf_token() }}', dp }
    })
    .done(function(){
      $('#deleteRowModal').modal('hide');
      table.ajax.reload(null, false);
      flashSuccess('Baris DP ' + new Intl.NumberFormat('id-ID').format(dp) + ' — {{ $motor->name }} berhasil dihapus.');
    })
    .fail(function(xhr){
      const msg = xhr?.responseJSON?.message || 'Gagal menghapus baris.';
      // pakai alert biasa untuk error (atau bikin flash merah kalau mau)
      alert(msg);
    })
    .always(function(){
      $('#btnRowDeleteYes').prop('disabled', false).text('Ya, Hapus Baris');
    });
  });

  // util: format uang saat ketik
  $(document).on('input', '.js-money', function(){
    const digits = this.value.replace(/[^\d]/g,'');
    this.value = digits ? new Intl.NumberFormat('id-ID').format(parseInt(digits)) : '';
  });
});
</script>
@endsection