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

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>[x-cloak] { display: none !important; }</style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
@php
    $modalProgrammesList = \App\Models\Programme::where('is_active', true)->orderBy('id')->get()->map(function($prog) {
        $photoUrl = $prog->image;
        if ($photoUrl && !\Illuminate\Support\Str::startsWith($photoUrl, 'http') && !\Illuminate\Support\Str::startsWith($photoUrl, 'data:')) {
            $photoUrl = asset('storage/' . $photoUrl);
        }
        if (!$photoUrl) {
            $photoUrl = match($prog->code) {
                'BAED' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?q=80&w=800&auto=format&fit=crop',
                'BSCED' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?q=80&w=800&auto=format&fit=crop',
                'IMPTE' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?q=80&w=800&auto=format&fit=crop',
                'Foundation' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=800&auto=format&fit=crop',
                default => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?q=80&w=800&auto=format&fit=crop',
            };
        }

        return [
            'id' => $prog->id,
            'name' => $prog->name,
            'code' => $prog->code,
            'duration_years' => $prog->duration_years,
            'department' => $prog->department ?? 'Department of Educational Studies',
            'faculty' => $prog->faculty ?? 'Faculty of Education',
            'description' => $prog->description ?? '',
            'entry_requirements' => $prog->entry_requirements ?? '',
            'annual_fee' => (float)$prog->annual_fee,
            'monthly_fee' => (float)($prog->monthly_fee ?? ($prog->annual_fee / 10)),
            'image' => $photoUrl,
            'apply_url' => route('applicant.wizard') . '?programme_id=' . $prog->id,
        ];
    });
