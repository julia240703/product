@extends('layouts.appAdmin')
<title>Kelola Price List</title>

@section('content')
    <div class="container-fluid">
        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="title mb-30">
                        <h2>Data Price List</h2>
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
                                    Price List
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-success mb-3 btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
            Tambah Price List
        </button>

        <!-- Modal Add -->
        <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addPriceListLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addPriceListLabel">Tambahkan Price List</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('admin.price_list.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="motorcycle_name" class="form-label">Nama Motor <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="motorcycle_name" name="motorcycle_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="motor_type" class="form-label">Tipe Motor <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="motor_type" name="motor_type" required>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Harga <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
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
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editPriceListLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPriceListLabel">Ubah Data Price List</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editPriceListForm" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="motorcycle_name_edit" class="form-label">Nama Motor <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="motorcycle_name_edit" name="motorcycle_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="motor_type_edit" class="form-label">Tipe Motor <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="motor_type_edit" name="motor_type" required>
                            </div>
                            <div class="mb-3">
                                <label for="price_edit" class="form-label">Harga <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="price_edit" name="price" required>
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

        <!-- Modal Delete -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deletePriceListLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deletePriceListLabel">Konfirmasi Hapus Price List</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Apakah Anda yakin ingin menghapus price list untuk motor <strong id="delete_motorcycle_name"></strong>?</p>
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
                    <table class="display" id="price-list-table" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Motor</th>
                                <th>Tipe Motor</th>
                                <th>Harga</th>
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
                var dataTable = $('#price-list-table').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.price_list.index') }}",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'motorcycle_name', name: 'motorcycle_name' },
                        { data: 'motor_type', name: 'motor_type' },
                        { data: 'price_display', name: 'price', orderable: false, searchable: false },
                        {
                            data: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });

                // === Edit ===
                $(document).on('click', '.editBtn', function() {
                    var id = $(this).data('id');
                    var motorcycleName = $(this).data('motorcycle-name');
                    var motorType = $(this).data('motor-type');
                    var price = $(this).data('price'); // <-- ini angka murni

                    $('#motorcycle_name_edit').val(motorcycleName);
                    $('#motor_type_edit').val(motorType);
                    $('#price_edit').val(price);

                    const editForm = $('#editPriceListForm');
                    editForm.attr('action', "{{ route('admin.price_list.update', 'DUMMY') }}".replace('DUMMY', id));
                    $('#editModal').modal('show');
                });

                // === Delete ===
                $(document).on('click', '.deleteBtn', function() {
                    const id = $(this).data('id');
                    const motorcycleName = $(this).data('motorcycle-name');
                    $('#delete_motorcycle_name').text(motorcycleName);
                    const form = $('#deleteForm');
                    form.attr('action', "{{ route('admin.price_list.delete', 'DUMMY') }}".replace('DUMMY', id));
                    $('#deleteModal').modal('show');
                });

                // Reset form kalau modal ditutup
                $('.modal').on('hidden.bs.modal', function () {
                    $(this).find('form')[0]?.reset();
                });
            });
        </script>
@endsection