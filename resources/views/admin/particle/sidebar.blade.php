<!-- Brand Logo -->
<a href="{{ route('dashboard') }}" class="brand-link">
  <div class="nb-logo">
    <svg viewBox="0 0 24 24" width="16" height="16" fill="#fff"><path d="M12 2L2 7l10 5 10-5-10-5zm0 5.236L5.618 7 12 3.764 18.382 7 12 7.236zM2 17l10 5 10-5-10-5-10 5z"/></svg>
  </div>
  <span class="brand-text">Live By Life</span>
</a>

<div class="sidebar">
  <nav class="mt-1">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
      <li class="nav-header">Main</li>

      <li class="nav-item">
        <a href="{{ route('dashboard') }}" class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}">
          <i class="nav-icon fas fa-th-large"></i>
          <p>Dashboard</p>
        </a>
      </li>

      @if(auth()->user() && auth()->user()->hasRole('super-admin'))
      <li class="nav-header">System</li>

      <li class="nav-item">
        <a href="{{ route('customers.index') }}" class="nav-link {{ Request::routeIs('customers.*') ? 'active' : '' }}">
          <i class="nav-icon fas fa-users"></i>
          <p>Customers</p>
        </a>
      </li>

      <li class="nav-item {{ Request::routeIs('roles.*', 'users.*', 'lead-sources.*', 'lead-stages.*', 'items.*', 'category.*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ Request::routeIs('roles.*', 'users.*', 'lead-sources.*', 'lead-stages.*', 'items.*', 'category.*') ? 'active' : '' }}">
          <i class="nav-icon fas fa-sliders-h"></i>
          <p>
            Settings
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>

        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="{{ route('roles.index') }}" class="nav-link {{ Request::routeIs('roles.*', 'users.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-shield"></i>
              <p>User & Role</p>
            </a>
          </li>
         
        </ul>
      </li>

      <li class="nav-item">
        <a href="{{ route('activity-logs.index') }}" class="nav-link {{ Request::routeIs('activity-logs.*') ? 'active' : '' }}">
          <i class="nav-icon fas fa-history"></i>
          <p>Activity Logs</p>
        </a>
      </li>
      @endif
    </ul>
  </nav>
</div>
