<!-- Styles are centralized in public/admin/dist/css/custom.css -->

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','Home') - My Store</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="" crossorigin="anonymous" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="{{ asset('admin/dist/css/custom.css') }}?v={{ filemtime(public_path('admin/dist/css/custom.css')) }}" rel="stylesheet">
  @stack('styles')
</head>
<body class="frontend">
  <nav class="navbar navbar-expand-lg navbar-light fixed-top border-bottom">
    <div class="container">
      <a class="navbar-brand font-weight-bold" href="{{ url('/') }}">My Store</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navMain">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navMain">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('products') }}">Products</a></li>
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

  <!-- Site-level interactive footer (moved from home.blade.php) -->
  <footer class="site-footer text-white pt-5 pb-4">
    <div class="container">
      <div class="row">
        <div class="col-md-4 mb-4">
          <a class="d-inline-block mb-2" href="{{ url('/') }}" aria-label="My Store logo">
            <svg viewBox="0 0 24 24" width="72" height="72" fill="#fff"><path d="M12 2L2 7l10 5 10-5-10-5zm0 5.236L5.618 7 12 3.764 18.382 7 12 7.236zM2 17l10 5 10-5-10-5-10 5z"/></svg>
          </a>
          <p class="mb-0 text-muted">My Store — quality products, delivered with care.</p>
        </div>

        <div class="col-md-3 mb-4">
          <h5 class="footer-title">Quick Links</h5>
          <ul class="list-unstyled">
            <li><a class="text-white-50" href="{{ url('/') }}">Home</a></li>
            <li><a class="text-white-50" href="{{ route('products') }}">Products</a></li>
            <li><a class="text-white-50" href="{{ route('about') }}">About</a></li>
            <li><a class="text-white-50" href="{{ route('contact') }}">Contact</a></li>
          </ul>
        </div>

        <div class="col-md-3 mb-4">
          <h5 class="footer-title">Contact</h5>
          <p class="mb-1 text-white-50">support@mystore.example</p>
          <p class="mb-0 text-white-50">+91 89769 76567</p>
        </div>

        <div class="col-md-2 mb-4">
          <h5 class="footer-title">Follow</h5>
          <ul class="list-unstyled d-flex">
            <li class="mr-2"><a class="text-white-50" href="#" aria-label="Follow on Facebook"><i class="fab fa-facebook fa-lg"></i></a></li>
            <li class="mr-2"><a class="text-white-50" href="#" aria-label="Follow on Instagram"><i class="fab fa-instagram fa-lg"></i></a></li>
            <li><a class="text-white-50" href="#" aria-label="Follow on Twitter"><i class="fab fa-twitter fa-lg"></i></a></li>
          </ul>
        </div>
      </div>

      <div class="row mt-3">
        <div class="col-12 text-center text-white-50 small">
          &copy; {{ date('Y') }} My Store. All rights reserved.
        </div>
      </div>
    </div>
  </footer>

  <button id="backToTop" title="Back to top">↑</button>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      // Accordion behavior
      document.querySelectorAll('#howToAccordion .item').forEach(function(it){
        it.addEventListener('click', function(){
          var content = this.querySelector('.content');
          var shown = content.style.display === 'block';
          document.querySelectorAll('#howToAccordion .content').forEach(function(c){ c.style.display = 'none' });
          content.style.display = shown ? 'none' : 'block';
        });
      });

      // Back to top
      var btn = document.getElementById('backToTop');
      if(btn){
        window.addEventListener('scroll', function(){ btn.style.display = (window.scrollY > 200) ? 'block' : 'none'; });
        btn.addEventListener('click', function(){ window.scrollTo({top:0,behavior:'smooth'}); });
      }
    });
  </script>
  @stack('scripts')
</body>
</html>
