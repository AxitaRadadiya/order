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
    * {margin: 0;padding: 0;box-sizing: border-box;}
    body {font-family: 'Syne', sans-serif;background: #F4F7FE;min-height: 100vh;display: flex;align-items: center;justify-content: center;padding: 24px;}
    .page {position: relative;width: 100%;max-width: 420px;}
    .card {background: #ffffff;border: 1px solid rgba(255, 255, 255, 0.09);border-radius: 16px;padding: 40px 36px;position: relative;box-shadow: 0 12px 40px rgba(0,0,0,0.02);}
    .card::before {content: '';position: absolute;top: -1px;left: 20%;right: 20%;height: 1px;background: linear-gradient(90deg, transparent, #6c63ff, #a855f7, transparent);}
    .brand { text-align: center; margin-bottom: 28px; }
    .brand-icon {width: 52px;height: 52px;background: linear-gradient(135deg, #6c63ff, #a855f7);border-radius: 14px;display: flex;align-items: center;justify-content: center;margin: 0 auto 16px;box-shadow: 0 8px 32px rgba(108, 99, 255, 0.35);}
    .brand-icon svg { fill: #fff; width: 24px; height: 24px; }
    .brand h1 {font-size: 1.5rem;font-weight: 800;color: #0b0e1a;letter-spacing: -0.5px;}
    .brand p {font-size: 0.82rem;color: #6b7a8f;margin-top: 4px;}

    .status-ok {background: rgba(34, 197, 94, 0.08);border: 1px solid rgba(34, 197, 94, 0.2);border-radius: 8px;padding: 0.7rem 1rem;font-size: 0.8rem;color: #22c55e;display: flex;align-items: center;gap: 0.5rem;margin-bottom: 1.2rem;}

    .field { margin-bottom: 14px; }
    .field label {display: block;font-size: 0.7rem;font-weight: 700;color: #6b7a8f;text-transform: uppercase;letter-spacing: 0.8px;margin-bottom: 5px;}
    .input-wrap {position: relative;display: flex;align-items: center;}
    .input-icon {position: absolute;left: 12px;pointer-events: none;display: flex;}
    .input-icon svg {width: 14px;height: 14px;fill: #8892a4;}
    .input-wrap input {width: 100%;height: 44px;background: #f8faff;border: 1px solid #e5e9f0;border-radius: 9px;padding: 0 44px 0 38px;font-size: 0.88rem;font-family: 'Syne', sans-serif;color: #0b0e1a;outline: none;transition: border 0.2s, box-shadow 0.2s;}
    .input-wrap input:focus {border-color: #6c63ff;box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.2);}
    .err {font-size: 0.74rem;color: #ef4444;margin-top: 5px;display: flex;align-items: center;gap: 4px;}
    .err::before { content: '◆'; font-size: 0.5rem; }

    .btn-submit {width: 100%;height: 46px;background: linear-gradient(135deg, #6c63ff, #a855f7);border: none;border-radius: 9px;color: #fff;font-family: 'Syne', sans-serif;font-weight: 700;font-size: 0.9rem;cursor: pointer;display: flex;align-items: center;justify-content: center;gap: 8px;box-shadow: 0 6px 24px rgba(108, 99, 255, 0.35);transition: transform 0.18s, box-shadow 0.18s;letter-spacing: 0.02em;}
    .btn-submit:hover {transform: translateY(-2px);box-shadow: 0 12px 32px rgba(108, 99, 255, 0.45);}
    .btn-submit .arrow { transition: transform 0.2s; }
    .btn-submit:hover .arrow { transform: translateX(3px); }

    .card-foot {text-align: center;margin-top: 18px;font-family: 'JetBrains Mono', monospace;font-size: 0.7rem;color: #8892a4;}
    .card-foot span { color: #6c63ff; font-weight: 600; }

    .otp-section {margin-top: 16px;animation: fadeSlide 0.25s ease;}
    @keyframes fadeSlide {0% { opacity: 0; transform: translateY(6px); }100% { opacity: 1; transform: translateY(0); }}

    .register-link {text-align: center;margin-top: 14px;font-size: 0.8rem;}
    .register-link a {color: #6c63ff;text-decoration: none;font-weight: 600;}
    .register-link a:hover { text-decoration: underline; }

    /* resend + timer row */
    .resend-row {display: flex;align-items: center;justify-content: space-between;background: #f0f3fe;border-radius: 10px;padding: 10px 14px;margin: 6px 0 16px 0;font-size: 0.8rem;color: #1c2333;}
    .resend-row .left {display: flex;align-items: center;gap: 8px;}
    .resend-row .left .check {color: #22c55e;font-weight: 700;}
    .resend-form {display: inline;}
    .resend-btn {background: none;border: none;color: #6c63ff;font-weight: 700;font-family: 'Syne', sans-serif;font-size: 0.8rem;cursor: pointer;padding: 4px 8px;border-radius: 6px;transition: background 0.2s;}
    .resend-btn:hover:not(:disabled) {background: rgba(108, 99, 255, 0.08);}
    .resend-btn:disabled {color: #a0aec0;cursor: not-allowed;}
    .timer {font-weight: 600;color: #1c2333;font-family: 'JetBrains Mono', monospace;margin-left: 4px;}
    .btn-verify {margin-top: 4px;}
    .new-here {margin-top: 18px;text-align: center;font-size: 0.8rem;color: #6b7a8f;}
    .new-here a {color: #6c63ff;font-weight: 600;text-decoration: none;}
    .new-here a:hover { text-decoration: underline; }
  </style>
</head>
<body>
<div class="page">
  <div class="card">

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

        <button type="submit" class="btn-submit">
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

          <button type="submit" class="btn-submit btn-verify">
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