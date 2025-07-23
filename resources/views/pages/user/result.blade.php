@extends('layouts.app')
<title>Hasil Psikotes</title>
 
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

@if ($results->isEmpty())
    <p>Anda Belum Mengerjakan Psikotes</p>
@else
    <div class="row text-center">
        @foreach ($results->sortBy('quiz.name') as $result)
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4 d-flex flex-column">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h5 class="card-title">{{ $result->quiz->name }}</h5>
                        @php
                            // Read the threshold values from text files
                            $baikSekaliThreshold = (int) File::get(public_path('/txt/baikSekali.txt'));
                            $baikBbThreshold = (int) File::get(public_path('/txt/baik_bb.txt'));
                            $baikBaThreshold = (int) File::get(public_path('/txt/baik_ba.txt'));
                            $cukupBbThreshold = (int) File::get(public_path('/txt/cukup_bb.txt'));
                            $cukupBaThreshold = (int) File::get(public_path('/txt/cukup_ba.txt'));

                            $scoreLabel = '';
                            if ($result->score >= $baikSekaliThreshold) {
                                $scoreLabel = 'Baik Sekali';
                            } elseif ($result->score >= $baikBbThreshold && $result->score <= $baikBaThreshold) {
                                $scoreLabel = 'Baik';
                            } elseif ($result->score >= $cukupBbThreshold && $result->score <= $cukupBaThreshold) {
                                $scoreLabel = 'Cukup';
                            } else {
                                $scoreLabel = 'Kurang';
                            }
                        @endphp
                        <p class="card-text">{{ $scoreLabel }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif



@endsection