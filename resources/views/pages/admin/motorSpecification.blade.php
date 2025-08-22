@extends('layouts.appAdmin')
<title>Kelola Spesifikasi Motor</title>

@section('content')
<div class="container-fluid">
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title mb-30">
                    <h2>Data Spesifikasi Motor</h2>
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
                                Motor Specification
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <button type="button" class="btn btn-success mb-3 btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
        Tambah Spesifikasi
    </button>

    <!-- Modal Tambah -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addSpecLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="addSpecForm" method="POST" action="{{ route('admin.specifications.store', $motor->id) }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addSpecLabel">Tambah Spesifikasi Motor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="category" class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="">Pilih Kategori</option>
                            <option value="Rangka">Rangka</option>
                            <option value="Dimensi">Dimensi</option>
                            <option value="Mesin">Mesin</option>
                            <option value="Kelistrikan">Kelistrikan</option>
                            <option value="Kapasitas">Kapasitas</option>
                        </select>
                        @error('category')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="atribut" class="form-label">Atribut (contoh: Tipe Mesin) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="atribut" name="atribut" value="{{ old('atribut') }}" required>
                        @error('atribut')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="detail" class="form-label">Detail (contoh: 4 Langkah, SOHC, eSP) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="detail" name="detail" value="{{ old('detail') }}" required>
                        @error('detail')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
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
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editSpecLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editSpecForm" method="POST" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editSpecLabel">Ubah Data Spesifikasi Motor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="spec_id" name="id">
                    <div class="mb-3">
                        <label for="category_edit" class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select class="form-select" id="category_edit" name="category" required>
                            <option value="">Pilih Kategori</option>
                            <option value="Rangka">Rangka</option>
                            <option value="Dimensi">Dimensi</option>
                            <option value="Mesin">Mesin</option>
                            <option value="Kelistrikan">Kelistrikan</option>
                            <option value="Kapasitas">Kapasitas</option>
                        </select>
                        @error('category')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="atribut_edit" class="form-label">Atribut <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="atribut_edit" name="atribut" required>
                        @error('atribut')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="detail_edit" class="form-label">Detail <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="detail_edit" name="detail" required>
                        @error('detail')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
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
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteSpecLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="deleteForm" method="POST" class="modal-content">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteSpecLabel">Konfirmasi Hapus Spesifikasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah kamu yakin ingin menghapus spesifikasi <strong id="delete_spec_atribut"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="display" id="specification-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kategori</th>
                            <th>Atribut</th>
                            <th>Detail</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div id="message-container"></div>
</div>

<script>
$(document).ready(function() {
    let dataTable;
    let isEdit = false;
    let deleteId = '';

    function initDataTable() {
        dataTable = $('#specification-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.specifications.index', ['motor' => $motor->id]) }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'category', name: 'category' },
                { data: 'atribut', name: 'atribut' },
                { data: 'detail', name: 'detail' },
                {
                    data: null,
                    className: "text-center",
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return `
                            <div class="btn-group">
                                <button class="btn btn-sm btn-primary me-1 editBtn" data-id="${data.id}">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button class="btn btn-sm btn-danger deleteBtn" data-id="${data.id}" data-atribut="${data.atribut}">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>`;
                    }
                }
            ]
        });
    }

    initDataTable();

    function showMessage(message, type = 'success') {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const messageHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show mt-3" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('#message-container').html(messageHtml);
        setTimeout(() => $('.alert').alert('close'), 5000);
    }

    // Handle form tambah (Add)
    $('#addSpecForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        const url = $(this).attr('action');

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            success: function(response) {
                $('#addModal').modal('hide');
                showMessage(response.success);
                dataTable.ajax.reload(null, false);
                $(this).trigger('reset');
            },
            error: function(xhr) {
                showMessage(xhr.responseJSON?.error || 'Gagal menambahkan spesifikasi.', 'error');
            }
        });
    });

    // Handle tombol edit
    $(document).on('click', '.editBtn', function() {
        isEdit = true;
        const specId = $(this).data('id');

        $.ajax({
            url: "{{ route('admin.specifications.edit', ['motor' => $motor->id, 'id' => ':id']) }}".replace(':id', specId),
            method: 'GET',
            success: function(response) {
                $('#spec_id').val(response.id);
                $('#category_edit').val(response.category);
                $('#atribut_edit').val(response.atribut);
                $('#detail_edit').val(response.detail);
                $('#editSpecLabel').text('Ubah Data Spesifikasi Motor');
                $('#editModal').modal('show');
            },
            error: function(xhr) {
                showMessage('Gagal memuat data spesifikasi.', 'error');
            }
        });
    });

    // Handle form edit
    $('#editSpecForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        const url = "{{ route('admin.specifications.update', ['motor' => $motor->id, 'id' => ':id']) }}".replace(':id', $('#spec_id').val());

        $.ajax({
            url: url,
            method: 'POST', // Gunakan POST karena PUT tidak langsung didukung di form HTML
            data: formData + '&_method=PUT',
            success: function(response) {
                $('#editModal').modal('hide');
                showMessage(response.success);
                dataTable.ajax.reload(null, false);
                isEdit = false;
            },
            error: function(xhr) {
                showMessage(xhr.responseJSON?.error || 'Gagal mengubah spesifikasi.', 'error');
            }
        });
    });

    // Handle tombol hapus
    $(document).on('click', '.deleteBtn', function() {
        deleteId = $(this).data('id');
        const atribut = $(this).data('atribut');
        $('#delete_spec_atribut').text(atribut);
        $('#deleteModal').modal('show');
    });

    // Handle form hapus
    $('#deleteForm').on('submit', function(e) {
        e.preventDefault();
        const url = "{{ route('admin.specifications.delete', ['motor' => $motor->id, 'id' => ':id']) }}".replace(':id', deleteId);

        $.ajax({
            url: url,
            method: 'POST', // Gunakan POST karena DELETE tidak langsung didukung di form HTML
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: 'DELETE'
            },
            success: function(response) {
                $('#deleteModal').modal('hide');
                showMessage(response.success);
                dataTable.ajax.reload(null, false);
            },
            error: function(xhr) {
                showMessage(xhr.responseJSON?.error || 'Gagal menghapus spesifikasi.', 'error');
            }
        });
    });

    // Reset form saat modal ditutup
    $('#addModal, #editModal').on('hidden.bs.modal', function() {
        isEdit = false;
        $(this).find('form')[0]?.reset();
    });
});
</script>
@endsection