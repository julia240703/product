@extends('layouts.appAdmin')
<title>Kelola Sparepart Motor</title>

@section('content')
    <div class="container-fluid">
        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="title mb-30">
                        <h2>Data Sparepart Motor</h2>
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
                                    Motor Sparepart
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-success mb-3 btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
            Tambah Sparepart
        </button>

        <!-- Modal Tambah -->
        <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addSparepartLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addSparepartLabel">Tambah Sparepart Motor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('admin.spareparts.store', $motor->id) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Sparepart <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Gambar <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label for="category" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-control" id="category" name="category" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="electric">Electric</option>
                                    <option value="engine">Engine</option>
                                    <option value="frame">Frame</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Harga <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="price" name="price" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="description" name="description"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="dimension" class="form-label">Dimensi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="dimension" name="dimension" required>
                            </div>
                            <div class="mb-3">
                                <label for="weight" class="form-label">Berat (gram) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="weight" name="weight" required>
                            </div>
                            <div class="mb-3">
                                <label for="part_number" class="form-label">Part Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="part_number" name="part_number" required>
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
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editSparepartLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editSparepartLabel">Ubah Data Sparepart Motor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editSparepartForm" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="name_edit" class="form-label">Nama Sparepart <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name_edit" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="image_edit" class="form-label">Gambar <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="image_edit" name="image" accept="image/*">
                                <div id="current-image" class="mt-2"></div>
                            </div>
                            <div class="mb-3">
                                <label for="category_edit" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-control" id="category_edit" name="category" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="electric">Electric</option>
                                    <option value="engine">Engine</option>
                                    <option value="frame">Frame</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="price_edit" class="form-label">Harga <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="price_edit" name="price" required>
                            </div>
                            <div class="mb-3">
                                <label for="description_edit" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="description_edit" name="description"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="dimension_edit" class="form-label">Dimensi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="dimension_edit" name="dimension" required>
                            </div>
                            <div class="mb-3">
                                <label for="weight_edit" class="form-label">Berat (gram) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="weight_edit" name="weight" required>
                            </div>
                            <div class="mb-3">
                                <label for="part_number_edit" class="form-label">Part Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="part_number_edit" name="part_number" required>
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
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteSparepartLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteSparepartLabel">Konfirmasi Hapus Sparepart</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <p>Apakah kamu yakin ingin menghapus sparepart <strong id="delete_sparepart_name"></strong>?</p>
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
                    <table class="display" id="sparepart-table" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Sparepart</th>
                                <th>Gambar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <!-- Script -->
        <script>
$(document).ready(function() {
    var dataTable = $('#sparepart-table').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.spareparts.index', $motor->id) }}",
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
    $(document).on('click', '.editBtn', function() {
        var sparepartData = dataTable.row($(this).closest('tr')).data();
        $('#name_edit').val(sparepartData.name);
        $('#category_edit').val(sparepartData.category);
        $('#price_edit').val(sparepartData.price);
        $('#description_edit').val(sparepartData.description);
        $('#dimension_edit').val(sparepartData.dimension);
        $('#weight_edit').val(sparepartData.weight);
        $('#part_number_edit').val(sparepartData.part_number);
        
        if (sparepartData.image) {
            $('#current-image').html(`
                <label class="form-label">Gambar Saat Ini:</label><br>
                <img src="${sparepartData.image}" alt="Current Image" style="width: 100px; height: 100px; object-fit: cover;" class="rounded">
            `);
        } else {
            $('#current-image').empty();
        }

        // Set dynamic action for edit form
        const editForm = $('#editSparepartForm');
        const baseEditUrl = "{{ route('admin.spareparts.update', ['motor' => $motor->id, 'id' => 'DUMMY']) }}".replace('DUMMY', sparepartData.id);
        editForm.attr('action', baseEditUrl);

        $('#editModal').modal('show');
    });

    // ========== DELETE ==========
    $(document).on('click', '.deleteBtn', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        $('#delete_sparepart_name').text(name);
        const form = $('#deleteForm');
        const baseUrl = "{{ route('admin.spareparts.delete', ['motor' => $motor->id, 'id' => 'DUMMY']) }}".replace('DUMMY', id);
        form.attr('action', baseUrl);
        $('#deleteModal').modal('show');
    });

    // ========== VIEW IMAGE ==========
    $(document).on('click', '.image-preview', function() {
        $('#modalImage').attr('src', $(this).data('image'));
        $('#imageTitle').text($(this).data('title'));
        $('#viewImageModal').modal('show');
    });

    // Reset form saat modal ditutup
    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('form')[0]?.reset();
        $('#current-image').empty();
    });
});
</script>
@endsection