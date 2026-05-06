<!-- Brand Logo -->
<a href="{{ route('dashboard') }}" class="brand-link d-flex align-items-center">
  <div class="nb-logo mr-2">
    <svg viewBox="0 0 24 24" width="20" height="20" fill="#fff"><path d="M12 2L2 7l10 5 10-5-10-5zm0 5.236L5.618 7 12 3.764 18.382 7 12 7.236zM2 17l10 5 10-5-10-5-10 5z"/></svg>
  </div>
  <span class="brand-text font-weight-bold">Live By Life</span>
</a>

<div class="sidebar">
  <nav class="mt-1">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
      <li class="nav-header">Main</li>

      <li class="nav-item">
        <a href="{{ route('dashboard') }}" class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}">
          <span class="nav-icon">🏡</span>
          <p>Dashboard</p>
        </a>
      </li>

      @php $user = auth()->user(); $allowed = session('allowed_modules'); @endphp

      @if($user && $user->hasRole('super-admin'))
      <li class="nav-header">System</li>

      <li class="nav-item">
        <a href="{{ route('customers.index') }}" class="nav-link {{ Request::routeIs('customers.*') ? 'active' : '' }}">
          <span class="nav-icon">🏢</span>
          <p>Customers</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('items.index') }}" class="nav-link {{ Request::routeIs('items.*') ? 'active' : '' }}">
          <span class="nav-icon">🗂️</span>
          <p>Items</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('orders.index') }}" class="nav-link {{ Request::routeIs('orders.*') ? 'active' : '' }}">
          <span class="nav-icon">📦</span>
          <p>Orders</p>
        </a>
      </li>

      <li class="nav-item {{ Request::routeIs('roles.*', 'users.*', 'master.*', 'item-master.*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ Request::routeIs('roles.*', 'users.*', 'master.*', 'item-master.*') ? 'active' : '' }}">
          <span class="nav-icon">⚙️</span>
          <p>
            Settings
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>

        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="{{ route('roles.index') }}" class="nav-link {{ Request::routeIs('roles.*', 'users.*') ? 'active' : '' }}">
              <span class="nav-icon">👥</span>
              <p>User & Role</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('master.index') }}" class="nav-link {{ Request::routeIs('master.*') ? 'active' : '' }}">
              <span class="nav-icon">📋</span>
              <p>Master Data</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('item-master.index') }}" class="nav-link {{ Request::routeIs('item-master.*') ? 'active' : '' }}">
              <span class="nav-icon">🧩</span>
              <p>Item Master</p>
            </a>
          </li>
                
        </ul>
      </li>

      <li class="nav-item">
        <a href="{{ route('activity-logs.index') }}" class="nav-link {{ Request::routeIs('activity-logs.*') ? 'active' : '' }}">
          <span class="nav-icon">🕒</span>
          <p>Activity Logs</p>
        </a>
      </li>

      @elseif($user)
      @php
        if (!empty($allowed)) {
          $showItems    = in_array('items',     $allowed, true);
          $showOrders   = in_array('orders',    $allowed, true);
          $showCatalog  = in_array('catalog',   $allowed, true);
          $showCustomers= in_array('customers', $allowed, true);
          $showSettings = in_array('settings',  $allowed, true);
        } else {
          // Fallback: derive from role permissions (normalize to lowercase)
          $role = $user->role;
          $perms = $role ? $role->permissions()->pluck('name')->map(fn($n) => strtolower($n))->toArray() : [];
          $showItems    = collect($perms)->contains(fn($p) => str_starts_with($p, 'item-'));
          $showOrders   = collect($perms)->contains(fn($p) => str_starts_with($p, 'order-'));
          $showCatalog  = $user->hasRole(['retailer', 'distributor'])
                          || collect($perms)->contains(fn($p) => str_starts_with($p, 'catalog-') || $p === 'catalog');
          $showCustomers= collect($perms)->contains(fn($p) => str_starts_with($p, 'customer-'));
          $showSettings = collect($perms)->contains(fn($p) => str_starts_with($p, 'role-') || str_starts_with($p, 'permission-') || str_starts_with($p, 'setting-'));
        }

        // Retailer / distributor always get catalog + orders only
        if ($user->hasRole(['retailer', 'distributor'])) {
          $showCatalog = true;
          $showOrders  = true;
          $showItems   = false;
        }

        if ($user->hasRole('distributor')) {
          $showCustomers = true;
        }
      @endphp

      @if($showCatalog)
      <li class="nav-header">Shop</li>
        <li class="nav-item">
          <a href="{{ route('catalog') }}" class="nav-link {{ Request::routeIs('catalog', 'catalog.show') ? 'active' : '' }}">
            <span class="nav-icon">🛍️</span>
            <p>Catalog</p>
          </a>
        </li>
      @endif

      @if($showCustomers)
      <li class="nav-header">System</li>
      <li class="nav-item">
        <a href="{{ route('customers.index') }}" class="nav-link {{ Request::routeIs('customers.*') ? 'active' : '' }}">
          <span class="nav-icon">🏢</span>
          <p>Customers</p>
        </a>
      </li>
      @endif

      @if($showItems || $showOrders)
      <li class="nav-header">Sell</li>
        @if($showItems)
        <li class="nav-item">
          <a href="{{ route('items.index') }}" class="nav-link {{ Request::routeIs('items.*') ? 'active' : '' }}">
            <span class="nav-icon">🗂️</span>
            <p>Items</p>
          </a>
        </li>
        @endif

        @if($showOrders)
        <li class="nav-item">
          <a href="{{ route('orders.index') }}" class="nav-link {{ Request::routeIs('orders.*') ? 'active' : '' }}">
            <span class="nav-icon">📦</span>
            <p>Orders</p>
          </a>
        </li>
        @endif
        @endif

        @if($showSettings)
        <li class="nav-header">Manage</li>
        <li class="nav-item {{ Request::routeIs('roles.*', 'users.*', 'master.*', 'item-master.*') ? 'menu-open' : '' }}">
          <a href="#" class="nav-link {{ Request::routeIs('roles.*', 'users.*', 'master.*', 'item-master.*') ? 'active' : '' }}">
            <span class="nav-icon">⚙️</span>
            <p>
              Settings
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>

          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('roles.index') }}" class="nav-link {{ Request::routeIs('roles.*', 'users.*') ? 'active' : '' }}">
                <span class="nav-icon">👥</span>
                <p>User & Role</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('master.index') }}" class="nav-link {{ Request::routeIs('master.*') ? 'active' : '' }}">
                <span class="nav-icon">📋</span>
                <p>Master Data</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('item-master.index') }}" class="nav-link {{ Request::routeIs('item-master.*') ? 'active' : '' }}">
                <span class="nav-icon">🧩</span>
                <p>Item Master</p>
              </a>
            </li>
          </ul>
        </li>
        @endif
      @endif
    </ul>
  </nav>
</div>