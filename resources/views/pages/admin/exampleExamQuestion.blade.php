@extends('layouts.appAdmin')
<title>Kelola Pertanyaan: {{ $quiz->name }}</title>
 
@section('content')
 <!-- ========== section start ========== -->
        <div class="container-fluid">
          <!-- ========== title-wrapper start ========== -->
          <div class="title-wrapper pt-30">
            <div class="row align-items-center">
              <div class="col-md-6">
                <div class="title mb-30">
                  <h2>Contoh Soal {{ $quiz->name }}</h2>
                </div>
              </div>
              <!-- end col -->
              <div class="col-md-6">
                <div class="breadcrumb-wrapper mb-30">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item">
                        <a href="#0">Admin</a>
                      </li>
                      <li class="breadcrumb-item">
                      <a href="#0">Manage-exam-question</a>
                      </li>
                      <li class="breadcrumb-item active" aria-current="page">
                      {{ $quiz->id }}
                      </li>
                    </ol>
                  </nav>
                </div>
              </div>
              <!-- end col -->
            </div>
            <!-- end row -->
          </div>
          <!-- ========== title-wrapper end ========== -->
          <!-- ========== Start-of-Button ========== -->

          <div class="d-flex mb-3">
          <button type="button" class="btn btn-success mb-3 ms-1 btn-sm" data-bs-toggle="modal" data-bs-target="#tambahSoal">Tambah Soal</button>
          </div>

                  <!-- Tambah Soal -->
                    <div class="modal fade" id="tambahSoal" aria-hidden="true" aria-labelledby="tambahSoalLabel" tabindex="-1">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="tambahSoalLabel">Pilih Tipe Soal</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body text-center">
                          <button class="btn btn-success" data-bs-target="#soalBiasa" data-bs-toggle="modal" data-bs-dismiss="modal">Soal Biasa</button>
                          <button class="btn btn-success" data-bs-target="#uploadImage" data-bs-toggle="modal" data-bs-dismiss="modal">Soal Bergambar</button>

                          </div>
                          <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- Tambah Soal -->

                    <!-- Confirmation Modal -->
                    <div class="modal fade" id="soalBiasa" tabindex="-1" aria-labelledby="soalBiasaLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="soalBiasaLabel">Tambah Pertanyaan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <form action="{{ route('store.question', ['quiz' => $quiz->id]) }}" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
                                @csrf
                                @method('POST')

                                <div class="modal-body">

                                  <input type="hidden" id="contoh" name="contoh" value="1">

                                  <!-- Selection for Image/Text Visibility -->
                                  <div class="form-outline mb-1">
                                      <label class="text-bold mb-1">Pilih Tipe Pertanyaan</label>
                                      <select id="inputTypeSelect" name="input_type" class="form-select" onchange="toggleInputType()">
                                          <option value="text">Text</option>
                                          <option value="image">Image</option>
                                      </select>
                                  </div>

                                  <!-- Question -->
                                  <div class="form-outline mb-3" id="questionDiv">
                                      <label class="text-bold mb-1">Pertanyaan (opsional)</label>
                                      <input type="text" id="question" name="question" class="form-control"/>
                                  </div>

                                  <!-- Image Input (Hidden by Default) -->
                                  <div class="form-outline mb-3" id="imageInputDiv" style="display: none;">
                                      <label class="text-bold mb-1">Pertanyaan (opsional)</label>
                                      <input type="file" id="image" name="image" class="form-control" accept="image/*"/>
                                  </div>

                                  <!-- Option A -->
                                  <div class="form-outline mb-3">
                                      <input type="radio" name="radio_option" value="A">
                                        <label class="text-bold mb-1">Pilihan A <span class="text-red mt-2">*</span></label>
                                      <input type="text" id="option_a" name="option_a" class="form-control" required/>
                                  </div>

                                  <!-- Option B -->
                                  <div class="form-outline mb-3">
                                      <input type="radio" name="radio_option" value="B">
                                        <label class="text-bold mb-1">Pilihan B <span class="text-red mt-2">*</span></label>
                                      <input type="text" id="option_b" name="option_b" class="form-control" required/>
                                  </div>

                                  <!-- Option c -->
                                  <div class="form-outline mb-3">
                                      <input type="radio" name="radio_option" value="C">
                                        <label class="text-bold mb-1">Pilihan C <span class="text-red mt-2">*</span></label>
                                      <input type="text" id="option_c" name="option_c" class="form-control" required/>
                                  </div>
                                  
                                  <!-- Option d -->
                                  <div class="form-outline mb-3">
                                      <input type="radio" name="radio_option" value="D">
                                        <label class="text-bold mb-1">Pilihan D <span class="text-red mt-2">*</span></label>
                                      <input type="text" id="option_d" name="option_d" class="form-control" required/>
                                  </div>

                                  <!-- Option e -->
                                  <div class="form-outline mb-3">
                                      <input type="radio" name="radio_option" value="E">
                                        <label class="text-bold mb-1">Pilihan E (opsional)</label>
                                      <input type="text" id="option_e" name="option_e" class="form-control"/>
                                  </div>

                                  <!-- example explanation -->
                                  <div class="form-outline mb-3">
                                      <label class="text-bold mb-1">Penjelasan Jawaban <span class="text-red mt-2">*</span></label>
                                      <textarea type="text" id="penjelasan" name="penjelasan" class="form-control" required></textarea>
                                  </div>

                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                                    <button type="submit" class="btn btn-success">Tambahkan</button>
                                </div>
                                </form>  
                            </div>
                        </div>
                    </div>
                    <!-- Confirmation-Modal-End --> 

                    <!-- Upload-Image-Modal -->
                    <div class="modal fade" id="uploadImage" tabindex="-1" aria-labelledby="uploadImageLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="uploadImageLabel">Tambah Soal Bergambar</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <form action="{{ route('store.question.image', ['quiz' => $quiz->id]) }}" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
                                @csrf
                                @method('POST')

                                <div class="modal-body">

                                  <input type="hidden" id="contoh" name="contoh" value="1">

                                  <!-- Question -->
                                  <!-- Selection for Upload/Reuse Visibility -->
                                  <div class="form-outline mb-1">
                                      <label class="text-bold mb-1">Pertanyaan (opsional)</label>
                                      <div class="row g-2">
                                          <div class="col-md-6">
                                              <select id="inputTypeSelect_question" name="input_type_question" class="form-select bg-secondary text-white" onchange="toggleInputTypeQuestion()">
                                                  <option value="upload">Upload Gambar</option>
                                                  <option value="reuse">Gunakan Gambar</option>
                                                  <option value="text">Gunakan Text</option>
                                              </select>
                                          </div>
                                          <div class="col-md-6">
                                              <div class="d-none" id="imagePreviewQuestionDiv">
                                                  <img id="imagePreviewQuestion" src="" alt="Image Preview" class="img-fluid rounded" style="max-width: 100px;">
                                              </div>
                                          </div>
                                      </div>
                                  </div>

                                  <!-- Upload Image -->
                                  <div class="form-outline mb-3" id="uploadImageDiv" style="display: block;">
                                      <input type="file" id="imageUploadQuestion" name="image_upload_question" class="form-control" accept="image/*"/>
                                  </div>

                                  <!-- Use Image -->
                                  <div class="form-outline mb-3" id="useImageDiv" style="display: none;">
                                      <select id="imageUseQuestion" name="image_use_question" class="form-select mb-3">
                                          <option value="">-- Pilih Gambar --</option>
                                          @foreach(scandir(storage_path('app/public/images')) as $file)
                                              @if (!in_array($file, ['.', '..']))
                                                  <option value="{{ $file }}">{{ $file }}</option>
                                              @endif
                                          @endforeach
                                      </select>
                                  </div>

                                  <!-- Use Text -->
                                  <div class="form-outline mb-3" id="questionDiv1">
                                      <label class="text-bold"></label>
                                      <input type="text" id="question1" name="question1" class="form-control"/>
                                  </div>

                                  <!-- Option A-D -->
                                      @php
                                          $optionLetters = ['A', 'B', 'C', 'D'];
                                      @endphp

                                @foreach ($optionLetters as $optionLetter)
                                  <!-- Selection for Upload/Reuse Visibility -->
                                  <div class="form-outline mb-1">
                                      <input type="radio" name="radio_option" value="{{ $optionLetter }}">
                                      <label class="text-bold mb-1">Pilihan {{ $optionLetter }} <span class="text-red mt-2">*</span></label>
                                      <div class="row g-2">
                                          <div class="col-md-6">
                                              <select id="inputTypeSelect_option{{ $optionLetter }}" name="input_type_option{{ $optionLetter }}" class="form-select bg-secondary text-white" onchange="toggleInputTypeOption{{ $optionLetter }}()">
                                                  <option value="reuse">Gunakan Gambar</option>
                                                  <option value="upload">Upload Gambar</option>
                                              </select>
                                          </div>
                                          <div class="col-md-6">
                                              <div class="d-none" id="imagePreviewOption{{ $optionLetter }}Div">
                                                  <img id="imagePreviewOption{{ $optionLetter }}" src="" alt="Image Preview" class="img-fluid rounded" style="max-width: 100px;">
                                              </div>
                                          </div>
                                      </div>
                                  </div>

                                  <!-- Upload Image -->
                                  <div class="form-outline mb-3" id="uploadImageOption{{ $optionLetter }}Div" style="display: block;">
                                      <input type="file" id="imageUploadOption{{ $optionLetter }}" name="image_upload_option{{ $optionLetter }}" class="form-control" accept="image/*" required/>
                                  </div>

                                  <!-- Use Image -->
                                  <div class="form-outline mb-3" id="useImageOption{{ $optionLetter }}Div" style="display: none;">
                                      <select id="imageUseOption{{ $optionLetter }}" name="image_use_option{{ $optionLetter }}" class="form-select mb-3">
                                          <option value="">-- Pilih Gambar --</option>
                                          @foreach(scandir(storage_path('app/public/images')) as $file)
                                              @if (!in_array($file, ['.', '..']))
                                                  <option value="{{ $file }}">{{ $file }}</option>
                                              @endif
                                          @endforeach
                                      </select>
                                  </div>
                              @endforeach

                              <!-- Option E -->
                              <!-- Selection for Upload/Reuse Visibility -->
                              <div class="form-outline mb-1">
                                  <input type="radio" name="radio_option" value="E">
                                  <label class="text-bold mb-1">Pilihan E (opsional)</label>
                                  <div class="row g-2">
                                      <div class="col-md-6">
                                          <select id="inputTypeSelect_optionE" name="input_type_optionE" class="form-select bg-secondary text-white" onchange="toggleInputTypeoptionE()">
                                              <option value="reuse">Gunakan Gambar</option>
                                              <option value="upload">Upload Gambar</option>
                                          </select>
                                      </div>
                                      <div class="col-md-6">
                                          <div class="d-none" id="imagePreviewoptionEDiv">
                                              <img id="imagePreviewoptionE" src="" alt="Image Preview" class="img-fluid rounded" style="max-width: 100px;">
                                          </div>
                                      </div>
                                  </div>
                              </div>

                              <!-- Upload Image -->
                              <div class="form-outline mb-3" id="uploadImageoptionEDiv" style="display: block;">
                                  <input type="file" id="imageUploadoptionE" name="image_upload_optionE" class="form-control" accept="image/*"/>
                              </div>

                              <!-- Use Image -->
                              <div class="form-outline mb-3" id="useImageoptionEDiv" style="display: none;">
                                  <select id="imageUseoptionE" name="image_use_optionE" class="form-select mb-3">
                                      <option value="">-- Pilih Gambar --</option>
                                      @foreach(scandir(storage_path('app/public/images')) as $file)
                                          @if (!in_array($file, ['.', '..']))
                                              <option value="{{ $file }}">{{ $file }}</option>
                                          @endif
                                      @endforeach
                                  </select>
                              </div>

                              <!-- example explanation -->
                              <div class="form-outline mb-3">
                                <label class="text-bold mb-1">Penjelasan Jawaban <span class="text-red mt-2">*</span></label>
                                <textarea type="text" id="penjelasan" name="penjelasan" class="form-control" required></textarea>
                              </div>

                              </div>

                              <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                                  <button type="submit" class="btn btn-success">Tambahkan</button>
                              </div>
                            </form>  
                          </div>
                      </div>
                  </div>
                <!-- Upload-Image-Modal -->


          <!-- End Row -->

          <!-- ========== Start-of-Datatables ========== -->

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-end">
        </div>
        <div class="table-responsive">
            <table class="display" id="question-table" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pertanyaan</th>
                        <th>Pilihan A</th>
                        <th>Pilihan B</th>
                        <th>Pilihan C</th>
                        <th>Pilihan D</th>
                        <th>Pilihan E</th>
                        <th>Jawaban</th>
                        <th>Penjelasan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <!-- Table content goes here (you can add table rows and data dynamically) -->
            </table>
        </div>
    </div>
