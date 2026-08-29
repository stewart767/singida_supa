<x-public-layout title="SUPA / OUT - Online University Admission Management System">

    @php
        $defaultSliders = [
            [
                'id' => 1,
                'title' => 'Admissions for 2026 / 2027 Are Now Open.',
                'subtitle' => 'Experience world-class open, distance, and digital higher education.',
                'cta' => 'Apply Now',
                'image' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=2070',
                'status' => 'Active'
            ],
            [
                'id' => 2,
                'title' => 'Study with Excellence & Innovation.',
                'subtitle' => 'Choose from over 85 accredited undergraduate, postgraduate, and foundation programmes.',
                'cta' => 'Explore Programmes',
                'image' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=2070',
                'status' => 'Active'
            ],
            [
                'id' => 3,
                'title' => 'Your Future Starts Here.',
                'subtitle' => 'Begin your academic journey with absolute confidence.',
                'cta' => 'Track Application',
                'image' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=2070',
                'status' => 'Active'
            ]
        ];
        $heroSliders = \App\Models\Setting::get('cms_hero_sliders', $defaultSliders);
        if (!is_array($heroSliders) || count($heroSliders) === 0) {
            $heroSliders = $defaultSliders;
        }
        $activeSliders = array_values(array_filter($heroSliders, fn($s) => ($s['status'] ?? 'Active') === 'Active'));
        if (count($activeSliders) === 0) $activeSliders = $defaultSliders;
    @endphp

    <!-- 1. Fullscreen Cinematic Hero Slider -->
    <section class="relative bg-slate-950 text-white min-h-[85vh] lg:min-h-[90vh] flex items-center overflow-hidden"
             x-data="{ 
                 activeSlide: 1, 
                 totalSlides: {{ count($activeSliders) }}, 
                 autoPlayTimer: null,
                 progress: 0,
                 init() {
                     this.startAutoPlay();
                 },
                 startAutoPlay() {
                     this.stopAutoPlay();
                     this.progress = 0;
                     const stepMs = 50;
                     const duration = 6000;
                     this.autoPlayTimer = setInterval(() => {
                         this.progress += (stepMs / duration) * 100;
                         if (this.progress >= 100) {
                             this.nextSlide();
                         }
                     }, stepMs);
                 },
                 stopAutoPlay() {
                     if (this.autoPlayTimer) clearInterval(this.autoPlayTimer);
                 },
                 nextSlide() {
                     this.activeSlide = this.activeSlide === this.totalSlides ? 1 : this.activeSlide + 1;
                     this.startAutoPlay();
                 },
                 prevSlide() {
                     this.activeSlide = this.activeSlide === 1 ? this.totalSlides : this.activeSlide - 1;
                     this.startAutoPlay();
                 },
                 goTo(slide) {
                     this.activeSlide = slide;
                     this.startAutoPlay();
                 }
             }"
             @mouseenter="stopAutoPlay()"
             @mouseleave="startAutoPlay()"
             @keydown.right.window="nextSlide()"
             @keydown.left.window="prevSlide()">

        @foreach($activeSliders as $idx => $slide)
            <!-- Slide {{ $idx + 1 }} Background Image -->
            <div x-show="activeSlide === {{ $idx + 1 }}" 
                 x-transition:enter="transition ease-out duration-1000"
                 x-transition:enter-start="opacity-0 scale-105"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-700"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute inset-0 z-0" @if($idx > 0) x-cloak @endif>
                @php
                    $slideImg = $slide['image'] ?? '';
                    if ($slideImg && !\Illuminate\Support\Str::startsWith($slideImg, 'http') && !\Illuminate\Support\Str::startsWith($slideImg, 'data:')) {
                        $slideImg = asset('storage/' . $slideImg);
                    }
                @endphp
                <img src="{{ $slideImg }}" 
                      alt="{{ $slide['title'] }}" 
                      loading="lazy" 
                      class="w-full h-full object-cover animate-kenburns {{ $idx === 0 ? 'opacity-100' : 'opacity-90' }}">
                @if($idx !== 0)
                    <div class="absolute inset-0 bg-gradient-to-r from-slate-950/80 via-slate-950/40 to-transparent"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
                @endif
            </div>
        @endforeach

        <!-- Hero Slider Content Container -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 py-16 w-full">
            <div class="max-w-2xl grid grid-cols-1">
                
                @foreach($activeSliders as $idx => $slide)
                    <!-- Slide {{ $idx + 1 }} Text -->
                    <div x-show="activeSlide === {{ $idx + 1 }}" 
                         x-transition:enter="transition ease-out duration-700 delay-200"
                         x-transition:enter-start="opacity-0 translate-y-6"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-4"
                         class="col-start-1 row-start-1 space-y-4" @if($idx > 0) x-cloak @endif>
                        @if($idx !== 0)
                            <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-amber-500/20 border border-amber-400/40 text-amber-300 text-xs font-extrabold uppercase tracking-wider">
                                <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                                <span>SUPA University Admissions</span>
                            </div>
                        @endif

                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white leading-tight">
                            {{ $slide['title'] }}
                        </h1>

                        <p class="text-sm sm:text-base text-slate-200 max-w-lg leading-relaxed font-normal">
                            {{ $slide['subtitle'] }}
                        </p>

                        <div class="flex flex-wrap items-center gap-3 pt-2">
                            @php
                                $ctaText = $slide['cta'] ?? 'Apply Now';
                                $ctaLink = $slide['cta_link'] ?? route('register');
                                if (!str_starts_with($ctaLink, 'http') && !str_starts_with($ctaLink, '/')) {
                                    try {
                                        $ctaLink = route($ctaLink);
                                    } catch (\Exception $e) {
                                        $ctaLink = url($ctaLink);
                                    }
                                }

                                $secText = $slide['secondary_cta'] ?? 'Explore Programmes';
                                $secLink = $slide['secondary_cta_link'] ?? route('public.programmes');
                                if (!str_starts_with($secLink, 'http') && !str_starts_with($secLink, '/')) {
                                    try {
                                        $secLink = route($secLink);
                                    } catch (\Exception $e) {
                                        $secLink = url($secLink);
                                    }
                                }
                            @endphp
                            
                            @if(!empty($ctaText))
                                <a href="{{ $ctaLink }}" class="gradient-btn-gold px-6 py-3 rounded-xl text-slate-950 font-extrabold text-xs shadow-md hover:scale-105 transition-transform flex items-center gap-1.5">
                                    <span>{{ $ctaText }}</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            @endif

                            @if(!empty($secText))
                                <a href="{{ $secLink }}" class="px-6 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 transition-all flex items-center gap-2">
                                    <span>{{ $secText }}</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach

            </div>
        </div>

        <!-- Slider Controls & Progress Indicator Bar -->
        <div class="absolute bottom-8 left-0 right-0 z-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                
                <!-- Progress Line Timer -->
                <div class="w-full sm:w-64 bg-slate-800/80 h-1.5 rounded-full overflow-hidden backdrop-blur-md">
                    <div class="bg-gradient-to-r from-amber-400 to-blue-500 h-full transition-all duration-75" :style="'width: ' + progress + '%'"></div>
                </div>

                <!-- Navigation Arrows & Dots -->
                <div class="flex items-center space-x-4">
                    <div class="flex space-x-2">
                        <template x-for="i in totalSlides" :key="i">
                            <button @click="goTo(i)" 
                                    class="w-3 h-3 rounded-full transition-all duration-300"
                                    :class="activeSlide === i ? 'bg-amber-400 w-8' : 'bg-slate-600 hover:bg-slate-400'"></button>
                        </template>
                    </div>

                    <div class="flex items-center space-x-2">
                        <button @click="prevSlide()" class="p-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white border border-white/20 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button @click="nextSlide()" class="p-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white border border-white/20 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </section>



    <!-- 2. Featured Programmes Showcase -->
    <section class="py-24 bg-white"
             x-data="{
                 detailsModal: false,
                 activeProg: null,
                 openDetails(prog) {
                     this.activeProg = prog;
                     this.detailsModal = true;
                 },
                 closeDetails() {
                     this.detailsModal = false;
                 }
             }"
             @keydown.escape.window="closeDetails()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-16 gap-6">
                <div>
                    <span class="text-xs font-extrabold text-amber-500 uppercase tracking-widest">Academic Offerings</span>
                    <h2 class="section-title text-slate-900 mt-1">Our Programmes</h2>
                </div>
                <a href="{{ route('public.programmes') }}" class="gradient-btn px-6 py-3 rounded-2xl font-bold text-xs shadow-md">View All Programmes &rarr;</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($programmes as $prog)
                    @php
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

                        $progData = [
                            'id' => $prog->id,
                            'name' => $prog->name,
                            'code' => $prog->code,
                            'duration_years' => $prog->duration_years,
                            'department' => $prog->department ?? 'Department of Educational Studies',
                            'faculty' => $prog->faculty ?? 'Faculty of Education',
                            'description' => $prog->description,
                            'entry_requirements' => $prog->entry_requirements,
                            'annual_fee' => (float)$prog->annual_fee,
                            'monthly_fee' => (float)($prog->monthly_fee ?? ($prog->annual_fee / 10)),
                            'application_fee' => 20000,
                            'image' => $photoUrl,
                            'apply_url' => route('applicant.wizard') . '?programme_id=' . $prog->id,
                        ];
                    @endphp
                    <div class="bg-white rounded-3xl overflow-hidden border border-slate-200 shadow-xl card-hover-effect flex flex-col justify-between group">
                        
                        <!-- Modern Photo Header -->
                        <div class="relative h-48 overflow-hidden cursor-pointer" @click="openDetails({{ json_encode($progData) }})">
                            <img src="{{ $photoUrl }}" 
                                 alt="{{ $prog->name }}" 
                                 loading="lazy" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent"></div>
                            
                            <!-- Floating Badges Over Photo -->
                            <div class="absolute top-3.5 left-3.5 right-3.5 flex justify-between items-center">
                                <span class="px-3 py-1 rounded-full text-[11px] font-extrabold bg-white/90 backdrop-blur-md text-amber-500 border border-amber-500/30 shadow-md">
                                    {{ $prog->code }}
                                </span>
                                <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-white/90 backdrop-blur-md text-slate-900 border border-slate-200/80 shadow-md">
                                    {{ $prog->duration_years }} Years
                                </span>
                            </div>

                            <div class="absolute bottom-3 left-4">
                                <span class="text-[10px] font-extrabold uppercase text-slate-200 tracking-wider">
                                    {{ $prog->department ?? 'Academic Programme' }}
                                </span>
                            </div>
                        </div>

                        <!-- Card Body Details -->
                        <div class="p-6 space-y-4 flex-grow flex flex-col justify-between">
                            <div class="space-y-2">
                                <h3 class="card-title text-slate-900 group-hover:text-amber-500 transition-colors line-clamp-2 cursor-pointer"
                                    @click="openDetails({{ json_encode($progData) }})">
                                    {{ $prog->name }}
                                </h3>

                                <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed">
                                    {{ $prog->description }}
                                </p>
                            </div>

                            <div class="pt-4 border-t border-slate-100 space-y-3">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-slate-500 font-bold uppercase text-[10px]">Tuition Fee</span>
                                    <span class="text-sm font-extrabold text-blue-800">TZS {{ number_format($prog->annual_fee) }}</span>
                                </div>

                                <button type="button"
                                        @click="openDetails({{ json_encode($progData) }})"
                                        class="gradient-btn-gold block w-full text-center py-3 rounded-2xl text-slate-950 font-black text-xs shadow-md hover:scale-105 transition-transform">
                                    Apply & View Details &rarr;
                                </button>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>

        <!-- Programme Information & Application Modal -->
        <div x-show="detailsModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[9995] overflow-y-auto" 
             role="dialog" 
             aria-modal="true" 
             x-cloak>
            
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-950/75 backdrop-blur-md transition-opacity" 
                 @click="closeDetails()"></div>

            <!-- Modal Wrapper -->
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-6">
                <div x-show="detailsModal" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     @click.stop
                     class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-3xl border border-slate-200 max-h-[90vh] flex flex-col">
                    
                    <template x-if="activeProg">
                        <div class="flex flex-col h-full overflow-hidden">
                            <!-- Modal Header Image & Badges -->
                            <div class="relative h-48 sm:h-56 shrink-0 overflow-hidden bg-slate-900">
                                <img :src="activeProg.image" :alt="activeProg.name" class="w-full h-full object-cover opacity-80">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/50 to-transparent"></div>
                                
                                <button type="button" 
                                        @click="closeDetails()" 
                                        class="absolute top-4 right-4 p-2.5 rounded-full bg-slate-900/80 hover:bg-slate-900 text-white transition-colors focus:outline-none z-10"
                                        title="Funga (Close)">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>

                                <div class="absolute top-4 left-4 flex gap-2">
                                    <span class="px-3.5 py-1 rounded-full text-xs font-black bg-amber-400 text-slate-950 shadow-md" x-text="'Code: ' + activeProg.code"></span>
                                    <span class="px-3.5 py-1 rounded-full text-xs font-extrabold bg-white/90 text-slate-900 shadow-md" x-text="activeProg.duration_years + ' Years Degree'"></span>
                                </div>

                                <div class="absolute bottom-4 left-6 right-6">
                                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-amber-400 block mb-1" x-text="activeProg.faculty + ' • ' + activeProg.department"></span>
                                    <h2 class="text-xl sm:text-2xl font-black text-white leading-tight" x-text="activeProg.name"></h2>
                                </div>
                            </div>

                            <!-- Modal Body Scrollable Content -->
                            <div class="p-6 sm:p-8 overflow-y-auto space-y-6 flex-grow text-xs leading-relaxed text-slate-600">
                                
                                <!-- Summary & Description -->
                                <div class="space-y-2">
                                    <h4 class="font-extrabold text-sm text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                                        <span>📖 Kuhusu Programu Hii (Programme Overview)</span>
                                    </h4>
                                    <p class="text-slate-700 leading-relaxed text-sm" x-text="activeProg.description"></p>
                                </div>

                                <!-- Financials & Fee Structure Card -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-5 rounded-2xl bg-blue-50/70 border border-blue-200">
                                    <div class="space-y-0.5">
                                        <span class="text-[10px] font-extrabold uppercase text-blue-900 tracking-wider">Ada ya Fomu ya Maombi</span>
                                        <div class="text-base font-black text-amber-600">TZS 20,000/=</div>
                                        <span class="text-[10px] text-slate-500 font-semibold">Inalipwa kupitia Control Number</span>
                                    </div>
                                    <div class="space-y-0.5">
                                        <span class="text-[10px] font-extrabold uppercase text-blue-900 tracking-wider">Ada ya Masomo (Mwaka)</span>
                                        <div class="text-base font-black text-blue-950" x-text="'TZS ' + Number(activeProg.annual_fee).toLocaleString() + '/='"></div>
                                        <span class="text-[10px] text-slate-500 font-semibold">Inalipwa kwa awamu</span>
                                    </div>
                                    <div class="space-y-0.5">
                                        <span class="text-[10px] font-extrabold uppercase text-blue-900 tracking-wider">Kiwango cha Mwezi</span>
                                        <div class="text-base font-black text-emerald-700" x-text="'TZS ' + Number(activeProg.monthly_fee).toLocaleString() + '/='"></div>
                                        <span class="text-[10px] text-slate-500 font-semibold">Ndani ya miezi 10 ya masomo</span>
                                    </div>
                                </div>

                                <!-- Entry Requirements Section -->
                                <div class="space-y-2">
                                    <h4 class="font-extrabold text-sm text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                                        <span>🎓 Sifa na Vigezo vya Kujiunga (Entry Criteria)</span>
                                    </h4>
                                    <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-slate-800 space-y-2">
                                        <p class="font-bold text-slate-900" x-text="activeProg.entry_requirements"></p>
                                        <ul class="list-disc list-inside space-y-1 text-slate-700 pt-1">
                                            <li><strong>Wenye Diploma:</strong> GPA 3.0+ wanastahili Direct Entry. Wenye GPA 2.0–2.9 hupata fursa kupitia Foundation Programme.</li>
                                            <li><strong>Wenye Kidato cha 6 (ACSEE):</strong> Points 5+ (Principal Passes mbili) kwa Direct Entry.</li>
                                            <li><strong>Kidato cha 4 (CSEE):</strong> Ufaulu wa angalau D nne kwenye masomo husika.</li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Required Checklist Before Applying -->
                                <div class="space-y-2">
                                    <h4 class="font-extrabold text-sm text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                                        <span>📋 Mahitaji Muhimu Wakati wa Kujaza Fomu:</span>
                                    </h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-slate-700">
                                        <div class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                                            <span class="text-emerald-600 font-bold">✓</span>
                                            <span>Kitambulisho cha NIDA / Kura / Kazi</span>
                                        </div>
                                        <div class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                                            <span class="text-emerald-600 font-bold">✓</span>
                                            <span>Cheti cha Kidato cha 4 & 6 au Diploma</span>
                                        </div>
                                        <div class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                                            <span class="text-emerald-600 font-bold">✓</span>
                                            <span>Picha ya Pasipoti (Background Nyeupe)</span>
                                        </div>
                                        <div class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                                            <span class="text-emerald-600 font-bold">✓</span>
                                            <span>Ada ya Fomu ya Maombi: TZS 20,000/=</span>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Modal Action Footer -->
                            <div class="p-6 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0">
                                <div class="flex items-center gap-2 w-full sm:w-auto">
                                    <a href="{{ route('public.admission-steps-guide') }}" target="_blank" class="px-4 py-3 rounded-2xl bg-white border border-slate-300 hover:bg-slate-100 text-slate-800 font-bold text-xs shadow-sm transition-colors text-center w-full sm:w-auto flex items-center justify-center gap-1.5">
                                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>Mwongozo wa Hatua (PDF)</span>
                                    </a>
                                </div>

                                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                                    <button type="button" @click="closeDetails()" class="px-5 py-3 rounded-2xl bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 font-bold text-xs shadow-sm transition-colors">
                                        Funga
                                    </button>
                                    <a :href="activeProg.apply_url" class="gradient-btn-gold px-8 py-3.5 rounded-2xl text-slate-950 font-black text-xs shadow-lg hover:scale-105 active:scale-95 transition-all text-center flex items-center justify-center gap-1.5 w-full sm:w-auto">
                                        <span>Anza Kujaza Maombi ya Programu Hii &rarr;</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. About University Section -->
    <section class="py-24 bg-[#F8FAFC] relative border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <div class="lg:col-span-6 space-y-6">
                    <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/30 text-blue-600 text-xs font-extrabold uppercase tracking-wider">
                        <span>{{ \App\Models\Setting::get('about_badge', 'Open & Distance Learning Excellence') }}</span>
                    </div>

                    <h2 class="section-title text-slate-900 font-extrabold">
                        {{ \App\Models\Setting::get('about_title', 'Leading the Future of Higher Distance Learning in Africa.') }}
                    </h2>

                    <p class="body-text text-slate-600 leading-relaxed">
                        {{ \App\Models\Setting::get('about_description', 'The SUPA / OUT Admission Portal is designed to provide qualified candidates across East Africa and globally with seamless, transparent access to accredited academic qualifications.') }}
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-2">
                            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center font-bold">01</div>
                            <h4 class="font-bold text-slate-900 text-base">Our Mission</h4>
                            <p class="text-xs text-slate-500">{{ \App\Models\Setting::get('about_mission', 'To expand accessible higher education through innovative digital technologies.') }}</p>
                        </div>
                        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-2">
                            <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center font-bold">02</div>
                            <h4 class="font-bold text-slate-900 text-base">Our Vision</h4>
                            <p class="text-xs text-slate-500">{{ \App\Models\Setting::get('about_vision', 'To be a premier global institution in open & distance university education.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-6">
                    <div class="relative">
                        <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white">
                            @php
                                $campusImg = \App\Models\Setting::get('about_campus_image');
                                $campusSrc = $campusImg ? asset('storage/' . $campusImg) : 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=1200&auto=format&fit=crop';
                            @endphp
                            <img src="{{ $campusSrc }}" 
                                 alt="University Campus Life" 
                                 loading="lazy" 
                                 class="w-full h-[440px] object-cover">
                        </div>
                        
                        <!-- Floating Accent Glass Widget -->
                        <div class="absolute -bottom-8 -left-8 bg-white/90 text-white p-6 rounded-3xl backdrop-blur-xl border border-slate-200 shadow-2xl max-w-xs hidden sm:block">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-2xl bg-amber-500 text-slate-950 font-black text-xl flex items-center justify-center">QR</div>
                                <div>
                                    <h4 class="font-extrabold text-sm text-white">Instant Verification</h4>
                                    <p class="text-[11px] text-slate-500">{{ \App\Models\Setting::get('about_verification_text', 'QR-verified official admission letters') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    @if(\App\Models\Setting::get('show_news_announcements', true) && isset($news) && count($news) > 0)
        <!-- 6.5 Latest Announcements Section -->
        <section class="py-24 bg-slate-900 text-white relative overflow-hidden border-t border-slate-800">
            <div class="absolute inset-0 opacity-5 bg-[radial-gradient(#3b82f6_1px,transparent_1px)] [background-size:24px_24px]"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 space-y-16">
                
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                    <div>
                        <span class="text-xs font-extrabold text-amber-400 uppercase tracking-widest bg-amber-500/10 px-3 py-1 rounded-full">Portal Bulletins & Circulars</span>
                        <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-3">Latest Announcements</h2>
                    </div>
                    <a href="{{ route('public.news') }}" class="gradient-btn px-6 py-3 rounded-2xl font-bold text-xs shadow-md text-white border border-slate-700 hover:bg-slate-800">View All News &rarr;</a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($news as $item)
                        @php
                            $coverImg = $item->image_path;
                            if ($coverImg && !\Illuminate\Support\Str::startsWith($coverImg, 'http')) {
                                $coverImg = asset('storage/' . $coverImg);
                            }
                            if (!$coverImg) {
                                $coverImg = 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?q=80&w=800';
                            }
                        @endphp
                        <div class="bg-slate-950/80 border border-slate-800 rounded-3xl p-6 shadow-2xl hover:shadow-indigo-500/10 transition-all duration-300 flex flex-col justify-between group hover:-translate-y-1">
                            <div class="space-y-4">
                                <div class="relative h-44 overflow-hidden rounded-2xl bg-slate-900 shrink-0 border border-slate-800">
                                    <img src="{{ $coverImg }}" 
                                         alt="{{ $item->title }}" 
                                         loading="lazy" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                </div>
                                <div class="space-y-2">
                                    <span class="text-[10px] font-extrabold text-amber-400 uppercase tracking-widest block bg-amber-500/10 w-max px-2.5 py-1 rounded-lg">
                                        {{ $item->published_at ? $item->published_at->format('M d, Y') : 'Notice' }}
                                    </span>
                                    <h3 class="font-extrabold text-white text-base group-hover:text-blue-400 transition-colors leading-snug line-clamp-2">
                                        {{ $item->title }}
                                    </h3>
                                    <p class="text-xs text-slate-400 leading-relaxed line-clamp-3">
                                        {{ $item->summary }}
                                    </p>
                                </div>
                            </div>
                            <div class="pt-4 mt-6 border-t border-slate-900 flex justify-between items-center text-xs">
                                <a href="{{ route('public.news.show', $item->slug) }}" class="font-extrabold text-blue-400 hover:underline flex items-center gap-1">
                                    <span>Read Full Notice</span>
                                    <span>&rarr;</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </section>
    @endif

    <!-- 7. High Impact Admission CTA Banner -->
    <section class="py-24 text-white relative overflow-hidden bg-slate-950">
        <!-- Background Campus Image -->
        <div class="absolute inset-0 z-0">
            @php
                $ctaBg = \App\Models\Setting::get('cta_background_image');
                $ctaBgUrl = $ctaBg ? (\Illuminate\Support\Str::startsWith($ctaBg, ['http://', 'https://', 'data:']) ? $ctaBg : asset('storage/' . $ctaBg)) : 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=2070';
            @endphp
            <img src="{{ $ctaBgUrl }}" class="w-full h-full object-cover opacity-45 transform scale-105" alt="University Campus">
        </div>
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-tr from-slate-950/90 via-blue-950/70 to-slate-950/90 z-5"></div>
        <!-- Decorative Glow -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[300px] bg-amber-500/10 blur-[120px] rounded-full pointer-events-none z-5"></div>

        <div class="max-w-5xl mx-auto px-4 text-center space-y-8 relative z-10">
            <span class="px-4 py-1.5 rounded-full bg-amber-500/20 border border-amber-500/40 text-amber-400 text-xs font-extrabold uppercase tracking-widest inline-block">
                {{ \App\Models\Setting::get('cta_badge', 'Academic Cycle 2026 / 2027') }}
            </span>

            <h2 class="hero-title font-extrabold tracking-tight text-white">
                {{ \App\Models\Setting::get('cta_title', 'Ready to Begin Your Academic Journey?') }}
            </h2>

            <p class="body-text text-blue-100 max-w-2xl mx-auto leading-relaxed">
                {{ \App\Models\Setting::get('cta_description', 'Take the first step towards securing your university degree. Submit your application online today in less than 10 minutes.') }}
            </p>

            <div class="flex flex-wrap justify-center gap-4 pt-4">
                <a href="{{ route('register') }}" class="gradient-btn-gold px-10 py-4 rounded-2xl text-slate-950 font-extrabold text-base shadow-2xl hover:scale-105 transition-transform">
                    Start Application Now &rarr;
                </a>
                <a href="{{ route('public.track') }}" class="px-8 py-4 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-sm border border-white/20 transition-all">
                    Track Existing Status
                </a>
            </div>
        </div>
    </section>

</x-public-layout>
