@extends('layouts.appAdmin')
<title>Kelola Fitur Motor</title>

@section('content')
    <div class="container-fluid">
        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="title mb-30">
                        <h2>Data Fitur Motor</h2>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="breadcrumb-wrapper mb-30">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="#0">Admin</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Motor Feature
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-success mb-3 btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
            Tambah Fitur
        </button>

        <!-- Modal Tambah -->
        <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addFeatureLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addFeatureLabel">Tambah Fitur Motor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('admin.features.store', $motor->id) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Fitur <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Gambar <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="description" name="description"></textarea>
                            </div>
                            <div class="mb-3">
  <label class="form-label">Tentukan Titik pada Gambar (klik)</label>
  <div id="picker" class="position-relative"
       style="border:1px dashed #dee2e6;border-radius:12px;overflow:hidden;">
    <img id="pickerImage"
         src="{{ $motor->feature_image ? asset('storage/'.$motor->feature_image) : asset('storage/'.$motor->feature_thumbnail) }}"
         alt="Base Image"
         class="img-fluid w-100">
    <div id="pickerDot"
         class="position-absolute"
         style="width:18px;height:18px;border-radius:50%;background:#dc3545;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.25);pointer-events:none;display:none;">
    </div>
  </div>
  <small class="text-muted">Klik pada gambar untuk mengisi X/Y (dalam persen). Nilai 0–100.</small>
</div>
                            <div class="mb-3">
                                <label for="x_position" class="form-label">Posisi X (%) <span class="text-danger">*</span></label>
<input type="number" step="0.01" min="0" max="100" class="form-control" id="x_position" name="x_position" required>
                            </div>
                            <div class="mb-3">
                                <label for="y_position" class="form-label">Posisi Y (%) <span class="text-danger">*</span></label>
