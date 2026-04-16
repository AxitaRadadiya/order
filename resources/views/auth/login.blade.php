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
<style>
:root{
  --bg:#0a0d12;--bg2:#10141c;--bg3:#161b26;--bg4:#1c2333;
  --accent:#6c63ff;--accent2:#9d8fff;--accent-glow:rgba(108,99,255,.22);
  --border:#ffffff0f;--border2:#ffffff18;
  --text:#e8eaf0;--text2:#8892a4;--text3:#525d70;
  --green:#22c55e;--red:#ef4444;
  --sans:'Syne',sans-serif;--mono:'JetBrains Mono',monospace;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html,body{min-height:100%;font-family:var(--sans);background:var(--bg);color:var(--text)}

/* Animated background grid */
.page{
  min-height:100vh;display:flex;align-items:center;justify-content:center;
  padding:24px;position:relative;overflow:hidden;
}
.page::before{
  content:'';position:absolute;inset:0;
  background-image:
    linear-gradient(rgba(108,99,255,.04) 1px,transparent 1px),
    linear-gradient(90deg,rgba(108,99,255,.04) 1px,transparent 1px);
  background-size:40px 40px;
  mask-image:radial-gradient(ellipse 80% 80% at 50% 50%,black,transparent);
}
.glow{
  position:absolute;width:500px;height:500px;border-radius:50%;
  background:radial-gradient(circle,rgba(108,99,255,.12),transparent 70%);
  top:50%;left:50%;transform:translate(-50%,-50%);pointer-events:none;
}

.card{
  width:100%;max-width:420px;
  background:var(--bg2);
  border:1px solid var(--border2);
  border-radius:16px;padding:40px 36px;
  position:relative;z-index:1;
  box-shadow:0 24px 80px rgba(0,0,0,.5),0 0 0 1px rgba(255,255,255,.04);
}
@media(max-width:480px){.card{padding:32px 24px}}

/* Top glow accent */
.card::before{
  content:'';position:absolute;top:-1px;left:20%;right:20%;height:1px;
  background:linear-gradient(90deg,transparent,#6c63ff,#a855f7,transparent);
  border-radius:1px;
}

/* Brand */
.brand{text-align:center;margin-bottom:32px}
.brand-icon{
  width:52px;height:52px;border-radius:14px;
  background:linear-gradient(135deg,#6c63ff,#a855f7);
  display:flex;align-items:center;justify-content:center;
  margin:0 auto 16px;
  box-shadow:0 8px 32px rgba(108,99,255,.4);
}
.brand-icon svg{width:22px;height:22px;fill:#fff}
.brand h1{font-size:1.5rem;font-weight:800;color:#fff;letter-spacing:-.5px;margin-bottom:6px}
.brand p{font-size:.82rem;color:var(--text2)}
.brand p span{color:var(--accent2);font-weight:600}

/* Status messages */
.status-ok{
  background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.2);
  border-radius:8px;padding:.7rem 1rem;font-size:.8rem;color:var(--green);
  margin-bottom:1.2rem;display:flex;align-items:center;gap:.5rem;
}
.status-warn{
  background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2);
  border-radius:8px;padding:.7rem 1rem;font-size:.8rem;color:#f59e0b;
  margin-bottom:1.2rem;display:flex;align-items:flex-start;gap:.5rem;line-height:1.5;
}

/* Field */
.field{margin-bottom:14px}
.field label{
  display:block;font-size:.72rem;font-weight:700;color:var(--text2);
  margin-bottom:6px;letter-spacing:.8px;text-transform:uppercase;
}
.input-wrap{position:relative;display:flex;align-items:center}
.input-icon{position:absolute;left:12px;pointer-events:none;display:flex}
.input-icon svg{width:14px;height:14px;fill:var(--text3)}
.input-wrap input{
  width:100%;height:44px;
  background:var(--bg3);border:1px solid var(--border2);border-radius:9px;
  padding:0 44px 0 38px;
  font-size:.88rem;font-family:var(--sans);color:var(--text);
  outline:none;transition:border-color .2s,box-shadow .2s;
}
.input-wrap input:focus{
  border-color:var(--accent);
  box-shadow:0 0 0 3px var(--accent-glow);
}
.input-wrap input::placeholder{color:var(--text3)}
.eye-btn{
  position:absolute;right:12px;background:none;border:none;cursor:pointer;
  padding:0;display:flex;align-items:center;
}
.eye-btn svg{width:15px;height:15px;fill:var(--text3);transition:fill .18s}
.eye-btn:hover svg{fill:var(--accent2)}

/* Error */
.err{font-size:.74rem;color:var(--red);margin-top:5px;display:flex;align-items:center;gap:4px}
.err::before{content:'◆';font-size:.5rem}

/* Remember + Forgot */
.meta-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px}
.remember{display:flex;align-items:center;gap:7px;font-size:.8rem;color:var(--text2);cursor:pointer}
.remember input[type="checkbox"]{accent-color:var(--accent);width:14px;height:14px}
.forgot{font-size:.8rem;color:var(--accent2);text-decoration:none;font-weight:600;transition:color .18s}
.forgot:hover{color:#fff}

/* Submit */
.btn-submit{
  width:100%;height:46px;
  background:linear-gradient(135deg,#6c63ff,#a855f7);
  border:none;border-radius:9px;color:#fff;
  font-family:var(--sans);font-size:.9rem;font-weight:700;cursor:pointer;
  display:flex;align-items:center;justify-content:center;gap:8px;
  box-shadow:0 6px 24px rgba(108,99,255,.35);
  transition:transform .18s,box-shadow .18s;letter-spacing:.02em;
}
.btn-submit:hover{transform:translateY(-2px);box-shadow:0 12px 32px rgba(108,99,255,.45)}
.btn-submit:active{transform:none}
.btn-submit .arrow{transition:transform .2s}
.btn-submit:hover .arrow{transform:translateX(3px)}

/* Bottom tag */
.card-foot{
  text-align:center;margin-top:22px;
  font-family:var(--mono);font-size:.7rem;color:var(--text3);
}
.card-foot span{color:var(--accent2)}
</style>
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
    
    @if (Route::has('register'))
      <p style="text-align:center;margin-top:12px">
        <a class="forgot" href="{{ route('register') }}">Create an account</a>
      </p>
    @endif

    <div class="card-foot">Secured by <span>{{ config('app.name') }}</span> · v2.0</div>
  </div>
</div>
</body>
</html>