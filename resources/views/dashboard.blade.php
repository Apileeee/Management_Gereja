<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Dashboard - Penjadwalan Pelayanan Panggung Gereja</title>
  <!-- General CSS Files -->
  <link rel="stylesheet" href="/assets/css/app.min.css">
  <link rel="stylesheet" href="/assets/bundles/datatables/datatables.min.css">
  <link rel="stylesheet" href="/assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css">
  <!-- Template CSS -->
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/components.css">
  <!-- Custom style CSS -->
  <link rel="stylesheet" href="/assets/css/custom.css">
  <link rel='shortcut icon' type='image/x-icon' href='/assets/img/icngereja.ico' />
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
          <h4 class="font-weight-bold mt-2">Data Periode</h4>
        </div>
        <ul class="navbar-nav navbar-right ">
          <li>
            <a href="#" id="btnLogout" class="nav-link nav-link-lg mr-5" title="Logout">
              <i data-feather="log-out"></i>
            </a>
          </li>
        </ul>
      </nav>

      <!-- Form hidden untuk logout -->
      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
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
            <li class="active"><a href="{{ route('dashboard') }}" class="nav-link"><i data-feather="calendar"></i><span>Data Periode</span></a></li>
            <li><a href="{{ route('musikpersonil') }}" class="nav-link"><i data-feather="music"></i><span>Alat Musik & Personil</span></a></li>
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
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Data Periode</h4>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">
                      <i class="fas fa-plus"></i> Tambah Periode
                    </button>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-striped" id="table-1">
                        <thead>
                          <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Periode</th>
                            <th class="text-center">Deskripsi</th>
                            <th class="text-center">Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                          @forelse($periodes as $index => $periode)
                          <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ $periode->nama_periode }}</td>
                            <td class="text-center">{{ $periode->deskripsi ?? '-' }}</td>
                            <td class="text-center">
                              <button class="btn btn-warning btn-sm btn-edit" 
                                data-id="{{ $periode->id_periode }}" 
                                data-nama="{{ $periode->nama_periode }}" 
                                data-deskripsi="{{ $periode->deskripsi ?? '' }}">
                                <i class="fas fa-edit"></i> Edit
                              </button>
                              <form action="{{ route('periode.destroy', $periode->id_periode) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm btn-hapus">
                                  <i class="fas fa-trash"></i> Hapus
                                </button>
                              </form>
                            </td>
                          </tr>
                          @empty
                          <tr>
                            <td colspan="4" class="text-center">Data tabel masih kosong</td>
                          </tr>
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

      <!-- Modal Tambah -->
      <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <form action="{{ route('periode.store') }}" method="POST">
              @csrf
              <div class="modal-header">
                <h5 class="modal-title">Tambah Periode</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <label>Nama Periode</label>
                  <input type="text" name="nama_periode" class="form-control" required>
                </div>
                <div class="form-group">
                  <label>Deskripsi</label>
                  <textarea name="deskripsi" class="form-control"></textarea>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Periode</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Modal Edit Tunggal -->
      <div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <form id="formEdit" method="POST">
              @csrf
              @method('PUT')
              <div class="modal-header">
                <h5 class="modal-title">Edit Periode</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <label>Nama Periode</label>
                  <input type="text" name="nama_periode" id="editNama" class="form-control" required>
                </div>
                <div class="form-group">
                  <label>Deskripsi</label>
                  <textarea name="deskripsi" id="editDeskripsi" class="form-control"></textarea>
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
      const hasData = $('#table-1 tbody tr').filter(function() {
        return $(this).find('td').length > 1;
      }).length > 0;

      if (hasData) {
        $("#table-1").DataTable({ "columnDefs": [{ "orderable": false, "targets": 3 }] });
      }

      // Logout
      $(document).on('click', '#btnLogout', function(e) {
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
          if (result.isConfirmed) {
            $('#logout-form').submit();
          }
        });
      });

      // Hapus
      $(document).on('click', '.btn-hapus', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        Swal.fire({
          title: 'Hapus Data?',
          text: "Data periode ini akan dihapus!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Ya, hapus!',
          cancelButtonText: 'Batal'
        }).then((result) => {
          if (result.isConfirmed) {
            form.submit();
          }
        });
      });

      // Edit modal
      $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');
        const deskripsi = $(this).data('deskripsi');
        $('#editNama').val(nama);
        $('#editDeskripsi').val(deskripsi);
        $('#formEdit').attr('action', '/periode/' + id);
        $('#modalEdit').modal('show');
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
    });
  </script>
</body>
</html>
