@extends('layouts.app')
<title>Preview {{ $quiz->name }}</title>
 
@section('content')
<!-- ========== section start ========== -->
    <div class="container-fluid">      
        <!-- ========== title-wrapper start ========== -->
        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
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


<div class="col-sm-6 mx-auto">
    <div class="card mb-3">
        <div class="card-body mx-2">
                    <div class="title mb-10">
                        <h2 class="mb-4 text-center">{{ $quiz->name }}</h2>
                        <h6 class="mb-3">{{ $quiz->description }}</h6>
                    </div>
            @foreach ($questions as $index => $question)
                @if ($question->image)
                    <p class="fw-bolder mb-3 mt-2 text-danger">Contoh Soal {{ $index + 1 }}.</p>
                    <img src="{{ asset('files/question/' . $question->image) }}" alt="Question Image" class="img-fluid mb-4" style="max-width: 50%;">
                @else
                <p class="fw-normal mb-3 mt-2 text-danger">Contoh Soal {{ $index + 1 }}.</p>
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
                                    $isCorrect = $originalValue === $question->is_correct;
                                @endphp

                                <div class="mb-2">
                                    <label class="radio">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $originalValue }}" class="{{ $isCorrect ? 'correct-answer' : '' }}" {{ $isCorrect ? 'checked' : '' }}>
                                        <span class="radio-mark"></span>
                                        @if ($isValidImage)
                                            <div class="image-container mb-4">
                                                <img src="{{ asset('files/question/' . $option['image']) }}" alt="Option {{ $optionKey }} Image" class="img-fluid rounded mb-4" style="max-width: 100%; max-height: 75px;">
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
                            <input class="form-control" type="text" name="answers[{{ $question->id }}]" placeholder="{{ $question->is_correct }}">
                        </div>
                    </div>
                @endif
                    <p class="fw-regular mb-3 mt-2">{!! $question->example_explanation !!}</p>
            @endforeach
            <a class="btn btn-success me-2 d-flex align-items-center justify-content-center mt-4" data-bs-toggle="modal" data-bs-target="#confirmationModal">Mulai Ujian</a>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Mulai Test</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                Apakah anda sudah yakin untuk memulai
                <p class="text-bold text-center">{{$quiz->name}}?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('detail.quiz', ['quiz' => $quiz->name]) }}">
                    @csrf
                    <button type="submit" class="btn btn-success">Mulai</button>
                </form>
            </div>
        </div>
    </div>
</div>




@endsection