<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Frank Mombe | @yield('title')</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('/admin')}}/plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{asset('/admin')}}/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{asset('/admin')}}/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="{{asset('/admin')}}/plugins/jqvmap/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('/admin')}}/dist/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{asset('/admin')}}/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{asset('/admin')}}/plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="{{asset('/admin')}}/plugins/summernote/summernote-bs4.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{asset('/admin')}}/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="{{asset('/admin')}}/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="{{asset('/admin')}}/dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="{{route('dashboard')}}" class="nav-link">Home</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
      <li class="nav-item">
        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
          <i class="fas fa-search"></i>
        </a>
        <div class="navbar-search-block">
          <form class="form-inline">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                  <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </li>

      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge">15</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">15 Notifications</span>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-users mr-2"></i> 8 new barbers
            <span class="float-right text-muted text-sm">3 mins</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-calendar mr-2"></i> 12 new bookings
            <span class="float-right text-muted text-sm">12 hours</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-money-bill mr-2"></i> Payments pending
            <span class="float-right text-muted text-sm">2 days</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{route('dashboard')}}" class="brand-link">
      <img src="{{asset('/admin')}}/dist/img/AdminLTELogo.png" alt="Frank Mombe Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Frank Mombe</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{asset('/admin')}}/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">Admin User</a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search Menu" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Dashboard -->
          <li class="nav-item">
            <a href="{{route('dashboard')}}" class="nav-link @if(Route::current()->getName() =='dashboard') active @endif">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>

          <!-- Users Management -->
          <li class="nav-item">
            <a href="{{route('users.index')}}" class="nav-link @if(Route::current()->getName() =='users.index') active @endif">
              <i class="nav-icon fas fa-users"></i>
              <p>Users</p>
            </a>
          </li>

          <!-- Services Management -->
          <li class="nav-item">
            <a href="{{route('categories.index')}}" class="nav-link @if(Route::current()->getName() =='services.index') active @endif">
              <i class="nav-icon fas fa-concierge-bell"></i>
              <p>Services</p>
            </a>
          </li>

          <!-- Amenities Management 
          <li class="nav-item">
            <a href="{{route('amenities.index')}}" class="nav-link @if(Route::current()->getName() =='amenities.index') active @endif">
              <i class="nav-icon fas fa-th-list"></i>
              <p>Amenities</p>
            </a>
          </li>-->
			 <li class="nav-item">
            <a href="#" class="nav-link @if(Route::current()->getName() =='amenities.index') active @endif">
              <i class="nav-icon fas fa-th-list"></i>
              <p>Amenities</p>
            </a>
          </li>
			

          <!-- Locations Management -->
          <li class="nav-item">
            <a href="{{route('locations.index')}}" class="nav-link @if(Route::current()->getName() =='locations.index') active @endif">
              <i class="nav-icon fas fa-map-marker-alt"></i>
              <p>Locations</p>
            </a>
          </li>

          <!-- Charges Management 
          <li class="nav-item">
            <a href="{{route('charges.index')}}" class="nav-link @if(Route::current()->getName() =='charges.index') active @endif">
              <i class="nav-icon fas fa-money-bill-wave"></i>
              <p>Charges</p>
            </a>
          </li>-->
		  <li class="nav-item">
            <a href="#" class="nav-link @if(Route::current()->getName() =='charges.index') active @endif">
              <i class="nav-icon fas fa-money-bill-wave"></i>
              <p>Charges</p>
            </a>
          </li>

          <!-- Payments Overview -->
          <li class="nav-item">
            <a href="{{route('payments.index')}}" class="nav-link @if(Route::current()->getName() =='payments.index') active @endif">
              <i class="nav-icon fas fa-credit-card"></i>
              <p>Total Payments</p>
            </a>
          </li>

          <!-- Dynamic Splitting 
          <li class="nav-item">
            <a href="{{route('splitting.index')}}" class="nav-link @if(Route::current()->getName() =='splitting.index') active @endif">
              <i class="nav-icon fas fa-code-branch"></i>
              <p>Splitting (Dynamic)</p>
            </a>
          </li>-->
		<li class="nav-item">
            <a href="{{route('splitting.index')}}" class="nav-link @if(Route::current()->getName() =='splitting.index') active @endif">
              <i class="nav-icon fas fa-code-branch"></i>
              <p>Splitting (Dynamic)</p>
            </a>
          </li>
			 <!-- Logout -->
			<li class="nav-item">
				<a href="#" class="nav-link text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
					<i class="nav-icon fas fa-sign-out-alt"></i>
					<p>Logout</p>
				</a>
				<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
					@csrf
				</form>
			</li>

          <!-- Bookings Management 
          <li class="nav-item">
            <a href="{{route('bookings.index')}}" class="nav-link @if(Route::current()->getName() =='bookings.index') active @endif">
              <i class="nav-icon fas fa-calendar-check"></i>
              <p>Bookings</p>
            </a>
          </li>-->

          <!-- Reviews Management 
          <li class="nav-item">
            <a href="{{route('reviews.index')}}" class="nav-link @if(Route::current()->getName() =='reviews.index') active @endif">
              <i class="nav-icon fas fa-star"></i>
              <p>Reviews</p>
            </a>
          </li>-->

          <!-- Support Tickets
          <li class="nav-item">
            <a href="{{route('support.index')}}" class="nav-link @if(Route::current()->getName() =='support.index') active @endif">
              <i class="nav-icon fas fa-headset"></i>
              <p>Support Tickets</p>
            </a>
          </li> -->

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">@yield('page-title')</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
              <li class="breadcrumb-item active">@yield('page-title')</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    
    @yield('content')
    
  </div>
  <!-- /.content-wrapper -->

  <footer class="main-footer">
    <strong>Copyright &copy; 2024 <a href="#">Frank Mombe</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 1.0.0
    </div>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{asset('/admin')}}/plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{asset('/admin')}}/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{asset('/admin')}}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="{{asset('/admin')}}/plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="{{asset('/admin')}}/plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="{{asset('/admin')}}/plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="{{asset('/admin')}}/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="{{asset('/admin')}}/plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="{{asset('/admin')}}/plugins/moment/moment.min.js"></script>
<script src="{{asset('/admin')}}/plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{asset('/admin')}}/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="{{asset('/admin')}}/plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="{{asset('/admin')}}/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="{{asset('/admin')}}/dist/js/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('/admin')}}/dist/js/demo.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{asset('/admin')}}/dist/js/pages/dashboard.js"></script>
<!-- Select2 -->
<script src="{{asset('/admin')}}/plugins/select2/js/select2.full.min.js"></script>
@stack('scripts')
<script>
  $(function () {
    $('.select2').select2();

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    });
  });
</script>
</body>
</html>