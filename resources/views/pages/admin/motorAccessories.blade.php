@extends('layouts.appAdmin')
<title>Kelola Aksesoris Motor</title>

@section('content')
    <div class="container-fluid">
        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="title mb-30">
                        <h2>Data Aksesoris Motor</h2>
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
                                    Motor Accessory
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-success mb-3 btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
            Tambah Aksesoris
        </button>

        <!-- Modal Tambah -->
        <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addAccessoryLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAccessoryLabel">Tambah Aksesoris Motor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('admin.accessories.store', $motor->id) }}" enctype="multipart/form-data" id="addAccessoryForm">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Aksesoris <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Gambar <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label for="part_number" class="form-label">Part Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="part_number" name="part_number">
                            </div>
                            <div class="mb-3">
                                <label for="dimension" class="form-label">Dimensi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="dimension" name="dimension">
                            </div>
                            <div class="mb-3">
                                <label for="weight" class="form-label">Berat (gram) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="weight" name="weight">
                            </div>
                            <div class="mb-3">
                                <label for="color" class="form-label">Warna</label>
                                <input type="text" class="form-control" id="color" name="color">
                            </div>
                            <div class="mb-3">
                                <label for="material" class="form-label">Material</label>
                                <input type="text" class="form-control" id="material" name="material">
                            </div>
                            <div class="mb-3">
                                <label for="stock" class="form-label">Stok</label>
                                <input type="number" class="form-control" id="stock" name="stock" min="0" value="0">
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="description" name="description"></textarea>
                            </div>

                            {{-- ===== HOTSPOT PICKER (ADD) ===== --}}
                            <div class="mb-3">
                                <label class="form-label">Tentukan Titik pada Gambar (klik)</label>
                                <div id="pickerAccAdd" class="position-relative"
                                     style="border:1px dashed #dee2e6;border-radius:12px;overflow:hidden;">
                                    <img id="pickerAccAddImage"
                                         src="{{ $motor->accessory_thumbnail ? asset('storage/'.$motor->accessory_thumbnail) : ($motor->thumbnail ? asset('storage/'.$motor->thumbnail) : asset('placeholder.png')) }}"
                                         alt="Base Image" class="img-fluid w-100">
                                    <div id="pickerAccAddDot"
                                         class="position-absolute"
                                         style="width:18px;height:18px;border-radius:50%;background:#dc3545;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.25);pointer-events:none;display:none;">
                                    </div>
                                </div>
                                <small class="text-muted">Klik pada gambar untuk mengisi X/Y (0–100, persen).</small>
                            </div>
                            <div class="mb-3">
                                <label for="x_percent" class="form-label">Posisi X (%)</label>
                                <input type="number" min="0" max="100" step="1" class="form-control" id="x_percent" name="x_percent">
                            </div>
                            <div class="mb-3">
                                <label for="y_percent" class="form-label">Posisi Y (%)</label>
                                <input type="number" min="0" max="100" step="1" class="form-control" id="y_percent" name="y_percent">
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

        <!-- Modal Edit -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editAccessoryLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editAccessoryLabel">Ubah Data Aksesoris Motor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editAccessoryForm" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="name_edit" class="form-label">Nama Aksesoris <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name_edit" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="image_edit" class="form-label">Gambar <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="image_edit" name="image" accept="image/*">
                                <div id="current-image" class="mt-2"></div>
                            </div>
                            <div class="mb-3">
                                <label for="part_number_edit" class="form-label">Part Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="part_number_edit" name="part_number">
                            </div>
                            <div class="mb-3">
                                <label for="dimension_edit" class="form-label">Dimensi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="dimension_edit" name="dimension">
                            </div>
                            <div class="mb-3">
                                <label for="weight_edit" class="form-label">Berat (gram) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="weight_edit" name="weight">
                            </div>
                            <div class="mb-3">
                                <label for="color_edit" class="form-label">Warna</label>
                                <input type="text" class="form-control" id="color_edit" name="color">
                            </div>
                            <div class="mb-3">
                                <label for="material_edit" class="form-label">Material</label>
                                <input type="text" class="form-control" id="material_edit" name="material">
                            </div>
                            <div class="mb-3">
                                <label for="stock_edit" class="form-label">Stok</label>
                                <input type="number" class="form-control" id="stock_edit" name="stock" min="0" value="0">
                            </div>
                            <div class="mb-3">
                                <label for="description_edit" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="description_edit" name="description"></textarea>
                            </div>

                            {{-- ===== HOTSPOT PICKER (EDIT) ===== --}}
                            <div class="mb-3">
                                <label class="form-label">Tentukan Titik pada Gambar (klik)</label>
                                <div id="pickerAccEdit" class="position-relative"
                                     style="border:1px dashed #dee2e6;border-radius:12px;overflow:hidden;">
                                    <img id="pickerAccEditImage"
                                         src="{{ $motor->accessory_thumbnail ? asset('storage/'.$motor->accessory_thumbnail) : ($motor->thumbnail ? asset('storage/'.$motor->thumbnail) : asset('placeholder.png')) }}"
                                         alt="Base Image" class="img-fluid w-100">
                                    <div id="pickerAccEditDot"
                                         class="position-absolute"
                                         style="width:18px;height:18px;border-radius:50%;background:#dc3545;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.25);pointer-events:none;display:none;">
                                    </div>
                                </div>
                                <small class="text-muted">Klik pada gambar untuk mengisi X/Y (0–100, persen).</small>
                            </div>
                            <div class="mb-3">
                                <label for="x_percent_edit" class="form-label">Posisi X (%)</label>
                                <input type="number" min="0" max="100" step="1" class="form-control" id="x_percent_edit" name="x_percent">
                            </div>
                            <div class="mb-3">
                                <label for="y_percent_edit" class="form-label">Posisi Y (%)</label>
                                <input type="number" min="0" max="100" step="1" class="form-control" id="y_percent_edit" name="y_percent">
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                                <button type="submit" class="btn btn-success">Ubah</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Hapus -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteAccessoryLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteAccessoryLabel">Konfirmasi Hapus Aksesoris</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <p>Apakah kamu yakin ingin menghapus aksesoris <strong id="delete_accessory_name"></strong>?</p>
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
                        <img id="modalImage" src="" alt="Preview" class="img-fluid rounded" style="max-height: 500px;">
                        <div class="mt-2">
                            <p id="imageTitle" class="mb-1 fw-bold"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="display" id="accessory-table" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Aksesoris</th>
                                <th>Gambar</th>
                                <th>Posisi (X,Y)</th>
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

    // ================== DATATABLE ==================
    var dataTable = $('#accessory-table').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.accessories.index', $motor->id) }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            {
                data: 'image',
                name: 'image',
                className: "text-center",
                render: function(data, type, row) {
                    return data
                        ? `<img src="${data}" style="width:50px;height:50px;object-fit:cover;cursor:pointer;" class="rounded image-preview" data-image="${data}" data-title="${row.name || ''}">`
                        : '<span class="text-muted">Tidak ada gambar</span>';
                }
            },
            { // tampilkan koordinat kalau ada
                data: null,
                orderable: false,
                searchable: false,
                render: function(row) {
                    const x = row.x_percent ?? null;
                    const y = row.y_percent ?? null;
                    return (x !== null && y !== null) ? `${x},${y}` : '<span class="text-muted">-</span>';
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data) {
                    return `
                        <div class="btn-group">
                            <button class="btn btn-sm btn-primary me-1 editBtn" data-id="${data.id}">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button class="btn btn-sm btn-danger deleteBtn" data-id="${data.id}" data-name="${data.name}">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ]
    });

    // ========== EDIT ==========
    $(document).on('click', '.editBtn', function() {
        var accessoryData = dataTable.row($(this).closest('tr')).data();

        $('#name_edit').val(accessoryData.name);
        $('#part_number_edit').val(accessoryData.part_number);
        $('#dimension_edit').val(accessoryData.dimension);
        $('#weight_edit').val(accessoryData.weight);
        $('#description_edit').val(accessoryData.description);

        $('#color_edit').val(accessoryData.color || '');
        $('#material_edit').val(accessoryData.material || '');
        $('#stock_edit').val(accessoryData.stock ?? 0);

        // set koordinat
        $('#x_percent_edit').val(accessoryData.x_percent ?? '');
        $('#y_percent_edit').val(accessoryData.y_percent ?? '');

        if (accessoryData.image) {
            $('#current-image').html(`
                <label class="form-label">Gambar Saat Ini:</label><br>
                <img src="${accessoryData.image}" alt="Current Image" style="width: 100px; height: 100px; object-fit: cover;" class="rounded">
            `);
        } else {
            $('#current-image').empty();
        }

        // Set dynamic action untuk form edit
        const editForm = $('#editAccessoryForm');
        const baseEditUrl = "{{ route('admin.accessories.update', ['motor' => $motor->id, 'id' => 'DUMMY']) }}".replace('DUMMY', accessoryData.id);
        editForm.attr('action', baseEditUrl);

        // buka modal -> init picker setelah tampil
        $('#editModal').one('shown.bs.modal', function(){ initAccPicker(this, 'edit'); }).modal('show');
    });

    // ========== DELETE ==========
    $(document).on('click', '.deleteBtn', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        $('#delete_accessory_name').text(name);
        const form = $('#deleteForm');
        const baseUrl = "{{ route('admin.accessories.delete', ['motor' => $motor->id, 'id' => 'DUMMY']) }}".replace('DUMMY', id);
        form.attr('action', baseUrl);
        $('#deleteModal').modal('show');
    });

    // ========== VIEW IMAGE ==========
    $(document).on('click', '.image-preview', function() {
        $('#modalImage').attr('src', $(this).data('image'));
        $('#imageTitle').text($(this).data('title'));
        $('#viewImageModal').modal('show');
    });

    // Reset form saat modal ditutup
    $('.modal').on('hidden.bs.modal', function () {
        $(this).find('form')[0]?.reset();
        $('#current-image').empty();
        const dot = this.querySelector('#pickerAccAddDot, #pickerAccEditDot');
        if (dot) dot.style.display = 'none';
    });

    // ========== PICKER: klik gambar -> isi X/Y (persen) ==========
    // init saat modal Tambah dibuka
    $('#addModal').on('shown.bs.modal', function(){ initAccPicker(this, 'add'); });
    // init saat modal Edit dibuka (fallback kalau dibuka tanpa klik editBtn)
    $('#editModal').on('shown.bs.modal', function(){ initAccPicker(this, 'edit'); });

    function initAccPicker(modalEl, mode){
        // pilih elemen sesuai mode (add/edit)
        const img  = modalEl.querySelector(mode==='add' ? '#pickerAccAddImage' : '#pickerAccEditImage');
        const dot  = modalEl.querySelector(mode==='add' ? '#pickerAccAddDot'   : '#pickerAccEditDot');
        const xInp = modalEl.querySelector(mode==='add' ? 'input[name="x_percent"]' : '#x_percent_edit');
        const yInp = modalEl.querySelector(mode==='add' ? 'input[name="y_percent"]' : '#y_percent_edit');
        if(!img || !dot || !xInp || !yInp) return;

        // pastikan tidak double-bind
        if (img._pickerClick) img.removeEventListener('click', img._pickerClick);

        img._pickerClick = function(e){
            const rect = img.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width)  * 100;
            const y = ((e.clientY - rect.top)  / rect.height) * 100;

            const xVal = Math.max(0, Math.min(100, Math.round(x)));
            const yVal = Math.max(0, Math.min(100, Math.round(y)));

            xInp.value = xVal;
            yInp.value = yVal;

            dot.style.left = xVal + '%';
            dot.style.top  = yVal + '%';
            dot.style.transform = 'translate(-50%, -50%)';
            dot.style.display   = 'block';
        };

        if (img.complete && img.naturalWidth) {
            img.addEventListener('click', img._pickerClick);
        } else {
            img.addEventListener('load', () => img.addEventListener('click', img._pickerClick), { once:true });
        }

        // render dot jika sudah ada nilai (mode edit)
        if (xInp.value !== '' && yInp.value !== '') {
            dot.style.left = xInp.value + '%';
            dot.style.top  = yInp.value + '%';
            dot.style.transform = 'translate(-50%, -50%)';
            dot.style.display   = 'block';
        } else {
            dot.style.display = 'none';
        }
    }

});
</script>
@endsection