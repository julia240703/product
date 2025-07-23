@extends('layouts.appAdmin')
<title>Hasil Psikotes Peserta</title>
 
@section('content')
 <!-- ========== section start ========== -->
 <div class="container-fluid">
          <!-- ========== title-wrapper start ========== -->
          <div class="title-wrapper pt-30">
            <div class="row align-items-center">
              <div class="col-md-6">
                <div class="title mb-30">
                  <h2>Hasil Psikotes</h2>
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
                      <li class="breadcrumb-item active" aria-current="page">
                      Results
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
                                <th>Tanggal Tes</th>
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
            var dataTable = $('#users-result-table').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('user.results') }}",
                    dataSrc: function(json) {
                        var data = json.data.map(function(user) {
                            user.profileUrl = "{{ route('users.show', ['user' => ':user']) }}".replace(':user', user.id);
                            return user;
                        });
                        return data;
                    }
                },
                columns: [
                    { data: 'created_at_formatted', name: 'created_at_formatted' },
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
                            buttons += '<a href="{{ route("user.results.detail", [":user"]) }}'.replace(':user', full.id) + '" class="btn btn-primary mb-1 ms-1 btn-sm"><i class="fas fa-info"></i></a>';
                            return buttons;
                        },
                    },
                ],
                order: [[5, 'desc'], [6, 'desc']]
            });
        });

        </script>




@endsection