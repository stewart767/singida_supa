<x-app-layout title="Recruitment Executive Dashboard">
    <x-slot name="header">Recruitment Executive Dashboard</x-slot>

    <div class="w-full space-y-8">
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-2xl text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif

        <!-- KPI Metrics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="{{ route('admin.recruitment.vacancies') }}" class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-2 card-hover-effect block hover:no-underline">
                <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest block">Total / Active Vacancies</span>
                <span class="text-3xl font-black text-slate-900 block">{{ $metrics['total_vacancies'] }} / <span class="text-amber-500">{{ $metrics['active_vacancies'] }}</span></span>
                <span class="text-[10px] text-blue-500 font-extrabold">Open for applications</span>
            </a>

            <a href="{{ route('admin.recruitment.applications.index') }}" class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-2 card-hover-effect block hover:no-underline">
                <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest block">Total Applicants</span>
                <span class="text-3xl font-black text-amber-500 block">{{ $metrics['total_applicants'] }}</span>
                <span class="text-[10px] text-amber-500 font-extrabold">{{ $metrics['applications_today'] }} submitted today</span>
            </a>

            <a href="{{ route('admin.recruitment.interviews') }}" class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-2 card-hover-effect block hover:no-underline">
                <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest block">Interview / Written Tests</span>
                <span class="text-3xl font-black text-emerald-500 block">{{ $metrics['interview_scheduled'] }} / {{ $metrics['written_tests'] }}</span>
                <span class="text-[10px] text-emerald-500 font-extrabold">Evaluation stages active</span>
            </a>

            <a href="{{ route('admin.recruitment.evaluations') }}" class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-2 card-hover-effect block hover:no-underline">
                <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest block">Selected / Hired</span>
                <span class="text-3xl font-black text-blue-600 block">{{ $metrics['selected_candidates'] }} / {{ $metrics['positions_filled'] }}</span>
                <span class="text-[10px] text-slate-500 font-extrabold">Offers accepted</span>
            </a>
        </div>

        <!-- Chart Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Applications by Designation -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                <h3 class="font-extrabold text-slate-900 text-base">Applications by Designation</h3>
                <div class="h-64">
                    <canvas id="desigChart"></canvas>
                </div>
            </div>

            <!-- Monthly Recruitment Trends -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                <h3 class="font-extrabold text-slate-900 text-base">Monthly Recruitment Trends</h3>
                <div class="h-64">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <!-- Gender Distribution -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                <h3 class="font-extrabold text-slate-900 text-base">Gender Distribution</h3>
                <div class="h-64">
                    <canvas id="genderChart"></canvas>
                </div>
            </div>

            <!-- Pipeline Overview -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-extrabold text-slate-900 text-base">Recruitment Pipeline Split</h3>
                    <a href="{{ route('admin.recruitment.applications.index') }}" class="text-xs font-extrabold text-blue-700 hover:underline">Manage All &rarr;</a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-2">
                    <a href="{{ route('admin.recruitment.applications.index', ['status' => 'Under Review']) }}" class="p-4 bg-slate-50 hover:bg-slate-100 rounded-2xl text-center block transition-all border border-slate-100 hover:border-slate-200">
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-bold">Under Review</span>
                        <span class="text-xl font-black text-slate-900">{{ $metrics['under_review'] }}</span>
                    </a>
                    <a href="{{ route('admin.recruitment.applications.index', ['status' => 'Shortlisted']) }}" class="p-4 bg-slate-50 hover:bg-slate-100 rounded-2xl text-center block transition-all border border-slate-100 hover:border-amber-200">
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-bold">Shortlisted</span>
                        <span class="text-xl font-black text-amber-500">{{ $metrics['shortlisted'] }}</span>
                    </a>
                    <a href="{{ route('admin.recruitment.applications.index', ['status' => 'Written Test']) }}" class="p-4 bg-slate-50 hover:bg-slate-100 rounded-2xl text-center block transition-all border border-slate-100 hover:border-blue-200">
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-bold">Written Test</span>
                        <span class="text-xl font-black text-blue-500">{{ $metrics['written_tests'] }}</span>
                    </a>
                    <a href="{{ route('admin.recruitment.applications.index', ['status' => 'Interview Scheduled']) }}" class="p-4 bg-slate-50 hover:bg-slate-100 rounded-2xl text-center block transition-all border border-slate-100 hover:border-indigo-200">
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-bold">Interviews</span>
                        <span class="text-xl font-black text-indigo-500">{{ $metrics['interview_scheduled'] + $metrics['final_interviews'] }}</span>
                    </a>
                    <a href="{{ route('admin.recruitment.applications.index', ['status' => 'Selected']) }}" class="p-4 bg-slate-50 hover:bg-slate-100 rounded-2xl text-center block transition-all border border-slate-100 hover:border-emerald-200">
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-bold">Selected</span>
                        <span class="text-xl font-black text-emerald-500">{{ $metrics['selected_candidates'] }}</span>
                    </a>
                    <a href="{{ route('admin.recruitment.applications.index', ['status' => 'Rejected']) }}" class="p-4 bg-slate-50 hover:bg-slate-100 rounded-2xl text-center block transition-all border border-slate-100 hover:border-red-200">
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-bold">Rejected</span>
                        <span class="text-xl font-black text-red-500">{{ $metrics['rejected_candidates'] }}</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- External Redirection Tracking Analytics -->
        <div class="border-t pt-8 space-y-6">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-black text-slate-900">External Redirection & Tracking Analytics</h2>
                <span class="px-3 py-1 rounded-xl bg-amber-50 text-amber-600 text-[10px] font-extrabold uppercase">Ajira Market Integration</span>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <a href="{{ route('admin.recruitment.reports') }}" class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-2 card-hover-effect block hover:no-underline">
                    <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest block">Total External Applications</span>
                    <span class="text-3xl font-black text-slate-900 block">{{ $totalRedirects }}</span>
                    <span class="text-[10px] text-blue-500 font-extrabold">Total outbound candidates tracked</span>
                </a>

                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-2 card-hover-effect">
                    <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest block">Redirects by Provider</span>
                    <div class="space-y-1 pt-1">
                        @forelse($redirectsByProvider as $prov)
                            <div class="flex justify-between text-xs font-bold text-slate-800">
                                <span>{{ ucfirst($prov->provider) }}</span>
                                <span>{{ $prov->count }}</span>
                            </div>
                        @empty
                            <span class="text-[10px] text-slate-400">No redirects tracked yet</span>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-2 card-hover-effect">
                    <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest block">Top Redirected Vacancies</span>
                    <div class="space-y-1 pt-1 max-h-16 overflow-y-auto">
                        @forelse($redirectsByVacancy as $rv)
                            <div class="flex justify-between text-[11px] text-slate-700">
                                <span class="truncate pr-2 font-bold">{{ $rv->job_title }}</span>
                                <span class="font-black text-slate-900">{{ $rv->count }}</span>
                            </div>
                        @empty
                            <span class="text-[10px] text-slate-400">No vacancies tracked yet</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Conversion Funnel & Redirects Trend Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Daily Redirect Trends (last 30 days) -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                    <h3 class="font-extrabold text-slate-900 text-base">Daily Redirect Trends (Last 30 Days)</h3>
                    <div class="h-64">
                        <canvas id="redirectTrendChart"></canvas>
                    </div>
                </div>

                <!-- Conversion Funnel Graph -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                    <h3 class="font-extrabold text-slate-900 text-base">External Conversion Funnel</h3>
                    <div class="h-64">
                        <canvas id="conversionFunnelChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart JS Setup -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Designation Chart
            const desigCtx = document.getElementById('desigChart').getContext('2d');
            new Chart(desigCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($appsByDesig->pluck('designation_name')) !!},
                    datasets: [{
                        label: 'Applicants',
                        data: {!! json_encode($appsByDesig->pluck('count')) !!},
                        backgroundColor: '#1E40AF',
                        borderRadius: 8
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            // Trend Chart
            const trendCtx = document.getElementById('trendChart').getContext('2d');
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthlyTrends->pluck('month')) !!},
                    datasets: [{
                        label: 'Applications Trend',
                        data: {!! json_encode($monthlyTrends->pluck('count')) !!},
                        borderColor: '#F59E0B',
                        tension: 0.3,
                        fill: false
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            // Gender Chart
            const genderCtx = document.getElementById('genderChart').getContext('2d');
            new Chart(genderCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($genderDistribution->pluck('gender')) !!},
                    datasets: [{
                        data: {!! json_encode($genderDistribution->pluck('count')) !!},
                        backgroundColor: ['#1E40AF', '#EC4899', '#94A3B8']
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, cutout: '70%' }
            });

            // Redirect Daily Trend Chart
            const redirectCtx = document.getElementById('redirectTrendChart').getContext('2d');
            new Chart(redirectCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($dailyRedirectTrends->pluck('date')) !!},
                    datasets: [{
                        label: 'Redirects',
                        data: {!! json_encode($dailyRedirectTrends->pluck('count')) !!},
                        borderColor: '#8B5CF6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            // Conversion Funnel Chart
            const funnelCtx = document.getElementById('conversionFunnelChart').getContext('2d');
            new Chart(funnelCtx, {
                type: 'bar',
                data: {
                    labels: ['Vacancy Viewed', 'Logged In', 'Career Profile Completed', 'Redirected to Ajira Market'],
                    datasets: [{
                        label: 'Candidates count',
                        data: [
                            {{ $funnelData['viewed'] }},
                            {{ $funnelData['logged_in'] }},
                            {{ $funnelData['profile_completed'] }},
                            {{ $funnelData['redirected'] }}
                        ],
                        backgroundColor: ['#3B82F6', '#1E40AF', '#10B981', '#8B5CF6'],
                        borderRadius: 8
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        });
    </script>
</x-app-layout>
