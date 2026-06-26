<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ config('app.name', 'LeadFlow') }} — Sign In</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="page">
  <div class="glow"></div>
  <div class="card">

    {{-- Brand --}}
    <div class="brand">
      <div class="brand-icon">
        <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
      </div>
      <h1>Welcome back</h1>
      <p>Sign in to <span>{{ config('app.name') }} CRM</span></p>
    </div>

    {{-- Session status --}}
    @if(session('status') && str_contains(session('status'),'another device'))
      <div class="status-warn">
        <svg width="14" height="14" fill="#f59e0b" viewBox="0 0 24 24"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
        <span>{{ session('status') }}</span>
      </div>
    @elseif(session('status'))
      <div class="status-ok">
        <svg width="14" height="14" fill="#22c55e" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
        {{ session('status') }}
      </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
      @csrf

      {{-- Email --}}
      <div class="field">
        <label for="email">Email address</label>
        <div class="input-wrap">
          <span class="input-icon">
            <svg viewBox="0 0 24 24"><path d="M20 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2zm0 2-8 5-8-5h16zm0 12H4V9l8 5 8-5v9z"/></svg>
          </span>
          <input id="email" type="email" name="email"
                 value="{{ old('email') }}"
                 placeholder="you@company.com"
                 required autofocus autocomplete="username">
        </div>
        @error('email')<p class="err">{{ $message }}</p>@enderror
      </div>

      {{-- Password --}}
      <div class="field">
        <label for="password">Password</label>
        <div class="input-wrap">
          <span class="input-icon">
            <svg viewBox="0 0 24 24"><path d="M12 1a7 7 0 00-7 7v2H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2v-9a2 2 0 00-2-2h-1V8a7 7 0 00-7-7zm0 2a5 5 0 015 5v2H7V8a5 5 0 015-5zm0 10a2 2 0 110 4 2 2 0 010-4z"/></svg>
          </span>
          <input id="password" type="password" name="password"
                 placeholder="••••••••"
                 required autocomplete="current-password">
          <button type="button" class="eye-btn" onclick="
            var p=document.getElementById('password');
            var s=this.querySelectorAll('svg');
            if(p.type==='password'){p.type='text';s[0].style.display='none';s[1].style.display='block'}
            else{p.type='password';s[0].style.display='block';s[1].style.display='none'}">
            <svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5C21.27 7.61 17 4.5 12 4.5zM12 17a5 5 0 110-10 5 5 0 010 10zm0-8a3 3 0 100 6 3 3 0 000-6z"/></svg>
            <svg viewBox="0 0 24 24" style="display:none"><path d="M2 5.27L3.28 4 20 20.72 18.73 22l-3.08-3.08A10.49 10.49 0 0112 19.5C7 19.5 2.73 16.39 1 12a10.44 10.44 0 014.35-5.38L2 5.27zM12 6a5 5 0 014.9 4.07L11.93 5A4.97 4.97 0 0112 6zm0 11a5 5 0 01-4.9-4.07L12.07 18A5 5 0 0112 17z"/></svg>
          </button>
        </div>
        @error('password')<p class="err">{{ $message }}</p>@enderror
      </div>

      {{-- Remember + Forgot --}}
      <div class="meta-row">
        <label class="remember">
          <input type="checkbox" name="remember" id="remember_me">
          Keep me signed in
        </label>
        @if(Route::has('password.request'))
          <a class="forgot" href="{{ route('password.request') }}">Forgot password?</a>
        @endif
      </div>

      <button type="submit" class="btn-submit">
        Sign In
        <svg class="arrow" width="15" height="15" fill="#fff" viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg>
      </button>
    </form>

    <div class="card-foot">Secured by <span>{{ config('app.name') }}</span> · v2.0</div>
  </div>
</div>
</body>
</html>