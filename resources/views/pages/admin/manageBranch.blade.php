@extends('layouts.appAdmin')
<title>Kelola Cabang</title>

@section('content')
    <!-- ========== section start ========== -->
    <div class="container-fluid">
        <!-- ========== title-wrapper start ========== -->
        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="title mb-30">
                        <h2>Data Cabang</h2>
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
                                    Branch
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
            data-bs-target="#addBranchModal">Tambah Cabang</button>

        <!-- Modal for adding a new branch -->
        <div class="modal fade" id="addBranchModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">Tambah Cabang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addUserForm" method="POST" action="{{ route('store.branch') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="kota" class="form-label">Kota Cabang <span
                                        class="text-red mt-2">*</span></label>
                                <select type="text" class="form-select" id="kota" name="kota" required>
                                    <option selected disabled value="">Pilih Kota</option>
                                    <option value="Jakarta">Jakarta</option>
                                    <option value="Tangerang">Tangerang</option>
                                    <option value="Luar Kota">Luar Kota</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="lokasi" class="form-label">Nama Cabang <span
                                        class="text-red mt-2">*</span></label>
                                <input class="form-control" id="lokasi" name="lokasi" required />
                            </div>
                            <div class="mb-3">
                                <label for="inisial" class="form-label">Inisial Cabang <span
                                        class="text-red mt-2">*</span></label>
                                <input class="form-control" id="inisial" name="inisial" required />
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


        <!-- Edit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Ubah Data Cabang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editForm">
                            <input type="hidden" id="branchId">
                            <div class="mb-3">
                                <label for="city" class="form-label">Kota Cabang <span
                                        class="text-red mt-2">*</span></label>
                                <select class="form-select" id="city" name="city" required>
                                    <option value="Jakarta">Jakarta</option>
                                    <option value="Tangerang">Tangerang</option>
                                    <option value="Luar Kota">Luar Kota</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="location" class="form-label">Nama Cabang <span
                                        class="text-red mt-2">*</span></label>
                                <input class="form-control" id="location" name="location"required />
                            </div>
                            <div class="mb-3">
                                <label for="initials" class="form-label">Inisial Cabang <span
                                        class="text-red mt-2">*</span></label>
                                <input class="form-control" id="initials" name="initials" required />
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

        <!-- Modal delete Branch -->
        <div class="modal fade" id="deleteBranchModal" tabindex="-1" aria-labelledby="deleteBranchModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('delete.branch') }}">
                    @csrf
                    <input type="hidden" name="id" id="delete_branch_id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Konfirmasi Hapus Cabang</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <p>Apakah kamu yakin ingin menghapus cabang <strong id="delete_branch_name"></strong>?</p>
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
                                <th>Kota Cabang</th>
                                <th>Nama Cabang</th>
                                <th>Insial Cabang</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <!-- Table content goes here (you can add table rows and data dynamically) -->
                    </table>
                </div>
            </div>
        </div>


        <script>
            $(document).ready(function() {
                var dataTable = $('#users-table').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('manage.branch') }}",
                    columns: [{
                            data: 'row_number',
                            name: 'row_number',
                            orderable: true,
                            searchable: false
                        },
                        {
                            data: 'city',
                            name: 'city'
                        },
                        {
                            data: 'location',
                            name: 'location'
                        },
                        {
                            data: 'initials',
                            name: 'initials'
                        },
                        {
                            data: null,
                            render: function(data) {
                                var editButton =
                                    '<button class="btn btn-primary editBtn mb-1 btn-sm" data-id="' +
                                    data.id + '"><i class="fa-solid fa-pen-to-square"></i></button>';
                                var deleteButton =
                                    '<button class="btn btn-danger deleteBtn mb-1 btn-sm" data-id="' +
                                    data.id +
                                    '"><i class="fa-solid fa-trash" style="color: #ffffff;"></i></button>';
                                return editButton + ' ' + deleteButton;
                            },
                        },
                    ]
                });

                // Edit button click event
                $(document).on('click', '.editBtn', function() {
                    var branchId = $(this).data('id');

                    // Retrieve the branch data from DataTable
                    var branchData = dataTable.row($(this).closest('tr')).data();

                    // Populate the edit modal with the branch data
                    $('#branchId').val(branchData.id);
                    $('#city').val(branchData.city);
                    $('#location').val(branchData.location);
                    $('#initials').val(branchData.initials);

                    // Open the edit modal
                    $('#editModal').modal('show');
                });

                // Update button click event
                $(document).on('click', '#updateBtn', function() {
                    var branchId = $('#branchId').val();
                    var city = $('#city').val();
                    var location = $('#location').val();
                    var initials = $('#initials').val();

                    // Perform the update operation
                    $.ajax({
                        url: "{{ route('edit.branch') }}",
                        method: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: branchId,
                            city: city,
                            location: location,
                            initials: initials,
                        },
                        success: function(response) {
                            console.log('Cabang berhasil diperbarui');
                            // Store the success message in a variable
                            var successMessage = 'Cabang berhasil diperbarui';
                            // Remove any existing success message container
                            $('#success-message').remove();
                            // Create a new success message container
                            var successElement = $(
                                '<div id="success-message" class="alert alert-success position-fixed top-0 end-0 m-3" style="max-width: 300px; z-index: 1050;"></div>'
                                );
                            successElement.text(successMessage);
                            // Append the success message container to a suitable location in your HTML
                            $('#message-container').append(successElement);
                            // Display the success message
                            successElement.fadeIn('slow');
                            // Automatically dismiss the success message after a certain time (e.g., 3 seconds)
                            setTimeout(function() {
                                successElement.fadeOut('slow', function() {
                                    successElement.remove();
                                });
                            }, 1500);
                            // Close the edit modal
                            $('#editModal').modal('hide');
                            // Optionally, you can perform any other actions or updates needed after a successful edit
                            // For example, you can update the DataTable or reload the page
                            dataTable.ajax.reload();
                        },
                    });
                });


                $(document).on('click', '.deleteBtn', function() {
                    const branchId = $(this).data('id');

                    const city = $(this).closest('tr').find('td:eq(1)').text().trim();
                    const branch = $(this).closest('tr').find('td:eq(2)').text().trim();

                    $('#delete_branch_id').val(branchId);
                    $('#delete_branch_name').text(`${city} - ${branch}`);

                    const modal = new bootstrap.Modal(document.getElementById('deleteBranchModal'));
                    modal.show();
                });


            });
        </script>
    @endsection
