<x-public-layout title="Apply Again - Continue Application">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center space-y-8">
        <!-- Success Animation/Icon -->
        <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-emerald-50 text-emerald-500 border border-emerald-100 shadow-lg shadow-emerald-500/10">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <!-- Heading -->
        <div class="space-y-3">
            <span class="px-3.5 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 text-xs font-black uppercase tracking-wider">
                Synchronization Complete
            </span>
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">Ajira Market Linked Successfully!</h1>
            <p class="text-xs text-slate-500 font-semibold max-w-lg mx-auto leading-relaxed">
                Your applicant profile has been securely connected to the National Ajira Market Portal. Your credentials are now authorized to submit and manage vacancy applications.
            </p>
        </div>

        <!-- Job Details Card -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xl space-y-6 text-left max-w-lg mx-auto">
            <div class="border-b pb-4">
                <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest block mb-1">Selected Job Vacancy</span>
                <h3 class="text-lg font-black text-slate-900 leading-snug">{{ $vacancy->job_title }}</h3>
                <p class="text-[10px] text-slate-500 font-bold mt-1">Ref: {{ $vacancy->vacancy_number }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4 text-xs font-semibold text-slate-600">
                <div class="space-y-1">
                    <span class="text-[10px] text-slate-400 font-extrabold uppercase">📍 Campus</span>
                    <p class="text-slate-800 font-bold">{{ $vacancy->campus->name ?? $vacancy->location }}</p>
                </div>
                <div class="space-y-1">
                    <span class="text-[10px] text-slate-400 font-extrabold uppercase">📅 Deadline</span>
                    <p class="text-slate-800 font-bold">{{ $vacancy->application_deadline->format('d M Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="space-y-4 max-w-xs mx-auto">
            @php
                $applyUrl = $vacancy->external_url ?: 'https://ajiramarket.co.tz';
            @endphp
            <a href="{{ $applyUrl }}" target="_blank" rel="noopener noreferrer" class="gradient-btn w-full text-center py-4 rounded-2xl text-white font-black text-xs shadow-md block hover:scale-[1.02] transition-all uppercase tracking-wider">
                Apply on Ajira Market &rarr;
            </a>
            
            <a href="{{ route('public.careers.dashboard') }}" class="w-full text-center py-3.5 rounded-2xl border text-xs font-bold hover:bg-slate-50 block text-slate-600 transition-colors">
                Go to Candidate Dashboard
            </a>
        </div>
    </div>
</x-public-layout>
