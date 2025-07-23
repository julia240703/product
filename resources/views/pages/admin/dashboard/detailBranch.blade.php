@extends('layouts.appAdmin')
<title>Data Cabang {{ $branch->location }}</title>
   
@section('content')
 <!-- ========== section start ========== -->
        <div class="container-fluid">
          <!-- ========== title-wrapper start ========== -->
          <div class="title-wrapper pt-30">
            <div class="row align-items-center">
              <div class="col-md-6">
                <div class="title">
                  <h2>{{ $branch->location }}</h2>
                </div>
              </div>
              <!-- end col -->
              <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item">
                        <a href="#0">Admin</a>
                      </li>
                      <li class="breadcrumb-item active" aria-current="page">
                        Home
                      </li>
                    </ol>
                  </nav>
                </div>
              </div>
              <!-- end col -->
            </div>
            <!-- end row -->
          </div>
        <!-- end row -->

<div class="row justify-content-end">
    <div class="col-md-2 select-style-1">
        <div class="select-position select-sm">
            <select id="userCountSelect" class="bg-white form-select" onchange="filterData()">
                <option value="all-time">Semua</option>
                <option value="today" {{ $selectedOption === 'today' ? 'selected' : '' }}>Hari Ini</option>
                <option value="7days" {{ $selectedOption === '7days' ? 'selected' : '' }}>7 Hari</option>
                <option value="30days" {{ $selectedOption === '30days' ? 'selected' : '' }}>30 Hari</option>
                <option value="60days" {{ $selectedOption === '60days' ? 'selected' : '' }}>60 Hari</option>
                <option value="90days" {{ $selectedOption === '90days' ? 'selected' : '' }}>90 Hari</option>
                <!-- Add more options as needed -->
            </select>
        </div>
    </div>
</div>
<!-- ========== title-wrapper end ========== -->

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="display" id="users-result-table" style="width:100%">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Pendidikan</th>
                        <th>Posisi</th>
                        <th>Tanggal Tes</th>
                        <th>Total Nilai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Table content goes here (you can add table rows and data dynamically) -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var branchLocation = "{{ $branch->location }}"; // Get the branch location from the PHP variable

    var table = $('#users-result-table').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        deferRender: true,
        ajax: {
            url: "{{ route('admin.branchDetail', ['branch_location' => $branch->location]) }}",
            data: function (d) {
                // Add the selected option as a parameter to the AJAX request
                d.duration = $('#userCountSelect').val();
            },
            dataSrc: function(json) {
                // Modify the JSON response to include the profile URL
                var data = json.data.map(function(user) {
                    user.profileUrl = "{{ route('users.show', ['user' => ':user']) }}".replace(':user', user.id);
                    return user;
                });
                return data;
            }
        },
        columns: [
            { data: 'name', name: 'name' },
            { data: 'education', name: 'education' },
            { data: 'applied_position', name: 'applied_position' },
            { data: 'latest_result_created_at', name: 'latest_result_created_at' },
            { data: 'average_score', name: 'average_score', orderable: true, searchable: false },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, full, meta) {
                    var buttons = '';
                    buttons += '<a href="{{ route("user.results.detail", [":user"]) }}'.replace(':user', full.id) + '" class="btn btn-primary mb-1 ms-1 btn-sm">Lihat Detail</a>';
                    // buttons += '<a href="' + full.profileUrl + '" class="btn btn-secondary mb-1 ms-1 btn-sm">Lihat Profile</a>';
                    return buttons;
                },
            },
        ],
        // Order by average_score then latest_result_created_at
        order: [[4, 'desc'], [3, 'desc']],

    });

    // Handle userCountSelect dropdown changes
    $('#userCountSelect').on('change', function() {
        table.draw(); // Redraw the DataTables with the new data based on the selected option
    });
});
</script>





@endsection