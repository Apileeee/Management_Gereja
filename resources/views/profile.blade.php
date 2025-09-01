@extends('layouts.app')
@section('title', 'Manajemen Profil - Penjadwalan Panggung')
@section('page-title', 'Manajemen Profil')
@section('profile', 'active')

@section('content')
<section class="section">
    <div class="section-body d-flex justify-content-center">
        <div class="card" style="width: 600px;"> <!-- Lebar diperbesar -->
            <div class="card-body text-center p-4"> <!-- Padding ditambah -->
                <!-- Foto Profil -->
                <img src="{{ auth()->user()->foto ? asset('storage/' . auth()->user()->foto) : asset('assets/img/usernopp.png') }}" 
                     alt="Profil" class="rounded-circle mb-3" width="100">

                <!-- Nama dan Role -->
                <h5 class="card-title">{{ auth()->user()->nama_user }}</h5>
                <p class="text-muted">Admin</p>

                <!-- Notifikasi Sukses -->
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Form Update Profil -->
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Ganti Password -->
                    <div class="form-group text-left">
                        <label>Password Baru</label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin diganti">
                        @error('password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group text-left">
                        <label>Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password">
                    </div>

                    <!-- Ganti Nama -->
                    <div class="form-group text-left">
                        <label>Nama Admin</label>
                        <input type="text" name="nama_user" class="form-control" value="{{ old('nama_user', auth()->user()->nama_user) }}" placeholder="Kosongkan jika tidak ingin diganti">
                        @error('nama_user')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Upload Foto Profile -->
                    <div class="form-group text-left">
                        <label>Foto Profil</label>
                        <input type="file" name="profile_photo" class="form-control-file">
                        @error('profile_photo')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Simpan Perubahan</button>
                </form>

            </div>
        </div>
    </div>
</section>
@endsection
