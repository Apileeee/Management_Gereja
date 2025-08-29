<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Alat Musik & Personil - Penjadwalan Pelayanan Panggung Gereja</title>

  <!-- General CSS -->
  <link rel="stylesheet" href="/assets/css/app.min.css">
  <link rel="stylesheet" href="/assets/bundles/datatables/datatables.min.css">
  <link rel="stylesheet" href="/assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/components.css">
  <link rel="stylesheet" href="/assets/css/custom.css">

  <link rel='shortcut icon' type='image/x-icon' href='/assets/img/icngereja.ico' />

  <!-- Custom CSS -->
  <style>
    /* Tab aktif biru */
    .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
        background-color: #007bff;
        color: white !important;
    }

    /* Thumbnail foto personil */
    .personil-photo {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 50%;
    }
  </style>
</head>

<body>
<div class="loader"></div>
<div id="app">
  <div class="main-wrapper main-wrapper-1">
    <div class="navbar-bg"></div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg main-navbar sticky">
      <div class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
          <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg collapse-btn"><i data-feather="align-justify"></i></a></li>
          <li><a href="#" class="nav-link nav-link-lg fullscreen-btn"><i data-feather="maximize"></i></a></li>
        </ul>
        <h4 class="font-weight-bold mt-2">Alat Musik & Personil</h4>
      </div>
      <ul class="navbar-nav navbar-right">
        <li>
          <a href="#" id="btnLogout" class="nav-link nav-link-lg mr-5" title="Logout">
            <i data-feather="log-out"></i>
          </a>
        </li>
      </ul>
    </nav>

    <!-- Form hidden logout -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
      @csrf
    </form>

    <!-- Sidebar -->
    <div class="main-sidebar sidebar-style-2">
      <aside id="sidebar-wrapper">
        <div class="sidebar-brand d-flex align-items-center p-3 border-bottom">
          <img alt="image" src="/assets/img/usernopp.png" class="rounded-circle" style="width:45px;height:45px;">
          <div class="ml-4 d-flex flex-column justify-content-center">
            <span class="font-weight-bold" style="line-height:2;">Admin</span>
            <small style="line-height:2;">Sistem Penjadwalan</small>
          </div>
        </div>
        <ul class="sidebar-menu mt-3">
          <li class="menu-header">Main Menu</li>
          <li><a href="{{ route('dashboard') }}" class="nav-link"><i data-feather="calendar"></i><span>Data Periode</span></a></li>
          <li class="active"><a href="{{ route('musikpersonil') }}" class="nav-link"><i data-feather="music"></i><span>Alat Musik & Personil</span></a></li>
          <li><a href="#" class="nav-link"><i data-feather="clock"></i><span>Data Ibadah & Waktu</span></a></li>
          <li><a href="#" class="nav-link"><i data-feather="cpu"></i><span>Generate Genetika</span></a></li>
          <li><a href="#" class="nav-link"><i data-feather="settings"></i><span>Manajemen Profil</span></a></li>
        </ul>
      </aside>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <section class="section">
        <div class="section-body">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h4 id="tableTitle">Data Alat Musik</h4>
            </div>
            <div class="card-body">
              <!-- Tabs -->
              <ul class="nav nav-tabs" id="musicTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="tab-alat" data-toggle="tab" href="#alat" role="tab">Data Alat Musik</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="tab-personil" data-toggle="tab" href="#personil" role="tab">Data Personil</a>
                </li>
              </ul>

              <div class="tab-content mt-3">
                <!-- Tab Alat Musik -->
                <div class="tab-pane fade show active" id="alat" role="tabpanel">
                  <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalTambahAlat"><i class="fas fa-plus"></i> Tambah Alat Musik</button>
                  <div class="table-responsive">
                    <table class="table table-striped" id="table-alat">
                      <thead>
                        <tr>
                          <th class="text-center">No</th>
                          <th class="text-center">Nama Alat</th>
                          <th class="text-center">Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($alatMusik as $index => $alat)
                        <tr>
                          <td class="text-center">{{ $index + 1 }}</td>
                          <td class="text-center">{{ $alat->nama_alat }}</td>
                          <td class="text-center">
                            <button class="btn btn-warning btn-sm btn-edit-alat" 
                                data-id="{{ $alat->id_alat }}" 
                                data-nama="{{ $alat->nama_alat }}"><i class="fas fa-edit"></i> Edit</button>
                            <form action="{{ route('alat.destroy', $alat->id_alat) }}" method="POST" class="d-inline">
                              @csrf
                              @method('DELETE')
                              <button type="button" class="btn btn-danger btn-sm btn-hapus-alat"><i class="fas fa-trash"></i> Hapus</button>
                            </form>
                          </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center">Data tabel masih kosong</td></tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- Tab Personil -->
                <div class="tab-pane fade" id="personil" role="tabpanel">
                  <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalTambahPersonil"><i class="fas fa-plus"></i> Tambah Personil</button>
                  <div class="table-responsive">
                    <table class="table table-striped" id="table-personil">
                      <thead>
                        <tr>
                          <th class="text-center">No</th>
                          <th class="text-center">Nama Personil</th>
                          <th class="text-center">Gender</th>
                          <th class="text-center">Foto</th>
                          <th class="text-center">Alat Musik</th>
                          <th class="text-center">Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($pemainMusik as $index => $pemain)
                        <tr>
                          <td class="text-center">{{ $index + 1 }}</td>
                          <td class="text-center">{{ $pemain->nama_pemain }}</td>
                          <td class="text-center">{{ $pemain->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                          <td class="text-center">
                            @if($pemain->foto)
                              <img src="{{ asset('storage/'.$pemain->foto) }}" alt="Foto" class="personil-photo">
                            @else
                              <img src="/assets/img/usernopp.png" alt="Foto" class="personil-photo">
                            @endif
                          </td>
                          <td class="text-center">
                            @foreach($pemain->alat as $a)
                              {{ $a->nama_alat }}{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                          </td>
                          <td class="text-center">
                            <button class="btn btn-warning btn-sm btn-edit-personil" 
                                data-id="{{ $pemain->id_pemain }}" 
                                data-nama="{{ $pemain->nama_pemain }}"
                                data-gender="{{ $pemain->gender }}"
                                data-alat="{{ $pemain->alat->pluck('id_alat') }}"
                                data-foto="{{ $pemain->foto }}"><i class="fas fa-edit"></i> Edit</button>
                            <form action="{{ route('pemain.destroy', $pemain->id_pemain) }}" method="POST" class="d-inline">
                              @csrf
                              @method('DELETE')
                              <button type="button" class="btn btn-danger btn-sm btn-hapus-personil mt-3"><i class="fas fa-trash"></i> Hapus</button>
                            </form>
                          </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center">Data tabel masih kosong</td></tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- Modals Alat Musik -->
    <div class="modal fade" id="modalTambahAlat" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <form action="{{ route('alat.store') }}" method="POST">@csrf
            <div class="modal-header"><h5>Tambah Alat Musik</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
            <div class="modal-body">
              <div class="form-group">
                <label>Nama Alat</label>
                <input type="text" name="nama_alat" class="form-control" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalEditAlat" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <form id="formEditAlat" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-header"><h5>Edit Alat Musik</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
            <div class="modal-body">
              <div class="form-group">
                <label>Nama Alat</label>
                <input type="text" name="nama_alat" id="editNamaAlat" class="form-control" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modals Personil -->
    <div class="modal fade" id="modalTambahPersonil" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <form action="{{ route('pemain.store') }}" method="POST" enctype="multipart/form-data">@csrf
                <div class="modal-header">
                <h5>Tambah Personil</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                <div class="row">
                    <!-- Form Kiri -->
                    <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama Personil</label>
                        <input type="text" name="nama_pemain" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Gender</label><br>
                        <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="genderL" value="L" required>
                        <label class="form-check-label" for="genderL">Laki-laki</label>
                        </div>
                        <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="genderP" value="P">
                        <label class="form-check-label" for="genderP">Perempuan</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Foto Personil</label>
                        <input type="file" name="foto" id="fotoPersonil" class="form-control" accept="image/*">
                    </div>
                    </div>

                    <!-- Preview Kanan -->
                    <div class="col-md-6 d-flex align-items-center justify-content-center">
                    <img id="previewFoto" src="/assets/img/usernopp.png" alt="Preview Foto" class="img-thumbnail" style="width:200px; height:200px; object-fit:cover;">
                    </div>
                </div>

                <hr>

                <!-- Posisi Alat Musik -->
                <div class="form-group">
                    <label>Tambah Posisi Yang Dimainkan <small>(Setiap personil dapat memainkan lebih dari satu alat musik)</small></label>
                    <div id="posisiContainer">
                    <div class="input-group mb-2 posisi-item">
                        <select name="alat[]" class="form-control" required>
                        <option value="">-- Pilih Alat Musik --</option>
                        @foreach($alatMusik as $alat)
                            <option value="{{ $alat->id_alat }}">{{ $alat->nama_alat }}</option>
                        @endforeach
                        </select>
                        <div class="input-group-append">
                        <button class="btn btn-danger btn-hapus-posisi" type="button">&times;</button>
                        </div>
                    </div>
                    </div>
                    <button type="button" class="btn btn-warning" id="btnTambahPosisi"><i class="fas fa-plus"></i> Tambah Posisi</button>
                </div>

                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Personil -->
    <div class="modal fade" id="modalEditPersonil" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <form id="formEditPersonil" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-header">
            <h5>Edit Personil</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
            <div class="row">
                <!-- Form Kiri -->
                <div class="col-md-6">
                <div class="form-group">
                    <label>Nama Personil</label>
                    <input type="text" name="nama_pemain" id="editNamaPemain" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Gender</label><br>
                    <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="editGenderL" value="L" required>
                    <label class="form-check-label" for="editGenderL">Laki-laki</label>
                    </div>
                    <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="editGenderP" value="P">
                    <label class="form-check-label" for="editGenderP">Perempuan</label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Foto Personil</label>
                    <input type="file" name="foto" id="editFotoPersonil" class="form-control" accept="image/*">
                </div>
                </div>

                <!-- Preview Kanan -->
                <div class="col-md-6 d-flex align-items-center justify-content-center">
                <img id="previewEditFoto" src="/assets/img/usernopp.png" alt="Preview Foto" class="img-thumbnail" style="width:200px; height:200px; object-fit:cover;">
                </div>
            </div>

            <hr>

            <!-- Posisi Alat Musik -->
            <div class="form-group">
                <label>Posisi Alat Musik</label>
                <div id="editPosisiContainer"></div>
                <button type="button" class="btn btn-warning" id="btnEditTambahPosisi"><i class="fas fa-plus"></i> Tambah Posisi</button>
            </div>
            </div>

            <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
        </div>
    </div>
    </div>

    <!-- Setting Sidebar -->
      <div class="settingSidebar">
          <a href="javascript:void(0)" class="settingPanelToggle"> <i class="fa fa-spin fa-cog"></i>
          </a>
          <div class="settingSidebar-body ps-container ps-theme-default">
            <div class=" fade show active">
              <div class="setting-panel-header">Setting Panel
              </div>
              <div class="p-15 border-bottom">
                <h6 class="font-medium m-b-10">Select Layout</h6>
                <div class="selectgroup layout-color w-50">
                  <label class="selectgroup-item">
                    <input type="radio" name="value" value="1" class="selectgroup-input-radio select-layout" checked>
                    <span class="selectgroup-button">Light</span>
                  </label>
                  <label class="selectgroup-item">
                    <input type="radio" name="value" value="2" class="selectgroup-input-radio select-layout">
                    <span class="selectgroup-button">Dark</span>
                  </label>
                </div>
              </div>
              <div class="p-15 border-bottom">
                <h6 class="font-medium m-b-10">Sidebar Color</h6>
                <div class="selectgroup selectgroup-pills sidebar-color">
                  <label class="selectgroup-item">
                    <input type="radio" name="icon-input" value="1" class="selectgroup-input select-sidebar">
                    <span class="selectgroup-button selectgroup-button-icon" data-toggle="tooltip"
                      data-original-title="Light Sidebar"><i class="fas fa-sun"></i></span>
                  </label>
                  <label class="selectgroup-item">
                    <input type="radio" name="icon-input" value="2" class="selectgroup-input select-sidebar" checked>
                    <span class="selectgroup-button selectgroup-button-icon" data-toggle="tooltip"
                      data-original-title="Dark Sidebar"><i class="fas fa-moon"></i></span>
                  </label>
                </div>
              </div>
              <div class="p-15 border-bottom">
                <h6 class="font-medium m-b-10">Color Theme</h6>
                <div class="theme-setting-options">
                  <ul class="choose-theme list-unstyled mb-0">
                    <li title="white" class="active">
                      <div class="white"></div>
                    </li>
                    <li title="cyan">
                      <div class="cyan"></div>
                    </li>
                    <li title="black">
                      <div class="black"></div>
                    </li>
                    <li title="purple">
                      <div class="purple"></div>
                    </li>
                    <li title="orange">
                      <div class="orange"></div>
                    </li>
                    <li title="green">
                      <div class="green"></div>
                    </li>
                    <li title="red">
                      <div class="red"></div>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="p-15 border-bottom">
                <div class="theme-setting-options">
                  <label class="m-b-0">
                    <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input"
                      id="mini_sidebar_setting">
                    <span class="custom-switch-indicator"></span>
                    <span class="control-label p-l-10">Mini Sidebar</span>
                  </label>
                </div>
              </div>
              <div class="p-15 border-bottom">
                <div class="theme-setting-options">
                  <label class="m-b-0">
                    <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input"
                      id="sticky_header_setting">
                    <span class="custom-switch-indicator"></span>
                    <span class="control-label p-l-10">Sticky Header</span>
                  </label>
                </div>
              </div>
              <div class="mt-4 mb-4 p-3 align-center rt-sidebar-last-ele">
                <a href="#" class="btn btn-icon icon-left btn-primary btn-restore-theme">
                  <i class="fas fa-undo"></i> Restore Default
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
      
    <!-- Footer -->
    <footer class="main-footer">
      <div class="footer text-center">
        © {{ date('Y') }} Aplikasi Penjadwalan Layanan Panggung
      </div>
    </footer>
  </div>
</div>

<!-- JS Scripts -->
<script src="/assets/js/app.min.js"></script>
<script src="/assets/bundles/datatables/datatables.min.js"></script>
<script src="/assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
<script src="/assets/bundles/jquery-ui/jquery-ui.min.js"></script>
<script src="/assets/js/scripts.js"></script>
<script src="/assets/js/custom.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Konversi alat musik dari PHP ke JS
    const alatMusikList = @json($alatMusik);

    // Inisialisasi DataTables jika ada data
    const tables = [
        { id: '#table-alat', lastCol: 2 },
        { id: '#table-personil', lastCol: 5 }
    ];

    tables.forEach(function(tbl) {
        const hasData = $(tbl.id + ' tbody tr').filter(function() {
            return $(this).find('td').length > 1 && !$(this).text().includes('Data tabel masih kosong');
        }).length > 0;

        if (hasData) {
            $(tbl.id).DataTable({
                "columnDefs": [{ "orderable": false, "targets": tbl.lastCol }]
            });
        }
    });

    // Logout
    $(document).on('click', '#btnLogout', function(e){
        e.preventDefault();
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda akan keluar dari sistem.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Iya',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if(result.isConfirmed) $('#logout-form').submit();
        });
    });

    // Hapus Alat Musik
    $(document).on('click', '.btn-hapus-alat', function(e){
        e.preventDefault();
        const form = $(this).closest('form');
        Swal.fire({
            title: 'Hapus Alat Musik?',
            text: "Data ini akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result)=>{ if(result.isConfirmed) form.submit(); });
    });

    // Hapus Personil
    $(document).on('click', '.btn-hapus-personil', function(e){
        e.preventDefault();
        const form = $(this).closest('form');
        Swal.fire({
            title: 'Hapus Personil?',
            text: "Data ini akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result)=>{ if(result.isConfirmed) form.submit(); });
    });

    // Edit Alat Musik
    $(document).on('click', '.btn-edit-alat', function(){
        const id = $(this).data('id');
        const nama = $(this).data('nama');
        $('#editNamaAlat').val(nama);
        $('#formEditAlat').attr('action', '/alat/'+id);
        $('#modalEditAlat').modal('show');
    });

    // Edit Personil
    $(document).on('click', '.btn-edit-personil', function(){
        const id = $(this).data('id');
        const nama = $(this).data('nama');
        const gender = $(this).data('gender');
        const alat = $(this).data('alat'); // array
        const foto = $(this).data('foto');

        $('#editNamaPemain').val(nama);
        $('#editGenderL').prop('checked', gender === 'L');
        $('#editGenderP').prop('checked', gender === 'P');

        // Preview foto
        $('#previewEditFoto').attr('src', foto ? '/storage/' + foto : '/assets/img/usernopp.png');

        // Isi posisi alat musik
        const container = $('#editPosisiContainer');
        container.empty();
        if(alat && alat.length > 0){
            alat.forEach(function(a){
                const selectOptions = alatMusikList.map(am => `<option value="${am.id_alat}" ${a == am.id_alat ? 'selected' : ''}>${am.nama_alat}</option>`).join('');
                const newPosisi = `<div class="input-group mb-2 posisi-item">
                    <select name="alat[]" class="form-control" required>
                        <option value="">-- Pilih Alat Musik --</option>
                        ${selectOptions}
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-danger btn-hapus-posisi" type="button">&times;</button>
                    </div>
                </div>`;
                container.append(newPosisi);
            });
        }

        $('#formEditPersonil').attr('action', '/pemain/'+id);
        $('#modalEditPersonil').modal('show');
    });

    // Preview foto saat upload
    $('#fotoPersonil, #editFotoPersonil').change(function(e){
        const [file] = this.files;
        if(file){
            const preview = $(this).attr('id') === 'fotoPersonil' ? '#previewFoto' : '#previewEditFoto';
            $(preview).attr('src', URL.createObjectURL(file));
        }
    });

    // Tambah Posisi
    $('#btnTambahPosisi, #btnEditTambahPosisi').click(function(){
        const selectOptions = alatMusikList.map(am => `<option value="${am.id_alat}">${am.nama_alat}</option>`).join('');
        const newPosisi = `<div class="input-group mb-2 posisi-item">
            <select name="alat[]" class="form-control" required>
                <option value="">-- Pilih Alat Musik --</option>
                ${selectOptions}
            </select>
            <div class="input-group-append">
                <button class="btn btn-danger btn-hapus-posisi" type="button">&times;</button>
            </div>
        </div>`;
        const container = $(this).attr('id') === 'btnTambahPosisi' ? '#posisiContainer' : '#editPosisiContainer';
        $(container).append(newPosisi);
    });

    // Hapus Posisi
    $(document).on('click', '.btn-hapus-posisi', function(){
        $(this).closest('.posisi-item').remove();
    });

    // Notifikasi sukses
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '{{ session("success") }}',
            timer: 2000,
            showConfirmButton: false
        });
    @endif

    // Ubah judul tabel saat tab berganti
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        let tabText = $(e.target).text();
        $('#tableTitle').text(tabText);
    });
});
</script>

</body>
</html>
