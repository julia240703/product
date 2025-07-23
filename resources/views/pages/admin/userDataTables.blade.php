@extends('layouts.appAdmin')
<title>Data Peserta</title>

@section('content')
    <!-- ========== section start ========== -->
    <div class="container-fluid">
        <!-- ========== title-wrapper start ========== -->
        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="title">
                        <h2>Data Peserta</h2>
                    </div>
                </div>
                <!-- end col -->
                <div class="col-md-6">
                    <div class="breadcrumb-wrapper">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="#0">Admin</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Users
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end row -->
        <div class="row mt-3 justify-content-end">
            <div class="col-md-2 select-style-1">
                <div class="select-position select-sm">
                    <select id="userCountSelect" class="bg-white form-select">
                        <option value="all">Semua</option>
                        <option value="today" {{ $selectedOption === 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="7days" {{ $selectedOption === '7days' ? 'selected' : '' }}>7 Hari</option>
                        <option value="30days" {{ $selectedOption === '30days' ? 'selected' : '' }}>30 Hari</option>
                        <option value="60days" {{ $selectedOption === '60days' ? 'selected' : '' }}>60 Hari</option>
                        <option value="90days" {{ $selectedOption === '90days' ? 'selected' : '' }}>90 Hari</option>
                        <!-- Add more options as needed -->
                    </select>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <div class="d-flex justify-content-end">
                        <!-- <button type="button" class="btn btn-danger mb-3 ms-1 btn-sm" data-bs-toggle="modal" data-bs-target="#confirmDeleteAllModal">Hapus Seluruh Peserta</button> -->
                    </div>
                    <table class="display" id="users-table" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Dibuat Pada</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <!-- Table content goes here (you can add table rows and data dynamically) -->
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="confirmDeleteAllModal" tabindex="-1" aria-labelledby="confirmDeleteAllModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmDeleteAllModalLabel">Konfirmasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Apakah anda yakin ingin menghapus seluruh data peserta?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <form method="POST" action="{{ route('admin.deleteAllUsers') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for confirming delete -->
        <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.deleteUser') }}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="user_id" id="delete_user_id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Konfirmasi Hapus User</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <p>Apakah kamu yakin ingin menghapus user <strong id="delete_user_name"></strong>?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                var dataTable = $('#users-table').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.users') }}", // Fetch data from the existing route
                        data: function(d) {
                            d.duration = $('#userCountSelect')
                                .val(); // Filter data based on selected duration
                        },
                    },
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
                            data: 'created_at',
                            name: 'created_at',
                            render: function(data) {
                                let date = new Date(data);
                                return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
                            }
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });

                // Reload DataTable when filter is changed
                $('#userCountSelect').change(function() {
                    dataTable.ajax.reload();
                });
            });

            $(document).on('click', '.deleteBtn', function() {
                const userId = $(this).data('id');
                const userName = $(this).closest('tr').find('td:eq(1)').text();

                $('#delete_user_id').val(userId);
                $('#delete_user_name').text(userName);

                const modal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
                modal.show();
            });
        </script>
    @endsection
