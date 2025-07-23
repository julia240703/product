@extends('layouts.appAdmin')
<title>Kelola Psikotes</title>

@section('content')
    <!-- ========== section start ========== -->
    <div class="container-fluid">
        <!-- ========== title-wrapper start ========== -->
        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="title mb-30">
                        <h2>Kelola Psikotes</h2>
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
                                    Manage-exam
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- ========== title-wrapper end ========== -->
        <!-- ========== Start-of-Button ========== -->

        <button type="button" class="btn btn-success btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#confirm">Tambah
            Psikotes</button>

        <!-- Confirmation Modal -->
        <div class="modal fade" id="confirm" tabindex="-1" aria-labelledby="confirmLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmLabel">Tambahkan Psikotes</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('store.exam') }}" method="POST">
                        @csrf
                        @method('POST')

                        <div class="modal-body">

                            <!-- Title input -->
                            <div class="form-outline mb-3">
                                <label class="text-bold mb-1"s>Judul Psikotes <span class="text-red mt-2">*</span></label>
                                <input type="text" id="title" name="title" class="form-control" required />
                            </div>

                            <!-- Description input -->
                            <div class="form-outline mb-3">
                                <label class="text-bold mb-1">Deskripsi Psikotes <span
                                        class="text-red mt-2">*</span></label>
                                <textarea type="text" id="desc" name="desc" class="form-control" required></textarea>
                            </div>

                            <!-- Exam type input -->
                            <div class="form-outline mb-3">
                                <label class="text-bold mb-1" for="tipe_tes">Tipe Psikotes <span
                                        class="text-red mt-2">*</span></label>
                                <select class="form-select" id="tipe_tes" name="tipe_tes" required>
                                    <option value="Pilihan-Ganda">Pilihan Ganda</option>
                                    <option value="Essay">Essay</option>
                                </select>
                            </div>

                            <!-- Time input -->
                            <div class="form-outline mb-3">
                                <label class="text-bold mb-1">Waktu Tes (menit) <span class="text-red mt-2">*</span></label>
                                <input type="text" id="duration" name="duration" class="form-control" required />
                            </div>

                            <!-- Limit input -->
                            <div class="form-outline mb-3">
                                <label class="text-bold mb-1">Jumlah Pertanyaan yang Tampil <span
                                        class="text-red mt-2">*</span></label>
                                <input type="text" id="limit" name="limit" class="form-control" required />
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                            <button type="submit" class="btn btn-success">Tambahkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- ========== End-of-Button ========== -->

        <!-- ========== Start-of-Datatables ========== -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="display" id="exam-table" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Judul</th>
                                <th>Deskripsi</th>
                                <th>Tipe</th>
                                <th>Waktu</th>
                                <th>Limitasi Soal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <!-- Table content goes here (you can add table rows and data dynamically) -->
                    </table>
                </div>
            </div>
        </div>

        <!-- ========== End-of-Datatables ========== -->

        <!-- Edit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Ubah Psikotes</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-bold">
                        <form id="editForm">
                            <input type="hidden" id="quizId">
                            <div class="mb-3">
                                <label for="name" class="form-label">Judul Psikotes <span
                                        class="text-red mt-2">*</span></label>
                                <input type="text" class="form-control" id="name" required />
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi Psikotes <span
                                        class="text-red mt-2">*</span></label>
                                <textarea class="form-control" id="description" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="type" class="form-label">Tipe Psikotes <span
                                        class="text-red mt-2">*</span></label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="Pilihan-Ganda">Pilihan Ganda</option>
                                    <option value="Essay">Essay</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="time" class="form-label">Waktu Test (menit) <span
                                        class="text-red mt-2">*</span></label>
                                <input type="text" class="form-control" id="time" required />
                            </div>
                            <div class="mb-3">
                                <label for="quiz_limit" class="form-label">Jumlah Pertanyaan yang Tampil <span
                                        class="text-red mt-2">*</span></label>
                                <input type="text" class="form-control" id="quiz_limit" required />
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

        <!-- delete Psikotes -->
        <div class="modal fade" id="deleteQuizModal" tabindex="-1" aria-labelledby="deleteQuizModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('manage.exam.delete') }}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="id" id="delete_quiz_id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Konfirmasi Hapus Psikotes</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <p>Apakah kamu yakin ingin menghapus psikotes <strong id="delete_quiz_name"></strong>?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========== End-of-Datatables ========== -->

        <!-- ========== Start-of-AJAX ========== -->

        <script>
            $(document).ready(function() {
                // DataTable initialization
                var dataTable = $('#exam-table').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('manage.exam') }}",
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
                            data: 'description',
                            name: 'description'
                        },
                        {
                            data: 'type',
                            name: 'type'
                        },
                        {
                            data: 'time',
                            name: 'time'
                        },
                        {
                            data: 'quiz_limit',
                            name: 'quiz_limit'
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
                    ],
                });

                // Edit button click event
                $(document).on('click', '.editBtn', function() {
                    var quizId = $(this).data('id');

                    // Retrieve the quiz data from DataTable
                    var quizData = dataTable.row($(this).closest('tr')).data();

                    // Populate the edit modal with the quiz data
                    $('#quizId').val(quizData.id);
                    $('#name').val(quizData.name);
                    $('#description').val(quizData.description);
                    $('#type').val(quizData.type);
                    $('#time').val(quizData.time);
                    $('#quiz_limit').val(quizData.quiz_limit);

                    // Open the edit modal
                    $('#editModal').modal('show');
                });

                // Update button click event
                $(document).on('click', '#updateBtn', function() {
                    var quizId = $('#quizId').val();
                    var name = $('#name').val();
                    var description = $('#description').val();
                    var type = $('#type').val();
                    var time = $('#time').val();
                    var quiz_limit = $('#quiz_limit').val();

                    // Perform the update operation
                    $.ajax({
                        url: "{{ route('manage.exam.edit') }}",
                        method: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: quizId,
                            name: name,
                            description: description,
                            type: type,
                            time: time,
                            quiz_limit: quiz_limit,
                        },
                        success: function(response) {
                            console.log('Psikotes berhasil diperbarui');
                            // Store the success message in a variable
                            var successMessage = 'Psikotes berhasil diperbarui';
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
                    const quizId = $(this).data('id');
                    const quizName = $(this).closest('tr').find('td:eq(1)')
                .text();

                    $('#delete_quiz_id').val(quizId);
                    $('#delete_quiz_name').text(quizName);

                    const modal = new bootstrap.Modal(document.getElementById('deleteQuizModal'));
                    modal.show();
                });


            });
        </script>

        <!-- ========== End-of-ajax ========== -->

        <!-- End Row -->
    @endsection
