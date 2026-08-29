<x-public-layout title="Contact Admissions - SUPA / OUT Portal">

    <div x-data="{ 
        form: { name: '', email: '', phone: '', subject: '', message: '', category: 'Direct Entry Requirements' }, 
        success: false, 
        loading: false,
        ticketId: '',
        activeFaq: null,
        activeCampus: 'singida'
    }">

        <!-- Hero Banner Section -->
        <section class="relative text-white py-20 border-b border-slate-200 overflow-hidden bg-cover bg-center bg-no-repeat bg-gradient-to-r from-slate-950 via-blue-950 to-slate-900"
                 @if(\App\Models\Setting::get('banner_contact')) style="background-image: linear-gradient(to right, rgba(2, 6, 23, 0.95), rgba(30, 58, 138, 0.85)), url('{{ asset('storage/' . \App\Models\Setting::get('banner_contact')) }}');" @endif>
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#10b981_1px,transparent_1px)] [background-size:20px_20px]"></div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-6">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 text-xs font-extrabold uppercase tracking-wider shadow-lg">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Admissions Desk Status: Online (Mon - Fri: 8:00 AM - 5:00 PM EAT)
                </div>

                <h1 class="hero-title text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight text-white leading-tight">
                    Contact Admissions & Advisory Desk
                </h1>

                <p class="body-text text-slate-300 max-w-3xl mx-auto text-base sm:text-lg leading-relaxed font-normal">
                    Have questions regarding entry requirements, GPA calculations, control number generation, or selection status? Our admission officers are available to support your application.
                </p>
            </div>
        </section>

        <!-- Quick Contact Channels Cards Grid -->
        <section class="py-12 bg-white border-b border-slate-200 text-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    
                    <!-- Admissions Desk -->
                    <div class="bg-white border border-slate-200 p-6 rounded-3xl space-y-3 hover:border-amber-500/50 transition-colors shadow-xl">
                        <div class="w-12 h-12 rounded-2xl bg-amber-600/10 text-amber-600 flex items-center justify-center text-xl font-black shrink-0">
                            📍
                        </div>
                        <h3 class="font-extrabold text-slate-900 text-base">Main Admissions Office</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            {{ \App\Models\Setting::get('university_name', "SINGIDA TEACHERS' TRAINING COLLEGE (STTC) & OUT") }}<br>
                            {{ \App\Models\Setting::get('contact_address', 'Kawawa Road, Kinondoni & Singida Campus') }}
                        </p>
                        <span class="text-[11px] font-bold text-amber-600 block pt-1">Open: {{ \App\Models\Setting::get('contact_hours', 'Mon - Fri: 8:00 AM - 5:00 PM') }}</span>
                    </div>

                    <!-- Hotline & Phone -->
                    <div class="bg-white border border-slate-200 p-6 rounded-3xl space-y-3 hover:border-blue-500/50 transition-colors shadow-xl">
                        <div class="w-12 h-12 rounded-2xl bg-blue-600/10 text-blue-600 flex items-center justify-center text-xl font-black shrink-0">
                            📞
                        </div>
                        <h3 class="font-extrabold text-slate-900 text-base">Telephone & WhatsApp</h3>
                        @php
                            $cPhone = \App\Models\Setting::get('contact_phone', '+255 22 266 8820');
                            $cWhatsapp = \App\Models\Setting::get('contact_whatsapp', '+255754000111');
                            $cWhatsappClean = preg_replace('/[^0-9]/', '', $cWhatsapp);
                        @endphp
                        <div class="space-y-1 text-xs text-slate-700 font-bold">
                            <p>Main Helpline: <a href="tel:{{ $cPhone }}" class="text-blue-600 hover:underline">{{ $cPhone }}</a></p>
                            @if($cWhatsapp)
                                <p>WhatsApp Support: <a href="https://wa.me/{{ $cWhatsappClean }}" class="text-blue-600 hover:underline">{{ $cWhatsapp }}</a></p>
                            @endif
                        </div>
                        @if($cWhatsapp)
                        <a href="https://wa.me/{{ $cWhatsappClean }}" target="_blank" class="inline-flex items-center gap-1.5 text-[11px] font-extrabold text-emerald-600 hover:underline pt-1">
                            <span>Chat on WhatsApp</span>
                            <span>&rarr;</span>
                        </a>
                        @endif
                    </div>

                    <!-- Email Desk -->
                    <div class="bg-white border border-slate-200 p-6 rounded-3xl space-y-3 hover:border-emerald-500/50 transition-colors shadow-xl">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-600/10 text-emerald-600 flex items-center justify-center text-xl font-black shrink-0">
                            ✉️
                        </div>
                        <h3 class="font-extrabold text-slate-900 text-base">Official Email Support</h3>
                        <div class="space-y-1 text-xs text-slate-700">
                            <p class="font-bold">Admissions: <span class="text-amber-600">{{ \App\Models\Setting::get('contact_email', 'admissions@supa.ac.tz') }}</span></p>
                            <p class="font-bold">Portal Support: <span class="text-amber-600">{{ \App\Models\Setting::get('support_email', 'support@supa.ac.tz') }}</span></p>
                        </div>
                        <span class="text-[11px] font-bold text-slate-500 block pt-1">Average Response: Under 4 Hours</span>
                    </div>

                    <!-- Regional Centres -->
                    <div class="bg-white border border-slate-200 p-6 rounded-3xl space-y-3 hover:border-indigo-500/50 transition-colors shadow-xl">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-600/10 text-indigo-600 flex items-center justify-center text-xl font-black shrink-0">
                            🏛️
                        </div>
                        <h3 class="font-extrabold text-slate-900 text-base">Regional Centres</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Support desks located in Singida, Dar es Salaam, Dodoma, Arusha, Mwanza, Mbeya, & Zanzibar.
                        </p>
                        <span class="text-[11px] font-bold text-indigo-600 block pt-1">30+ Learning Centers Countrywide</span>
                    </div>

                </div>
            </div>
        </section>

        <!-- Main Form & Helpful FAQs Grid -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
                    
                    <!-- Left Column: Contact Form (7 cols) -->
                    <div class="lg:col-span-7 bg-white p-8 sm:p-10 rounded-3xl border border-slate-200 shadow-2xl space-y-6">
                        
                        <div class="border-b border-slate-100 pb-4 space-y-1">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-amber-500 bg-amber-500/10 px-3 py-1 rounded-full">
                                Direct Inquiry Submission
                            </span>
                            <h2 class="text-2xl font-black text-slate-900">Send a Message to Admissions</h2>
                            <p class="text-xs text-slate-500">Fill in the details below and an admission officer will respond shortly.</p>
                        </div>

                        <!-- Success Alert Box -->
                        <div x-show="success" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="p-6 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-slate-900 space-y-4 shadow-lg" x-cloak>
                            <div class="flex items-center space-x-3 text-emerald-600 font-black text-base">
                                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center text-xl shrink-0">
                                    ✓
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-sm sm:text-base">Inquiry Submitted Successfully!</h4>
                                    <span class="text-xs text-slate-500 font-normal">Tracking Reference: <strong class="text-emerald-500" x-text="'SUPA-INQ-' + ticketId"></strong></span>
                                </div>
                            </div>

                            <p class="text-xs leading-relaxed text-slate-600">
                                Thank you for contacting the SUPA / OUT Admissions Office. A reference notification has been logged in our queue. Our officers review inquiries promptly during office hours.
                            </p>

                            <button @click="success = false; form = { name: '', email: '', phone: '', subject: '', message: '', category: 'Direct Entry Requirements' }" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-extrabold text-xs hover:bg-emerald-700 transition-colors shadow-md">
                                Submit Another Inquiry
                            </button>
                        </div>

                        <!-- Contact Form -->
                        <form x-show="!success" @submit.prevent="
                            loading = true;
                            let fullSubject = '[' + form.category + '] ' + form.subject;
                            axios.post('{{ url('/api/v1/public/contact') }}', {
                                name: form.name,
                                email: form.email,
                                phone: form.phone,
                                subject: fullSubject,
                                message: form.message
                            })
                            .then((res) => { 
                                success = true; 
                                loading = false; 
                                ticketId = res.data.contact_id || Math.floor(100000 + Math.random() * 900000);
                                toast('Inquiry submitted successfully!', 'success');
                            })
                            .catch((err) => { 
                                loading = false; 
                                toast(err.response?.data?.message || 'Error submitting message. Please check required fields.', 'error'); 
                            })
                        " class="space-y-5">
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">
                                        Full Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" x-model="form.name" required placeholder="e.g. Baraka Juma"
                                           class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500 shadow-sm transition-all">
                                </div>

                                <div>
                                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">
                                        Email Address <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" x-model="form.email" required placeholder="e.g. baraka@gmail.com"
                                           class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500 shadow-sm transition-all">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">
                                        Phone Number (Optional)
                                    </label>
                                    <input type="tel" x-model="form.phone" placeholder="e.g. +255 712 345 678"
                                           class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500 shadow-sm transition-all">
                                </div>

                                <div>
                                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">
                                        Inquiry Category <span class="text-red-500">*</span>
                                    </label>
                                    <select x-model="form.category" class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500 shadow-sm transition-all">
                                        <option value="Direct Entry Requirements">Direct Entry Requirements (GPA / Form VI)</option>
                                        <option value="Foundation Course">Foundation Course (Bridging Intake)</option>
                                        <option value="Fee & Control Number">Fee Payments & Control Numbers</option>
                                        <option value="Document Verification">Document & NECTA Verification Issue</option>
                                        <option value="Application Status">Application Status & Selection Query</option>
                                        <option value="General Query">General Admission Inquiry</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">
                                    Subject <span class="text-red-500">*</span>
                                </label>
                                <input type="text" x-model="form.subject" required placeholder="e.g. Inquiry regarding Diploma GPA verification for Bachelor of Education"
                                       class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500 shadow-sm transition-all">
                            </div>

                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <label class="text-xs font-extrabold text-slate-700 uppercase">
                                        Message Details <span class="text-red-500">*</span>
                                    </label>
                                    <span class="text-[11px] text-slate-500" x-text="form.message.length + '/1000 characters'"></span>
                                </div>
                                <textarea x-model="form.message" rows="5" required maxlength="1000" placeholder="Please describe your query in detail including your Form 4 / Form 6 index number or Application Number if applicable..."
                                          class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-amber-500 shadow-sm transition-all"></textarea>
                            </div>

                            <button type="submit" :disabled="loading" class="w-full gradient-btn py-4 rounded-2xl text-white font-extrabold text-sm shadow-xl hover:scale-[1.01] transition-transform duration-200 flex items-center justify-center gap-2">
                                <span x-show="!loading" class="flex items-center gap-2">
                                    <span>Send Message to Admissions</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </span>
                                <span x-show="loading" x-cloak class="flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span>Submitting Message...</span>
                                </span>
                            </button>

                        </form>

                    </div>

                    <!-- Right Column: Instant Help FAQs & Quick Links (5 cols) -->
                    <div class="lg:col-span-5 space-y-8">
                        
                        <!-- Instant FAQs Card -->
                        <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-xl space-y-6">
                            <div class="border-b border-slate-100 pb-3">
                                <span class="text-[10px] font-extrabold text-amber-500 uppercase tracking-widest">Self-Service Help</span>
                                <h3 class="text-lg font-black text-slate-900 mt-1">Frequently Asked Questions</h3>
                                <p class="text-xs text-slate-500">Quick answers to standard applicant queries</p>
                            </div>

                            <div class="space-y-3">
                                
                                <!-- FAQ 1 -->
                                <div class="border border-slate-200 rounded-2xl overflow-hidden">
                                    <button @click="activeFaq = activeFaq === 1 ? null : 1" class="w-full text-left px-5 py-3.5 bg-slate-50 flex justify-between items-center text-xs font-bold text-slate-900">
                                        <span>How long does application verification take?</span>
                                        <span class="text-amber-500 font-black text-base" x-text="activeFaq === 1 ? '−' : '+'"></span>
                                    </button>
                                    <div x-show="activeFaq === 1" class="p-5 text-xs text-slate-600 leading-relaxed border-t border-slate-200 bg-white" x-cloak>
                                        Applications undergo automated NECTA and NAKTE verification immediately upon submission. Admissions officer final verification typically takes 24 to 48 hours.
                                    </div>
                                </div>

                                <!-- FAQ 2 -->
                                <div class="border border-slate-200 rounded-2xl overflow-hidden">
                                    <button @click="activeFaq = activeFaq === 2 ? null : 2" class="w-full text-left px-5 py-3.5 bg-slate-50 flex justify-between items-center text-xs font-bold text-slate-900">
                                        <span>How do I generate a Control Number for payment?</span>
                                        <span class="text-amber-500 font-black text-base" x-text="activeFaq === 2 ? '−' : '+'"></span>
                                    </button>
                                    <div x-show="activeFaq === 2" class="p-5 text-xs text-slate-600 leading-relaxed border-t border-slate-200 bg-white" x-cloak>
                                        Log in to your applicant dashboard, complete step 1 & 2 of the application, and click "Generate Control Number" under the Payment step. You can then pay via M-Pesa, Tigo Pesa, or Bank.
                                    </div>
                                </div>

                                <!-- FAQ 3 -->
                                <div class="border border-slate-200 rounded-2xl overflow-hidden">
                                    <button @click="activeFaq = activeFaq === 3 ? null : 3" class="w-full text-left px-5 py-3.5 bg-slate-50 flex justify-between items-center text-xs font-bold text-slate-900">
                                        <span>Who qualifies for the Foundation Course?</span>
                                        <span class="text-amber-500 font-black text-base" x-text="activeFaq === 3 ? '−' : '+'"></span>
                                    </button>
                                    <div x-show="activeFaq === 3" class="p-5 text-xs text-slate-600 leading-relaxed border-t border-slate-200 bg-white" x-cloak>
                                        Diploma holders with a GPA between 2.0 and 2.9 or Form Six applicants with 4.5 points qualify for the Foundation Course (Bridging) program.
                                    </div>
                                </div>

                                <!-- FAQ 4 -->
                                <div class="border border-slate-200 rounded-2xl overflow-hidden">
                                    <button @click="activeFaq = activeFaq === 4 ? null : 4" class="w-full text-left px-5 py-3.5 bg-slate-50 flex justify-between items-center text-xs font-bold text-slate-900">
                                        <span>How do I track my submitted application status?</span>
                                        <span class="text-amber-500 font-black text-base" x-text="activeFaq === 4 ? '−' : '+'"></span>
                                    </button>
                                    <div x-show="activeFaq === 4" class="p-5 text-xs text-slate-600 leading-relaxed border-t border-slate-200 bg-white" x-cloak>
                                        Visit the <a href="{{ route('public.track') }}" class="text-amber-500 font-bold hover:underline">Track Application</a> page and enter your Application Number (e.g. APP-2026-0001) for instant status breakdown.
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Campus Location & Directions Card -->
                        <div class="bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 text-white p-8 rounded-3xl border border-slate-200 shadow-xl space-y-4">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-400 bg-emerald-500/20 px-3 py-1 rounded-full">
                                Physical Campus Visit
                            </span>
                            <h3 class="text-xl font-black text-white">Singida Main Campus & Registry</h3>
                            <p class="text-xs text-slate-700 leading-relaxed">
                                Singida Teachers' Training College (STTC) Campus, Near Singida Town Center, P.O. Box 234, Singida, Tanzania.
                            </p>

                            <div class="pt-2 flex flex-col sm:flex-row gap-3">
                                <a href="https://maps.google.com" target="_blank" class="gradient-btn-gold px-5 py-3 rounded-2xl text-slate-950 font-extrabold text-xs shadow-lg flex items-center justify-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>Get Directions on Google Maps</span>
                                </a>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </section>

    </div>

</x-public-layout>
