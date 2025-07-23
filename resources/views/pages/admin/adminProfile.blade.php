@extends('layouts.appAdmin')
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
                        <a href="#0">Admin</a>
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

    <div class="row justify-content-center">
      <div class="col-lg-4">
        <div class="icon-card mb-4">
          <div class="card-body text-center">
            <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3.webp" alt="avatar"
              class="rounded-circle img-fluid" style="width: 150px; height: 150px;">
            <h5 class="my-3">Admin</h5>
            <p class="text-muted mb-3">Superuser Account</p>
            <a href="{{ route('admin.changePassword') }}" class="btn btn-success">Ubah Password</a>
            <div class="d-flex justify-content-center mb-2">
            </div>
          </div>
        </div>
      </div>
    </div>
    
@endsection