</div>



          <!-- ========== End-of-Datatables ========== -->

          <!-- Edit Modal Question Without Image -->
          <div class="modal fade" id="editModalQuestion" tabindex="-1" aria-labelledby="editModalLabelQuestion" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="editModalQuestionLabel">Ubah Pertanyaan</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-bold">
                  <form id="editFormQuestion">
                    <input type="hidden" id="exampleQuestion" value="1">
                    <input type="hidden" id="questionIdQuestion">

                    <!-- Question -->
                    <div class="form-outline mb-3">
                        <label class="text-bold mb-1">Pertanyaan</label>
                        <textarea type="text" id="question2" name="question2" class="form-control"></textarea>
                    </div>

                    <!-- Option A -->
                    <div class="form-outline mb-3">
                        <input type="radio" name="radio_option" value="A">
                          <label class="text-bold mb-1">Pilihan A <span class="text-red mt-2">*</span></label>
                        <input type="text" id="option_a2" name="option_a2" class="form-control" required/>
                    </div>

                    <!-- Option B -->
                    <div class="form-outline mb-3">
                        <input type="radio" name="radio_option" value="B">
                          <label class="text-bold mb-1">Pilihan B <span class="text-red mt-2">*</span></label>
                        <input type="text" id="option_b2" name="option_b2" class="form-control" required/>
                    </div>

                    <!-- Option c -->
                    <div class="form-outline mb-3">
                        <input type="radio" name="radio_option" value="C">
                          <label class="text-bold mb-1">Pilihan C <span class="text-red mt-2">*</span></label>
                        <input type="text" id="option_c2" name="option_c2" class="form-control" required/>
                    </div>

                    <!-- Option d -->
                    <div class="form-outline mb-3">
                        <input type="radio" name="radio_option" value="D">
                          <label class="text-bold mb-1">Pilihan D <span class="text-red mt-2">*</span></label>
                        <input type="text" id="option_d2" name="option_d2" class="form-control" required/>
                    </div>

                    <!-- Option e -->
                    <div class="form-outline mb-3">
                        <input type="radio" name="radio_option" value="E">
                          <label class="text-bold mb-1">Pilihan E (opsional)</label>
                        <input type="text" id="option_e2" name="option_e2" class="form-control"/>
                    </div>

                    <!-- example explanation -->
                    <div class="form-outline mb-3">
                        <label class="text-bold mb-1">Penjelasan Jawaban <span class="text-red mt-2">*</span></label>
                        <textarea type="text" id="penjelasan2" name="penjelasan2" class="form-control" required></textarea>
                    </div>
                    
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                  <button type="button" class="btn btn-success" id="updateBtnQuestion">Ubah</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Edit Modal Question With Image -->
          <div class="modal fade" id="editModalImage" tabindex="-1" aria-labelledby="editModalImageLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="editModalImageLabel">Ubah Pertanyaan</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-bold">
                  <form id="editFormImage">
                    <input type="hidden" id="exampleImage" value="1">
                    <input type="hidden" id="questionIdImage">

                    <!-- Question -->
                    <div class="form-outline mb-3">
                        <label class="text-bold mb-1">Pertanyaan</label>
                        <div class="row g-2 align-items-center">
                            <div class="col-lg-8">
                                <input type="file" id="image1" name="image1" class="form-control" accept="image/*" required/>
                            </div>
                            <div class="col">
                                <img id="imagePreview1" src="" alt="Image Preview" style="max-width: 100px; display: none;">
                            </div>
                        </div>
                    </div>

                    <!-- Option A -->
                    <div class="form-outline mb-3">
                        <input type="radio" name="radio_option" value="A">
                          <label class="text-bold mb-1">Pilihan A <span class="text-red mt-2">*</span></label>
                        <input type="text" id="option_a3" name="option_a3" class="form-control" required/>
                    </div>

                    <!-- Option B -->
                    <div class="form-outline mb-3">
                        <input type="radio" name="radio_option" value="B">
                          <label class="text-bold mb-1">Pilihan B <span class="text-red mt-2">*</span></label>
                        <input type="text" id="option_b3" name="option_b3" class="form-control" required/>
                    </div>

                    <!-- Option c -->
                    <div class="form-outline mb-3">
                        <input type="radio" name="radio_option" value="C">
                          <label class="text-bold mb-1">Pilihan C <span class="text-red mt-2">*</span></label>
                        <input type="text" id="option_c3" name="option_c3" class="form-control" required/>
                    </div>

                    <!-- Option d -->
                    <div class="form-outline mb-3">
                        <input type="radio" name="radio_option" value="D">
                          <label class="text-bold mb-1">Pilihan D <span class="text-red mt-2">*</span></label>
                        <input type="text" id="option_d3" name="option_d3" class="form-control" required/>
                    </div>

                    <!-- Option e -->
                    <div class="form-outline mb-3">
                        <input type="radio" name="radio_option" value="E">
                          <label class="text-bold mb-1">Pilihan E (opsional)</label>
                        <input type="text" id="option_e3" name="option_e3" class="form-control"/>
                    </div>

                    <!-- example explanation -->
                    <div class="form-outline mb-3">
                        <label class="text-bold mb-1">Penjelasan Jawaban <span class="text-red mt-2">*</span></label>
                        <textarea type="text" id="penjelasan3" name="penjelasan3" class="form-control" required></textarea>
                    </div>
                    
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                  <button type="button" class="btn btn-success" id="updateBtnImage">Ubah</button>
                </div>
              </div>
            </div>
          </div>

        <!-- Edit Modal Question With Multiple Image -->
          <div class="modal fade" id="editModalMultipleImage" tabindex="-1" aria-labelledby="editModalMultipleImageLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="editModalMultipleImageLabel">Ubah Pertanyaan</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-bold">
                  <form id="editFormMultipleImage">
                    <input type="hidden" id="exampleMultipleImage" value="1">
                    <input type="hidden" id="questionIdMultipleImage">

                    <!-- Question -->
                    <div class="form-outline mb-3">
                        <label class="text-bold mb-1">Pertanyaan</label>
                        <div class="row g-2 align-items-center">
                            <div class="col-lg-8">
                                <input type="file" id="image2" name="image2" class="form-control" accept="image/*" required/>
                            </div>
                            <div class="col">
                                <img id="imagePreview2" src="" alt="Image Preview" style="max-width: 100px; display: none;">
                            </div>
                        </div>
                    </div>

