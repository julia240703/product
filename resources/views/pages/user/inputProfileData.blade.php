@extends('layouts.guest2')
<title>Input Data Diri</title>

@section('content')
<form action="{{ route('store.profile.data') }}" method="POST" enctype="multipart/form-data">
@csrf
<div class="container-fluid">        
<div class="container rounded bg-white mt-4 mb-4">
    <div class="row">
        <h2 class="text-center mt-4">Data Diri Pelamar</h2>
        <div class="col-md-6 border-right">
            <div class="p-3 py-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                </div>

                    <div class="row mt-2 text-bold">
                        <div class="col-md-12 mb-2 text-center">
                            <div class="text-center">
                                <div class="preview-container">
                                    <img class="rounded-circle" src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3.webp" id="preview" alt="Preview Image" style="width: 150px; height: 150px;">
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="image" class="form-label">Upload Foto Profile <span class="text-red mt-2">*</span></label>
                                <input class="form-control" type="file" name="image" id="image" accept=".jpg, .jpeg, .png" onchange="previewImage(event)" required>
                                <label for="image" class="text-red fs-6">Ukuran file maksimum: 2MB (JPG|JPEG|PNG)</label>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="labels">Nama Lengkap <span class="text-red">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $profile->name }}" required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="labels">Alamat Email <span class="text-red">*</span></label>
                            <input type="text" class="form-control" id="email" name="email" value="{{ $profile->email }}" disabled>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="labels">No. KTP <span class="text-red">*</span></label>
                            <input type="text" class="form-control" id="national_id" name="national_id" minlength="16" maxlength="16" oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 16);" required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="labels">Alamat sesuai KTP <span class="text-red">*</span></label>
                            <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}" required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="labels">Alamat Domisili <span class="text-red">*</span></label>
                            <input type="text" class="form-control" id="domicile" name="domicile" value="{{ old('domicile') }}" required>
                        </div>

                        <div class="col-md-5">
                            <label class="labels">Tempat Lahir<span class="text-red">*</span></label>
                            <input type="text" class="form-control" id="birthplaceInput" name="birthplace" value="{{ old('birthplace') }}" required>
                        </div>

                        <div class="col-md-7 mb-3">
                            <label class="labels">Tanggal Lahir <span class="text-red">*</span></label>
                            <input type="date" class="form-control smooth-scroll" id="birthdateInput" name="birthdate" value="{{ old('birthdate') }}" required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="labels">Nomor HP <span class="text-red">*</span></label>
                            <input type="text" class="form-control" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 13);" required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="labels">Telepon Rumah (opsional)</label>
                            <input type="text" class="form-control" id="landline_phone" name="landline_phone" value="{{ old('landline_phone') }}" oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 13);">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 border-right">
                <div class="p-3 py-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                    </div>
                    <div class="row mt-2 text-bold">

                        <div class="col-md-12 mb-3">
                            <label class="labels">Jenis Kelamin <span class="text-red">*</span></label>
                            <select type="text" class="form-select" id="gender"  name="gender" value="{{ old('gender') }}" required>
                                <option selected disabled value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-Laki">Laki-Laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="labels">Status Pernikahan <span class="text-red">*</span></label>
                            <select type="text" class="form-select" id="marital_status"  name="marital_status" value="{{ old('marital_status') }}" required>
                            <option selected disabled value="">Pilih Status Pernikahan</option>
                                <option value="Belum Kawin">Belum Kawin</option>
                                <option value="Kawin">Kawin</option>
                                <option value="Cerai">Cerai</option>
                                <option value="Janda">Janda</option>
                                <option value="Duda">Duda</option>
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="labels">Agama <span class="text-red">*</span></label>
                            <select type="text" class="form-select" id="religion"  name="religion" value="{{ old('religion') }}" required>
                            <option selected disabled value="">Pilih Agama</option>
                                <option value="Islam">Islam</option>
                                <option value="Protestan">Protestan</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Buddha">Buddha</option>
                                <option value="Khonghucu">Khonghucu</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="labels">Pendidikan Terakhir <span class="text-red">*</span></label>
                            <select class="form-select" id="education" name="education" value="{{ old('education') }}" required>
                                <option selected disabled value="">Pendidikan Terakhir</option>
                                <option value="S3">S3</option>
                                <option value="S2">S2</option>
                                <option value="S1">S1</option>
                                <option value="D4">D4</option>
                                <option value="D3">D3</option>
                                <option value="D2">D2</option>
                                <option value="D1">D1</option>
                                <option value="SMK">SMK</option>
                                <option value="SMA">SMA</option>
                                <option value="SMP">SMP</option>
                                <option value="SD">SD</option>
                            </select>
                        </div>
                        <div id="majorField" class="col-md-6 mb-3" style="display: none;">
                            <label class="labels">Jurusan <span class="text-red">*</span></label>
                            <input type="text" class="form-control" id="major" name="major" value="{{ old('major') }}">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="labels">Posisi yang Dilamar <span class="text-red">*</span></label>
                            <select type="text" class="form-select" id="applied_position"  name="applied_position" value="{{ old('applied_position') }}" required>
                                <option selected disabled value="">Pilih Posisi</option>
                                @foreach($jobPosition as $position)
                                    <option value="{{ $position }}">{{ $position }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-5 mb-3">
                            <label class="labels">Cabang yang Dilamar <span class="text-red">*</span></label>
                            <select type="text" class="form-select" id="branch_city" name="branch_city" value="{{ old('branch_city') }}" required>
                                <option selected disabled value="">Pilih Kota</option>
                                @foreach($branchCities as $city)
                                    <option value="{{ $city }}">{{ $city }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-7 mb-3">
                            <label class="labels">Lokasi yang Dilamar <span class="text-red">*</span></label>
                            <select type="text" class="form-select" id="branch" name="branch" value="{{ old('branch') }}" required>
                                <option selected disabled value="">Pilih Kota Terlebih Dahulu</option>
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="labels">Status Pekerjaan <span class="text-red">*</span></label>
                            <select type="text" class="form-select" id="job_status"  name="job_status" value="{{ old('job_status') }}" required>
                            <option selected disabled value="">Status Pekerjaan</option>
                                <option value="Belum Bekerja">Belum Bekerja</option>
                                <option value="Sedang Bekerja">Sedang Bekerja</option>
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="labels">Dapat Bekerja Mulai <span class="text-red">*</span></label>
                            <input type="date" class="form-control smooth-scroll" id="able_to_work"  name="able_to_work" value="{{ old('able_to_work') }}" required>
                        </div>

                        <div class="mb-3">
                        <label for="cv" class="form-label">Upload CV Anda <span class="text-red">*</span></label>
                        <input class="form-control" type="file" id="cv" name="cv" accept=".pdf" required> <!-- ubah jadi required jika perlu -->
                        <label for="cv" class="text-red fs-6">Ukuran file maksimum: 2MB (PDF)</label>
                        </div>

                        <div class="col-md-12 mt-2 mb-3">
                            <label class="labels">Dari Mana Anda Mendapatkan Informasi Rekrutmen Kami?<span class="text-red">*</span></label>
                            <select type="text" class="form-select smooth-scroll" id="recruitment_source" name="recruitment_source" required>
                                <option selected disabled value=""></option>
                                <option value="Website Perusahaan">Website Perusahaan</option>
                                <option value="Sosial Media Perusahaan">Sosial Media Perusahaan</option>
                                <option value="Email Perusahaan">Email Perusahaan</option>
                                <option value="Walk In Cabang">Walk In Cabang</option>
                                <option value="Other">Lainnya</option>
                            </select>
                            <div id="otherSourceInput" style="display: none;">
                                <label class="labels mt-2">Tuliskan Sumber Informasi Lainnya<span class="text-red">*</span></label>
                                <input type="text" class="form-control" id="other_source" name="other_source">
                            </div>
                        </div>

                    </div>
                    <div class="mt-3">
                        <button class="btn btn-success profile-button" type="submit">Simpan Data Diri</button>
                    </form>

                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        @method('POST')
                            <button type="submit" class="btn btn-danger float-end mt-4">
                                <i class="lni lni-exit"></i> {{ __('Keluar') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- ========== footer start =========== -->
<footer class="footer">
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

<script>
    const form = document.getElementById('yourForm');
    const recruitmentSourceSelect = document.getElementById('recruitment_source');
    const otherSourceInput = document.getElementById('otherSourceInput');

    recruitmentSourceSelect.addEventListener('change', function() {
        if (recruitmentSourceSelect.value === 'Other') {
            otherSourceInput.style.display = 'block';
            document.getElementById('other_source').setAttribute('required', 'required');
        } else {
            otherSourceInput.style.display = 'none';
            document.getElementById('other_source').removeAttribute('required');
        }
    });
</script>

<script>
    var birthdateInput = document.getElementById("birthdateInput");
    var today = new Date();

    // Set the max attribute of the input element to the date exactly 17 years ago from today
    var maxDate = new Date(today);
    maxDate.setFullYear(maxDate.getFullYear() - 17);
    birthdateInput.setAttribute("max", maxDate.toISOString().split("T")[0]);

    // Set the min attribute of the input element to the date exactly 55 years ago from today
    var minDate = new Date(today);
    minDate.setFullYear(minDate.getFullYear() - 55);
    birthdateInput.setAttribute("min", minDate.toISOString().split("T")[0]);

    // Function to convert the input date to "dd-mm-yyyy" format
    function formatDateToDDMMYYYY(date) {
        var day = ("0" + date.getDate()).slice(-2);
        var month = ("0" + (date.getMonth() + 1)).slice(-2);
        var year = date.getFullYear();
        return day + "-" + month + "-" + year;
    }

    // Add event listener to validate and format the input date
    // birthdateInput.addEventListener("change", function() {
    //     var selectedDate = new Date(birthdateInput.value);

    //     // Calculate the difference in years
    //     var ageDiff = today.getFullYear() - selectedDate.getFullYear();

    //     // Check if the user is at least 17 years old and not more than 55 years old
    //     if (ageDiff < 17 || ageDiff > 55) {
    //         alert("You must be between 17 and 55 years old.");
    //         birthdateInput.value = ""; // Clear the input value if invalid date
    //     }
    // });
</script>

<script>
    // Set the minimum date (today)
    var minDate = new Date();
    var minDateString = minDate.toISOString().split("T")[0];
    var ableToWorkInput = document.getElementById("able_to_work");
    ableToWorkInput.setAttribute("min", minDateString);

    // Set the maximum date (3 months later)
    var maxDate = new Date();
    maxDate.setMonth(maxDate.getMonth() + 3);
    var maxDateString = maxDate.toISOString().split("T")[0];
    ableToWorkInput.setAttribute("max", maxDateString);

    // Add event listener to validate the input date
    ableToWorkInput.addEventListener("change", function() {
        var selectedDate = new Date(ableToWorkInput.value);
        var today = new Date();

        // Calculate the difference in days
        var timeDiff = selectedDate.getTime() - today.getTime();
        var daysDiff = timeDiff / (1000 * 3600 * 24);

        // Check if the date is within the valid range
        if (daysDiff < -1 || daysDiff > 92) {
            alert("Tanggal harus antara hari ini hingga 3 bulan kemudian.");
            ableToWorkInput.value = ""; // Clear the input value if outside the valid range
        }
    });
</script>


<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('preview');
            output.src = reader.result;
            output.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    const educationSelect = document.getElementById('education');
    const majorField = document.getElementById('majorField');

    educationSelect.addEventListener('change', function() {
        if (this.value === 'S3' || this.value === 'S2' || this.value === 'S1' || this.value === 'D4' || this.value === 'D3' || this.value === 'D2' || this.value === 'D1' || this.value === 'SMK' || this.value === 'SMA') {
            majorField.style.display = 'block';
            majorField.classList.remove('col-md-12');
            majorField.classList.add('col-md-6');
        } else {
            majorField.style.display = 'none';
            majorField.classList.remove('col-md-6');
            majorField.classList.add('col-md-12');
        }
    });


    // input branch location
    document.addEventListener('DOMContentLoaded', function() {
    const branchCitySelect = document.getElementById('branch_city');
    const branchSelect = document.getElementById('branch');
    const allBranches = @json($branches);

    // Function to update the branch select options
    function updateBranchOptions() {
        const selectedCity = branchCitySelect.value;
        const branchOptions = allBranches.filter(branch => branch.city === selectedCity);

        // Clear existing options
        branchSelect.innerHTML = '';

        // Add new options
        if (branchOptions.length > 0) {
            branchSelect.style.display = 'block';
            branchOptions.forEach(branch => {
                const option = document.createElement('option');
                option.value = branch.location;
                option.textContent = branch.location;
                branchSelect.appendChild(option);
            });
        } else {
            branchSelect.style.display = 'none';
        }
    }

    // Event listener for branch location change
    branchCitySelect.addEventListener('change', function() {
        const selectedCity = branchCitySelect.value;

        if (selectedCity === '') {
            branchSelect.style.display = 'none';
        } else {
            updateBranchOptions();
        }
    });
});

    function combineFields() {
    var education = document.getElementById('education').value;
    var major = document.getElementById('major').value;
    var edu = education + ', ' + education;
    document.getElementById('education').value = edu;
    }
</script>

@endsection