<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('nav.dashboard')) — {{ __('common.protege_programme') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/protege-mindef-logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-link.active { background-color: rgba(200, 16, 46, 0.15); border-left: 3px solid #C8102E; }
        .sidebar-link:hover { background-color: rgba(255,255,255,0.08); }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #1e3a5f; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.3); border-radius: 3px; }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-gray-100">
<div class="flex h-full" x-data="{ sidebarOpen: false }" x-init="">

    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 flex flex-col w-64 bg-[#1E3A5F] shadow-xl transition-transform duration-300"
         :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
         id="sidebar">

        <!-- Logo/Header -->
        <div class="flex items-center justify-center px-6 py-5 border-b border-white/10">
            <img src="{{ asset('images/yltat-horizantal-white.png') }}" alt="Yayasan LTAT" class="h-10 w-auto">
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            @php $role = auth()->user()->role?->role_name; @endphp

            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-blue-100 text-sm transition-all {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>{{ __('nav.dashboard') }}</span>
            </a>

            {{-- ═══════════════════════════════ MANAGEMENT ═══════════════════════════════ --}}
            <div class="pt-2 pb-1">
                <div class="px-3 text-xs font-semibold text-blue-400 uppercase tracking-wider">{{ __('nav.management') }}</div>
            </div>

            {{-- Applications --}}
            @if(in_array($role, ['super_admin', 'pmo_admin']))
            <a href="{{ route('admin.applications.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-blue-100 text-sm transition-all {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                <span>{{ __('nav.applications') }}</span>
                @php $pendingCount = \App\Models\Talent::where('status','applied')->count(); @endphp
                @if($pendingCount > 0)
                    <span class="ml-auto bg-[#C8102E] text-white text-xs rounded-full px-2 py-0.5">{{ $pendingCount }}</span>
                @endif
            </a>
            @endif

            {{-- Graduate Management --}}
            @if(!in_array($role, ['rakan_kolaborasi']))
            <a href="{{ route('admin.talents.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-blue-100 text-sm transition-all {{ request()->routeIs('admin.talents.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>{{ __('nav.graduan') }}</span>
            </a>
            @endif

            {{-- Implementing Company --}}
            @if(!in_array($role, ['rakan_kolaborasi']))
            <a href="{{ route('admin.syarikat-pelaksana.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-blue-100 text-sm transition-all {{ request()->routeIs('admin.syarikat-pelaksana.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span>{{ __('nav.syarikat_pelaksana') }}</span>
            </a>
            @endif

            {{-- Placement Company --}}
            @if(!in_array($role, ['syarikat_pelaksana']))
            <a href="{{ route('admin.syarikat-penempatan.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-blue-100 text-sm transition-all {{ request()->routeIs('admin.syarikat-penempatan.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                </svg>
                <span>{{ __('nav.syarikat_penempatan') }}</span>
            </a>
            @endif

            {{-- Manage Placement --}}
            @if(in_array($role, ['super_admin', 'pmo_admin', 'syarikat_pelaksana', 'rakan_kolaborasi']))
            <a href="{{ route('admin.manage-placement.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-blue-100 text-sm transition-all {{ request()->routeIs('admin.manage-placement.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <span>{{ __('nav.manage_placement') }}</span>
            </a>
            @endif

            {{-- Applicant Request --}}
            @if(in_array($role, ['super_admin', 'pmo_admin', 'mindef_viewer', 'syarikat_pelaksana', 'rakan_kolaborasi']))
            <a href="{{ route('admin.applicant-requests.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-blue-100 text-sm transition-all {{ request()->routeIs('admin.applicant-requests.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h8M8 14h5m-1 7l-6-6V5a2 2 0 012-2h8a2 2 0 012 2v10l-6 6z"/>
                </svg>
                <span>Applicant Request</span>
            </a>
            @endif

            {{-- ═══════════════════════════════ MONITORING ═══════════════════════════════ --}}
            <div class="pt-2 pb-1">
                <div class="px-3 text-xs font-semibold text-blue-400 uppercase tracking-wider">{{ __('nav.monitoring') }}</div>
            </div>

            {{-- Attendance & Performance --}}
            @if(!in_array($role, ['syarikat_pelaksana']))
            <a href="{{ route('admin.kehadiran.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-blue-100 text-sm transition-all {{ request()->routeIs('admin.kehadiran.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>{{ __('nav.kehadiran') }}</span>
            </a>
            @endif

            {{-- Daily Logs --}}
            @if(in_array($role, ['super_admin', 'pmo_admin', 'mindef_viewer']))
            <a href="{{ route('admin.daily-logs.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-blue-100 text-sm transition-all {{ request()->routeIs('admin.daily-logs.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>{{ __('nav.daily_logs') }}</span>
            </a>
            @endif

            {{-- Logbook --}}
            <a href="{{ route('admin.logbook.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-blue-100 text-sm transition-all {{ request()->routeIs('admin.logbook.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span>{{ __('nav.logbook') }}</span>
            </a>

            {{-- Training --}}
            @if(!in_array($role, ['syarikat_pelaksana']))
            <a href="{{ route('admin.training.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-blue-100 text-sm transition-all {{ request()->routeIs('admin.training.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
                <span>{{ __('nav.training') }}</span>
            </a>
            @endif

            {{-- Issues & Risk --}}
            @if(!in_array($role, ['rakan_kolaborasi', 'syarikat_pelaksana']))
            <a href="{{ route('admin.isu-risiko.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-blue-100 text-sm transition-all {{ request()->routeIs('admin.isu-risiko.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>{{ __('nav.isu_risiko') }}</span>
            </a>
            @endif

            {{-- Letter Status --}}
            @if(!in_array($role, ['rakan_kolaborasi']))
            <a href="{{ route('admin.status-surat.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-blue-100 text-sm transition-all {{ request()->routeIs('admin.status-surat.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span>{{ __('nav.status_surat') }}</span>
            </a>
            @endif

            {{-- ═══════════════════════════════ FINANCE ═══════════════════════════════ --}}
            @if(!in_array($role, ['rakan_kolaborasi']))
            <div class="pt-2 pb-1">
                <div class="px-3 text-xs font-semibold text-blue-400 uppercase tracking-wider">{{ __('nav.finance') }}</div>
            </div>

            {{-- Allowance Payment --}}
            <a href="{{ route('admin.kewangan.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-blue-100 text-sm transition-all {{ request()->routeIs('admin.kewangan.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ __('nav.kewangan') }}</span>
            </a>

            {{-- Budget & Expenditure --}}
            @if(in_array($role, ['super_admin', 'pmo_admin', 'mindef_viewer']))
            <a href="{{ route('admin.budget.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-blue-100 text-sm transition-all {{ request()->routeIs('admin.budget.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3v-6m-3 6v-1m9-9V3M9 3v2M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>{{ __('nav.budget') }}</span>
            </a>
            @endif
            @endif

            {{-- ═══════════════════════════════ ANALYTICS & REPORTING ═══════════════════════════════ --}}
            @if(in_array($role, ['super_admin', 'pmo_admin', 'mindef_viewer']))
            <div class="pt-2 pb-1">
                <div class="px-3 text-xs font-semibold text-blue-400 uppercase tracking-wider">{{ __('nav.analytics_reporting') }}</div>
            </div>

            {{-- KPI Dashboard --}}
            @if(in_array($role, ['super_admin', 'pmo_admin', 'mindef_viewer']))
            <a href="{{ route('admin.kpi-dashboard.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-blue-100 text-sm transition-all {{ request()->routeIs('admin.kpi-dashboard.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>{{ __('nav.kpi_dashboard') }}</span>
            </a>
            @endif

            {{-- Reports --}}
            @if(in_array($role, ['super_admin', 'pmo_admin', 'mindef_viewer']))
            <a href="{{ route('admin.reports.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-blue-100 text-sm transition-all {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>{{ __('nav.reports') }}</span>
            </a>
            @endif

            {{-- Feedback --}}
            <a href="{{ route('admin.feedback.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-blue-100 text-sm transition-all {{ request()->routeIs('admin.feedback.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
                <span>{{ __('nav.feedback') }}</span>
            </a>
            @endif {{-- end analytics section --}}

            {{-- ═══════════════════════════════ OTHERS ═══════════════════════════════ --}}
            <div class="pt-2 pb-1">
                <div class="px-3 text-xs font-semibold text-blue-400 uppercase tracking-wider">{{ __('nav.others') }}</div>
            </div>

            {{-- System Guide --}}
            <a href="{{ route('admin.system-guide.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-blue-100 text-sm transition-all {{ request()->routeIs('admin.system-guide.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ __('nav.system_guide') }}</span>
            </a>

            {{-- Talent Search Portal --}}
            @if($role === 'rakan_kolaborasi')
            <a href="{{ route('portal.index') }}" target="_blank"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-blue-100 text-sm transition-all">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/>
                </svg>
                <span>{{ __('nav.portal') }}</span>
                <svg class="w-3 h-3 ml-auto opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </a>
            @endif

            {{-- Settings --}}
            @if(in_array($role, ['super_admin', 'pmo_admin']))
            <a href="{{ route('admin.settings.users') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-blue-100 text-sm transition-all {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>{{ __('nav.settings') }}</span>
            </a>
            @endif
        </nav>

        <!-- Footer -->
        <div class="p-4 border-t border-white/10">
            <p class="text-center text-blue-300/50 text-[9px] mb-2">{{ __('common.powered_by') }}</p>
            <div class="flex justify-center">
                <img src="{{ asset('images/WESB-Logo-bcground-wh.png') }}" alt="Weststar Engineering Sdn Bhd" class="h-7 w-auto opacity-80">
            </div>
        </div>
    </div>

    <!-- Overlay for mobile -->
    <div class="fixed inset-0 bg-black/50 z-40 lg:hidden"
         x-show="sidebarOpen"
         @click="sidebarOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;"></div>

    <!-- Main content -->
    <div class="flex-1 flex flex-col min-h-screen lg:ml-64">

        <!-- Top navbar -->
        <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-30">
            <div class="flex items-center justify-between h-16 px-4 lg:px-6">

                <!-- Mobile menu button -->
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-md text-gray-500 hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <!-- Page title -->
                <div class="flex-1 lg:flex-none">
                    <h1 class="text-gray-700 font-semibold text-base lg:text-lg">@yield('page-title', __('nav.dashboard'))</h1>
                </div>

                <!-- Right section -->
                <div class="flex items-center gap-3">
                    <!-- Language toggle -->
                    <div class="flex items-center bg-gray-100 rounded-lg p-1 gap-1">
                        <form method="POST" action="{{ route('language.set') }}">
                            @csrf
                            <input type="hidden" name="lang" value="ms">
                            <button type="submit" class="px-2.5 py-1 rounded-md text-xs font-medium transition-all {{ app()->getLocale() === 'ms' ? 'bg-[#1E3A5F] text-white' : 'text-gray-500 hover:text-gray-700' }}">BM</button>
                        </form>
                        <form method="POST" action="{{ route('language.set') }}">
                            @csrf
                            <input type="hidden" name="lang" value="en">
                            <button type="submit" class="px-2.5 py-1 rounded-md text-xs font-medium transition-all {{ app()->getLocale() === 'en' ? 'bg-[#1E3A5F] text-white' : 'text-gray-500 hover:text-gray-700' }}">EN</button>
                        </form>
                    </div>

                    <!-- User dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-[#1E3A5F] flex items-center justify-center text-white text-sm font-bold">
                                {{ substr(auth()->user()->full_name, 0, 1) }}
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
                                <div class="text-xs text-gray-500">{{ auth()->user()->email }}</div>
                            </div>
                            <a href="{{ route('admin.profile.show') }}"
                               class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ __('nav.profile') }}
                            </a>
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

        <!-- Page content -->
        <main class="flex-1 p-4 lg:p-6">
            <!-- Flash messages -->
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

            @if(isset($errors) && $errors->any())
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

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 px-6 py-3">
            <p class="text-xs text-gray-400 text-center">
                &copy; {{ date('Y') }} {{ __('common.protege_programme') }} — {{ __('common.app_full_name') }}. {{ __('common.all_rights_reserved') }} {{ __('common.powered_by_weststar') }}
            </p>
        </footer>
    </div>
</div>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('input[data-numeric="integer"]').forEach((input) => {
        input.addEventListener('input', () => {
            input.value = input.value.replace(/[^\d]/g, '');
        });
    });
});
</script>
@stack('scripts')
</body>
</html>
