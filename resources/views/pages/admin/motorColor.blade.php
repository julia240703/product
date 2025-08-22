@extends('layouts.appAdmin')
<title>Kelola Warna Motor</title>

@section('content')
<div class="container-fluid">
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title mb-30">
                    <h2>Data Warna Motor</h2>
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
                                Motor Color
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <button type="button" class="btn btn-success mb-3 btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
        Tambah Warna
    </button>

    <!-- Modal Tambah -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addColorLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.colors.store', $motorModel->id) }}" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addColorLabel">Tambah Warna Motor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="color_code" class="form-label">Kode Warna (Hex) <span class="text-red">*</span></label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="color_code" name="color_code" value="#000000" title="Pilih warna" required style="width: 50px;" onchange="document.getElementById('color_code_text').value=this.value;">
                            <input type="text" class="form-control" id="color_code_text" name="color_code" value="#000000" required placeholder="Masukkan Hex (e.g., #FF0000)" style="flex-grow: 1;" oninput="document.getElementById('color_code').value=this.value;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Gambar Warna</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                    <button type="submit" class="btn btn-success">Tambahkan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editColorLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editColorForm" method="POST" action="" enctype="multipart/form-data" class="modal-content">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="color_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="editColorLabel">Ubah Data Warna Motor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="color_code_edit" class="form-label">Kode Warna (Hex) <span class="text-red">*</span></label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="color_code_edit" name="color_code" value="#000000" title="Pilih warna" required style="width: 50px;" onchange="document.getElementById('color_code_text_edit').value=this.value;">
                            <input type="text" class="form-control" id="color_code_text_edit" name="color_code" value="#000000" required placeholder="Masukkan Hex (e.g., #FF0000)" style="flex-grow: 1;" oninput="document.getElementById('color_code_edit').value=this.value;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="image_edit" class="form-label">Gambar Warna</label>
                        <input type="file" class="form-control" id="image_edit" name="image" accept="image/*" />
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar</small>
                        <div class="mt-2" id="currentImageContainer" style="display:none;">
                            <label>Gambar Saat Ini:</label><br/>
                            <img src="" id="currentImage" style="width: 100px; height: auto; object-fit: contain;" alt="Gambar Warna" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                    <button type="submit" class="btn btn-success">Ubah</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Hapus -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteColorLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="deleteForm" method="POST" action="" class="modal-content">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteColorLabel">Konfirmasi Hapus Warna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah kamu yakin ingin menghapus warna <strong id="delete_color_code"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
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
                <table class="display" id="color-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kode Warna</th>
                            <th>Gambar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
$(document).ready(function() {
    var dataTable = $('#color-table').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.colors.index', $motorModel->id) }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { 
                data: 'color_code', 
                name: 'color_code',
                render: function(data) {
                    return `<div style="display:flex; align-items:center;">
                                <div style="width:15px; height:15px; background-color:${data}; border:1px solid #ccc; margin-right:8px;"></div>
                                <span>${data}</span>
                            </div>`;
                }
            },
            { 
                data: 'image', 
                name: 'image', 
                orderable: false,
                searchable: false,
                className: "text-center",
                render: function(data, type, row) {
                    return data 
                        ? `<img src="${data}" alt="Gambar Warna" style="width:50px; height:50px; object-fit:cover; cursor:pointer;" class="rounded image-preview" data-image="${data}" data-title="${row.color_code || ''}">`
                        : '<span class="text-muted">Tidak ada gambar</span>';
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                className: "text-center",
                render: function(data) {
                    return `
                        <div class="btn-group">
                            <button class="btn btn-sm btn-primary me-1 editBtn" data-id="${data.id}">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button class="btn btn-sm btn-danger deleteBtn" data-id="${data.id}" data-code="${data.color_code}">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>`;
                }
            }
        ]
    });

    // View image
    $(document).on('click', '.image-preview', function() {
        $('#modalImage').attr('src', $(this).data('image'));
        $('#imageTitle').text($(this).data('title'));
        $('#viewImageModal').modal('show');
    });

    // Edit button click
    $(document).on('click', '.editBtn', function() {
        var colorData = dataTable.row($(this).closest('tr')).data();
        $('#color_id').val(colorData.id);
        $('#color_code_edit').val(colorData.color_code);
        $('#color_code_text_edit').val(colorData.color_code);
        if (colorData.image) {
            $('#currentImageContainer').show();
            $('#currentImage').attr('src', colorData.image);
        } else {
            $('#currentImageContainer').hide();
            $('#currentImage').attr('src', '');
        }
        const editForm = $('#editColorForm');
        const baseEditUrl = "{{ route('admin.colors.update', ['motor' => $motorModel->id, 'id' => 'DUMMY']) }}".replace('DUMMY', colorData.id);
        editForm.attr('action', baseEditUrl);
        $('#editModal').modal('show');
    });

    // Delete button click
    $(document).on('click', '.deleteBtn', function() {
        const id = $(this).data('id');
        const code = $(this).data('code');
        $('#delete_color_code').text(code);
        const form = $('#deleteForm');
        const baseUrl = "{{ route('admin.colors.delete', ['motor' => $motorModel->id, 'id' => 'DUMMY']) }}".replace('DUMMY', id);
        form.attr('action', baseUrl);
        $('#deleteModal').modal('show');
    });

    // Reset form saat modal ditutup
    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('form')[0]?.reset();
        $('#currentImageContainer').hide();
        $('#currentImage').attr('src', '');
    });
});
</script>
@endsection