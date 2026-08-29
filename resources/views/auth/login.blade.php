<x-public-layout title="Applicant & Staff Login - SUPA University">

    <style>
        @keyframes float-slow-1 {
            0%, 100% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.95); }
        }
        @keyframes float-slow-2 {
            0%, 100% { transform: translate(0px, 0px) scale(1.1); }
            50% { transform: translate(-40px, 40px) scale(0.9); }
        }
        @keyframes spin-slow {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
        .animate-float-1 {
            animation: float-slow-1 15s ease-in-out infinite;
        }
        .animate-float-2 {
            animation: float-slow-2 18s ease-in-out infinite;
        }
        .animate-spin-slow {
            animation: spin-slow 30s linear infinite;
        }
    </style>

    @php
        $bgImage = \App\Models\Setting::get('login_background_image');
        $oldFlow = old('flow', request()->query('flow', 'program'));
    @endphp

    <section class="relative py-20 flex items-center justify-center min-h-[90vh] overflow-hidden bg-slate-950">
        @if($bgImage)
            <!-- Managed Background Image -->
            <div class="absolute inset-0 bg-cover bg-center opacity-50 transition-all duration-700" style="background-image: url('{{ asset('storage/' . $bgImage) }}')"></div>
        @endif

        <!-- Floating Animated Background Orbs -->
        <div class="absolute -top-20 -left-20 w-96 h-96 rounded-full bg-blue-600/20 blur-[120px] animate-float-1"></div>
        <div class="absolute -bottom-20 -right-20 w-[450px] h-[450px] rounded-full bg-amber-500/15 blur-[140px] animate-float-2"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] rounded-full bg-gradient-to-br from-indigo-600/10 via-transparent to-amber-600/10 blur-[160px] animate-spin-slow"></div>

        <!-- Custom Grid overlay -->
        <div class="absolute inset-0 opacity-20 bg-[linear-gradient(to_right,#334155_1px,transparent_1px),linear-gradient(to_bottom,#334155_1px,transparent_1px)] bg-[size:4rem_4rem]"></div>

        <div class="max-w-md w-full px-4 relative z-10" x-data="{ 
            flow: '{{ $oldFlow }}', 
            email: '{{ old('email', '') }}', 
            password: '', 
            showPass: false,
            switchFlow(newFlow) {
                this.flow = newFlow;
                this.email = '';
                this.password = '';
                const url = new URL(window.location);
                if (newFlow === 'career') {
                    url.searchParams.set('flow', 'career');
                } else {
                    url.searchParams.delete('flow');
                }
                window.history.pushState({}, '', url);
            }
        }">
            <!-- Academic Programs Card -->
            <div x-show="flow === 'program'" @if($oldFlow !== 'program') x-cloak @endif x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 class="bg-slate-900/60 backdrop-blur-xl p-8 sm:p-10 rounded-3xl border border-white/10 shadow-[0_8px_32px_0_rgba(0,0,0,0.5)] space-y-6 hover:border-indigo-500/30 transition-colors duration-300">
                
                <div class="text-center space-y-3">
                    @if(\App\Models\Setting::get('system_logo'))
                        <div class="w-16 h-16 rounded-2xl bg-white border border-slate-200/20 flex items-center justify-center mx-auto shadow-xl overflow-hidden p-1">
                            <img src="{{ asset('storage/' . \App\Models\Setting::get('system_logo')) }}" alt="System Logo" class="w-full h-full object-contain">
                        </div>
                    @else
                        <div class="flex items-center justify-center space-x-2">
                            <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200/20 flex items-center justify-center shadow-lg p-1 overflow-hidden shrink-0">
                                @if(\App\Models\Setting::get('sttc_logo'))
                                    <img src="{{ asset('storage/' . \App\Models\Setting::get('sttc_logo')) }}" alt="STTC Logo" class="w-full h-full object-contain">
                                @else
                                    <span class="text-slate-900 font-extrabold text-[10px]">STTC</span>
                                @endif
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200/20 flex items-center justify-center shadow-lg p-1 overflow-hidden shrink-0">
                                @if(\App\Models\Setting::get('out_logo'))
                                    <img src="{{ asset('storage/' . \App\Models\Setting::get('out_logo')) }}" alt="OUT Logo" class="w-full h-full object-contain">
                                @else
                                    <span class="text-blue-900 font-extrabold text-[10px]">OUT</span>
                                @endif
                            </div>
                        </div>
                    @endif
                    <h2 class="text-2xl font-extrabold text-white tracking-tight">Academic Admission Login</h2>
                    <p class="text-xs text-slate-400 font-medium">Access your student applicant dashboard or administrative desk.</p>
                </div>

                <!-- Seeded Test Accounts Quick Fill Buttons -->
                <div class="p-4 rounded-2xl bg-slate-900/80 border border-white/10 text-xs space-y-2 text-slate-300">
                    <span class="font-extrabold text-amber-500 block uppercase text-[10px] tracking-wider">Quick Fill Test Credentials:</span>
                    <div class="flex flex-wrap gap-2 pt-1">
                        <button type="button" @click="email = 'admin@supa.ac.tz'; password = 'Password123!'" 
                                class="px-3 py-1.5 rounded-xl bg-amber-500/20 hover:bg-amber-500 hover:text-slate-950 text-amber-400 text-[11px] font-extrabold transition-all border border-amber-500/30">
                            Super Admin
                        </button>
                        <button type="button" @click="email = 'admission@supa.ac.tz'; password = 'Password123!'" 
                                class="px-3 py-1.5 rounded-xl bg-blue-500/20 hover:bg-blue-500 hover:text-white text-blue-400 text-[11px] font-extrabold transition-all border border-blue-500/30">
                            Admission Officer
                        </button>
                        <button type="button" @click="email = 'applicant1@supa.ac.tz'; password = 'Password123!'" 
                                class="px-3 py-1.5 rounded-xl bg-emerald-500/20 hover:bg-emerald-500 hover:text-white text-emerald-400 text-[11px] font-extrabold transition-all border border-emerald-500/30">
                            Applicant User
                        </button>
                    </div>
                </div>

                @if($errors->any())
                    <div class="p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-bold animate-pulse">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Form -->
                <form action="{{ url('/login') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="flow" value="program">

                    <div>
                        <label class="block text-xs font-extrabold text-slate-300 uppercase mb-2">Email Address or Phone Number</label>
                        <input type="text" name="email" x-model="email" required placeholder="e.g. applicant1@supa.ac.tz" 
                               class="w-full px-4 py-3 rounded-2xl border border-white/10 bg-slate-950/60 text-white placeholder-slate-600 text-sm font-semibold outline-none focus:ring-2 focus:ring-indigo-500 transition-all focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-300 uppercase mb-2">Password</label>
                        <div class="relative">
                            <input :type="showPass ? 'text' : 'password'" name="password" x-model="password" required placeholder="••••••••" 
                                   class="w-full px-4 py-3 rounded-2xl border border-white/10 bg-slate-950/60 text-white placeholder-slate-600 text-sm font-semibold outline-none focus:ring-2 focus:ring-indigo-500 transition-all focus:border-indigo-500 pr-12">
                            <button type="button" @click="showPass = !showPass" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-white text-xs font-bold transition-colors">
                                <span x-text="showPass ? 'Hide' : 'Show'"></span>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-extrabold text-sm shadow-xl hover:shadow-indigo-500/20 hover:scale-[1.02] transition-all duration-300">
                        Sign In to Program Portal &rarr;
                    </button>
                </form>

                <!-- Footer Link -->
                <div class="text-center pt-6 border-t border-white/5 text-xs">
                    <div class="mb-2">
                        <span class="text-slate-400">Don't have an academic account?</span>
                        <a href="{{ route('register') }}?flow=program" class="text-indigo-400 font-extrabold hover:underline ml-1">Create Account &rarr;</a>
                    </div>
                    <button type="button" @click="switchFlow('career')" class="text-amber-500 font-extrabold hover:underline block mx-auto mt-3">
                        Are you applying for a job vacancy? Go to Careers Login &rarr;
                    </button>
                </div>
            </div>

            <!-- Job Careers Card -->
            <div x-show="flow === 'career'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" @if($oldFlow !== 'career') x-cloak @endif
                 class="bg-slate-900/60 backdrop-blur-xl p-8 sm:p-10 rounded-3xl border border-white/10 shadow-[0_8px_32px_0_rgba(0,0,0,0.5)] space-y-6 hover:border-emerald-500/30 transition-colors duration-300">
                
                <div class="text-center space-y-3">
                    @if(\App\Models\Setting::get('system_logo'))
                        <div class="w-16 h-16 rounded-2xl bg-white border border-slate-200/20 flex items-center justify-center mx-auto shadow-xl overflow-hidden p-1">
                            <img src="{{ asset('storage/' . \App\Models\Setting::get('system_logo')) }}" alt="System Logo" class="w-full h-full object-contain">
                        </div>
                    @else
                        <div class="flex items-center justify-center space-x-2">
                            <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200/20 flex items-center justify-center shadow-lg p-1 overflow-hidden shrink-0">
                                @if(\App\Models\Setting::get('sttc_logo'))
                                    <img src="{{ asset('storage/' . \App\Models\Setting::get('sttc_logo')) }}" alt="STTC Logo" class="w-full h-full object-contain">
                                @else
                                    <span class="text-slate-900 font-extrabold text-[10px]">STTC</span>
                                @endif
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200/20 flex items-center justify-center shadow-lg p-1 overflow-hidden shrink-0">
                                @if(\App\Models\Setting::get('out_logo'))
                                    <img src="{{ asset('storage/' . \App\Models\Setting::get('out_logo')) }}" alt="OUT Logo" class="w-full h-full object-contain">
                                @else
                                    <span class="text-blue-900 font-extrabold text-[10px]">OUT</span>
                                @endif
                            </div>
                        </div>
                    @endif
                    <h2 class="text-2xl font-extrabold text-white tracking-tight">Careers Portal Login</h2>
                    <p class="text-xs text-slate-400 font-medium">Access your candidate portal to apply for and track jobs.</p>
                </div>

                <!-- Seeded Test Accounts Quick Fill Buttons -->
                <div class="p-4 rounded-2xl bg-slate-900/80 border border-white/10 text-xs space-y-2 text-slate-300">
                    <span class="font-extrabold text-amber-500 block uppercase text-[10px] tracking-wider">Quick Fill Test Credentials:</span>
                    <div class="flex flex-wrap gap-2 pt-1">
                        <button type="button" @click="email = 'admin@supa.ac.tz'; password = 'Password123!'" 
                                class="px-3 py-1.5 rounded-xl bg-amber-500/20 hover:bg-amber-500 hover:text-slate-950 text-amber-400 text-[11px] font-extrabold transition-all border border-amber-500/30">
                            Super Admin
                        </button>
                        <button type="button" @click="email = 'admission@supa.ac.tz'; password = 'Password123!'" 
                                class="px-3 py-1.5 rounded-xl bg-blue-500/20 hover:bg-blue-500 hover:text-white text-blue-400 text-[11px] font-extrabold transition-all border border-blue-500/30">
                            Admission Officer
                        </button>
                        <button type="button" @click="email = 'applicant1@supa.ac.tz'; password = 'Password123!'" 
                                class="px-3 py-1.5 rounded-xl bg-emerald-500/20 hover:bg-emerald-500 hover:text-white text-emerald-400 text-[11px] font-extrabold transition-all border border-emerald-500/30">
                            Applicant User
                        </button>
                    </div>
                </div>

                @if($errors->any())
                    <div class="p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-bold animate-pulse">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Form -->
                <form action="{{ url('/login') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="flow" value="career">

                    <div>
                        <label class="block text-xs font-extrabold text-slate-300 uppercase mb-2">Email Address or Phone Number</label>
                        <input type="text" name="email" x-model="email" required placeholder="e.g. applicant1@supa.ac.tz" 
                               class="w-full px-4 py-3 rounded-2xl border border-white/10 bg-slate-950/60 text-white placeholder-slate-600 text-sm font-semibold outline-none focus:ring-2 focus:ring-emerald-500 transition-all focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-300 uppercase mb-2">Password</label>
                        <div class="relative">
                            <input :type="showPass ? 'text' : 'password'" name="password" x-model="password" required placeholder="••••••••" 
                                   class="w-full px-4 py-3 rounded-2xl border border-white/10 bg-slate-950/60 text-white placeholder-slate-600 text-sm font-semibold outline-none focus:ring-2 focus:ring-emerald-500 transition-all focus:border-emerald-500 pr-12">
                            <button type="button" @click="showPass = !showPass" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-white text-xs font-bold transition-colors">
                                <span x-text="showPass ? 'Hide' : 'Show'"></span>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-extrabold text-sm shadow-xl hover:shadow-emerald-500/20 hover:scale-[1.02] transition-all duration-300">
                        Sign In to Careers Portal &rarr;
                    </button>
                </form>

                <!-- Footer Link -->
                <div class="text-center pt-6 border-t border-white/5 text-xs">
                    <div class="mb-2">
                        <span class="text-slate-400">Don't have a candidate account?</span>
                        <a href="{{ route('register') }}?flow=career" class="text-emerald-400 font-extrabold hover:underline ml-1">Create Account &rarr;</a>
                    </div>
                    <button type="button" @click="switchFlow('program')" class="text-amber-500 font-extrabold hover:underline block mx-auto mt-3">
                        Are you applying for an academic program? Go to Admission Login &rarr;
                    </button>
                </div>
            </div>
        </div>
    </section>

</x-public-layout>