<input type="number" step="0.01" min="0" max="100" class="form-control" id="y_position" name="y_position" required>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                                <button type="submit" class="btn btn-success">Tambahkan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Edit -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editFeatureLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editFeatureLabel">Ubah Data Fitur Motor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editFeatureForm" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="name_edit" class="form-label">Nama Fitur <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name_edit" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="image_edit" class="form-label">Gambar <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="image_edit" name="image" accept="image/*">
                                <div id="current-image" class="mt-2"></div>
                            </div>
                            <div class="mb-3">
                                <label for="description_edit" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="description_edit" name="description"></textarea>
                            </div>
                            <div class="mb-3">
  <label class="form-label">Tentukan Titik pada Gambar (klik)</label>
  <div id="picker" class="position-relative"
       style="border:1px dashed #dee2e6;border-radius:12px;overflow:hidden;">
    <img id="pickerImage"
         src="{{ $motor->feature_image ? asset('storage/'.$motor->feature_image) : asset('storage/'.$motor->feature_thumbnail) }}"
         alt="Base Image"
         class="img-fluid w-100">
    <div id="pickerDot"
         class="position-absolute"
         style="width:18px;height:18px;border-radius:50%;background:#dc3545;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.25);pointer-events:none;display:none;">
    </div>
  </div>
  <small class="text-muted">Klik pada gambar untuk mengisi X/Y (dalam persen). Nilai 0–100.</small>
</div>
                            <div class="mb-3">
                                <label for="x_position_edit" class="form-label">Posisi X (%) <span class="text-danger">*</span></label>
<input type="number" step="0.01" min="0" max="100" class="form-control" id="x_position_edit" name="x_position" required>
                            </div>
                            <div class="mb-3">
                                <label for="y_position_edit" class="form-label">Posisi Y (%) <span class="text-danger">*</span></label>
<input type="number" step="0.01" min="0" max="100" class="form-control" id="y_position_edit" name="y_position" required>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                                <button type="submit" class="btn btn-success">Ubah</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Hapus -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteFeatureLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteFeatureLabel">Konfirmasi Hapus Fitur</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <p>Apakah kamu yakin ingin menghapus fitur <strong id="delete_feature_name"></strong>?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal View Image -->
        <div class="modal fade" id="viewImageModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Lihat Gambar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img id="modalImage" src="" alt="Preview" class="img-fluid rounded" style="max-height: 500px;">
                        <div class="mt-2">
                            <p id="imageTitle" class="mb-1 fw-bold"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="display" id="feature-table" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Fitur</th>
                                <th>Gambar</th>
                                <th>Posisi (X,Y)</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <!-- Script -->
        <script>
$(function () {

  // ================== DATATABLE ==================
  var dataTable = $('#feature-table').DataTable({
    responsive: true,
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.features.index', $motor->id) }}",
    columns: [
      { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
      { data: 'name', name: 'name' },
      {
        data: 'image',
        name: 'image',
        className: "text-center",
        render: function(data, type, row) {
          return data
            ? `<img src="${data}" style="width:50px;height:50px;object-fit:cover;cursor:pointer;" class="rounded image-preview" data-image="${data}" data-title="${row.name || ''}">`
            : '<span class="text-muted">Tidak ada gambar</span>';
        }
      },
      { data: 'position', name: 'position', render: function(data){ return data || 'N/A'; } },
      {
        data: null,
        orderable: false,
        searchable: false,
        render: function(data) {
          return `
            <div class="btn-group">
              <button class="btn btn-sm btn-primary me-1 editBtn" data-id="${data.id}">
                <i class="fa-solid fa-pen-to-square"></i>
              </button>
              <button class="btn btn-sm btn-danger deleteBtn" data-id="${data.id}" data-name="${data.name}">
                <i class="fa-solid fa-trash"></i>
              </button>
            </div>
          `;
        }
      }
    ]
  });

  // ========== EDIT ==========
  $(document).on('click', '.editBtn', function () {
    var featureData = dataTable.row($(this).closest('tr')).data();
    $('#name_edit').val(featureData.name);
    $('#description_edit').val(featureData.description);
    $('#x_position_edit').val(featureData.x_position);
    $('#y_position_edit').val(featureData.y_position);

    if (featureData.image) {
      $('#current-image').html(`
        <label class="form-label">Gambar Saat Ini:</label><br>
        <img src="${featureData.image}" alt="Current Image"
             style="width:100px;height:100px;object-fit:cover;" class="rounded">
      `);
    } else {
      $('#current-image').empty();
    }

    const editForm = $('#editFeatureForm');
    const baseEditUrl = "{{ route('admin.features.update', ['motor' => $motor->id, 'id' => 'DUMMY']) }}".replace('DUMMY', featureData.id);
    editForm.attr('action', baseEditUrl);

    // buka modal -> init picker setelah tampil
    $('#editModal').one('shown.bs.modal', function(){ initFeaturePicker(this); }).modal('show');
  });

  // ========== DELETE ==========
  $(document).on('click', '.deleteBtn', function () {
    const id = $(this).data('id');
    const name = $(this).data('name');
    $('#delete_feature_name').text(name);
    const form = $('#deleteForm');
    const baseUrl = "{{ route('admin.features.delete', ['motor' => $motor->id, 'id' => 'DUMMY']) }}".replace('DUMMY', id);
    form.attr('action', baseUrl);
    $('#deleteModal').modal('show');
  });

  // ========== VIEW IMAGE ==========
  $(document).on('click', '.image-preview', function () {
    $('#modalImage').attr('src', $(this).data('image'));
    $('#imageTitle').text($(this).data('title'));
    $('#viewImageModal').modal('show');
  });

  // Reset form saat modal ditutup
  $('.modal').on('hidden.bs.modal', function () {
    this.querySelector('form')?.reset();
    $('#current-image').empty();
    const dot = this.querySelector('#pickerDot');
    if (dot) dot.style.display = 'none';
  });

  // ========== PICKER: klik gambar -> muncul titik + isi X/Y (persen) ==========
  // init saat modal Tambah dibuka
  $('#addModal').on('shown.bs.modal', function(){ initFeaturePicker(this); });
  // init saat modal Edit dibuka (untuk kasus open tanpa klik editBtn lebih dulu)
  $('#editModal').on('shown.bs.modal', function(){ initFeaturePicker(this); });

  function initFeaturePicker(modalEl){
    const picker = modalEl.querySelector('#picker');
    const img    = modalEl.querySelector('#pickerImage');   // gambar panggung
    const dot    = modalEl.querySelector('#pickerDot');     // titik merah
    const xInp   = modalEl.querySelector('input[name="x_position"]');
    const yInp   = modalEl.querySelector('input[name="y_position"]');
    if(!picker || !img || !dot || !xInp || !yInp) return;

    picker.style.cursor = 'crosshair';
    img.style.display = 'block';

    // pastikan tidak double-bind
    if (img._pickerClick) img.removeEventListener('click', img._pickerClick);

    img._pickerClick = function(e){
      const rect = img.getBoundingClientRect();
      // hitung persen posisi klik terhadap IMG
      const x = ((e.clientX - rect.left) / rect.width)  * 100;
      const y = ((e.clientY - rect.top)  / rect.height) * 100;

      const xVal = Math.max(0, Math.min(100, x)).toFixed(2);
      const yVal = Math.max(0, Math.min(100, y)).toFixed(2);

      xInp.value = xVal;
      yInp.value = yVal;

      // tampilkan titik tepat di posisi klik
      dot.style.left = xVal + '%';
      dot.style.top  = yVal + '%';
      dot.style.transform = 'translate(-50%, -50%)';
      dot.style.display   = 'block';
    };

    // pasang listener setelah img siap (biar rect.width > 0)
    if (img.complete && img.naturalWidth) {
      img.addEventListener('click', img._pickerClick);
    } else {
      img.addEventListener('load', () => img.addEventListener('click', img._pickerClick), { once:true });
    }

    // jika mode edit & sudah ada nilai, render titiknya
    if (xInp.value && yInp.value) {
      dot.style.left = xInp.value + '%';
      dot.style.top  = yInp.value + '%';
      dot.style.transform = 'translate(-50%, -50%)';
      dot.style.display   = 'block';
    } else {
      dot.style.display = 'none';
    }
  }

});
</script>
@endsection