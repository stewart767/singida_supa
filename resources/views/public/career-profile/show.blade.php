<x-public-layout title="My Career Profile">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
        <!-- Profile Header Card -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl p-8 sm:p-10 space-y-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b pb-6">
                <div class="space-y-1.5">
                    <span class="px-3 py-1 rounded-xl bg-blue-50 text-blue-600 text-[10px] font-extrabold uppercase">Career Profile</span>
                    <h1 class="text-2xl font-black text-slate-900">{{ Auth::user()->name }}</h1>
                    <p class="text-slate-500 font-bold text-sm">{{ $profile->current_profession }} &bull; {{ $profile->years_experience }} Years Experience</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('career.profile.edit') }}" class="px-5 py-3 rounded-2xl border border-slate-200 hover:bg-slate-50 text-xs font-bold block">
                        Edit Profile
                    </a>
                </div>
            </div>

            <!-- Profile Body -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pt-4 text-xs font-semibold text-slate-700">
                <!-- Info Section -->
                <div class="md:col-span-2 space-y-8">
                    <!-- Core Skills -->
                    <div class="space-y-3">
                        <h3 class="font-extrabold text-slate-900 text-sm">Core Skills & Expertise</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($profile->skills as $skill)
                                <span class="px-3.5 py-1.5 rounded-xl bg-blue-50 text-blue-600 font-extrabold text-[10px] uppercase">
                                    {{ $skill }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Preferences -->
                    <div class="space-y-4 pt-6 border-t">
                        <h3 class="font-extrabold text-slate-900 text-sm">Job Preferences</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <span class="text-slate-400 block">Preferred Categories</span>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($profile->preferred_job_categories as $cat)
                                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600">{{ $cat }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="space-y-1">
                                <span class="text-slate-400 block">Preferred Locations</span>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($profile->preferred_locations as $loc)
                                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600">{{ $loc }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-6 border-t">
                        <div>
                            <span class="text-slate-400 block mb-1">Expected Salary</span>
                            <span class="text-emerald-500 font-black text-sm">
                                {{ $profile->expected_salary ? number_format($profile->expected_salary) . ' TZS' : 'N/A' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">Availability Date</span>
                            <span class="text-slate-900 font-bold">
                                {{ $profile->availability_date ? $profile->availability_date->format('d M Y') : 'Immediate' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Actions / Details -->
                <div class="space-y-6 bg-slate-50 p-6 rounded-2xl border border-slate-200">
                    <h3 class="font-extrabold text-slate-900 text-sm border-b pb-3">Documents & Links</h3>
                    
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <span class="text-slate-400 block">Curriculum Vitae</span>
                            <a href="{{ $cvDownloadUrl }}" class="gradient-btn text-white w-full text-center py-3.5 rounded-xl font-bold shadow block hover:shadow-md transition">
                                ⬇️ Download CV
                            </a>
                        </div>

                        @if($profile->linkedin_url)
                            <div class="space-y-1.5 pt-2 border-t border-slate-200">
                                <span class="text-slate-400 block">LinkedIn Profile</span>
                                <a href="{{ $profile->linkedin_url }}" target="_blank" class="text-blue-600 hover:underline block truncate">
                                    🔗 {{ $profile->linkedin_url }}
                                </a>
                            </div>
                        @endif

                        @if($profile->portfolio_url)
                            <div class="space-y-1.5 pt-2 border-t border-slate-200">
                                <span class="text-slate-400 block">Portfolio / Website</span>
                                <a href="{{ $profile->portfolio_url }}" target="_blank" class="text-blue-600 hover:underline block truncate">
                                    🔗 {{ $profile->portfolio_url }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-public-layout>