@endphp
</head>
<body class="bg-white text-slate-800 antialiased min-h-screen flex flex-col relative selection:bg-amber-500 selection:text-slate-900"
      x-data="{ 
          scrolled: false, 
          searchModal: false, 
          mobileMenu: false, 
          programmeMenu: false, 
          admissionMenu: false,
          admissionModal: {{ request()->routeIs('home') ? 'true' : 'false' }},
          admissionStep: 1,
          progSearch: '',
          modalProgrammes: {{ json_encode($modalProgrammesList) }},
          trackNumber: '',
          trackResult: null,
          trackError: null,
          trackLoading: false,
          trackOtpLoading: false,
          trackOtpError: null,
          get filteredProgrammes() {
              if (!this.progSearch || !this.progSearch.trim()) {
                  return this.modalProgrammes;
              }
              const q = this.progSearch.toLowerCase().trim();
              return this.modalProgrammes.filter(p => 
                  (p.name && p.name.toLowerCase().includes(q)) || 
                  (p.code && p.code.toLowerCase().includes(q)) ||
                  (p.department && p.department.toLowerCase().includes(q)) ||
                  (p.faculty && p.faculty.toLowerCase().includes(q)) ||
                  (p.entry_requirements && p.entry_requirements.toLowerCase().includes(q))
              );
          },
          openAdmissionModal(step = 1) {
              this.admissionStep = step;
              this.admissionModal = true;
          },
          closeAdmissionModal() {
              this.admissionModal = false;
          }
      }" 
      @scroll.window="scrolled = window.scrollY > 30"
      @keydown.escape.window="closeAdmissionModal()">

    <!-- Page Loader Overlay -->
    <div id="page-loader" class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-slate-900/95 backdrop-blur-2xl transition-all duration-500 ease-out">
        <div class="relative flex flex-col items-center space-y-6">
            <!-- Loader Animated Rings -->
            <div class="relative flex items-center justify-center w-24 h-24">
                <!-- Glowing background pulse -->
                <div class="absolute inset-0 rounded-full bg-amber-500/20 blur-xl animate-pulse"></div>
                <!-- Outer spinning orbit line -->
                <div class="absolute inset-0 rounded-full border-2 border-transparent border-t-amber-500 border-b-blue-500 animate-spin" style="animation-duration: 1.5s;"></div>
                <!-- Inner logo wrapper -->
                <div class="flex items-center space-x-3.5 animate-pulse">
                    <div class="w-14 h-14 rounded-2xl bg-white border border-slate-200 flex items-center justify-center shadow-lg p-1">
                        @if(\App\Models\Setting::get('system_logo'))
                            <img src="{{ asset('storage/' . \App\Models\Setting::get('system_logo')) }}" alt="System Logo" class="w-full h-full object-contain">
                        @elseif(\App\Models\Setting::get('sttc_logo'))
                            <img src="{{ asset('storage/' . \App\Models\Setting::get('sttc_logo')) }}" alt="STTC Logo" class="w-full h-full object-contain">
                        @else
                            <span class="text-slate-900 font-extrabold text-xs">STTC</span>
                        @endif
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-white border border-slate-200 flex items-center justify-center shadow-lg p-1">
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
            :class="scrolled ? 'py-2 bg-white/95 backdrop-blur-xl border-b border-slate-200 shadow-md' : 'py-3.5 bg-white/90 backdrop-blur-lg border-b border-slate-200/60'">
        <div class="max-w-7xl mx-auto flex justify-between items-center gap-3">
            
            <!-- University Logo Area -->
            <a href="{{ route('home') }}" class="flex items-center space-x-3 group shrink-0 min-w-0">
                <div class="flex items-center space-x-1.5 shrink-0">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-white border border-slate-200 flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform duration-300 overflow-hidden p-1">
                        @if(\App\Models\Setting::get('system_logo'))
                            <img src="{{ asset('storage/' . \App\Models\Setting::get('system_logo')) }}" alt="System Logo" class="w-full h-full object-contain">
                        @elseif(\App\Models\Setting::get('sttc_logo'))
                            <img src="{{ asset('storage/' . \App\Models\Setting::get('sttc_logo')) }}" alt="STTC Logo" class="w-full h-full object-contain">
                        @else
                            <span class="text-amber-600 font-extrabold text-[10px]">STTC</span>
                        @endif
                    </div>
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-white border border-slate-200 flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform duration-300 overflow-hidden p-1">
                        @if(\App\Models\Setting::get('out_logo'))
                            <img src="{{ asset('storage/' . \App\Models\Setting::get('out_logo')) }}" alt="OUT Logo" class="w-full h-full object-contain">
                        @else
                            <span class="text-blue-800 font-extrabold text-[10px]">OUT</span>
                        @endif
                    </div>
                </div>
                <div class="min-w-0 max-w-[200px] sm:max-w-xs md:max-w-sm lg:max-w-xs xl:max-w-sm">
                    <span class="text-xs sm:text-sm md:text-base font-extrabold tracking-tight text-slate-900 block leading-tight truncate">
                        {{ \App\Models\Setting::get('university_name', "SINGIDA TEACHERS' TRAINING COLLEGE (STTC) & OUT") }}
                    </span>
                    <span class="text-[9px] sm:text-[10px] text-blue-800 tracking-wider uppercase block font-bold truncate mt-0.5">
                        Admission Management System
                    </span>
                </div>
            </a>

            <!-- Desktop Navigation Menu -->
            <nav class="hidden xl:flex items-center space-x-5 2xl:space-x-7 text-xs xl:text-sm font-semibold text-slate-600 shrink-0">
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
                <!-- Mega Menu: Careers -->
                <div class="relative" x-data="{ careerMenu: false }" @mouseenter="careerMenu = true" @mouseleave="careerMenu = false">
                    <button class="nav-link flex items-center gap-1.5 py-2 {{ request()->routeIs('public.careers.*') ? 'text-blue-800 active' : 'hover:text-blue-800' }}">
                        <span>Careers</span>
                        <svg class="w-4 h-4 text-slate-500 transition-transform" :class="{ 'rotate-180': careerMenu }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    
                    <div x-show="careerMenu" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-2"
                         class="absolute left-0 mt-1 w-80 bg-white/95 backdrop-blur-2xl border border-slate-200 rounded-3xl shadow-2xl p-4 z-50 text-xs" x-cloak>
                        <a href="{{ route('public.careers.index') }}" class="flex items-start space-x-3 p-3 rounded-xl hover:bg-slate-50 transition-colors">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs shrink-0">1</div>
                            <div>
                                <span class="font-bold text-slate-900 block">Job Vacancies</span>
                                <span class="text-[11px] text-slate-500">Explore open academic & administrative roles</span>
                            </div>
                        </a>
                        <a href="{{ route('public.careers.track') }}" class="flex items-start space-x-3 p-3 rounded-xl hover:bg-slate-50 transition-colors">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xs shrink-0">2</div>
                            <div>
                                <span class="font-bold text-slate-900 block">Track Job Application</span>
                                <span class="text-[11px] text-slate-500">Track and resume incomplete job applications</span>
                            </div>
                        </a>
                    </div>
                </div>
                <a href="{{ route('public.contact') }}" class="nav-link {{ request()->routeIs('public.contact') ? 'text-blue-800 active' : 'hover:text-blue-800' }}">Contact</a>
            </nav>

            <!-- Navigation Actions & CTAs -->
            <div class="flex items-center space-x-2 sm:space-x-3 shrink-0">
                
                <!-- Quick Search Trigger Button -->
                <button @click="searchModal = true" class="p-2 sm:p-2.5 rounded-xl bg-slate-100 text-slate-600 hover:text-slate-900 hover:bg-slate-200 transition-colors shrink-0" title="Search Site">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>

                @auth
                    @if(auth()->user()->isApplicant())
                        <a href="{{ route('applicant.dashboard') }}" class="gradient-btn px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-xl font-bold text-xs shadow-md hidden sm:flex items-center gap-1.5 hover:scale-105 transition-transform" title="Admission Portal Dashboard">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7"/></svg>
                            <span>Admission Portal</span>
                        </a>
                        <a href="{{ route('public.careers.dashboard') }}" class="gradient-btn-gold px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-xl font-bold text-xs shadow-md hidden 2xl:flex items-center gap-1.5 hover:scale-105 transition-transform" title="Careers Portal Dashboard">
                            <span>Careers Portal</span>
                        </a>
                    @else
                        <a href="{{ route('admin.dashboard') }}" class="gradient-btn px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl text-white font-bold text-xs shadow-md hover:scale-105 transition-transform hidden sm:flex items-center gap-2">
                            <span>Admin Panel</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="hidden sm:inline-block text-xs font-bold text-slate-600 hover:text-slate-950 px-2.5 py-1.5 rounded-lg hover:bg-slate-100 transition-colors">Sign In</a>
                    <a href="{{ route('public.programmes') }}" class="gradient-btn px-3.5 py-2 rounded-xl font-bold text-xs shadow-md hidden sm:flex items-center gap-1.5 hover:scale-105 transition-transform" title="Admission - Academic Programmes">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7"/></svg>
                        <span>Admission</span>
                    </a>
                    <a href="{{ route('public.careers.index') }}" class="gradient-btn-gold px-3.5 py-2 rounded-xl font-bold text-xs shadow-md hidden 2xl:flex items-center gap-1.5 hover:scale-105 transition-transform" title="Apply for Job - Created Vacancies">
                        <span>Apply for Job</span>
                    </a>
                @endauth

                <!-- Mobile / Tablet Hamburger Button -->
                <button @click="mobileMenu = true" class="xl:hidden p-2 sm:p-2.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors focus:outline-none" aria-label="Open Navigation Menu">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>

        </div>
    </header>

    <!-- Mobile / Tablet Slide-over Drawer -->
    <div x-show="mobileMenu" class="fixed inset-0 z-50 xl:hidden flex justify-end" x-cloak>
        <div @click="mobileMenu = false" class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm"></div>
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
                    <a href="{{ route('public.careers.index') }}" class="block p-3 rounded-xl hover:bg-slate-50 text-slate-600 font-bold">Careers - Vacancies</a>
                    <a href="{{ route('public.careers.track') }}" class="block p-3 rounded-xl hover:bg-slate-50 text-slate-600 font-bold">Careers - Track Application</a>
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
                    <a href="{{ route('public.faqs') }}" class="hover:text-blue-800 transition-colors">FAQ</a>
                    <a href="{{ route('public.privacy') }}" class="hover:text-blue-800 transition-colors">Privacy Policy</a>
                    <a href="{{ route('public.terms') }}" class="hover:text-blue-800 transition-colors">Terms of Admission</a>
                    <a href="#" class="hover:text-blue-800 transition-colors">Security & Compliance</a>
                </div>
            </div>
        </div>

        <!-- Back To Top Button -->
        <button @click="window.scrollTo({ top: 0, behavior: 'smooth' })" class="fixed bottom-6 left-6 p-3 rounded-2xl bg-white text-blue-800 border border-slate-200 shadow-xl hover:scale-110 transition-transform z-40" title="Back to top">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
        </button>
    </footer>

    <!-- Admission Welcome & Programme Selection Modal -->
    <div x-show="admissionModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9990] overflow-y-auto" 
         aria-labelledby="admission-modal-title" 
         role="dialog" 
         aria-modal="true" 
         x-cloak>
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm transition-opacity" 
             @click="closeAdmissionModal()"></div>

        <!-- Modal Positioning Wrapper -->
        <div class="flex min-h-full items-center justify-center p-3 sm:p-6 text-center">
            <div x-show="admissionModal" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @click.stop
                 class="relative transform overflow-hidden rounded-3xl bg-white shadow-2xl transition-all duration-300 w-full border border-slate-100"
                 :class="admissionStep === 2 || admissionStep === 3 ? 'max-w-4xl p-5 sm:p-8 text-left' : 'max-w-2xl p-8 sm:p-12 text-center'">
                
                <!-- STEP 1: Welcome & Initial Prompt -->
                <div x-show="admissionStep === 1" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="space-y-6">
                    
                    <!-- Close Button (Top Right) -->
                    <button type="button" 
                            @click="closeAdmissionModal()" 
                            class="absolute top-5 right-5 p-2 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors focus:outline-none"
                            title="Funga (Close)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>

                    <!-- Icon Badge (Green Checkmark Badge) -->
                    <div class="w-16 h-16 rounded-full bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center mx-auto shadow-sm">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>

                    <!-- Main Title -->
                    <div>
                        <h2 id="admission-modal-title" class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mb-2">
                            Usajili wa mtandao umefunguliwa
                        </h2>
                        <p class="text-lg sm:text-xl font-black text-[#ff5500] tracking-tight">
                            Jisajili kwaajili ya stashahada &amp; shahada
                        </p>
                    </div>

                    <!-- Swahili Prompt -->
                    <p class="text-base sm:text-lg font-medium text-slate-700 max-w-xl mx-auto leading-relaxed">
                        Je, ungependa <strong class="font-extrabold text-slate-900">kuanza usajili</strong> sasa, au <strong class="font-extrabold text-slate-900">kuendelea kutazama tovuti</strong> bila kuingia kwenye fomu ya maombi?
                    </p>

                    <!-- English Subtitle -->
                    <p class="text-xs sm:text-sm text-slate-500 max-w-lg mx-auto leading-normal">
                        Online admission is open. Do you want to <strong class="font-semibold text-slate-700">start registration</strong> now, or <strong class="font-semibold text-slate-700">continue browsing</strong> the website?
                    </p>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-2">
                        <button type="button"
                                @click="admissionStep = 2" 
                                class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-[#ff5500] hover:bg-[#e04b00] text-white font-black text-sm sm:text-base shadow-lg shadow-orange-500/25 hover:shadow-orange-500/40 hover:scale-[1.02] active:scale-95 transition-all text-center flex items-center justify-center gap-2 cursor-pointer">
                            <span>Anza usajili</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                        <button type="button"
                                @click="admissionStep = 3; trackNumber = ''; trackResult = null; trackError = null; trackOtpError = null;"
                                class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-blue-50 border-2 border-blue-200 hover:bg-blue-100 hover:border-blue-300 text-blue-700 font-bold text-sm sm:text-base shadow-sm hover:scale-[1.02] active:scale-95 transition-all text-center flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4 sm:w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            <span>Fuatilia maombi</span>
                        </button>
                        <button type="button" 
                                @click="closeAdmissionModal()" 
                                class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-white border-2 border-slate-200 hover:border-slate-300 text-slate-800 hover:bg-slate-50 font-bold text-sm sm:text-base shadow-sm active:scale-95 transition-all text-center cursor-pointer">
                            Endelea kutazama tovuti
                        </button>
                    </div>

                    <!-- Footer Hint -->
                    <p class="text-xs text-slate-400 max-w-md mx-auto leading-relaxed pt-2">
                        Kama tayari una namba ya maombi, unaweza kuitumia baadaye kupitia kiungo &quot;Apply Now&quot; au ukurasa wa maombi.
                    </p>
                </div>

                <!-- STEP 2: POP-OUT PROGRAMMES OFFERED -->
                <div x-show="admissionStep === 2" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="space-y-4">
                    
                    <!-- Top Action Bar -->
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <button type="button" 
                                @click="admissionStep = 1" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                            <span>Rudi Nyuma (Back)</span>
                        </button>

                        <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-800 text-[10px] sm:text-[11px] font-extrabold tracking-wider uppercase border border-blue-200/60">
                            CHUO CHA UALIMU SINGIDA (STTC) + OUT
                        </span>

                        <button type="button" 
                                @click="closeAdmissionModal()" 
                                class="p-2 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer"
                                title="Funga (Close)">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Heading & Guide -->
                    <div>
                        <h3 class="text-lg sm:text-2xl font-black text-slate-900 tracking-tight">
                            Chagua Programu ya Masomo (Select Programme)
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1">
                            Chagua programu unayotaka kusomea kisha uendelee na <strong>Fomu ya Udahili na Tamko la Idhini (Consent Form)</strong>.
                        </p>
                    </div>

                    <!-- Search Filter Box -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" 
                               x-model="progSearch" 
                               placeholder="Tafuta programu kwa jina au code (mf. BAED, BSCED, Ualimu, Sayansi)..." 
                               class="w-full pl-10 pr-10 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#ff5500]/30 focus:border-[#ff5500] transition-all">
                        <button x-show="progSearch" 
                                @click="progSearch = ''" 
                                type="button" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Scrollable List of Offered Programmes -->
                    <div class="max-h-[50vh] sm:max-h-[55vh] overflow-y-auto space-y-3 pr-1 divide-y divide-slate-100">
                        <template x-for="prog in filteredProgrammes" :key="prog.id">
                            <div class="pt-3 first:pt-0 pb-1 group">
                                <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-50/60 hover:bg-orange-50/40 border border-slate-200/80 hover:border-orange-300 transition-all shadow-sm hover:shadow-md flex flex-col md:flex-row md:items-center justify-between gap-3.5">
                                    
                                    <!-- Programme Thumbnail & Info -->
                                    <div class="flex items-start gap-3 sm:gap-4 flex-1">
                                        <div class="relative w-14 h-14 sm:w-16 sm:h-16 rounded-xl overflow-hidden shrink-0 border border-slate-200 shadow-sm">
                                            <img :src="prog.image" :alt="prog.name" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                            <span class="absolute bottom-0 inset-x-0 bg-slate-950/80 text-white text-[8px] sm:text-[9px] font-black text-center py-0.5" x-text="prog.code"></span>
                                        </div>

                                        <div class="space-y-1 flex-1 min-w-0">
                                            <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                                                <span class="px-2 py-0.5 rounded-md bg-orange-100 text-[#ff5500] text-[9px] sm:text-[10px] font-black tracking-wide" x-text="'Code: ' + prog.code"></span>
                                                <span class="px-2 py-0.5 rounded-md bg-blue-100 text-blue-800 text-[9px] sm:text-[10px] font-bold" x-text="'Miaka ' + prog.duration_years"></span>
                                                <span class="text-[10px] sm:text-[11px] text-slate-500 font-semibold truncate" x-text="prog.faculty"></span>
                                            </div>

                                            <h4 class="text-xs sm:text-sm font-extrabold text-slate-900 group-hover:text-[#ff5500] transition-colors leading-snug" x-text="prog.name"></h4>

                                            <p class="text-[11px] text-slate-600 line-clamp-1" x-text="prog.description"></p>

                                            <div class="flex items-center gap-2 pt-0.5 text-[10px] sm:text-[11px] text-slate-500 font-medium">
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span class="font-bold text-slate-700" x-text="prog.entry_requirements"></span>
                                                </span>
                                                <span class="text-slate-300">•</span>
                                                <span class="text-slate-700 font-bold">Ada ya Fomu: <span class="text-amber-600 font-extrabold">TZS 20,000</span></span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Button -->
                                    <div class="md:shrink-0 flex items-center justify-end">
                                        <a :href="prog.apply_url" 
                                           @click="closeAdmissionModal()" 
                                           class="w-full md:w-auto px-4 py-2.5 rounded-xl bg-[#ff5500] hover:bg-[#e04b00] text-white font-black text-xs shadow-md hover:shadow-orange-500/30 flex items-center justify-center gap-1.5 hover:scale-105 active:scale-95 transition-all text-center">
                                            <span>Chagua &amp; Anza Fomu</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Empty state -->
                        <div x-show="filteredProgrammes.length === 0" class="p-8 text-center space-y-2">
                            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-lg">🔍</div>
                            <p class="text-xs font-bold text-slate-700">Hakuna programu iliyopatikana kwa neno &quot;<span x-text="progSearch"></span>&quot;</p>
                            <button type="button" @click="progSearch = ''" class="text-xs font-bold text-[#ff5500] hover:underline cursor-pointer">
                                Onyesha programu zote
                            </button>
                        </div>
                    </div>

                    <!-- Footer Guidance -->
                    <div class="pt-2 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-2 text-[11px] sm:text-xs text-slate-500">
                        <div class="flex items-center gap-1.5">
                            <span class="text-sm">📋</span>
                            <span>Baada ya kuchagua programu, utaingia moja kwa moja kwenye <strong>Fomu ya Udahili &amp; Fomu ya Idhini (Consent Form)</strong>.</span>
                        </div>
                        <button type="button" 
                                @click="closeAdmissionModal()" 
                                class="text-slate-400 hover:text-slate-700 font-bold underline shrink-0 cursor-pointer">
                            Endelea kutazama tovuti
                        </button>
                    </div>
                </div>

                <!-- STEP 3: TRACK APPLICATION FORM (IN-MODAL POP FORM) -->
                <div x-show="admissionStep === 3" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="space-y-6">
                    
                    <!-- Top Action Bar -->
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <button type="button" 
                                @click="admissionStep = 1" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                            <span>Rudi Nyuma (Back)</span>
                        </button>

                        <button type="button" 
                                @click="closeAdmissionModal()" 
                                class="p-2 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer"
                                title="Funga (Close)">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Heading & Instructions Box -->
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg sm:text-2xl font-black text-slate-900 tracking-tight">
                                Fuatilia Usajili (Track Application Status)
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-600 mt-1">
                                Weka namba yako ya simu uliyosajilia au Control namba ili kuendelea na udahili.
                            </p>
                        </div>

                        <!-- Prominent Instructions in Swahili -->
                        <div class="p-4 rounded-2xl bg-blue-50 border border-blue-100 text-blue-900 text-xs sm:text-sm font-semibold leading-relaxed flex gap-3 items-start">
                            <div class="p-1.5 rounded-xl bg-blue-600 text-white mt-0.5 shadow-md flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="space-y-1">
                                <p class="text-blue-950 text-sm font-bold">Maelekezo / Instructions:</p>
                                <p class="text-blue-900 font-extrabold text-[13px] sm:text-sm leading-relaxed">
                                    Andika namba yako ya simu uliyosajilia au Control namba yako kuendelea na hatua zilizobaki ili kukamilisha usajili.
                                </p>
                                <p class="text-xs text-slate-550 font-medium italic">
                                    (Enter the phone number you registered with or your Control number to continue with the remaining steps to complete your registration.)
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Query Form -->
                    <form @submit.prevent="
                        trackLoading = true; trackError = null; trackResult = null; trackOtpError = null;
                        axios.post('{{ url('/api/v1/public/track-application') }}', { application_number: trackNumber })
                            .then(res => { trackResult = res.data; trackLoading = false; })
                            .catch(err => { trackError = err.response?.data?.message || 'Hakuna maombi yaliyopatikana. (No active application record was found.)'; trackLoading = false; })
                    " class="space-y-4">
                        <div>
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-2">
                                Namba ya Simu / Control Number
                            </label>
                            <div class="relative rounded-2xl shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <input type="text" x-model="trackNumber" required 
                                       placeholder="Mfano: 0712345678 au 99123xxxxx" 
                                       class="w-full pl-10 pr-5 py-3.5 rounded-2xl border border-slate-200 bg-slate-50 text-slate-900 font-black text-sm focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 outline-none transition-all placeholder-slate-400">
                            </div>
                        </div>

                        <button type="submit" :disabled="trackLoading" 
                                class="w-full bg-gradient-to-r from-blue-900 to-indigo-950 hover:from-blue-800 hover:to-indigo-900 py-3.5 rounded-2xl text-white font-extrabold text-xs sm:text-sm shadow-xl flex items-center justify-center gap-2 transition-all hover:shadow-indigo-900/20 active:scale-[0.99] disabled:opacity-50 cursor-pointer">
                            <span x-show="!trackLoading" class="flex items-center gap-1">Tafuta Maombi / Search &rarr;</span>
                            <span x-show="trackLoading" x-cloak class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Inatafuta...
                            </span>
                        </button>
                    </form>

                    <!-- Error Alert -->
                    <div x-show="trackError" class="p-4 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-xs sm:text-sm font-bold flex gap-2 items-center" x-cloak>
                        <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <span x-text="trackError"></span>
                    </div>

                    <!-- Result Card inside modal -->
                    <div x-show="trackResult" class="bg-slate-50 rounded-2xl p-5 border border-slate-200 space-y-4 text-left" x-cloak>
                        <div class="flex justify-between items-center border-b border-slate-200 pb-3">
                            <div>
                                <span class="text-[9px] font-black text-slate-450 uppercase tracking-wider block">ID ya Maombi / Application ID</span>
                                <span class="text-sm sm:text-base font-black text-blue-900" x-text="trackResult?.application_number"></span>
                            </div>
                            <span class="inline-block px-3 py-1 rounded-full text-[10px] font-black uppercase border"
                                  :class="{ 
                                      'bg-amber-50 text-amber-800 border-amber-200': trackResult?.status === 'Draft' || trackResult?.status === 'Pending Payment' || trackResult?.status === 'PAYMENT_PENDING' || trackResult?.status === 'IN_PROGRESS', 
                                      'bg-emerald-50 text-emerald-800 border-emerald-200': trackResult?.status === 'Approved' || trackResult?.status === 'SUBMITTED' || trackResult?.status === 'Under Review', 
                                      'bg-red-50 text-red-800 border-red-200': trackResult?.status === 'Rejected' || trackResult?.status === 'Expired' 
                                  }"
                                  x-text="trackResult?.status"></span>
                        </div>

                        <!-- Mini Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs font-semibold text-slate-700">
                            <div>
                                <span class="text-slate-450 block text-[9px] uppercase font-black mb-0.5">Kozi / Programme</span>
                                <span class="font-extrabold text-slate-900" x-text="trackResult?.programme"></span>
                            </div>
                            <div>
                                <span class="text-slate-450 block text-[9px] uppercase font-black mb-0.5">Malipo / Payment Status</span>
                                <span class="font-black uppercase text-[10px] flex items-center gap-1" :class="trackResult?.payment_status === 'paid' ? 'text-emerald-600' : 'text-amber-500'">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="trackResult?.payment_status === 'paid' ? 'bg-emerald-500' : 'bg-amber-400'"></span>
                                    <span x-text="trackResult?.payment_status === 'paid' ? 'Imelipwa (Paid)' : 'Haijalipwa (Pending)'"></span>
                                </span>
                            </div>
                            <div class="sm:col-span-2">
                                <span class="text-slate-450 block text-[9px] uppercase font-black mb-0.5">Hatua Uliyofikia / Progress</span>
                                <span class="font-extrabold text-slate-900" x-text="'Hatua ' + (trackResult?.current_step || 1) + ' (' + (trackResult?.completion_percentage || 0) + '%)'"></span>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="w-full bg-slate-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-amber-500 to-emerald-500 h-full rounded-full transition-all duration-300"
                                 :style="'width: ' + (trackResult?.completion_percentage || 0) + '%'"></div>
                        </div>

                        <!-- Incomplete Action Flow inside modal -->
                        <template x-if="trackResult?.status === 'Draft' || trackResult?.status === 'IN_PROGRESS' || trackResult?.status === 'Pending Payment' || trackResult?.status === 'PAYMENT_PENDING'">
                            <div class="border-t border-slate-200 pt-3 space-y-3">
                                <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-[11px] text-amber-900 leading-normal font-semibold">
                                    ⚠️ <strong>Ombi halijakamilika (Application Incomplete).</strong> Bofya kitufe kilicho chini ili kuendelea na usajili mara moja.
                                </div>
                                <button @click="
                                    trackOtpLoading = true; trackOtpError = null;
                                    axios.post('{{ url('/api/v1/public/resume-direct') }}', { application_id: trackResult.application_id, user_id: trackResult.user_id })
                                        .then(res => { window.location.href = res.data.redirect_url; })
                                        .catch(err => { trackOtpError = err.response?.data?.message || 'Imeshindikana kuendelea na usajili. Jaribu tena baadae.'; trackOtpLoading = false; })
                                " :disabled="trackOtpLoading" 
                                        class="w-full bg-amber-500 hover:bg-amber-600 text-slate-900 font-black py-3.5 rounded-2xl text-xs sm:text-sm shadow-md transition-all active:scale-[0.99] flex items-center justify-center gap-1.5 cursor-pointer">
                                    <span x-show="!trackOtpLoading" class="flex items-center gap-1.5">🚀 Endelea na Usajili (Resume Registration)</span>
                                    <span x-show="trackOtpLoading" x-cloak class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4 text-slate-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Inafungua...
                                    </span>
                                </button>
                                <div x-show="trackOtpError" x-cloak class="p-2 bg-red-100 border border-red-200 text-red-800 rounded-xl text-[10px] font-bold" x-text="trackOtpError"></div>
                            </div>
                        </template>

                        <!-- Completed Application Message -->
                        <template x-if="trackResult?.status === 'Approved' || trackResult?.status === 'SUBMITTED' || trackResult?.status === 'Under Review'">
                            <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-100 text-[11px] text-emerald-900 leading-normal font-semibold">
                                🎉 <strong>Ombi lako limepokelewa kikamilifu (Submitted).</strong> Hakuna hatua za ziada. Tafadhali subiri mrejesho wa udahili.
                            </div>
                        </template>
                    </div>

                    <!-- Footer Guidance -->
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-end text-[11px] sm:text-xs">
                        <button type="button" 
                                @click="closeAdmissionModal()" 
                                class="text-slate-400 hover:text-slate-700 font-bold underline cursor-pointer">
                            Funga na uendelee na tovuti
                        </button>
                    </div>
                </div>

            </div>
        </div>
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
