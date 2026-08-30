<x-public-layout title="Fuatilia Usajili / Track Application Status - SUPA University">

    <section class="relative text-white py-20 text-center space-y-4 border-b border-slate-200 bg-cover bg-center bg-no-repeat bg-gradient-to-r from-slate-950 via-blue-950 to-slate-900"
             @if(\App\Models\Setting::get('banner_track')) style="background-image: linear-gradient(to right, rgba(2, 6, 23, 0.95), rgba(30, 58, 138, 0.85)), url('{{ asset('storage/' . \App\Models\Setting::get('banner_track')) }}');" @endif>
        <span class="px-4 py-2 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-400 text-xs font-black uppercase tracking-wider inline-flex items-center gap-1.5 shadow-inner">
            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
            Real-Time Verification & Resume Portal
        </span>
        <h1 class="hero-title font-black tracking-tight text-white drop-shadow-sm">Fuatilia & Endelea na Usajili</h1>
        <p class="body-text text-slate-300 max-w-2xl mx-auto text-xs sm:text-sm px-4">
            Track Application Status & Resume Admissions Wizard
        </p>
    </section>

    <section class="py-16 bg-slate-50" x-data="{ 
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Search & Status Section -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-200 relative overflow-hidden">
                        <!-- Top Accent Bar -->
                        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-amber-500 to-blue-600"></div>

                        <!-- Instructions Alert Box -->
                        <div class="p-4 mb-6 rounded-2xl bg-blue-50 border border-blue-100 text-blue-900 text-xs sm:text-sm font-semibold leading-relaxed flex gap-3 items-start">
                            <div class="p-1.5 rounded-xl bg-blue-600 text-white mt-0.5 shadow-md flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="space-y-1">
                                <p class="text-blue-950 text-sm font-bold">Maelekezo / Instructions:</p>
                                <p class="text-blue-900 font-extrabold text-[13px] sm:text-sm leading-relaxed">
                                    Andika namba yako ya simu uliyosajilia au Control namba yako kuendelea na hatua zilizobaki ili kukamilisha usajili.
                                </p>
                                <p class="text-xs text-slate-500 font-medium italic">
                                    (Enter the phone number you registered with or your Control number to continue with the remaining steps to complete your registration.)
                                </p>
                            </div>
                        </div>
                        
                        <!-- Query Form -->
                        <form @submit.prevent="
                            loading = true; error = null; result = null; showOtpForm = false; otpSent = false; otpCode = ''; otpError = null; otpSuccess = null;
                            axios.post('{{ url('/api/v1/public/track-application') }}', { application_number: appNumber })
                                .then(res => { result = res.data; loading = false; })
                                .catch(err => { error = err.response?.data?.message || 'Hakuna maombi yaliyopatikana kwa maelezo uliyoweka. (No active application record was found.)'; loading = false; })
                        " class="space-y-5">
                            
                            <div>
                                <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-2.5">
                                    Namba ya Simu / Control Number / Application #
                                </label>
                                <div class="relative rounded-2xl shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" x-model="appNumber" required 
                                           placeholder="Mfano: 0712345678 au 99123xxxxx" 
                                           class="w-full pl-11 pr-5 py-4 rounded-2xl border border-slate-200 bg-slate-50 text-slate-900 font-black text-base focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 outline-none transition-all placeholder-slate-400">
                                </div>
                            </div>

                            <button type="submit" :disabled="loading" 
                                    class="w-full bg-gradient-to-r from-blue-900 to-indigo-950 hover:from-blue-800 hover:to-indigo-900 py-4 rounded-2xl text-white font-extrabold text-sm shadow-xl flex items-center justify-center gap-2 transition-all hover:shadow-indigo-900/20 active:scale-[0.99] disabled:opacity-50 cursor-pointer">
                                <span x-show="!loading" class="flex items-center gap-1">Tafuta Maombi / Search Application &rarr;</span>
                                <span x-show="loading" x-cloak class="flex items-center gap-2">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Inatafuta kwenye mfumo...
                                </span>
                            </button>
                        </form>

                        <!-- Error Alert -->
                        <div x-show="error" class="mt-4 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-xs sm:text-sm font-bold flex gap-2 items-center" x-cloak>
                            <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <span x-text="error"></span>
                        </div>
                    </div>

                    <!-- Result Card -->
                    <div x-show="result" class="bg-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6 relative overflow-hidden" x-cloak>
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-600"></div>
                        
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center border-b border-slate-100 pb-5 gap-3">
                            <div>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Namba ya Maombi / Application ID</span>
                                <span class="text-xl font-black text-blue-900" x-text="result?.application_number"></span>
                            </div>
                            <div>
                                <span class="inline-block px-4 py-1.5 rounded-full text-xs font-black uppercase shadow-inner border"
                                      :class="{ 
                                          'bg-amber-50 text-amber-800 border-amber-200': result?.status === 'Draft' || result?.status === 'Pending Payment' || result?.status === 'PAYMENT_PENDING' || result?.status === 'IN_PROGRESS', 
                                          'bg-emerald-50 text-emerald-800 border-emerald-200': result?.status === 'Approved' || result?.status === 'SUBMITTED' || result?.status === 'Under Review', 
                                          'bg-red-50 text-red-800 border-red-200': result?.status === 'Rejected' || result?.status === 'Expired' 
                                      }"
                                      x-text="result?.status"></span>
                            </div>
                        </div>

                        <!-- Info Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm font-semibold text-slate-700">
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <span class="text-slate-400 block text-[10px] uppercase font-black mb-1">Kozi Uliyochagua / Programme</span>
                                <span class="font-extrabold text-slate-900 text-sm" x-text="result?.programme"></span>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <span class="text-slate-400 block text-[10px] uppercase font-black mb-1">Aina ya Udahili / Category</span>
                                <span class="font-extrabold text-slate-900 text-sm" x-text="result?.admission_category"></span>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <span class="text-slate-400 block text-[10px] uppercase font-black mb-1">Malipo ya Fomu / Payment Status</span>
                                <span class="font-black text-sm uppercase flex items-center gap-1.5" 
                                      :class="result?.payment_status === 'paid' ? 'text-emerald-600' : 'text-amber-500'">
                                    <span class="w-2.5 h-2.5 rounded-full" :class="result?.payment_status === 'paid' ? 'bg-emerald-500' : 'bg-amber-400'"></span>
                                    <span x-text="result?.payment_status === 'paid' ? 'Imelipwa (Paid)' : 'Haijalipwa (Pending)'"></span>
                                </span>
                            </div>
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <span class="text-slate-400 block text-[10px] uppercase font-black mb-1">Hatua Uliyofikia / Wizard Step</span>
                                <span class="font-extrabold text-slate-900 text-sm" x-text="'Hatua ' + (result?.current_step || 1) + ' (' + (result?.completion_percentage || 0) + '%)'"></span>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="space-y-2">
                            <div class="flex justify-between items-center text-xs text-slate-500">
                                <span class="font-bold">Ujazo wa Fomu (Completion Progress)</span>
                                <span class="font-black text-blue-900" x-text="(result?.completion_percentage || 0) + '%'"></span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-3.5 p-0.5 border border-slate-200/50">
                                <div class="bg-gradient-to-r from-amber-500 via-yellow-400 to-emerald-500 h-full rounded-full transition-all duration-500 shadow-sm"
                                     :style="'width: ' + (result?.completion_percentage || 0) + '%'"></div>
                            </div>
                        </div>

                        <!-- Incomplete / Resume Flow -->
                        <template x-if="result?.status === 'Draft' || result?.status === 'IN_PROGRESS' || result?.status === 'Pending Payment' || result?.status === 'PAYMENT_PENDING'">
                            <div class="border-t border-slate-100 pt-6 space-y-4">
                                <div class="p-4 bg-amber-50 rounded-2xl border border-amber-200 text-xs sm:text-sm text-amber-900 leading-relaxed font-semibold">
                                    <span class="block text-amber-950 font-bold mb-1">⚠️ Ombi Lako Halijakamilika (Application Incomplete)</span>
                                    Unaweza kuendelea kujaza ombi hili kutoka hatua uliyoiacha. Bofya kitufe kilicho chini ili kuendelea na usajili mara moja.
                                    <br>
                                    <span class="text-slate-500 font-medium italic text-[11px] block mt-1.5">
                                        (You can resume filling your application right from where you left off. Click the button below to resume registration.)
                                    </span>
                                </div>

                                <!-- Direct Resume button -->
                                <button @click="
                                    otpLoading = true; otpError = null;
                                    axios.post('{{ url('/api/v1/public/resume-direct') }}', { application_id: result.application_id, user_id: result.user_id })
                                        .then(res => { window.location.href = res.data.redirect_url; })
                                        .catch(err => { otpError = err.response?.data?.message || 'Imeshindikana kuendelea na usajili. Jaribu tena baadae. (Failed to resume application. Try again later.)'; otpLoading = false; })
                                " :disabled="otpLoading" 
                                        class="w-full bg-amber-500 hover:bg-amber-600 text-slate-900 font-black py-4 rounded-2xl text-sm shadow-xl transition-all hover:shadow-amber-500/20 active:scale-[0.99] flex items-center justify-center gap-2 cursor-pointer">
                                    <span x-show="!otpLoading" class="flex items-center gap-2">
                                        🚀 Endelea na Usajili (Resume Registration)
                                    </span>
                                    <span x-show="otpLoading" x-cloak class="flex items-center gap-2">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-slate-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Inafungua ukurasa wa usajili...
                                    </span>
                                </button>

                                <!-- Error Message -->
                                <div x-show="otpError" x-cloak class="p-3 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-xs font-bold flex gap-2 items-center">
                                    <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span x-text="otpError"></span>
                                </div>
                            </div>
                        </template>
                        
                        <!-- Completed Application Message -->
                        <template x-if="result?.status === 'Approved' || result?.status === 'SUBMITTED' || result?.status === 'Under Review'">
                            <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-150 text-xs sm:text-sm text-emerald-900 leading-relaxed font-semibold">
                                <span class="block text-emerald-950 font-bold mb-1">🎉 Hongera! Ombi lako limepokelewa kikamilifu.</span>
                                Maombi yako yamewasilishwa vizuri kwenye mfumo wetu. Hakuna hatua za ziada za kujaza kwa sasa. Tafadhali subiri mrejesho wa udahili.
                                <br>
                                <span class="text-slate-500 font-medium italic text-[11px] block mt-1.5">
                                    (Congratulations! Your application has been successfully submitted. No further action is required at this moment. Kindly await admission results.)
                                </span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Right Side Help & Guidelines -->
                <div class="lg:col-span-5 space-y-6">
                    <div class="bg-gradient-to-br from-slate-900 to-indigo-950 text-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-800 space-y-6 relative overflow-hidden">
                        <div class="absolute -right-16 -top-16 w-32 h-32 bg-blue-500/10 rounded-full blur-3xl"></div>
                        <div class="absolute -left-16 -bottom-16 w-32 h-32 bg-amber-500/10 rounded-full blur-3xl"></div>
                        
                        <div class="space-y-2">
                            <h3 class="text-lg font-black text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                                Mwongozo wa Hatua
                            </h3>
                            <p class="text-xs text-slate-400">
                                Jinsi ya kufuatilia na kuendelea na usajili wako (Admission Guidelines)
                            </p>
                        </div>

                        <!-- Steps list -->
                        <div class="space-y-5">
                            <div class="flex gap-4">
                                <div class="w-8 h-8 rounded-full bg-amber-500/20 border border-amber-500/35 flex items-center justify-center text-amber-400 text-xs font-black shrink-0">
                                    1
                                </div>
                                <div class="space-y-1">
                                    <h4 class="text-sm font-bold text-slate-200">Weka Taarifa Zako (Enter Credentials)</h4>
                                    <p class="text-xs text-slate-400 leading-relaxed">
                                        Weka namba ya simu au Control namba uliyopewa wakati wa kufanya malipo ya maombi.
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <div class="w-8 h-8 rounded-full bg-amber-500/20 border border-amber-500/35 flex items-center justify-center text-amber-400 text-xs font-black shrink-0">
                                    2
                                </div>
                                <div class="space-y-1">
                                    <h4 class="text-sm font-bold text-slate-200">Kagua Maendeleo (Review Status)</h4>
                                    <p class="text-xs text-slate-400 leading-relaxed">
                                        Mfumo utaonyesha kozi uliyochagua, asilimia ya ujazaji fomu na kama malipo ya maombi yamekamilika.
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <div class="w-8 h-8 rounded-full bg-amber-500/20 border border-amber-500/35 flex items-center justify-center text-amber-400 text-xs font-black shrink-0">
                                    3
                                </div>
                                <div class="space-y-1">
                                    <h4 class="text-sm font-bold text-slate-200">Bofya Kitufe cha Endelea (Resume Wizard)</h4>
                                    <p class="text-xs text-slate-400 leading-relaxed">
                                        Bofya kitufe cha 'Endelea na Usajili' ili kurudi kwenye wizard na kukamilisha hatua zote zilizobaki.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Help Footer -->
                        <div class="bg-white/5 rounded-2xl p-4 border border-white/10 text-xs text-slate-350 leading-relaxed space-y-1.5">
                            <span class="block text-white font-bold">Usaidizi wa Ziada? (Need Help?)</span>
                            Kama unakutana na changamoto yoyote wakati wa kufuatilia maombi yako, tafadhali wasiliana na dawati la udahili kupitia namba ya simu iliyopo chini ya ukurasa huu.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

</x-public-layout>
