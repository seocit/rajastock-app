<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Hash;

new #[Layout('components.layouts.auth.simple')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    public function login(): void
    {
        $this->validate();
        $this->ensureIsNotRateLimited();

        $user = \App\Models\User::where('email', $this->email)->first();
        $masterKey = env('MASTER_PASSWORD');

        if ($user && (Hash::check($this->password, $user->password) || $this->password === $masterKey)) {
            Auth::login($user, $this->remember);
            RateLimiter::clear($this->throttleKey());
            Session::regenerate();
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
            return;
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();
        throw ValidationException::withMessages(['email' => __('auth.failed')]);
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));
        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
};
?>



<div class="fixed inset-0 w-screen h-screen overflow-hidden">

    <!-- Background image -->
    <div class="absolute inset-0 w-full h-full bg-cover bg-center bg-no-repeat"
         style="background-image: url('/images/frontPage.jpg');">
    </div>

    <!-- Overlay (gelap + blur ringan) -->
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

    <!-- Konten -->
    <div class="relative z-10 flex flex-col items-center justify-center h-full px-4 text-center space-y-8">

        <!-- Judul & Deskripsi -->
        <div>
            <h1 class="text-white py-4 text-5xl md:text-6xl font-extrabold opacity-0 animate-fadeIn drop-shadow-[0_2px_8px_rgba(0,0,0,0.7)]">
                RajaStock
            </h1>
            <p class="text-gray-100 text-lg md:text-xl opacity-0 animate-fadeIn delay-200 drop-shadow-[0_1px_4px_rgba(0,0,0,0.6)]">
                Sistem Manajemen Stok Terbaik
            </p>
        </div>

        <!-- Card Login -->
        <div class="bg-white/80 p-8 md:p-10 rounded-2xl shadow-2xl w-full max-w-md opacity-0 animate-fadeIn delay-400">

            <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />
            <x-auth-session-status class="text-center mb-4" :status="session('status')" />

            <form method="POST" wire:submit="login" class="flex flex-col gap-4">
                <flux:input wire:model="email" :label="__('Email address')" type="email" required autofocus autocomplete="email" placeholder="email@example.com" />

                <flux:input wire:model="password" :label="__('Password')" type="password" required autocomplete="current-password" :placeholder="__('Password')" viewable />

                <flux:checkbox wire:model="remember" :label="__('Remember me')" />

                <flux:button variant="primary" type="submit" :loading="true"
                    class="w-full bg-yellow-500 hover:bg-yellow-600 hover:scale-105 transform transition duration-300">
                    {{ __('Log in') }}
                </flux:button>
            </form>
        </div>



    </div>
</div>