<div class="options-container">
    <!-- Loop through options A to D -->
    <?php for ($i = 0; $i < 4; $i++) { ?>
        <div class="form-outline mb-3">
            <input type="radio" name="radio_option" value="<?= chr(65 + $i) ?>">
            <label class="text-bold mb-1 form-label">Pilihan <?= chr(65 + $i) ?> <span class="text-red mt-2">*</span></label>
            <div class="row g-2 align-items-center">
                <div class="col-lg-8">
                    <input type="file" id="option_<?= strtolower(chr(97 + $i)) ?>4" name="option_<?= strtolower(chr(97 + $i)) ?>4" class="form-control option-file" required data-preview-id="<?= $i + 3 ?>">
                </div>
                <div class="col">
                    <img id="imagePreview<?= $i + 3 ?>" src="" alt="Image Preview" style="max-width: 100px; display: none;">
                </div>
            </div>
        </div>
    <?php } ?>
</div>

                    <!-- Option e -->
                    <div class="form-outline mb-3">
                        <input type="radio" name="radio_option" value="E">
                        <label class="text-bold mb-1">Pilihan E <span class="text-red mt-2">*</span></label>
                            <div class="row g-2 align-items-center">
                                <div class="col-lg-8">
                                    <input type="file" id="option_e4" name="option_e4" class="form-control" required/>
                                </div>
                            <div class="col">
                                <img id="imagePreview7" src="" alt="Image Preview" style="max-width: 100px; display: none;">
                            </div>
                        </div>
                    </div>

                    <!-- example explanation -->
                    <div class="form-outline mb-3">
                        <label class="text-bold mb-1">Penjelasan Jawaban <span class="text-red mt-2">*</span></label>
                        <textarea type="text" id="penjelasan4" name="penjelasan4" class="form-control" required></textarea>
                    </div>
                    
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                  <button type="button" class="btn btn-success" id="updateBtnMultipleImage">Ubah</button>
                </div>
              </div>
            </div>
          </div>

          <!-- ========== Start-of-AJAX ========== -->

