<header class="admin-header">
    <div class="container">
        <a href="{{ url('/') }}" class="brand">Order App</a>

        <div class="header-right">
            <span class="user-name">{{ auth()->user()?->name ?? 'Guest' }}</span>
        </div>
    </div>
</header>
