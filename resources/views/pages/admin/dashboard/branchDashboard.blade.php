@extends('layouts.appAdmin')
<title>Dashboard Cabang</title>
   
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
            <div class="row mt-3 justify-content-end">
              <div class="col-md-2 select-style-1">
                  <div class="select-position select-sm">
                      <select id="userCountSelect" class="bg-white form-select" onchange="location = this.value;">
                          <option value="{{ route('admin.branch') }}">Semua</option>
                          <option value="{{ route('admin.branch', ['duration' => '1days']) }}" {{ $selectedOption === '1days' ? 'selected' : '' }}>Hari Ini</option>
                          <option value="{{ route('admin.branch', ['duration' => '7days']) }}" {{ $selectedOption === '7days' ? 'selected' : '' }}>7 Hari</option>
                          <option value="{{ route('admin.branch', ['duration' => '30days']) }}" {{ $selectedOption === '30days' ? 'selected' : '' }}>30 Hari</option>
                          <option value="{{ route('admin.branch', ['duration' => '60days']) }}" {{ $selectedOption === '60days' ? 'selected' : '' }}>60 Hari</option>
                          <option value="{{ route('admin.branch', ['duration' => '90days']) }}" {{ $selectedOption === '90days' ? 'selected' : '' }}>90 Hari</option>
                      </select>
                  </div>
              </div>

              <div class="col-md-4 select-style-1">
                  <div class="select-position select-sm">
                      <select id="jobPositionSelect" class="bg-white form-select" onchange="location = this.value;">
                          <option value="{{ route('admin.branch', ['duration' => $selectedOption, 'job_position' => '']) }}">Semua Posisi</option>
                          @foreach($jobPositions as $position)
                              <option value="{{ route('admin.branch', ['duration' => $selectedOption, 'job_position' => $position->position]) }}" {{ $selectedJobPosition === $position->position ? 'selected' : '' }}>{{ $position->position }}</option>
                          @endforeach
                      </select>
                  </div>
              </div>
          </div>
          <!-- end row -->
      </div>

  
          <div class="row">
  <div class="col-xl-4 col-lg-6 col-sm-12">
    <a href="{{ route('manage.branch') }}" class="icon-card mb-30 custom-hover">
      <div class="icon orange">
        <i class="fa-solid fa-city"></i>
      </div>
      <div class="content text-end ms-auto">
        <h6 class="mb-10">Jumlah Cabang</h6>
        <h3 class="text-bold mb-10">{{$branchCount}}</h3>
      </div>
    </a>
  </div>

  <div class="col-xl-4 col-lg-6 col-sm-12">
    <a href="{{ route('job.position') }}" class="icon-card mb-30 custom-hover">
      <div class="icon purple">
        <i class="fa-solid fa-briefcase"></i>
      </div>
      <div class="content text-end ms-auto">
        <h6 class="mb-10">Jumlah Test Ride</h6>
        <h3 class="text-bold mb-10">{{$jobPositionCount}}</h3>
      </div>
    </a>
  </div>

  <div class="col-xl-4 col-lg-6 col-sm-12">
    <a href="{{ route('job.position') }}" class="icon-card mb-30 custom-hover">
      <div class="icon purple">
        <i class="fa-solid fa-briefcase"></i>
      </div>
      <div class="content text-end ms-auto">
        <h6 class="mb-10">Simulasi Kredit</h6>
        <h3 class="text-bold mb-10">{{$jobPositionCount}}</h3>
      </div>
    </a>
  </div>
</div>

          <div class="row">
            <div class="col-md-7 d-flex">
              <div class="card-style mb-30">
                <div class="title d-flex flex-wrap justify-content-between">
                  <div class="left">
                    <!-- <h6 class="text-medium mb-5">Jumlah Peserta Berdasarkan Cabang 30 Hari Terakhir</h6> -->
                    <!-- <h3 class="text-bold"></h3> -->
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
                      <canvas id="userChartMonthly" style="width: 100%; height: 400px"></canvas>
                  </div>
                <!-- End Chart -->
              </div>
            </div>
            <!-- End Col -->
            <div class="col-md-5 d-flex">
              <div class="card-style mb-30 flex-fill">
                <div
                  class="
                    title
                    d-flex
                    flex-wrap
                    align-items-center
                    justify-content-between
                  "
                >
                  <div class="left">
                    <!-- <h6 class="text-medium mb-5">Jumlah Peserta Berdasarkan Cabang 7 Hari Terakhir</h6> -->
                    <!-- <h3 class="text-bold"></h3> -->
                  </div>
                  <div class="right">
                    <div class="select-style-1">
                      <!-- <div class="select-position select-sm">
                        <select class="light-bg">
                          <option value="">Yearly</option>
                        </select>
                      </div> -->
                    </div>
                    <!-- end select -->
                  </div>
                </div>
                <!-- End Title -->
                <div class="chart">
                    <canvas id="userChartWeekly" style="width: 100%; height: 400px"></canvas>
                </div>
                <!-- End Chart -->
              </div>
            </div>
            <!-- End Col -->
          </div>
          <!-- End Row -->
</div>

  <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userCountsJakarta = @json($userCountsJakarta);
            const userCountsTangerang = @json($userCountsTangerang);
            const userCountsLuarKota = @json($userCountsLuarKota);

            const totalUsers = counts => counts.reduce((total, count) => total + count, 0);

            const ctx = document.getElementById('userChartMonthly').getContext('2d');
            const userChartMonthly = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Jakarta', 'Tangerang', 'Luar Kota'],
                    datasets: [{
                        data: [
                            totalUsers(Object.values(userCountsJakarta)),
                            totalUsers(Object.values(userCountsTangerang)),
                            totalUsers(Object.values(userCountsLuarKota))
                        ],
                        backgroundColor: [
                            '#D1393A',
                            '#B6B8BA',
                            '#24262B'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        position: 'bottom'
                    }
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const userCountsJakarta = @json($userCountsJakarta);
            const userCountsTangerang = @json($userCountsTangerang);
            const userCountsLuarKota = @json($userCountsLuarKota);

            const totalUsers = counts => counts.reduce((total, count) => total + count, 0);

            const ctx = document.getElementById('userChartWeekly').getContext('2d');
            const userChartWeekly = new Chart(ctx, {
                type: 'bar', // Change the chart type to 'bar'
                data: {
                    labels: ['Jakarta', 'Tangerang', 'Luar Kota'],
                    datasets: [{
                        data: [
                            totalUsers(Object.values(userCountsJakarta)),
                            totalUsers(Object.values(userCountsTangerang)),
                            totalUsers(Object.values(userCountsLuarKota))
                        ],
                        backgroundColor: [
                            '#D1393A',
                            '#B6B8BA',
                            '#24262B'
                        ],
                        borderColor: [
                            '#D1393A',
                            '#B6B8BA',
                            '#24262B'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    scales: {
                        xAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        });
    </script>




@endsection