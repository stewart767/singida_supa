<x-public-layout title="{{ $vacancy->job_title }} - Career Opportunities">

    <!-- Job Hero Banner Section -->
    <section class="relative text-white py-16 border-b border-slate-200 overflow-hidden bg-cover bg-center bg-no-repeat bg-gradient-to-r from-slate-950 via-blue-950 to-slate-900"
             @if(\App\Models\Setting::get('banner_careers')) style="background-image: linear-gradient(to right, rgba(2, 6, 23, 0.95), rgba(30, 58, 138, 0.85)), url('{{ asset('storage/' . \App\Models\Setting::get('banner_careers')) }}');" @endif>
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#3b82f6_1px,transparent_1px)] [background-size:20px_20px]"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 space-y-4">
            <div class="flex items-center space-x-3 text-xs">
                <a href="{{ route('public.careers.index') }}" class="text-amber-400 font-extrabold hover:underline flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Job List
                </a>
                <span class="text-slate-600">•</span>
                <span class="px-3 py-1 rounded-xl bg-blue-500/20 border border-blue-500/30 text-blue-300 font-extrabold uppercase tracking-wider text-[10px]">{{ $vacancy->employment_type }}</span>
                <span class="px-3 py-1 rounded-xl bg-slate-500/20 border border-slate-500/30 text-slate-300 font-extrabold uppercase tracking-wider text-[10px]">{{ $vacancy->contract_type }}</span>
                <span class="px-3 py-1 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 font-extrabold uppercase tracking-wider text-[10px]">Online Application</span>
                <span class="px-3 py-1 rounded-xl bg-slate-500/20 border border-slate-500/30 text-slate-300 font-extrabold uppercase tracking-wider text-[10px]">Direct Entry</span>
            </div>

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white leading-tight tracking-tight">
                {{ $vacancy->job_title }}
            </h1>

            <p class="text-xs text-slate-300 pt-2 border-t border-slate-700/80 font-bold">
                Vacancy Reference: {{ $vacancy->vacancy_number }} &bull; Department: {{ $vacancy->department_name ?? 'N/A' }} &bull; Designation: {{ $vacancy->designation->name ?? 'N/A' }}
            </p>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Details Columns -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xl space-y-6">
                <!-- Descriptions Sections -->
                <div class="space-y-6 text-xs font-semibold text-slate-700">
                    <div class="space-y-2">
                        <h3 class="font-extrabold text-slate-900 text-sm">Key Responsibilities</h3>
                        <p class="leading-relaxed whitespace-pre-line">{{ $vacancy->responsibilities }}</p>
                    </div>

                    <div class="space-y-2 pt-4 border-t">
                        <h3 class="font-extrabold text-slate-900 text-sm">Required Qualifications</h3>
                        <p class="leading-relaxed whitespace-pre-line">{{ $vacancy->qualifications }}</p>
                    </div>

                    <div class="space-y-2 pt-4 border-t">
                        <h3 class="font-extrabold text-slate-900 text-sm">Required Experience</h3>
                        <p class="leading-relaxed whitespace-pre-line">{{ $vacancy->required_experience }}</p>
                    </div>

                    @if($vacancy->required_skills)
                        <div class="space-y-2 pt-4 border-t">
                            <h3 class="font-extrabold text-slate-900 text-sm">Required Skills</h3>
                            <p class="leading-relaxed whitespace-pre-line">{{ $vacancy->required_skills }}</p>
                        </div>
                    @endif

                    @if($vacancy->benefits)
                        <div class="space-y-2 pt-4 border-t">
                            <h3 class="font-extrabold text-slate-900 text-sm">Benefits & Perks</h3>
                            <p class="leading-relaxed whitespace-pre-line">{{ $vacancy->benefits }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Actions & Share Panel -->
        <div class="space-y-8">
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xl space-y-6 text-xs font-semibold">
                <h3 class="font-extrabold text-slate-900 text-sm border-b pb-3">Apply Desk</h3>

                <div class="space-y-2.5">
                    <div class="flex justify-between items-center text-slate-500">
                        <span>📍 Campus</span>
                        <span class="text-slate-800 font-bold">{{ $vacancy->campus->name ?? $vacancy->location }}</span>
                    </div>
                    @if($vacancy->recommended_region)
                        <div class="flex justify-between items-center text-slate-500">
                            <span>🗺️ Recommended Region</span>
                            <span class="text-slate-800 font-bold">{{ $vacancy->recommended_region }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center text-slate-500">
                        <span>📅 Deadline</span>
                        <span class="text-red-500 font-black">{{ $vacancy->application_deadline->format('d M Y') }}</span>
                    </div>
                    @if($vacancy->salary_range)
                        <div class="flex justify-between items-center text-slate-500">
                            <span>💰 Salary Range</span>
                            <span class="text-emerald-500 font-black">{{ $vacancy->salary_range }}</span>
                        </div>
                    @endif
                </div>

                <div class="space-y-2 pt-4 border-t">
                    @php
                        $applyUrl = $vacancy->external_url ?: 'https://ajiramarket.co.tz';
                    @endphp
                    <a href="{{ $applyUrl }}" target="_blank" rel="noopener noreferrer" class="gradient-btn w-full text-center py-4 rounded-2xl text-white font-extrabold text-xs shadow-md block flex items-center justify-center gap-2 hover:scale-[1.02] transition-all">
                        <span>Apply on Ajira Market</span>
                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                    
                    <a href="{{ route('public.careers.jd', $vacancy->vacancy_number) }}" target="_blank" class="w-full text-center py-3 rounded-2xl border hover:bg-slate-50 block">
                        Download Job Description (PDF)
                    </a>
                </div>
            </div>

            <!-- Share Vacancy -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xl space-y-4 text-xs font-semibold">
                <h3 class="font-extrabold text-slate-900 text-sm border-b pb-3">Share This Vacancy</h3>
                <div class="flex flex-col gap-2">
                    <a href="mailto:?subject={{ urlencode('Job Opening: ' . $vacancy->job_title) }}&body={{ urlencode(url()->current()) }}" class="p-3 rounded-2xl border hover:bg-slate-50 flex items-center justify-between">
                        <span>Share via Email</span>
                        <span>✉️</span>
                    </a>
                    <a href="https://wa.me/?text={{ urlencode('Job Opening: ' . $vacancy->job_title . ' at SUPA. Apply here: ' . url()->current()) }}" target="_blank" class="p-3 rounded-2xl border hover:bg-slate-50 flex items-center justify-between">
                        <span>Share via WhatsApp</span>
                        <span>💬</span>
                    </a>
                </div>
            </div>

            <!-- Related Vacancies -->
            @if($relatedVacancies->isNotEmpty())
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xl space-y-4 text-xs font-semibold">
                    <h3 class="font-extrabold text-slate-900 text-sm border-b pb-3">Related Opportunities</h3>
                    <div class="space-y-4">
                        @foreach($relatedVacancies as $rel)
                            <div class="space-y-1">
                                <a href="{{ route('public.careers.show', $rel->vacancy_number) }}" class="font-bold text-slate-900 hover:text-amber-500 hover:underline text-xs block leading-snug">{{ $rel->job_title }}</a>
                                <div class="text-[10px] text-slate-500">{{ $rel->designation->name ?? 'N/A' }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-public-layout>
