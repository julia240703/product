@extends('layouts.appAdmin')
<title>Kelola Posisi Pekerjaan</title>

@section('content')
    <!-- ========== section start ========== -->
    <div class="container-fluid">
        <!-- ========== title-wrapper start ========== -->
        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="title mb-30">
                        <h2>Data Posisi Pekerjaan</h2>
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

        <!-- Button to trigger the modal -->
        <button type="button" class="btn btn-success mb-3 btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">Tambah
            Posisi</button>

        <!-- Modal for adding a new branch -->
        <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">Tambah Posisi Pekerjaan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addUserForm" method="POST" action="{{ route('store.jobPosition') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="posisi" class="form-label">Nama Posisi <span
                                        class="text-red mt-2">*</span></label>
                                <input class="form-control" id="posisi" name="posisi" required />
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
                        <h5 class="modal-title" id="editModalLabel">Ubah Data Posisi Pekerjaan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editForm">
                            <input type="hidden" id="jobPositionId">

                            <div class="mb-3">
                                <label for="position" class="form-label">Nama Posisi <span
                                        class="text-red mt-2">*</span></label>
                                <input class="form-control" id="position" name="position" required />
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


        <!-- delete job position-->
        <div class="modal fade" id="deleteJobModal" tabindex="-1" aria-labelledby="deleteJobModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('delete.jobPosition') }}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="id" id="delete_job_id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Konfirmasi Hapus Posisi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <p>Apakah kamu yakin ingin menghapus posisi <strong id="delete_job_name"></strong>?</p>
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
                                <th>Posisi Pekerjaan</th>
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
                    ajax: "{{ route('job.position') }}",
                    columns: [{
                            data: 'row_number',
                            name: 'row_number',
                            orderable: true,
                            searchable: false
                        },
                        {
                            data: 'position',
                            name: 'position'
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
                    var jobPositionId = $(this).data('id');

                    // Retrieve the branch data from DataTable
                    var jobPositionData = dataTable.row($(this).closest('tr')).data();

                    // Populate the edit modal with the branch data
                    $('#jobPositionId').val(jobPositionData.id);
                    $('#position').val(jobPositionData.position);

                    // Open the edit modal
                    $('#editModal').modal('show');
                });

                // Update button click event
                $(document).on('click', '#updateBtn', function() {
                    var jobPositionId = $('#jobPositionId').val();
                    var position = $('#position').val();

                    // Perform the update operation
                    $.ajax({
                        url: "{{ route('edit.jobPosition') }}",
                        method: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: jobPositionId,
                            position: position,

                        },
                        success: function(response) {
                            console.log('Posisi Pekerjaan berhasil diperbarui');
                            // Store the success message in a variable
                            var successMessage = 'Posisi Pekerjaan berhasil diperbarui';
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
                    const jobId = $(this).data('id');
                    const jobName = $(this).closest('tr').find('td:eq(1)')
                .text();

                    $('#delete_job_id').val(jobId);
                    $('#delete_job_name').text(jobName);

                    const modal = new bootstrap.Modal(document.getElementById('deleteJobModal'));
                    modal.show();
                });

            });
        </script>
    @endsection
