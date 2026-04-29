<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth.login') }} — {{ __('common.protege_programme') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/protege-mindef-logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-white">
    <!-- Full-cover white background with circle illustrations -->
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-white via-[#f8fafc] to-[#f1f5f9]"></div>
        <img src="{{ asset('images/bg-circles.svg') }}" alt="" class="absolute inset-0 w-full h-full object-cover" />
        <!-- Top accent bar -->
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#274670] via-[#C8102E] to-[#274670]"></div>
    </div>

    <main class="relative z-10 flex min-h-screen items-center justify-center px-4 py-10 sm:px-6">
        <div class="w-full max-w-[30rem]">
            <div class="mb-10 flex justify-center">
                <a href="{{ route('portal.index') }}" class="inline-flex rounded-2xl focus:outline-none focus:ring-4 focus:ring-[#274670]/10" aria-label="Go to talent portal">
                    <img src="{{ asset('images/protege-mindef-logo.png') }}" alt="Protege MINDEF" class="h-24 w-auto drop-shadow-[0_12px_24px_rgba(30,58,95,0.14)] sm:h-28">
                </a>
            </div>

            <div class="rounded-[1.75rem] border border-slate-200/60 bg-white/90 p-8 shadow-[0_20px_60px_rgba(39,70,112,0.10)] backdrop-blur-xl md:p-9">
                <div class="mb-8 flex items-start justify-between gap-4">
                    <h1 class="text-[2rem] font-semibold tracking-[-0.04em] text-slate-800">{{ __('auth.login') }}</h1>

                    <div class="flex items-center rounded-xl bg-slate-100 p-1.5 shadow-inner">
                        <form method="POST" action="{{ route('language.set') }}">
                            @csrf
                            <input type="hidden" name="lang" value="ms">
                            <button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-all {{ app()->getLocale() === 'ms' ? 'bg-[#274670] text-white shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">BM</button>
                        </form>
                        <form method="POST" action="{{ route('language.set') }}">
                            @csrf
                            <input type="hidden" name="lang" value="en">
                            <button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-all {{ app()->getLocale() === 'en' ? 'bg-[#274670] text-white shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">EN</button>
                        </form>
                    </div>
                </div>

                @if($errors->any())
                    <div class="mb-5 rounded-2xl border border-red-200 bg-red-50/90 p-4">
                        @foreach($errors->all() as $error)
                            <p class="text-sm text-red-600">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                @if(session('success'))
                    <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50/90 p-4">
                        <p class="text-sm text-emerald-700">{{ session('success') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">{{ __('auth.email') }}</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full rounded-2xl border border-slate-200 bg-white px-5 py-3.5 text-base text-slate-700 shadow-[inset_0_1px_1px_rgba(15,23,42,0.03)] outline-none transition placeholder:text-slate-400 focus:border-[#274670] focus:ring-4 focus:ring-[#274670]/10 @error('email') border-red-400 focus:border-red-400 focus:ring-red-100 @enderror"
                               placeholder="admin@yltat.gov.my">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">{{ __('auth.password_label') }}</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required
                                   class="w-full rounded-2xl border border-slate-200 bg-white px-5 py-3.5 pr-14 text-base text-slate-700 shadow-[inset_0_1px_1px_rgba(15,23,42,0.03)] outline-none transition placeholder:text-slate-400 focus:border-[#274670] focus:ring-4 focus:ring-[#274670]/10"
                                   placeholder="••••••••">
                            <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 transition hover:text-slate-600" aria-label="Toggle password visibility">
                                <svg id="eye-open" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <svg id="eye-closed" class="hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.58 10.58A2 2 0 0012 14a2 2 0 001.42-.58"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.88 5.09A9.77 9.77 0 0112 5c4.48 0 8.27 2.94 9.54 7a10.64 10.64 0 01-4.04 5.07"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.23 6.23A10.65 10.65 0 002.46 12c1.27 4.06 5.06 7 9.54 7 1.61 0 3.14-.38 4.5-1.05"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="remember" id="remember" class="h-4 w-4 rounded border-slate-300 text-[#274670] focus:ring-[#274670]">
                            <label for="remember" class="text-sm font-medium text-slate-600">{{ __('auth.remember_me') }}</label>
                        </div>
                        <a href="{{ route('password.forgot') }}" class="text-sm font-medium text-[#274670] hover:text-[#1f3a5c] transition-colors">{{ __('auth.forgot_password') }}?</a>
                    </div>

                    <button type="submit"
                            class="w-full rounded-2xl bg-[#274670] px-4 py-4 text-base font-semibold text-white shadow-[0_14px_30px_rgba(39,70,112,0.28)] transition hover:bg-[#1f3a5c] focus:outline-none focus:ring-4 focus:ring-[#274670]/15">
                        {{ __('auth.login') }}
                    </button>
                </form>
            </div>

            <p class="mt-7 text-center text-xs font-medium text-slate-400">
                &copy; {{ date('Y') }} Yayasan Lembaga Tabung Angkatan Tentera (YLTAT)
            </p>
        </div>
    </main>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const eyeOpen = document.getElementById('eye-open');
            const eyeClosed = document.getElementById('eye-closed');
            const showPassword = input.type === 'password';

            input.type = showPassword ? 'text' : 'password';
            eyeOpen.classList.toggle('hidden', showPassword);
            eyeClosed.classList.toggle('hidden', !showPassword);
        }
    </script>
</body>
</html>
