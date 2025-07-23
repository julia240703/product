@extends('layouts.appManager')
<title>Data Peserta Ditindaklanjuti </title>
 
@section('content')
 <!-- ========== section start ========== -->
        <div class="container-fluid">
          <!-- ========== title-wrapper start ========== -->
          <div class="title-wrapper pt-30">
            <div class="row align-items-center">
              <div class="col-md-6">
                <div class="title">
                  <h2>Peserta yang telah anda respon (Follow Up)</h2>
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
                        user you follow
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
                        <th>No</th>
                        <th>Nama</th>
                        <th>Pendidikan Terakhir</th>
                        <th>Cabang yang dilamar</th>
                        <th>Posisi</th>
                        <th>Tanggal Tes</th>
                        <th>Dapat Bekerja Mulai</th>
                        <th>Total Nilai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Table content goes here -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTables
    var table = $('#users-result-table').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('muser.followingCount') }}",
            data: function (d) {
                // Include the selected option value in the AJAX request
                d.duration = $('#userCountSelect').val();
            }
        },
        columns: [
            { data: null, orderable: false, searchable: false }, // Placeholder for the "No" column, data will be set in the rowCallback
            { data: 'name', name: 'name' },
            { data: 'education', name: 'education' },
            { data: 'branch_location', name: 'branch_location' },
            { data: 'applied_position', name: 'applied_position'},
            { data: 'latest_result_created_at', name: 'latest_result_created_at', orderable: true, searchable: true },
            { data: 'able_to_work', name: 'able_to_work', orderable: true, searchable: true },
            { data: 'average_score', name: 'average_score', orderable: true, searchable: false },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, full, meta) {
                    var buttons = '';
                    buttons += '<a href="{{ route("Muser.results.detail", [":user"]) }}'.replace(':user', full.id) + '" class="btn btn-primary mb-1 ms-1 btn-sm"><i class="fas fa-info"></i></a>';
                    // buttons += '<a href="' + full.profileUrl + '" class="btn btn-secondary mb-1 ms-1 btn-sm">Lihat Profile</a>';
                    return buttons;
                },
            },
        ],
        order: [[5, 'desc'], [6, 'desc'], [7, 'desc']], // Order by progress (column index 5) and average score (column index 6) in descending order

        // Add a rowCallback function to set the dynamic numbering
        rowCallback: function(row, data, index) {
            var api = this.api();
            var startIndex = api.context[0]._iDisplayStart; // Get the index of the first row displayed on the current page
            var numbering = startIndex + index + 1; // Calculate the dynamic numbering
            $('td:eq(0)', row).html(numbering); // Update the "No" column with the dynamic numbering
        }
    });

    // Handle userCountSelect dropdown changes
    $('#userCountSelect').on('change', function() {
        table.ajax.reload(); // Reload the DataTables with new data based on the selected option
    });
});
</script>


          <!-- End Row -->

@endsection