@extends('layouts.appAdmin')
<title>Data Motor</title>

@section('content')
    <div class="container-fluid">
        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="title mb-30">
                        <h2>Produk Sepeda Motor Honda</h2>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="breadcrumb-wrapper mb-30">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#0">Admin</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Motor</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-success mb-3 btn-sm" data-bs-toggle="modal" data-bs-target="#addMotorModal">Tambah Motor</button>

        <div class="row mb-4">
    <!-- Published -->
    <div class="col-md-6">
        <div class="card shadow-lg border-0 h-100 hover-card">
            <div class="card-body text-center py-5">
                <i class="bi bi-check-circle-fill text-success display-4 mb-3"></i>
                <h5 class="fw-bold text-dark fs-4 mb-2">Published</h5>
                <h1 class="fw-bold text-success mt-2">{{ $publishedCount }}</h1>
                <a href="{{ route('admin.motors.published') }}" class="btn btn-outline-success mt-3 px-4 rounded-pill">
                    <i class="bi bi-eye"></i> Lihat Data
                </a>
            </div>
        </div>
    </div>
            <!-- Unpublished -->
    <div class="col-md-6">
        <div class="card shadow-lg border-0 h-100 hover-card">
            <div class="card-body text-center py-5">
                <i class="bi bi-x-circle-fill text-danger display-4 mb-3"></i>
                <h5 class="fw-bold text-dark fs-4 mb-2">Unpublished</h5>
                <h1 class="fw-bold text-danger mt-2">{{ $unpublishedCount }}</h1>
                <a href="{{ route('admin.motors.unpublished') }}" class="btn btn-outline-danger mt-3 px-4 rounded-pill">
                    <i class="bi bi-eye-slash"></i> Lihat Data
                </a>
            </div>
        </div>
    </div>
</div>

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
                            <div class="mb-3">
                                <label class="form-label">Status <span class="text-red">*</span></label>
                                <select class="form-select" name="status" required>
                                    <option value="published">Published</option>
                                    <option value="unpublished">Unpublished</option>
                                </select>
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

        <!-- Script for add modal (no DataTable here, since this is dashboard) -->
        <script>
        $(document).ready(function() {
            // === Event Dependent Dropdown Tambah ===
            $('#add_category_id').on('change', function() {
                const categoryId = $(this).val();
                loadTypes(categoryId, $('#add_type_id'));
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
        });
        </script>
@endsection