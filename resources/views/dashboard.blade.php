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
          <h4 class="font-weight-bold mt-2" style="color:black">Data Periode</h4>
        </div>
        <ul class="navbar-nav navbar-right">
          <li>
            <a href="#" id="btnLogout" class="nav-link nav-link-lg text-danger mr-5" title="Logout">
              <i class="fas fa-sign-out-alt" style="font-size:20px;"></i>
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
            <img alt="image" src="/assets/img/user.png" class="rounded-circle" style="width:45px;height:45px;">
            <div class="ml-4 d-flex flex-column justify-content-center">
              <span class="font-weight-bold" style="line-height:2;">Admin</span>
              <small style="line-height:2;">Sistem Penjadwalan</small>
            </div>
          </div>
          <ul class="sidebar-menu mt-3">
            <li class="menu-header">Main Menu</li>
            <li class="active"><a href="#" class="nav-link"><i data-feather="calendar"></i><span>Data Periode</span></a></li>
            <li><a href="#" class="nav-link"><i data-feather="music"></i><span>Alat Musik & Personil</span></a></li>
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
            <!-- Card Data Periode -->
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Data Periode</h4>
                    <button class="btn btn-primary">
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
                            <th class="text-center">Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td class="text-center">1</td>
                            <td class="text-center">Januari 2025</td>
                            <td class="text-center">
                              <a href="#" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                              <a href="#" class="btn btn-danger btn-sm btn-hapus"><i class="fas fa-trash"></i> Hapus</a>
                            </td>
                          </tr>
                          <tr>
                            <td class="text-center">2</td>
                            <td class="text-center">Februari 2025</td>
                            <td class="text-center">
                              <a href="#" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                              <a href="#" class="btn btn-danger btn-sm btn-hapus"><i class="fas fa-trash"></i> Hapus</a>
                            </td>
                          </tr>
                          <tr>
                            <td class="text-center">3</td>
                            <td class="text-center">Maret 2025</td>
                            <td class="text-center">
                              <a href="#" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                              <a href="#" class="btn btn-danger btn-sm btn-hapus"><i class="fas fa-trash"></i> Hapus</a>
                            </td>
                          </tr>
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

      <!-- Setting Sidebar (Tetap sama) -->
      <div class="settingSidebar">...</div>

    </div>
  </div>

  <!-- General JS Scripts -->
  <script src="/assets/js/app.min.js"></script>
  <!-- JS Libraries -->
  <script src="/assets/bundles/datatables/datatables.min.js"></script>
  <script src="/assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
  <script src="/assets/bundles/jquery-ui/jquery-ui.min.js"></script>
  <!-- Template JS File -->
  <script src="/assets/js/scripts.js"></script>
  <!-- Custom JS File -->
  <script src="/assets/js/custom.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    // Inisialisasi DataTable
    $("#table-1").DataTable({
      "columnDefs": [{ "orderable": false, "targets": 2 }] // kolom Aksi tidak bisa di-sort
    });

    // Logout
    document.getElementById("btnLogout").addEventListener("click", function (e) {
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
          document.getElementById('logout-form').submit();
        }
      })
    });

    // Konfirmasi hapus data periode
    document.querySelectorAll(".btn-hapus").forEach(function (button) {
      button.addEventListener("click", function (e) {
        e.preventDefault();
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
            Swal.fire('Terhapus!', 'Data periode berhasil dihapus.', 'success')
            // TODO: tambahkan aksi backend untuk hapus data
          }
        })
      });
    });
  </script>
</body>
</html>
