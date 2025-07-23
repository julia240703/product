<!DOCTYPE html>
@extends('layouts.appAdmin')
<title>Kontrol Database</title>
   
@section('content')
 <!-- ========== section start ========== -->
        <div class="container-fluid">
          <!-- ========== title-wrapper start ========== -->
          <div class="title-wrapper pt-30">
            <div class="row align-items-center">
              <div class="col-md-6">
                <div class="title mb-30">
                  <h2>Panel Kendali</h2>
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
                        Control-panel
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

          <div class="row justify-content-center">
              <div class="col-lg-4">
                  <div class="icon-card mb-4 h-100"> <!-- Added h-100 class -->
                      <div class="card-body text-center">
                          <h5 class="mb-4">Hapus Cache Jawaban</h5>
                          <h6 class="mb-4 text-center text-danger">Jumlah Jawaban: {{ $recordCount }} ({{ $totalSizeHumanReadable }})</h6>
                          <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#resetConfirmationModal">
                              Reset Tabel Jawaban
                          </button>
                      </div>
                  </div>
              </div>
              <div class="col-lg-4">
                  <div class="icon-card mb-4 h-100"> <!-- Added h-100 class -->
                      <div class="card-body text-center">
                          <h5 class="mb-4">Kontrol Halaman Daftar</h5>
                          @php
                            $filePath = public_path('txt/registration.txt'); // Update the file path
                            $registrationEnabled = File::get($filePath) === "1";
                          @endphp

                          @if($registrationEnabled)
                            <h6 class="mb-4 text-center text-success">Pendaftaran saat ini dibuka</h6>
                          @else
                            <h6 class="mb-4 text-center text-danger">Pendaftaran saat ini ditutup</h6>
                          @endif
                          <form action="{{ route('admin.toggle.registration') }}" method="post">
                              @csrf
                              @if($registrationEnabled)
                                  <button type="submit" class="btn btn-danger">Tutup Pendaftaran</button>
                              @else
                                  <button type="submit" class="btn btn-success">Buka Pendaftaran</button>
                              @endif
                          </form>
                      </div>
                  </div>
              </div>
              <h3 class="mt-4 text-center">Pengumuman</h3>
              <div id="viewContent">
                  <div class="card mt-4">
                      <div class="card-body">
                      {!! $editableContent !!}
                      </div>
                  </div>
                  <button id="editButton" class="btn btn-success mt-4 float-end" onclick="toggleEdit()">Edit Content</button>
              </div>

              <div id="editContent" style="display: none;">
                  <form method="POST" action="{{ route('admin.updateAnnouncement') }}">
                      @csrf
                      <div class="mt-4">
                          <textarea class="form-control" name="content" contenteditable="true">{{ $editableContent }}</textarea>
                      </div>
                      <button type="submit" class="btn btn-success float-end mt-4">Update Content</button>
                      <button type="button" class="btn btn-secondary me-2 float-end m-4" onclick="cancelEdit()">Cancel</button>
                  </form>
              </div>
          </div>
        </div>

        <!-- Modal Reset Answers-->
        <div class="modal fade" id="resetConfirmationModal" tabindex="-1" aria-labelledby="resetConfirmationModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="resetConfirmationModalLabel">Konfirmasi Reset</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Yakin ingin mereset ulang tabel jawaban?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                        <form action="{{ route('answers.reset') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger">Reset</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

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
