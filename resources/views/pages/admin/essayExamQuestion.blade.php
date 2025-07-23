@extends('layouts.appAdmin')
<title>Kelola Pertanyaan: {{ $quiz->name }}</title>
 
@section('content')
 <!-- ========== section start ========== -->
        <div class="container-fluid">
          <!-- ========== title-wrapper start ========== -->
          <div class="title-wrapper pt-30">
            <div class="row align-items-center">
              <div class="col-md-6">
                <div class="title mb-30">
                  <h2>{{ $quiz->name }}</h2>
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
                      <li class="breadcrumb-item">
                      <a href="#0">Manage-exam-question</a>
                      </li>
                      <li class="breadcrumb-item active" aria-current="page">
                      {{ $quiz->id }}
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
          
          <div class="d-flex mb-3">
          <button type="button" class="btn btn-success mb-3 ms-1 btn-sm" data-bs-toggle="modal" data-bs-target="#soalBiasa">Tambah Soal</button>
              <div class="form-group">
                <button type="button" id="bulk upload" class="btn btn-primary ms-1 btn-sm" data-bs-toggle="modal" data-bs-target="#confirmUploadModal">Bulk Upload (xlsx)</button>
              </div>
          </div>


            <!-- Upload Modal -->
            <div class="modal fade" id="confirmUploadModal" tabindex="-1" aria-labelledby="confirmUploadModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmUploadModalLabel">Confirm Upload</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                        
                        <div class="d-flex justify-content-center align-items-center">
                            <a href="{{ route('download.essay.xlsx') }}" class="btn btn-success btn-sm">Download Contoh File</a>

                            <form id="bulkInsertForm" action="{{ route('essay.bulk-insert', ['quiz' => $quiz->id]) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <input type="file" name="xlsx_file" id="xlsx_file" accept=".xlsx" class="form-control" style="display: none;">
                                <button type="button" id="uploadButton" class="btn btn-primary ms-2 btn-sm">Upload File (xlsx)</button>
                            </form>
                        </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
              document.getElementById('uploadButton').addEventListener('click', function() {
                document.getElementById('xlsx_file').click();
              });

              document.getElementById('xlsx_file').addEventListener('change', function() {
                document.getElementById('bulkInsertForm').submit();
              });
            </script>

                    <!-- Add-Question-Modal -->
                    <div class="modal fade" id="soalBiasa" tabindex="-1" aria-labelledby="soalBiasaLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="soalBiasaLabel">Tambah Pertanyaan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <form action="{{ route('store.essay', ['quiz' => $quiz->id]) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('POST')

                                <div class="modal-body">

                                  <!-- Selection for Image/Text Visibility -->
                                  <div class="form-outline mb-1">
                                      <label class="text-bold mb-1">Pilih Tipe Pertanyaan <span class="text-red mt-2">*</span></label>
                                      <select id="inputTypeSelect" name="input_type" class="form-select" onchange="toggleInputType()">
                                          <option value="text">Text</option>
                                          <option value="image">Image</option>
                                      </select>
                                  </div>

                                  <!-- Question -->
                                  <div class="form-outline mb-3" id="questionDiv">
                                      <label class="text-bold mb-1">Pertanyaan <span class="text-red mt-2">*</span></label>
                                      <textarea type="text" id="question" name="question" class="form-control" required></textarea>
                                  </div>

                                  <!-- Image Input (Hidden by Default) -->
                                  <div class="form-outline mb-3" id="imageInputDiv" style="display: none;">
                                      <label class="text-bold mb-1">Pertanyaan <span class="text-red mt-2">*</span></label>
                                      <input type="file" id="image" name="image" class="form-control" accept="image/*" />
                                  </div>

                                  <!-- Is_Correct -->
                                  <div class="form-outline mb-3">
                                      <label class="text-bold mb-1">Jawaban <span class="text-red mt-2">*</span></label>
                                      <input type="text" id="correct" name="correct" class="form-control lowercase-input" required/>
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
                    <!-- Add-Question-Modal -->

          <!-- End Row -->

          <!-- ========== Start-of-Datatables ========== -->

          <div class="card">
              <div class="card-body">
                <div class="d-flex justify-content-end">
                  <button type="button" class="btn btn-danger mb-3 ms-1 btn-sm" data-bs-toggle="modal" data-bs-target="#deleteQuestionsModal">Hapus Seluruh Soal</button>
                  </div>
                  <div class="table-responsive">
                      <table class="display" id="question-table" style="width:100%">
                          <thead>
                              <tr>
                                  <th>#</th>
                                  <th>Pertanyaan</th>
                                  <th>Jawaban</th>
                                  <th>Aksi</th>
                              </tr>
                          </thead>
                          <!-- Table content goes here (you can add table rows and data dynamically) -->
                      </table>
                  </div>
              </div>
          </div>

          <!-- ========== End-of-Datatables ========== -->

          <!-- Detele All Question Confirmation Modal -->
<div class="modal fade" id="deleteQuestionsModal" tabindex="-1" aria-labelledby="deleteQuestionsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteQuestionsModalLabel">Hapus Seluruh Soal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                Apakah Anda yakin ingin menghapus seluruh soal untuk 
                <p>{{$quiz->name}}?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('delete.questions.for.quiz', ['quiz' => $quiz->id]) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

          <!-- Edit Modal Questions.question -->
          <div class="modal fade" id="editModalWithQuestion" tabindex="-1" aria-labelledby="editModalLabelWithQuestion" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="editModalLabel">Ubah Pertanyaan</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-bold">
                  <form id="editFormWithQuestion">
                    <input type="hidden" id="questionIdWithQuestion">

                    <!-- Question -->
                    <div class="form-outline mb-3">
                        <label class="text-bold mb-1">Pertanyaan <span class="text-red mt-2">*</span></label>
                        <textarea type="text" id="question1" name="question1" class="form-control" required></textarea>
                    </div>

                    <!-- Is_Correct -->
                    <div class="form-outline mb-3">
                        <label class="text-bold mb-1">Jawaban <span class="text-red mt-2">*</span></label>
                        <input type="text" id="is_correct" name="is_correct" class="form-control lowercase-input" required/>
                    </div>
                    
                  </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                    <!-- Move the "Ubah" button outside of the form and give it a unique ID -->
                    <button type="button" class="btn btn-success" id="updateBtnWithQuestion">Ubah</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Edit Modal Questions.image -->
          <div class="modal fade" id="editModalWithImage" tabindex="-1" aria-labelledby="editModalLabelWithImage" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="editModalLabel">Ubah Pertanyaan</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-bold">
                  <form id="editFormWithImage">
                    <input type="hidden" id="questionIdWithImage">

                    <!-- Image Input -->
                    <div class="form-outline mb-3" id="imageInputDiv">
                        <label class="text-bold mb-1">Pertanyaan (opsional)</label>
                        <div class="row g-2 align-items-center">
                            <div class="col-lg-8">
                                <input type="file" id="image1" name="image1" class="form-control" accept="image/*"/>
                            </div>
                            <div class="col">
                                <img id="imagePreview" src="" alt="Image Preview" style="max-width: 100px; display: none;">
                            </div>
                        </div>
                    </div>

                    <!-- Is_Correct -->
                    <div class="form-outline mb-3">
                        <label class="text-bold mb-1">Jawaban <span class="text-red mt-2">*</span></label>
                        <input type="text" id="is_correct1" name="is_correct" class="form-control lowercase-input" required/> <!-- Changed ID here -->
                    </div>
                    
                  </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                    <!-- Move the "Ubah" button outside of the form and give it a unique ID -->
                    <button type="button" class="btn btn-success" id="updateBtnWithImage">Ubah</button>
                </div>
              </div>
            </div>
          </div>

<script>
// JavaScript code to convert input value to lowercase
const lowercaseInputs = document.querySelectorAll('.lowercase-input');

lowercaseInputs.forEach(input => {
    input.addEventListener('input', () => {
        input.value = input.value.toLowerCase();
    });
});
</script>

<script>
function toggleInputType() {
    const questionDiv = document.getElementById('questionDiv');
    const imageInputDiv = document.getElementById('imageInputDiv');
    const inputTypeSelect = document.getElementById('inputTypeSelect');

    if (inputTypeSelect.value === 'text') {
        questionDiv.style.display = 'block';
        imageInputDiv.style.display = 'none';

        // Make the "question" input required and remove "required" from "image" input
        document.getElementById('question').setAttribute('required', '');
        document.getElementById('image').removeAttribute('required');
    } else if (inputTypeSelect.value === 'image') {
        questionDiv.style.display = 'none';
        imageInputDiv.style.display = 'block';

        // Make the "image" input required and remove "required" from "question" input
        document.getElementById('image').setAttribute('required', '');
        document.getElementById('question').removeAttribute('required');
    } else {
        // If "Select" is chosen, hide both inputs and remove "required" from both
        questionDiv.style.display = 'none';
        imageInputDiv.style.display = 'none';
        document.getElementById('question').removeAttribute('required');
        document.getElementById('image').removeAttribute('required');
    }
}


    $(document).on('change', '#image1', function() {
    const fileInput = document.getElementById('image1');
    const imagePreview = document.getElementById('imagePreview');

    if (fileInput.files && fileInput.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            imagePreview.src = e.target.result;
            imagePreview.style.display = 'block';
        };

        reader.readAsDataURL(fileInput.files[0]);
    } else {
        imagePreview.src = '';
        imagePreview.style.display = 'none';
    }
});
</script>

<!-- ========== Start-of-AJAX ========== -->
<script>
  $(document).ready(function() {
    // DataTable initialization
    var dataTable = $('#question-table').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('essay.exam', ['quiz' => $quiz->id]) }}",
            data: function(d) {
                // Add the question ID as a parameter in the AJAX request
                d.questionId = $('#questionId').val(); // Get the question ID from the edit modal form
            }
        },
        columns: [
            { data: 'row_number', name: 'row_number', orderable: true, searchable: false },
            {
                data: 'question',
                name: 'question',
                render: function(data, type, full, meta) {
                    if (type === 'display') {
                        if (full.image && /\.(jpeg|jpg|png)$/i.test(full.image)) {
                            var imageUrl = "{{ asset('files/question') }}/" + full.image;
                            return '<img src="' + imageUrl + '" alt="Option A Image" class="img-fluid" style="width: 50px;">';
                        } else {
                            return data;
                        }
                    }
                    return data;
                }
            },            
            { data: 'is_correct', name: 'is_correct' },
            {
                data: null,
                render: function(data) {
                    var editButton = '<button class="btn btn-primary editBtn mb-1 btn-sm" data-id="' + data.id + '"><i class="fa-solid fa-pen-to-square"></i></button>';
                    var deleteButton = '<button class="btn btn-danger deleteBtn mb-1 btn-sm" data-id="' + data.id + '"><i class="fa-solid fa-trash" style="color: #ffffff;"></i></button>';
                    return editButton + ' ' + deleteButton;
                }
            },
        ],
    });


