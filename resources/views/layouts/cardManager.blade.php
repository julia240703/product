<div class="col-lg-4 mb-4">

<div class="card border shadow-0 h-100">
    <div class="card-body">
    <p class="text-bold card-title mb-3 text-center">{{ $quiz->name }}</p>
      <!-- <p class="card-text mb-2">
      {{ $quiz->description }}
      </p> -->

      <div class="col-lg-10 mb-4">
      <table class="table table-borderless table-sm mb-0">
            <tbody>
              <tr>
                <td class="font-weight-normal align-middle">Durasi </td>
                <td class="float-end font-weight-normal">
                  <p class="mb-1">{{ $quiz->time }} <span class="text-muted">Menit</span></p>
                </td>
                <td class="float-start ms-4">
                    <i class="fa-solid fa-grip-lines"></i>
                </td>
              </tr>
              <tr>
                <td class="font-weight-normal align-middle">Jumlah Soal </td>
                <td class="float-end font-weight-normal">
                  <p class="mb-1">{{ $quiz->questionCount }} <span class="text-muted">Soal</span></p>
                </td>
                <td class="float-start ms-4">
                    <i class="fa-solid fa-grip-lines"></i>
                </td>
              </tr>
              <tr>
                <td class="font-weight-normal align-middle">Limitasi Soal </td>
                <td class="float-end font-weight-normal">
                  <p class="mb-1">{{ $quiz->quiz_limit }} <span class="fw-normal">Soal</span></p>
                </td>
                <td class="float-start ms-4">
                    <i class="fa-solid fa-grip-lines"></i>
                </td>
              </tr>
              <tr>
                <td class="font-weight-normal align-middle">Tipe Soal </td>
                <td class="float-end font-weight-normal">
                  <p class="mb-1">
                    @if ($quiz->type === 'Pilihan-Ganda')
                      PG
                    @else
                      {{ $quiz->type }}
                    @endif
                  </p>                
              </td>
                <td class="float-start ms-4">
                    <i class="fa-solid fa-grip-lines"></i>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <a href="{{ route('Minstruction.quiz', ['quiz' => $quiz->name]) }}" class="btn btn-secondary btn-sm d-flex align-items-center justify-content-center">Preview</a>

        <!-- <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmationModal">Delete</button> -->
    </div>
  </div>
</div>
                    

