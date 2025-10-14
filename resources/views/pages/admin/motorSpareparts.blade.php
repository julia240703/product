@extends('layouts.appAdmin')
<title>Kelola Sparepart Motor</title>

@section('content')
    <div class="container-fluid">
        <div class="title-wrapper pt-30">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="title mb-30">
                        <h2>Data Sparepart Motor</h2>
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
                                    Motor Sparepart
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alerts (pakai key khusus agar tidak dobel sama layout) --}}
        @if(session('success_parts'))
            <div id="success-message" class="alert alert-success">{{ session('success_parts') }}</div>
        @endif
        @if($errors->any())
            <div id="error-message" class="alert alert-danger">
                @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
            </div>
        @endif

        {{-- CTA Upload / Ganti --}}
        <button type="button"
                class="btn btn-success mb-3 btn-sm"
                data-bs-toggle="modal"
                data-bs-target="#addModal">
            {{ $motor->parts_pdf ? 'Ganti Katalog PDF' : 'Upload Katalog PDF' }}
        </button>

        {{-- Modal Upload/Ganti PDF --}}
        <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addSparepartLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addSparepartLabel">
                            {{ $motor->parts_pdf ? 'Ganti Katalog PDF' : 'Upload Katalog PDF' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST"
                              action="{{ route('admin.spareparts.store', $motor->id) }}"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="parts_pdf" class="form-label">File PDF <span class="text-danger">*</span></label>
                                <input type="file"
                                       class="form-control"
                                       id="parts_pdf"
                                       name="parts_pdf"
                                       accept="application/pdf"
                                       required>
                                <div class="form-text">
                                    Format: PDF • Maks 50MB. Mengunggah file baru akan <strong>mengganti</strong> file yang ada.
                                </div>
                            </div>
                            <div class="modal-footer px-0">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                                <button type="submit" class="btn btn-success">
                                    {{ $motor->parts_pdf ? 'Simpan Perubahan' : 'Unggah' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kartu Status + Aksi --}}
        <div class="card">
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="p-3 border rounded h-100">
                            <div class="d-flex align-items-start justify-content-between mb-2">
                                <div>
                                    <div class="fw-bold">Status Katalog Motor</div>
                                    <div class="text-muted" style="font-size:13px;">{{ $motor->name }}</div>
                                </div>
                                @if($motor->parts_pdf_url)
                                    <span class="badge bg-success">Sudah diunggah</span>
                                @else
                                    <span class="badge bg-secondary">Belum ada</span>
                                @endif
                            </div>

                            @if($motor->parts_pdf_url)
                                <div class="mb-3">
                                    <a class="btn btn-primary btn-sm me-2" target="_blank" href="{{ $motor->parts_pdf_url }}">
                                        <i class="fa-solid fa-file-pdf me-1"></i> Buka di Tab Baru
                                    </a>

                                    <button class="btn btn-outline-success btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#addModal">
                                        <i class="fa-solid fa-rotate me-1"></i> Ganti PDF
                                    </button>
                                </div>

                                {{-- Tombol hapus memicu modal konfirmasi --}}
                                <button type="button"
                                        class="btn btn-outline-danger btn-sm deletePdfBtn"
                                        data-action="{{ route('admin.spareparts.delete', [$motor->id, 0]) }}"
                                        data-name="{{ $motor->name }}">
                                    <i class="fa-solid fa-trash me-1"></i> Hapus PDF
                                </button>
                            @else
                                <div class="text-muted">
                                    Belum ada PDF diunggah. Klik tombol <strong>Upload Katalog PDF</strong> di atas.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="p-0">
                            <div class="fw-bold mb-2">Pratinjau</div>
                            @if($motor->parts_pdf_url)
                                <iframe src="{{ $motor->parts_pdf_url }}"
                                        style="width:100%; height:70vh; border:1px solid #e3e3e3; border-radius:8px;">
                                </iframe>
                            @else
                                <div class="text-muted">
                                    Pratinjau akan tampil setelah PDF diunggah.
                                </div>
                            @endif
                        </div>
                    </div>
                </div> {{-- /row --}}
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus PDF --}}
    <div class="modal fade" id="deletePdfModal" tabindex="-1" aria-labelledby="deletePdfLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="deletePdfForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deletePdfLabel">Konfirmasi Hapus Katalog</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        Apakah kamu yakin ingin menghapus katalog PDF untuk motor
                        <strong id="pdfMotorName">-</strong>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Script kecil --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // reset input saat modal upload dibuka
            document.addEventListener('shown.bs.modal', function(e){
                if (e.target?.id === 'addModal') {
                    const input = document.getElementById('parts_pdf');
                    if (input) input.value = '';
                }
            });

            // trigger modal konfirmasi hapus
            document.body.addEventListener('click', function (e) {
                const btn = e.target.closest('.deletePdfBtn');
                if (!btn) return;

                const action = btn.getAttribute('data-action');
                const name   = btn.getAttribute('data-name') || '';

                document.getElementById('deletePdfForm').setAttribute('action', action);
                document.getElementById('pdfMotorName').textContent = name.toUpperCase();

                const modal = new bootstrap.Modal(document.getElementById('deletePdfModal'));
                modal.show();
            });
        });
    </script>
@endsection