// JavaScript to populate the image source from questionData.image
$(document).on('click', '.editBtn', function() {
    var questionId = $(this).data('id');
    var questionData = dataTable.row($(this).closest('tr')).data();
    var imagePreview = document.getElementById('imagePreview');

    // Show/hide the appropriate modal based on whether the question has a question value or an image value
    if (questionData.question) {
        $('#editModalWithQuestion').modal('show');
        $('#questionIdWithQuestion').val(questionData.id);
        $('#question1').val(questionData.question);
        $('#is_correct').val(questionData.is_correct);
    } else if (questionData.image) {
        $('#editModalWithImage').modal('show');
        $('#questionIdWithImage').val(questionData.id);
        // Update the image preview
        imagePreview.src = "{{ asset('files/question/') }}" + '/' + questionData.image;
        imagePreview.style.display = 'block';
        $('#is_correct1').val(questionData.is_correct);

        // Hide the question input field
    }
});

// Update button click event for modal with question
$(document).on('click', '#updateBtnWithQuestion', function() {
    var updatedQuestion = {
        id: $('#questionIdWithQuestion').val(),
        question: $('#question1').val(),
        is_correct: $('#is_correct').val(),
        _token: '{{ csrf_token() }}'
    };

    // Perform the update operation using the correct form ID
    $.ajax({
        url: "{{ route('essay.edit', ['quiz' => $quiz->id]) }}",
        method: 'POST',
        data: updatedQuestion,
        success: function(response) {
            console.log('Pertanyaan berhasil diperbarui');

            // Store the success message in a variable
            var successMessage = 'Pertanyaan berhasil diperbarui';

            // Remove any existing success message container
            $('#success-message').remove();

            // Create a new success message container
            var successElement = $('<div id="success-message" class="alert alert-success position-fixed top-0 end-0 m-3" style="max-width: 300px; z-index: 1050;"></div>');
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

            dataTable.ajax.reload(); // Update DataTable with updated data
            $('#editModalWithQuestion').modal('hide'); // Close the edit modal
          },
          error: function(xhr, status, error) {
            console.error('Gagal memperbarui pertanyaan:', error);
            // Display an error message or take appropriate action
          }
        });
      });

