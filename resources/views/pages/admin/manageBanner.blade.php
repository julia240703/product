@extends('layouts.appAdmin')
<title>Kelola Banner</title>

@section('content')
    <div class="container-fluid">
        <!-- Header -->
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
                                <li class="breadcrumb-item active">Banner</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Template Button -->
        <button type="button" class="btn btn-success mb-3 btn-sm" data-bs-toggle="modal" data-bs-target="#templateModal">
            Tambah Template Banner
        </button>

        <!-- Template Cards -->
        <div class="row" id="templatesContainer"></div>

        <!-- Banner Table -->
        <div class="card mt-4">
            <div class="card-header">
                <h5>Daftar Banner</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="display" id="banner-table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Template</th>
                                <th>Judul</th>
                                <th>Gambar</th>
                                <th>Status</th>
                                <th>Order</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <!-- Template Modal (Add/Edit) -->
        <div class="modal fade" id="templateModal" tabindex="-1">
            <div class="modal-dialog">
                <form id="templateForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="templateModalTitle">Tambah Template Banner</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="template_id" name="template_id">
                            <div class="mb-3">
                                <label for="template_name" class="form-label">Nama Template<span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="template_name" name="name" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success" id="templateSubmitBtn">Simpan Template</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Banner Modal (Add/Edit) -->
        <div class="modal fade" id="bannerModal" tabindex="-1">
            <div class="modal-dialog">
                <form id="bannerForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="bannerModalTitle">Tambah Banner</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="banner_id" name="id">

                            <div class="mb-3">
                                <label class="form-label">Template<span class="text-danger">*</span></label>
                                <select class="form-select" id="banner_template_id" name="banner_template_id" required>
                                    <option value="">Pilih Template</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Judul Banner</label>
                                <input type="text" class="form-control" id="banner_title" name="title">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Gambar<span class="text-danger"
                                        id="image-required">*</span></label>
                                <input type="file" class="form-control" id="banner_image" name="image"
                                    accept="image/*">
                                <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 10MB</small>
                                <div id="current-image" class="mt-2"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status<span class="text-danger">*</span></label>
                                <select class="form-select" id="banner_status" name="status" required>
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success" id="bannerSubmitBtn">Tambahkan</button>
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
                            <p class="text-danger small" id="deleteWarning" style="display: none;">
                                Template yang memiliki banner tidak dapat dihapus.
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Image View Modal -->
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
                            <p id="imageTemplate" class="text-muted"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="message-container"></div>
    </div>

    <script>
        $(document).ready(function() {
            let dataTable;
            let isEdit = false;
            let deleteType = '';
            let deleteId = '';

            // Initialize
            loadTemplates();
            loadTemplateOptions();
            initDataTable();

            // Initialize DataTable
            function initDataTable() {
                dataTable = $('#banner-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.manage', ['templateId' => 'all']) }}",
                    order: [
                        [0, 'asc']
                    ],
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'template_name',
                            name: 'template_name',
                            orderable: false
                        },
                        {
                            data: 'title',
                            name: 'title'
                        },
                        {
                            data: 'image_path',
                            name: 'image_path',
                            className: "text-center",
                            render: function(data, type, row) {
                                if (row.image_path) {
                                    return `<img src="${row.image_path}" alt="Preview" style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;" class="rounded image-preview" data-image="${row.image_path}" data-title="${row.title || ''}" data-template="${row.template_name}">`;
                                }
                                return '<span class="text-muted">Tidak ada gambar</span>';
                            }
                        },
                        {
                            data: 'status',
                            name: 'status',
                            className: "text-center",
                            render: function(data) {
                                return data === 'active' ?
                                    '<span class="badge bg-success">Aktif</span>' :
                                    '<span class="badge bg-danger">Tidak Aktif</span>';
                            }
                        },
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
                                <button class="btn btn-sm btn-primary me-1 rounded edit-banner" data-id="${data.id}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger rounded delete-banner" data-id="${data.id}" data-title="${data.title || 'Banner'}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        `;
                            }
                        }
                    ]
                });
            }

            // Load Templates
            function loadTemplates() {
                $.get('{{ route('admin.template.manage') }}', function(response) {
                    let html = '';
                    if (response && response.length > 0) {
                        response.forEach(function(template) {
                            html += `
                        <div class="col-md-6 col-lg-4">
                            <div class="template-card">
                                <div class="template-header">
                                    <h6 class="template-title">${template.name}</h6>
                                    <div class="template-actions">
                                        <button class="btn btn-sm btn-success add-banner me-1" data-template-id="${template.id}" title="Tambah Banner">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary edit-template me-1" data-id="${template.id}" title="Edit Template">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-template" data-id="${template.id}" data-name="${template.name}" title="Hapus Template">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <p class="text-muted small mb-0">Total Banner: ${template.banners_count || 0}</p>
                            </div>
                        </div>
                    `;
                        });
                    } else {
                        html = `
                    <div class="col-12">
                        <div class="alert alert-info">
                            <p class="mb-0">Belum ada template banner. Silakan tambah template terlebih dahulu.</p>
                        </div>
                    </div>
                `;
                    }
                    $('#templatesContainer').html(html);
                }).fail(function() {
                    $('#templatesContainer').html(`
                <div class="col-12">
                    <div class="alert alert-danger">
                        <p class="mb-0">Gagal memuat template. Silakan refresh halaman.</p>
                    </div>
                </div>
            `);
                });
            }

            // Load Template Options
            function loadTemplateOptions() {
                $.get('{{ route('admin.template.manage') }}', function(response) {
                    let options = '<option value="">Pilih Template</option>';
                    if (response && response.length > 0) {
                        response.forEach(function(template) {
                            options += `<option value="${template.id}">${template.name}</option>`;
                        });
                    }
                    $('#banner_template_id').html(options);
                });
            }

            // Reset Modal
            function resetModal(modalId, formId) {
                $(modalId).find('form')[0].reset();
                $(modalId).find('.is-invalid').removeClass('is-invalid');
                $(modalId).find('.invalid-feedback').remove();
                $('#current-image').empty();
            }

            // Show Message
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

            // Event Handlers

            // Add Template
            $('#templateModal').on('show.bs.modal', function() {
                if (!isEdit) {
                    resetModal('#templateModal', '#templateForm');
                    $('#templateModalTitle').text('Tambah Template Banner');
                    $('#templateSubmitBtn').text('Simpan Template');
                }
            });

            // Edit Template
            $(document).on('click', '.edit-template', function() {
                const templateId = $(this).data('id');
                isEdit = true;

                $.get(`{{ route('admin.template.manage') }}/${templateId}`, function(template) {
                    resetModal('#templateModal', '#templateForm');
                    $('#template_id').val(template.id);
                    $('#template_name').val(template.name);
                    $('#templateModalTitle').text('Edit Template Banner');
                    $('#templateSubmitBtn').text('Update Template');
                    $('#templateModal').modal('show');
                }).fail(function() {
                    showMessage('Gagal memuat data template', 'error');
                });
            });

            // Delete Template
            $(document).on('click', '.delete-template', function() {
                deleteType = 'template';
                deleteId = $(this).data('id');
                const templateName = $(this).data('name');

                $('#deleteModalTitle').text('Hapus Template');
                $('#deleteMessage').html(
                    `Yakin ingin menghapus template <strong>${templateName}</strong>?`);
                $('#deleteWarning').show();
                $('#deleteModal').modal('show');
            });

            // Add Banner
            $(document).on('click', '.add-banner', function() {
                isEdit = false;
                const templateId = $(this).data('template-id');

                resetModal('#bannerModal', '#bannerForm');
                $('#banner_template_id').val(templateId);
                $('#bannerModalTitle').text('Tambah Banner');
                $('#bannerSubmitBtn').text('Tambahkan');
                $('#image-required').show();
                $('#banner_image').prop('required', true);
                $('#bannerModal').modal('show');
            });

            // Edit Banner
            $(document).on('click', '.edit-banner', function() {
                isEdit = true;
                const data = dataTable.row($(this).parents('tr')).data();

                resetModal('#bannerModal', '#bannerForm');
                $('#banner_id').val(data.id);
                $('#banner_template_id').val(data.banner_template_id);
                $('#banner_title').val(data.title);
                $('#banner_status').val(data.status);
                $('#bannerModalTitle').text('Edit Banner');
                $('#bannerSubmitBtn').text('Update');
                $('#image-required').hide();
                $('#banner_image').prop('required', false);

                if (data.image_path) {
                    $('#current-image').html(`
                <label class="form-label">Gambar Saat Ini:</label><br>
                <img src="${data.image_path}" alt="Current Image" style="width: 100px; height: 100px; object-fit: cover;" class="rounded">
            `);
                }

                $('#bannerModal').modal('show');
            });

            // Delete Banner
            $(document).on('click', '.delete-banner', function() {
                deleteType = 'banner';
                deleteId = $(this).data('id');
                const bannerTitle = $(this).data('title');

                $('#deleteModalTitle').text('Hapus Banner');
                $('#deleteMessage').html(`Yakin ingin menghapus banner <strong>${bannerTitle}</strong>?`);
                $('#deleteWarning').hide();
                $('#deleteModal').modal('show');
            });
            // Move Banner Order Up
            $(document).on('click', '.move-up', function() {
                const bannerId = $(this).data('id');
                const currentOrder = $(this).data('order');

                if (currentOrder > 1) {
                    updateBannerOrder(bannerId, currentOrder - 1);
                }
            });

            // Move Banner Order Down
            $(document).on('click', '.move-down', function() {
                const bannerId = $(this).data('id');
                const currentOrder = $(this).data('order');

                updateBannerOrder(bannerId, currentOrder + 1);
            });

            // Update Banner Order Function
            function updateBannerOrder(bannerId, newOrder) {
                $.ajax({
                    url: '{{ route('admin.updateOrder') }}',
                    method: 'POST',
                    data: {
                        id: bannerId,
                        order: newOrder,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            dataTable.ajax.reload(null, false);
                            loadTemplates();
                        } else {
                            showMessage(response.message || 'Gagal mengubah urutan', 'error');
                        }
                    },
                    error: function(xhr) {
                        showMessage('Terjadi kesalahan saat mengubah urutan', 'error');
                    }
                });
            }
            // View Image
            $(document).on('click', '.image-preview', function() {
                const imageSrc = $(this).data('image');
                const title = $(this).data('title');
                const template = $(this).data('template');

                $('#modalImage').attr('src', imageSrc);
                $('#imageTitle').text(title);
                $('#imageTemplate').text('Template: ' + template);
                $('#viewImageModal').modal('show');
            });

            // Form Submissions

            // Template Form
            $('#templateForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const url = isEdit ? '{{ route('admin.template.edit') }}' :
                    '{{ route('admin.template.store') }}';

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#templateModal').modal('hide');
                        showMessage(isEdit ? 'Template berhasil diperbarui!' :
                            'Template berhasil ditambahkan!');
                        loadTemplates();
                        loadTemplateOptions();
                        isEdit = false;
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors;
                        if (errors) {
                            Object.keys(errors).forEach(key => {
                                const input = $(`[name="${key}"]`);
                                input.addClass('is-invalid');
                                input.after(
                                    `<div class="invalid-feedback">${errors[key][0]}</div>`
                                );
                            });
                        } else {
                            showMessage('Terjadi kesalahan saat menyimpan data', 'error');
                        }
                    }
                });
            });

            // Banner Form
            $('#bannerForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const url = isEdit ? '{{ route('admin.edit') }}' : '{{ route('admin.store') }}';

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#bannerModal').modal('hide');
                        showMessage(isEdit ? 'Banner berhasil diperbarui!' :
                            'Banner berhasil ditambahkan!');
                        dataTable.ajax.reload(null, false);
                        loadTemplates();
                        isEdit = false;
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors;
                        if (errors) {
                            Object.keys(errors).forEach(key => {
                                const input = $(`[name="${key}"]`);
                                input.addClass('is-invalid');
                                input.after(
                                    `<div class="invalid-feedback">${errors[key][0]}</div>`
                                );
                            });
                        } else {
                            showMessage('Terjadi kesalahan saat menyimpan data', 'error');
                        }
                    }
                });
            });

            // Delete Form
            $('#deleteForm').on('submit', function(e) {
                e.preventDefault();
                const url = deleteType === 'template' ? '{{ route('admin.template.delete') }}' :
                    '{{ route('admin.delete') }}';
                const data = {
                    id: deleteId
                };

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#deleteModal').modal('hide');
                        showMessage(
                            `${deleteType === 'template' ? 'Template' : 'Banner'} berhasil dihapus!`
                        );

                        if (deleteType === 'template') {
                            loadTemplates();
                            loadTemplateOptions();
                        } else {
                            dataTable.ajax.reload(null, false);
                            loadTemplates();
                        }
                    },
                    error: function(xhr) {
                        showMessage(xhr.responseJSON?.message ||
                            'Terjadi kesalahan saat menghapus data', 'error');
                    }
                });
            });

            // File Validation
            $('#banner_image').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const fileSizeMB = file.size / (1024 * 1024);
                    if (fileSizeMB > 10) {
                        alert(
                            `Ukuran file terlalu besar! Maksimal 10MB. Ukuran file Anda: ${fileSizeMB.toFixed(2)}MB`
                            );
                        this.value = '';
                        return false;
                    }
                }
            });

            // Reset isEdit when modals are hidden
            $('#templateModal, #bannerModal').on('hidden.bs.modal', function() {
                isEdit = false;
            });
        });
    </script>
@endsection
