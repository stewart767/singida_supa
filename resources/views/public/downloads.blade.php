<x-public-layout title="Download Hub - SUPA University">

    <section class="relative text-white py-20 text-center space-y-4 border-b border-slate-200 bg-cover bg-center bg-no-repeat bg-gradient-to-r from-slate-950 via-blue-950 to-slate-900"
             @if(\App\Models\Setting::get('banner_downloads')) style="background-image: linear-gradient(to right, rgba(2, 6, 23, 0.95), rgba(30, 58, 138, 0.85)), url('{{ asset('storage/' . \App\Models\Setting::get('banner_downloads')) }}');" @endif>
        <span class="px-3.5 py-1.5 rounded-full bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 text-xs font-extrabold uppercase tracking-wider">
            Official Prospectus & PDF Forms
        </span>
        <h1 class="hero-title font-extrabold tracking-tight">Download Hub</h1>
        <p class="body-text text-slate-300 max-w-2xl mx-auto">
            Download official academic prospectuses, fee structures, application guides, and medical clearance forms.
        </p>
    </section>

    <section class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <!-- Printable 7 Steps Application Guide PDF -->
            <div class="bg-gradient-to-r from-amber-500/10 via-orange-500/10 to-amber-500/10 bg-white p-6 rounded-3xl border border-amber-300 shadow-lg flex flex-col sm:flex-row sm:items-center justify-between gap-4 card-hover-effect">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-600/10 text-amber-600 flex items-center justify-center font-black text-xs uppercase shrink-0">
                        PDF
                    </div>
                    <div>
                        <h4 class="font-extrabold text-slate-900 text-base">Mwongozo wa Hatua 7 za Fomu ya Udahili (SUPA Admission Steps Guide)</h4>
                        <span class="text-xs text-amber-700 font-bold">Orodha na maelekezo ya hatua zote 7 za kujaza fomu ya maombi (Ada TZS 20,000/=)</span>
                    </div>
                </div>
                <a href="{{ route('public.admission-steps-guide') }}" target="_blank" class="px-6 py-3 rounded-2xl bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs shadow-md shrink-0 text-center">
                    🖨️ Fungua / Chapisha PDF &rarr;
                </a>
            </div>

            <!-- Downloadable Excel Admission Requirements Template -->
            <div class="bg-gradient-to-r from-emerald-500/10 via-teal-500/10 to-emerald-500/10 bg-white p-6 rounded-3xl border border-emerald-300 shadow-lg flex flex-col sm:flex-row sm:items-center justify-between gap-4 card-hover-effect">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-600/10 text-emerald-600 flex items-center justify-center font-black text-xs uppercase shrink-0">
                        XLS
                    </div>
                    <div>
                        <h4 class="font-extrabold text-slate-900 text-base">Fomu ya Mahitaji ya Udahili (Excel Requirements Template)</h4>
                        <span class="text-xs text-emerald-700 font-bold">Jedwali kamili la Excel/CSV lenye orodha ya vipengele vyote vinavyohitajika kujazwa</span>
                    </div>
                </div>
                <a href="{{ route('public.download.admission-excel') }}" class="px-6 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md shrink-0 text-center">
                    📥 Pakua Fomu ya Excel &rarr;
                </a>
            </div>

            <!-- Printable Student Guide Part 2 -->
            <div class="bg-gradient-to-r from-blue-900/10 via-indigo-900/10 to-blue-900/10 bg-white p-6 rounded-3xl border border-blue-300 shadow-lg flex flex-col sm:flex-row sm:items-center justify-between gap-4 card-hover-effect">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-600/10 text-blue-600 flex items-center justify-center font-black text-xs uppercase shrink-0">
                        PDF
                    </div>
                    <div>
                        <h4 class="font-extrabold text-slate-900 text-base">SEHEMU YA 2: Kiongozi na Maelezo Muhimu kwa Mwanafunzi</h4>
                        <span class="text-xs text-blue-600 font-bold">Mwongozo Rasmi wa Udahili, Ada, Michango, Ratiba na Mamlaka (2026)</span>
                    </div>
                </div>
                <a href="{{ route('public.student-guide') }}" target="_blank" class="px-6 py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md shrink-0 text-center">
                    🖨️ Fungua / Chapisha PDF &rarr;
                </a>
            </div>

            @foreach($downloads as $file)
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-md flex flex-col sm:flex-row sm:items-center justify-between gap-4 card-hover-effect">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-2xl bg-red-500/10 text-red-600 flex items-center justify-center font-black text-xs uppercase shrink-0">
                            PDF
                        </div>
                        <div>
                            <h4 class="font-extrabold text-slate-900 text-base">{{ $file->title }}</h4>
                            <span class="text-xs text-slate-500 font-bold">Category: {{ $file->category }}</span>
                        </div>
                    </div>
                    <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="gradient-btn px-6 py-3 rounded-2xl text-white font-extrabold text-xs shadow-md shrink-0">
                        Download File &rarr;
                    </a>
                </div>
            @endforeach
        </div>
    </section>

</x-public-layout>
