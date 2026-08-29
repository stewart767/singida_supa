<x-public-layout title="Track Application Status & Resume Wizard - SUPA University">

    <section class="relative text-white py-20 text-center space-y-4 border-b border-slate-200 bg-cover bg-center bg-no-repeat bg-gradient-to-r from-slate-950 via-blue-950 to-slate-900"
             @if(\App\Models\Setting::get('banner_track')) style="background-image: linear-gradient(to right, rgba(2, 6, 23, 0.95), rgba(30, 58, 138, 0.85)), url('{{ asset('storage/' . \App\Models\Setting::get('banner_track')) }}');" @endif>
        <span class="px-3.5 py-1.5 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-400 text-xs font-extrabold uppercase tracking-wider">
            Real-Time Verification & Resume Portal
        </span>
        <h1 class="hero-title font-extrabold tracking-tight">Track & Continue Application</h1>
        <p class="body-text text-slate-300 max-w-2xl mx-auto text-xs sm:text-sm">
            Enter your Phone Number, Control Number, or Application Number to check selection status or verify identity and resume your admission wizard.
        </p>
    </section>

    <section class="py-20 bg-slate-50" x-data="{ 
        appNumber: '', 
        result: null, 
        error: null, 
        loading: false, 
        otpLoading: false, 
        otpError: null, 
        otpSuccess: null, 
        showOtpForm: false, 
        otpCode: '', 
        otpSent: false 
    }">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl p-8 sm:p-10 shadow-2xl border border-slate-200 space-y-6">
                
                <!-- Query Form -->
                <form @submit.prevent="
                    loading = true; error = null; result = null; showOtpForm = false; otpSent = false; otpCode = ''; otpError = null; otpSuccess = null;
                    axios.post('{{ url('/api/v1/public/track-application') }}', { application_number: appNumber })
                        .then(res => { result = res.data; loading = false; })
                        .catch(err => { error = err.response?.data?.message || 'No active application record was found.'; loading = false; })
                " class="space-y-4">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Phone Number, Control Number, or Application #</label>
                        <div class="relative">
                            <input type="text" x-model="appNumber" required placeholder="e.g., 0712345678 or SUPA-2026-000001" 
                                   class="w-full px-5 py-4 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 font-extrabold text-base focus:ring-2 focus:ring-amber-500 outline-none">
                        </div>
                    </div>

                    <button type="submit" :disabled="loading" class="w-full gradient-btn py-4 rounded-2xl text-white font-extrabold text-sm shadow-xl flex items-center justify-center gap-2">
                        <span x-show="!loading">Search Application &rarr;</span>
                        <span x-show="loading" x-cloak>Checking database...</span>
                    </button>
                </form>

                <!-- Error Alert -->
                <div x-show="error" class="p-4 rounded-2xl bg-red-500/10 border border-red-500/30 text-red-600 text-xs font-bold" x-cloak x-text="error"></div>

                <!-- Result Card -->
                <div x-show="result" class="p-6 rounded-3xl bg-slate-50 border border-slate-200 space-y-6" x-cloak>
                    <div class="flex justify-between items-center border-b border-slate-200 pb-4">
                        <div>
                            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Application Number</span>
                            <span class="text-lg font-black text-blue-900" x-text="result?.application_number"></span>
                        </div>
                        <span class="inline-block px-3 py-1.5 rounded-full text-[10px] font-black uppercase shadow-sm"
                              :class="{ 
                                  'bg-amber-100 text-amber-800 border border-amber-200': result?.status === 'Draft' || result?.status === 'Pending Payment' || result?.status === 'PAYMENT_PENDING' || result?.status === 'IN_PROGRESS', 
                                  'bg-emerald-100 text-emerald-800 border border-emerald-200': result?.status === 'Approved' || result?.status === 'SUBMITTED' || result?.status === 'Under Review', 
                                  'bg-red-100 text-red-800 border border-red-200': result?.status === 'Rejected' || result?.status === 'Expired' 
                              }"
                              x-text="result?.status"></span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-semibold text-slate-700">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold mb-0.5">Chosen Programme</span>
                            <span class="font-extrabold text-slate-900" x-text="result?.programme"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold mb-0.5">Admission Category</span>
                            <span class="font-extrabold text-slate-900" x-text="result?.admission_category"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold mb-0.5">Fee Payment Status</span>
                            <span class="font-black uppercase" :class="result?.payment_status === 'paid' ? 'text-emerald-600' : 'text-amber-500'" x-text="result?.payment_status"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold mb-0.5">Last Step Progress</span>
                            <span class="font-extrabold text-slate-900" x-text="'Step ' + (result?.current_step || 1) + ' (' + (result?.completion_percentage || 0) + '%)'"></span>
                        </div>
                    </div>

                    <!-- Incomplete / Resume Flow -->
                    <template x-if="result?.status === 'Draft' || result?.status === 'IN_PROGRESS' || result?.status === 'Pending Payment' || result?.status === 'PAYMENT_PENDING'">
                        <div class="border-t border-slate-200 pt-6 space-y-4">
                            <div class="p-4 bg-amber-50 rounded-2xl border border-amber-200 text-xs text-amber-900 leading-relaxed font-medium">
                                ⚠️ <strong>Ombi lako halijakamilika.</strong> Unaweza kuendelea kujaza ombi hili kutoka hatua uliyoiacha. Bofya kitufe kilicho chini ili kuendelea na usajili.
                                <br><br>
                                (<strong>Your application is incomplete.</strong> You can resume filling it. Click below to continue with the registration.)
                            </div>

                            <!-- Direct Resume button -->
                            <button @click="
                                otpLoading = true; otpError = null;
                                axios.post('{{ url('/api/v1/public/resume-direct') }}', { application_id: result.application_id, user_id: result.user_id })
                                    .then(res => { window.location.href = res.data.redirect_url; })
                                    .catch(err => { otpError = err.response?.data?.message || 'Failed to resume application. Try again later.'; otpLoading = false; })
                            " :disabled="otpLoading" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-3.5 rounded-2xl text-xs shadow-md transition-all">
                                <span x-show="!otpLoading">🚀 Endelea na Usajili (Resume Registration)</span>
                                <span x-show="otpLoading" x-cloak>Connecting...</span>
                            </button>

                            <!-- Error Message -->
                            <div x-show="otpError" x-cloak class="p-3 bg-red-100 border border-red-200 text-red-800 rounded-xl text-xs font-bold" x-text="otpError"></div>
                        </div>
                    </template>
                </div>

            </div>
        </div>
    </section>

</x-public-layout>
