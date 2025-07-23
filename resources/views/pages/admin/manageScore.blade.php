@extends('layouts.appAdmin')
<title>Kelola Nilai</title>
   
@section('content')
 <!-- ========== section start ========== -->
        <div class="container-fluid">
          <!-- ========== title-wrapper start ========== -->
          <div class="title-wrapper pt-30">
            <div class="row align-items-center">
              <div class="col-md-6">
                <div class="title mb-30">
                  <h2>Kelola Nilai</h2>
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
            <div class="col-lg-3">
              <div class="icon-card mb-4">
                <div class="card-body text-center">
                  <h5 class="mb-4">Rata Rata Nilai Kelulusan</h5>
                    <div class="mb-4">
                      <h4 class="mb-4 text-center text-danger">{{ $avgScore }}</h4>
                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#editModal">
                            Ubah
                        </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="row justify-content-center">
            <div class="col-lg-3">
              <div class="icon-card mb-4">
                <div class="card-body text-center">
                  <h5 class="mb-4">Baik Sekali</h5>
                    <div class="mb-4">
                      <h4 class="mb-4 text-center text-danger">>={{ $baikSekali }}</h4>
                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#baikSekaliModal">
                            Ubah
                        </button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-3">
              <div class="icon-card mb-4">
                <div class="card-body text-center">
                  <h5 class="mb-4">Baik</h5>
                    <div class="mb-4">
                      <h4 class="mb-4 text-center text-danger"><={{ $baik_ba }}, >={{ $baik_bb }}</h4>
                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#baikModal">
                            Ubah
                        </button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-3">
              <div class="icon-card mb-4">
                <div class="card-body text-center">
                  <h5 class="mb-4">Cukup</h5>
                    <div class="mb-4">
                      <h4 class="mb-4 text-center text-danger"><={{ $cukup_ba }}, >={{ $cukup_bb }}</h4>
                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#cukupModal">
                            Ubah
                        </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>


          
<!-- Rata Rata Nilai -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
          <form action="{{ route('admin.updateAvgScore') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Rata Rata Nilai Kelulusan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  
                  <div class="form-outline mb-3">
                    <label class="text-bold mb-1">Nilai <span class="text-red mt-2">*</span></label>
                    <input class="form-control mb-4 text-center" type="text" name="new_avg_score" value="{{ $avgScore }}" required>
                  </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                    <button type="submit" class="btn btn-success">Ubah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Baik Sekali -->
<div class="modal fade" id="baikSekaliModal" tabindex="-1" aria-labelledby="baikSekaliModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
          <form action="{{ route('admin.updateBaikSekali') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="editbaikSekaliLabel">Edit Nilai Untuk Pesan Baik Sekali</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                  <div class="form-outline mb-3">
                    <label class="text-bold mb-1">Nilai <span class="text-red mt-2">*</span></label>
                    <input class="form-control mb-4 text-center" type="text" name="new_baik_sekali" value="{{ $baikSekali }}" required>
                  </div>
                  
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                    <button type="submit" class="btn btn-success">Ubah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Baik -->
<div class="modal fade" id="baikModal" tabindex="-1" aria-labelledby="baikModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
          <form action="{{ route('admin.updateBaik') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="editbaikLabel">Edit Nilai Untuk Pesan Baik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                  <div class="form-outline mb-3">
                      <label class="text-bold mb-1">Nilai Batas Atas <span class="text-red mt-2">*</span></label>
                      <input class="form-control mb-4 text-center" type="text" name="new_baik_ba" value="{{ $baik_ba }}" required>
                  </div>

                  <div class="form-outline mb-3">
                      <label class="text-bold mb-1">Nilai Batas Bawah <span class="text-red mt-2">*</span></label>
                      <input class="form-control mb-4 text-center" type="text" name="new_baik_bb" value="{{ $baik_bb }}" required>
                  </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                    <button type="submit" class="btn btn-success">Ubah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cukup -->
<div class="modal fade" id="cukupModal" tabindex="-1" aria-labelledby="cukupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
          <form action="{{ route('admin.updateCukup') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="editcukupLabel">Edit Nilai Untuk Pesan Baik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                  <div class="form-outline mb-3">
                      <label class="text-bold mb-1">Nilai Batas Atas <span class="text-red mt-2">*</span></label>
                      <input class="form-control mb-4 text-center" type="text" name="new_cukup_ba" value="{{ $cukup_ba }}" required>
                  </div>

                  <div class="form-outline mb-3">
                      <label class="text-bold mb-1">Nilai Batas Bawah <span class="text-red mt-2">*</span></label>
                      <input class="form-control mb-4 text-center" type="text" name="new_cukup_bb" value="{{ $cukup_bb }}" required>
                  </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                    <button type="submit" class="btn btn-success">Ubah</button>
                </div>
            </form>
        </div>
    </div>
</div>



    
@endsection