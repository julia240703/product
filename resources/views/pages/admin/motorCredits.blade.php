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
              <li class="breadcrumb-item active" aria-current="page">Credit Simulation</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>

  {{-- Tombol bulk upload + template --}}
  <div class="d-flex gap-2 mb-3">
    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#bulkModal">
      Bulk Upload (Excel)
    </button>

    <a href="{{ route('admin.credits.template', $motor->id) }}" class="btn btn-outline-secondary btn-sm" download>
      Unduh Template
    </a>
  </div>

  {{-- =================== MODAL BULK UPLOAD =================== --}}
  <div class="modal fade" id="bulkModal" tabindex="-1" aria-labelledby="bulkLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form id="bulkForm" class="modal-content" enctype="multipart/form-data">
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

          {{-- field lama (hidden) untuk kompatibilitas --}}
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

          <div id="bulkAlert" class="alert d-none mt-3" role="alert"></div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
          <button type="submit" class="btn btn-success" id="btnBulkSubmit">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  {{-- =================== MODAL EDIT BARIS =================== --}}
  <div class="modal fade" id="rowModal" tabindex="-1" aria-labelledby="rowLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <form id="rowForm" class="modal-content">
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
              <input type="text" class="form-control js-money" name="dp" id="row_dp" placeholder="mis. 6.500">
              <div class="form-text">
                Ketik dalam satuan <em>ribuan</em>. Contoh: <code>6.500</code> = <strong>6.500.000</strong>.
              </div>
            </div>
            <div class="col-sm-8">
              <label class="form-label">Tenor &amp; Angsuran</label>
              <div id="row_tenors" class="row g-2">
                {{-- diisi dinamis --}}
              </div>
              <div class="form-text">
                Angsuran juga tanpa “Rp”. Contoh: <code>750</code> = <strong>750.000</strong>.
              </div>
            </div>
          </div>
          <div id="rowAlert" class="alert d-none mt-3"></div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary" id="btnRowSave">Simpan Perubahan</button>
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
              <th>Daftar DP</th> {{-- tanpa (Rp) --}}
              <th>Aksi</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>

  {{-- =============== MODAL HAPUS (disamakan dengan halaman Fitur Motor) =============== --}}
  <div class="modal fade" id="deleteHeaderModal" tabindex="-1" aria-labelledby="deleteHeaderLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form id="deleteHeaderForm" method="POST">
        @csrf
        @method('DELETE')
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteHeaderLabel">Konfirmasi Hapus Periode</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
          </div>
          <div class="modal-body">
            <p>
              Apakah kamu yakin ingin menghapus
              <strong>Simulasi Kredit – {{ $motor->name }}</strong>?
            </p>
            {{-- opsional: detail periode/DP ditampilkan di sini via JS bila ada --}}
            <div id="delete_header_desc" class="text-muted small"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger" id="btnHeaderDeleteYes">Ya, Hapus</button>
          </div>
        </div>
      </form>
    </div>
  </div>

</div>

