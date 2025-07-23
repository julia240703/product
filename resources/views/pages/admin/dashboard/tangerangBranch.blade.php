@extends('layouts.appAdmin')
<title>Dashboard Cabang Tangerang</title>

@section('content')
 <!-- ========== section start ========== -->
        <div class="container-fluid">
          <!-- ========== title-wrapper start ========== -->
          <div class="title-wrapper pt-30">
            <div class="row align-items-center">
              <div class="col-md-6">
                <div class="title">
                  <h2>Dashboard Cabang Tangerang</h2>
                </div>
              </div>
              <!-- end col -->
              <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item">
                        <a href="#0">Admin</a>
                      </li>
                      <li class="breadcrumb-item active" aria-current="page">
                        Home
                      </li>
                    </ol>
                  </nav>
                </div>
              </div>
              <!-- end col -->
            </div>
            <!-- end row -->
          </div>
          <!-- end row -->
            <div class="row mt-3 justify-content-end">
              <div class="col-md-2 select-style-1">
                  <div class="select-position select-sm">
                      <select id="userCountSelect" class="bg-white form-select" onchange="location = this.value;">
                          <option value="{{ route('admin.tgrbranch') }}">Semua</option>
                          <option value="{{ route('admin.tgrbranch', ['duration' => '1days']) }}" {{ $selectedOption === '1days' ? 'selected' : '' }}>Hari Ini</option>
                          <option value="{{ route('admin.tgrbranch', ['duration' => '7days']) }}" {{ $selectedOption === '7days' ? 'selected' : '' }}>7 Hari</option>
                          <option value="{{ route('admin.tgrbranch', ['duration' => '30days']) }}" {{ $selectedOption === '30days' ? 'selected' : '' }}>30 Hari</option>
                          <option value="{{ route('admin.tgrbranch', ['duration' => '60days']) }}" {{ $selectedOption === '60days' ? 'selected' : '' }}>60 Hari</option>
                          <option value="{{ route('admin.tgrbranch', ['duration' => '90days']) }}" {{ $selectedOption === '90days' ? 'selected' : '' }}>90 Hari</option>
                      </select>
                  </div>
              </div>

              <div class="col-md-4 select-style-1">
                  <div class="select-position select-sm">
                      <select id="jobPositionSelect" class="bg-white form-select" onchange="location = this.value;">
                          <option value="{{ route('admin.tgrbranch', ['duration' => $selectedOption, 'job_position' => '']) }}">Semua Posisi</option>
                          @foreach($jobPositions as $position)
                              <option value="{{ route('admin.tgrbranch', ['duration' => $selectedOption, 'job_position' => $position->position]) }}" {{ $selectedJobPosition === $position->position ? 'selected' : '' }}>{{ $position->position }}</option>
                          @endforeach
                      </select>
                  </div>
              </div>
          </div>

          <!-- ========== title-wrapper end ========== -->

          <div class="row">
              @foreach($mergedData as $item)
                  <div class="col-xl-2 col-lg-3 col-sm-6 d-flex">
                    <a href="{{ route('admin.branchDetail', ['branch_location' => $item['branch']->location]) }}"class="icon-card mb-30 custom-hover w-100">
                      <div class="content text-center mx-auto d-flex flex-column justify-content-center align-items-center">
                              <div class="icon orange text-center mx-auto text-danger">
                                  <p>{{ $item['branch']->initials }}</p>
                              </div>
                              <h6 class="mb-10 mt-2">{{ $item['branch']->location }}</h6>
                              <h3 class="text-bold mb-10">{{ $item['userCount'] }}</h3>
                              <!-- <p class="text-sm text-success">
                                  <i class="lni lni-arrow-up"></i> +2.00%
                              </p> -->
                          </div>
                      </a>
                  </div>
              @endforeach  
          </div>



              
@endsection