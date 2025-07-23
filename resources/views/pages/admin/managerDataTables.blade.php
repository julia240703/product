@extends('layouts.appAdmin')
<title>Data Admin</title>

@section('content')
    <!-- ========== section start ========== -->
    <div class="container-fluid">
        <!-- ========== title-wrapper start ========== -->
        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="title mb-30">
                        <h2>Data Sub-Admin</h2>
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
                                    Manager
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
            data-bs-target="#addUserModal">Tambah Sub-Admin</button>

        <!-- Modal for adding a new user -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">Tambah Sub-Admin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addUserForm" method="POST" action="{{ route('admin.addManager') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama <span class="text-red mt-2">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-red mt-2">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password <span
                                        class="text-red mt-2">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="branch_id" class="form-label">Lokasi Cabang <span
                                        class="text-red mt-2">*</span></label>
                                <select class="form-select select2" id="branch_id" name="branch_id" required>
                                    <option selected disabled value="">Pilih Cabang</option>
                                    <option value="">HO/Pusat</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->city }} - {{ $branch->location }}
                                        </option>
                                    @endforeach
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
                <form method="POST" action="{{ route('admin.updateManager') }}">
                    @csrf
                    <input type="hidden" name="user_id" id="edit_user_id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Sub-Admin</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_name" class="form-label">Nama</label>
                                <input type="text" class="form-control" name="name" id="edit_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="edit_email" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_branch_id" class="form-label">Cabang</label>
                                <select class="form-select select2" name="branch_id" id="edit_branch_id">
                                    <option value="">HO</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">
                                            {{ $branch->city }} - {{ $branch->location }}
                                        </option>
                                    @endforeach
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

        <!-- Modal for confirming delete -->
        <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.deleteManager') }}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="user_id" id="delete_user_id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Konfirmasi Hapus Sub-Admin</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <p>Apakah kamu yakin ingin menghapus Sub-Admin <strong id="delete_user_name"></strong>?</p>
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
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Cabang</th>
                                <th>Dibuat Pada</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <!-- Table content goes here (you can add table rows and data dynamically) -->
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal for confirming reset password -->
        <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.resetManagerPassword') }}">
                    @csrf
                    <input type="hidden" name="user_id" id="reset_user_id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Konfirmasi Reset Password</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <p>Apakah kamu yakin ingin mereset password untuk <strong id="reset_user_name"></strong>?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning">Ya, Reset</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>




        <script>
            $(document).ready(function() {
                $('#users-table').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.manager') }}",
                    columns: [{
                            data: 'row_number',
                            name: 'row_number',
                            orderable: true,
                            searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'branch',
                            name: 'branch',
                            render: function(data, type, full) {
                                return data ? data : 'HO';
                            }
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                            render: function(data) {
                                let date = new Date(data);
                                let year = date.getFullYear();
                                let month = String(date.getMonth() + 1).padStart(2, '0');
                                let day = String(date.getDate()).padStart(2, '0');
                                return `${year}-${month}-${day}`;
                            }
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                        },
                    ]
                });

                $(document).on('click', '.deleteBtn', function() {
                    const userId = $(this).data('id');
                    const userName = $(this).closest('tr').find('td:eq(1)').text();

                    $('#delete_user_id').val(userId);
                    $('#delete_user_name').text(userName);

                    const modal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
                    modal.show();
                });

                $(document).on('click', '.editBtn', function() {
                    const id = $(this).data('id');
                    const name = $(this).data('name');
                    const email = $(this).data('email');
                    const branch = $(this).data('branch');

                    $('#edit_user_id').val(id);
                    $('#edit_name').val(name);
                    $('#edit_email').val(email);
                    $('#edit_branch_id').val(branch).trigger('change');
                    $('#editUserModal').modal('show');
                });

                $(document).on('click', '.resetPasswordBtn', function() {
                    const userId = $(this).data('id');
                    const userName = $(this).closest('tr').find('td:eq(1)').text();

                    $('#reset_user_id').val(userId);
                    $('#reset_user_name').text(userName);

                    const modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
                    modal.show();
                });




            });


            $(document).ready(function() {
                $('.select2').select2({
                    width: '100%',
                    placeholder: "Pilih Lokasi Cabang",
                    allowClear: true
                });
            });
        </script>
    @endsection
