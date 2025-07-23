@extends('layouts.appManager')
<title>Data Peserta</title>
   
@section('content')
 <!-- ========== section start ========== -->
        <div class="container-fluid">
          <!-- ========== title-wrapper start ========== -->
          <div class="title-wrapper pt-30">
            <div class="row align-items-center">
              <div class="col-md-6">
                <div class="title">
                  <h2>Data Peserta</h2>
                </div>
              </div>
              <!-- end col -->
              <div class="col-md-6">
                <div class="breadcrumb-wrapper">
                  <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item">
                        <a href="#0">Manager</a>
                      </li>
                      <li class="breadcrumb-item active" aria-current="page">
                        Users
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
          <div class="row mt-3 justify-content-end">
            <div class="col-md-2 select-style-1">
                <div class="select-position select-sm">
                    <select id="userCountSelect" class="bg-white form-select">
                        <option value="all">Semua</option>
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
        
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="display" id="users-table" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Dibuat Pada</th>
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
    var dataTable = $('#users-table').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('manager.users') }}",
            data: function (d) {
                d.duration = $('#userCountSelect').val();
                d.branch_id = "{{ Auth::user()->branch_id ?? 'all' }}";
            },
        },
        columns: [
            { data: 'row_number', name: 'row_number', orderable: true, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { 
                data: 'created_at',
                name: 'created_at',
                render: function(data) {
                    let date = new Date(data);
                    let year = date.getFullYear();
                    let month = String(date.getMonth() + 1).padStart(2, '0');
                    let day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                }
            },
            { 
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, full, meta) {
                    var actionBtn = '<a href="{{ route('managerusers.show', 'user_id') }}" class="btn btn-primary btn-sm"><i class="fas fa-info"></i></a>';
                    actionBtn = actionBtn.replace('user_id', full.id);

                    return actionBtn;
                }
            },
        ]
    });

    // On select change event, reload the DataTable with the selected option
    $('#userCountSelect').change(function() {
        dataTable.ajax.reload();
    });
  });
    </script>

@endsection