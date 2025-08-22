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
                            <li class="breadcrumb-item">
                                <a href="#0">Admin</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Apparel
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Tombol Tambah Apparel -->
    <button type="button" class="btn btn-success mb-3 btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
        Tambah Apparel
    </button>

    <!-- Modal Tambah Apparel -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Tambah Apparel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <form id="addForm" method="POST" action="{{ route('admin.apparels.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nama Apparel <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name_apparel" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gambar <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="image" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select" name="category_id" id="category_id" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Dimensi</label>
                            <input type="text" class="form-control" name="dimensions">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Berat (gram)</label>
                            <input type="number" class="form-control" name="weight">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Warna</label>
                            <input type="text" class="form-control" name="color">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ukuran</label>
                            <input type="text" class="form-control" name="size">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nomor Part</label>
                            <input type="text" class="form-control" name="part_number">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">Tambah</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Apparel -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Ubah Apparel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="apparelId" name="id">
                        <div class="mb-3">
                            <label class="form-label">Nama Apparel <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name_apparel_edit" name="name_apparel" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gambar <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="image">
                            <div id="current-image" class="mt-2"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id_edit" class="form-select" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="description" id="description_edit"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Dimensi</label>
                            <input type="text" class="form-control" name="dimensions" id="dimensions_edit">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Berat (gram)</label>
                            <input type="number" class="form-control" name="weight" id="weight_edit">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Warna</label>
                            <input type="text" class="form-control" name="color" id="color_edit">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ukuran</label>
                            <input type="text" class="form-control" name="size" id="size_edit">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nomor Part</label>
                            <input type="text" class="form-control" name="part_number" id="part_number_edit">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="updateBtn">Ubah</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Delete -->
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
                        <p id="imageCategory" class="text-muted"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTables -->
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
$(document).ready(function() {
    var dataTable = $('#apparel-table').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.apparels.data') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name_apparel', name: 'name_apparel' },
            { 
                data: 'image', 
                name: 'image',
                className: "text-center",
                render: function(data, type, row) {
                    if (data) {
                        return `<img src="${data}" alt="Preview" style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;" class="rounded image-preview" data-image="${data}" data-title="${row.name_apparel || ''}" data-category="${row.category || '-'}">`;
                    }
                    return '<span class="text-muted">Tidak ada gambar</span>';
                }
            },
            { data: 'part_number', name: 'part_number' },
            {
                data: null,
                className: "text-center",
                orderable: false,
                searchable: false,
                render: function(data) {
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
        ]
    });

    // Edit
    $(document).on('click', '.editBtn', function() {
        var row = dataTable.row($(this).closest('tr')).data();
        $('#apparelId').val(row.id);
        $('#name_apparel_edit').val(row.name_apparel);
        $('#category_id_edit').val(row.category_id);
        $('#description_edit').val(row.description);
        $('#dimensions_edit').val(row.dimensions);
        $('#weight_edit').val(row.weight);
        $('#color_edit').val(row.color);
        $('#size_edit').val(row.size);
        $('#part_number_edit').val(row.part_number);
        
        if (row.image) {
            $('#current-image').html(`
                <label class="form-label">Gambar Saat Ini:</label><br>
                <img src="${row.image}" alt="Current Image" style="width: 100px; height: 100px; object-fit: cover;" class="rounded">
            `);
        } else {
            $('#current-image').empty();
        }
        $('#editModal').modal('show');
    });

    $('#updateBtn').on('click', function() {
        var formData = new FormData($('#editForm')[0]);
        var id = $('#apparelId').val();

        $.ajax({
            url: '{{ route('admin.apparels.update', ['id' => ':id']) }}'.replace(':id', id),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#editModal').modal('hide');
                dataTable.ajax.reload();
                alert('Apparel berhasil diubah!');
            },
            error: function(xhr) {
                alert('Error mengubah apparel: ' + xhr.responseText);
            }
        });
    });

    // Delete
    $(document).on('click', '.deleteBtn', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');

        $('#delete_apparel_name').text(name);
        $('#deleteForm').attr('action', '{{ route('admin.apparels.delete', ['id' => ':id']) }}'.replace(':id', id));
        $('#deleteModal').modal('show');
    });

    // View Image
    $(document).on('click', '.image-preview', function() {
        const imageSrc = $(this).data('image');
        const title = $(this).data('title');
        const category = $(this).data('category');

        $('#modalImage').attr('src', imageSrc);
        $('#imageTitle').text(title);
        $('#imageCategory').text('Kategori: ' + category);
        $('#viewImageModal').modal('show');
    });
});
</script>
@endsection