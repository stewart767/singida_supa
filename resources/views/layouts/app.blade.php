<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidebarOpen: false, userDropdown: false, recruitmentOpen: {{ request()->routeIs('admin.recruitment.*') ? 'true' : 'false' }} }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Portal - SUPA Admission Management System' }}</title>

    <!-- Dynamic Favicon -->
    @if(\App\Models\Setting::get('system_logo'))
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . \App\Models\Setting::get('system_logo')) }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>[x-cloak] { display: none !important; }</style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex selection:bg-amber-500 selection:text-slate-900">

    <!-- Page Loader Overlay -->
    <div id="page-loader" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-slate-900/95 backdrop-blur-2xl transition-all duration-500 ease-out">
        <div class="relative flex flex-col items-center space-y-6">
            <!-- Loader Animated Rings -->
            <div class="relative flex items-center justify-center w-36 h-24">
                <!-- Glowing background pulse -->
                <div class="absolute inset-0 rounded-full bg-amber-500/20 blur-xl animate-pulse"></div>
                <!-- Outer spinning orbit line -->
                <div class="absolute inset-0 rounded-full border-2 border-transparent border-t-amber-500 border-b-blue-500 animate-spin" style="animation-duration: 1.5s;"></div>
                <!-- Inner logo wrappers -->
                <div class="flex items-center space-x-2 animate-pulse relative z-10">
                    <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 flex items-center justify-center shadow-lg p-1">
                        @if(\App\Models\Setting::get('system_logo'))
                            <img src="{{ asset('storage/' . \App\Models\Setting::get('system_logo')) }}" alt="System Logo" class="w-full h-full object-contain">
                        @elseif(\App\Models\Setting::get('sttc_logo'))
                            <img src="{{ asset('storage/' . \App\Models\Setting::get('sttc_logo')) }}" alt="STTC Logo" class="w-full h-full object-contain">
                        @else
                            <span class="text-slate-900 font-extrabold text-xs">STTC</span>
                        @endif
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 flex items-center justify-center shadow-lg p-1">
                        @if(\App\Models\Setting::get('out_logo'))
                            <img src="{{ asset('storage/' . \App\Models\Setting::get('out_logo')) }}" alt="OUT Logo" class="w-full h-full object-contain">
                        @else
                            <span class="text-blue-900 font-extrabold text-xs">OUT</span>
                        @endif
                    </div>
                </div>
            </div>
            <!-- Progress Text -->
            <div class="flex flex-col items-center space-y-1 text-center">
                <span class="text-xs font-black tracking-widest text-white uppercase mt-2">Loading</span>
                <div class="flex space-x-1 justify-center items-center">
                    <div class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                    <div class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    <div class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-bounce" style="animation-delay: 0.3s"></div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        (function() {
            function dismissLoader() {
                const loader = document.getElementById('page-loader');
                if (loader && !loader.dataset.dismissed) {
                    loader.dataset.dismissed = 'true';
                    loader.classList.add('opacity-0', 'pointer-events-none');
                    setTimeout(() => {
                        try { loader.remove(); } catch(e) {}
                    }, 500);
                }
            }
            if (document.readyState === 'complete') {
                dismissLoader();
            } else {
                window.addEventListener('load', dismissLoader);
                document.addEventListener('DOMContentLoaded', () => setTimeout(dismissLoader, 300));
                setTimeout(dismissLoader, 1200); // Safety fallback for slow network/assets
            }
        })();
    </script>

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-white/40 backdrop-blur-sm z-40 lg:hidden" x-cloak></div>

    <!-- Sidebar Navigation -->
    <aside class="fixed lg:sticky lg:top-0 inset-y-0 left-0 z-50 w-72 h-screen bg-white text-slate-600 flex flex-col transform transition-transform duration-300 border-r border-slate-200 overflow-hidden"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
        
        <!-- Sidebar Brand Header -->
        <div class="h-20 flex items-center justify-between px-6 border-b border-slate-200 bg-white shrink-0">
            <div class="flex items-center space-x-3 overflow-hidden">
                <div class="flex items-center space-x-1.5 shrink-0">
                    <div class="w-9 h-9 rounded-xl bg-white border border-slate-200 flex items-center justify-center shadow-sm overflow-hidden p-0.5">
                        @if(\App\Models\Setting::get('system_logo'))
                            <img src="{{ asset('storage/' . \App\Models\Setting::get('system_logo')) }}" alt="System Logo" class="w-full h-full object-contain">
                        @elseif(\App\Models\Setting::get('sttc_logo'))
                            <img src="{{ asset('storage/' . \App\Models\Setting::get('sttc_logo')) }}" alt="STTC Logo" class="w-full h-full object-contain">
                        @else
                            <span class="text-amber-600 font-extrabold text-[10px]">STTC</span>
                        @endif
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-white border border-slate-200 flex items-center justify-center shadow-sm overflow-hidden p-0.5">
                        @if(\App\Models\Setting::get('out_logo'))
                            <img src="{{ asset('storage/' . \App\Models\Setting::get('out_logo')) }}" alt="OUT Logo" class="w-full h-full object-contain">
                        @else
                            <span class="text-blue-800 font-extrabold text-[10px]">OUT</span>
                        @endif
                    </div>
                </div>
                <div class="overflow-hidden">
                    <span class="text-xs font-extrabold text-slate-900 block tracking-tight truncate" title="{{ \App\Models\Setting::get('university_name', 'SUPA PORTAL') }}">{{ \App\Models\Setting::get('university_name', 'SUPA PORTAL') }}</span>
                    <span class="text-[10px] text-amber-500 font-extrabold uppercase tracking-wider block">
                        {{ auth()->user()->role ?? 'Portal User' }}
                    </span>
                </div>
            </div>
            <!-- Close Button for Mobile -->
            <button @click="sidebarOpen = false" class="lg:hidden p-1.5 rounded-xl bg-slate-100 text-slate-500 hover:text-slate-700 hover:bg-slate-200 transition-colors" title="Close Sidebar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Scrollable Navigation Area -->
        <div class="flex-grow overflow-y-auto sidebar-scrollbar min-h-0">
            <!-- Navigation Links Grid -->
            <nav class="p-4 space-y-4">
                @if(auth()->user()->isApplicant())
                    <div class="space-y-1">
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500 px-3">Applicant Portal</span>
                        <a href="{{ route('applicant.dashboard') }}" class="flex items-center px-4 py-3 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('applicant.dashboard') ? 'bg-blue-800 text-white' : 'hover:bg-blue-50 text-slate-600 hover:text-blue-800' }}">
                            <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Dashboard
                        </a>
                        <a href="{{ route('applicant.wizard') }}" class="flex items-center px-4 py-3 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('applicant.wizard') ? 'bg-blue-800 text-white' : 'hover:bg-blue-50 text-slate-600 hover:text-blue-800' }}">
                            <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Application
                        </a>
                        <a href="{{ route('applicant.privacy-consent') }}" class="flex items-center px-4 py-3 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('applicant.privacy-consent') ? 'bg-blue-800 text-white' : 'hover:bg-blue-50 text-slate-600 hover:text-blue-800' }}">
                            <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            My Privacy & Consent
                        </a>
                    </div>
                @else
                    <!-- EXECUTIVE OVERVIEW -->
                    <div class="space-y-1">
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500 px-3">Overview</span>
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-blue-800 text-white' : 'hover:bg-blue-50 text-slate-600 hover:text-blue-800' }}">
                            <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            Executive Dashboard
                        </a>
                    </div>

                    <!-- ADMISSION & APPLICANTS -->
                    <div class="space-y-1">
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500 px-3">Admissions & Finance</span>
                        <a href="{{ route('admin.applications.index') }}" class="flex items-center px-4 py-2.5 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.applications.*') && request()->get('view') !== 'applicants' ? 'bg-blue-800 text-white' : 'hover:bg-blue-50 text-slate-600 hover:text-blue-800' }}">
                            <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Admissions Desk
                        </a>
                        <a href="{{ route('admin.applications.index') }}?view=applicants" class="flex items-center px-4 py-2.5 rounded-lg text-xs font-bold transition-all {{ request()->get('view') === 'applicants' ? 'bg-blue-800 text-white' : 'hover:bg-blue-50 text-slate-600 hover:text-blue-800' }}">
                            <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            Applicants Directory
                        </a>
                        <a href="{{ route('admin.payments.index') }}" class="flex items-center px-4 py-2.5 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.payments.*') ? 'bg-blue-800 text-white' : 'hover:bg-blue-50 text-slate-600 hover:text-blue-800' }}">
                            <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Finance & Control Numbers
                        </a>
                    </div>

                    <!-- RECRUITMENT & ATS -->
                    @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole(['hr_director', 'hr_manager', 'hr_officer', 'interview_panel', 'designation_head']))
                    <div class="space-y-1">
                        <button @click="recruitmentOpen = !recruitmentOpen" class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg text-xs font-bold transition-all hover:bg-blue-50 text-slate-600 hover:text-blue-800">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-3 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                Recruitment & ATS
                            </span>
                            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': recruitmentOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        
                        <div x-show="recruitmentOpen" x-transition class="pl-4 space-y-1" x-cloak>
                            <a href="{{ route('admin.recruitment.dashboard') }}" class="flex items-center px-4 py-2 rounded-lg text-[11px] font-bold transition-all {{ request()->routeIs('admin.recruitment.dashboard') ? 'bg-blue-800 text-white shadow-sm' : 'hover:bg-blue-50 text-slate-600 hover:text-blue-800' }}">Dashboard</a>
                            <a href="{{ route('admin.recruitment.applications.index') }}" class="flex items-center justify-between px-4 py-2 rounded-lg text-[11px] font-bold transition-all {{ request()->routeIs('admin.recruitment.applications.*') ? 'bg-blue-800 text-white shadow-sm' : 'hover:bg-blue-50 text-slate-600 hover:text-blue-800' }}">
                                <span class="flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Manage Applications
                                </span>
                                @php
                                    $pendingRecCount = \App\Models\JobApplication::whereIn('status', ['Applied', 'Under Review'])->count();
                                @endphp
                                @if($pendingRecCount > 0)
                                    <span class="bg-amber-500 text-white text-[9px] font-black px-1.5 py-0.5 rounded-full">{{ $pendingRecCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('admin.recruitment.ats') }}" class="flex items-center px-4 py-2 rounded-lg text-[11px] font-bold transition-all {{ request()->routeIs('admin.recruitment.ats') ? 'bg-blue-800 text-white shadow-sm' : 'hover:bg-blue-50 text-slate-600 hover:text-blue-800' }}">ATS Kanban Pipeline</a>
                            <a href="{{ route('admin.recruitment.vacancies') }}" class="flex items-center px-4 py-2 rounded-lg text-[11px] font-bold transition-all {{ request()->routeIs('admin.recruitment.vacancies') ? 'bg-blue-800 text-white shadow-sm' : 'hover:bg-blue-50 text-slate-600 hover:text-blue-800' }}">Vacancies</a>
                            <a href="{{ route('admin.recruitment.written-tests') }}" class="flex items-center px-4 py-2 rounded-lg text-[11px] font-bold transition-all {{ request()->routeIs('admin.recruitment.written-tests') ? 'bg-blue-800 text-white shadow-sm' : 'hover:bg-blue-50 text-slate-600 hover:text-blue-800' }}">Written Tests</a>
                            <a href="{{ route('admin.recruitment.interviews') }}" class="flex items-center px-4 py-2 rounded-lg text-[11px] font-bold transition-all {{ request()->routeIs('admin.recruitment.interviews') ? 'bg-blue-800 text-white shadow-sm' : 'hover:bg-blue-50 text-slate-600 hover:text-blue-800' }}">Interview Schedule</a>
                            <a href="{{ route('admin.recruitment.scores') }}" class="flex items-center px-4 py-2 rounded-lg text-[11px] font-bold transition-all {{ request()->routeIs('admin.recruitment.scores') ? 'bg-blue-800 text-white shadow-sm' : 'hover:bg-blue-50 text-slate-600 hover:text-blue-800' }}">Scorecards</a>
                            <a href="{{ route('admin.recruitment.evaluations') }}" class="flex items-center px-4 py-2 rounded-lg text-[11px] font-bold transition-all {{ request()->routeIs('admin.recruitment.evaluations') ? 'bg-blue-800 text-white shadow-sm' : 'hover:bg-blue-50 text-slate-600 hover:text-blue-800' }}">Final Evaluations</a>
                            <a href="{{ route('admin.recruitment.offer-letters') }}" class="flex items-center px-4 py-2 rounded-lg text-[11px] font-bold transition-all {{ request()->routeIs('admin.recruitment.offer-letters') ? 'bg-blue-800 text-white shadow-sm' : 'hover:bg-blue-50 text-slate-600 hover:text-blue-800' }}">Offer Letters</a>
                            <a href="{{ route('admin.recruitment.talent-pool') }}" class="flex items-center px-4 py-2 rounded-lg text-[11px] font-bold transition-all {{ request()->routeIs('admin.recruitment.talent-pool') ? 'bg-blue-800 text-white shadow-sm' : 'hover:bg-blue-50 text-slate-600 hover:text-blue-800' }}">Talent Pool</a>
                            <a href="{{ route('admin.recruitment.positions') }}" class="flex items-center px-4 py-2 rounded-lg text-[11px] font-bold transition-all {{ request()->routeIs('admin.recruitment.positions') ? 'bg-blue-800 text-white shadow-sm' : 'hover:bg-blue-50 text-slate-600 hover:text-blue-800' }}">Positions</a>
                            <a href="{{ route('admin.recruitment.designations') }}" class="flex items-center px-4 py-2 rounded-lg text-[11px] font-bold transition-all {{ request()->routeIs('admin.recruitment.designations') ? 'bg-blue-800 text-white shadow-sm' : 'hover:bg-blue-50 text-slate-600 hover:text-blue-800' }}">Designations</a>
                            <a href="{{ route('admin.recruitment.categories') }}" class="flex items-center px-4 py-2 rounded-lg text-[11px] font-bold transition-all {{ request()->routeIs('admin.recruitment.categories') ? 'bg-blue-800 text-white shadow-sm' : 'hover:bg-blue-50 text-slate-600 hover:text-blue-800' }}">Job Categories</a>
                            <a href="{{ route('admin.recruitment.reports') }}" class="flex items-center px-4 py-2 rounded-lg text-[11px] font-bold transition-all {{ request()->routeIs('admin.recruitment.reports') ? 'bg-blue-800 text-white shadow-sm' : 'hover:bg-blue-50 text-slate-600 hover:text-blue-800' }}">Reports & Analytics</a>
                            <a href="{{ route('admin.recruitment.settings') }}" class="flex items-center px-4 py-2 rounded-lg text-[11px] font-bold transition-all {{ request()->routeIs('admin.recruitment.settings') ? 'bg-blue-800 text-white shadow-sm' : 'hover:bg-blue-50 text-slate-600 hover:text-blue-800' }}">Settings</a>
                        </div>
                    </div>
                    @endif

                    <!-- ACADEMICS & CONTENT -->
                    <div class="space-y-1">
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500 px-3">Academics & CMS</span>
                        <a href="{{ route('admin.programmes.index') }}" class="flex items-center px-4 py-2.5 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.programmes.*') ? 'bg-blue-800 text-white shadow-sm' : 'hover:bg-blue-50 text-slate-600 hover:text-blue-800' }}">
                            <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            Academics Catalog
                        </a>
                        <a href="{{ route('admin.cms.index') }}" class="flex items-center px-4 py-2.5 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.cms.*') ? 'bg-blue-800 text-white shadow-sm' : 'hover:bg-blue-50 text-slate-600 hover:text-blue-800' }}">
                            <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                            Website CMS & Media
                        </a>
                    </div>

                    <!-- SYSTEM & REPORTS -->
                    <div class="space-y-1">
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500 px-3">System Administration</span>
                        <a href="{{ route('admin.cms.index') }}#users" class="flex items-center px-4 py-2.5 rounded-lg text-xs font-bold transition-all hover:bg-blue-50 text-slate-600 hover:text-blue-800">
                            <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Users & Roles
                        </a>
                        <a href="{{ route('admin.compliance.index') }}" class="flex items-center px-4 py-2.5 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.compliance.*') ? 'bg-blue-800 text-white shadow-sm' : 'hover:bg-blue-50 text-slate-600 hover:text-blue-800' }}">
                            <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Data Privacy Compliance
                        </a>
                        <a href="{{ route('admin.cms.index') }}#reports" class="flex items-center px-4 py-2.5 rounded-lg text-xs font-bold transition-all hover:bg-blue-50 text-slate-600 hover:text-blue-800">
                            <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Reports & Analytics
                        </a>
                        <a href="{{ route('admin.cms.index') }}#logs" class="flex items-center px-4 py-2.5 rounded-lg text-xs font-bold transition-all hover:bg-blue-50 text-slate-600 hover:text-blue-800">
                            <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Audit Logs & Settings
                        </a>
                    </div>
                @endif

                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2.5 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('profile.edit') ? 'bg-blue-800 text-white shadow-sm' : 'hover:bg-blue-50 text-slate-600 hover:text-blue-800' }}">
                    <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Manage Profile
                </a>

                <a href="{{ route('home') }}" class="flex items-center px-4 py-2.5 rounded-lg text-xs font-bold hover:bg-blue-50 text-slate-600 hover:text-blue-800 transition-all mt-4 border border-slate-200">
                    <svg class="w-4 h-4 mr-3 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Public Website
                </a>
            </nav>
        </div>

        <!-- Sidebar User Footer -->
        <div class="p-4 border-t border-slate-200 space-y-3 bg-white shrink-0">
            <div class="flex items-center space-x-3 p-2 rounded-2xl bg-white border border-slate-200">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-800 text-white font-extrabold flex items-center justify-center text-sm shadow-md shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="truncate max-w-[130px]">
                    <span class="text-xs font-extrabold text-slate-900 block truncate">{{ auth()->user()->name ?? 'User' }}</span>
                    <span class="text-[10px] text-slate-500 block truncate">{{ auth()->user()->email ?? '' }}</span>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center px-4 py-2.5 rounded-2xl text-xs font-bold bg-red-50 hover:bg-red-600 text-red-600 hover:text-white border border-red-200 hover:border-transparent transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
        
        <!-- Portal Header Bar -->
        <header class="h-20 bg-white/90 backdrop-blur border-b border-slate-200 flex items-center justify-between px-6 shadow-sm sticky top-0 z-30">
            <div class="flex items-center space-x-4">
                <button @click="sidebarOpen = true" class="lg:hidden p-2.5 rounded-xl text-slate-600 hover:bg-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-lg font-extrabold text-slate-900 tracking-tight">{{ $header ?? 'Portal' }}</h1>
            </div>

            <div class="flex items-center space-x-4">
                <!-- Profile Dropdown -->
                <div class="relative" @click.away="userDropdown = false">
                    <button @click="userDropdown = !userDropdown" class="flex items-center space-x-2.5 p-1.5 rounded-2xl hover:bg-slate-100 transition-colors">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-500 to-blue-900 text-white font-extrabold flex items-center justify-center text-sm shadow-md">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <span class="text-xs font-extrabold text-slate-800 hidden sm:inline-block">{{ auth()->user()->name }}</span>
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="userDropdown" x-cloak class="absolute right-0 mt-2 w-52 bg-white rounded-3xl shadow-2xl border border-slate-200 py-2 z-50">
                        <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-100">
                            <svg class="w-4 h-4 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Manage Profile
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="border-t border-slate-100 mt-1 pt-1">
                            @csrf
                            <button type="submit" class="w-full flex items-center px-4 py-2.5 text-xs font-bold text-red-600 hover:bg-red-50 text-left">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <main class="p-6 sm:p-8 flex-1">
            {{ $slot }}
        </main>

        <!-- Portal Footer with Developer Credit -->
        <footer class="px-8 py-4 border-t border-slate-200 text-xs text-slate-500 flex flex-col sm:flex-row justify-between items-center gap-2">
            <p>{{ \App\Models\Setting::get('footer_copyright', '© ' . date('Y') . ' SUPA / OUT University Admission Management System. All rights reserved.') }}</p>
            <p class="text-[11px] text-slate-500">
                Developed by 
                <a href="{{ \App\Models\Setting::get('developer_url', 'http://www.reliancesolutions.co.tz') }}" target="_blank" rel="noopener noreferrer" class="text-amber-500 hover:underline font-extrabold">
                    {{ \App\Models\Setting::get('developer_name', 'Reliance Solutions & Technology') }}
                </a>
                <span class="text-slate-500">({{ str_replace(['http://', 'https://'], '', \App\Models\Setting::get('developer_url', 'www.reliancesolutions.co.tz')) }})</span>
            </p>
        </footer>
    </div>

    <!-- Toast Notification Overlay Container -->
    <div x-data="{ toasts: [] }" 
         @toast.window="toasts.push({ id: Date.now(), message: $event.detail.message, type: $event.detail.type }); setTimeout(() => { toasts.shift() }, 4000)"
         class="fixed bottom-6 right-6 z-50 space-y-3 pointer-events-none">
         <template x-for="t in toasts" :key="t.id">
            <div class="pointer-events-auto flex items-center space-x-3 px-5 py-4 rounded-2xl shadow-2xl text-white font-bold text-xs transition-all transform translate-y-0"
                 :class="t.type === 'error' ? 'bg-red-600' : 'bg-emerald-600'">
                <span x-text="t.message"></span>
            </div>
        </template>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.axios) {
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = '{{ csrf_token() }}';
            }
        });
    </script>
</body>
</html>
