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
        display: none;
    }
    .stat-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        padding: 5px 0;
        border-bottom: 1px solid rgba(255,255,255,0.2);
    }
    .stat-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
    .loading-spinner {
        display: none;
        text-align: center;
        padding: 30px;
        background-color: #f8f9fa;
        border-radius: 10px;
        margin: 20px 0;
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    .badge-personil {
        background-color: #28a745;
        color: white;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 0.8em;
    }
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
    .btn-generate:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
    }
    .btn-generate:disabled {
        background: #6c757d;
        transform: none;
        box-shadow: none;
    }
    .alert-info {
        border-left: 4px solid #17a2b8;
        background-color: #d1ecf1;
        color: #0c5460;
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

                {{-- Form untuk pilih periode dan generate --}}
                <div class="generate-form">
                    <form id="generateForm" action="{{ route('generate.jadwal') }}" method="GET">
                        <div class="row align-items-end">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label for="periode" class="form-label font-weight-bold">
                                        <i class="fas fa-calendar-alt"></i> Pilih Periode Layanan:
                                    </label>
                                    <select name="periode" id="periode" class="form-control form-control-lg" required>
                                        <option value="">-- Pilih Periode --</option>
                                        @foreach($periodes as $periode_layanan)
                                            <option value="{{ $periode_layanan->id }}"
                                                {{ $selectedPeriode == $periode_layanan->id ? 'selected' : '' }}>
                                                {{ $periode_layanan->nama_periode }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <button type="submit" class="btn btn-primary btn-generate btn-lg btn-block" id="generateBtn">
                                        <i class="fas fa-dna"></i> Generate Jadwal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    @if(!$selectedPeriode)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Petunjuk:</strong> Pilih periode layanan terlebih dahulu, kemudian klik tombol "Generate dengan Genetika" untuk membuat jadwal otomatis.
                    </div>
                    @endif
                </div>

                {{-- Loading Spinner --}}
                <div class="loading-spinner" id="loadingSpinner">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="sr-only">Generating...</span>
                    </div>
                    <h5 class="mt-3">Algoritma Genetika Sedang Bekerja...</h5>
                    <p class="text-muted">Sedang mengoptimalkan jadwal untuk menghindari bentrok personil dan distribusi yang seimbang</p>
                    <div class="progress mt-3" style="width: 300px; margin: 0 auto;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             role="progressbar" style="width: 100%"></div>
                    </div>
                </div>

                {{-- Statistik Algoritma Genetika --}}
                @if(isset($generationStats) && $generationStats)
                <div class="stats-card" id="statsCard">
                    <h5><i class="fas fa-chart-line"></i> Statistik Algoritma Genetika</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="stat-item">
                                <span><i class="fas fa-layer-group"></i> Generasi:</span>
                                <strong>{{ $generationStats['generations'] }}</strong>
                            </div>
                            <div class="stat-item">
                                <span><i class="fas fa-clock"></i> Waktu Eksekusi:</span>
                                <strong>{{ $generationStats['execution_time'] }}s</strong>
                            </div>
                            <div class="stat-item">
                                <span><i class="fas fa-trophy"></i> Fitness Terbaik:</span>
                                <strong>{{ number_format($generationStats['best_fitness'], 1) }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-item">
                                <span><i class="fas fa-church"></i> Total Ibadah:</span>
                                <strong>{{ $generationStats['total_ibadah'] }}</strong>
                            </div>
                            <div class="stat-item">
                                <span><i class="fas fa-users"></i> Total Pemain:</span>
                                <strong>{{ $generationStats['total_pemain'] }}</strong>
                            </div>
                            <div class="stat-item">
                                <span><i class="fas fa-check-circle"></i> Status:</span>
                                <strong class="text-light">
                                    <i class="fas fa-check"></i> Optimisasi Berhasil
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Tabel hasil generate --}}
                <div id="jadwalTableContainer" style="{{ empty($jadwal) ? 'display:none;' : '' }}">
                    @if(!empty($jadwal))
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-check text-success"></i> 
                            Jadwal Hasil Generate ({{ count($jadwal) }} Ibadah)
                        </h5>
                        <button class="btn btn-success" id="simpanJadwalBtn">
                            <i class="fas fa-save"></i> Simpan Jadwal
                        </button>
                    </div>
                    @endif
                    
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-striped table-hover" id="jadwalTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="15%">Periode</th>
                                    <th width="25%">Nama Ibadah</th>
                                    <th width="15%">Waktu Ibadah</th>
                                    <th width="25%">Personil</th>
                                    <th width="15%">Alat Musik</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jadwal as $index => $j)
                                <tr>
                                    <td class="text-center font-weight-bold">{{ $index + 1 }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $j['periode'] }}</span>
                                    </td>
                                    <td class="font-weight-bold">{{ $j['nama_ibadah'] }}</td>
                                    <td>
                                        <i class="fas fa-clock text-primary"></i>
                                        {{ \Carbon\Carbon::parse($j['waktu_ibadah'])->format('d-m-Y H:i') }}
                                    </td>
                                    <td>
                                        <span class="badge-personil">
                                            <i class="fas fa-users"></i> {{ $j['jumlah_personil'] ?? 'N/A' }}
                                        </span>
                                        <div class="mt-1 small">{{ $j['personil'] }}</div>
                                    </td>
                                    <td>
                                        <i class="fas fa-music text-warning"></i>
                                        {{ $j['alat'] ?: 'Tidak ada' }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-calendar-times fa-2x mb-2"></i>
                                        <br>Belum ada jadwal yang digenerate
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card kedua: Data Jadwal yang Telah Ditambahkan --}}
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-database"></i> Data Jadwal yang Telah Tersimpan</h4>
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
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="jadwalSavedTable">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="15%">Periode</th>
                                <th width="25%">Nama Ibadah</th>
                                <th width="15%">Waktu Ibadah</th>
                                <th width="25%">Personil</th>
                                <th width="15%">Alat Musik</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jadwalTersimpan as $index => $j)
                            <tr>
                                <td class="text-center font-weight-bold">{{ $index + 1 }}</td>
                                <td>
                                    <span class="badge badge-secondary">{{ $j->periode }}</span>
                                </td>
                                <td class="font-weight-bold">{{ $j->nama_ibadah }}</td>
                                <td>
                                    <i class="fas fa-clock text-primary"></i>
                                    {{ \Carbon\Carbon::parse($j->waktu_ibadah)->format('d-m-Y H:i') }}
                                </td>
                                <td>
                                    <span class="badge-personil">
                                        <i class="fas fa-users"></i> {{ $j->jumlah_personil }}
                                    </span>
                                    <div class="mt-1 small">{{ $j->personil }}</div>
                                </td>
                                <td>
                                    <i class="fas fa-music text-warning"></i>
                                    {{ $j->alat_musik ?: 'Tidak ada' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <br>Belum ada jadwal yang disimpan
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Modal Konfirmasi Simpan --}}
<div class="modal fade" id="simpanModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-save"></i> Konfirmasi Simpan Jadwal</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menyimpan jadwal yang telah digenerate ini?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Perhatian:</strong> Jika sudah ada jadwal untuk periode yang sama, maka jadwal lama akan ditimpa.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="confirmSimpan">
                    <i class="fas fa-save"></i> Ya, Simpan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection


