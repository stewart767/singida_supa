<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'SUPA / OUT - Online University Admission Management System' }}</title>

    <!-- Dynamic Favicon -->
    @if(\App\Models\Setting::get('system_logo'))
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . \App\Models\Setting::get('system_logo')) }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    <!-- Google Fonts: Ubuntu -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-white text-slate-800 antialiased min-h-screen flex flex-col relative selection:bg-amber-500 selection:text-slate-900"
      x-data="{ 
          scrolled: false, 
          searchModal: false, 
          mobileMenu: false, 
          programmeMenu: false, 
          admissionMenu: false 
      }" 
      @scroll.window="scrolled = window.scrollY > 30">

    <!-- Top Announcement Bar -->
    <div class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-900 text-white text-xs font-semibold py-2.5 px-4 border-b border-blue-950 z-50">
        <div class="max-w-7xl mx-auto flex flex-wrap justify-between items-center px-4 sm:px-6 lg:px-8">
            <div class="flex items-center space-x-3">
                <span class="bg-amber-500 text-slate-950 px-2.5 py-0.5 rounded-full text-[10px] uppercase font-extrabold tracking-wider animate-pulse">{{ \App\Models\Setting::get('top_announcement_badge', '2026/2027') }}</span>
                <span class="text-white">{{ \App\Models\Setting::get('top_announcement_text', 'Online Admissions Now Open for Undergraduate & Postgraduate Programmes') }}</span>
            </div>
            <div class="hidden sm:flex items-center space-x-6">
                <a href="{{ \App\Models\Setting::get('top_announcement_link_url') ?: route('public.track') }}" class="hover:text-amber-400 transition-colors flex items-center gap-1.5 font-medium">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    {{ \App\Models\Setting::get('top_announcement_link_text', 'Track Application Status') }}
                </a>
                <span class="text-blue-300">|</span>
                @php
                    $topPhone = \App\Models\Setting::get('top_announcement_phone', '+255 22 266 8820');
                    $cleanPhone = preg_replace('/[^0-9+]/', '', $topPhone);
                @endphp
                <a href="tel:{{ $cleanPhone }}" class="hover:text-amber-400 transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    {{ $topPhone }}
                </a>
            </div>
        </div>
    </div>

    <!-- Floating Header Navigation -->
    <header class="sticky top-0 z-40 transition-all duration-300 px-4 sm:px-6 lg:px-8 py-3"
            :class="scrolled ? 'py-2 bg-white/95 backdrop-blur-xl border-b border-slate-200 shadow-md' : 'py-4 bg-white/90 backdrop-blur-lg border-b border-slate-200/60'">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            
            <!-- University Logo Area -->
            <a href="{{ route('home') }}" class="flex items-center space-x-3.5 group">
                <div class="flex items-center space-x-2 shrink-0">
                    <div class="w-11 h-11 rounded-2xl bg-white border border-slate-200 flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform duration-300 overflow-hidden p-1">
                        @if(\App\Models\Setting::get('system_logo'))
                            <img src="{{ asset('storage/' . \App\Models\Setting::get('system_logo')) }}" alt="System Logo" class="w-full h-full object-contain">
                        @elseif(\App\Models\Setting::get('sttc_logo'))
                            <img src="{{ asset('storage/' . \App\Models\Setting::get('sttc_logo')) }}" alt="STTC Logo" class="w-full h-full object-contain">
                        @else
                            <span class="text-amber-600 font-extrabold text-[10px]">STTC</span>
                        @endif
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-white border border-slate-200 flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform duration-300 overflow-hidden p-1">
                        @if(\App\Models\Setting::get('out_logo'))
                            <img src="{{ asset('storage/' . \App\Models\Setting::get('out_logo')) }}" alt="OUT Logo" class="w-full h-full object-contain">
                        @else
                            <span class="text-blue-800 font-extrabold text-[10px]">OUT</span>
                        @endif
                    </div>
                </div>
                <div>
                    <span class="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 block leading-none">
                        {{ \App\Models\Setting::get('university_name', "SINGIDA TEACHERS' TRAINING COLLEGE (STTC) & OUT") }}
                    </span>
                    <span class="text-[10px] text-blue-800 tracking-widest uppercase block font-bold mt-1">
                        Admission Management System
                    </span>
                </div>
            </a>

            <!-- Desktop Navigation Menu -->
            <nav class="hidden lg:flex items-center space-x-7 text-sm font-semibold text-slate-600">
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'text-blue-800 active' : 'hover:text-blue-800' }}">Home</a>
                
                <!-- Mega Menu: Programmes -->
                <div class="relative" @mouseenter="programmeMenu = true" @mouseleave="programmeMenu = false">
                    <button class="nav-link flex items-center gap-1.5 py-2 {{ request()->routeIs('public.programmes') ? 'text-blue-800 active' : 'hover:text-blue-800' }}">
                        <span>Programmes</span>
                        <svg class="w-4 h-4 text-slate-500 transition-transform" :class="{ 'rotate-180': programmeMenu }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    
                    <div x-show="programmeMenu" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-2"
                         class="absolute left-0 mt-1 w-80 bg-white/95 backdrop-blur-2xl border border-slate-200 rounded-3xl shadow-2xl p-4 z-50 text-xs" x-cloak>
                        @php
                            $navCatalogTitle = \App\Models\Setting::get('catalog_title', 'Academic Catalog');
                            $navCatalogSubtitle = \App\Models\Setting::get('catalog_subtitle', 'Explore Degrees & Diplomas');
                            $defaultCategories = [
                                ['code' => 'UG', 'title' => 'Undergraduate Programmes', 'subtitle' => 'Bachelor of Science, Education, Commerce', 'color' => 'blue'],
                                ['code' => 'PG', 'title' => 'Postgraduate Degrees', 'subtitle' => "Master's & Postgraduate Diplomas", 'color' => 'amber'],
                                ['code' => 'FC', 'title' => 'Foundation Courses', 'subtitle' => 'Bridging courses for Direct Entry', 'color' => 'emerald'],
                            ];
                            $navCategories = \App\Models\Setting::get('programme_categories', $defaultCategories);
                        @endphp
                        <div class="p-3 rounded-2xl bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 mb-2">
                            <span class="text-[10px] uppercase font-bold text-amber-600 tracking-wider">{{ $navCatalogTitle }}</span>
                            <h4 class="font-extrabold text-slate-900 text-sm mt-0.5">{{ $navCatalogSubtitle }}</h4>
                        </div>
                        @foreach($navCategories as $cat)
                            @if(isset($cat['is_active']) && !$cat['is_active'])
                                @continue
                            @endif
                            @php
                                $color = $cat['color'] ?? 'blue';
                                $badgeClass = match($color) {
                                    'amber' => 'bg-amber-50 text-amber-600 border border-amber-200',
                                    'emerald' => 'bg-emerald-50 text-emerald-600 border border-emerald-200',
                                    'purple' => 'bg-purple-50 text-purple-600 border border-purple-200',
                                    'rose', 'red' => 'bg-rose-50 text-rose-600 border border-rose-200',
                                    default => 'bg-blue-50 text-blue-600 border border-blue-200',
                                };
                            @endphp
                            <a href="{{ route('public.programmes') }}" class="flex items-start space-x-3 p-3 rounded-xl hover:bg-slate-50 transition-colors">
                                <div class="w-8 h-8 rounded-lg {{ $badgeClass }} flex items-center justify-center font-bold text-xs shrink-0">{{ $cat['code'] }}</div>
                                <div>
                                    <span class="font-bold text-slate-900 block">{{ $cat['title'] }}</span>
                                    <span class="text-[11px] text-slate-500">{{ $cat['subtitle'] }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Mega Menu: Admissions -->
                <div class="relative" @mouseenter="admissionMenu = true" @mouseleave="admissionMenu = false">
                    <button class="nav-link flex items-center gap-1.5 py-2 {{ request()->routeIs('public.requirements') ? 'text-blue-800 active' : 'hover:text-blue-800' }}">
                        <span>Admissions</span>
                        <svg class="w-4 h-4 text-slate-500 transition-transform" :class="{ 'rotate-180': admissionMenu }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    
                    <div x-show="admissionMenu" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-2"
                         class="absolute left-0 mt-1 w-80 bg-white/95 backdrop-blur-2xl border border-slate-200 rounded-3xl shadow-2xl p-4 z-50 text-xs" x-cloak>
                        <a href="{{ route('public.requirements') }}" class="flex items-start space-x-3 p-3 rounded-xl hover:bg-slate-50 transition-colors">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xs shrink-0">1</div>
                            <div>
                                <span class="font-bold text-slate-900 block">Admission Requirements</span>
                                <span class="text-[11px] text-slate-500">Direct Entry GPA vs Foundation Points</span>
                            </div>
                        </a>
                        <a href="{{ route('public.track') }}" class="flex items-start space-x-3 p-3 rounded-xl hover:bg-slate-50 transition-colors">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs shrink-0">2</div>
                            <div>
                                <span class="font-bold text-slate-900 block">Track Application</span>
                                <span class="text-[11px] text-slate-500">Check control number & approval status</span>
                            </div>
                        </a>
                        <a href="{{ route('public.downloads') }}" class="flex items-start space-x-3 p-3 rounded-xl hover:bg-slate-50 transition-colors">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs shrink-0">3</div>
                            <div>
                                <span class="font-bold text-slate-900 block">Download Prospectus</span>
                                <span class="text-[11px] text-slate-500">PDF Guide & Admission Forms</span>
                            </div>
                        </a>
                    </div>
                </div>

                @if(\App\Models\Setting::get('show_news_announcements', true))
                    <a href="{{ route('public.news') }}" class="nav-link {{ request()->routeIs('public.news') ? 'text-blue-800 active' : 'hover:text-blue-800' }}">News</a>
                @endif
                <a href="{{ route('public.faqs') }}" class="nav-link {{ request()->routeIs('public.faqs') ? 'text-blue-800 active' : 'hover:text-blue-800' }}">FAQs</a>
                <a href="{{ route('public.careers.index') }}" class="nav-link {{ request()->routeIs('public.careers.*') ? 'text-blue-800 active' : 'hover:text-blue-800' }}">Careers</a>
                <a href="{{ route('public.contact') }}" class="nav-link {{ request()->routeIs('public.contact') ? 'text-blue-800 active' : 'hover:text-blue-800' }}">Contact</a>
            </nav>

            <!-- Navigation Actions & CTAs -->
            <div class="flex items-center space-x-3">
                
                <!-- Quick Search Trigger Button -->
                <button @click="searchModal = true" class="p-2.5 rounded-xl bg-slate-100 text-slate-600 hover:text-slate-900 hover:bg-slate-200 transition-colors" title="Search Site">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>

                @auth
                    @if(auth()->user()->isApplicant())
                        <a href="{{ route('applicant.dashboard') }}" class="gradient-btn px-4 py-2.5 rounded-2xl font-bold text-xs shadow-md flex items-center gap-1.5 hover:scale-105 transition-transform" title="Admission Portal Dashboard">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7"/></svg>
                            <span>Admission Portal</span>
                        </a>
                        <a href="{{ route('public.careers.dashboard') }}" class="gradient-btn-gold px-4 py-2.5 rounded-2xl font-bold text-xs shadow-md flex items-center gap-1.5 hover:scale-105 transition-transform" title="Careers Portal Dashboard">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4.674 1.255A23.985 23.985 0 0112 7c.896 0 1.778-.049 2.651-.145M12 12v17"/></svg>
                            <span>Careers Portal</span>
                        </a>
                    @else
                        <a href="{{ route('admin.dashboard') }}" class="gradient-btn px-5 py-2.5 rounded-2xl text-white font-bold text-xs shadow-lg hover:scale-105 transition-transform flex items-center gap-2">
                            <span>Admin Panel</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        <!-- Admission and Apply for Job Buttons -->
                        <a href="{{ route('public.programmes') }}" class="gradient-btn px-4 py-2.5 rounded-2xl font-bold text-xs shadow-md flex items-center gap-1.5 hover:scale-105 transition-transform" title="Admission - Academic Programmes">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7"/></svg>
                            <span>Admission</span>
                        </a>
                        <a href="{{ route('public.careers.index') }}" class="gradient-btn-gold px-4 py-2.5 rounded-2xl font-bold text-xs shadow-md flex items-center gap-1.5 hover:scale-105 transition-transform" title="Apply for Job - Created Vacancies">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4.674 1.255A23.985 23.985 0 0112 7c.896 0 1.778-.049 2.651-.145M12 12v17"/></svg>
                            <span>Apply for Job</span>
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="hidden sm:inline-block text-xs font-bold text-slate-600 hover:text-slate-950 px-3 py-2">Sign In</a>
                    <!-- Admission and Apply for Job Buttons -->
                    <a href="{{ route('public.programmes') }}" class="gradient-btn px-4 py-2.5 rounded-2xl font-bold text-xs shadow-md flex items-center gap-1.5 hover:scale-105 transition-transform" title="Admission - Academic Programmes">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7"/></svg>
                        <span>Admission</span>
                    </a>
                    <a href="{{ route('public.careers.index') }}" class="gradient-btn-gold px-4 py-2.5 rounded-2xl font-bold text-xs shadow-md flex items-center gap-1.5 hover:scale-105 transition-transform" title="Apply for Job - Created Vacancies">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4.674 1.255A23.985 23.985 0 0112 7c.896 0 1.778-.049 2.651-.145M12 12v17"/></svg>
                        <span>Apply for Job</span>
                    </a>
                @endauth

                <!-- Mobile Hamburger Button -->
                <button @click="mobileMenu = true" class="lg:hidden p-2.5 rounded-xl bg-slate-100 text-slate-600 hover:text-slate-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>

        </div>
    </header>

    <!-- Mobile Slide-over Drawer -->
    <div x-show="mobileMenu" class="fixed inset-0 z-50 lg:hidden flex justify-end" x-cloak>
        <div @click="mobileMenu = false" class="fixed inset-0 bg-white/40 backdrop-blur-sm"></div>
        <div class="relative w-80 max-w-full bg-white text-slate-800 h-full p-6 shadow-2xl overflow-y-auto flex flex-col justify-between border-l border-slate-200 z-50">
            <div>
                <div class="flex justify-between items-center border-b border-slate-200 pb-4 mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500 text-white font-extrabold flex items-center justify-center text-lg">S</div>
                        <span class="font-extrabold text-slate-900 text-base">SUPA University</span>
                    </div>
                    <button @click="mobileMenu = false" class="p-2 text-slate-500 hover:text-slate-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <nav class="space-y-3 font-bold text-sm">
                    <a href="{{ route('home') }}" class="block p-3 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-900">Home</a>
                    <a href="{{ route('public.programmes') }}" class="block p-3 rounded-xl hover:bg-slate-50 text-slate-600">Academic Programmes</a>
                    <a href="{{ route('public.requirements') }}" class="block p-3 rounded-xl hover:bg-slate-50 text-slate-600">Admission Requirements</a>
                    <a href="{{ route('public.track') }}" class="block p-3 rounded-xl hover:bg-slate-50 text-slate-600">Track Application</a>
                    @if(\App\Models\Setting::get('show_news_announcements', true))
                        <a href="{{ route('public.news') }}" class="block p-3 rounded-xl hover:bg-slate-50 text-slate-600">News & Events</a>
                    @endif
                    <a href="{{ route('public.faqs') }}" class="block p-3 rounded-xl hover:bg-slate-50 text-slate-600">FAQs</a>
                    <a href="{{ route('public.careers.index') }}" class="block p-3 rounded-xl hover:bg-slate-50 text-slate-600">Careers</a>
                    <a href="{{ route('public.contact') }}" class="block p-3 rounded-xl hover:bg-slate-50 text-slate-600">Contact Office</a>
                </nav>
            </div>

            <div class="pt-6 border-t border-slate-200 space-y-3">
                @auth
                    @if(auth()->user()->isApplicant())
                        <a href="{{ route('applicant.dashboard') }}" class="block text-center w-full gradient-btn py-3 rounded-xl font-bold text-xs">
                            Admission Portal
                        </a>
                        <a href="{{ route('public.careers.dashboard') }}" class="block text-center w-full gradient-btn-gold py-3 rounded-xl font-bold text-xs">
                            Careers Portal
                        </a>
                    @else
                        <a href="{{ route('admin.dashboard') }}" class="block text-center w-full bg-slate-100 hover:bg-slate-200 py-3 rounded-xl font-bold text-xs text-slate-900 border border-slate-200">
                            Admin Panel
                        </a>
                        <a href="{{ route('public.programmes') }}" class="block text-center w-full gradient-btn py-3 rounded-xl font-bold text-xs">
                            Admission
                        </a>
                        <a href="{{ route('public.careers.index') }}" class="block text-center w-full gradient-btn-gold py-3 rounded-xl font-bold text-xs">
                            Apply for Job
                        </a>
                    @endif
                @else
                    <a href="{{ route('public.programmes') }}" class="block text-center w-full gradient-btn py-3 rounded-xl font-bold text-xs">
                        Admission
                    </a>
                    <a href="{{ route('public.careers.index') }}" class="block text-center w-full gradient-btn-gold py-3 rounded-xl font-bold text-xs">
                        Apply for Job
                    </a>
                    <a href="{{ route('login') }}" class="block text-center w-full bg-slate-100 hover:bg-slate-200 py-3 rounded-xl font-bold text-xs text-slate-900 border border-slate-200">
                        Sign In
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Quick Search Modal -->
    <div x-show="searchModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div @click="searchModal = false" class="fixed inset-0 bg-white/40 backdrop-blur-md"></div>
        <div class="relative bg-white border border-slate-200 max-w-xl w-full p-6 rounded-3xl shadow-2xl z-50 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="font-extrabold text-slate-900 text-base">Search University Portal</h3>
                <button @click="searchModal = false" class="text-slate-500 hover:text-slate-600">✕</button>
            </div>
            <div class="relative">
                <input type="text" placeholder="Type programme name, requirements, or application number..." 
                       class="w-full px-5 py-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex justify-between text-xs text-slate-500">
                <span>Quick links:</span>
                <div class="flex gap-2">
                    <a href="{{ route('public.programmes') }}" class="text-amber-600 hover:underline">Programmes</a>
                    <a href="{{ route('public.track') }}" class="text-blue-600 hover:underline">Track Application</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Dynamic Content Slot -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Global Toast Notification Toast Box -->
    <div x-data="{ toasts: [] }" 
         @toast.window="toasts.push({ id: Date.now(), message: $event.detail.message, type: $event.detail.type }); setTimeout(() => { toasts.shift() }, 4000)"
         class="fixed bottom-6 right-6 z-50 space-y-3 pointer-events-none">
        <template x-for="t in toasts" :key="t.id">
            <div class="pointer-events-auto flex items-center space-x-3 px-5 py-4 rounded-2xl shadow-2xl text-white font-bold text-xs transition-all transform translate-y-0"
                 :class="t.type === 'error' ? 'bg-red-600' : 'bg-emerald-600'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span x-text="t.message"></span>
            </div>
        </template>
    </div>

    <!-- Premium Institutional Footer -->
    <footer class="bg-slate-50 text-slate-600 border-t border-slate-200 text-sm relative overflow-hidden">
        <!-- Background Accent Decorative Glow -->
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-blue-100/40 blur-[120px] rounded-full pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
                
                <!-- University Brand Bio -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-3.5">
                        <div class="flex items-center space-x-2 shrink-0">
                            <div class="w-11 h-11 rounded-2xl bg-white border border-slate-200 flex items-center justify-center shadow-md overflow-hidden shrink-0 p-1">
                                @if(\App\Models\Setting::get('system_logo'))
                                    <img src="{{ asset('storage/' . \App\Models\Setting::get('system_logo')) }}" alt="System Logo" class="w-full h-full object-contain">
                                @elseif(\App\Models\Setting::get('sttc_logo'))
                                    <img src="{{ asset('storage/' . \App\Models\Setting::get('sttc_logo')) }}" alt="STTC Logo" class="w-full h-full object-contain">
                                @else
                                    <span class="text-amber-600 font-extrabold text-[10px]">STTC</span>
                                @endif
                            </div>
                            <div class="w-11 h-11 rounded-2xl bg-white border border-slate-200 flex items-center justify-center shadow-md overflow-hidden shrink-0 p-1">
                                @if(\App\Models\Setting::get('out_logo'))
                                    <img src="{{ asset('storage/' . \App\Models\Setting::get('out_logo')) }}" alt="OUT Logo" class="w-full h-full object-contain">
                                @else
                                    <span class="text-blue-800 font-extrabold text-[10px]">OUT</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <span class="text-xl font-black text-slate-900 tracking-tight block">{{ \App\Models\Setting::get('university_name', "SINGIDA TEACHERS' TRAINING COLLEGE (STTC) & OUT") }}</span>
                            <span class="text-[10px] text-amber-600 font-bold uppercase tracking-widest block">Open & Distance Learning</span>
                        </div>
                    </div>
                    <p class="text-xs leading-relaxed text-slate-500">
                        {{ \App\Models\Setting::get('footer_tagline', 'A world-class accredited higher learning institution dedicated to distance, open, and flexible online education with international research standards.') }}
                    </p>
                    <div class="pt-2 flex items-center space-x-3 text-slate-500">
                        <a href="{{ \App\Models\Setting::get('footer_twitter', '#') }}" target="_blank" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:text-blue-800 hover:border-blue-800 transition-colors">TW</a>
                        <a href="{{ \App\Models\Setting::get('footer_facebook', '#') }}" target="_blank" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:text-blue-800 hover:border-blue-800 transition-colors">FB</a>
                        <a href="{{ \App\Models\Setting::get('footer_linkedin', '#') }}" target="_blank" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:text-blue-800 hover:border-blue-800 transition-colors">LN</a>
                        <a href="{{ \App\Models\Setting::get('footer_youtube', '#') }}" target="_blank" class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:text-blue-800 hover:border-blue-800 transition-colors">YT</a>
                    </div>
                </div>

                <!-- Academic Programs Quick Links -->
                <div>
                    <h4 class="text-slate-900 font-extrabold mb-4 text-xs uppercase tracking-wider border-l-2 border-amber-500 pl-2.5">Academic Catalog</h4>
                    <ul class="space-y-2.5 text-xs">
                        <li><a href="{{ route('public.programmes') }}" class="hover:text-blue-800 transition-colors flex items-center gap-1.5">&rsaquo; Undergraduate Degrees</a></li>
                        <li><a href="{{ route('public.programmes') }}" class="hover:text-blue-800 transition-colors flex items-center gap-1.5">&rsaquo; Postgraduate Masters</a></li>
                        <li><a href="{{ route('public.programmes') }}" class="hover:text-blue-800 transition-colors flex items-center gap-1.5">&rsaquo; Foundation Course (Bridging)</a></li>
                        <li><a href="{{ route('public.requirements') }}" class="hover:text-blue-800 transition-colors flex items-center gap-1.5">&rsaquo; Entry Requirements Guide</a></li>
                    </ul>
                </div>

                <!-- Student & Admission Hub -->
                <div>
                    <h4 class="text-slate-900 font-extrabold mb-4 text-xs uppercase tracking-wider border-l-2 border-blue-500 pl-2.5">Admissions Hub</h4>
                    <ul class="space-y-2.5 text-xs">
                        <li><a href="{{ route('public.track') }}" class="hover:text-blue-800 transition-colors flex items-center gap-1.5">&rsaquo; Track Application Status</a></li>
                        <li><a href="{{ route('public.faqs') }}" class="hover:text-blue-800 transition-colors flex items-center gap-1.5">&rsaquo; Frequently Asked Questions</a></li>
                        <li><a href="{{ route('public.downloads') }}" class="hover:text-blue-800 transition-colors flex items-center gap-1.5">&rsaquo; Download Prospectus PDF</a></li>
                        <li><a href="{{ route('public.contact') }}" class="hover:text-blue-800 transition-colors flex items-center gap-1.5">&rsaquo; Admission Office Help Desk</a></li>
                    </ul>
                </div>

                <!-- Contact & Newsletter -->
                <div>
                    <h4 class="text-slate-900 font-extrabold mb-4 text-xs uppercase tracking-wider border-l-2 border-emerald-500 pl-2.5">Admissions Office</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">{{ \App\Models\Setting::get('footer_address', 'Main Campus, Kawawa Road, Kinondoni, Dar es Salaam, Tanzania') }}</p>
                    <p class="text-xs text-blue-800 font-bold mt-2">Email: {{ \App\Models\Setting::get('support_email', 'admissions@supa.ac.tz') }}</p>
                    <p class="text-xs text-slate-600 mt-0.5">Phone: {{ \App\Models\Setting::get('support_phone', '+255 22 266 8820') }}</p>
                    
                    <form @submit.prevent="toast('Subscribed to admission updates!', 'success')" class="mt-4 flex">
                        <input type="email" placeholder="Enter your email" required class="w-full px-3 py-2 rounded-l-xl bg-white border border-slate-200 text-xs text-slate-900 outline-none focus:border-blue-500">
                        <button type="submit" class="gradient-btn px-4 py-2 rounded-r-xl font-bold text-xs">Join</button>
                    </form>
                </div>

            </div>

            <!-- Footer Bottom -->
            <div class="border-t border-slate-200 pt-8 flex flex-col md:flex-row justify-between items-center text-xs text-slate-500 gap-4">
                <div>
                    <p>{{ \App\Models\Setting::get('footer_copyright', '© ' . date('Y') . ' SUPA / OUT University Admission Management System. All rights reserved.') }}</p>
                    @if(\App\Models\Setting::get('developer_name', 'Reliance Solutions & Technology'))
                        <p class="text-[11px] text-slate-500 mt-1 flex flex-wrap items-center gap-1">
                            <span>Developed by</span>
                            @if(\App\Models\Setting::get('developer_url', 'http://www.reliancesolutions.co.tz'))
                                <a href="{{ \App\Models\Setting::get('developer_url', 'http://www.reliancesolutions.co.tz') }}" target="_blank" rel="noopener noreferrer" class="text-blue-800 hover:underline font-extrabold flex items-center gap-1">
                                    {{ \App\Models\Setting::get('developer_name', 'Reliance Solutions & Technology') }}
                                    <span class="text-slate-500 font-normal">({{ str_replace(['http://', 'https://'], '', \App\Models\Setting::get('developer_url', 'www.reliancesolutions.co.tz')) }})</span>
                                </a>
                            @else
                                <strong class="text-slate-600">{{ \App\Models\Setting::get('developer_name', 'Reliance Solutions & Technology') }}</strong>
                            @endif
                        </p>
                    @endif
                </div>
                <div class="flex space-x-6 font-medium shrink-0">
                    <a href="#" class="hover:text-blue-800 transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-blue-800 transition-colors">Terms of Admission</a>
                    <a href="#" class="hover:text-blue-800 transition-colors">Security & Compliance</a>
                </div>
            </div>
        </div>

        <!-- Back To Top Button -->
        <button @click="window.scrollTo({ top: 0, behavior: 'smooth' })" class="fixed bottom-6 left-6 p-3 rounded-2xl bg-white text-blue-800 border border-slate-200 shadow-xl hover:scale-110 transition-transform z-40" title="Back to top">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
        </button>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.axios) {
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = '{{ csrf_token() }}';
            }
        });
    </script>
</body>
</html>
