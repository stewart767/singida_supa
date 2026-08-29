<x-public-layout title="Admission Requirements - SUPA University">

    <section class="relative text-white py-20 text-center space-y-4 border-b border-slate-200 bg-cover bg-center bg-no-repeat bg-gradient-to-r from-slate-950 via-blue-950 to-slate-900"
             @if(\App\Models\Setting::get('banner_requirements')) style="background-image: linear-gradient(to right, rgba(2, 6, 23, 0.95), rgba(30, 58, 138, 0.85)), url('{{ asset('storage/' . \App\Models\Setting::get('banner_requirements')) }}');" @endif>
        <span class="px-3.5 py-1.5 rounded-full bg-blue-500/20 border border-blue-500/30 text-blue-400 text-xs font-extrabold uppercase tracking-wider">
            Guidelines & Qualifications
        </span>
        <h1 class="hero-title font-extrabold tracking-tight">Admission Requirements</h1>
        <p class="body-text text-slate-300 max-w-2xl mx-auto">
            Clear guidelines for Direct Entry into Degree Programmes vs Foundation Course (Bridging) Qualifications.
        </p>
    </section>

    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Direct Entry Card -->
                <div class="bg-white p-8 rounded-3xl border border-emerald-200 shadow-xl space-y-6 card-hover-effect">
                    <div class="flex items-center justify-between">
                        <span class="px-4 py-1.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-700">
                            Category 1: Direct Entry
                        </span>
                        <span class="text-xs font-bold text-slate-500">Bachelor Degrees</span>
                    </div>

                    <h3 class="text-2xl font-extrabold text-slate-900">Direct Degree Admission</h3>
                    
                    <ul class="space-y-4 text-xs text-slate-600">
                        <li class="flex items-start">
                            <div class="w-6 h-6 rounded-full bg-emerald-500/10 text-emerald-500 flex items-center justify-center font-bold mr-3 shrink-0">✓</div>
                            <span><strong>Diploma Qualifications:</strong> Minimum GPA of <strong>3.0</strong> or B Average from an accredited institution.</span>
                        </li>
                        <li class="flex items-start">
                            <div class="w-6 h-6 rounded-full bg-emerald-500/10 text-emerald-500 flex items-center justify-center font-bold mr-3 shrink-0">✓</div>
                            <span><strong>Form Six (ACSEE):</strong> Minimum of <strong>5 Points</strong> across relevant subject combinations.</span>
                        </li>
                    </ul>
                </div>

                <!-- Foundation Course Card -->
                <div class="bg-white p-8 rounded-3xl border border-amber-200 shadow-xl space-y-6 card-hover-effect">
                    <div class="flex items-center justify-between">
                        <span class="px-4 py-1.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-700">
                            Category 2: Foundation Course
                        </span>
                        <span class="text-xs font-bold text-slate-500">Bridging Programme</span>
                    </div>

                    <h3 class="text-2xl font-extrabold text-slate-900">Foundation Bridging Admission</h3>

                    <ul class="space-y-4 text-xs text-slate-600">
                        <li class="flex items-start">
                            <div class="w-6 h-6 rounded-full bg-amber-500/10 text-amber-500 flex items-center justify-center font-bold mr-3 shrink-0">✓</div>
                            <span><strong>Diploma Qualifications:</strong> GPA between <strong>2.0 and 2.9</strong> (Pass classification).</span>
                        </li>
                        <li class="flex items-start">
                            <div class="w-6 h-6 rounded-full bg-amber-500/10 text-amber-500 flex items-center justify-center font-bold mr-3 shrink-0">✓</div>
                            <span><strong>Form Six (ACSEE):</strong> 2 to 4 Points in principal passes.</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="text-center pt-6 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('public.student-guide') }}" target="_blank" class="px-8 py-4 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm shadow-xl transition-transform hover:scale-105 flex items-center gap-2">
                    📄 Fungua/Chapisha Sehemu ya 2 (Mwongozo wa Mwanafunzi PDF) &rarr;
                </a>
                <a href="{{ route('register') }}" class="gradient-btn-gold px-10 py-4 rounded-2xl text-slate-950 font-extrabold text-sm shadow-2xl inline-block hover:scale-105 transition-transform">
                    Check Eligibility & Apply Now &rarr;
                </a>
            </div>
        </div>
    </section>

</x-public-layout>