@push('js')
<script>
$(document).ready(function() {
    // Initialize DataTables
    let jadwalTable = null;
    let savedTable = null;

    function initializeTables() {
        if (jadwalTable) {
            jadwalTable.destroy();
        }
        if (savedTable) {
            savedTable.destroy();
        }

        jadwalTable = $('#jadwalTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            },
            "pageLength": 10,
            "responsive": true,
            "order": [[3, 'asc']] // Sort by waktu ibadah
        });

        savedTable = $('#jadwalSavedTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            },
            "pageLength": 10,
            "responsive": true,
            "order": [[3, 'asc']] // Sort by waktu ibadah
        });
    }

    // Initialize tables on page load
    initializeTables();

    // Form Generate Handler
    $('#generateForm').on('submit', function(e) {
        const periodeValue = $('#periode').val();
        
        if (!periodeValue) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Periode Belum Dipilih',
                text: 'Silakan pilih periode layanan terlebih dahulu!',
                confirmButtonColor: '#007bff'
            });
            return false;
        }

        // Show loading spinner
        $('#loadingSpinner').show();
        $('#generateBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generating...');
        $('#jadwalTableContainer').hide();
        $('#statsCard').hide();

        // Submit form (akan refresh page dengan hasil)
        return true;
    });

    // Show stats and table if data exists
    @if(!empty($jadwal))
        $('#jadwalTableContainer').show();
        @if(isset($generationStats))
            $('#statsCard').show();
        @endif
    @endif

    // Simpan Jadwal Handler
    $('#simpanJadwalBtn').on('click', function() {
        const periodeId = $('#periode').val();
        
        if (!periodeId) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Periode tidak ditemukan!',
            });
            return;
        }

        $('#simpanModal').modal('show');
    });

    // Confirm Simpan
    $('#confirmSimpan').on('click', function() {
        const periodeId = $('#periode').val();
        
        // Collect jadwal data
        const jadwalData = [];
        @if(!empty($jadwal))
            @foreach($jadwal as $j)
                jadwalData.push({
                    ibadah_id: {{ $j['ibadah_id'] ?? 'null' }},
                    personil: "{{ addslashes($j['personil']) }}",
                    alat_musik: "{{ addslashes($j['alat']) }}",
                    pemain_ids: @json($j['pemain_ids'] ?? [])
                });
            @endforeach
        @endif

        // Show loading
        $('#confirmSimpan').html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').prop('disabled', true);

        // AJAX Request to save
        $.ajax({
            url: "{{ route('generate.jadwal.simpan') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                periode_id: periodeId,
                jadwal_data: jadwalData
            },
            success: function(response) {
                $('#simpanModal').modal('hide');
                
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        // Reload page to show updated saved schedules
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message,
                    });
                }
            },
            error: function(xhr) {
                $('#simpanModal').modal('hide');
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat menyimpan jadwal.',
                });
                console.error(xhr.responseText);
            },
            complete: function() {
                $('#confirmSimpan').html('<i class="fas fa-save"></i> Ya, Simpan').prop('disabled', false);
            }
        });
    });

    // Search functionality for saved table
    $('#searchInput').on('keyup', function() {
        savedTable.search(this.value).draw();
    });

    // Period change handler
    $('#periode').on('change', function() {
        const selectedText = $(this).find('option:selected').text();
        if ($(this).val()) {
            $('#generateBtn').removeClass('btn-secondary').addClass('btn-primary');
        } else {
            $('#generateBtn').removeClass('btn-primary').addClass('btn-secondary');
        }
    });

    // Hide loading on page load
    $('#loadingSpinner').hide();
    $('#generateBtn').prop('disabled', false).html('<i class="fas fa-dna"></i> Generate Jadwal');
});
</script>

{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush