@extends('layouts.appAdmin')
<title>Kelola Simulasi Kredit</title>

@section('content')
    <div class="container-fluid">
        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="title mb-30">
                        <h2>Data Simulasi Kredit</h2>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="breadcrumb-wrapper mb-30">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="#0">Admin</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Credit Simulations
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-success mb-3 btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
            Tambah Simulasi
        </button>

        <!-- Modal Add -->
        <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addSimulationLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addSimulationLabel">Tambahkan Simulasi Kredit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('admin.credit_simulations.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Kategori Motor <span class="text-danger">*</span></label>
                                <select class="form-control" id="category_id" name="category_id" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="motor_type_id" class="form-label">Tipe Motor <span class="text-danger">*</span></label>
                                <select class="form-control" id="motor_type_id" name="motor_type_id" required>
                                    <option value="">Pilih Tipe Motor</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="motorcycle_variant" class="form-label">Varian Motor <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="motorcycle_variant" name="motorcycle_variant" required>
                            </div>
                            <div class="mb-3">
                                <label for="otr_price" class="form-label">Harga OTR <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="otr_price" name="otr_price" required>
                            </div>
                            <div class="mb-3">
                                <label for="minimum_dp" class="form-label">Minimal DP <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="minimum_dp" name="minimum_dp" required>
                            </div>
                            <div class="mb-3">
                                <label for="loan_term" class="form-label">Jangka Waktu (Bulan) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="loan_term" name="loan_term" required>
                            </div>
                            <div class="mb-3">
                                <label for="interest_rate" class="form-label">Suku Bunga (%) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="interest_rate" name="interest_rate" required>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-success">Tambahkan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Edit -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editSimulationLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editSimulationLabel">Ubah Data Simulasi Kredit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editSimulationForm" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="category_id_edit" class="form-label">Kategori Motor <span class="text-danger">*</span></label>
                                <select class="form-control" id="category_id_edit" name="category_id" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="motor_type_id_edit" class="form-label">Tipe Motor <span class="text-danger">*</span></label>
                                <select class="form-control" id="motor_type_id_edit" name="motor_type_id" required>
                                    <option value="">Pilih Tipe Motor</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="motorcycle_variant_edit" class="form-label">Varian Motor <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="motorcycle_variant_edit" name="motorcycle_variant" required>
                            </div>
                            <div class="mb-3">
                                <label for="otr_price_edit" class="form-label">Harga OTR <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="otr_price_edit" name="otr_price" required>
                            </div>
                            <div class="mb-3">
                                <label for="minimum_dp_edit" class="form-label">Minimal DP <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="minimum_dp_edit" name="minimum_dp" required>
                            </div>
                            <div class="mb-3">
                                <label for="loan_term_edit" class="form-label">Jangka Waktu (Bulan) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="loan_term_edit" name="loan_term" required>
                            </div>
                            <div class="mb-3">
                                <label for="interest_rate_edit" class="form-label">Suku Bunga (%) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="interest_rate_edit" name="interest_rate" required>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-success">Ubah</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Delete -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteSimulationLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteSimulationLabel">Konfirmasi Hapus Simulasi Kredit</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Apakah Anda yakin ingin menghapus simulasi untuk varian <strong id="delete_motorcycle_variant"></strong>?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="display" id="credit-simulation-table" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Kategori Motor</th>
                                <th>Tipe Motor</th>
                                <th>Varian Motor</th>
                                <th>Harga OTR</th>
                                <th>Minimal DP</th>
                                <th>Jangka Waktu (Bulan)</th>
                                <th>Suku Bunga (%)</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <!-- Script -->
        <script>
        $(document).ready(function() {
            var dataTable = $('#credit-simulation-table').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.credit_simulations.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'category.name', name: 'category.name' },
                    { data: 'motor_type_name', name: 'motor_type_name' },
                    { data: 'motorcycle_variant', name: 'motorcycle_variant' },
                    { data: 'otr_price', name: 'otr_price', orderable: false, searchable: false },
                    { data: 'minimum_dp', name: 'minimum_dp', orderable: false, searchable: false },
                    { data: 'loan_term', name: 'loan_term' },
                    { data: 'interest_rate', name: 'interest_rate' },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-primary me-1 editBtn" data-id="${data.id}" data-variant="${data.motorcycle_variant}" data-category="${data.category_id}" data-motor-type="${data.motor_type_id}">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger deleteBtn" data-id="${data.id}" data-variant="${data.motorcycle_variant}">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ]
            });

            // Fungsi untuk memuat tipe motor berdasarkan kategori
            function loadMotorTypes(categoryId, selectElement) {
                selectElement.empty();
                selectElement.append(new Option('Pilih Tipe Motor', ''));
                @foreach($motorTypes as $type)
                    if ({{ $type->category_id }} === parseInt(categoryId)) {
                        selectElement.append(new Option('{{ $type->name }}', '{{ $type->id }}'));
                    }
                @endforeach
            }

            // Event listener untuk kategori motor (Add Modal)
            $('#category_id').on('change', function() {
                var categoryId = $(this).val();
                loadMotorTypes(categoryId, $('#motor_type_id'));
            });

            // Event listener untuk kategori motor (Edit Modal)
            $('#category_id_edit').on('change', function() {
                var categoryId = $(this).val();
                loadMotorTypes(categoryId, $('#motor_type_id_edit'));
            });

            // Edit
            $(document).on('click', '.editBtn', function() {
                var simulationData = dataTable.row($(this).closest('tr')).data();
                $('#category_id_edit').val(simulationData.category_id);
                $('#motor_type_id_edit').val(simulationData.motor_type_id);
                $('#motorcycle_variant_edit').val(simulationData.motorcycle_variant);
                $('#otr_price_edit').val(simulationData.otr_price);
                $('#minimum_dp_edit').val(simulationData.minimum_dp);
                $('#loan_term_edit').val(simulationData.loan_term);
                $('#interest_rate_edit').val(simulationData.interest_rate);

                // Muat tipe motor berdasarkan kategori saat edit
                loadMotorTypes(simulationData.category_id, $('#motor_type_id_edit'));
                $('#motor_type_id_edit').val(simulationData.motor_type_id);

                const editForm = $('#editSimulationForm');
                editForm.attr('action', "{{ route('admin.credit_simulations.update', 'DUMMY') }}".replace('DUMMY', simulationData.id));
                $('#editModal').modal('show');
            });

            // Delete
            $(document).on('click', '.deleteBtn', function() {
                const id = $(this).data('id');
                const variant = $(this).data('variant');
                $('#delete_motorcycle_variant').text(variant);
                const form = $('#deleteForm');
                form.attr('action', "{{ route('admin.credit_simulations.delete', 'DUMMY') }}".replace('DUMMY', id));
                $('#deleteModal').modal('show');
            });

            // Reset form on modal close
            $('.modal').on('hidden.bs.modal', function () {
                $(this).find('form')[0]?.reset();
                $('#motor_type_id').empty().append(new Option('Pilih Tipe Motor', ''));
                $('#motor_type_id_edit').empty().append(new Option('Pilih Tipe Motor', ''));
            });
        });
        </script>
@endsection