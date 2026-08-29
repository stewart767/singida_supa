<x-public-layout title="Create Applicant Account - SUPA University">

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

        <div class="max-w-lg w-full px-4 relative z-10" x-data="{ 
            step: 1, 
            form: { 
                name: '', 
                email: '', 
                phone: '', 
                password: '', 
                password_confirmation: '', 
                flow: '{{ $oldFlow }}' 
            }, 
            error: null, 
            errors: {}, 
            loading: false, 
            showPass: false,
            switchFlow(newFlow) {
                this.form.flow = newFlow;
                this.form.name = '';
                this.form.email = '';
                this.form.phone = '';
                this.form.password = '';
                this.form.password_confirmation = '';
                this.error = null;
                this.errors = {};
                const url = new URL(window.location);
                if (newFlow === 'career') {
                    url.searchParams.set('flow', 'career');
                } else {
                    url.searchParams.delete('flow');
                }
                window.history.pushState({}, '', url);
            }
        }">
            <div class="bg-slate-900/60 backdrop-blur-xl p-8 sm:p-10 rounded-3xl border border-white/10 shadow-[0_8px_32px_0_rgba(0,0,0,0.5)] space-y-6 transition-all duration-300"
                 :class="form.flow === 'career' ? 'hover:border-emerald-500/30' : 'hover:border-indigo-500/30'">
                
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
                    <h2 class="text-2xl font-extrabold text-white tracking-tight" x-text="form.flow === 'career' ? 'Create Candidate Account' : 'Create Applicant Account'">Create Applicant Account</h2>
                    <p class="text-xs text-slate-400 font-medium" x-text="form.flow === 'career' ? 'Step 1 of Job Application: Register your candidate credentials.' : 'Step 1 of Online Admission: Register your student account credentials.'">Step 1 of Online Admission: Register your student account credentials.</p>
                </div>
 
                <div x-show="error" class="p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-bold" x-cloak>
                    <p x-text="error"></p>
                    <template x-for="(msgs, field) in errors" :key="field">
                        <ul class="list-disc list-inside mt-1">
                            <template x-for="msg in msgs" :key="msg">
                                <li x-text="msg"></li>
                            </template>
                        </ul>
                    </template>
                </div>
 
                <!-- Registration Form -->
                <div>
                    <form @submit.prevent="
                        loading = true; error = null; errors = {};
                        axios.post('{{ url('/register') }}', form)
                            .then(res => {
                                loading = false;
                                toast('Account created successfully!', 'success');
                                window.location.href = res.data.redirect_url;
                            })
                            .catch(err => {
                                loading = false;
                                error = err.response?.data?.message || 'Registration failed. Check inputs.';
                                errors = err.response?.data?.errors || {};
                             })
                    " class="space-y-4">

                        <div>
                            <label class="block text-xs font-extrabold text-slate-300 uppercase mb-2">Full Legal Name</label>
                            <input type="text" x-model="form.name" required placeholder="e.g. John Peter Mwangi" 
                                   class="w-full px-4 py-3.5 rounded-2xl border border-white/10 bg-slate-950/60 text-white placeholder-slate-500 text-sm font-semibold outline-none focus:ring-2 transition-all"
                                   :class="form.flow === 'career' ? 'focus:ring-emerald-500 focus:border-emerald-500 hover:border-emerald-500/30' : 'focus:ring-indigo-500 focus:border-indigo-500 hover:border-indigo-500/30'">
                        </div>
 
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-extrabold text-slate-300 uppercase mb-2">Email Address</label>
                                <input type="email" x-model="form.email" required placeholder="e.g. john@gmail.com" 
                                       class="w-full px-4 py-3.5 rounded-2xl border border-white/10 bg-slate-950/60 text-white placeholder-slate-500 text-sm font-semibold outline-none focus:ring-2 transition-all"
                                       :class="form.flow === 'career' ? 'focus:ring-emerald-500 focus:border-emerald-500 hover:border-emerald-500/30' : 'focus:ring-indigo-500 focus:border-indigo-500 hover:border-indigo-500/30'">
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-slate-300 uppercase mb-2">Phone Number</label>
                                <input type="text" x-model="form.phone" required placeholder="e.g. +255711000999" 
                                       class="w-full px-4 py-3.5 rounded-2xl border border-white/10 bg-slate-950/60 text-white placeholder-slate-500 text-sm font-semibold outline-none focus:ring-2 transition-all"
                                       :class="form.flow === 'career' ? 'focus:ring-emerald-500 focus:border-emerald-500 hover:border-emerald-500/30' : 'focus:ring-indigo-500 focus:border-indigo-500 hover:border-indigo-500/30'">
                            </div>
                        </div>
 
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-extrabold text-slate-300 uppercase mb-2">Password</label>
                                <input :type="showPass ? 'text' : 'password'" x-model="form.password" required placeholder="••••••••" 
                                       class="w-full px-4 py-3.5 rounded-2xl border border-white/10 bg-slate-950/60 text-white placeholder-slate-500 text-sm font-semibold outline-none focus:ring-2 transition-all"
                                       :class="form.flow === 'career' ? 'focus:ring-emerald-500 focus:border-emerald-500 hover:border-emerald-500/30' : 'focus:ring-indigo-500 focus:border-indigo-500 hover:border-indigo-500/30'">
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-slate-300 uppercase mb-2">Confirm Password</label>
                                <input :type="showPass ? 'text' : 'password'" x-model="form.password_confirmation" required placeholder="••••••••" 
                                       class="w-full px-4 py-3.5 rounded-2xl border border-white/10 bg-slate-950/60 text-white placeholder-slate-500 text-sm font-semibold outline-none focus:ring-2 transition-all"
                                       :class="form.flow === 'career' ? 'focus:ring-emerald-500 focus:border-emerald-500 hover:border-emerald-500/30' : 'focus:ring-indigo-500 focus:border-indigo-500 hover:border-indigo-500/30'">
                            </div>
                        </div>
 
                        <div class="flex items-center space-x-2 pt-1">
                            <input type="checkbox" id="showPassCheckbox" @click="showPass = !showPass" class="rounded text-blue-800 focus:ring-blue-500">
                            <label for="showPassCheckbox" class="text-xs text-slate-400 font-bold cursor-pointer">Show Password Characters</label>
                        </div>
 
                        <button type="submit" :disabled="loading" class="w-full py-4 rounded-2xl text-white font-extrabold text-sm shadow-xl hover:scale-[1.02] transition-all duration-300"
                                :class="form.flow === 'career' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 hover:shadow-emerald-500/20' : 'bg-gradient-to-r from-indigo-600 to-blue-600 hover:shadow-indigo-500/20'">
                            <span x-show="!loading" x-text="form.flow === 'career' ? 'Create Candidate Account &rarr;' : 'Create Account &rarr;'">Create Account &rarr;</span>
                            <span x-show="loading" x-cloak x-text="form.flow === 'career' ? 'Creating Candidate Account...' : 'Creating Applicant Account...'">Creating Applicant Account...</span>
                        </button>
                    </form>
                </div>

                <div class="text-center pt-4 border-t border-white/5 text-xs space-y-2">
                    <div>
                        <span class="text-slate-400">Already have an account?</span>
                        <a :href="'{{ route('login') }}' + '?flow=' + form.flow" class="text-amber-500 font-extrabold hover:underline ml-1">Sign In &rarr;</a>
                    </div>
                    
                    <button type="button" x-show="form.flow === 'program'" @click="switchFlow('career')" class="text-slate-400 hover:text-white font-extrabold hover:underline block mx-auto mt-2" @if($oldFlow !== 'program') x-cloak @endif>
                        Are you applying for a job vacancy? Create Candidate Account &rarr;
                    </button>
                    
                    <button type="button" x-show="form.flow === 'career'" @click="switchFlow('program')" class="text-slate-400 hover:text-white font-extrabold hover:underline block mx-auto mt-2" @if($oldFlow !== 'career') x-cloak @endif>
                        Are you applying for an academic program? Create Applicant Account &rarr;
                    </button>
                </div>

            </div>
        </div>
    </section>

</x-public-layout>
