<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('company.portal_title')) — Protege MINDEF</title>
    <link rel="icon" type="image/png" href="{{ asset('images/protege-mindef-logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-link.active { background-color: rgba(200, 16, 46, 0.15); border-left: 3px solid #C8102E; color: #fff; }
        .sidebar-link:hover { background-color: rgba(255,255,255,0.08); }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #1e3a5f; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.3); border-radius: 3px; }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-gray-50">
@php
    $companyContext = auth()->user()->company
        ?? \App\Models\Company::where('contact_email', auth()->user()->email)->first();
@endphp
<div class="flex h-full" x-data="{ sidebarOpen: false }">

    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 flex flex-col w-64 bg-[#1E3A5F] shadow-xl transition-transform duration-300"
         :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        <!-- Logo -->
        <div class="flex items-center justify-center px-6 py-5 border-b border-white/10">
            <img src="{{ asset('images/yltat-horizantal-white.png') }}" alt="Yayasan LTAT" class="h-10 w-auto">
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">

            <a href="{{ route('company.dashboard') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-blue-100 text-sm transition-all {{ request()->routeIs('company.dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>{{ __('nav.dashboard') }}</span>
            </a>

            <a href="{{ route('company.placements.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-blue-100 text-sm transition-all {{ request()->routeIs('company.placements.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>{{ __('nav.placements') }}</span>
            </a>

            <a href="{{ route('company.feedback.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-blue-100 text-sm transition-all {{ request()->routeIs('company.feedback.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
                <span>{{ __('nav.feedback') }}</span>
                @php
                    $pendingFb = $companyContext?->placements()
                        ->whereIn('placement_status', ['active','confirmed'])
                        ->whereDoesntHave('feedback', fn($q) => $q->where('feedback_from','company'))
                        ->count() ?? 0;
                @endphp
                @if($pendingFb > 0)
                    <span class="ml-auto bg-[#C8102E] text-white text-xs rounded-full px-2 py-0.5">{{ $pendingFb }}</span>
                @endif
            </a>

            <a href="{{ route('company.finance.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-blue-100 text-sm transition-all {{ request()->routeIs('company.finance.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ __('common.tab_finance') }}</span>
            </a>
        </nav>

        <!-- Footer -->
        <div class="p-4 border-t border-white/10">
            <p class="text-center text-blue-300/50 text-[9px] mb-2">{{ __('common.powered_by') }}</p>
            <div class="flex justify-center">
                <img src="{{ asset('images/WESB-Logo-bcground-wh.png') }}" alt="Weststar Engineering Sdn Bhd" class="h-7 w-auto opacity-80">
            </div>
        </div>
    </div>

    <!-- Mobile overlay -->
    <div class="fixed inset-0 bg-black/50 z-40 lg:hidden"
         x-show="sidebarOpen" @click="sidebarOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         style="display: none;"></div>

    <!-- Main content -->
    <div class="flex-1 flex flex-col min-h-screen lg:ml-64">

        <!-- Top navbar -->
        <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-30">
            <div class="flex items-center justify-between h-16 px-4 lg:px-6">

                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-md text-gray-500 hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <div class="flex-1 lg:flex-none">
                    <h1 class="text-gray-700 font-semibold text-base lg:text-lg">@yield('page-title', __('company.portal_title'))</h1>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Language toggle -->
                    <div class="flex items-center bg-gray-100 rounded-lg p-1 gap-1">
                        <form method="POST" action="{{ route('language.set') }}">
                            @csrf <input type="hidden" name="lang" value="ms">
                            <button type="submit" class="px-2.5 py-1 rounded-md text-xs font-medium transition-all {{ app()->getLocale() === 'ms' ? 'bg-[#1E3A5F] text-white' : 'text-gray-500 hover:text-gray-700' }}">BM</button>
                        </form>
                        <form method="POST" action="{{ route('language.set') }}">
                            @csrf <input type="hidden" name="lang" value="en">
                            <button type="submit" class="px-2.5 py-1 rounded-md text-xs font-medium transition-all {{ app()->getLocale() === 'en' ? 'bg-[#1E3A5F] text-white' : 'text-gray-500 hover:text-gray-700' }}">EN</button>
                        </form>
                    </div>

                    <!-- User dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-[#1E3A5F] flex items-center justify-center text-white text-sm font-bold">
                                {{ substr(auth()->user()->full_name ?? 'C', 0, 1) }}
                            </div>
                            <span class="hidden md:block text-sm text-gray-700">{{ auth()->user()->full_name }}</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-52 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
                             style="display: none;">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <div class="text-sm font-medium text-gray-800">{{ auth()->user()->full_name }}</div>
                                <div class="text-xs text-gray-500">{{ $companyContext?->company_name }}</div>
                            </div>
                            <div class="border-t border-gray-100 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    {{ __('nav.logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 p-4 lg:p-6">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-green-700 text-sm">{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-red-700 text-sm">{{ session('error') }}</p>
                </div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-red-700 text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>

        <footer class="bg-white border-t border-gray-200 px-6 py-3">
            <p class="text-xs text-gray-400 text-center">&copy; {{ date('Y') }} {{ __('common.protege_programme') }} — {{ __('common.app_full_name') }}. {{ __('common.all_rights_reserved') }} {{ __('common.powered_by_weststar') }}</p>
        </footer>
    </div>
</div>
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@stack('scripts')
</body>
</html>
