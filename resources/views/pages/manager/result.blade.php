@extends('layouts.appManager')
<title>Hasil Psikotes Peserta</title>
 
@section('content')
<!-- ========== section start ========== -->
    <div class="container-fluid">
        <!-- ========== title-wrapper start ========== -->
        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                <div class="col">
                    <div class="title mb-30">
                        <h2>Hasil Psikotes</h2>
                    </div>
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- ========== title-wrapper end ========== -->

        <div class="card">
            <div class="card-body">
                <div class="btn-group" role="group" aria-label="Export Buttons">
                    <a href="{{ route('export.pdf') }}" class="btn btn-light btn-sm border-dark" target="_blank">PDF</a>
                    <a href="{{ route('export.excel') }}" class="btn btn-light btn-sm border-dark">XLSX</a>
                </div>
                <div class="table-responsive mt-4">
                    <table class="display" id="users-result-table" style="width:100%">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Pendidikan Terakhir</th>
                                <th>Cabang yang dilamar</th>
                                <th>Posisi yang dilamar</th>
                                <th>Progress</th>
                                <th>Hasil Akhir</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <!-- Table content goes here (you can add table rows and data dynamically) -->
                    </table>
                </div>
            </div>
        </div>

        <script>
$(document).ready(function() {
    $('#users-result-table').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('Muser.results') }}",
            dataSrc: function(json) {
                // Modify the JSON response to include the profile URL
                var data = json.data.map(function(user) {
                    user.profileUrl = "{{ route('managerusers.show', ['user' => ':user']) }}".replace(':user', user.id);
                    return user;
                });
                return data;
            }
        },
        columns: [
            { data: 'name', name: 'name' },
            { data: 'education', name: 'education' },
            { data: 'branch_location', name: 'branch_location' },
            { data: 'applied_position', name: 'applied_position' },
            { data: 'progress', name: 'progress', orderable: true, searchable: false },
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
        order: [[4, 'desc'], [5, 'desc']] // Order by progress (column index 3) and average score (column index 4) in descending order
    });
});


        </script>




@endsection