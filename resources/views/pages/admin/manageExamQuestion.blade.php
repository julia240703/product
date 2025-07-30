<!DOCTYPE html>
@extends('layouts.appAdmin')
<title>Kelola Pertanyaan Psikotes</title>
 
@section('content')
 <!-- ========== section start ========== -->
        <div class="container-fluid">
          <!-- ========== title-wrapper start ========== -->
          <div class="title-wrapper pt-30">
            <div class="row align-items-center">
              <div class="col-md-6">
                <div class="title mb-30">
                  <h2>Kelola Aksesoris</h2>
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
                        Accessories
                      </li>
                    </ol>
                  </nav>
                </div>
              </div>

              <div id="viewContent">
                  <div class="card mb-4">
                      <div class="card-body">
                      {!! $editableContent !!}
                      </div>
                  </div>
                  <button id="editButton" class="btn btn-success mb-4 float-end" onclick="toggleEdit()">Edit Content</button>
              </div>

              <div id="editContent" style="display: none;">
                  <form method="POST" action="{{ route('manage.examQuestion.Update') }}">
                      @csrf
                      <div class="mb-4">
                          <textarea class="form-control" name="content" contenteditable="true">{{ $editableContent }}</textarea>
                      </div>
                      <button type="submit" class="btn btn-success mb-4 float-end">Update Content</button>
                      <button type="button" class="btn btn-secondary mb-4 me-2 float-end" onclick="cancelEdit()">Cancel</button>
                  </form>
              </div>
              <!-- end col -->
            </div>
            <!-- end row -->
          </div>

          <!-- ========== title-wrapper end ========== -->

<div class="row">

  @foreach ($quizzes as $quiz)
    @include('layouts.card', ['quiz' => $quiz])
  @endforeach
  
</div>
          <!-- End Row -->

<script>
    function toggleEdit() {
        document.getElementById('viewContent').style.display = 'none';
        document.getElementById('editContent').style.display = 'block';
    }

    function cancelEdit() {
        document.getElementById('editContent').style.display = 'none';
        document.getElementById('viewContent').style.display = 'block';
    }
</script>

<script>
    tinymce.init({
      selector: 'textarea',
      plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
      toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | align lineheight | tinycomments | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
      tinycomments_mode: 'embedded',
      tinycomments_author: 'Author name',
      mergetags_list: [
        { value: 'First.Name', title: 'First Name' },
        { value: 'Email', title: 'Email' },
      ],
      ai_request: (request, respondWith) => respondWith.string(() => Promise.reject("See docs to implement AI Assistant"))
    });
  </script>

@endsection