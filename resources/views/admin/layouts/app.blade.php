<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Live By Life</title>
  <link rel="icon" type="image/x-icon" href="#">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
  @include('admin.particle.css')
</head>
<body class="hold-transition sidebar-mini layout-fixed text-sm">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand">
    @include('admin.particle.navbar')
  </nav>

  <!-- Main Sidebar -->
  <aside class="main-sidebar sidebar-light-primary elevation-4">
    @include('admin.particle.sidebar')
  </aside>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    @yield('content')
  </div>

  <footer class="main-footer">
    @include('admin.particle.footer')
  </footer>

  <aside class="control-sidebar control-sidebar-light"></aside>
</div>

@include('admin.particle.script')
@yield('style')
@yield('pageScript')
</body>
</html>
