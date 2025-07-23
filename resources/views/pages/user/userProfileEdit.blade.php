    @extends('layouts.app')
    <title>Edit Profile</title>
    
    @section('content')
    <!-- ========== section start ========== -->
    <form action="{{route('profile.update')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- ========== title-wrapper start ========== -->
            <div class="title-wrapper pt-30">
                <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="title mb-30">
                    <h2>Edit Profile</h2>
                    </div>
                </div>
                <!-- end col -->
                <div class="col-md-6">
                    <div class="breadcrumb-wrapper mb-30">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="#0">profile</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            edit
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
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="container rounded bg-white mt-4 mb-4">
                <div class="row">
                    <div class="col-md-6 border-right">
                        <div class="p-3 py-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                            </div>

                                <div class="row mt-2 text-bold">
                                    <div class="col-md-12 mb-2 text-center">
                                        <div class="text-center">
                                            <div class="preview-container">
                                                <img class="rounded-circle" src="{{ asset('storage/files/photo/' . $profile->photo)}}" id="preview" alt="Preview Image" style="width: 150px; height: 150px;">
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label for="image" class="form-label">Update Foto Profile <span class="text-red mt-2">*</span></label>
                                            <input class="form-control" type="file" name="image" id="image" accept="image/*" onchange="previewImage(event)">
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
                                        <input type="text" class="form-control" id="national_id" name="national_id" value="{{ $profile->national_id }}" minlength="16" maxlength="16" oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 16);" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="labels">Alamat sesuai KTP <span class="text-red">*</span></label>
                                        <input type="text" class="form-control" id="address" name="address" value="{{ $profile->address }}" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="labels">Alamat Domisili <span class="text-red">*</span></label>
                                        <input type="text" class="form-control" id="domicile" name="domicile" value="{{ $profile->domicile }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="labels">Tempat Lahir <span class="text-red">*</span></label>
                                        <input type="text" class="form-control" id="birthplaceInput" name="birthplace" value="{{ $birthplace }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="labels">Tanggal Lahir <span class="text-red">*</span></label>
                                        <input type="date" class="form-control smooth-scroll" id="birthdateInput" name="birthdate" value="{{ $birthdate ? \Carbon\Carbon::createFromFormat('Y-m-d', $birthdate)->format('Y-m-d') : '' }}" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="labels">Nomor HP <span class="text-red">*</span></label>
                                        <input type="text" class="form-control" id="mobile_number" name="mobile_number" value="{{ $profile->mobile_number }}" oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 13);" required>
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
                                        <label class="labels">Telepon Rumah (opsional)</label>
                                        <input type="text" class="form-control" id="landline_phone"  name="landline_phone" value="{{ $profile->landline_phone }}" oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 13);">
                                    </div>
    <div class="col-md-12 mb-3">
        <label class="labels">Jenis Kelamin <span class="text-red">*</span></label>
        <select type="text" class="form-select" id="gender" name="gender" required>
            <option disabled value="">Pilih Jenis Kelamin</option>
            <option value="Laki-Laki" {{ $profile->gender === 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
            <option value="Perempuan" {{ $profile->gender === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
        </select>
    </div>

    <div class="col-md-12 mb-3">
        <label class="labels">Status Pernikahan <span class="text-red">*</span></label>
        <select class="form-select" id="marital_status" name="marital_status" required>
            <option disabled value="">Pilih Status Pernikahan</option>
            <option value="Belum Menikah" {{ $profile->marital_status === 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah</option>
            <option value="Menikah" {{ $profile->marital_status === 'Menikah' ? 'selected' : '' }}>Menikah</option>
            <option value="Duda" {{ $profile->marital_status === 'Duda' ? 'selected' : '' }}>Duda</option>
            <option value="Janda" {{ $profile->marital_status === 'Janda' ? 'selected' : '' }}>Janda</option>
        </select>
    </div>

    <div class="col-md-12 mb-3">
        <label class="labels">Agama <span class="text-red">*</span></label>
        <select class="form-select" id="religion" name="religion" required>
            <option disabled value="">Pilih Agama</option>
            <option value="Islam" {{ $profile->religion === 'Islam' ? 'selected' : '' }}>Islam</option>
            <option value="Kristen" {{ $profile->religion === 'Kristen' ? 'selected' : '' }}>Kristen</option>
            <option value="Katolik" {{ $profile->religion === 'Katolik' ? 'selected' : '' }}>Katolik</option>
            <option value="Hindu" {{ $profile->religion === 'Hindu' ? 'selected' : '' }}>Hindu</option>
            <option value="Buddha" {{ $profile->religion === 'Buddha' ? 'selected' : '' }}>Buddha</option>
            <option value="Konghucu" {{ $profile->religion === 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
            <!-- Add more options if needed -->
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label class="labels">Pendidikan Terakhir <span class="text-red">*</span></label>
        <select class="form-select" id="education" name="education" required>
            <option disabled value="">Pilih Pendidikan Terakhir</option>
            <option value="S3" {{ explode(', ', $profile->education)[0] === 'S3' ? 'selected' : '' }}>S3</option>
            <option value="S2" {{ explode(', ', $profile->education)[0] === 'S2' ? 'selected' : '' }}>S2</option>
            <option value="S1" {{ explode(', ', $profile->education)[0] === 'S1' ? 'selected' : '' }}>S1</option>
            <option value="D4" {{ explode(', ', $profile->education)[0] === 'D4' ? 'selected' : '' }}>D4</option>
            <option value="D3" {{ explode(', ', $profile->education)[0] === 'D3' ? 'selected' : '' }}>D3</option>
            <option value="D2" {{ explode(', ', $profile->education)[0] === 'D2' ? 'selected' : '' }}>D2</option>
            <option value="D1" {{ explode(', ', $profile->education)[0] === 'D1' ? 'selected' : '' }}>D1</option>
            <option value="SMK" {{ explode(', ', $profile->education)[0] === 'SMK' ? 'selected' : '' }}>SMK</option>
            <option value="SMA" {{ explode(', ', $profile->education)[0] === 'SMA' ? 'selected' : '' }}>SMA</option>
            <option value="SMP" {{ explode(', ', $profile->education)[0] === 'SMP' ? 'selected' : '' }}>SMP</option>
            <option value="SD" {{ explode(', ', $profile->education)[0] === 'SD' ? 'selected' : '' }}>SD</option>
            <!-- Add more options if needed -->
        </select>
    </div>

    <div id="majorField" class="col-md-6 mb-3" style="{{ in_array(explode(', ', $profile->education)[0], ['S3', 'S2', 'S1', 'D4', 'D3', 'D2', 'D1', 'SMK', 'SMA']) ? '' : 'display: none;' }}">
        <label class="labels">Jurusan <span class="text-red">*</span></label>
        <input type="text" class="form-control" id="major" name="major" value="{{ in_array(explode(', ', $profile->education)[0], ['S3', 'S2', 'S1', 'D4', 'D3', 'D2', 'D1', 'SMK', 'SMA']) && isset(explode(', ', $profile->education)[1]) ? explode(', ', $profile->education)[1] : '' }}">
    </div>

    <div class="col-md-12 mb-3">
        <label class="labels">Posisi yang dilamar <span class="text-red">*</span></label>
        <select class="form-select" id="applied_position" name="applied_position" required>
            <option disabled value="">Pilih Posisi yang Dilamar</option>
            @foreach ($jobPositions as $jobPosition)
                <option value="{{ $jobPosition->position }}" {{ $profile->applied_position === $jobPosition->position ? 'selected' : '' }}>
                    {{ $jobPosition->position }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label class="labels">Cabang yang dilamar <span class="text-red">*</span></label>
        <select class="form-select" id="branch_city" name="branch_city" required>
            <option selected disabled value="">Pilih Kota</option>
            @foreach($branchCities as $city)
                <option value="{{ $city }}" {{ old('branch_city', $selectedBranchCity) == $city ? 'selected' : '' }}>
                    {{ $city }}
                </option>
            @endforeach            
        </select>
    </div>
    
    <div class="col-md-6 mb-3" id="branchLocationField" style="display: none;">
        <label class="labels">Lokasi yang dilamar <span class="text-red">*</span></label>
        <select class="form-select" id="branch" name="branch" required>
            <option selected disabled value="">Pilih Lokasi</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}" {{ old('branch_location', $selectedBranch) == $branch->id ? 'selected' : '' }}>
                    {{ $branch->location }}
                </option>
            @endforeach
        </select>
    </div>    
    
    <div class="col-md-12 mb-3">
        <label class="labels">Status Pekerjaan <span class="text-red">*</span></label>
        <select class="form-select" id="job_status" name="job_status" required>
            <option selected disabled value="">Pilih Status Pekerjaan</option>
            <option value="Belum Bekerja" {{ $profile->job_status === 'Belum Bekerja' ? 'selected' : '' }}>Belum Bekerja</option>
            <option value="Sedang Bekerja" {{ $profile->job_status === 'Sedang Bekerja' ? 'selected' : '' }}>Sedang Bekerja</option>
            <!-- Add more options as needed -->
        </select>
    </div>

    <div class="col-md-12 mb-3">
        <label class="labels">Dapat Bekerja Mulai <span class="text-red">*</span></label>
        <input type="date" class="form-control smooth-scroll" id="able_to_work" name="able_to_work" value="{{ $profile->able_to_work ? \Carbon\Carbon::createFromFormat('Y-m-d', $profile->able_to_work)->format('Y-m-d') : '' }}" required>
    </div>

                                    @if ($profile->cv)                     
                                    @else
                                        <div class="mb-3">
                                        <label for="cv" class="form-label">Upload CV anda (PDF)<span class="text-red">*</span></label>
                                        <input class="form-control" type="file" id="cv" name="cv" accept=".pdf" required>
                                    @endif
                                </div>
                                </div>
                                <div class="mt-3 mb-3 text-center">
                                    <button class="btn btn-success profile-button" type="submit">Simpan Data Diri</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>

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
        birthdateInput.addEventListener("change", function() {
            var selectedDate = new Date(birthdateInput.value);

            // Calculate the difference in years
            var ageDiff = today.getFullYear() - selectedDate.getFullYear();

            // Check if the user is at least 17 years old and not more than 55 years old
            if (ageDiff < 17 || ageDiff > 55) {
                alert("Anda harus berusia antara 17 dan 55 tahun.");
                birthdateInput.value = ""; // Clear the input value if invalid date
            }
        });
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

        // Ambil nilai yang tersimpan di database
        const branchCitySelect = document.getElementById('branch_city');
        const branchSelect = document.getElementById('branch');
        const allBranches = @json($branches);

        // Data dari database
        const selectedBranchId = "{{ $profile->branch_location ?? '' }}";
        const selectedCity = "{{ $selectedBranchCity ?? '' }}";

        // Fungsi untuk update daftar lokasi berdasarkan kota yang dipilih
        function updateBranchOptions(selectedCity, selectedBranchId = null) {
            const branchOptions = allBranches.filter(branch => branch.city === selectedCity);

            // Kosongkan dropdown sebelum diisi ulang
            branchSelect.innerHTML = '<option selected disabled value="">Pilih Lokasi</option>';

            if (branchOptions.length > 0) {
                branchOptions.forEach(branch => {
                    const option = document.createElement('option');
                    option.value = branch.id; // Gunakan ID sebagai value
                    option.textContent = branch.location;

                    // Tandai opsi yang sesuai dengan data di database
                    if (selectedBranchId && branch.id == selectedBranchId) {
                        option.selected = true;
                    }

                    branchSelect.appendChild(option);
                });

                // Tampilkan dropdown lokasi
                document.getElementById('branchLocationField').style.display = 'block';
            } else {
                branchSelect.innerHTML = '<option selected disabled value="">Pilih kota terlebih dahulu</option>';
                document.getElementById('branchLocationField').style.display = 'none';
            }
        }

        // Pastikan dropdown diisi saat halaman dimuat
        if (selectedCity !== '') {
            updateBranchOptions(selectedCity, selectedBranchId);
        }

        // Event listener untuk mengupdate lokasi cabang saat kota berubah
        branchCitySelect.addEventListener('change', function() {
            const selectedCity = branchCitySelect.value;
            if (selectedCity !== '') {
                updateBranchOptions(selectedCity);
            } else {
                branchSelect.innerHTML = '<option selected disabled value="">Pilih kota terlebih dahulu</option>';
                document.getElementById('branchLocationField').style.display = 'none';
            }
        });

        // Pastikan "branch" terisi sebelum form dikirim
        document.querySelector("form").addEventListener("submit", function(event) {
            if (branchSelect.value === "" || branchSelect.value === null) {
                alert("Pilih lokasi yang dilamar sebelum menyimpan!");
            }
        });
        

        function combineFields() {
        var birthplace = document.getElementById('birthplaceInput').value;
        var birthdate = document.getElementById('birthdateInput').value;
        var ttl = birthplace + ', ' + birthdate;
        document.getElementById('birthdate').value = ttl;
        }
    </script>

    @endsection