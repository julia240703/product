    <div class="col-lg-4 mb-4">
        <div class="card border shadow-0 h-100"> <!-- Added 'h-100' class here -->
            <div class="card-body text-left d-flex flex-column"> <!-- Added 'd-flex flex-column' classes here -->
                <p class="text-bold card-title mb-3 text-center">{{ $quiz->name }}</p>
                <!-- <p class="card-text mb-2">{{ $quiz->description }}</p> -->
                    @if (in_array($quiz->id, $attemptedQuizzes))
                    <button class="btn btn-danger disabled mb-2 d-flex align-items-center justify-content-center">Ujian Telah Dikerjakan</button>
                @else
                    <button type="button" class="btn btn-success mb-2 d-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#confirmationModal{{ $quiz->id }}">Mulai Ujian</button>
                @endif
            </div>
        </div>
    </div>

    <!-- Confirmation Modal for Quiz {{ $quiz->id }} -->
    <div class="modal fade" id="confirmationModal{{ $quiz->id }}" tabindex="-1" aria-labelledby="confirmationModalLabel{{ $quiz->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel{{ $quiz->id }}">Konfirmasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p>Apakah Anda yakin ingin memulai?
                        <p class="fw-bold">{{ $quiz->name }}</p>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                    <a href="{{ route('instruction', ['quiz' => $quiz->name]) }}" class="btn btn-success">Ya, Mulai</a>
                </div>
            </div>
        </div>
    </div>

