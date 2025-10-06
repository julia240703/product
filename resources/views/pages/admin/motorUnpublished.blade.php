@extends('layouts.appAdmin')
<title>Data Motor Unpublished</title>

@section('content')
    <div class="container-fluid">
        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="title mb-30">
                        <h2>Kelola Data Motor Unpublished</h2>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="breadcrumb-wrapper mb-30">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#0">Admin</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Motor Unpublished</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-success mb-3 btn-sm" data-bs-toggle="modal" data-bs-target="#addMotorModal">Tambah Motor</button>

        <!-- Modal Tambah Motor -->
        <div class="modal fade" id="addMotorModal" tabindex="-1" aria-labelledby="addMotorModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addMotorModalLabel">Tambah Motor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addMotorForm" method="POST" action="{{ route('admin.motors.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Nama Motor <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kode Motor (Harga OTR) <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="motor_code_otr" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kode Motor (Harga Kredit per Kota) <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="motor_code_credit" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">WMS Code <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="wms_code" required>
                            </div>
                            <div class="mb-3">
                                <label for="add_category_id" class="form-label">Kategori <span class="text-red">*</span></label>
                                <select name="category_id" id="add_category_id" class="form-select" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="add_type_id" class="form-label">Tipe Motor <span class="text-red">*</span></label>
                                <select name="type_id" id="add_type_id" class="form-select" required>
                                    <option value="">-- Pilih Tipe Motor --</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deskripsi <span class="text-red">*</span></label>
                                <textarea class="form-control" name="description" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Thumbnail <span class="text-red">*</span></label>
                                <input type="file" class="form-control" name="thumbnail" accept="image/*" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Aksesori Thumbnail <span class="text-red">*</span></label>
                                <input type="file" class="form-control" name="accessory_thumbnail" accept="image/*" required>
                            </div>

                            {{-- 360 --}}
                            <div class="mb-3">
                                <label class="form-label">Upload 360° (GIF) <small class="text-muted">(opsional)</small></label>
                                <input type="file" class="form-control" name="spin_gif" accept="image/gif">
                                <div class="form-text">Format .gif. Kosongkan jika produk tidak memiliki 360°.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status <span class="text-red">*</span></label>
                                <select class="form-select" name="status" required>
                                    <option value="published">Published</option>
                                    <option value="unpublished" selected>Unpublished</option>
                                </select>
                            </div>

                            <!-- NEW -->
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="add_is_new" name="is_new" value="1">
                                <label class="form-check-label" for="add_is_new">Tandai sebagai <strong>NEW</strong></label>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                                <button type="submit" class="btn btn-success">Tambahkan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Edit Motor -->
        <div class="modal fade" id="editMotorModal" tabindex="-1" aria-labelledby="editMotorModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" id="editMotorForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="motor_id" id="edit_motor_id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editMotorModalLabel">Edit Motor</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nama Motor <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="name" id="edit_name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kode Motor (Harga OTR) <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="motor_code_otr" id="edit_motor_code_otr" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kode Motor (Harga Kredit per Kota) <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="motor_code_credit" id="edit_motor_code_credit" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">WMS Code <span class="text-red">*</span></label>
                                <input type="text" class="form-control" name="wms_code" id="edit_wms_code" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_category_id" class="form-label">Kategori <span class="text-red">*</span></label>
                                <select name="category_id" id="edit_category_id" class="form-select" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="edit_type_id" class="form-label">Tipe Motor <span class="text-red">*</span></label>
                                <select name="type_id" id="edit_type_id" class="form-select" required>
                                    <option value="">-- Pilih Tipe Motor --</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deskripsi <span class="text-red">*</span></label>
                                <textarea class="form-control" name="description" id="edit_description" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Thumbnail <span class="text-red">*</span></label>
                                <input type="file" class="form-control" name="thumbnail" id="edit_thumbnail" accept="image/*">
                                <div id="current-thumbnail" class="mt-2"></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Aksesori Thumbnail <span class="text-red">*</span></label>
                                <input type="file" class="form-control" name="accessory_thumbnail" id="edit_accessory_thumbnail" accept="image/*">
                                <div id="current-accessory-thumbnail" class="mt-2"></div>
                            </div>

                            {{-- 360 --}}
                            <div class="mb-3">
                                <label class="form-label">Upload 360° (GIF)</label>
                                <input type="file" class="form-control" name="spin_gif" id="edit_spin_gif" accept="image/gif">
                                <div id="current-spin" class="mt-2"></div>
                                <div class="form-text">Format .gif. Kosongkan jika tidak ingin mengubah.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status <span class="text-red">*</span></label>
                                <select class="form-select" name="status" id="edit_status" required>
                                    <option value="published">Published</option>
                                    <option value="unpublished">Unpublished</option>
                                </select>
                            </div>

                            <!-- NEW -->
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="edit_is_new" name="is_new" value="1">
                                <label class="form-check-label" for="edit_is_new">Tandai sebagai <strong>NEW</strong></label>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                                <button type="submit" class="btn btn-success">Ubah</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Hapus Motor -->
        <div class="modal fade" id="deleteMotorModal" tabindex="-1" aria-labelledby="deleteMotorModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteMotorModalLabel">Konfirmasi Hapus Motor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <form id="deleteMotorForm" method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="motor_id" id="delete_motor_id">
                            <p>Apakah kamu yakin ingin menghapus Motor <strong id="delete_motor_name"></strong>?</p>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal View Image -->
        <div class="modal fade" id="viewImageModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Lihat Gambar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img id="modalImage" src="" alt="Preview" class="img-fluid rounded" style="max-height: 500px;">
                        <div class="mt-2">
                            <p id="imageTitle" class="mb-1 fw-bold"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DataTable -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="display" id="motors-table" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // === DataTables ===
        var dataTable = $('#motors-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.motors.unpublished') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'product', name: 'product', orderable: false, searchable: false },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        let baseUrl = "{{ asset('storage') }}";
                        return `
                            <div class="btn-group">
                                <button class="btn btn-sm btn-primary me-1 editBtn" 
                                    data-id="${data.id}" 
                                    data-name="${data.name}" 
                                    data-motor_code_otr="${data.motor_code_otr}" 
                                    data-motor_code_credit="${data.motor_code_credit}" 
                                    data-wms_code="${data.wms_code}" 
                                    data-category_id="${data.category_id}" 
                                    data-type_id="${data.type_id}" 
                                    data-description="${data.description}" 
                                    data-status="${data.status}"
                                    data-is_new="${data.is_new ? 1 : 0}"
                                    data-thumbnail="${data.thumbnail ? baseUrl + '/' + data.thumbnail : ''}" 
                                    data-accessory_thumbnail="${data.accessory_thumbnail ? baseUrl + '/' + data.accessory_thumbnail : ''}"
                                    data-feature_thumbnail="${data.feature_thumbnail ? baseUrl + '/' + data.feature_thumbnail : ''}"
                                    data-spin_gif="${data.spin_gif ? (String(data.spin_gif).startsWith('http') ? data.spin_gif : baseUrl + '/' + data.spin_gif) : ''}">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button class="btn btn-sm btn-danger deleteBtn" 
                                    data-id="${data.id}" 
                                    data-name="${data.name}">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ]
        });

        function loadTypes(categoryId, targetSelect, selectedTypeId = null) {
            targetSelect.empty().append('<option value="">Memuat...</option>');
            if (categoryId) {
                $.ajax({
                    url: "{{ route('admin.motors.getTypes', ['category_id' => ':id']) }}".replace(':id', categoryId),
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        targetSelect.empty().append('<option value="">-- Pilih Tipe Motor --</option>');
                        if (data.length > 0) {
                            data.forEach(function(type) {
                                let selected = (selectedTypeId && selectedTypeId == type.id) ? 'selected' : '';
                                targetSelect.append('<option value="' + type.id + '" ' + selected + '>' + type.name + '</option>');
                            });
                            if (!selectedTypeId && data.length > 0) {
                                targetSelect.val(data[0].id).trigger('change');
                            }
                        } else {
                            targetSelect.append('<option value="">Tidak ada tipe tersedia</option>');
                        }
                    },
                    error: function(xhr, status, error) {
                        targetSelect.empty().append('<option value="">Gagal memuat tipe (Status: ' + status + ')</option>');
                    }
                });
            } else {
                targetSelect.empty().append('<option value="">-- Pilih Kategori Terlebih Dahulu --</option>');
            }
        }

        // Tambah
        $('#add_category_id').on('change', function() {
            const categoryId = $(this).val();
            loadTypes(categoryId, $('#add_type_id'));
        });

        // Edit
        $('#edit_category_id').on('change', function() {
            const categoryId = $(this).val();
            loadTypes(categoryId, $('#edit_type_id'));
        });

        // === Edit Button Click ===
        $(document).on('click', '.editBtn', function() {
            var motorData = dataTable.row($(this).closest('tr')).data();
            $('#edit_motor_id').val(motorData.id);
            $('#edit_name').val(motorData.name);
            $('#edit_motor_code_otr').val(motorData.motor_code_otr);
            $('#edit_motor_code_credit').val(motorData.motor_code_credit);
            $('#edit_wms_code').val(motorData.wms_code);
            $('#edit_description').val(motorData.description);
            $('#edit_category_id').val(motorData.category_id).trigger('change');
            loadTypes(motorData.category_id, $('#edit_type_id'), motorData.type_id);
            $('#edit_status').val(motorData.status);
            $('#edit_is_new').prop('checked', (motorData.is_new == 1 || motorData.is_new === true));

            // 360 (GIF) preview if exists
            const gifUrl = motorData.spin_gif
                ? (String(motorData.spin_gif).startsWith('http') ? motorData.spin_gif : "{{ asset('storage') }}/" + motorData.spin_gif)
                : '';
            $('#edit_spin_gif').val('');
            if (gifUrl) {
                $('#current-spin').html(`
                    <label class="form-label">360° Saat Ini:</label><br>
                    <img src="${gifUrl}" style="max-width:180px;border-radius:8px;border:1px solid #ddd;">
                `);
            } else {
                $('#current-spin').html('<label class="form-label">360° Saat Ini: Tidak ada</label>');
            }

            // Thumbnail Preview
            if (motorData.thumbnail) {
                let baseUrl = "{{ asset('storage') }}";
                $('#current-thumbnail').html(`
                    <label class="form-label">Thumbnail Saat Ini:</label><br>
                    <img src="${baseUrl}/${motorData.thumbnail}" style="width:120px;height:120px;object-fit:cover;border-radius:8px;border:1px solid #ddd;" class="img-preview">
                `);
            } else {
                $('#current-thumbnail').html('<label class="form-label">Thumbnail Saat Ini: Tidak ada</label>');
            }

            // Accessory Preview
            if (motorData.accessory_thumbnail) {
                let baseUrl = "{{ asset('storage') }}";
                $('#current-accessory-thumbnail').html(`
                    <label class="form-label">Gambar Aksesoris Motor Saat Ini:</label><br>
                    <img src="${baseUrl}/${motorData.accessory_thumbnail}" style="width:120px;height:120px;object-fit:cover;border-radius:8px;border:1px solid #ddd;" class="img-preview">
                `);
            } else {
                $('#current-accessory-thumbnail').html('<label class="form-label">Gambar Aksesoris Motor Saat Ini: Tidak ada</label>');
            }

            // Reset file inputs
            $('#edit_thumbnail').val('');
            $('#edit_accessory_thumbnail').val('');

            const baseEditUrl = "{{ route('admin.motors.update', ['id' => 'DUMMY']) }}".replace('DUMMY', motorData.id);
            $('#editMotorForm').attr('action', baseEditUrl);

            $('#editMotorModal').modal('show');
        });

        // Delete Button Click
        $(document).on('click', '.deleteBtn', function() {
            const motorId = $(this).data('id');
            const motorName = $(this).data('name');
            $('#delete_motor_id').val(motorId);
            $('#delete_motor_name').text(motorName);
            const baseDeleteUrl = "{{ route('admin.motors.delete', ['id' => 'DUMMY']) }}".replace('DUMMY', motorId);
            $('#deleteMotorForm').attr('action', baseDeleteUrl);
            $('#deleteMotorModal').modal('show');
        });

        // View Image Motor
        $(document).on('click', '.image-preview', function() {
            const imageSrc = $(this).data('image');
            const title = $(this).data('title');
            $('#modalImage').attr('src', imageSrc);
            $('#imageTitle').text(title);
            $('#viewImageModal').modal('show');
        });
    });
    </script>
@endsection