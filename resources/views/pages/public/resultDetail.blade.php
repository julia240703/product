@extends('layouts.appPublic')
<title>Hasil Psikotes: {{ $profile->name }}</title>
 
@section('content')
<!-- ========== section start ========== -->
    <div class="container-fluid">
        <!-- ========== title-wrapper start ========== -->
        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                <div class="col">
                    <div class="title mb-30">
                        <h2>Hasil Psikotes</h2>
                    </div>
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- ========== title-wrapper end ========== -->
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="icon-card mb-4 h-100">
            <div class="card-body text-center">
                <img src="{{ asset('storage/files/photo/' . $profile->photo)}}" alt="avatar" class="rounded-circle img-fluid" style="width: 150px; height: 150px;">
                <h5 class="my-3">{{ $profile->name }}</h5>
                <p class="text-muted mb-4">{{ $profile->education }}</p>
                <!-- <a href="{{ route('managerusers.show', ['user' => $user->id]) }}" class="btn btn-success">Lihat Profile</a> -->
                @if (Auth::check() && $profile->follow_up === Auth::user()->name)
                  <!-- Show unfollow button -->
                  <form action="{{ route('manager.unfollow', ['user' => $profile->id]) }}" method="POST">
                    @csrf
                    @method('POST')
                        <button type="submit" class="btn btn-danger mt-4">Unfollow</button>
                            </form>
                @elseif ($profile->follow_up)
                    <!-- Show follow-up message -->
                            <p class="text-red mt-4">Peserta Telah Ditindaklanjuti Oleh: {{ $profile->follow_up }}</p>
                @else
                    <!-- Show follow button -->
                            <!-- <form action="{{ route('manager.follow_up', $profile) }}" method="POST">
                    @csrf
                        <button type="submit" class="btn btn-primary mt-4">Follow up</button>
                    </form> -->
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-8 mb-4">
        <div class="icon-card mb-4 h-100">
            <div class="card-body text-center">
                <div class="chart">
                    <canvas id="linechart" style="width: 100%; height: 400px"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row justify-content-center">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <div class="container">
                    <div class="row">
                        <hr>
                        <?php
                            // Sort the results by quiz name (A to Z)
                            $sortedResults = $user->results->sortBy(function ($result) {
                                return $result->quiz->name;
                            });
                        ?>
                        @foreach ($sortedResults as $result)
                        <div class="col-md-6 text-start mb-4">
                            <p>{{ $result->quiz->name }}</p>
                        </div>

                        <div class="col-md-6 text-end mb-4">
                            <p>{{ $result->score }}</p>
                        </div>
                        <hr>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    // Get the data from the PHP variables passed from the controller
    var labels = @json($labels);
    var scores = @json($scores);

    // Create an array of objects, each containing a label and its corresponding score
    var dataPoints = labels.map((label, index) => ({ label: label, score: scores[index] }));

    // Sort the data points array based on the label in alphabetical order
    dataPoints.sort((a, b) => a.label.localeCompare(b.label));

    // Extract the sorted labels and scores arrays from the sorted data points
    labels = dataPoints.map(dataPoint => dataPoint.label);
    scores = dataPoints.map(dataPoint => dataPoint.score);

    // Create the line chart using Chart.js
    var ctx = document.getElementById('linechart').getContext('2d');
    var lineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Nilai',
                data: scores,
                borderColor: '#D1393A',
                backgroundColor: '#D00000',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

@endsection