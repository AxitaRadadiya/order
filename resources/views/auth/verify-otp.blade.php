<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        @if (!empty($isWhatsAppLogin))
            Enter the OTP you received on WhatsApp to verify your login.
        @else
            Enter the OTP sent to your email address to verify your password reset request.
        @endif
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ !empty($isWhatsAppLogin) ? route('login.verify.otp') : route('password.verify.store') }}">

        @csrf

        @if (!empty($isWhatsAppLogin))
            <input type="hidden" name="mobile" value="{{ $mobile ?? '' }}" />
        @else
            <div>
                <x-input-label for="email" :value="'Email Address'" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', session('email'))" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
        @endif

        <div class="mt-4">
            <x-input-label for="otp" :value="'OTP'" />
            <x-text-input id="otp" class="block mt-1 w-full" type="text" name="otp" :value="old('otp')" required />
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                Verify OTP
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