<script>
$(document).on('change', '#image1', function() {
    const fileInput = document.getElementById('image1');
    const imagePreview1 = document.getElementById('imagePreview1');

    if (fileInput.files && fileInput.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            imagePreview1.src = e.target.result;
            imagePreview1.style.display = 'block';
        };

        reader.readAsDataURL(fileInput.files[0]);
    } else {
        imagePreview1.src = '';
        imagePreview1.style.display = 'none';
    }
});

$(document).on('change', '#image2', function() {
    const fileInput = document.getElementById('image2');
    const imagePreview2 = document.getElementById('imagePreview2');

    if (fileInput.files && fileInput.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            imagePreview2.src = e.target.result;
            imagePreview2.style.display = 'block';
        };

        reader.readAsDataURL(fileInput.files[0]);
    } else {
        imagePreview2.src = '';
        imagePreview2.style.display = 'none';
    }
});

$(document).ready(function() {
    $(document).on('change', '.option-file', function() {
        const optionNumber = parseInt($(this).data('preview-id'));
        const imagePreviewId = '#imagePreview' + optionNumber;

        console.log('Generated imagePreviewId:', imagePreviewId);

        const imagePreview = $(imagePreviewId)[0]; // Select the element using jQuery

        if (this.files && this.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
            };

            reader.readAsDataURL(this.files[0]);
        } else {
            imagePreview.src = '';
            imagePreview.style.display = 'none';
        }
    });
});


$(document).on('change', '#option_e4', function() {
    const fileInput = document.getElementById('option_e4');
    const imagePreview7 = document.getElementById('imagePreview7');

    if (fileInput.files && fileInput.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            imagePreview7.src = e.target.result;
            imagePreview7.style.display = 'block';
        };

        reader.readAsDataURL(fileInput.files[0]);
    } else {
        imagePreview7.src = '';
        imagePreview7.style.display = 'none';
    }
});

function validateForm() {
        // Get all radio buttons with the name "radio_option"
        var radioButtons = document.getElementsByName("radio_option");
        var checked = false;

        // Loop through the radio buttons to check if any is selected
        for (var i = 0; i < radioButtons.length; i++) {
            if (radioButtons[i].checked) {
                checked = true;
                break;
            }
        }

        // If at least one radio button is selected, return true to submit the form
        if (checked) {
            return true;
        } else {
            // If no radio button is selected, show an alert and prevent form submission
            alert("Pilih setidaknya satu opsi radio.");
            return false;
        }
    }
</script>          

<script>
$(document).on('change', '#image1', function() {
    const fileInput = document.getElementById('image1');
    const imagePreview1 = document.getElementById('imagePreview1');

    if (fileInput.files && fileInput.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            imagePreview1.src = e.target.result;
            imagePreview1.style.display = 'block';
        };

        reader.readAsDataURL(fileInput.files[0]);
    } else {
        imagePreview1.src = '';
        imagePreview1.style.display = 'none';
    }
});

function validateForm() {
        // Get all radio buttons with the name "radio_option"
        var radioButtons = document.getElementsByName("radio_option");
        var checked = false;

        // Loop through the radio buttons to check if any is selected
        for (var i = 0; i < radioButtons.length; i++) {
            if (radioButtons[i].checked) {
                checked = true;
                break;
            }
        }

        // If at least one radio button is selected, return true to submit the form
        if (checked) {
            return true;
        } else {
            // If no radio button is selected, show an alert and prevent form submission
            alert("Pilih setidaknya satu opsi radio.");
            return false;
        }
    }
</script>
          
