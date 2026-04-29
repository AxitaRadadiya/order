<style>
.btn-create {
    background: linear-gradient(135deg, #5E60CE, #7F53AC) !important;
    color: #fff !important;
    border: none;
    border-radius: 12px;
    padding: 7px 14px;
    margin-bottom: 7px;
}
.container{
    background: #F4F7FE !important;
}
</style>

<!doctype html>
<html lang="en">
<head style="background: #F4F7FE !important;">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','Home') - My Store</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="" crossorigin="anonymous" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>body{padding-top:70px} .hero{padding:60px 0;background-color:#F4F7FE !important}</style>
</head>
<body style="background: #F4F7FE !important;">
  <nav class="navbar navbar-expand-lg navbar-light fixed-top border-bottom" style="background: #F4F7FE !important;" >
    <div class="container">
      <a class="navbar-brand font-weight-bold" href="{{ url('/') }}">My Store</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navMain">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navMain">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('products') }}">Products</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('categories') }}">Categories</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ url('/about') }}">About</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ url('/contact') }}">Contact</a></li>
        </ul>

        <ul class="navbar-nav">
          @guest
            <li class="nav-item"><a class="btn-create mr-2" href="{{ route('login') }}">Login</a></li>
            <li class="nav-item"><a class="btn-create" href="{{ route('register') }}">Sign Up</a></li>
          @else
            <li class="nav-item dropdown">
              <a id="userDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">{{ auth()->user()->name }}</a>
              <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a>
                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Logout</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
              </div>
            </li>
          @endguest
        </ul>
      </div>
    </div>
  </nav>

  <main class="container">
    @yield('content')
  </main>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
