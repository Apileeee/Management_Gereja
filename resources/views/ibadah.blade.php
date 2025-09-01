@extends('layouts.app')
@section('title', 'Data Ibadah dan Waktu - Penjadwalan Panggung')
@section('page-title', 'Data Ibadah dan Waktu')
@section('menu-ibadah', 'active')

@section('content')
<section class="section">
    <div class="section-body">
        <div class="card">
            <div class="card-header"><h4>Data Ibadah dan Waktu</h4></div>
            <div class="card-body">

                <!-- Nav Tabs -->
                <ul class="nav nav-tabs" id="ibadahTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#ibadah" role="tab">Data Ibadah</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#waktu" role="tab">Waktu</a>
                    </li>
                </ul>

                <div class="tab-content mt-3">

                    <!-- Data Ibadah -->
                    <div class="tab-pane fade show active" id="ibadah" role="tabpanel">
                        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalTambahIbadah">
                            <i class="fas fa-plus"></i> Tambah Ibadah
                        </button>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped text-center">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Ibadah</th>
                                        <th>Periode</th>
                                        <th>Deskripsi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ibadahs as $index => $ibadah)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $ibadah->nama_ibadah }}</td>
                                        <td>{{ $ibadah->periode->nama_periode ?? '-' }}</td>
                                        <td>{{ $ibadah->deskripsi ?? '-' }}</td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalEditIbadah{{ $ibadah->id }}">Edit</button>
                                            <form action="{{ route('ibadah.destroy', $ibadah->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm btn-hapus-ibadah"><i class="fas fa-trash"></i> Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Data Waktu -->
                    <div class="tab-pane fade" id="waktu" role="tabpanel">
                        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalTambahWaktu">
                            <i class="fas fa-plus"></i> Tambah Waktu
                        </button>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped text-center">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Ibadah</th>
                                        <th>Periode</th>
                                        <th>Tanggal</th>
                                        <th>Jam</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ibadahs as $index => $ibadah)
                                        @if($ibadah->waktu_ibadah)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $ibadah->nama_ibadah }}</td>
                                            <td>{{ $ibadah->periode->nama_periode ?? '-' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($ibadah->waktu_ibadah)->format('d-m-Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($ibadah->waktu_ibadah)->format('H.i') }}</td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalEditWaktu{{ $ibadah->id }}">Edit</button>
                                                <form action="{{ route('ibadah.destroyWaktu', $ibadah->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm btn-hapus-waktu"><i class="fas fa-trash"></i> Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Tambah Ibadah -->
<div class="modal fade" id="modalTambahIbadah" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="{{ route('ibadah.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Ibadah</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Periode</label>
                        <select name="nama_periode" class="form-control" required>
                            <option value="">-- Pilih Periode --</option>
                            @foreach($periodes as $periode)
                                <option value="{{ $periode }}">{{ $periode }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nama Ibadah</label>
                        <input type="text" name="nama_ibadah" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Waktu -->
<div class="modal fade" id="modalTambahWaktu" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="{{ route('ibadah.storeWaktu') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Waktu Ibadah</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Ibadah</label>
                        <select name="id_ibadah" class="form-control" required>
                            <option value="">-- Pilih Ibadah --</option>
                            @foreach($ibadahs as $i)
                                <option value="{{ $i->id }}">{{ $i->nama_ibadah }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal_ibadah" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Waktu</label>
                        <input type="time" name="waktu_ibadah" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- Modal Edit Ibadah -->
@foreach($ibadahs as $ibadah)
<div class="modal fade" id="modalEditIbadah{{ $ibadah->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="{{ route('ibadah.update', $ibadah->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Ibadah</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Periode</label>
                        <select name="nama_periode" class="form-control" required>
                            <option value="">-- Pilih Periode --</option>
                            @foreach($periodes as $periode)
                                <option value="{{ $periode }}" {{ $ibadah->nama_periode == $periode ? 'selected' : '' }}>
                                    {{ $periode }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nama Ibadah</label>
                        <input type="text" name="nama_ibadah" class="form-control" value="{{ $ibadah->nama_ibadah }}" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control">{{ $ibadah->deskripsi }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach

<!-- Modal Edit Waktu -->
@foreach($ibadahs as $ibadah)
@if($ibadah->waktu_ibadah)
<div class="modal fade" id="modalEditWaktu{{ $ibadah->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="{{ route('ibadah.updateWaktu', $ibadah->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Waktu Ibadah</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Ibadah</label>
                        <input type="text" name="nama_ibadah" class="form-control" value="{{ $ibadah->nama_ibadah }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal_ibadah" class="form-control" value="{{ \Carbon\Carbon::parse($ibadah->waktu_ibadah)->format('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Waktu</label>
                        <input type="time" name="waktu_ibadah" class="form-control" value="{{ \Carbon\Carbon::parse($ibadah->waktu_ibadah)->format('H:i') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
@endforeach


@endsection

@push('js')
<script>
$(document).ready(function() {
    $('table').DataTable();
});

// Hapus Ibadah
    $(document).on('click', '.btn-hapus-ibadah', function(e){
        e.preventDefault();
        const form = $(this).closest('form');
        Swal.fire({
            title: 'Hapus Data Ibadah?',
            text: "Data ini akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result)=>{ if(result.isConfirmed) form.submit(); });
    });

    // Hapus Wajty
    $(document).on('click', '.btn-hapus-waktu', function(e){
        e.preventDefault();
        const form = $(this).closest('form');
        Swal.fire({
            title: 'Hapus Data Waktu Ibadah?',
            text: "Data ini akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result)=>{ if(result.isConfirmed) form.submit(); });
    });
</script>
@endpush