<script>
function toggleInputType() {
    const questionDiv = document.getElementById('questionDiv');
    const imageInputDiv = document.getElementById('imageInputDiv');
    const inputTypeSelect = document.getElementById('inputTypeSelect');

    if (inputTypeSelect.value === 'text') {
        questionDiv.style.display = 'block';
        imageInputDiv.style.display = 'none';

        // Make the "question" input required and remove "required" from "image" input
        document.getElementById('question').setAttribute('required', '');
        document.getElementById('image').removeAttribute('required');
    } else if (inputTypeSelect.value === 'image') {
        questionDiv.style.display = 'none';
        imageInputDiv.style.display = 'block';

        // Make the "image" input required and remove "required" from "question" input
        document.getElementById('image').setAttribute('required', '');
        document.getElementById('question').removeAttribute('required');
    } else {
        // If "Select" is chosen, hide both inputs and remove "required" from both
        questionDiv.style.display = 'none';
        imageInputDiv.style.display = 'none';
        document.getElementById('question').removeAttribute('required');
        document.getElementById('image').removeAttribute('required');
    }
}

</script>

<!-- Question -->
<script>
    function toggleInputTypeQuestion() {
        const inputTypeSelect = document.getElementById('inputTypeSelect_question');
        const uploadImageDiv = document.getElementById('uploadImageDiv');
        const useImageDiv = document.getElementById('useImageDiv');
        const questionDiv1 = document.getElementById('questionDiv1');

        if (inputTypeSelect.value === 'upload') {
            uploadImageDiv.style.display = 'block';
            questionDiv1.style.display = 'none'; // Hide the questionDiv1 for "upload" option
            useImageDiv.style.display = 'none';
        } else if (inputTypeSelect.value === 'reuse') {
            uploadImageDiv.style.display = 'none';
            questionDiv1.style.display = 'none'; // Show the questionDiv1 for "reuse" option
            useImageDiv.style.display = 'block';
        } else if (inputTypeSelect.value === 'text') {
            uploadImageDiv.style.display = 'none';
            questionDiv1.style.display = 'block'; // Show the questionDiv1 for "text" option
            useImageDiv.style.display = 'none';
        } else {
            // If "Select" is chosen, hide all divs
            uploadImageDiv.style.display = 'none';
            questionDiv1.style.display = 'none';
            useImageDiv.style.display = 'none';
        }
    }

    // Call the function on page load to set the initial state based on the selected value
    toggleInputTypeQuestion();

    // Update the image preview when a file is selected in the "Upload Gambar" section
    document.getElementById('imageUploadQuestion').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreviewQuestion').src = e.target.result;
            };
            reader.readAsDataURL(file);
            document.getElementById('imagePreviewQuestionDiv').classList.remove('d-none');
        } else {
            document.getElementById('imagePreviewQuestion').src = '';
            document.getElementById('imagePreviewQuestionDiv').classList.add('d-none');
        }
    });

    // Update the image preview when a file is selected in the "Gunakan Gambar" section
    document.getElementById('imageUseQuestion').addEventListener('change', function() {
        const selectedImage = this.value;
        if (selectedImage) {
            const imagePath = '{{ asset('storage/images/') }}/' + selectedImage; // Updated path
            document.getElementById('imagePreviewQuestion').src = imagePath;
            document.getElementById('imagePreviewQuestionDiv').classList.remove('d-none');
        } else {
            document.getElementById('imagePreviewQuestion').src = '';
            document.getElementById('imagePreviewQuestionDiv').classList.add('d-none');
        }
    });
</script>

<script>
    // Function to toggle visibility of image upload and image use sections for each option
    @php
        $optionLetters = ['A', 'B', 'C', 'D'];
    @endphp

    @foreach ($optionLetters as $optionLetter)
        function toggleInputTypeOption{{ $optionLetter }}() {
            const inputTypeSelect = document.getElementById('inputTypeSelect_option{{ $optionLetter }}');
            const uploadImageDiv = document.getElementById('uploadImageOption{{ $optionLetter }}Div');
            const useImageDiv = document.getElementById('useImageOption{{ $optionLetter }}Div');
            const imageUploadInput = document.getElementById('imageUploadOption{{ $optionLetter }}');
            const imageUseSelect = document.getElementById('imageUseOption{{ $optionLetter }}');

            if (inputTypeSelect.value === 'upload') {
                uploadImageDiv.style.display = 'block';
                useImageDiv.style.display = 'none';

                // Make the "imageUpload" input required and remove "required" from "imageUse" select
                imageUploadInput.setAttribute('required', '');
                imageUseSelect.removeAttribute('required');
            } else if (inputTypeSelect.value === 'reuse') {
                uploadImageDiv.style.display = 'none';
                useImageDiv.style.display = 'block';

                // Make the "imageUse" select required and remove "required" from "imageUpload" input
                imageUseSelect.setAttribute('required', '');
                imageUploadInput.removeAttribute('required');
            } else {
                // If "Select" is chosen, hide both inputs and remove "required" from both
                uploadImageDiv.style.display = 'none';
                useImageDiv.style.display = 'none';
                imageUploadInput.removeAttribute('required');
                imageUseSelect.removeAttribute('required');
            }
        }

        // Call the function on page load to set the initial state based on the selected value
        toggleInputTypeOption{{ $optionLetter }}();

        // Update the image preview when a file is selected in the "Upload Gambar" section
        document.getElementById('imageUploadOption{{ $optionLetter }}').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreviewOption{{ $optionLetter }}').src = e.target.result;
                };
                reader.readAsDataURL(file);
                document.getElementById('imagePreviewOption{{ $optionLetter }}Div').classList.remove('d-none');
            } else {
                document.getElementById('imagePreviewOption{{ $optionLetter }}').src = '';
                document.getElementById('imagePreviewOption{{ $optionLetter }}Div').classList.add('d-none');
            }
        });

        // Update the image preview when a file is selected in the "Gunakan Gambar" section
        document.getElementById('imageUseOption{{ $optionLetter }}').addEventListener('change', function() {
            const selectedImage = this.value;
            if (selectedImage) {
                const imagePath = '{{ asset('storage/images/') }}/' + selectedImage; // Updated path
                document.getElementById('imagePreviewOption{{ $optionLetter }}').src = imagePath;
                document.getElementById('imagePreviewOption{{ $optionLetter }}Div').classList.remove('d-none');
            } else {
                document.getElementById('imagePreviewOption{{ $optionLetter }}').src = '';
                document.getElementById('imagePreviewOption{{ $optionLetter }}Div').classList.add('d-none');
            }
        });
    @endforeach
</script>

<script>
    function toggleInputTypeoptionE() {
        const inputTypeSelect = document.getElementById('inputTypeSelect_optionE');
        const uploadImageDiv = document.getElementById('uploadImageoptionEDiv');
        const useImageDiv = document.getElementById('useImageoptionEDiv');
        const imageUploadInput = document.getElementById('imageUploadoptionE');
        const imageUseSelect = document.getElementById('imageUseoptionE');

        if (inputTypeSelect.value === 'upload') {
            uploadImageDiv.style.display = 'block';
            useImageDiv.style.display = 'none';

        } else if (inputTypeSelect.value === 'reuse') {
            uploadImageDiv.style.display = 'none';
            useImageDiv.style.display = 'block';

        } else {
            // If "Select" is chosen, hide both inputs and remove "required" from both
            uploadImageDiv.style.display = 'none';
            useImageDiv.style.display = 'none';
        }
    }

    // Call the function on page load to set the initial state based on the selected value
    toggleInputTypeoptionE();

    // Update the image preview when a file is selected in the "Upload Gambar" section
    document.getElementById('imageUploadoptionE').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreviewoptionE').src = e.target.result;
            };
            reader.readAsDataURL(file);
            document.getElementById('imagePreviewoptionEDiv').classList.remove('d-none');
        } else {
            document.getElementById('imagePreviewoptionE').src = '';
            document.getElementById('imagePreviewoptionEDiv').classList.add('d-none');
        }
    });

    // Update the image preview when a file is selected in the "Gunakan Gambar" section
    document.getElementById('imageUseoptionE').addEventListener('change', function() {
        const selectedImage = this.value;
        if (selectedImage) {
            const imagePath = '{{ asset('storage/images/') }}/' + selectedImage; // Updated path
            document.getElementById('imagePreviewoptionE').src = imagePath;
            document.getElementById('imagePreviewoptionEDiv').classList.remove('d-none');
        } else {
            document.getElementById('imagePreviewoptionE').src = '';
            document.getElementById('imagePreviewoptionEDiv').classList.add('d-none');
        }
    });
</script>

<script>
  $(document).ready(function() {
    // DataTable initialization
    var dataTable = $('#question-table').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('example.exam', ['quiz' => $quiz->id]) }}",
            data: function(d) {
                // Add the question ID as a parameter in the AJAX request
                d.questionId = $('#questionId').val(); // Get the question ID from the edit modal form
            }
        },
        columns: [
            { data: 'row_number', name: 'row_number', orderable: true, searchable: false },
            {
                data: 'question',
                name: 'question',
                render: function(data, type, full, meta) {
                    if (type === 'display') {
                        if (full.image && /\.(jpeg|jpg|png)$/i.test(full.image)) {
                            var imageUrl = "{{ asset('files/question') }}/" + full.image;
                            return '<img src="' + imageUrl + '" alt="Option A Image" class="img-fluid" style="width: 50px;">';
                        } else {
                            return data;
                        }
                    }
                    return data;
                }
            },            
            {
                data: 'option_a',
                name: 'option_a',
                render: function(data, type, full, meta) {
                    if (type === 'display') {
                        if (full.option_a && /\.(jpeg|jpg|png)$/i.test(full.option_a)) {
                            var imageUrl = "{{ asset('files/question') }}/" + full.option_a;
                            return '<img src="' + imageUrl + '" alt="Option A Image" class="img-fluid" style="width: 50px;">';
                        } else {
                            return data;
                        }
                    }
                    return data;
                }
            },
            {
                data: 'option_b',
                name: 'option_b',
                render: function(data, type, full, meta) {
                    if (type === 'display') {
                        if (full.option_b && /\.(jpeg|jpg|png)$/i.test(full.option_b)) {
                            var imageUrl = "{{ asset('files/question') }}/" + full.option_b;
                            return '<img src="' + imageUrl + '" alt="Option A Image" class="img-fluid" style="width: 50px;">';
                        } else {
                            return data;
                        }
                    }
                    return data;
                }
            },
            {
                data: 'option_c',
                name: 'option_c',
                render: function(data, type, full, meta) {
                    if (type === 'display') {
                        if (full.option_c && /\.(jpeg|jpg|png)$/i.test(full.option_c)) {
                            var imageUrl = "{{ asset('files/question') }}/" + full.option_c;
                            return '<img src="' + imageUrl + '" alt="Option A Image" class="img-fluid" style="width: 50px;">';
                        } else {
                            return data;
                        }
                    }
                    return data;
                }
            },
            {
                data: 'option_d',
                name: 'option_d',
                render: function(data, type, full, meta) {
                    if (type === 'display') {
                        if (full.option_d && /\.(jpeg|jpg|png)$/i.test(full.option_d)) {
                            var imageUrl = "{{ asset('files/question') }}/" + full.option_d;
                            return '<img src="' + imageUrl + '" alt="Option D Image" class="img-fluid" style="width: 50px;">';
                        } else {
                            return data;
                        }
                    }
                    return data;
                }
            },
            {
                data: 'option_e',
                name: 'option_e',
                render: function(data, type, full, meta) {
                    if (type === 'display') {
                        if (full.option_e && /\.(jpeg|jpg|png)$/i.test(full.option_e)) {
                            var imageUrl = "{{ asset('files/question') }}/" + full.option_e;
                            return '<img src="' + imageUrl + '" alt="Option E Image" class="img-fluid" style="width: 50px;">';
                        } else {
                            return data;
                        }
                    }
                    return data;
                }
            },
            { data: 'is_correct', name: 'is_correct' },
            { data: 'example_explanation', name: 'example_explanation' },
            {
                data: null,
                render: function(data) {
                    var editButton = '<button class="btn btn-primary editBtn mb-1 btn-sm" data-id="' + data.id + '"><i class="fa-solid fa-pen-to-square"></i></button>';
                    var deleteButton = '<button class="btn btn-danger deleteBtn mb-1 btn-sm" data-id="' + data.id + '"><i class="fa-solid fa-trash" style="color: #ffffff;"></i></button>';
                    return editButton + ' ' + deleteButton;
                }
            },
        ],
    });


 // Edit button click event
$(document).on('click', '.editBtn', function() {
    var questionIdQuestion = $(this).data('id');
    var questionData = dataTable.row($(this).closest('tr')).data();
    var imagePreview = document.getElementById('imagePreview');

    // Show/hide the appropriate modal based on whether the question has a question value or an image value
    if (questionData.question || questionData.question == null && questionData.image == null) {
        $('#editModalQuestion').modal('show');

        $('#questionIdQuestion').val(questionData.id);
        $('#question2').val(questionData.question);
        $('#option_a2').val(questionData.option_a);
        $('#option_b2').val(questionData.option_b);
        $('#option_c2').val(questionData.option_c);
        $('#option_d2').val(questionData.option_d);
        $('#option_e2').val(questionData.option_e);
        $('#penjelasan2').val(questionData.example_explanation);

        $('#exampleQuestion').val(questionData.is_example);

    // Store the current is_correct value in a variable
    var currentIsCorrect = questionData.is_correct;

    // Call the function to set the correct radio button based on the currentIsCorrect value
    setCorrectOption(currentIsCorrect);

    } else if (questionData.image && isPlainText(questionData.option_a)) {
        $('#editModalImage').modal('show');

        $('#questionIdImage').val(questionData.id);
        // Update the image preview
        imagePreview1.src = "{{ asset('files/question/') }}" + '/' + questionData.image;
        imagePreview1.style.display = 'block';
        $('#option_a3').val(questionData.option_a);
        $('#option_b3').val(questionData.option_b);
        $('#option_c3').val(questionData.option_c);
        $('#option_d3').val(questionData.option_d);
        $('#option_e3').val(questionData.option_e);
        $('#penjelasan3').val(questionData.example_explanation);

        $('#exampleImage').val(questionData.is_example);

    // Store the current is_correct value in a variable
    var currentIsCorrect = questionData.is_correct;

    // Call the function to set the correct radio button based on the currentIsCorrect value
    setCorrectOption(currentIsCorrect);

    } else if (questionData.image && getImageFileNames(questionData.option_a)) {
        $('#editModalMultipleImage').modal('show');
        $('#questionIdMultipleImage').val(questionData.id);
        // Update the image preview
        imagePreview2.src = "{{ asset('files/question/') }}" + '/' + questionData.image;
        imagePreview2.style.display = 'block';
        // Assuming you have questionData object available
        <?php for ($i = 0; $i < 4; $i++) { ?>
            const imagePreview<?= $i + 3 ?> = document.getElementById('imagePreview<?= $i + 3 ?>');
            imagePreview<?= $i + 3 ?>.src = "{{ asset('files/question/') }}" + '/' + questionData['option_<?= strtolower(chr(97 + $i)) ?>'];
            imagePreview<?= $i + 3 ?>.style.display = 'block';
        <?php } ?>
        imagePreview7.src = "{{ asset('files/question/') }}" + '/' + questionData.option_e;
        imagePreview7.style.display = 'block';

        $('#penjelasan4').val(questionData.example_explanation);
        $('#exampleMultipleImage').val(questionData.is_example);

    // Store the current is_correct value in a variable
    var currentIsCorrect = questionData.is_correct;

    // Call the function to set the correct radio button based on the currentIsCorrect value
    setCorrectOption(currentIsCorrect);
    }
    
});

// Add this event handling for radio buttons within the form
$(document).on('change', 'input[name="radio_option"]', function() {
    var checkedValue = $(this).val();
    setCorrectOption(checkedValue);
});

// Function to handle setting the radio button value based on is_correct
function setCorrectOption(isCorrectValue) {
    $('input[name="radio_option"]').each(function() {
        $(this).prop('checked', $(this).val() === isCorrectValue);
    });
}

 // Update button click event
$(document).on('click', '#updateBtnQuestion', function() {
    var updatedQuestion = {
        id: $('#questionIdQuestion').val(),
        question: $('#question2').val(),
        option_a: $('#option_a2').val(),
        option_b: $('#option_b2').val(),
        option_c: $('#option_c2').val(),
        option_d: $('#option_d2').val(),
        option_e: $('#option_e2').val(),
        example_explanation: $('#penjelasan2').val(),
        is_correct: $('input[name="radio_option"]:checked').val(),
        is_example: $('#exampleQuestion').val(),

        _token: '{{ csrf_token() }}' // Add the CSRF token
    };

    // Perform the update operation
    $.ajax({
        url: "{{ route('question.edit', ['quiz' => $quiz->id]) }}",
        method: 'POST',
        data: updatedQuestion,
        success: function(response) {
            console.log('Pertanyaan berhasil diperbarui');

            // Store the success message in a variable
            var successMessage = 'Pertanyaan berhasil diperbarui';

            // Remove any existing success message container
            $('#success-message').remove();

            // Create a new success message container
            var successElement = $('<div id="success-message" class="alert alert-success position-fixed top-0 end-0 m-3" style="max-width: 300px; z-index: 1050;"></div>');
            successElement.text(successMessage);

            // Append the success message container to a suitable location in your HTML
            $('#message-container').append(successElement);

            // Display the success message
            successElement.fadeIn('slow');

            // Automatically dismiss the success message after a certain time (e.g., 3 seconds)
            setTimeout(function() {
              successElement.fadeOut('slow', function() {
                successElement.remove();
              });
            }, 1500);

            dataTable.ajax.reload(); // Update DataTable with updated data
            $('#editModalQuestion').modal('hide'); // Close the edit modal
          },
          error: function(xhr, status, error) {
            console.error('Gagal memperbarui pertanyaan:', error);
            // Display an error message or take appropriate action
          }
        });
      });

