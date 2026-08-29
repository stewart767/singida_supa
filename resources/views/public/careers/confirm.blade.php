<x-public-layout title="Confirm Application Redirect - {{ $vacancy->job_title }}">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl overflow-hidden">
            <!-- Header Section -->
            <div class="bg-slate-950 text-white p-8 text-center space-y-2">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-amber-500/10 text-amber-500 mb-2">
                    <span class="text-3xl">🔗</span>
                </div>
                <h1 class="text-2xl font-black tracking-tight">External Application Redirect</h1>
                <p class="text-slate-400 text-xs font-semibold">You are leaving SUPA Careers and heading to the external recruitment provider.</p>
            </div>

            <!-- Body Section -->
            <div class="p-8 sm:p-10 space-y-8 text-xs font-semibold text-slate-700">
                <!-- Job details card -->
                <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200 space-y-4">
                    <h2 class="text-slate-900 font-extrabold text-sm border-b pb-3">Vacancy Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="text-slate-400 block mb-1">Job Title</span>
                            <span class="text-slate-900 font-bold text-base">{{ $vacancy->job_title }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">Department / Designation</span>
                            <span class="text-slate-900 font-bold">{{ $vacancy->designation->name ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">Employment Type</span>
                            <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-600 font-extrabold text-[10px] uppercase inline-block">{{ $vacancy->employment_type }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">Application Deadline</span>
                            <span class="text-red-500 font-black">{{ $vacancy->application_deadline->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Info Alert -->
                <div class="p-5 bg-amber-50 border border-amber-200 rounded-2xl flex items-start gap-4">
                    <span class="text-xl">⚠️</span>
                    <div class="space-y-1.5 text-amber-900">
                        <h4 class="font-extrabold text-sm">Notice Regarding Final Application</h4>
                        <p class="leading-relaxed">
                            This vacancy is externally managed by <strong>{{ ucfirst($vacancy->external_provider ?? 'Ajira Market') }}</strong>. 
                            To submit your final application, you will need to continue on their platform. 
                            Your SUPA Career Profile will be used internally for tracking, but does not substitute the final application step on Ajira Market.
                        </p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-4 justify-between items-center pt-6 border-t">
                    <a href="{{ route('public.careers.show', $vacancy->vacancy_number) }}" class="px-6 py-3.5 rounded-2xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-center w-full sm:w-auto">
                        Return to Vacancy
                    </a>
                    <a href="{{ $redirectUrl }}" class="gradient-btn px-8 py-4 rounded-2xl text-white font-extrabold shadow-lg hover:shadow-xl text-center w-full sm:w-auto transition-all duration-200">
                        Continue to Ajira Market &rarr;
                    </a>
                </div>
                
                <p class="text-slate-400 text-[10px] text-center">
                    This redirect authorization link is secure and will expire in 10 minutes.
                </p>
            </div>
        </div>
    </div>
</x-public-layout>
