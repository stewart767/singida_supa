<x-app-layout title="Recruitment Settings">
    <x-slot name="header">Recruitment System Settings</x-slot>

    <div class="w-full space-y-8">
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-2xl text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.recruitment.settings.update') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @csrf

            <!-- Column 1: System Toggles -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6 text-xs font-semibold lg:col-span-1">
                <h3 class="font-extrabold text-slate-900 text-sm border-b pb-2">Toggle System Toggles</h3>

                <div class="space-y-4">
                    @foreach([
                        'enable_recruitment_module' => 'Enable Recruitment Module',
                        'enable_public_career_portal' => 'Enable Public Career Portal',
                        'enable_recruitment_email_notifications' => 'Enable Email Notifications',
                        'enable_recruitment_sms_notifications' => 'Enable SMS Notifications',
                        'enable_online_applications' => 'Enable Online Applications',
                        'enable_interview_scheduling' => 'Enable Interview Scheduling & Assessment',
                        'enable_offer_letter_generation' => 'Enable Offer Letter Generation & Digital Signatures',
                        'enable_talent_pool' => 'Enable Talent Pool Database Registration',
                    ] as $key => $label)
                        <div class="flex items-center justify-between py-2 border-b">
                            <div>
                                <span class="text-slate-800 font-bold block text-[11px]">{{ $label }}</span>
                                <span class="text-[9px] text-slate-500">Configure global access settings for this feature</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="{{ $key }}" value="1" {{ $settings[$key] ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-500"></div>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Column 2 & 3: Recruitment Wizard Builder & Configs -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6 text-xs font-semibold lg:col-span-2">
                <h3 class="font-extrabold text-slate-900 text-sm border-b pb-2">Recruitment Wizard & Pipeline Configuration</h3>

                <div class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="block text-slate-800 font-bold">ATS Pipeline Stages (Comma separated)</label>
                        <textarea name="recruitment_stages" rows="2" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">{{ $settings['recruitment_stages'] }}</textarea>
                        <span class="text-[10px] text-slate-500">Stages in the candidate review pipeline.</span>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-slate-800 font-bold">Allowed Education Levels (Comma separated)</label>
                        <textarea name="education_levels" rows="2" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">{{ $settings['education_levels'] }}</textarea>
                        <span class="text-[10px] text-slate-500">Options shown in candidate's education profile steps.</span>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-slate-800 font-bold">Professional Qualifications Checklist (Comma separated)</label>
                        <textarea name="professional_qualifications" rows="2" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">{{ $settings['professional_qualifications'] }}</textarea>
                        <span class="text-[10px] text-slate-500">Professional credentials and certification options.</span>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-slate-800 font-bold">Mandatory Documents Checklist (Comma separated)</label>
                        <textarea name="required_documents" rows="2" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">{{ $settings['required_documents'] }}</textarea>
                        <span class="text-[10px] text-slate-500">Document types required from applicants.</span>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-slate-800 font-bold">ICT Skills Checklist (Comma separated)</label>
                        <textarea name="ict_skills" rows="3" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">{{ $settings['ict_skills'] }}</textarea>
                        <span class="text-[10px] text-slate-500">List of standard ICT competency checkboxes.</span>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-slate-800 font-bold">Minimum Referees Count Requirement</label>
                        <input type="number" name="referee_requirements" value="{{ $settings['referee_requirements'] }}" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        <span class="text-[10px] text-slate-500">Minimum number of referees candidate must supply.</span>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-slate-800 font-bold">Ajira Market Registration URL</label>
                        <input type="text" name="ajira_market_registration_url" value="{{ $settings['ajira_market_registration_url'] }}" placeholder="{{ route('public.careers.ajira.register') }}" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        <span class="text-[10px] text-slate-500">Custom dynamic URL to redirect candidate registrations. Defaults to the internal simulation registration page if left empty.</span>
                    </div>

                    <div class="space-y-1.5 border-t pt-4 mt-4">
                        <label class="block text-slate-800 font-bold">Login & Registration Background Image</label>
                        <div class="flex items-center gap-4">
                            @if(\App\Models\Setting::get('login_background_image'))
                                <div class="w-24 h-12 rounded-xl bg-slate-200 border border-slate-300 flex items-center justify-center overflow-hidden shrink-0">
                                    <img src="{{ asset('storage/' . \App\Models\Setting::get('login_background_image')) }}" alt="Login Background" class="w-full h-full object-cover">
                                </div>
                            @endif
                            <div class="flex-grow space-y-1">
                                <input type="file" name="login_background_image" accept="image/*" class="w-full p-2 bg-slate-50 rounded-xl border border-slate-200 text-slate-950 text-xs">
                                <span class="text-[10px] text-slate-500 block">Upload a background image (PNG, JPG, WEBP, SVG) to display behind the portal login & registration cards.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t flex justify-end">
                    <button type="submit" class="gradient-btn px-8 py-3.5 rounded-2xl text-white font-extrabold shadow-xl">
                        Save Configuration Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
