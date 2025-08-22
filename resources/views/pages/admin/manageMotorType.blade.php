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
                            <li class="breadcrumb-item">
                                <a href="#0">Admin</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Motor Type
                            </li>
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
                    <form id="addMotorTypeForm" method="POST" action="{{ route('admin.motor-type.store') }}">
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
                    <form id="editForm">
                        <input type="hidden" id="motorTypeId">
                        <div class="mb-3">
                            <label for="nama_edit" class="form-label">Nama Motor <span class="text-red">*</span></label>
                            <input class="form-control" id="nama_edit" name="nama_edit" required />
                        </div>
                        <div class="mb-3">
                            <label for="kategori_id_edit" class="form-label">Tipe (Kategori) <span class="text-red">*</span></label>
                            <select class="form-select" id="kategori_id_edit" name="kategori_id_edit" required>
                                <option value="">Pilih Tipe</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
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

    <!-- Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="display" id="motor-type-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
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
    var dataTable = $('#motor-type-table').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.motor-type.data') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'tipe', name: 'tipe' },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data) {
                    return `
                        <button class="btn btn-sm btn-primary editBtn"
                            data-id="${data.id}"
                            data-name="${data.name}"
                            data-category_id="${data.category_id}">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deleteBtn"
                            data-id="${data.id}"
                            data-name="${data.name}">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        error: function (xhr, error, code) {
            console.log(xhr.responseText);
            alert("Ajax Error: " + xhr.status + " " + xhr.statusText);
        }
    });

    // Edit button
        $(document).on('click', '.editBtn', function() {
        var motorData = dataTable.row($(this).closest('tr')).data();
        $('#motorTypeId').val(motorData.id);
        $('#nama_edit').val(motorData.name);
        $('#kategori_id_edit').val(motorData.category_id);
        $('#editModal').modal('show');
    });

    // Update via Ajax
    $('#updateBtn').on('click', function() {
        $.ajax({
            url: "{{ route('admin.motor-type.update') }}",
            method: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id: $('#motorTypeId').val(),
                nama: $('#nama_edit').val(),
                kategori_id: $('#kategori_id_edit').val(),
            },
            success: function(response) {
                $('#editModal').modal('hide');
                dataTable.ajax.reload();
            },
            error: function(xhr) {
                alert("Update error: " + xhr.statusText);
            }
        });
    });

    // Delete button
    $(document).on('click', '.deleteBtn', function() {
        const id = $(this).data('id');
        const name = $(this).closest('tr').find('td:eq(1)').text();
        $('#delete_motor_type_id').val(id);
        $('#delete_motor_type_name').text(name);
        $('#deleteModal').modal('show');
        $('#deleteModal form').off('submit').on('submit', function() {
            $(this).find('button[type="submit"]').prop('disabled', true);
        });
    });
});
</script>
@endsection