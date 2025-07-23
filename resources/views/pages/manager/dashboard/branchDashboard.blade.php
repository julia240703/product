@extends('layouts.appManager')
<title>Dashboard Cabang</title>
   
@section('content')
 <!-- ========== section start ========== -->
        <div class="container-fluid">
          <!-- ========== title-wrapper start ========== -->
          <div class="title-wrapper pt-30">
            <div class="row align-items-center">
              <div class="col-md-6">
                <div class="title">
                  <h2>Dashboard Cabang</h2>
                </div>
              </div>
              <!-- end col -->
              <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item">
                        <a href="#0">Manager</a>
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
                          <option value="{{ route('manager.branch') }}">Semua</option>
                          <option value="{{ route('manager.branch', ['duration' => '1days']) }}" {{ $selectedOption === '1days' ? 'selected' : '' }}>Hari Ini</option>
                          <option value="{{ route('manager.branch', ['duration' => '7days']) }}" {{ $selectedOption === '7days' ? 'selected' : '' }}>7 Hari</option>
                          <option value="{{ route('manager.branch', ['duration' => '30days']) }}" {{ $selectedOption === '30days' ? 'selected' : '' }}>30 Hari</option>
                          <option value="{{ route('manager.branch', ['duration' => '60days']) }}" {{ $selectedOption === '60days' ? 'selected' : '' }}>60 Hari</option>
                          <option value="{{ route('manager.branch', ['duration' => '90days']) }}" {{ $selectedOption === '90days' ? 'selected' : '' }}>90 Hari</option>
                      </select>
                  </div>
              </div>

              <div class="col-md-4 select-style-1">
                  <div class="select-position select-sm">
                      <select id="jobPositionSelect" class="bg-white form-select" onchange="location = this.value;">
                          <option value="{{ route('manager.branch', ['duration' => $selectedOption, 'job_position' => '']) }}">Semua Posisi</option>
                          @foreach($jobPositions as $position)
                              <option value="{{ route('manager.branch', ['duration' => $selectedOption, 'job_position' => $position->position]) }}" {{ $selectedJobPosition === $position->position ? 'selected' : '' }}>{{ $position->position }}</option>
                          @endforeach
                      </select>
                  </div>
              </div>
          </div>
          <!-- end row -->
      </div>

          <!-- ========== title-wrapper end ========== -->
          <div class="row">
            <div class="col-xl-4 col-lg-6 col-sm-12">
              <a href="{{ route('manager.jktbranch') }}" class="icon-card mb-30 custom-hover">
                <div class="icon success">
                  <i class="fa-solid fa-building"></i>
                </div>
                <div class="content text-end ms-auto">
                  <h6 class="mb-10">Jakarta</h6>
                  <h3 class="text-bold mb-10">{{$userCountJakarta}}</h3>
                  <!-- <p class="text-sm text-success">
                    <i class="lni lni-arrow-up"></i> +2.00%
                  </p> -->
                </div>
              </a>
            <!-- End Icon Cart -->
            </div>

            <!-- End Col -->
            <div class="col-xl-4 col-lg-6 col-sm-12">
              <a href="{{ route('manager.tgrbranch') }}" class="icon-card mb-30 custom-hover">
                <div class="icon primary">
                  <i class="fa-solid fa-building"></i>
                </div>
                <div class="content text-end ms-auto">
                  <h6 class="mb-10">Tangerang</h6>
                  <h3 class="text-bold mb-10">{{$userCountTangerang}}</h3>
                  <!-- <p class="text-sm text-success">
                    <i class="lni lni-arrow-up"></i> +5.45%
                  </p> -->
                </div>
              </a>
              <!-- End Icon Cart -->
            </div>
            <!-- End Col -->
            <div class="col-xl-4 col-lg-6 col-sm-12">
              <a href="{{ route('manager.othersbranch') }}" class="icon-card mb-30 custom-hover">
                <div class="icon orange">
                  <i class="fa-solid fa-building"></i>
                </div>
                <div class="content text-end ms-auto">
                  <h6 class="mb-10">Luar Kota</h6>
                  <h3 class="text-bold mb-10">{{$userCountLuarKota}}</h3>
                  <!-- <p class="text-sm text-danger">
                    <i class="lni lni-arrow-down"></i> -2.00%
                    <span class="text-gray">Expense</span>
                  </p> -->
                </div>
              </a>
              <!-- End Icon Cart -->
            </div>
            <!-- End Col -->
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
                      <canvas id="piechart" style="width: 100%; height: 400px"></canvas>
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
                    <canvas id="barchart" style="width: 100%; height: 400px"></canvas>
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

            const ctx = document.getElementById('piechart').getContext('2d');
            const piechart = new Chart(ctx, {
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

            const ctx = document.getElementById('barchart').getContext('2d');
            const barchart = new Chart(ctx, {
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