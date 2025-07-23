
<div class="col-lg-4 mb-4">

<div class="card border shadow-0">
    <div class="card-body text-center">
    <p class="text-bold card-title mb-3">{{ $quiz->name }}</p>
      <p class="card-text mb-2">
      {{ $quiz->description }}
      </p>
      <p class="card-text mb-3">
        Durasi: {{ $quiz->time }} Menit
      </p>

      <a href="{{ route('detail.quiz', ['quiz' => $quiz->name]) }}" type="button" class="btn btn-success">Start Exam</a>
      <!-- <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmationModal">Delete</button> -->
    </div>
  </div>

</div>

                    

