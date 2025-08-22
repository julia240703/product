    @extends('layouts.appAdmin')
    <title>Kelola Kategori Apparel</title>

    @section('content')
        <div class="container-fluid">
            <div class="title-wrapper pt-30">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="title mb-30">
                            <h2>Data Kategori Apparel</h2>
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
                                        Apparels Category
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-success mb-3 btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">Tambah
                Kategori</button>

            <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addUserModalLabel">Tambah Kategori Apparel</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addCategoryForm" method="POST" action="{{ route('admin.apparel-categories.store') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="kategori" class="form-label">Nama Kategori <span class="text-red">*</span></label>
                                    <input class="form-control" id="kategori" name="kategori" required />
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

            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Ubah Data Kategori Motor</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editForm">
                                <input type="hidden" id="categoryId">
                                <div class="mb-3">
                                    <label for="kategori_edit" class="form-label">Nama Kategori <span class="text-red">*</span></label>
                                    <input class="form-control" id="kategori_edit" name="kategori_edit" required />
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

            <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('admin.apparel-categories.delete') }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="id" id="delete_category_id">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Konfirmasi Hapus Kategori</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                            </div>
                            <div class="modal-body">
                                <p>Apakah kamu yakin ingin menghapus kategori <strong id="delete_category_name"></strong>?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display" id="category-table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Kategori</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <script>
                $(document).ready(function() {
                    var dataTable = $('#category-table').DataTable({
                        responsive: true,
                        processing: true,
                        serverSide: true,
                        ajax: "{{ route('admin.apparel-categories.data') }}",
                        columns: [
                            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                            { data: 'nama_kategori', name: 'nama_kategori' },
                            {
                                data: null,
                                orderable: false,
                                searchable: false,
                                render: function(data) {
                                    return `
                                        <button class="btn btn-primary editBtn btn-sm" data-id="${data.id}" data-name="${data.nama_kategori}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button class="btn btn-danger deleteBtn btn-sm" data-id="${data.id}" data-name="${data.nama_kategori}">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    `;
                                }
                            }
                        ]
                    });

                    $(document).on('click', '.editBtn', function() {
                        var categoryId = $(this).data('id');
                        var categoryData = dataTable.row($(this).closest('tr')).data();
                        $('#categoryId').val(categoryData.id);
                        $('#kategori_edit').val(categoryData.nama_kategori);
                        $('#editModal').modal('show');
                    });

                    $('#updateBtn').on('click', function() {
                        $.ajax({
                            url: "{{ route('admin.apparel-categories.update') }}",
                            method: "POST",
                            data: {
                                _token: '{{ csrf_token() }}',
                                id: $('#categoryId').val(),
                                nama_kategori: $('#kategori_edit').val(),
                            },
                            success: function(response) {
                                $('#editModal').modal('hide');
                                dataTable.ajax.reload();
                            }
                        });
                    });

                    $(document).on('click', '.deleteBtn', function() {
                        const id = $(this).data('id');
                        const name = $(this).closest('tr').find('td:eq(1)').text();
                        $('#delete_category_id').val(id);
                        $('#delete_category_name').text(name);
                        $('#deleteModal').modal('show');
                    });
                });
            </script>
        @endsection