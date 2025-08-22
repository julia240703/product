@extends('layouts.appAdmin')
<title>Kelola Kota Cabang</title>

@section('content')
<div class="container-fluid">
    <div class="title-wrapper pt-30">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="title mb-30">
                    <h2>Data Kota Cabang</h2>
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
                                Branch City
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <button type="button" class="btn btn-success mb-3 btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">Tambah
        Kota</button>

    <!-- Modal Tambah -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Tambah Kota Cabang </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addCategoryForm" method="POST" action="{{ route('admin.branch-cities.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="city" class="form-label">Nama Kota <span class="text-red">*</span></label>
                            <input class="form-control" id="city" name="nama_kota" required />
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
                    <h5 class="modal-title" id="editModalLabel">Ubah Data Kota Cabang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <input type="hidden" id="cityId">
                        <div class="mb-3">
                            <label for="city_edit" class="form-label">Nama Kota <span class="text-red">*</span></label>
                            <input class="form-control" id="city_edit" name="nama_kota_edit" required />
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

    <!-- Modal Delete -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.branch-cities.delete') }}">
                @csrf
                @method('DELETE')
                <input type="hidden" name="id" id="delete_city_id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Konfirmasi Hapus Kota</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah kamu yakin ingin menghapus kota <strong id="delete_city_name"></strong>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- DataTable -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="display" id="city-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Kota</th>
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
            var dataTable = $('#city-table').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.branch-cities.data') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'nama_kota', name: 'nama_kota' },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `
                                <button class="btn btn-primary editBtn btn-sm" data-id="${data.id}" data-name="${data.nama_kota}">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button class="btn btn-danger deleteBtn btn-sm" data-id="${data.id}" data-name="${data.nama_kota}">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            `;
                        }
                    }
                ]
            });

            // Edit Button
            $(document).on('click', '.editBtn', function() {
                var cityData = dataTable.row($(this).closest('tr')).data();
                $('#cityId').val(cityData.id);
                $('#city_edit').val(cityData.nama_kota);
                $('#editModal').modal('show');
            });

            // Update City
            $('#updateBtn').on('click', function() {
                $.ajax({
                    url: "{{ route('admin.branch-cities.update') }}",
                    method: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: $('#cityId').val(),
                        nama_kota: $('#city_edit').val(),
                    },
                    success: function(response) {
                        $('#editModal').modal('hide');
                        dataTable.ajax.reload();
                    }
                });
            });

            // Delete Button
            $(document).on('click', '.deleteBtn', function() {
                const id = $(this).data('id');
                const name = $(this).closest('tr').find('td:eq(1)').text();
                $('#delete_city_id').val(id);
                $('#delete_city_name').text(name);
                $('#deleteModal').modal('show');
                $('#deleteModal form').off('submit').on('submit', function() {
                    $(this).find('button[type="submit"]').prop('disabled', true);
                });
            });
        });
    </script>
@endsection