<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'LeadFlow') }} — Register</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('admin/dist/css/custom.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    html,body{min-height:100%;font-family:'Syne',sans-serif;background:#F4F7FE;color:#e8eaf0}
    /* Dropdown options */
    .select2-container--default .select2-results__option { color: #000 !important;}
    /* Search input */
    .select2-search__field { color: #000 !important;}
    </style>
</head>
<body>
<div class="register-page">
    <!-- <div class="glow"></div> -->
    <div class="register-card">

        <div class="brand">
            <div class="brand-icon">
                <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
            </div>
            <h1>Create an account</h1>
            <p>Sign up to get access to your dashboard</p>
        </div>

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

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="row">
            <div class="field col-md-6">
                <label for="first_name">First name</label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-3.33 0-10 1.67-10 5v3h20v-3c0-3.33-6.67-5-10-5z"/></svg>
                    </span>
                    <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" placeholder="Your first name" required autofocus autocomplete="given-name">
                </div>
                @error('first_name')<p class="err">{{ $message }}</p>@enderror
            </div>
            <div class="field col-md-6">
                <label for="last_name">Last name</label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-3.33 0-10 1.67-10 5v3h20v-3c0-3.33-6.67-5-10-5z"/></svg>
                    </span>
                    <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Your last name" required autocomplete="family-name">
                </div>
                @error('last_name')<p class="err">{{ $message }}</p>@enderror
            </div>
            </div>

            <div class="field">
                <label for="email">Email address</label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg viewBox="0 0 24 24"><path d="M20 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2zm0 2-8 5-8-5h16zm0 12H4V9l8 5 8-5v9z"/></svg>
                    </span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="you@company.com" required autocomplete="username">
                </div>
                @error('email')<p class="err">{{ $message }}</p>@enderror
            </div>

            <div class="field">
                <label for="mobile">Mobile number</label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg viewBox="0 0 24 24"><path d="M17 4h-10c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM12 19c-1.66 0-3-1.34-3-3h6c0 1.66-1.34 3-3 3z"/></svg>
                    </span>
                    <input id="mobile" type="text" name="mobile" value="{{ old('mobile') }}" placeholder="Mobile number" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')" inputmode="numeric" required autocomplete="mobile">
                </div>
                @error('mobile')<p class="err">{{ $message }}</p>@enderror
            </div>

            <div class="field">
                <label for="shop_name">Shop name</label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    </span>
                    <input id="shop_name" type="text" name="shop_name" value="{{ old('shop_name') }}" placeholder="Shop name" required autocomplete="organization">
                </div>
                @error('shop_name')<p class="err">{{ $message }}</p>@enderror
            </div>

            <div class="row">
                <div class="field col-md-6">
                    <label for="state_id">State</label>
                    <select name="state_id" id="state_id" class="form-control select2" required>
                        <option value="">Select State</option>
                        @foreach($states as $state)
                            <option value="{{ $state->id }}"
                                {{ old('state_id') == $state->id ? 'selected' : '' }}>
                                {{ $state->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field col-md-6">
                    <label for="city_id">City</label>
                    <select name="city_id" id="city_id" class="form-control select2" required>
                        <option value="">Select City</option>
                    </select>
                </div>
            </div>

            <div class="field">
                <label for="gst_number">GST number (optional)</label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-3.33 0-10 1.67-10 5v3h20v-3c0-3.33-6.67-5-10-5z"/></svg>
                    </span>
                    <input id="gst_number" type="text" name="gst_number" value="{{ old('gst_number') }}" placeholder="GST number (optional)" autocomplete="organization">
                </div>
                @error('gst_number')<p class="err">{{ $message }}</p>@enderror
            </div>

            <div class="meta-row">
                <div></div>
                <div class="text-sm">
                    <a class="forgot" href="{{ route('login') }}">Already registered?</a>
                </div>
            </div>

            <button type="submit" class="login-btn-submit">
                Create account
                <svg class="arrow" width="15" height="15" fill="#fff" viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg>
            </button>
        </form>

       
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    var STATES = @json($states);
    var CITIES = @json($cities);
    function populateStates($sel, countryId, selectedId) {
    $sel.empty().append('<option value="">-- Select State --</option>');

    STATES.forEach(function (s) {
        if (String(s.country_id) === String(countryId)) {
        $sel.append(
            $('<option>', {
            value: s.id,
            text: s.name
            }).prop('selected', String(s.id) === String(selectedId))
        );
        }
    });
    }

    function getStateId($stateSelect) {
    return $stateSelect.val();
    }

    $(document).ready(function () {
        $('#state_id').select2({
            placeholder: 'Select State',
            width: '100%'
        });
        $('#city_id').select2({
            placeholder: 'Select City',
            width: '100%'
        });
        function loadCities(stateId) {
            $('#city_id').empty();
            $('#city_id').append(
                '<option value="">Select City</option>'
            );
            CITIES.forEach(function (city) {
                if (String(city.state_id) === String(stateId)) {
                    $('#city_id').append(
                        new Option(city.name, city.id)
                    );
                }
            });
            // Refresh Select2
            $('#city_id').trigger('change');
        }
        $('#state_id').on('change', function () {
            loadCities($(this).val());
        });
        // For old selected state after validation error
        let oldState = $('#state_id').val();
        if (oldState) {
            loadCities(oldState);
            let oldCity = "{{ old('city_id') }}";
            if (oldCity) {
                $('#city_id').val(oldCity).trigger('change');
            }
        }
    });
</script>
</body>
</html>