<x-public-layout title="Academic Programmes Catalog - SUPA University">

    <section class="relative text-white py-20 border-b border-slate-200 bg-cover bg-center bg-no-repeat bg-gradient-to-r from-slate-950 via-blue-950 to-slate-900"
             @if(\App\Models\Setting::get('banner_programmes')) style="background-image: linear-gradient(to right, rgba(2, 6, 23, 0.95), rgba(30, 58, 138, 0.85)), url('{{ asset('storage/' . \App\Models\Setting::get('banner_programmes')) }}');" @endif>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-4">
            <span class="px-3.5 py-1.5 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-400 text-xs font-extrabold uppercase tracking-wider">
                Official 2026/2027 Prospectus
            </span>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight">Academic Programmes Catalog</h1>
            <p class="body-text text-slate-300 max-w-2xl mx-auto">
                Discover accredited undergraduate degrees, postgraduate diplomas, and foundation courses designed for flexible distance learning.
            </p>
        </div>
    </section>

    <section class="py-20 bg-white" 
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
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
                        
                        <!-- Photo Header -->
                        <div class="relative h-56 overflow-hidden cursor-pointer" @click="openDetails({{ json_encode($progData) }})">
                            <img src="{{ $photoUrl }}" 
                                 alt="{{ $prog->name }}" 
                                 loading="lazy" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent"></div>
                            
                            <!-- Badges -->
                            <div class="absolute top-4 left-4 right-4 flex justify-between items-center">
                                <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-white/90 backdrop-blur-md text-amber-700 border border-amber-500/30 shadow-md">
                                    Code: {{ $prog->code }}
                                </span>
                                <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-white/90 backdrop-blur-md text-slate-900 border border-slate-200/80 shadow-md">
                                    Duration: {{ $prog->duration_years }} Years
                                </span>
                            </div>

                            <div class="absolute bottom-3 left-5">
                                <span class="text-xs font-extrabold uppercase text-slate-200 tracking-wider">
                                    {{ $prog->department }} | {{ $prog->faculty }}
                                </span>
                            </div>
                        </div>

                        <!-- Details -->
                        <div class="p-8 space-y-6 flex-grow flex flex-col justify-between">
                            <div class="space-y-4">
                                <h3 class="card-title text-slate-900 text-2xl group-hover:text-amber-500 transition-colors cursor-pointer"
                                    @click="openDetails({{ json_encode($progData) }})">
                                    {{ $prog->name }}
                                </h3>

                                <p class="text-xs text-slate-600 leading-relaxed">{{ $prog->description }}</p>

                                <div class="bg-slate-50 p-5 rounded-2xl space-y-2 border border-slate-200">
                                    <span class="text-xs font-extrabold text-slate-800 uppercase tracking-wider block">Entry Requirements:</span>
                                    <p class="text-xs text-slate-600 leading-relaxed">{{ $prog->entry_requirements }}</p>
                                </div>
                            </div>

                            <div class="pt-6 border-t border-slate-200 flex items-center justify-between gap-4">
                                <div>
                                    <span class="text-[10px] text-slate-500 font-extrabold uppercase block">Ada ya Masomo (Annual)</span>
                                    <span class="text-xl font-extrabold text-blue-800">TZS {{ number_format($prog->annual_fee) }}</span>
                                </div>
                                <button type="button"
                                        @click="openDetails({{ json_encode($progData) }})"
                                        class="gradient-btn-gold px-7 py-3.5 rounded-2xl text-slate-950 font-black text-xs shadow-lg hover:scale-105 transition-transform flex items-center gap-1.5 shrink-0">
                                    <span>Apply & View Details</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
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
                                    <div class="col-span-full pt-3 border-t border-blue-200/50 flex flex-col sm:flex-row justify-between items-center gap-2 text-[11px] font-bold text-blue-900">
                                        <span>Tayari umeanza kuomba? (Already started?)</span>
                                        <a href="{{ route('public.track') }}" class="text-amber-600 hover:text-amber-700 hover:underline flex items-center gap-1">
                                            Fuatilia kwa Control au Simu (Track & Continue) &rarr;
                                        </a>
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
                                    <a href="{{ route('public.track') }}" class="px-5 py-3 rounded-2xl bg-blue-50 border border-blue-200 hover:bg-blue-100 text-blue-700 font-bold text-xs shadow-sm transition-colors text-center flex items-center justify-center gap-1.5 w-full sm:w-auto">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                        <span>Fuatilia Maombi (Track Status)</span>
                                    </a>
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

</x-public-layout>
