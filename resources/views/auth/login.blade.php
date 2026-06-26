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
  <link rel="stylesheet" href="{{ asset('admin/dist/css/custom.css') }}">
  <style>
    * {margin: 0;padding: 0;box-sizing: border-box;}
    body {font-family: 'Syne', sans-serif;background: #F4F7FE;min-height: 100vh;display: flex;align-items: center;justify-content: center;padding: 24px;}
  </style>
</head>
<body>
<div class="login-page">
  <div class="login-card">

    <div class="brand">
      <div class="brand-icon">
        <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
      </div>
      <h1>Welcome back</h1>
      <p>Sign in to continue to your dashboard</p>
    </div>

    @if (session('status'))
      <div class="status-ok">
        <svg width="14" height="14" fill="#22c55e" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
        <span>{{ session('status') }}</span>
      </div>
    @endif

    @if (! session('whatsapp_login_mobile'))

      {{-- STEP 1: request OTP. Server decides which step to render. --}}
      <form method="POST" action="{{ route('login.request.otp') }}">
        @csrf
        <div class="field">
          <label for="mobile">Mobile number</label>
          <div class="input-wrap">
            <span class="input-icon">
              <svg viewBox="0 0 24 24"><path d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 011 1V20a1 1 0 01-1 1C10.07 21 3 13.93 3 5a1 1 0 011-1h3.5a1 1 0 011 1c0 1.25.2 2.46.57 3.58a1 1 0 01-.25 1.01l-2.2 2.2z"/></svg>
            </span>
            <input id="mobile" type="text" inputmode="numeric" name="mobile" maxlength="10" minlength="10" pattern="[0-9]{10}"
                   placeholder="Enter 10-digit mobile number"
                   value="{{ old('mobile') }}"
                   required autofocus autocomplete="username">
          </div>
          @error('mobile')
            <div class="err">{{ $message }}</div>
          @enderror
        </div>

        <button type="submit" class="login-btn-submit">
          Send OTP
          <svg class="arrow" width="15" height="15" fill="#fff" viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg>
        </button>
      </form>

      <div class="register-link">
        <a href="{{ route('register') }}">Create an account</a>
      </div>

    @else

      {{-- STEP 2: verify OTP. Mobile comes from the session the
           controller set in requestOtp(), not from client JS. --}}
      <div class="otp-section">
        <div class="resend-row">
          <div class="left">
            <span class="check">✔</span>
            <span>OTP sent to <strong>{{ session('whatsapp_login_mobile') }}</strong></span>
          </div>
          <div>
            <form class="resend-form" method="POST" action="{{ route('login.request.otp') }}">
              @csrf
              <input type="hidden" name="mobile" value="{{ session('whatsapp_login_mobile') }}">
              <button type="submit" id="resendBtn" class="resend-btn" disabled>Resend OTP</button>
            </form>
            <span class="timer" id="timerDisplay">00:42</span>
          </div>
        </div>

        <form method="POST" action="{{ route('login.verify.otp') }}">
          @csrf
          <input type="hidden" name="mobile" value="{{ session('whatsapp_login_mobile') }}">

          <div class="field">
            <label for="otp">Enter OTP</label>
            <div class="input-wrap">
              <span class="input-icon">
                <svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 1010 10A10 10 0 0012 2zm1 15h-2v-2h2zm0-4h-2V7h2z"/></svg>
              </span>
              <input id="otp" type="text" inputmode="numeric" name="otp" maxlength="6" pattern="[0-9]{6}"
                     placeholder="6-digit OTP"
                     value="{{ old('otp') }}"
                     required autofocus autocomplete="one-time-code">
            </div>
            @error('otp')
              <div class="err">{{ $message }}</div>
            @enderror
          </div>

          <button type="submit" class="login-btn-submit btn-verify">
            Verify OTP
            <svg class="arrow" width="15" height="15" fill="#fff" viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg>
          </button>
        </form>

        <div class="new-here">
          Wrong number? <a href="{{ route('login', ['change' => 1]) }}">Change mobile number</a>
        </div>
      </div>

    @endif

  </div>
</div>

<script>
  (function () {
    "use strict";
    const resendBtn = document.getElementById('resendBtn');
    const timerDisplay = document.getElementById('timerDisplay');
    if (!resendBtn || !timerDisplay) return;

    let seconds = 42;

    function render() {
      const m = String(Math.floor(seconds / 60)).padStart(2, '0');
      const s = String(seconds % 60).padStart(2, '0');
      timerDisplay.textContent = `${m}:${s}`;
    }

    render();
    const interval = setInterval(() => {
      seconds--;
      if (seconds <= 0) {
        clearInterval(interval);
        timerDisplay.textContent = '00:00';
        resendBtn.disabled = false;
        return;
      }
      render();
    }, 1000);
  })();
</script>
</body>
</html>