<!-- Left: Toggle -->
<ul class="navbar-nav">
  <li class="nav-item">
    <a class="nav-link" style="color:#62748E;" data-widget="pushmenu" href="#" role="button">
      <i class="fas fa-bars"></i>
    </a>
  </li>
</ul>

<!-- Right -->
<ul class="navbar-nav ml-auto align-items-center">

  <!-- User dropdown -->
  <li class="nav-item dropdown ml-1">
    <a class="navbar-user-pill" data-toggle="dropdown" href="#">
      <span class="user-avatar">
        <img src="{{ auth()->user()->profile_image_url }}"
             alt="{{ auth()->user()->name }}">
      </span>
      <span class="d-none d-md-inline"
            style="font-size:.83rem;font-weight:600;color:#62748E;">
        {{ ucfirst(Auth()->user()->name) }}
      </span>
      <i class="fas fa-caret-down ml-1" style="font-size:10px;opacity:.7;color:#fff;"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-right" style="min-width:185px;">
      <span class="dropdown-header">
        <i class="fas fa-user-circle mr-1" style="color:#7F53AC;"></i>
        {{ ucfirst(Auth()->user()->name) }}
      </span>

      <div class="dropdown-divider"></div>
      <a href="{{ route('admin.profile.edit') }}" class="dropdown-item">
        <i class="fas fa-user-circle mr-2" style="color:#7F53AC;"></i> My Profile
      </a>
       <a href="{{ route('admin.profile.password') }}" class="dropdown-item">
        <i class="fas fa-user-circle mr-2" style="color:#7F53AC;"></i> Change Password
      </a>
      
      <div class="dropdown-divider"></div>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <a href="{{ route('logout') }}" class="dropdown-item"
           style="color:#e05252;"
           onclick="event.preventDefault();this.closest('form').submit();">
          <i class="fas fa-sign-out-alt mr-2"></i> Logout
        </a>
      </form>
    </div>
  </li>

  <!-- Fullscreen -->
  <li class="nav-item ml-1">
    <a class="nav-link" data-widget="fullscreen" href="#" role="button" title="Fullscreen"
       style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:7px;color:#62748E;">
      <i class="fas fa-expand-arrows-alt" style="font-size:.82rem;"></i>
    </a>
  </li>

  <!-- Quick Logout -->
  <li class="nav-item ml-1">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <a href="{{ route('logout') }}" class="nav-link"
         onclick="event.preventDefault();this.closest('form').submit();"
         title="Logout"
         style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;
                border-radius:7px;color:#62748E;
                border:1px solid rgba(255,255,255,.25);">
        <i class="fas fa-power-off" style="font-size:.82rem;"></i>
      </a>
    </form>
  </li>

</ul>
