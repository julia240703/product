@extends('layouts.appAdmin')
<title>Kelola Tipe Motor</title>

@section('content')
<div class="container-fluid">
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title mb-30">
                    <h2>Data Tipe Motor</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="breadcrumb-wrapper mb-30">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#0">Admin</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Motor Type</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Tombol Tambah -->
    <button type="button" class="btn btn-success mb-3 btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">Tambah Tipe</button>

    <!-- Modal Tambah -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Tambah Tipe Motor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addMotorTypeForm" method="POST" action="{{ route('admin.motor-type.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Motor <span class="text-red">*</span></label>
                            <input class="form-control" id="nama" name="name" required />
                        </div>

                        <div class="mb-3">
                            <label for="kategori_id" class="form-label">Tipe (Kategori) <span class="text-red">*</span></label>
                            <select class="form-select" id="kategori_id" name="category_id" required>
                                <option value="">Pilih Tipe</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Cover Image</label>
                            <input type="file" class="form-control" name="cover_image" id="cover_add" accept="image/*">
                            <div class="small text-muted mt-1">jpg/jpeg/png/webp, maks 2MB.</div>
                            <div class="mt-2">
                                <img id="preview_add" src="{{ asset('no-image.png') }}" alt="Preview" style="height:64px;border-radius:8px">
                            </div>
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
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Ubah Data Tipe Motor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="motorTypeId" name="id">

                        <div class="mb-3">
                            <label for="nama_edit" class="form-label">Nama Motor <span class="text-red">*</span></label>
                            <input class="form-control" id="nama_edit" name="name" required />
                        </div>

                        <div class="mb-3">
                            <label for="kategori_id_edit" class="form-label">Tipe (Kategori) <span class="text-red">*</span></label>
                            <select class="form-select" id="kategori_id_edit" name="category_id" required>
                                <option value="">Pilih Tipe</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Cover Image (opsional ganti)</label>
                            <input type="file" class="form-control" name="cover_image" id="cover_edit" accept="image/*">
                            <div class="small text-muted mt-1">Biarkan kosong jika tidak diganti.</div>
                            <div class="mt-2">
                                <img id="preview_edit" src="{{ asset('no-image.png') }}" alt="Preview" style="height:64px;border-radius:8px">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                    <button type="button" class="btn btn-success" id="updateBtn">Ubah</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Hapus -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.motor-type.delete') }}">
                @csrf
                @method('DELETE')
                <input type="hidden" name="id" id="delete_motor_type_id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Konfirmasi Hapus Tipe</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah kamu yakin ingin menghapus tipe <strong id="delete_motor_type_name"></strong>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal View Image (Preview Cover) -->
    <div class="modal fade" id="viewImageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Lihat Gambar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Preview" class="img-fluid rounded" style="max-height:500px;">
                    <div class="mt-2">
                        <p id="imageTitle" class="mb-0 fw-bold"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="display" id="motor-type-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cover</th>
                            <th>Nama Motor</th>
                            <th>Tipe</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- DataTables Script -->
<script>
$(document).ready(function() {
    const baseStorage = "{{ asset('storage') }}/";
    const noImg = "{{ asset('no-image.png') }}";

    // preview add
    $('#cover_add').on('change', function(e){
        const f = e.target.files?.[0]; if(!f) return;
        const rd = new FileReader();
        rd.onload = ev => $('#preview_add').attr('src', ev.target.result);
        rd.readAsDataURL(f);
    });

    // preview edit
    $('#cover_edit').on('change', function(e){
        const f = e.target.files?.[0]; if(!f) return;
        const rd = new FileReader();
        rd.onload = ev => $('#preview_edit').attr('src', ev.target.result);
        rd.readAsDataURL(f);
    });

    var dataTable = $('#motor-type-table').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.motor-type.data') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },

            // COVER → jika ada tampil gambar (klik untuk preview), jika tidak tampil teks "belum ada cover"
            {
                data: 'cover_image',
                name: 'cover_image',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(data, type, row){
                    if (!data) {
                        return '<span class="text-muted small fst-italic">Belum Ada Cover</span>';
                    }
                    const src = baseStorage + data;
                    const safeTitle = $('<div>').text(row.name || 'Cover').html();
                    return `
                        <img src="${src}"
                             alt="cover"
                             class="img-thumbnail mt-1 image-preview"
                             data-image="${src}"
                             data-title="${safeTitle}"
                             style="height:48px;width:auto;cursor:pointer;border-radius:8px"
                             onerror="this.onerror=null;this.replaceWith('<span class=&quot;text-muted small fst-italic&quot;>belum ada cover</span>');">
                    `;
                }
            },

            { data: 'name', name: 'motor_types.name' },
            { data: 'tipe', name: 'categories.name' },

            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data) {
                    return `
                        <button class="btn btn-sm btn-primary editBtn"
                            data-id="${data.id}"
                            data-name="${$('<div>').text(data.name).html()}"
                            data-category_id="${data.category_id}"
                            data-cover="${data.cover_image || ''}">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deleteBtn"
                            data-id="${data.id}"
                            data-name="${$('<div>').text(data.name).html()}">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        error: function (xhr) {
            console.log(xhr.responseText);
            alert("Ajax Error: " + xhr.status + " " + xhr.statusText);
        }
    });

    // Edit button → isi form + preview
    $(document).on('click', '.editBtn', function() {
        var motorData = dataTable.row($(this).closest('tr')).data();
        $('#motorTypeId').val(motorData.id);
        $('#nama_edit').val(motorData.name);
        $('#kategori_id_edit').val(motorData.category_id);

        const src = motorData.cover_image ? (baseStorage + motorData.cover_image) : noImg;
        $('#preview_edit').attr('src', src);

        $('#editModal').modal('show');
    });

    // Update via Ajax
    $('#updateBtn').on('click', function() {
        const form = document.getElementById('editForm');
        const fd = new FormData(form);
        fd.set('id', $('#motorTypeId').val());
        fd.append('_token', '{{ csrf_token() }}');

        $.ajax({
            url: "{{ route('admin.motor-type.update') }}",
            method: "POST",
            data: fd,
            processData: false,
            contentType: false,
            success: function() {
                $('#editModal').modal('hide');
                dataTable.ajax.reload(null, false);
            },
            error: function(xhr) {
                const r = xhr.responseJSON || {};
                const msg = r.message || xhr.statusText;
                const firstErr = r.errors ? Object.values(r.errors)[0][0] : '';
                alert("Update error: " + msg + (firstErr ? "\n" + firstErr : ""));
            }
        });
    });

    // Delete button
    $(document).on('click', '.deleteBtn', function() {
        const id = $(this).data('id');
        const name = $(this).closest('tr').find('td:eq(2)').text();
        $('#delete_motor_type_id').val(id);
        $('#delete_motor_type_name').text(name);
        $('#deleteModal').modal('show');
        $('#deleteModal form').off('submit').on('submit', function() {
            $(this).find('button[type="submit"]').prop('disabled', true);
        });
    });

    // Klik gambar cover → modal preview
    $(document).on('click', '#motor-type-table .image-preview', function () {
        const src   = $(this).data('image');
        const title = $(this).data('title') || 'Cover';
        $('#modalImage').attr('src', src);
        $('#imageTitle').text(title);
        $('#viewImageModal').modal('show');
    });
});
</script>
@endsection