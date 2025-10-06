@extends('layouts.appAdmin')
<title>Kelola Apparel</title>

@section('content')
<div class="container-fluid">
  <div class="title-wrapper pt-30">
    <div class="row align-items-center">
      <div class="col-md-6">
        <div class="title mb-30">
          <h2>Data Semua Apparel</h2>
        </div>
      </div>
      <div class="col-md-6">
        <div class="breadcrumb-wrapper mb-30">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="#0">Admin</a></li>
              <li class="breadcrumb-item active" aria-current="page">Apparel</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>

  <!-- Tombol Tambah -->
  <button type="button" class="btn btn-success mb-3 btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
    Tambah Apparel
  </button>

  <!-- ============== Modal Tambah ============== -->
  <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addModalLabel">Tambah Apparel</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <form id="addForm" method="POST" action="{{ route('admin.apparels.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Nama Apparel <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name_apparel" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                <select class="form-select" name="category_id" id="category_id" required>
                  @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                  @endforeach
                </select>
              </div>

              <!-- Cover & Preview (sejajar) -->
              <div class="col-md-6">
                <label class="form-label">Cover Image</label>
                <input type="file" class="form-control" name="cover_image" accept="image/*">
              </div>
              <div class="col-md-6">
                <label class="form-label d-flex align-items-center gap-2">
                  <span>Preview Cover</span>
                  <small class="text-muted">(kosong = belum dipilih)</small>
                </label>
                <div class="border rounded d-flex align-items-center justify-content-center p-2" style="height:120px;">
                  <span class="text-muted small">Belum ada cover</span>
                </div>
              </div>

              <div class="col-12">
                <label class="form-label">Deskripsi</label>
                <textarea class="form-control" name="description"></textarea>
              </div>

              <div class="col-md-6">
                <label class="form-label">Material</label>
                <textarea class="form-control" name="material"></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label">Dimensi</label>
                <input type="text" class="form-control" name="dimensions">
              </div>

              <div class="col-md-4">
                <label class="form-label">Berat (gram)</label>
                <input type="number" class="form-control" name="weight">
              </div>
              <div class="col-md-4">
                <label class="form-label">Warna</label>
                <input type="text" class="form-control" name="color">
              </div>
              <div class="col-md-4">
                <label class="form-label">Ukuran</label>
                <input type="text" class="form-control" name="size">
              </div>

              <div class="col-md-6">
                <label class="form-label">Nomor Part</label>
                <input type="text" class="form-control" name="part_number">
              </div>
              <div class="col-md-6">
                <label class="form-label">Stok</label>
                <input type="number" class="form-control" name="stock" min="0" value="0">
              </div>

              <!-- NEW -->
              <div class="col-12 mt-2">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="add_is_new" name="is_new" value="1">
                  <label class="form-check-label" for="add_is_new">Tandai sebagai <strong>NEW</strong></label>
                </div>
              </div>
            </div>

            <hr class="my-3">

            <!-- Gallery multiple -->
            <div class="mb-2 d-flex align-items-center justify-content-between">
              <h6 class="mb-0">Gambar Tambahan (Gallery)</h6>
              <small class="text-muted">Boleh pilih banyak file sekaligus.</small>
            </div>
            <input type="file" name="gallery[]" multiple class="form-control" accept="image/*">

            <div class="modal-footer mt-3">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
              <button type="submit" class="btn btn-success">Tambahkan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- ============== Modal Edit ============== -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Ubah Apparel</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" id="apparelId" name="id">

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Nama Apparel <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name_apparel_edit" name="name_apparel" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                <select name="category_id" id="category_id_edit" class="form-select" required>
                  @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                  @endforeach
                </select>
              </div>

              <!-- Cover & Preview (sejajar) -->
              <div class="col-md-6">
                <label class="form-label">Cover Image (baru)</label>
                <input type="file" class="form-control" name="cover_image" accept="image/*">
              </div>
              <div class="col-md-6">
                <label class="form-label">Cover Saat Ini:</label>
                <div id="current-cover-box" class="border rounded d-flex align-items-center justify-content-center p-2" style="height:120px;">
                  <span class="text-muted small">Tidak ada cover</span>
                </div>
              </div>

              <div class="col-12">
                <label class="form-label">Deskripsi</label>
                <textarea class="form-control" name="description" id="description_edit"></textarea>
              </div>

              <div class="col-md-6">
                <label class="form-label">Material</label>
                <textarea class="form-control" name="material" id="material_edit"></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label">Dimensi</label>
                <input type="text" class="form-control" name="dimensions" id="dimensions_edit">
              </div>

              <div class="col-md-4">
                <label class="form-label">Berat (gram)</label>
                <input type="number" class="form-control" name="weight" id="weight_edit">
              </div>
              <div class="col-md-4">
                <label class="form-label">Warna</label>
                <input type="text" class="form-control" name="color" id="color_edit">
              </div>
              <div class="col-md-4">
                <label class="form-label">Ukuran</label>
                <input type="text" class="form-control" name="size" id="size_edit">
              </div>

              <div class="col-md-6">
                <label class="form-label">Nomor Part</label>
                <input type="text" class="form-control" name="part_number" id="part_number_edit">
              </div>
              <div class="col-md-6">
                <label class="form-label">Stok</label>
                <input type="number" class="form-control" name="stock" id="stock_edit" min="0" value="0">
              </div>

              <!-- NEW -->
              <div class="col-12 mt-2">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="edit_is_new" name="is_new" value="1">
                  <label class="form-check-label" for="edit_is_new">Tandai sebagai <strong>NEW</strong></label>
                </div>
              </div>
            </div>

            <hr class="my-3">

            <!-- Gallery saat ini (preview + hapus) -->
            <div class="mb-2 d-flex align-items-center justify-content-between">
              <h6 class="mb-0">Gallery Saat Ini</h6>
              <small class="text-muted">Klik gambar untuk perbesar.</small>
            </div>
            <div id="current-gallery" class="row g-2 mb-3"></div>

            <!-- Tambah gallery -->
            <div class="mb-2 d-flex align-items-center justify-content-between">
              <h6 class="mb-0">Tambah Gambar Gallery</h6>
              <small class="text-muted">Pilih beberapa file untuk menambah.</small>
            </div>
            <input type="file" name="gallery[]" multiple class="form-control" accept="image/*">

            <div class="modal-footer mt-3">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
              <button type="submit" class="btn btn-success">Ubah</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- ============== Modal Hapus Apparel ============== -->
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form method="POST" id="deleteForm">
        @csrf
        @method('DELETE')
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Konfirmasi Hapus Apparel</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
          </div>
          <div class="modal-body">
            <p>Apakah kamu yakin ingin menghapus <strong id="delete_apparel_name"></strong>?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- ============== Modal View Image ============== -->
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
            <p id="imageCategory" class="text-muted"></p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ============== DataTables ============== -->
  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="display" id="apparel-table" style="width:100%">
          <thead>
            <tr>
              <th>#</th>
              <th>Nama Apparel</th>
              <th>Gambar</th>
              <th>Nomor Part</th>
              <th>Aksi</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function () {

  $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

  // ================== DATATABLE ==================
  var dataTable = $('#apparel-table').DataTable({
    responsive: true,
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.apparels.data') }}",
    columns: [
      { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
      { data: 'name_apparel', name: 'name_apparel' },
      { data: 'image', name: 'image', className: "text-center", orderable: false, searchable: false },
      { data: 'part_number', name: 'part_number' },
      {
        data: null,
        className: "text-center",
        orderable: false,
        searchable: false,
        render: function (data) {
          return `
            <div class="btn-group">
              <button class="btn btn-sm btn-primary me-1 rounded editBtn" data-id="${data.id}">
                <i class="fa fa-edit"></i>
              </button>
              <button class="btn btn-sm btn-danger rounded deleteBtn" data-id="${data.id}" data-name="${data.name_apparel}">
                <i class="fa fa-trash"></i>
              </button>
            </div>
          `;
        }
      }
    ],
    error: function (xhr) {
      console.error('DataTables AJAX error:', xhr?.responseText || xhr);
    }
  });

  // ================== EDIT (GET JSON) ==================
  $(document).on('click', '.editBtn', function () {
    const id = $(this).data('id');
    const editUrl   = "{{ route('admin.apparels.edit', ':id') }}".replace(':id', id);
    const updateUrl = "{{ route('admin.apparels.update', ':id') }}".replace(':id', id);

    $.get(editUrl, function (res) {
      $('#editForm').attr('action', updateUrl);
      $('#apparelId').val(res.id);

      $('#name_apparel_edit').val(res.name_apparel || '');
      $('#category_id_edit').val(res.category_id || '');
      $('#description_edit').val(res.description || '');
      $('#material_edit').val(res.material || '');
      $('#dimensions_edit').val(res.dimensions || '');
      $('#weight_edit').val(res.weight || '');
      $('#color_edit').val(res.color || '');
      $('#size_edit').val(res.size || '');
      $('#part_number_edit').val(res.part_number || '');
      $('#stock_edit').val(res.stock ?? 0);

      // NEW checkbox
      $('#edit_is_new').prop('checked', !!res.is_new);

      // Cover saat ini di kolom kanan
      if (res.cover_image) {
        $('#current-cover-box').html(`
          <img src="/storage/${res.cover_image}"
               style="max-height:100%; max-width:100%; object-fit:contain; cursor:pointer"
               class="image-preview"
               data-image="/storage/${res.cover_image}"
               data-title="${res.name_apparel || ''}">
        `);
      } else {
        $('#current-cover-box').html('<span class="text-muted small">Tidak ada cover</span>');
      }

      // Gallery saat ini (dengan tombol hapus)
      $('#current-gallery').empty();
      const delUrlTpl = "{{ route('admin.apparels.images.delete', ':id') }}";

      if (Array.isArray(res.images) && res.images.length) {
        res.images.forEach(function (im) {
          const url    = '/storage/' + im.image;
          const title  = im.caption || (res.name_apparel || '');
          const delUrl = delUrlTpl.replace(':id', im.id);

          $('#current-gallery').append(`
            <div class="col-6 col-md-3 col-lg-2 gallery-item" data-id="${im.id}">
              <div class="position-relative border rounded overflow-hidden">
                <button type="button"
                        class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 btn-del-gallery"
                        data-url="${delUrl}" title="Hapus gambar" aria-label="Hapus gambar">
                  <i class="fa-solid fa-xmark"></i>
                </button>
                <img src="${url}"
                     class="img-fluid image-preview"
                     style="width:100%;height:100%;object-fit:cover;cursor:pointer"
                     data-image="${url}"
                     data-title="${title}">
              </div>
            </div>
          `);
        });
      } else {
        $('#current-gallery').html('<div class="text-muted small">Belum ada gambar gallery.</div>');
      }

      $('#editModal').modal('show');
    }).fail(function (xhr) {
      console.error('Gagal ambil data:', xhr?.responseText || xhr);
      alert('Gagal memuat data. Coba lagi.');
    });
  });

  // ================== HAPUS 1 GAMBAR GALLERY ==================
  $(document).on('click', '.btn-del-gallery', function (e) {
    e.preventDefault();
    e.stopPropagation();

    const $btn = $(this);
    const url  = $btn.data('url');

    if (!confirm('Hapus gambar ini dari gallery?')) return;

    const original = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

    $.ajax({
      url: url,
      type: 'POST',
      data: { _method: 'DELETE' },
      success: function () {
        $btn.closest('.gallery-item').remove();
        if ($('#current-gallery .gallery-item').length === 0) {
          $('#current-gallery').html('<div class="text-muted small">Belum ada gambar gallery.</div>');
        }
      },
      error: function (xhr) {
        alert('Gagal menghapus gambar. Coba lagi.');
        console.error(xhr?.responseText || xhr);
        $btn.prop('disabled', false).html(original);
      }
    });
  });

  // ================== DELETE APPAREL ==================
  $(document).on('click', '.deleteBtn', function () {
    const id = $(this).data('id');
    const name = $(this).data('name');

    const deleteUrl = "{{ route('admin.apparels.delete', ':id') }}".replace(':id', id);

    $('#delete_apparel_name').text(name);
    $('#deleteForm').attr('action', deleteUrl);
    $('#deleteModal').modal('show');
  });

  // ================== VIEW IMAGE (preview) ==================
  $(document).on('click', '.image-preview', function () {
    const imageSrc = $(this).data('image');
    const title    = $(this).data('title') || '';
    $('#modalImage').attr('src', imageSrc);
    $('#imageTitle').text(title);
    $('#viewImageModal').modal('show');
  });

  // ================== RESET FORM ==================
  $('#addModal').on('hidden.bs.modal', function () {
    this.querySelector('form')?.reset();
    $('#add_is_new').prop('checked', false);
  });
  $('#editModal').on('hidden.bs.modal', function () {
    this.querySelector('form')?.reset();
    $('#edit_is_new').prop('checked', false);
    $('#current-cover-box').html('<span class="text-muted small">Tidak ada cover</span>');
    $('#current-gallery').empty();
  });
});
</script>
@endsection