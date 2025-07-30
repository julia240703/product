    @extends('layouts.appAdmin')
    <title>Data Motor</title>

    @section('content')
        <!-- ========== section start ========== -->
        <div class="container-fluid">
            <!-- ========== title-wrapper start ========== -->
            <div class="title-wrapper pt-30">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="title mb-30">
                            <h2>Kelola Data Motor</h2>
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-md-6">
                        <div class="breadcrumb-wrapper mb-30">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="#0">Admin</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        Motor
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>

            <!-- Button to trigger the modal -->
            <button type="button" class="btn btn-success mb-3 btn-sm" data-bs-toggle="modal"
                data-bs-target="#addUserModal">Tambah Motor</button>

            <!-- Modal for adding a new user -->
            <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addUserModalLabel">Tambah Motor</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addUserForm" method="POST" action="{{ route('admin.motors.store') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="text-bold mb-1">Nama Motor <span class="text-red mt-2">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label class="text-bold mb-1">Kategori <span class="text-red mt-2">*</span></label>
                                    <select name="category_id" id="category" class="form-select select2" required> <!-- Ubah name ke category_id -->
                                        <option disabled selected value="">Pilih Kategori</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option> <!-- Gunakan id kategori -->
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="text-bold mb-1">Harga <span class="text-red mt-2">*</span></label>
                                    <input type="text" class="form-control" id="price" name="price" required>
                                </div>
                                <div class="mb-3">
                                    <label class="text-bold mb-1">Warna <span class="text-red mt-2">*</span></label>
                                    <select class="form-select select2" id="color" name="color" required>
                                        <option selected disabled value="">Pilih Warna</option>
                                        <option value="Merah">Merah</option>
                                        <option value="Biru">Biru</option>
                                        <option value="Hitam">Hitam</option>
                                        <option value="Putih">Putih</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <input type="hidden" name="type" value="2">
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

            <!-- Modal for editing user -->
            <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('admin.motors.update') }}">
                        @csrf
                        <input type="hidden" name="user_id" id="edit_user_id">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Motor</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="edit_name" class="form-label">Nama Motor</label>
                                    <input type="text" class="form-control" name="name" id="edit_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_category" class="form-label">Kategori</label>
                                    <select name="category" id="edit_category" class="form-select select2" required>
                                    <option disabled selected value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->name }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_price" class="form-label">Harga</label>
                                    <input type="text" class="form-control" name="price" id="edit_price" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_color" class="form-label">Warna</label>
                                    <select class="form-select select2" name="color" id="edit_color">
                                        <option value="Merah">Merah</option>
                                        <option value="Biru">Biru</option>
                                        <option value="Hitam">Hitam</option>
                                        <option value="Putih">Putih</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal for confirming delete motor -->
            <div class="modal fade" id="deleteMotorModal" tabindex="-1" aria-labelledby="deleteMotorModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('admin.motors.delete') }}">
                        @csrf
                        @method('DELETE') {{-- <== penting ini --}}
                        <input type="hidden" name="motor_id" id="delete_motor_id">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Konfirmasi Hapus Motor</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                            </div>
                            <div class="modal-body">
                                <p>Apakah kamu yakin ingin menghapus Motor <strong id="delete_motor_name"></strong>?</p>
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
                        <table class="display" id="users-table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Motor</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Warna</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <script>
                $(document).ready(function() {
                    $('#users-table').DataTable({
                        responsive: true,
                        processing: true,
                        serverSide: true,
                        ajax: "{{ route('admin.motor.data') }}",
                        columns: [
                            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                            { data: 'name', name: 'name' },
                            { data: 'category', name: 'category' },
                            { data: 'price', name: 'price' },
                            { data: 'color', name: 'color' },
                            { data: 'action', name: 'action', orderable: false, searchable: false },
                        ]
                    });

                    $(document).on('click', '.deleteBtn', function() {
                        const motorId = $(this).data('id');
                        const motorName = $(this).closest('tr').find('td:eq(1)').text();

                        $('#delete_motor_id').val(motorId);
                        $('#delete_motor_name').text(motorName);

                        const modal = new bootstrap.Modal(document.getElementById('deleteMotorModal'));
                        modal.show();
                    });

                    $(document).on('click', '.editBtn', function() {
                        const id = $(this).data('id');
                        const name = $(this).data('name');
                        const category = $(this).data('category');
                        const price = $(this).data('price');
                        const color = $(this).data('color');

                        $('#edit_user_id').val(id);
                        $('#edit_name').val(name);
                        $('#edit_category').val(category).trigger('change');
                        $('#edit_price').val(price);
                        $('#edit_color').val(color).trigger('change');
                        $('#editUserModal').modal('show');
                    });
                });

                $(document).ready(function() {
                    $('.select2').select2({
                        width: '100%',
                        placeholder: "Pilih Warna",
                        allowClear: true
                    });
                });
            </script>

            <script>
                // Format input harga saat user mengetik
                $('#price').on('input', function () {
                    let value = $(this).val().replace(/\D/g, '');
                    if (value) {
                        $(this).val(new Intl.NumberFormat('id-ID').format(value));
                    } else {
                        $(this).val('');
                    }
                });

                // Bersihkan titik saat form dikirim (biar backend dapet angka asli)
                $('#addUserForm').on('submit', function () {
                    let cleanPrice = $('#price').val().replace(/\./g, '').replace(/,/g, '');
                    $('#price').val(cleanPrice);
                });
            </script>
        @endsection