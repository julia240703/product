@extends('layouts.appAdmin')
<title>Kelola Aksesoris (General)</title>

@section('content')
<div class="container-fluid">
  <div class="title-wrapper pt-30">
    <div class="row align-items-center">
      <div class="col-md-6">
        <div class="title mb-30">
          <h2>Data Aksesoris (General)</h2>
        </div>
      </div>
      <div class="col-md-6">
        <div class="breadcrumb-wrapper mb-30">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="#0">Admin</a></li>
              <li class="breadcrumb-item active" aria-current="page">General Accessory</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>

  <button type="button" class="btn btn-success mb-3 btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
    Tambah Aksesoris
  </button>

  {{-- ========================= Modal Tambah ========================= --}}
  <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addAccessoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addAccessoryLabel">Tambah Aksesoris (General)</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <form method="POST" action="{{ route('admin.accessories.general.store') }}" enctype="multipart/form-data" id="addAccessoryForm">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <label for="name" class="form-label">Nama Aksesoris <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" required>
              </div>
              <div class="col-md-6">
                <label for="cover_image" class="form-label">Cover Image</label>
                <input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/*">
              </div>

              <div class="col-md-6">
                <label for="part_number" class="form-label">Part Number</label>
                <input type="text" class="form-control" id="part_number" name="part_number">
              </div>
              <div class="col-md-6">
                <label for="material" class="form-label">Material</label>
                <input type="text" class="form-control" id="material" name="material">
              </div>

              <div class="col-md-6">
                <label for="dimension" class="form-label">Dimensi</label>
                <input type="text" class="form-control" id="dimension" name="dimension">
              </div>
              <div class="col-md-6">
                <label for="weight" class="form-label">Berat (gram)</label>
                <input type="number" step="0.01" class="form-control" id="weight" name="weight">
              </div>

              <div class="col-md-6">
                <label for="color" class="form-label">Warna</label>
                <input type="text" class="form-control" id="color" name="color">
              </div>
              <div class="col-md-6">
                <label for="stock" class="form-label">Stok</label>
                <input type="number" class="form-control" id="stock" name="stock" min="0" value="0">
              </div>

              <div class="col-12">
                <label for="description" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="description" name="description"></textarea>
              </div>

              {{-- Varian: teks panjang --}}
              <div class="col-12">
                <label for="variant" class="form-label">Varian (teks)</label>
                <textarea class="form-control" id="variant" name="variant" placeholder="Contoh: All Size (BeAT Sporty, Scoopy, Vario 125...), atau jelaskan sesuai kebutuhan"></textarea>
              </div>
            </div>

            <hr class="my-3">

            {{-- ===== GALLERY ===== --}}
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

  {{-- ========================= Modal Edit ========================= --}}
  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editAccessoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editAccessoryLabel">Ubah Aksesoris (General)</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <form id="editAccessoryForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-3">
              <div class="col-md-6">
                <label for="name_edit" class="form-label">Nama Aksesoris <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name_edit" name="name" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Cover Image (baru)</label>
                <input type="file" class="form-control" name="cover_image" accept="image/*">
                <div id="current-cover" class="mt-2"></div>
              </div>

              <div class="col-md-6">
                <label class="form-label" for="part_number_edit">Part Number</label>
                <input type="text" class="form-control" id="part_number_edit" name="part_number">
              </div>
              <div class="col-md-6">
                <label class="form-label" for="material_edit">Material</label>
                <input type="text" class="form-control" id="material_edit" name="material">
              </div>

              <div class="col-md-6">
                <label class="form-label" for="dimension_edit">Dimensi</label>
                <input type="text" class="form-control" id="dimension_edit" name="dimension">
              </div>
              <div class="col-md-6">
                <label class="form-label" for="weight_edit">Berat (gram)</label>
                <input type="number" step="0.01" class="form-control" id="weight_edit" name="weight">
              </div>

              <div class="col-md-6">
                <label class="form-label" for="color_edit">Warna</label>
                <input type="text" class="form-control" id="color_edit" name="color">
              </div>
              <div class="col-md-6">
                <label class="form-label" for="stock_edit">Stok</label>
                <input type="number" class="form-control" id="stock_edit" name="stock" min="0" value="0">
              </div>

              <div class="col-12">
                <label class="form-label" for="description_edit">Deskripsi</label>
                <textarea class="form-control" id="description_edit" name="description"></textarea>
              </div>

              {{-- Varian: teks panjang --}}
              <div class="col-12">
                <label class="form-label" for="variant_edit">Varian (teks)</label>
                <textarea class="form-control" id="variant_edit" name="variant"></textarea>
              </div>
            </div>

            <hr class="my-3">

            {{-- ====== Gallery saat ini (preview + hapus) ====== --}}
            <div class="mb-2 d-flex align-items-center justify-content-between">
              <h6 class="mb-0">Gallery Saat Ini</h6>
              <small class="text-muted">Klik gambar untuk perbesar.</small>
            </div>
            <div id="current-gallery" class="row g-2 mb-3"></div>

            {{-- ===== Gallery tambahan (Edit) ===== --}}
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

  {{-- ========================= Modal Hapus ========================= --}}
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteAccessoryLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form id="deleteForm" method="POST">
        @csrf
        @method('DELETE')
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteAccessoryLabel">Konfirmasi Hapus Aksesoris</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
          </div>
          <div class="modal-body">
            <p>Apakah kamu yakin ingin menghapus aksesoris <strong id="delete_accessory_name"></strong>?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- ========================= Modal View Image ========================= --}}
  <div class="modal fade" id="viewImageModal" tabindex="-1" aria-hidden="true">
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

  {{-- ========================= Table ========================= --}}
  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="display" id="accessory-table" style="width:100%">
          <thead>
          <tr>
            <th>#</th>
            <th>Nama Aksesoris</th>
            <th>Cover</th>
            <th>Varian</th>
            <th>Aksi</th>
          </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>

  {{-- ========================= Script ========================= --}}
  <script>
    $(document).ready(function () {

      // CSRF untuk AJAX
      $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

      // ================== DATATABLE ==================
      var dataTable = $('#accessory-table').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.accessories.general.data') }}",
        columns: [
          {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
          {data: 'name', name: 'name'},
          {
            data: 'cover_image',
            name: 'cover_image',
            className: "text-center",
            render: function (data, type, row) {
              return data
                ? `<img src="${data}" style="width:50px;height:50px;object-fit:cover;cursor:pointer;" class="rounded image-preview" data-image="${data}" data-title="${row.name || ''}">`
                : '<span class="text-muted">-</span>';
            }
          },
          {
            data: 'variant',
            name: 'variant',
            render: function (d) {
              if (!d) return '<span class="text-muted">-</span>';
              const txt = String(d).replace(/<[^>]*>?/gm, '');
              return txt.length > 80 ? txt.substring(0, 80) + '…' : txt;
            }
          },
          {
            data: null,
            orderable: false,
            searchable: false,
            render: function (data) {
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
        ],
        error: function (xhr) {
          console.error('DataTables AJAX error:', xhr?.responseText || xhr);
        }
      });

      // ================== EDIT ==================
      $(document).on('click', '.editBtn', function () {
        const id = $(this).data('id');

        const editUrl   = "{{ route('admin.accessories.general.edit', ':id') }}".replace(':id', id);
        const updateUrl = "{{ route('admin.accessories.general.update', ':id') }}".replace(':id', id);

        $.get(editUrl, function (res) {
          $('#editAccessoryForm').attr('action', updateUrl);

          $('#name_edit').val(res.name);
          $('#part_number_edit').val(res.part_number ?? '');
          $('#dimension_edit').val(res.dimension ?? '');
          $('#weight_edit').val(res.weight ?? '');
          $('#color_edit').val(res.color ?? '');
          $('#material_edit').val(res.material ?? '');
          $('#stock_edit').val(res.stock ?? 0);
          $('#description_edit').val(res.description ?? '');
          $('#variant_edit').val(res.variant ?? '');

          // cover saat ini
          if (res.cover_image) {
            $('#current-cover').html(`
              <label class="form-label">Cover Saat Ini:</label><br>
              <img src="/storage/${res.cover_image}" style="width: 100px; height: 100px; object-fit: cover;" class="rounded image-preview" data-image="/storage/${res.cover_image}" data-title="${res.name || ''}">
            `);
          } else {
            $('#current-cover').empty();
          }

          // gallery saat ini (dengan tombol hapus)
          $('#current-gallery').empty();
          const delUrlTpl = "{{ route('admin.accessories.general.images.delete', ':id') }}";

          if (Array.isArray(res.images) && res.images.length) {
            res.images.forEach(function (im) {
              const url   = '/storage/' + im.image;
              const title = im.caption || (res.name || '');
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

      // ================== HAPUS SATU GAMBAR GALLERY ==================
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

      // ================== DELETE ACCESSORY ==================
      $(document).on('click', '.deleteBtn', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');

        const deleteUrl = "{{ route('admin.accessories.general.delete', ':id') }}".replace(':id', id);

        $('#delete_accessory_name').text(name);
        $('#deleteForm').attr('action', deleteUrl);
        $('#deleteModal').modal('show');
      });

      // ================== VIEW IMAGE (preview) ==================
      $(document).on('click', '.image-preview', function () {
        $('#modalImage').attr('src', $(this).data('image'));
        $('#imageTitle').text($(this).data('title'));
        $('#viewImageModal').modal('show');
      });

      // ================== RESET FORM (scoped!) ==================
      $('#addModal').on('hidden.bs.modal', function () {
        this.querySelector('form')?.reset();
      });
      $('#editModal').on('hidden.bs.modal', function () {
        this.querySelector('form')?.reset();
        $('#current-cover').empty();
        $('#current-gallery').empty();
      });
    });
  </script>
</div>
@endsection