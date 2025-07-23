@extends('layouts.appAdmin')
<title>Preview {{ $quiz->name }}</title>
 
@section('content')
<!-- ========== section start ========== -->
    <div class="container-fluid">      
        <!-- ========== title-wrapper start ========== -->
        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                <div class="col-sm-6 mx-auto">
                    <div class="title mb-10">
                        <h2 class="mb-4 text-center">{{ $quiz->name }}</h2>
                        <h6>{{ $quiz->description }}</h6>
                    </div>
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- ========== title-wrapper end ========== -->

        <div class="row">
            <div class="col-md-12">
                <div id="timer" class="text-center mb-4"></div>
            </div>
        </div>

@foreach ($questions as $index => $question)
    <div class="col-sm-6 mx-auto">
        <div class="card mb-3">
            <div class="card-body mx-2">
                @if ($question->image)
                    <p class="fw-bolder mb-3 mt-2 text-danger">Soal Nomor {{ $index + 1 }}.</p>
                    <img src="{{ asset('files/question/' . $question->image) }}" alt="Question Image" class="img-fluid mb-4">
                @else
                <p class="fw-normal mb-3 mt-2 text-danger">Soal Nomor {{ $index + 1 }}.</p>
                    @if ($question->question)
                        <p class="fw-bold mb-3 mt-2">{{ $question->question }}</p>
                    @endif
                @endif

                @if ($question->quiz->type === 'Pilihan-Ganda')
                    @php
                        $options = [
                            'A' => [
                                'text' => $question->option_a,
                                'image' => $question->option_a,
                            ],
                            'B' => [
                                'text' => $question->option_b,
                                'image' => $question->option_b,
                            ],
                            'C' => [
                                'text' => $question->option_c,
                                'image' => $question->option_c,
                            ],
                            'D' => [
                                'text' => $question->option_d,
                                'image' => $question->option_d,
                            ],
                        ];

                        if ($question->option_e) {
                            $options['E'] = [
                                'text' => $question->option_e,
                                'image' => $question->option_e,
                            ];
                        }

                        $shuffledOptions = collect($options)->shuffle();
                        $columns = $question->image ? 'col-md-6 col-lg-6' : 'col-md-6 col-lg-12';
                    @endphp

                    <div class="row mt-4">
                        @foreach ($shuffledOptions as $optionKey => $option)
                            <div class="col-md-{{ $columns }}">
                                @php
                                    $originalValue = array_search($option, $options);
                                    $imageExtension = pathinfo($option['image'], PATHINFO_EXTENSION);
                                    $isValidImage = in_array($imageExtension, ['jpeg', 'jpg', 'png']);
                                @endphp

                                <div class="mb-2">
                                    <label class="radio">
                                        <input type="radio" name="answers[{{ $question->id }}]" value="{{ $originalValue }}">
                                        <span class="radio-mark"></span>
                                        @if ($isValidImage)
                                            <div class="image-container mb-4">
                                                <img src="{{ asset('files/question/' . $option['image']) }}" alt="Option {{ $optionKey }} Image" class="img-fluid rounded mb-4">
                                            </div>
                                        @else
                                            <span class="card-text radio-label">{{ $option['text'] }}</span>
                                        @endif
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif ($question->quiz->type === 'Essay')
                    <div class="row mt-4">
                        <div class="mb-3">
                            <input class="form-control" type="text" name="answers[{{ $question->id }}]" placeholder="Ketik jawaban anda disini">
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endforeach







@endsection