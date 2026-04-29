<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth.forgot_password') }} — {{ __('common.protege_programme') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/protege-mindef-logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-white">
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-white via-[#f8fafc] to-[#f1f5f9]"></div>
        <img src="{{ asset('images/bg-circles.svg') }}" alt="" class="absolute inset-0 w-full h-full object-cover" />
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#274670] via-[#C8102E] to-[#274670]"></div>
    </div>

    <main class="relative z-10 flex min-h-screen items-center justify-center px-4 py-10 sm:px-6">
        <div class="w-full max-w-[28rem]">
            <div class="mb-8 flex justify-center">
                <a href="{{ route('login') }}">
                    <img src="{{ asset('images/protege-mindef-logo.png') }}" alt="Protege MINDEF" class="h-20 w-auto drop-shadow-[0_12px_24px_rgba(30,58,95,0.14)]">
                </a>
            </div>

            <div class="rounded-[1.75rem] border border-slate-200/60 bg-white/90 p-8 shadow-[0_20px_60px_rgba(39,70,112,0.10)] backdrop-blur-xl">
                <h1 class="text-xl font-semibold text-slate-800 mb-2">{{ __('auth.forgot_password') }}</h1>

                {{-- Step indicator --}}
                <div class="flex items-center gap-2 mb-6">
                    @foreach([
                        [1, __('auth.fp_step_email')],
                        [2, __('auth.fp_step_otp')],
                        [3, __('auth.fp_step_reset')],
                    ] as [$num, $label])
                    <div class="flex items-center {{ !$loop->first ? 'flex-1' : '' }}">
                        @if(!$loop->first)
                            <div class="flex-1 h-px mx-1.5 {{ $num <= $step ? 'bg-[#274670]' : 'bg-gray-200' }}"></div>
                        @endif
                        <div class="flex flex-col items-center">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold {{ $num <= $step ? 'bg-[#274670] text-white' : 'bg-gray-200 text-gray-400' }}">
                                @if($num < $step) &#10003; @else {{ $num }} @endif
                            </div>
                            <span class="text-[10px] mt-0.5 whitespace-nowrap {{ $num <= $step ? 'text-[#274670] font-medium' : 'text-gray-400' }}">{{ $label }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if(session('error'))
                    <div class="mb-4 rounded-xl border border-red-200 bg-red-50/90 p-3">
                        <p class="text-sm text-red-600">{{ session('error') }}</p>
                    </div>
                @endif
                @if(session('success'))
                    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50/90 p-3">
                        <p class="text-sm text-emerald-700">{{ session('success') }}</p>
                    </div>
                @endif

                {{-- Step 1: Enter email --}}
                @if($step === 1)
                    <p class="text-sm text-slate-500 mb-4">{{ __('auth.fp_email_hint') }}</p>
                    <form method="POST" action="{{ route('password.send-otp') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-slate-700">{{ __('auth.email') }}</label>
                            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                   class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none focus:border-[#274670] focus:ring-2 focus:ring-[#274670]/10"
                                   placeholder="your@email.com">
                        </div>
                        <button type="submit"
                                class="w-full rounded-xl bg-[#274670] px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#1f3a5c] transition-colors">
                            {{ __('auth.fp_send_otp') }}
                        </button>
                    </form>

                {{-- Step 2: Enter OTP --}}
                @elseif($step === 2)
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 mb-4">
                        <p class="text-xs text-blue-700">{!! __('auth.fp_otp_sent_to', ['email' => '<strong>' . $email . '</strong>']) !!}</p>
                    </div>
                    <form method="POST" action="{{ route('password.verify-otp') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-slate-700">{{ __('common.enter_otp') }}</label>
                            <input type="text" name="otp" maxlength="6" inputmode="numeric" pattern="\d{6}" placeholder="000000" required autofocus
                                   class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-center text-2xl font-mono tracking-[0.5em] outline-none focus:border-[#274670] focus:ring-2 focus:ring-[#274670]/10">
                        </div>
                        <button type="submit"
                                class="w-full rounded-xl bg-[#274670] px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-[#1f3a5c] transition-colors">
                            {{ __('common.verify_otp') }}
                        </button>
                    </form>
                    <div class="mt-3 text-center">
                        <form method="POST" action="{{ route('password.send-otp') }}" class="inline">
                            @csrf
                            <input type="hidden" name="email" value="{{ $email }}">
                            <button type="submit" class="text-xs text-slate-400 hover:text-[#274670] underline">{{ __('common.resend_otp') }}</button>
                        </form>
                    </div>

                {{-- Step 3: Set new password --}}
                @elseif($step === 3)
                    <form method="POST" action="{{ route('password.reset') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-slate-700">{{ __('common.new_password') }}</label>
                            <input type="password" name="new_password" required minlength="8" autocomplete="new-password"
                                   class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none focus:border-[#274670] focus:ring-2 focus:ring-[#274670]/10">
                            <p class="mt-1 text-xs text-slate-400">{{ __('common.password_min_hint') }}</p>
                            @error('new_password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-slate-700">{{ __('common.confirm_new_password') }}</label>
                            <input type="password" name="new_password_confirmation" required autocomplete="new-password"
                                   class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none focus:border-[#274670] focus:ring-2 focus:ring-[#274670]/10">
                        </div>
                        <button type="submit"
                                class="w-full rounded-xl bg-[#C8102E] px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-red-700 transition-colors">
                            {{ __('common.update_password') }}
                        </button>
                    </form>
                @endif

                <div class="mt-5 text-center">
                    <a href="{{ route('login') }}" class="text-sm text-slate-400 hover:text-slate-600 transition-colors">&larr; {{ __('auth.back_to_login') }}</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
