<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen antialiased dark:bg-neutral-900 overflow-hidden">

    <!-- Wrapper full screen -->
    <div class="relative min-h-screen w-full bg-cover bg-center font-sans"
         style="background-image: url('/images/frontPage.jpg');">

        <!-- Overlay gelap + blur -->
        <div class="absolute inset-0 bg-black bg-opacity-60 backdrop-blur-sm
                    flex flex-col items-center justify-center text-center px-4 md:px-0 space-y-6 md:space-y-8">

            <!-- Logo + App Name -->
            <div class="flex flex-col items-center gap-2">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2" wire:navigate>
                    <span class="flex h-9 w-9 items-center justify-center rounded-md">
                        <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" />
                    </span>
                    <span class="text-white text-xl font-bold">{{ config('app.name', 'RajaStock') }}</span>
                </a>
            </div>

            <!-- Slot untuk form login -->
            <div class="flex flex-col gap-6 w-full max-w-md">
                {{ $slot }}
            </div>

            <!-- Footer kata-kata kecil -->
            <p class="text-gray-300 text-sm mt-4 md:mt-6 opacity-0 animate-fadeIn delay-600">
                Kelola stok Anda dengan mudah dan cepat
            </p>
        </div>
    </div>

    @fluxScripts

    <!-- Tailwind Custom Animations -->
    <style>
        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fadeIn { animation: fadeIn 0.8s forwards; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-600 { animation-delay: 0.6s; }
    </style>
</body>
</html>
