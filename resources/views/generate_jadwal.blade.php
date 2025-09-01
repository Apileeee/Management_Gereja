@extends('layouts.app')

@section('title', 'Generate Jadwal Ibadah')
@section('page-title', 'Generate Jadwal Ibadah dengan Algoritma Genetika')

@push('css')
<style>
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        display: {{ session('generation_stats') ? 'block' : 'none' }};
    }
    .stat-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        padding: 5px 0;
        border-bottom: 1px solid rgba(255,255,255,0.2);
    }
    .stat-item:last-child { border-bottom: none; margin-bottom: 0; }
    .loading-spinner {
        display: none;
        text-align: center;
        padding: 30px;
        background-color: #f8f9fa;
        border-radius: 10px;
        margin: 20px 0;
    }
    .table-hover tbody tr:hover { background-color: #f8f9fa; }
    .generate-form {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    .btn-generate {
        background: linear-gradient(45deg, #007bff, #0056b3);
        border: none;
        padding: 12px 25px;
        font-weight: bold;
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        transition: all 0.3s ease;
    }
    .btn-generate:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4); }
    .btn-generate:disabled { background: #6c757d; transform: none; box-shadow: none; }
    .alert-info { border-left: 4px solid #17a2b8; background-color: #d1ecf1; color: #0c5460; }

    .table th, .table td {
        vertical-align: middle !important;
        text-align: center;
    }
    .table thead th {
        background: #343a40;
        color: #fff;
    }
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f9f9f9;
    }
    .table-striped tbody tr:nth-of-type(even) {
        background-color: #f1f1f1;
    }
    .preview-table {
        background: linear-gradient(145deg, #f8f9fa, #e9ecef);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .btn-save {
        background: linear-gradient(45deg, #28a745, #20c997);
        border: none;
        padding: 12px 25px;
        font-weight: bold;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        transition: all 0.3s ease;
    }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4); }
    .btn-regenerate {
        background: linear-gradient(45deg, #ffc107, #e0a800);
        border: none;
        padding: 12px 25px;
        font-weight: bold;
        box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
        transition: all 0.3s ease;
        color: #212529;
    }
    .btn-regenerate:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
        color: #212529;
    }
    .selected-periode {
        background-color: #e3f2fd;
        border: 2px solid #2196f3;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
    }
    .periode-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .selected-periode {
    background: linear-gradient(135deg, #e0f7fa, #b2ebf2);
    border-left: 5px solid #00acc1;
    border-radius: 12px;
    padding: 20px 25px;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    }
    .selected-periode:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.15);
    }

    .periode-info {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 15px;
    }

    .periode-info i {
        font-size: 2.2rem;
        color: #00acc1;
    }

    .periode-info .periode-text {
        display: flex;
        flex-direction: column;
    }

    .periode-text strong {
        font-size: 1.2rem;
        color: #007c91;
    }

    .periode-text small {
        font-size: 0.9rem;
        color: #555;
}

</style>
@endpush

@section('content')
<div class="section">
    <div class="section-body">

        {{-- Alert Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        {{-- Card Generator --}}
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-robot"></i> Generate Jadwal dengan Algoritma Genetika</h4>
                <div class="card-header-action">
                    <small class="text-muted">Optimisasi otomatis untuk distribusi jadwal yang seimbang</small>
                </div>
            </div>
            <div class="card-body">

                {{-- Form pilih periode --}}
                <div class="generate-form">
                    <form id="generateForm" action="{{ route('generate.jadwal') }}" method="POST" class="d-flex align-items-center" style="gap: 15px;">
                        @csrf
                        <select name="periode" id="periode" class="form-control form-control-lg" required style="min-width: 250px;">
                            <option value="">-- Pilih Periode --</option>
                            @foreach($periodes as $periode_layanan)
                                <option value="{{ $periode_layanan->nama_periode }}"
                                    {{ $selectedPeriode == $periode_layanan->nama_periode ? 'selected' : '' }}>
                                    {{ $periode_layanan->nama_periode }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary btn-generate" id="generateBtn">
                            {{ $selectedPeriode && session('generated_jadwal') ? 'Generate' : 'Generate' }}
                        </button>
                    </form>

                    @if(!$selectedPeriode && !session('generated_jadwal'))
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i>
                            Pilih periode layanan terlebih dahulu lalu klik tombol Generate untuk melihat preview jadwal.
                        </div>
                    @elseif($selectedPeriode && !session('generated_jadwal'))
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle"></i>
                            Periode <strong>{{ $periodes->firstWhere('nama_periode', $selectedPeriode)->nama_periode ?? 'Unknown' }}</strong> sudah dipilih. 
                            Klik tombol <strong>"Generate Jadwal"</strong> untuk memulai proses optimisasi.
                        </div>
                    @endif
                </div>

                {{-- Loading Spinner --}}
                <div class="loading-spinner" id="loadingSpinner">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
                    <h5 class="mt-3">Algoritma Genetika Sedang Bekerja...</h5>
                    <p class="text-muted">Mengoptimalkan jadwal agar distribusi seimbang</p>
                </div>

                {{-- Preview Hasil Generate (sebelum disimpan) --}}
                @if(session('generated_jadwal') && $selectedPeriode)
                    @php 
                        $jadwalPreview = session('generated_jadwal');
                        $currentPeriode = $periodes->firstWhere('nama_periode', $selectedPeriode);
                    @endphp
                    <div class="preview-table" id="previewContainer">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5><i class="fas fa-eye text-info"></i> Preview Jadwal: {{ $currentPeriode->nama_periode ?? 'Unknown' }}</h5>
                            <div>
                                <form action="{{ route('generate.jadwal') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="periode" value="{{ $selectedPeriode }}">
                                    <button type="submit" class="btn btn-warning btn-regenerate mr-2">
                                        <i class="fas fa-redo"></i> Generate
                                    </button>
                                </form>
                                <form action="{{ route('generate.jadwal.save') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="periode" value="{{ $selectedPeriode }}">
                                    <button type="submit" class="btn btn-success btn-save" id="simpanJadwalBtn">
                                        <i class="fas fa-save"></i> Simpan ke Database
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Ini adalah preview jadwal hasil generate untuk periode <strong>{{ $currentPeriode->nama_periode ?? 'Unknown' }}</strong>. 
                            Silakan review terlebih dahulu sebelum menyimpan ke database.
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover text-center align-middle">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Ibadah</th>
                                        <th>Tanggal</th>
                                        <th>Jam</th>
                                        <th>Personil</th>
                                        <th>Alat Musik</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @foreach($jadwalPreview as $ibadah)
                                        @php
                                            $tanggal = \Carbon\Carbon::parse($ibadah['waktu_ibadah'])->format('d-m-Y');
                                            $jam = \Carbon\Carbon::parse($ibadah['waktu_ibadah'])->format('H:i');
                                            $rowspan = count($ibadah['alat_assignments']);
                                        @endphp
                                        
                                        @foreach($ibadah['alat_assignments'] as $index => $assignment)
                                            <tr>
                                                @if($index == 0)
                                                    <td rowspan="{{ $rowspan }}">{{ $no++ }}</td>
                                                    <td rowspan="{{ $rowspan }}" class="font-weight-bold">{{ $ibadah['nama_ibadah'] }}</td>
                                                    <td rowspan="{{ $rowspan }}"><i class="fas fa-calendar-alt text-primary"></i> {{ $tanggal }}</td>
                                                    <td rowspan="{{ $rowspan }}"><i class="fas fa-clock text-success"></i> {{ $jam }}</td>
                                                @endif
                                                <td>{{ $assignment['nama_pemain'] }}</td>
                                                <td>{{ $assignment['nama_alat'] }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

            </div>
        </div>

        {{-- Card kedua: Data Jadwal yang Telah Tersimpan --}}
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-database"></i> Data Jadwal yang Tersimpan</h4>
                <div class="d-flex align-items-center">
                    @if(!empty($jadwalTersimpan) && count($jadwalTersimpan) > 0)
                        <span class="badge badge-primary mr-3">{{ count($jadwalTersimpan) }} Data</span>
                    @endif
                    <div class="input-group" style="width: 300px;">
                        <input type="text" class="form-control" placeholder="Cari data jadwal..." id="searchInput">
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($jadwalTersimpan->isEmpty())
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada jadwal yang tersimpan</h5>
                        <p class="text-muted">Generate jadwal terlebih dahulu untuk melihat data di sini</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="jadwalSavedTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Periode</th>
                                    <th>Nama Ibadah</th>
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                    <th>Personil</th>
                                    <th>Alat Musik</th>
                                </tr>
                            </thead>
                            <tbody>
                            @php
                                $grouped = collect($jadwalTersimpan)->groupBy(function($item) {
                                    return $item->nama_ibadah.'|'.$item->waktu_ibadah;
                                });
                                $rowNumber = 1;
                            @endphp

                            @foreach($grouped as $groupKey => $items)
                                @php
                                    [$namaIbadah, $waktuIbadah] = explode('|', $groupKey);
                                    $rowspan = count($items);
                                    $tanggal = \Carbon\Carbon::parse($waktuIbadah)->format('d-m-Y');
                                    $jam = \Carbon\Carbon::parse($waktuIbadah)->format('H:i');
                                @endphp

                                @foreach($items as $idx => $j)
                                    <tr>
                                        @if($idx == 0)
                                            <td rowspan="{{ $rowspan }}">{{ $rowNumber++ }}</td>
                                            <td rowspan="{{ $rowspan }}"><span class="badge badge-secondary">{{ $j->nama_periode }}</span></td>
                                            <td rowspan="{{ $rowspan }}" class="font-weight-bold">{{ $namaIbadah }}</td>
                                            <td rowspan="{{ $rowspan }}"><i class="fas fa-calendar-alt text-primary"></i> {{ $tanggal }}</td>
                                            <td rowspan="{{ $rowspan }}"><i class="fas fa-clock text-success"></i> {{ $jam }}</td>
                                        @endif
                                        <td>{{ $j->nama_pemain }}</td>
                                        <td>{{ $j->nama_alat ?: 'Tidak ada' }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Variabel dari server
    var selectedPeriode = @json($selectedPeriode);
    var jadwalTersimpanCount = {{ $jadwalTersimpan->count() }};
    var hasGeneratedJadwal = {{ session()->has('generated_jadwal') ? 'true' : 'false' }};
    var hasGeneratedJadwalLastPeriod = {{ session()->has('last_generated_periode') ? 'true' : 'false' }};
    var lastGeneratedPeriode = @json(session('last_generated_periode'));

    // Jika periode terpilih, tapi tidak ada data tersimpan untuk periode itu,
    // dan sebelumnya ada hasil generate periode lain, maka otomatis generate ulang
    if (selectedPeriode && jadwalTersimpanCount === 0 && hasGeneratedJadwalLastPeriod && lastGeneratedPeriode !== selectedPeriode) {
        Swal.fire({
            title: 'Data Tidak Ditemukan',
            text: `Tidak ada data jadwal tersimpan untuk periode "${selectedPeriode}". Sistem akan melakukan generate ulang secara otomatis.`,
            icon: 'info',
            timer: 4000,
            timerProgressBar: true,
            showConfirmButton: false,
            didClose: () => {
                // Submit form generate otomatis dengan periode terpilih
                $('<form>', {
                    'method': 'POST',
                    'action': '{{ route("generate.jadwal") }}'
                })
                .append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': '{{ csrf_token() }}'
                }))
                .append($('<input>', {
                    'type': 'hidden',
                    'name': 'periode',
                    'value': selectedPeriode
                }))
                .appendTo('body')
                .submit();
            }
        });
    }

    // ============ SUCCESS NOTIFICATIONS ============
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        showConfirmButton: true,
        confirmButtonText: 'OK',
        confirmButtonColor: '#28a745',
        timer: 5000,
        timerProgressBar: true,
        toast: false,
        position: 'center'
    });
    @endif

    // ============ ERROR NOTIFICATIONS ============
    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Oops! Terjadi Kesalahan',
        text: '{{ session('error') }}',
        showConfirmButton: true,
        confirmButtonText: 'Tutup',
        confirmButtonColor: '#dc3545',
        footer: '<small>Silakan coba lagi atau hubungi administrator</small>'
    });
    @endif

    // ============ FORM VALIDATION ============
    $('#generateForm').on('submit', function(e) {
        var periode = $('#periode').val();
        
        if (!periode) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Periode Belum Dipilih',
                text: 'Silakan pilih periode layanan terlebih dahulu!',
                showConfirmButton: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#ffc107'
            });
            return false;
        }

        // Show loading notification
        $('#generateBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generating...');
        $('#loadingSpinner').show();
        
        // Loading notification dengan progress
        Swal.fire({
            title: 'Algoritma Genetika Sedang Bekerja...',
            html: `
                <div class="text-center">
                    <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;"></div>
                    <p class="mb-2">Mengoptimalkan distribusi jadwal untuk periode <strong>${$('#periode option:selected').text()}</strong></p>
                    <div class="progress mt-3">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             style="width: 0%" id="progressBar"></div>
                    </div>
                </div>
            `,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                // Simulate progress
                let progress = 0;
                const interval = setInterval(() => {
                    progress += Math.random() * 15;
                    if (progress > 90) progress = 90;
                    $('#progressBar').css('width', progress + '%');
                }, 200);
                
                // Store interval untuk clear nanti
                window.loadingInterval = interval;
            }
        });
    });

    // ============ SAVE CONFIRMATION ============
    $('#simpanJadwalBtn').on('click', function(e) {
        e.preventDefault();
        var btn = $(this);
        var periodeName = '{{ $selectedPeriode && isset($currentPeriode) ? $currentPeriode->nama_periode : "jadwal ini" }}';

        Swal.fire({
            title: 'Konfirmasi Simpan Jadwal',
            html: `
                <div class="text-left">
                    <p>Apakah Anda yakin ingin menyimpan jadwal untuk periode:</p>
                    <div class="alert alert-info text-center">
                        <strong>${periodeName}</strong>
                    </div>
                    <div class="text-danger small">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Perhatian:</strong> Jadwal yang sudah tersimpan tidak dapat diubah atau dihapus.
                    </div>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-save"></i> Ya, Simpan',
            cancelButtonText: '<i class="fas fa-times"></i> Batal',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            reverseButtons: true,
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
                
                Swal.fire({
                    title: 'Menyimpan Jadwal...',
                    text: 'Mohon tunggu sebentar',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                btn.closest('form').submit();
            }
        });
    });

    // ============ REGENERATE CONFIRMATION ============
    $('.btn-regenerate').on('click', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        var periodeName = '{{ $selectedPeriode && isset($currentPeriode) ? $currentPeriode->nama_periode : "" }}';

        Swal.fire({
            title: 'Generate Ulang Jadwal?',
            html: `
                <div class="text-left">
                    <p>Anda akan men-generate ulang jadwal untuk periode:</p>
                    <div class="alert alert-warning text-center">
                        <strong>${periodeName}</strong>
                    </div>
                    <p class="text-muted small">
                        <i class="fas fa-info-circle"></i>
                        Jadwal yang sudah di-preview akan diganti dengan hasil generate yang baru.
                    </p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-redo"></i> Ya, Generate Ulang',
            cancelButtonText: '<i class="fas fa-times"></i> Batal',
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // ============ WELCOME NOTIFICATION ============
    @if(!session('generated_jadwal') && !$selectedPeriode)
    Swal.fire({
        title: 'Selamat Datang!',
        html: `
            <div class="text-left">
                <p>Gunakan <strong>Algoritma Genetika</strong> untuk mengoptimalkan jadwal ibadah Anda.</p>
                <div class="alert alert-info">
                    <i class="fas fa-lightbulb"></i>
                    <strong>Fitur Utama:</strong>
                    <ul class="text-left mt-2 mb-0">
                        <li>Distribusi personil yang seimbang</li>
                        <li>Optimisasi otomatis</li>
                        <li>Preview sebelum menyimpan</li>
                    </ul>
                </div>
            </div>
        `,
        icon: 'info',
        confirmButtonText: 'Mulai Generate',
        confirmButtonColor: '#007bff',
        showClass: {
            popup: 'animate__animated animate__fadeInDown'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp'
        }
    });
    @endif

    // ============ PERIODE SELECTION NOTIFICATION ============
    @if($selectedPeriode && !session('generated_jadwal'))
    Swal.fire({
        title: 'Periode Terpilih',
        html: `
            <div class="text-center">
                <div class="alert alert-success">
                    <i class="fas fa-calendar-check fa-2x mb-2"></i>
                    <h5>{{ $periodes->firstWhere('nama_periode', $selectedPeriode)->nama_periode ?? 'Unknown' }}</h5>
                </div>
                <p>Klik tombol <strong>"Generate"</strong> untuk memulai optimisasi jadwal.</p>
            </div>
        `,
        icon: 'success',
        confirmButtonText: 'Siap Generate!',
        confirmButtonColor: '#28a745',
        timer: 3000,
        timerProgressBar: true
    });
    @endif

    // ============ UTILITY FUNCTIONS ============
    function showToast(icon, title, text) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icon,
            title: title,
            text: text,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    // ============ UTILITY FUNCTIONS ============
    
    // Function untuk show toast notification
    function showToast(icon, title, text) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icon,
            title: title,
            text: text,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    // Function untuk konfirmasi action dengan custom message
    function confirmAction(title, text, confirmText, cancelText, callback) {
        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed && typeof callback === 'function') {
                callback();
            }
        });
    }

    // ============ ADDITIONAL FEATURES ============
    
  

    // ============ FORM SUBMISSIONS ============
    
    // Loading spinner dengan SweetAlert untuk form generate
    $('#generateForm').on('submit', function(e) {
        var periode = $('#periode').val();
        
        if (!periode) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Periode Belum Dipilih',
                text: 'Silakan pilih periode layanan terlebih dahulu!',
                confirmButtonText: 'OK',
                confirmButtonColor: '#ffc107'
            });
            return false;
        }

        // Disable button dan show loading
        $('#generateBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generating...');
        $('#loadingSpinner').show();
        
        // Show SweetAlert loading
        Swal.fire({
            title: 'Memproses Algoritma Genetika',
            html: `
                <div class="text-center">
                    <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;"></div>
                    <p class="mb-2">Mengoptimalkan jadwal untuk periode:</p>
                    <div class="alert alert-info">
                        <strong>${$('#periode option:selected').text()}</strong>
                    </div>
                    <div class="progress mt-3">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-gradient" 
                             style="width: 0%" id="progressBar"></div>
                    </div>
                </div>
            `,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            background: '#f8f9fa',
            didOpen: () => {
                // Animate progress bar
                let progress = 0;
                const interval = setInterval(() => {
                    progress += Math.random() * 12;
                    if (progress > 85) progress = 85;
                    $('#progressBar').css('width', progress + '%');
                }, 300);
                
                window.loadingInterval = interval;
            }
        });
    });

    // ============ SAVE TO DATABASE ============
    
    $('#simpanJadwalBtn').on('click', function(e) {
        e.preventDefault();
        var btn = $(this);
        var periodeName = '{{ $selectedPeriode && isset($currentPeriode) ? $currentPeriode->nama_periode : "jadwal ini" }}';
        var totalData = $('#previewContainer tbody tr').length;

        Swal.fire({
            title: 'Konfirmasi Simpan Jadwal',
            html: `
                <div class="text-left">
                    <div class="alert alert-warning">
                        <i class="fas fa-database"></i>
                        <strong>Periode:</strong> ${periodeName}
                    </div>
                    <p class="text-center"><strong>Lanjutkan penyimpanan?</strong></p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-save"></i> Ya, Simpan Sekarang',
            cancelButtonText: '<i class="fas fa-times"></i> Batal',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            reverseButtons: true,
            focusCancel: true,
            customClass: {
                confirmButton: 'btn btn-success btn-lg',
                cancelButton: 'btn btn-secondary btn-lg'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show saving progress
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
                
                Swal.fire({
                    title: 'Menyimpan ke Database...',
                    html: `
                        <div class="text-center">
                            <div class="spinner-grow text-success mb-3"></div>
                            <p>Menyimpan ${totalData} data jadwal</p>
                            <div class="progress">
                                <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                                     style="width: 100%"></div>
                            </div>
                        </div>
                    `,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false
                });
                
                // Submit form
                setTimeout(() => {
                    btn.closest('form').submit();
                }, 1000);
            }
        });
    });

    // ============ AUTO HIDE ALERTS ============
    
    // Auto hide bootstrap alerts dengan fade effect
    setTimeout(function() {
        $('.alert:not(.alert-permanent)').each(function() {
            $(this).fadeOut('slow', function() {
                $(this).remove();
            });
        });
    }, 8000);

    // ============ PERIODE CHANGE HANDLER ============
    
    $('#periode').on('change', function() {
        var selectedValue = $(this).val();
        var selectedText = $(this).find('option:selected').text();
        
        if (selectedValue) {
            // Show notification periode berubah
            showToast('info', 'Periode Dipilih', `Periode "${selectedText}" telah dipilih`);
            
            // Redirect dengan loading
            setTimeout(() => {
                window.location.href = '{{ route("generate.jadwal") }}' + '?periode=' + selectedValue;
            }, 1000);
        }
    });

    // ============ PREVIEW SCROLL ============
    
    // Auto scroll ke preview dengan notification
    @if(session('generated_jadwal'))
    setTimeout(function() {
        Swal.fire({
            toast: true,
            position: 'top',
            icon: 'success',
            title: 'Jadwal berhasil di-generate!',
            text: 'Scroll ke bawah untuk melihat preview',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });
        
        // Auto scroll
        setTimeout(() => {
            $('html, body').animate({
                scrollTop: $('#previewContainer').offset().top - 100
            }, 1500);
        }, 500);
    }, 800);
    @endif

    // ============ KEYBOARD SHORTCUTS ============
    
    // Keyboard shortcuts dengan notification
    $(document).on('keydown', function(e) {
        // Ctrl + G untuk generate
        if (e.ctrlKey && e.key === 'g') {
            e.preventDefault();
            if ($('#periode').val()) {
                $('#generateForm').submit();
            } else {
                showToast('warning', 'Shortcut', 'Pilih periode terlebih dahulu (Ctrl+G)');
            }
        }
        
        // Ctrl + S untuk save (jika ada preview)
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            if ($('#simpanJadwalBtn').length) {
                $('#simpanJadwalBtn').click();
            } else {
                showToast('info', 'Shortcut', 'Tidak ada jadwal untuk disimpan (Ctrl+S)');
            }
        }
    });

    // ============ NETWORK ERROR HANDLING ============
    
    // Handle network errors
    window.addEventListener('beforeunload', function() {
        if (window.loadingInterval) {
            clearInterval(window.loadingInterval);
        }
    });

    // ============ DATA VALIDATION NOTIFICATIONS ============
    
    // Notification jika data kosong
    @if($jadwalTersimpan->isEmpty() && !session('generated_jadwal'))
    setTimeout(function() {
        showToast('info', 'Info', 'Belum ada data jadwal. Mulai dengan generate jadwal baru!');
    }, 2000);
    @endif

    // ============ SUCCESS AFTER SAVE ============
    
    // Notification khusus setelah berhasil save
    @if(session('jadwal_saved'))
    Swal.fire({
        icon: 'success',
        title: 'Jadwal Berhasil Disimpan!',
        html: `
            <div class="text-center">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <p>Jadwal untuk periode <strong>{{ session('saved_periode') }}</strong> telah tersimpan ke database.</p>
                <div class="alert alert-success">
                    <i class="fas fa-database"></i>
                    Data dapat dilihat di tabel "Data Jadwal yang Tersimpan" di bawah.
                </div>
            </div>
        `,
        showConfirmButton: true,
        confirmButtonText: 'Lihat Data Tersimpan',
        confirmButtonColor: '#28a745',
        showClass: {
            popup: 'animate__animated animate__bounceIn'
        }
    }).then(() => {
        // Scroll ke tabel data tersimpan
        $('html, body').animate({
            scrollTop: $('#jadwalSavedTable').offset().top - 100
        }, 1000);
    });
    @endif

});

// ============ GLOBAL HELPER FUNCTIONS ============

// Function untuk show loading dengan custom message
function showCustomLoading(title, message) {
    Swal.fire({
        title: title,
        text: message,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

// Function untuk close loading
function closeLoading() {
    Swal.close();
    if (window.loadingInterval) {
        clearInterval(window.loadingInterval);
    }
}

// Function untuk error handling
function showError(title, message, footer = null) {
    Swal.fire({
        icon: 'error',
        title: title,
        text: message,
        footer: footer,
        confirmButtonText: 'Tutup',
        confirmButtonColor: '#dc3545'
    });
}

// Function untuk success notification dengan action
function showSuccessWithAction(title, message, actionText, actionCallback) {
    Swal.fire({
        icon: 'success',
        title: title,
        text: message,
        showCancelButton: true,
        confirmButtonText: actionText,
        cancelButtonText: 'Tutup',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed && typeof actionCallback === 'function') {
            actionCallback();
        }
    });
}
</script>
@endpush


