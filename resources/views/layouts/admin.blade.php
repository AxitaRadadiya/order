<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials._meta')
    @include('partials._styles')
</head>
<body class="admin-layout">
    @include('components.admin.header')

    <div class="admin-wrapper">
        @include('components.admin.sidebar')

        <main class="admin-main">
            @include('components.admin.breadcrumb')

            <div class="admin-content">
                @include('components.alert')
                @yield('content')
            </div>
        </main>
    </div>

    @include('components.admin.footer')
    @include('partials._scripts')
</body>
</html>
