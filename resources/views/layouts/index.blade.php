<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>@yield('title', 'Dashboard')</title>
  <!-- General CSS Files -->
  <link rel="stylesheet" href="/assets/css/app.min.css">
  <link rel="stylesheet" href="/assets/bundles/datatables/datatables.min.css">
  <link rel="stylesheet" href="/assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css">
  <!-- Template CSS -->
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/components.css">
  <link rel="stylesheet" href="/assets/css/custom.css">
  <link rel='shortcut icon' type='image/x-icon' href='/assets/img/icngereja.ico' />
  @stack('css')
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
          <h4 class="font-weight-bold mt-2">@yield('page-title', 'Dashboard')</h4>
        </div>
        <ul class="navbar-nav navbar-right ">
          <li>
            <a href="#" id="btnLogout" class="nav-link nav-link-lg mr-5" title="Logout">
              <i data-feather="log-out"></i>
            </a>
          </li>
        </ul>
      </nav>

      <!-- Logout Form -->
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
                        <li class="@yield('menu-periode', '')">
                            <a href="{{ route('dashboard') }}" class="nav-link">
                                <i data-feather="calendar"></i><span>Data Periode</span>
                            </a>
                        </li>
                        <li class="@yield('menu-musik-personil', '')">
                            <a href="{{ route('musikpersonil') }}" class="nav-link">
                                <i data-feather="music"></i><span>Alat Musik & Personil</span>
                            </a>
                        </li>
                        <li class="@yield('menu-ibadah', '')">
                            <a href="{{ route('ibadah.index') }}" class="nav-link">
                                <i data-feather="clock"></i><span>Data Ibadah & Waktu</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('generate.jadwal') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('generate.jadwal') }}">
                                <i data-feather="calendar"></i> Generate Jadwal Ibadah
                            </a>
                        </li>
                        <li class="@yield('profile', '')">
                            <a href="{{ route('profile.edit') }}" class="nav-link">
                                <i data-feather="settings"></i><span>Manajemen Profile</span>
                            </a>
                        </li>
          </ul>
        </aside>
      </div>

      <!-- Main Content -->
      <div class="main-content">
        @yield('content')
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

  @stack('js')
  @yield('scripts')
</body>
</html>
