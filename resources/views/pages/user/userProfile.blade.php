@extends('layouts.app')
<title>Profile</title>
   
@section('content')
 <!-- ========== section start ========== -->
        <div class="container-fluid">
          <!-- ========== title-wrapper start ========== -->
          <div class="title-wrapper pt-30">
            <div class="row align-items-center">
              <div class="col-md-6">
                <div class="title mb-30">
                  <h2>Profile</h2>
                </div>
              </div>
              <!-- end col -->
              <div class="col-md-6">
                <div class="breadcrumb-wrapper mb-30">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item">
                        <a href="#0"></a>
                      </li>
                      <li class="breadcrumb-item active" aria-current="page">
                        Profile
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
    <div class="row">
      <div class="col-lg-4 mb-4">
        <div class="icon-card mb-4">
          <div class="card-body text-center">
            <img src="{{ asset('storage/files/photo/' . $profile->photo)}}" alt="avatar"
              class="rounded-circle" style="width: 150px; height: 150px;">
            <h5 class="my-3">{{ $profile->name }}</h5>
            <p class="text-muted mb-4">{{ $profile->education }}</p>
            <a href="{{ route('profile.edit') }}" class="btn btn-success">Edit Profile</a>
            <!-- <p class="text-muted mb-4">Bay Area, San Francisco, CA</p> -->
            <div class="d-flex justify-content-center mb-2">
            </div>
          </div>
        </div>
        <div class="icon-card  mb-4 mb-lg-0">
          <div class="card-body p-0">
            <p class="mb-4 text-center fw-bold">Informasi Kontak</p>
              <ul class="list-group list-group-flush rounded-3">
                  <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap mb-2">
                  <i class="fa-solid fa-envelope me-2"></i>
                      <div class="d-flex align-items-center">
                          <span class="text" style="word-break: break-all;">{{ $profile->email }}</span>
                      </div>
                  </li>
                  <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap mb-2">
                  <i class="fa-solid fa-mobile me-2"></i>
                      <div class="d-flex align-items-center">
                          <span class="text" style="word-break: break-all;">{{ $profile->mobile_number }}</span>
                      </div>
                  </li>
                  <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap mb-2">
                  <i class="fa-solid fa-phone me-2"></i>
                      <div class="d-flex align-items-center">
                          <span class="text" style="word-break: break-all;">{{ $profile->landline_phone }}</span>
                      </div>
                  </li>
              </ul>
          </div>
        </div>
      </div>
      <div class="col-lg-8">
        <div class="icon-card mb-4">
          <div class="card-body">
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0 text-bold">No. KTP</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0">{{ $profile->national_id }}</p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0 text-bold">Alamat KTP</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0">{{ $profile->address }}</p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0 text-bold">Alamat Domisili</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0">{{ $profile->domicile }}</p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0 text-bold">Tanggal Lahir</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0">{{ $profile->birthdate }}</p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0 text-bold">Jenis Kelamin</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0">{{ $profile->gender }}</p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0 text-bold">Status Pernikahan</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0">{{ $profile->marital_status }}</p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0 text-bold">Agama</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0">{{ $profile->religion }}</p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0 text-bold">Posisi yang dilamar</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0">{{ $profile->applied_position }}</p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0 text-bold">Cabang yang dilamar</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0">{{ $profile->branch->city}} - {{ $profile->branch->location}}</p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0 text-bold">Status Pekerjaan</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0">{{ $profile->job_status }}</p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0 text-bold">Siap Bekerja</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0">{{ $profile->able_to_work }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
      <div class="row">
          <div class="col">
            <div class="icon-card">
              <div class="card-body text-center">
                <h3 class="mb-4">Curriculum Vitae</h3>
                    @if ($profile->cv)
                            <form action="{{ route('profile.deleteCV', ['id' => $profile->id]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger mb-3" data-bs-toggle="modal" data-bs-target="#confirmationModal">Hapus CV</button>
                            
                            <iframe src=" {{ asset('storage/files/cv/' . $profile->cv)}} " width="100%" height="600px"></iframe>

                            <!-- Confirmation Modal -->
                            <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="confirmationModalLabel">Confirmation</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Apakah anda yakin ingin menghapus file CV anda?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                                            <button type="submit" class="btn btn-success" onclick="deleteCv()">Ya</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </form>

                            <!-- JavaScript -->
                            <script>
                                function deleteCv() {
                                    document.getElementById('deleteCvForm').submit();
                                }
                            </script>
                    @else
                        <div class="text-danger">Anda belum mengupload CV</div>
                    @endif
              </div>
            </div>
          </div>
        </div>
  </div>
@endsection