// Update button click event (common function for both modals)
$(document).on('click', '#updateBtnWithImage', function() {
    // Create a new FormData object
    var formData = new FormData();

    // Use the correct form ID to get the data from the form
    formData.append('id', $('#questionIdWithImage').val());
    formData.append('question', ''); // No need to include question field
    formData.append('is_correct', $('#is_correct1').val());
    formData.append('_token', '{{ csrf_token() }}');

    // Check if an image is present and add it to the FormData object
    var imageInput = document.getElementById('image1');
    if (imageInput.files.length > 0) {
        formData.append('image', imageInput.files[0]);
    }

    // Perform the update operation using AJAX
    $.ajax({
        url: "{{ route('essay.edit', ['quiz' => $quiz->id]) }}",
        method: 'POST',
        data: formData,
        contentType: false, // Set to false to prevent jQuery from automatically setting the Content-Type header
        processData: false, // Set to false to prevent jQuery from processing the data (the FormData object is already properly formatted)
        success: function(response) {
            console.log('Pertanyaan berhasil diperbarui');

            // Store the success message in a variable
            var successMessage = 'Pertanyaan berhasil diperbarui';

            // Remove any existing success message container
            $('#success-message').remove();

            // Create a new success message container
            var successElement = $('<div id="success-message" class="alert alert-success position-fixed top-0 end-0 m-3" style="max-width: 300px; z-index: 1050;"></div>');
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

            dataTable.ajax.reload(); // Update DataTable with updated data
            $('#editModalWithImage').modal('hide'); // Close the edit modal
        },
        error: function(xhr, status, error) {
            console.error('Gagal memperbarui pertanyaan:', error);
            // Display an error message or take appropriate action
        }
    });
});




                    $(document).on('click', '.deleteBtn', function() {
                          var questionId = $(this).data('id');
                          var quizId = $(this).data('quiz');

                          // Show a confirmation dialog before deleting
                          if (confirm("Apakah Anda yakin ingin menghapus pertanyaan ini?")) {
                              // Perform the delete operation
                              $.ajax({
                                  url: "{{ route('question.delete', ['quiz' => ':quiz']) }}".replace(':quiz', quizId),
                                  method: "POST",
                                  data: {
                                      _token: '{{ csrf_token() }}',
                                      _method: 'DELETE', // Set the request method to DELETE
                                      id: questionId
                                  },
                                  success: function(response) {
                                      console.log('Pertanyaan berhasil dihapus');
                                      // Optionally, you can remove the row from the DataTable
                                      dataTable.row($(this).closest('tr')).remove().draw(false);
                                      // Show a success message
                                      alert('Pertanyaan berhasil dihapus!');
                                  },
                                  error: function(xhr, status, error) {
                                      console.error('Gagal menghapus pertanyaan:', error);
                                      // Show an error message
                                      alert('Terjadi kesalahan saat menghapus pertanyaan. Silakan coba lagi.');
                                  }
                              });
                          }
                      });
                  });

          </script>

          <!-- ========== End-of-ajax ========== -->

@endsection