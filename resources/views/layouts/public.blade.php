<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Bakat') — Protege MINDEF</title>
    <link rel="icon" type="image/png" href="{{ asset('images/protege-mindef-logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; } [x-cloak] { display: none !important; }</style>
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col relative bg-white">
    <!-- Full-cover white background with circle illustrations -->
    <div class="pointer-events-none fixed inset-0 z-0 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-white via-[#f8fafc] to-[#f1f5f9]"></div>
        <img src="{{ asset('images/bg-circles.svg') }}" alt="" class="absolute inset-0 w-full h-full object-cover" />
    </div>

    <!-- Navbar -->
    <nav class="relative z-10 border-b border-slate-200/80 bg-white/90 shadow-[0_4px_20px_rgba(39,70,112,0.06)] backdrop-blur-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('portal.index') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/protege-mindef-logo.png') }}" alt="Protege MINDEF" class="h-10 w-auto">
                    <div>
                        <div class="text-slate-900 font-bold text-sm">Protege MINDEF</div>
                        <div class="text-slate-500 text-xs">{{ __('nav.portal') }}</div>
                    </div>
                </a>

                <div class="flex items-center gap-4">
                    <!-- Language toggle -->
                    <div class="flex items-center rounded-xl border border-slate-200 bg-slate-50 p-1 gap-1 shadow-inner">
                        <form method="POST" action="{{ route('language.set') }}">
                            @csrf
                            <input type="hidden" name="lang" value="ms">
                            <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold transition-all {{ app()->getLocale() === 'ms' ? 'bg-[#274670] text-white shadow-sm' : 'text-slate-500 hover:bg-white hover:text-slate-700' }}">BM</button>
                        </form>
                        <form method="POST" action="{{ route('language.set') }}">
                            @csrf
                            <input type="hidden" name="lang" value="en">
                            <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold transition-all {{ app()->getLocale() === 'en' ? 'bg-[#274670] text-white shadow-sm' : 'text-slate-500 hover:bg-white hover:text-slate-700' }}">EN</button>
                        </form>
                    </div>

                    <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 transition-colors hover:text-[#274670]">
                        {{ __('auth.login') }}
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="relative z-10 flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="relative z-10 bg-[#1E3A5F] text-blue-200 py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="flex justify-center mb-3">
                <img src="{{ asset('images/yltat-horizantal-white.png') }}" alt="YLTAT" class="h-7 w-auto opacity-80">
            </div>
            <p class="text-sm">&copy; {{ date('Y') }} Protege MINDEF — {{ __('common.yltat_full') }}</p>
            <p class="text-xs mt-2 text-blue-400">{{ __('common.app_full_name') }} — {{ __('common.protege_programme') }}</p>
        </div>
    </footer>

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @stack('scripts')
</body>
</html>
