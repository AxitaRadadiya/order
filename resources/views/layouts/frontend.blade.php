<!-- Styles are centralized in public/admin/dist/css/custom.css -->

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','Home') - Live By Style</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="" crossorigin="anonymous" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="{{ asset('admin/dist/css/custom.css') }}?v={{ filemtime(public_path('admin/dist/css/custom.css')) }}" rel="stylesheet">
  <link href="{{ asset('admin/dist/css/frontend.css') }}?v={{ filemtime(public_path('admin/dist/css/frontend.css')) }}" rel="stylesheet">
  @stack('styles')
</head>
<body class="frontend">
  <nav class="navbar navbar-expand-lg navbar-light fixed-top border-bottom">
    <div class="container">
      <a class="navbar-brand font-weight-bold" href="{{ url('/') }}">Live By Style</a>
      <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#navMain" aria-controls="navMain" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fas fa-bars" style="color: #7358b9; font-size: 1.4rem;"></i>
      </button>

      <div class="collapse navbar-collapse" id="navMain">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item mr-1"><a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Home</a></li>
          <li class="nav-item mr-1"><a class="nav-link {{ request()->is('products') ? 'active' : '' }}" href="{{ route('products') }}">Products</a></li>
          <li class="nav-item mr-1"><a class="nav-link {{ request()->is('about') ? 'active' : '' }}" href="{{ url('/about') }}">About</a></li>
          <li class="nav-item mr-1"><a class="nav-link {{ request()->is('network') ? 'active' : '' }}" href="{{ url('/network') }}">Network</a></li>
          <li class="nav-item mr-1"><a class="nav-link {{ request()->is('contact') ? 'active' : '' }}" href="{{ url('/contact') }}">Contact</a></li>
        </ul>

        <ul class="navbar-nav navbar-auth align-items-lg-center">
          @guest
            <li class="nav-item"><a class="btn-create mr-lg-2" href="{{ route('login') }}">Login</a></li>
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

  <main class="@yield('main_class', 'container')">
    @yield('content')
  </main>

  <!-- Site-level interactive footer (moved from home.blade.php) -->
  <footer class="site-footer text-white pt-5 pb-4">
    <div class="container">
      <div class="row">
        <div class="col-md-3 mb-4">
          <a class="d-inline-block mb-2" href="{{ url('/') }}" aria-label="Live By Style logo">
            <svg viewBox="0 0 24 24" width="72" height="72" fill="#fff"><path d="M12 2L2 7l10 5 10-5-10-5zm0 5.236L5.618 7 12 3.764 18.382 7 12 7.236zM2 17l10 5 10-5-10-5-10 5z"/></svg>
          </a>
          <p class="mb-3">Live By Style</p>
          <p class="mb-3 text-muted">Crafting Comfort. Delivering Style. Building Trust Since 2007.</p>
          <ul class="list-unstyled d-flex gap-3 mb-0">
              <li><a class="text-white-50 social-icon mr-2" href="#" aria-label="Facebook"><i class="fab fa-facebook fa-lg"></i></a></li>
              <li><a class="text-white-50 social-icon mr-2" href="#" aria-label="Instagram"><i class="fab fa-instagram fa-lg"></i></a></li>
              <li><a class="text-white-50 social-icon" href="#" aria-label="Twitter"><i class="fab fa-twitter fa-lg"></i></a></li>
          </ul>
        </div>

        <div class="col-md-2 mb-4">
          <h5 class="footer-title">Quick Links</h5>
          <ul class="list-unstyled">
            <li><a class="text-white-50" href="{{ url('/') }}">Home</a></li>
            <li><a class="text-white-50" href="{{ route('products') }}">Products</a></li>
            <li><a class="text-white-50" href="{{ route('about') }}">About</a></li>
            <li><a class="text-white-50" href="{{ route('network') }}">Network</a></li>
            <li><a class="text-white-50" href="{{ route('contact') }}">Contact</a></li>
          </ul>
        </div>

        <div class="col-md-4 mb-4">
          <h5 class="footer-title">Categories</h5>
          @php
              $footerCategories = \App\Models\Category::orderBy('name')->get();
              $chunks = $footerCategories->chunk(ceil($footerCategories->count() / 2));
          @endphp

          <div class="row">
            @foreach($chunks as $chunk)
              <div class="col-6">
                <ul class="list-unstyled">
                  @forelse($chunk as $cat)
                    <li>
                      <a class="text-white-50" href="{{ route('products') }}?category_id={{ $cat->id }}">
                        {{ $cat->name }}
                      </a>
                    </li>
                  @empty
                    <li class="text-white-50 small">No categories found</li>
                  @endforelse
                </ul>
              </div>
            @endforeach           
          </div>
          
        </div>

        <div class="col-md-3 mb-4">
          <h5 class="footer-title">Contact</h5>
          <p class="mb-1 text-white-50">📧 Livebystyle.amd@gmail.com</p>
          <p class="mb-2 text-white-50">📞 +91 81414 67888</p>
          <h6 class="text-white">Corporate Office</h6>
          <p class="text-white-50 mb-3">
              GF, Jaisinghbhai Vadi,<br>
              Opp. Gheekanta Metro Station,<br>
              Gheekanta Road,<br>
              Ahmedabad, Gujarat, India
          </p>

          <!-- <h6 class="text-white">Manufacturing Unit</h6>
          <p class="text-white-50 mb-0">
              First Floor, Santosh Nagar Society,<br>
              Opp. Navrang Tenament,<br>
              Near Ranna Park,<br>
              Partheshwar Mahadev Road,<br>
              Isanpur–Vatva Road,<br>
              Ahmedabad, Gujarat, India
          </p> -->
        </div>

        <!-- <div class="col-md-2 mb-4">
          <h5 class="footer-title">Follow</h5>
          <ul class="list-unstyled d-flex">
            <li class="mr-2"><a class="text-white-50" href="#" aria-label="Follow on Facebook"><i class="fab fa-facebook fa-lg"></i></a></li>
            <li class="mr-2"><a class="text-white-50" href="#" aria-label="Follow on Instagram"><i class="fab fa-instagram fa-lg"></i></a></li>
            <li><a class="text-white-50" href="#" aria-label="Follow on Twitter"><i class="fab fa-twitter fa-lg"></i></a></li>
          </ul>
        </div> -->
      </div>

      <div class="row mt-3">
        <div class="col-12 text-center text-white-50 small">
          &copy; {{ date('Y') }} Live By Style. All rights reserved.
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