// Update button click event (common function for both modals)
$(document).on('click', '#updateBtnImage', function() {
    
        // Create a new FormData object
        var formData = new FormData();

        // Use the correct form ID to get the data from the form
        formData.append('id', $('#questionIdImage').val());
        formData.append('question', ''); // No need to include question field
        formData.append('option_a', $('#option_a3').val());
        formData.append('option_b', $('#option_b3').val());
        formData.append('option_c', $('#option_c3').val());
        formData.append('option_d', $('#option_d3').val());
        formData.append('option_e', $('#option_e3').val());
        formData.append('example_explanation', $('#penjelasan3').val());
        formData.append('is_correct', $('input[name="radio_option"]:checked').val());
        formData.append('is_example', $('#exampleImage').val());

        formData.append('_token', '{{ csrf_token() }}');

        // Check if an image is present and add it to the FormData object
        var imageInput = document.getElementById('image1');
        if (imageInput.files.length > 0) {
            formData.append('image', imageInput.files[0]);
        }

        // Perform the update operation using AJAX
        $.ajax({
            url: "{{ route('question.edit', ['quiz' => $quiz->id]) }}",
            method: 'POST',
            data: formData,
            contentType: false, // Set to false to prevent jQuery from automatically setting the Content-Type header
            processData: false, // Set to false to prevent jQuery from processing the data (the FormData object is already properly formatted)
            success: function(response) {
                console.log('Pertanyaan berhasil diperbarui');

                // Store the success message in a variable
                var successMessage = 'Pertanyaan berhasil diperbarui';

                // Remove any existing success message container
                $('#success-message').remove();

                // Create a new success message container
                var successElement = $('<div id="success-message" class="alert alert-success position-fixed top-0 end-0 m-3" style="max-width: 300px; z-index: 1050;"></div>');
                successElement.text(successMessage);

                // Append the success message container to a suitable location in your HTML
                $('#message-container').append(successElement);

                // Display the success message
                successElement.fadeIn('slow');

                // Automatically dismiss the success message after a certain time (e.g., 3 seconds)
                setTimeout(function() {
                    successElement.fadeOut('slow', function() {
                        successElement.remove();
                    });
                }, 1500);

                dataTable.ajax.reload(); // Update DataTable with updated data
                $('#editModalImage').modal('hide'); // Close the edit modal
            },
            error: function(xhr, status, error) {
                console.error('Gagal memperbarui pertanyaan:', error);
                // Display an error message or take appropriate action
            }
        });
});  


    // Update button click event (common function for both modals)
    $(document).on('click', '#updateBtnMultipleImage', function() {
        
        // Create a new FormData object
        var formData = new FormData();

        // Use the correct form ID to get the data from the form
        formData.append('id', $('#questionIdMultipleImage').val());
        formData.append('question', ''); // No need to include question field

        // Check if an image is present and add it to the FormData object
        var imageInput = document.getElementById('image2');
        if (imageInput.files.length > 0) {
            formData.append('image', imageInput.files[0]);
        }

        for (var i = 0; i < 4; i++) {
            var optionLetter = String.fromCharCode(97 + i); // 'a' for i=0, 'b' for i=1, and so on
            var optionImageInput = document.getElementById('option_' + optionLetter + '4');
            
            if (optionImageInput.files.length > 0) {
                formData.append('option_' + optionLetter, optionImageInput.files[0]);
            }
        }

        var imageInput = document.getElementById('option_e4');
        if (imageInput.files.length > 0) {
            formData.append('option_e', imageInput.files[0]);
        }

        formData.append('example_explanation', $('#penjelasan4').val());
        formData.append('is_correct', $('input[name="radio_option"]:checked').val());
        formData.append('is_example', $('#exampleMultipleImage').val());
        formData.append('_token', '{{ csrf_token() }}');



        // Perform the update operation using AJAX
        $.ajax({
            url: "{{ route('question.edit', ['quiz' => $quiz->id]) }}",
            method: 'POST',
            data: formData,
            contentType: false, // Set to false to prevent jQuery from automatically setting the Content-Type header
            processData: false, // Set to false to prevent jQuery from processing the data (the FormData object is already properly formatted)
            success: function(response) {
                console.log('Pertanyaan berhasil diperbarui');

                // Store the success message in a variable
                var successMessage = 'Pertanyaan berhasil diperbarui';

                // Remove any existing success message container
                $('#success-message').remove();

                // Create a new success message container
                var successElement = $('<div id="success-message" class="alert alert-success position-fixed top-0 end-0 m-3" style="max-width: 300px; z-index: 1050;"></div>');
                successElement.text(successMessage);

                // Append the success message container to a suitable location in your HTML
                $('#message-container').append(successElement);

                // Display the success message
                successElement.fadeIn('slow');

                // Automatically dismiss the success message after a certain time (e.g., 3 seconds)
                setTimeout(function() {
                    successElement.fadeOut('slow', function() {
                        successElement.remove();
                    });
                }, 1500);

                dataTable.ajax.reload(); // Update DataTable with updated data
                $('#editModalMultipleImage').modal('hide'); // Close the edit modal
            },
            error: function(xhr, status, error) {
                console.error('Gagal memperbarui pertanyaan:', error);
                // Display an error message or take appropriate action
            }
        });
    });


                    $(document).on('click', '.deleteBtn', function() {
                          var questionId = $(this).data('id');
                          var quizId = $(this).data('quiz');

                          // Show a confirmation dialog before deleting
                          if (confirm("Apakah Anda yakin ingin menghapus pertanyaan ini?")) {
                              // Perform the delete operation
                              $.ajax({
                                  url: "{{ route('question.delete', ['quiz' => ':quiz']) }}".replace(':quiz', quizId),
                                  method: "POST",
                                  data: {
                                      _token: '{{ csrf_token() }}',
                                      _method: 'DELETE', // Set the request method to DELETE
                                      id: questionId
                                  },
                                  success: function(response) {
                                      console.log('Pertanyaan berhasil dihapus');
                                      // Optionally, you can remove the row from the DataTable
                                      dataTable.row($(this).closest('tr')).remove().draw(false);
                                      // Show a success message
                                      alert('Pertanyaan berhasil dihapus!');
                                  },
                                  error: function(xhr, status, error) {
                                      console.error('Gagal menghapus pertanyaan:', error);
                                      // Show an error message
                                      alert('Terjadi kesalahan saat menghapus pertanyaan. Silakan coba lagi.');
                                  }
                              });
                          }
                      });
                  });
          </script>

<script>
function isPlainText(str) {
    // Define a regex to match common image formats
    var imageFormatRegex = /\.(jpg|jpeg|png)$/i;
    // Trim the string to remove leading/trailing spaces
    var trimmedStr = str.trim();
    // Check if the trimmed string matches the image format regex
    return !imageFormatRegex.test(trimmedStr);
}

function getImageFileNames(arr) {
    var imageFormatRegex = /\.(jpg|jpeg|png)$/i;
    var imageFileNames = [];

    for (var i = 0; i < arr.length; i++) {
        var trimmedStr = arr[i].trim();
        if (imageFormatRegex.test(trimmedStr)) {
            imageFileNames.push(trimmedStr);
        }
    }

    return imageFileNames;
}
</script>


          <!-- ========== End-of-ajax ========== -->

@endsection