@extends('layouts.appAdmin')
<title>Kelola Cabang</title>

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="title mb-30">
                        <h2>Data Cabang</h2>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="breadcrumb-wrapper mb-30">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Admin</a></li>
                                <li class="breadcrumb-item active">List Branch</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Branch Button -->
    <button type="button" class="btn btn-success mb-3 btn-sm" data-bs-toggle="modal" data-bs-target="#addBranchModal">
        Tambah Cabang
    </button>

    <!-- Branch Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="display" id="branch-table" style="width: 100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Cabang</th>
                            <th>Alamat Cabang</th>
                            <th>Kode</th>
                            <th>Order</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Add/Edit Branch Modal -->
    <div class="modal fade" id="addBranchModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form id="branchForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="branchModalTitle">Tambah Cabang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="branch_id" name="id">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Cabang *</label>
                                <input class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Cabang *</label>
                                <input class="form-control" id="code" name="code" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">NPWP</label>
                                <input class="form-control" id="tax_number" name="tax_number">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status Harga *</label>
                                <select class="form-select" id="price_status" name="price_status" required>
                                    <option value="reguler">Reguler</option>
                                    <option value="khusus">Khusus</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Area *</label>
                                <select class="form-select" id="area_id" name="area_id" required>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}">{{ $area->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kota *</label>
                                <select class="form-select" id="city_id" name="city_id" required>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ranking</label>
                                <input type="text" class="form-control" id="ranking" name="ranking">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Service</label>
                                <input class="form-control" id="service" name="service">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Alamat *</label>
                                <textarea class="form-control" id="address" name="address" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Latitude</label>
                                <input class="form-control" id="latitude" name="latitude">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Longitude</label>
                                <input class="form-control" id="longitude" name="longitude">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">URL</label>
                                <input class="form-control" id="url" name="url">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input class="form-control" id="phone" name="phone">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">2nd Phone</label>
                                <input class="form-control" id="phone2" name="phone2">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">3rd Phone</label>
                                <input class="form-control" id="phone3" name="phone3">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fax</label>
                                <input class="form-control" id="fax" name="fax">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Wanda Dealer ID</label>
                                <input class="form-control" id="wanda_dealer_id" name="wanda_dealer_id">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Wanda API Key</label>
                                <input class="form-control" id="wanda_api_key" name="wanda_api_key">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Wanda API Secret</label>
                                <input class="form-control" id="wanda_api_secret" name="wanda_api_secret">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Ahass</label>
                                <input class="form-control" id="ahass_code" name="ahass_code">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success" id="branchSubmitBtn">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="deleteForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalTitle">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p id="deleteMessage"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="message-container"></div>
</div>

        <script>
        $(document).ready(function() {
            let dataTable;
            let isEdit = false;
            let deleteId = '';

            function initDataTable() {
                dataTable = $('#branch-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.branches.index') }}",
                    order: [[0, 'asc']],
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'nama', name: 'nama' },
                        { data: 'alamat', name: 'alamat' },
                        { data: 'kode', name: 'kode' },
                        { 
                            data: 'order',
                            name: 'order',
                            className: "text-center",
                            render: function(data, type, row) {
                                return `
                                    <div class="order-controls">
                                        <span class="order-number">${data}</span>
                                        <div class="btn-group-vertical btn-group-sm ms-2">
                                            <button class="btn btn-outline-primary btn-xs move-up" data-id="${row.id}" data-order="${data}" ${data == 1 ? 'disabled' : ''}>
                                                <i class="fa fa-chevron-up"></i>
                                            </button>
                                            <button class="btn btn-outline-primary btn-xs move-down" data-id="${row.id}" data-order="${data}">
                                                <i class="fa fa-chevron-down"></i>
                                            </button>
                                        </div>
                                    </div>
                                `;
                            }
                        },
                        {
                            data: null,
                            className: "text-center",
                            orderable: false,
                            render: function(data) {
                                return `
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-primary me-1 rounded edit-branch" data-id="${data.id}">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger rounded delete-branch" data-id="${data.id}" data-name="${data.nama}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                `;
                            }
                        }
                    ]
                });
            }

            initDataTable();

            function resetModal(modalId, formId) {
                $(modalId).find('form')[0].reset();
                $(modalId).find('.is-invalid').removeClass('is-invalid');
                $(modalId).find('.invalid-feedback').remove();
            }

            function showMessage(message, type = 'success') {
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const messageHtml = `
                    <div class="alert ${alertClass} alert-dismissible fade show mt-3" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                $('#message-container').html(messageHtml);
                setTimeout(() => $('.alert').alert('close'), 5000);
            }

            $('#addBranchModal').on('show.bs.modal', function() {
                if (!isEdit) {
                    resetModal('#addBranchModal', '#branchForm');
                    $('#branchModalTitle').text('Tambah Cabang');
                    $('#branchSubmitBtn').text('Tambahkan');
                }
            });

            $(document).on('click', '.edit-branch', function() {
                isEdit = true;
                const branchId = $(this).data('id');

                $.ajax({
                    url: "{{ route('admin.branches.edit', ['id' => ':id']) }}".replace(':id', branchId),
                    method: 'GET',
                    success: function(response) {
                        resetModal('#addBranchModal', '#branchForm');
                        $('#branch_id').val(response.id);
                        $('#name').val(response.name);
                        $('#code').val(response.code);
                        $('#tax_number').val(response.tax_number);
                        $('#price_status').val(response.price_status);
                        $('#area_id').val(response.area_id);
                        $('#city_id').val(response.city_id);
                        $('#ranking').val(response.ranking);
                        $('#service').val(response.service);
                        $('#address').val(response.address);
                        $('#latitude').val(response.latitude);
                        $('#longitude').val(response.longitude);
                        $('#url').val(response.url);
                        $('#phone').val(response.phone);
                        $('#phone2').val(response.phone2);
                        $('#phone3').val(response.phone3);
                        $('#fax').val(response.fax);
                        $('#wanda_dealer_id').val(response.wanda_dealer_id);
                        $('#wanda_api_key').val(response.wanda_api_key);
                        $('#wanda_api_secret').val(response.wanda_api_secret);
                        $('#ahass_code').val(response.ahass_code);

                        $('#branchModalTitle').text('Edit Cabang');
                        $('#branchSubmitBtn').text('Ubah');
                        $('#addBranchModal').modal('show');
                    },
                    error: function(xhr) {
                        showMessage('Gagal memuat data cabang', 'error');
                    }
                });
            });

            // Move Branch Order Up
            $(document).on('click', '.move-up', function() {
                const branchId = $(this).data('id');
                const currentOrder = $(this).data('order');
                if (currentOrder > 1) {
                    updateBranchOrder(branchId, currentOrder - 1);
                }
            });

            // Move Branch Order Down
            $(document).on('click', '.move-down', function() {
                const branchId = $(this).data('id');
                const currentOrder = $(this).data('order');
                updateBranchOrder(branchId, currentOrder + 1);
            });

            // Update Branch Order Function
            function updateBranchOrder(branchId, newOrder) {
            $.ajax({
                url: '{{ route('admin.branches.updateOrder') }}', // FIX route
                method: 'POST',
                data: {
                    id: branchId,
                    order: newOrder,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showMessage(response.message);
                        dataTable.ajax.reload(null, false);
                    } else {
                        showMessage(response.message || 'Gagal mengubah urutan', 'error');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan saat mengubah urutan';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showMessage(errorMessage, 'error');
                    console.error('Error:', xhr);
                }
            });
        }

            // Form Submission
            $('#branchForm').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                const url = isEdit ? '{{ route('admin.branches.update', ['id' => ':id']) }}'.replace(':id', $('#branch_id').val()) : '{{ route('admin.branches.store') }}';
                const method = isEdit ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    success: function(response) {
                        $('#addBranchModal').modal('hide');
                        showMessage(isEdit ? 'Cabang berhasil diperbarui!' : 'Cabang berhasil ditambahkan!');
                        dataTable.ajax.reload(null, false);
                        isEdit = false;
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors;
                        if (errors) {
                            Object.keys(errors).forEach(key => {
                                const input = $(`[name="${key}"]`);
                                input.addClass('is-invalid');
                                input.after(`<div class="invalid-feedback">${errors[key][0]}</div>`);
                            });
                        } else {
                            showMessage('Terjadi kesalahan saat menyimpan data', 'error');
                        }
                    }
                });
            });

            // Delete Branch
            $(document).on('click', '.delete-branch', function() {
                deleteId = $(this).data('id');
                const branchName = $(this).data('name');

                $('#deleteModalTitle').text('Hapus Cabang');
                $('#deleteMessage').html(`Yakin ingin menghapus cabang <strong>${branchName}</strong>?`);
                $('#deleteModal').modal('show');
            });

            $('#deleteForm').on('submit', function(e) {
                e.preventDefault();
                const url = '{{ route('admin.branches.delete', ['id' => ':id']) }}'.replace(':id', deleteId);

                $.ajax({
                    url: url,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#deleteModal').modal('hide');
                        showMessage('Cabang berhasil dihapus!');
                        dataTable.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        showMessage(xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data', 'error');
                    }
                });
            });

            $('#addBranchModal').on('hidden.bs.modal', function() {
                isEdit = false;
            });
        });
        </script>
@endsection