{{-- =================== SCRIPTS =================== --}}
<script>
$(function () {
  // ===== DataTables
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

  // Samakan style tombol aksi (biru/merah + ikon) & bungkus btn-group
  $('#credits-table').on('draw.dt', function(){
    $('#credits-table .js-edit')
      .addClass('btn btn-sm btn-primary me-1')
      .each(function(){
        if($(this).find('i.fa-solid.fa-pen-to-square').length === 0){
          $(this).html('<i class="fa-solid fa-pen-to-square"></i>');
        }
      });

    $('#credits-table .js-del')
      .addClass('btn btn-sm btn-danger')
      .each(function(){
        if($(this).find('i.fa-solid.fa-trash').length === 0){
          $(this).html('<i class="fa-solid fa-trash"></i>');
        }
      });

    $('#credits-table td:has(.js-edit, .js-del)').each(function(){
      const cell = $(this);
      if(cell.find('.btn-group').length === 0){
        const e = cell.find('.js-edit').detach();
        const d = cell.find('.js-del').detach();
        cell.empty().append($('<div class="btn-group"></div>').append(e).append(d));
      }
    });
  });

  // ===== Bulk upload
  $('#bulkForm').on('submit', function(e){
    e.preventDefault();
    $('#bulkAlert').addClass('d-none').removeClass('alert-danger alert-success').text('');

    const file = $('#excel_file')[0].files[0];
    if(!file){
      $('#bulkAlert').removeClass('d-none').addClass('alert-danger').text('Pilih file terlebih dahulu.');
      return;
    }

    const fd = new FormData(this);
    $('#btnBulkSubmit').prop('disabled', true).text('Menyimpan…');

    $.ajax({
      url: "{{ route('admin.credits.import', $motor->id) }}",
      method: 'POST',
      data: fd,
      processData: false,
      contentType: false
    })
    .done(function(){
      $('#bulkAlert').removeClass('d-none').addClass('alert-success').text('Upload selesai.');
      table.ajax.reload(null, false);
      setTimeout(()=>$('#bulkModal').modal('hide'), 700);
    })
    .fail(function(xhr){
      const msg = xhr?.responseJSON?.message || 'Gagal mengunggah file. Pastikan format sesuai template.';
      $('#bulkAlert').removeClass('d-none').addClass('alert-danger').text(msg);
    })
    .always(function(){
      $('#btnBulkSubmit').prop('disabled', false).text('Simpan');
    });
  });

  // Reset modal bulk
  $('#bulkModal').on('hidden.bs.modal', function(){
    this.querySelector('#bulkForm').reset();
    $('#bulkAlert').addClass('d-none').removeClass('alert-danger alert-success').text('');
  });

  // ===== Open edit baris
  $(document).on('click', '.js-edit', function(){
    const headerId = $(this).data('id');
    const dpRaw    = $(this).data('dp');
    $('#row_header_id').val(headerId);
    $('#row_old_dp').val(dpRaw);
    $('#rowAlert').addClass('d-none').removeClass('alert-danger alert-success').text('');
    $('#row_tenors').empty();

    $('#row_dp').val(new Intl.NumberFormat('id-ID').format(dpRaw));

    const url = "{{ route('admin.credits.row.show', ['motor'=>$motor->id, 'header'=>'HID']) }}".replace('HID', headerId) + '?dp='+dpRaw;

    $.getJSON(url, function(res){
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

  // ===== Save edit baris
  $('#rowForm').on('submit', function(e){
    e.preventDefault();
    const headerId = $('#row_header_id').val();
    const url = "{{ route('admin.credits.row.update', ['motor'=>$motor->id, 'header'=>'HID']) }}".replace('HID', headerId);

    const payload = $(this).serializeArray();

    $('#btnRowSave').prop('disabled', true).text('Menyimpan…');
    $.ajax({ url, method: 'POST', data: payload })
      .done(function(){
        $('#rowAlert').removeClass('d-none').addClass('alert-success').text('Perubahan disimpan.');
        table.ajax.reload(null, false);
        setTimeout(()=>$('#rowModal').modal('hide'), 600);
      })
      .fail(function(xhr){
        const msg = xhr?.responseJSON?.message || 'Gagal menyimpan.';
        $('#rowAlert').removeClass('d-none').addClass('alert-danger').text(msg);
      })
      .always(function(){
        $('#btnRowSave').prop('disabled', false).text('Simpan Perubahan');
      });
  });

  // ===== Hapus periode/header -> pakai modal
  let __delUrl = null;

  $(document).on('click', '.js-del', function(){
    const headerId = $(this).data('id');
    const desc     = $(this).data('desc') || ''; // contoh: "DP 6.500 • Tenor 35 bln"
    __delUrl = "{{ route('admin.credits.delete', ['motor' => $motor->id, 'header' => 'HID']) }}"
                .replace('HID', headerId);

    $('#delete_header_desc').text(desc); // akan kosong jika tak disediakan
    $('#deleteHeaderForm').attr('action', __delUrl);
    $('#deleteHeaderModal').modal('show');
  });

  $('#deleteHeaderForm').on('submit', function(e){
    e.preventDefault();
    if(!__delUrl) return;

    $('#btnHeaderDeleteYes').prop('disabled', true).text('Menghapus…');
    $.ajax({
      url: __delUrl,
      method: 'POST',
      data: { _method:'DELETE', _token:'{{ csrf_token() }}' }
    })
    .done(function(){
      $('#deleteHeaderModal').modal('hide');
      table.ajax.reload(null, false);
    })
    .fail(function(){
      alert('Gagal menghapus.');
    })
    .always(function(){
      $('#btnHeaderDeleteYes').prop('disabled', false).text('Ya, Hapus');
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