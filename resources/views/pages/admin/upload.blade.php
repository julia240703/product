@extends('layouts.appAdmin')
<title>Upload File</title>
   
@section('content')
 <!-- ========== section start ========== -->
        <div class="container-fluid">
          <!-- ========== title-wrapper start ========== -->
          <div class="title-wrapper pt-30">
            <div class="row align-items-center">
              <div class="col-md-6">
                <div class="title mb-30">
                  <h2>Upload File</h2>
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
                        Upload
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
      <div class="col-lg-6">
        <div class="icon-card mb-4">
          <div class="card-body text-center">
            <h5 class="mb-4">Upload Gambar</h5>
            <form action="{{ route('admin.uploadimage') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <img id="imagePreview" src="" alt="Image Preview" style="display: block; margin: 0 auto; width: 150px; height: 150px; display: none;">
                <input class="form-control mb-2 mt-4" type="file" name="image" accept="image/*" required onchange="previewImage(event)">
                <input class="form-control mb-4" type="text" name="image_name" placeholder="Masukkan Nama Gambar" required>
                <button class="btn btn-success" type="submit">Upload</button>
            </form>
            </div>
          </div>
        </div>
      </div>
      <div class="row justify-content-center">
          @foreach($files as $file)
          <div class="col-lg-3 mb-4">
              <div class="icon-card h-100">
                  <div class="card-body text-center">
                      <img src="{{ asset(str_replace('public', 'storage', $file)) }}" alt="Image Preview" class="img-fluid" style="max-width: 150px; max-height: 150px;">
                      <p class="card-text mt-2">{{ basename($file) }}</p>
                      <button class="btn btn-danger btn-sm mt-3" onclick="deleteImage('{{ basename($file) }}')">Delete</button>
                  </div>
              </div>
          </div>
          @endforeach
      </div>
    </div>



<script>
function previewImage(event) {
  const fileInput = event.target;
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
}

function deleteImage(filename) {
    if (confirm("Apakah Anda yakin ingin menghapus gambar ini?")) {
        $.ajax({
            url: '{{ route('image.delete') }}',
            type: 'POST',
            data: JSON.stringify({ filename: filename }),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Image deleted successfully, show success message
                alert('Gambar berhasil dihapus!');
                
                // Reload the page to refresh the image gallery after deletion
                location.reload();
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                alert('Gagal menghapus gambar. Silakan coba lagi.');
            }
        });
    }
}

</script>
    
    
@endsection