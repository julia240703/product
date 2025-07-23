<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ $quiz->name }}</title>

    <!-- ========== All CSS files linkup ========= -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/lineicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/main.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
</head>
<body>


<!-- ======== main-wrapper start =========== -->
    <!-- ========== header start ========== -->
    <nav class="navbar navbar-expand-lg fixed-top bg-light navbar-light">
    <div class="container d-flex justify-content-center">
    <div class="row">
      <div class="col-12 d-flex justify-content-center">
        <div class="row">
            <div class="col-md-12 mt-2">
                <div id="timer-{{ $quiz->id }}" class="text-center text-bold"></div>
            </div>
        </div>
      </div>
      <div class="col-12 d-flex justify-content-center">
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
        </div>
      </div>
    </div>
  </div>
</nav>
    <!-- ========== header end ========== -->

    <!-- ========== section start ========== -->
    <section class="section bg-light">
        <div class="container-fluid">
            
            <!-- Error Handler -->

            @if ($errors->any())
                <div id="error-message" class="alert alert-danger position-fixed top-0 end-0 m-3" style="max-width: 300px; z-index: 1050;">
                    <ul>
                        @foreach($errors->all() as $error)
                        <li>{{$error}}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (\Session::has('success'))
                <div id="success-message" class="alert alert-success position-fixed top-0 end-0 m-3" style="max-width: 300px; z-index: 1050;">
                    <p class="mb-0">{{ \Session::get('success') }}</p>
                </div>
            @endif

            @if (\Session::has('error'))
                <div id="error-message" class="alert alert-danger position-fixed top-0 end-0 m-3" style="max-width: 300px; z-index: 1050;">
                    <p class="mb-0">{{ \Session::get('error') }}</p>
                </div>
            @endif
        <!-- ========== section start ========== -->
        <div class="container-fluid">
                <!-- ========== title-wrapper start ========== -->
                <div class="title-wrapper pt-30">
                    <div class="row align-items-center">
                        <div class="col-sm-6 mx-auto">
                            <div class="title mb-10 mt-50">
                                <h2 class="mb-4 text-center">{{ $quiz->name }}</h2>
                                <h6>{{ $quiz->description }}</h6>
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                </div>
                <!-- ========== title-wrapper end ========== -->


                <div class="column">
                <form id="quiz-form-{{ $quiz->id }}" action="{{ route('answers.store', ['quiz' => $quiz->id]) }}" method="POST">
                        @csrf

                        @foreach ($questions as $index => $question)
                            <div class="col-sm-6 mx-auto">
                                <div class="card mb-3">
                                    <div class="card-body mx-2">
                                        @if ($question->image)
                                            <p class="fw-bolder mb-3 mt-2 text-danger">Soal Nomor {{ $index + 1 }}.</p>
                                            <img src="{{ asset('files/question/' . $question->image) }}" alt="Question Image" class="img-fluid mb-4" style="max-width: 50%;">
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
                                                    <input class="form-control lowercase-input" type="text" name="answers[{{ $question->id }}]" placeholder="Ketik jawaban anda disini" value="{{ strtolower(old('answers.'.$question->id)) }}">
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    <div class="col-sm-6 mx-auto">
                <button type="button" class="btn btn-success" id="submitAnswersBtn" data-bs-toggle="modal" data-bs-target="#confirmationModal" data-quizid="{{ $quiz->id }}">
                    Kirim Jawaban
                </button>
                <!-- Add this modal structure -->
                <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmationModalLabel">Konfirmasi</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center">
                                <p>Apakah anda yakin ingin mengirim jawaban anda?<p>
                                Anda masih memiliki sisa waktu: <span class="text-bold" id="remainingTimeInModal"></span>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                                <button type="submit" class="btn btn-success">Kirim</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>


    <script>
        document.getElementById('quiz-form-{{ $quiz->id }}').addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });

        document.getElementById('confirmSubmitBtn').addEventListener('click', function() {
            document.getElementById('quiz-form-{{ $quiz->id }}').submit();
        });
    </script>
    
    <script>
    // JavaScript code to convert input value to lowercase
    const lowercaseInputs = document.querySelectorAll('.lowercase-input');

    lowercaseInputs.forEach(input => {
        input.addEventListener('input', () => {
            input.value = input.value.toLowerCase();
        });
    });
    </script>

    <script>
    @foreach($questions as $question)
        initializeTimer({{ $question->quiz_id }}, {{ $question->quiz->time * 60 }});
    @endforeach

    function initializeTimer(quizId, totalTimeInSeconds) {
        const timerElement = document.getElementById(`timer-${quizId}`);
        const radioInputs = document.querySelectorAll(`input[type="radio"][data-quiz="${quizId}"]`);
        const textInputs = document.querySelectorAll(`input[type="text"][data-quiz="${quizId}"]`);
        const answerKey = `answers_${quizId}`;

        let remainingTime = totalTimeInSeconds;
        let intervalId = null;
        let timerStarted = false;

        function startTimer() {
            intervalId = setInterval(updateTimer, 1000);
            timerStarted = true;
            disableBackButton();
        }

        function updateTimer() {
            const minutes = Math.floor(remainingTime / 60);
            const seconds = remainingTime % 60;

            timerElement.innerHTML = `${formatTime(minutes)}:${formatTime(seconds)}`;

            if (remainingTime <= 0) {
                clearInterval(intervalId);
                submitForm(quizId);
            } else {
                if (remainingTime <= 30) {
                    timerElement.style.color = "red"; // Change font color to red when 30 seconds left
                }

                remainingTime--;
                // Store remaining time in local storage
                localStorage.setItem(`remainingTime-${quizId}`, remainingTime);
            }
        }

        function formatTime(time) {
            return time < 10 ? `0${time}` : time;
        }

        function disableBackButton() {
            history.pushState(null, null, document.URL);
            window.addEventListener('popstate', function () {
                history.pushState(null, null, document.URL);
            });
        }

        // Store selected choices in local storage (radio inputs)
        radioInputs.forEach(input => {
            input.addEventListener('change', () => {
                const selectedOption = input.value;
                const answers = JSON.parse(localStorage.getItem(answerKey)) || {};
                answers[input.name] = selectedOption;
                localStorage.setItem(answerKey, JSON.stringify(answers));
            });
        });

        // Store entered text in local storage (text inputs)
        textInputs.forEach(input => {
            input.addEventListener('input', () => {
                const enteredText = input.value;
                const answers = JSON.parse(localStorage.getItem(answerKey)) || {};
                answers[input.name] = enteredText;
                localStorage.setItem(answerKey, JSON.stringify(answers));
            });
        });

        // Restore selected choices and entered text from local storage
        radioInputs.forEach(input => {
            const answers = JSON.parse(localStorage.getItem(answerKey)) || {};
            const selectedOption = answers[input.name];
            if (selectedOption === input.value) {
                input.checked = true;
            }
        });

        textInputs.forEach(input => {
            const answers = JSON.parse(localStorage.getItem(answerKey)) || {};
            const enteredText = answers[input.name];
            if (enteredText) {
                input.value = enteredText;
            }
        });

        function submitForm(quizId) {
            // Perform your form submission here
            const answers = JSON.parse(localStorage.getItem(answerKey)) || {};
            const form = document.getElementById(`quiz-form-${quizId}`);
            for (const key in answers) {
                if (answers.hasOwnProperty(key)) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = answers[key];
                    form.appendChild(input);
                }
            }
            form.submit();
            // Reset the timer and answers after submitting the form
            resetTimer(quizId);
        }

        function resetTimer(quizId) {
            clearInterval(intervalId);
            remainingTime = totalTimeInSeconds;
            localStorage.removeItem(`remainingTime-${quizId}`);
            localStorage.removeItem(answerKey);
            timerElement.innerHTML = `${formatTime(Math.floor(remainingTime / 60))}:${formatTime(remainingTime % 60)}`;
        }

        // Clear remaining time from local storage on quiz access
        localStorage.removeItem(`remainingTime-${quizId}`);

        if (!timerStarted) {
            startTimer();
        }
    }

    function clearLocalStorage() {
        const quizIds = [@foreach($questions as $question){{ $question->quiz_id }},@endforeach];
        quizIds.forEach(quizId => {
            // Remove remaining time and answers from local storage
            localStorage.removeItem(`remainingTime-${quizId}`);
            localStorage.removeItem(`answers_${quizId}`);
        });
    }

    // Clear local storage when the page loads
    window.onload = clearLocalStorage;


    clearLocalStorage();
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const submitAnswersBtn = document.getElementById("submitAnswersBtn");
    const remainingTimeInModal = document.getElementById("remainingTimeInModal");

    let remainingTimeInterval; // Variable to hold the interval ID

    submitAnswersBtn.addEventListener("click", function () {
        const quizId = submitAnswersBtn.getAttribute("data-quizid");
        const remainingTime = localStorage.getItem(`remainingTime-${quizId}`);
        const minutes = Math.floor(remainingTime / 60);
        const seconds = remainingTime % 60;
        remainingTimeInModal.textContent = `${formatTime(minutes)}:${formatTime(seconds)}`;

        // Start an interval to update the displayed time in real-time
        remainingTimeInterval = setInterval(function () {
            const remainingTime = localStorage.getItem(`remainingTime-${quizId}`);
            const minutes = Math.floor(remainingTime / 60);
            const seconds = remainingTime % 60;
            remainingTimeInModal.textContent = `${formatTime(minutes)}:${formatTime(seconds)}`;

            if (remainingTime <= 0) {
                clearInterval(remainingTimeInterval); // Clear the interval when time is up
            } else if (remainingTime <= 30) {
                remainingTimeInModal.style.color = "red"; // Change font color to red
            }
        }, 1000); // Update every second
    });

    function formatTime(time) {
        return time < 10 ? `0${time}` : time;
    }
});

</script>

        </div>
        <!-- end container -->
    </section>
    <!-- ========== section end ========== -->

    <!-- ========== footer start =========== -->
    <footer class="footer bg-light">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 order-last order-md-first">
                    <div class="copyright text-md-start">
                        <p class="text-sm">
                            Developed by
                            <a
                                    href="https://www.wahanaritelindo.com/"
                                    rel="nofollow"
                                    target="_blank"
                                    class="text-red"
                            >
                                Wahana Ritelindo
                            </a>
                        </p>
                    </div>
                </div>
                <div class="col-md-6 order-last order-md-first">
                    <div class="copyright text-md-end">
                        <p class="text-sm">
                            Version
                            <a
                                    class="text-red"
                            >
                            1.0.0
                            </a>
                        </p>
                    </div>
                </div>
                <!-- end col-->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </footer>
    <!-- ========== footer end =========== -->
</main>
<!-- ======== main-wrapper end =========== -->

<!-- ========= All Javascript files linkup ======== -->
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>
</body>
</html>
 
@section('content')




@endsection