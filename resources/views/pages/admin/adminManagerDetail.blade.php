@extends('layouts.appAdmin')
<title>{{ $profile->name }}</title>
   
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
                      <li class="breadcrumb-item">
                      <a href="#0">Manager</a>
                      </li>
                      <li class="breadcrumb-item active" aria-current="page">
                      {{ $user->id }}
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
      <div class="col-lg-4">
        <div class="icon-card mb-4">
          <div class="card-body text-center">
            <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3.webp" alt="avatar"
              class="rounded-circle img-fluid" style="width: 150px; height: 150px;">
            <h5 class="my-3">{{ $user->name }}</h5>
            <p class="text-muted mb-4">{{ $user->email }}</p>
            <a href="" class="btn btn-success">Follow Up</a>
            <!-- <p class="text-muted mb-4">Bay Area, San Francisco, CA</p> -->
            <div class="d-flex justify-content-center mb-2">
            </div>
          </div>
        </div>
        <div class="icon-card  mb-4 mb-lg-0">
          <div class="card-body p-0">
          <p class="mb-4 text-center">Pengalaman</p>

            <ul class="list-group list-group-flush rounded-3">
              <li class="list-group-item p-3">
                <p class="mb-0 text-center">{{$profile->pengalaman1}}</p>
              </li>
              <li class="list-group-item p-3">
                <p class="mb-0 text-center">{{$profile->pengalaman2}}</p>
              </li>
              <li class="list-group-item p-3">
                <p class="mb-0 text-center">{{$profile->pengalaman3}}</p>
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
                <p class="mb-0">Nama</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0">{{ $profile->name }}</p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0">Alamat</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0">{{ $profile->address }}</p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0">Tanggal Lahir</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0">{{ $profile->birthdate }}</p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0">No. HP</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0">{{ $profile->phone_number }}</p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0">Jenis Kelamin</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0">{{ $profile->gender }}</p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0">Pendidikan Terakhir</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0">{{ $profile->education }}</p>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <div class="icon-card">
              <div class="card-body">
                <p class="mb-3 text-center text-bold">Curriculum Vitae</p>
                @if ($profile->cv)
                    <iframe src="{{ asset('files/cv/' . $profile->cv) }}" width="100%" height="600px"></iframe>
                @else
                    <div class="text-center text-danger">Peserta belum mengupload CV</div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection