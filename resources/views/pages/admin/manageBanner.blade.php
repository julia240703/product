@extends('layouts.appAdmin')
<title>Kelola Banner</title>

@section('content')
    <div class="container-fluid">
        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="title mb-30">
                        <h2>Kelola Banner</h2>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="breadcrumb-wrapper mb-30">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Admin</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Banner</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tombol Tambah Banner -->
        <button type="button" class="btn btn-success mb-3 btn-sm" data-bs-toggle="modal"
            data-bs-target="#addBannerModal">Tambah Banner</button>

        <!-- Modal Tambah Banner -->
        <div class="modal fade" id="addBannerModal" tabindex="-1" aria-labelledby="addBannerModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.banner.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Banner</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="title" class="form-label">Judul Banner</label>
                                <input type="text" class="form-control" name="title">
                            </div>
                            <div class="mb-3">
                                <label for="position" class="form-label">Posisi<span
                                        class="text-danger">*</span></label>
                                <select class="form-control" name="position" required>
                                    <option value="1">Home</option>
                                    <option value="2">Produk</option>
                                    <option value="3">Aksesoris</option>
                                    <option value="4">Parts</option>
                                    <option value="5">Apparel</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Gambar</label>
                                <input type="file" class="form-control" name="image" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 10MB</small>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" name="status">
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">Simpan Banner</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Banner -->
        <div class="modal fade" id="editBannerModal" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.banner.edit') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="edit_banner_id" name="id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Banner</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="title" class="form-label">Judul Banner</label>
                                <input type="text" class="form-control" id="edit_title" name="title">
                            </div>
                            <div class="mb-3">
                                <label for="position" class="form-label">Posisi<span
                                        class="text-danger">*</span></label>
                                <select class="form-control" id="edit_position" name="position" required>
                                    <option value="1">Home</option>
                                    <option value="2">About</option>
                                    <option value="3">Contact</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Gambar</label>
                                <input type="file" class="form-control" id="edit_image" name="image"
                                    accept="image/*">
                                <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 10MB. Kosongkan jika tidak
                                    ingin mengubah gambar.</small>
                                <div id="current-image" class="mt-2"></div>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" id="edit_status" name="status">
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">Update Banner</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Delete Banner -->
        <div class="modal fade" id="deleteBannerModal" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.banner.delete') }}">
                    @csrf
                    <input type="hidden" name="id" id="delete_banner_id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Hapus Banner</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Yakin ingin menghapus Banner <strong id="delete_banner_name"></strong>?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                        </div>
                    </div>
                </form>
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
                        <img id="modalImage" src="" alt="Preview" class="img-fluid rounded"
                            style="max-height: 500px;">
                        <div class="mt-2">
                            <p id="imageTitle" class="mb-1 fw-bold"></p>
                            <p id="imageDescription" class="text-muted"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Banner -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="display" id="banners-table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID Banner</th>
                                <th>Judul Banner</th>
                                <th>Posisi</th>
                                <th>Gambar</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <div id="message-container"></div>
    </div>

    <script>
        $(document).ready(function() {
            // Fungsi validasi ukuran file
            function validateFileSize(input, maxSizeMB = 10) {
                const file = input.files[0];
                if (file) {
                    const fileSizeMB = file.size / (1024 * 1024);
                    if (fileSizeMB > maxSizeMB) {
                        alert(
                            `Ukuran file terlalu besar! Maksimal ${maxSizeMB}MB. Ukuran file Anda: ${fileSizeMB.toFixed(2)}MB`
                        );
                        input.value = ''; // Reset input
                        return false;
                    }
                }
                return true;
            }

            // Validasi file saat dipilih - Modal Tambah
            $('input[name="image"]').on('change', function() {
                validateFileSize(this);
            });

            // Validasi file saat dipilih - Modal Edit
            $('#edit_image').on('change', function() {
                validateFileSize(this);
            });

            var dataTable = $('#banners-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.banner.data') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'id', name: 'id' },
                    { data: 'title', name: 'title' },
                    {
                        data: 'position',
                        name: 'position',
                        render: function(data) {
                            return data === 1 ? 'Home' : data === 2 ? 'About' : data === 3 ? 'Contact' : 'Unknown';
                        }
                    },
                    {
                        data: 'image_path',
                        name: 'image_path',
                        className: "text-center",
                        render: function(data, type, row) {
                            if (row.image_path) {
                                return `<img src="${row.image_path}" alt="Preview" style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;" class="rounded image-preview" data-image="${row.image_path}" data-title="${row.title}" data-description="${row.position_name || ''}">`;
                            } else {
                                return '<span class="text-muted">Tidak ada gambar</span>';
                            }
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: "text-center",
                        render: function(data) {
                            return data === 1 ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Nonaktif</span>';
                        }
                    },
                    {
                        data: null,
                        className: "text-center",
                        render: function(data) {
                            return `
                <div class="btn-group">
                <button class="btn btn-sm btn-primary me-1 rounded editBtn" data-id="${data.id}"><i class="fa fa-edit"></i></button>
                <button class="btn btn-sm btn-danger me-1 rounded deleteBtn" data-id="${data.id}" data-name="${data.title}"><i class="fa fa-trash"></i></button>
                </div>
                `;
                        }
                    }
                ]
            });

            // Open edit modal
            $(document).on('click', '.editBtn', function() {
                const data = dataTable.row($(this).parents('tr')).data();
                $('#edit_banner_id').val(data.id);
                $('#edit_title').val(data.title);
                $('#edit_position').val(data.position);
                $('#edit_status').val(data.status);

                // Tampilkan gambar saat ini
                if (data.image_path) {
                    $('#current-image').html(`
                <label class="form-label">Gambar Saat Ini:</label><br>
                <img src="${data.image_path}" alt="Current Image" style="width: 100px; height: 100px; object-fit: cover;" class="rounded">
            `);
                } else {
                    $('#current-image').html('<small class="text-muted">Belum ada gambar</small>');
                }

                $('#editBannerModal').modal('show');
            });

            // Validasi file form submit
            $('#addBannerModal form').on('submit', function(e) {
                const imageInput = $(this).find('input[name="image"]')[0];
                if (imageInput && imageInput.files.length > 0) {
                    if (!validateFileSize(imageInput)) {
                        e.preventDefault();
                        return false;
                    }
                }
            });

            // Delete modal
            $(document).on('click', '.deleteBtn', function() {
                $('#delete_banner_id').val($(this).data('id'));
                $('#delete_banner_name').text($(this).data('name'));
                $('#deleteBannerModal').modal('show');
            });

            // View image modal
            $(document).on('click', '.image-preview', function() {
                const imageSrc = $(this).data('image');
                const title = $(this).data('title');
                const description = $(this).data('description');

                $('#modalImage').attr('src', imageSrc);
                $('#imageTitle').text(title);
                $('#imageDescription').text(description);
                $('#viewImageModal').modal('show');
            });
        });
    </script>
    <style>
        .template-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .template-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .template-title {
            font-weight: 600;
            font-size: 16px;
            color: #333;
            margin: 0;
        }

        .template-options {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .template-options li {
            padding: 5px 0;
            color: #666;
        }

        .template-options li:before {
            content: "• ";
            color: #007bff;
            font-weight: bold;
        }

        .option-row {
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 10px;
            background: #f8f9fa;
        }

        .template-header .btn {
            padding: 4px 8px;
        }
    </style>
@endsection