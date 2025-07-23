@extends('layouts.appAdmin')
<title>Dashboard Utama</title>

@section('content')
 <!-- ========== section start ========== -->
        <div class="container-fluid">
          <!-- ========== title-wrapper start ========== -->
          <div class="title-wrapper pt-30">
            <div class="row align-items-center">
              <div class="col-md-6">
                <div class="title">
                  <h2>Dashboard Utama</h2>
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
                  <option value="{{ route('admin.home') }}">Semua</option>
                  <option value="{{ route('admin.home', ['duration' => 'today']) }}" {{ $selectedOption === 'today' ? 'selected' : '' }}>Hari Ini</option>
                  <option value="{{ route('admin.home', ['duration' => '7days']) }}" {{ $selectedOption === '7days' ? 'selected' : '' }}>7 Hari</option>
                  <option value="{{ route('admin.home', ['duration' => '30days']) }}" {{ $selectedOption === '30days' ? 'selected' : '' }}>30 Hari</option>
                  <option value="{{ route('admin.home', ['duration' => '60days']) }}" {{ $selectedOption === '60days' ? 'selected' : '' }}>60 Hari</option>
                  <option value="{{ route('admin.home', ['duration' => '90days']) }}" {{ $selectedOption === '90days' ? 'selected' : '' }}>90 Hari</option>
                </select>
              </div>
            </div>
          </div>
          <!-- ========== title-wrapper end ========== -->

          <div class="row">
            <div class="col-xl-3 col-lg-4 col-sm-6">
              <a href="{{ route('admin.users') }}" class="icon-card mb-30 custom-hover">
                <div class="icon success">
                  <i class="lni lni-users"></i>
                </div>
                <div class="content text-end ms-auto">
                  <h6 class="mb-10">Jumlah Peserta</h6>
                  <h3 class="text-bold mb-10">{{ $totalUserCount }}</h3>
                  <!-- <p class="text-sm text-success">
                    <i class="lni lni-arrow-up"></i> +2.00%
                  </p> -->
                </div>
              </a>
            <!-- End Icon Cart -->
            </div>

            <!-- End Col -->
            <div class="col-xl-3 col-lg-4 col-sm-6">
              <a href="{{ route('followed.user') }}" class="icon-card mb-30 custom-hover">
                <div class="icon primary">
                  <i class="lni lni-user"></i>
                </div>
                <div class="content text-end ms-auto">
                  <h6 class="mb-10">Telah direspons</h6>
                  <h3 class="text-bold mb-10">{{ $followingCount }}</h3>
                  <!-- <p class="text-sm text-success">
                    <i class="lni lni-arrow-up"></i> +5.45%
                  </p> -->
                </div>
              </a>
              <!-- End Icon Cart -->
            </div>
            <!-- End Col -->
            <div class="col-xl-3 col-lg-4 col-sm-6">
              <a href="{{ route('user.followingCount') }}" class="icon-card mb-30 custom-hover">
                <div class="icon orange">
                  <i class="lni lni-user"></i>
                </div>
                <div class="content text-end ms-auto">
                  <h6 class="mb-10">Anda respons</h6>
                  <h3 class="text-bold mb-10">{{ $userFollowingCount }}</h3>
                  <!-- <p class="text-sm text-danger">
                    <i class="lni lni-arrow-down"></i> -2.00%
                    <span class="text-gray">Expense</span>
                  </p> -->
                </div>
              </a>
              <!-- End Icon Cart -->
            </div>
            <!-- End Col -->
            <div class="col-xl-3 col-lg-4 col-sm-6">
              <a href="{{ route('user.candidate') }}" class="icon-card mb-30 custom-hover">
                <div class="icon purple">
                  <i class="lni lni-user"></i>
                </div>
                <div class="content text-end ms-auto">
                  <h6 class="mb-10">Calon Kandidat</h6>
                  <h3 class="text-bold mb-10">{{ $passedNotFollowingCount }}</h3>
                  <!-- <p class="text-sm text-danger">
                    <i class="lni lni-arrow-down"></i> -25.00%
                    <span class="text-gray"> Earning</span>
                  </p> -->
                </div>
              </a>
              <!-- End Icon Cart -->
            </div>
            <!-- End Col -->
          </div>
          <!-- End Row -->

          <div class="row">
            <div class="col-xl-6 col-lg-8 col-sm-12">
              <a href="{{ route('manage.exam') }}" class="icon-card mb-30 custom-hover">
                <div class="icon">
                  <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="content text-end ms-auto">
                  <h6 class="mb-10">Jumlah Subtes</h6>
                  <h3 class="text-bold mb-10">{{ $quizCount }}</h3>
                  <!-- <p class="text-sm text-success">
                    <i class="lni lni-arrow-up"></i> +2.00%
                  </p> -->
                </div>
              </a>
            <!-- End Icon Cart -->
            </div>
            <div class="col-xl-6 col-lg-8 col-sm-12">
              <a href="{{ route('manage.examQuestion') }}" class="icon-card mb-30 custom-hover">
              <div class="content text-start me-auto">
                  <h6 class="mb-10">Jumlah Pertanyaan</h6>
                  <h3 class="text-bold mb-10">{{ $questionCount }}</h3>
                  <!-- <p class="text-sm text-success">
                    <i class="lni lni-arrow-up"></i> +2.00%
                  </p> -->
                </div>
                <div class="icon orange">
                <i class="fas fa-clipboard-question"></i>                
              </div>
              </a>
            <!-- End Icon Cart -->
            </div>
          
            <div class="col-lg-12">
              <div class="card-style mb-30">
                <div class="title d-flex flex-wrap justify-content-between">
                  <div class="left">
                    <!-- <h6 class="text-medium mb-10">Jumlah Pendaftar 30 Hari Terakhir</h6>
                    <h3 class="text-bold"></h3> -->
                  </div>
                  <div class="right">
                    <div class="select-style-1">
                      <!-- <div class="select-position select-sm">
                        <select class="light-bg">
                          <option value="">30 Hari</option>
                        </select>
                      </div> -->
                    </div>
                    <!-- end select -->
                  </div>
                </div>
                <!-- End Title -->
                  <div class="chart">
                      <canvas id="linechart" style="width: 100%; height: 400px"></canvas>
                  </div>
                <!-- End Chart -->
              </div>
            </div>
            <!-- End Col -->
          </div>
          <!-- End Row -->


          <script>

            //  Start of linechart//

              // Get the user count data from the server-side
              var labels = <?php echo json_encode($labels); ?>;
              var counts = <?php echo json_encode($counts); ?>;

              // Create the line chart
              var ctx = document.getElementById('linechart').getContext('2d');
              var myChart = new Chart(ctx, {
                  type: 'line',
                  data: {
                      labels: labels,
                      datasets: [{
                          label: 'Jumlah Peserta',
                          data: counts,
                          fill: false,
                          borderColor: '#d1393A',
                          tension: 0.1
                      }]
                  },
                  options: {
                      scales: {
                          y: {
                              beginAtZero: true,
                              precision: 0,
                              callback: function (value) {
                                  if (Number.isInteger(value)) {
                                      return value;
                                  }
                              }
                          }
                      }
                  } 
              });

              //  END of linechart //
              
          </script>



@endsection