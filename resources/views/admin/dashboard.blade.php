<x-app-layout title="Admin Executive Dashboard">
    <x-slot name="header">Admissions Executive Dashboard</x-slot>

    <div class="w-full space-y-8">
        
        <!-- KPI Statistic Cards Grid (5 Cards) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
            <!-- Total Applications -->
            <a href="{{ route('admin.applications.index') }}" class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-2 card-hover-effect block hover:no-underline">
                <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest block">Total Applications</span>
                <span class="text-3xl font-black text-slate-900 block">{{ $metrics['total_applications'] }}</span>
                <span class="text-[10px] text-blue-500 font-extrabold">2026/2027 Cycle</span>
            </a>

            <!-- Pending Verification -->
            <a href="{{ route('admin.applications.index', ['status' => 'Under Review']) }}" class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-2 card-hover-effect block hover:no-underline">
                <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest block">Pending Review</span>
                <span class="text-3xl font-black text-amber-500 block">{{ $metrics['pending_applications'] }}</span>
                <span class="text-[10px] text-amber-500 font-extrabold">Requires Verification</span>
            </a>

            <!-- Approved Applications -->
            <a href="{{ route('admin.applications.index', ['status' => 'Approved']) }}" class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-2 card-hover-effect block hover:no-underline">
                <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest block">Approved</span>
                <span class="text-3xl font-black text-emerald-500 block">{{ $metrics['approved_applications'] }}</span>
                <span class="text-[10px] text-emerald-500 font-extrabold">Letters Issued</span>
            </a>

            <!-- Rejected Applications -->
            <a href="{{ route('admin.applications.index', ['status' => 'Rejected']) }}" class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-2 card-hover-effect block hover:no-underline">
                <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest block">Rejected</span>
                <span class="text-3xl font-black text-red-500 block">{{ $metrics['rejected_applications'] }}</span>
                <span class="text-[10px] text-red-500 font-extrabold">Not Qualified</span>
            </a>

            <!-- Total Revenue -->
            <a href="{{ route('admin.payments.index') }}" class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-2 card-hover-effect block hover:no-underline">
                <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest block">Revenue Collected</span>
                <span class="text-2xl font-black text-blue-600 block">TZS {{ number_format($metrics['total_revenue']) }}</span>
                <span class="text-[10px] text-slate-500 font-extrabold">Verified Fees</span>
            </a>
        </div>

        <!-- Interactive Chart.js Graphs Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Applications per Programme Chart -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                    <h3 class="font-extrabold text-slate-900 text-base">Applications per Programme</h3>
                    <span class="text-xs text-slate-500 font-bold">Distribution</span>
                </div>
                <div class="h-64">
                    <canvas id="programmeChart"></canvas>
                </div>
            </div>

            <!-- Admission Category Breakdown Chart -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                    <h3 class="font-extrabold text-slate-900 text-base">Admission Category Split</h3>
                    <span class="text-xs text-slate-500 font-bold">Direct Entry vs Foundation</span>
                </div>
                <div class="h-64">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Applications Data Table -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm space-y-4 p-6 overflow-hidden">
            <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-base">Recent Submissions</h3>
                    <p class="text-xs text-slate-500">Latest applicant submissions requiring review.</p>
                </div>
                <a href="{{ route('admin.applications.index') }}" class="text-xs font-extrabold text-amber-500 hover:underline">View All &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold tracking-wider">
                            <th class="py-3.5 px-4">Application #</th>
                            <th class="py-3.5 px-4">Applicant</th>
                            <th class="py-3.5 px-4">Programme</th>
                            <th class="py-3.5 px-4">Category</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-semibold">
                        @foreach($recentApplications as $app)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-4 px-4 font-black text-blue-600">{{ $app->application_number }}</td>
                                <td class="py-4 px-4 text-slate-900 font-bold">{{ $app->applicant->user->name ?? 'N/A' }}</td>
                                <td class="py-4 px-4 text-slate-600 font-bold">{{ $app->programme->code ?? 'N/A' }}</td>
                                <td class="py-4 px-4">{{ $app->admission_category }}</td>
                                <td class="py-4 px-4">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase {{ $app->status === 'Approved' ? 'bg-emerald-100 text-emerald-800' : ($app->status === 'Rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $app->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <a href="{{ route('admin.applications.show', $app->id) }}" class="gradient-btn px-4 py-2 rounded-xl text-white font-extrabold text-[10px] shadow-sm inline-block">
                                        Review &rarr;
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Chart.js Script Initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Programme Chart
            const progCtx = document.getElementById('programmeChart').getContext('2d');
            new Chart(progCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode(array_column($metrics['applications_per_programme']->toArray(), 'code')) !!},
                    datasets: [{
                        label: 'Applications Count',
                        data: {!! json_encode(array_column($metrics['applications_per_programme']->toArray(), 'applications_count')) !!},
                        backgroundColor: '#1E40AF',
                        borderRadius: 10,
                        borderSkipped: false
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    }
                }
            });

            // Category Chart
            const catCtx = document.getElementById('categoryChart').getContext('2d');
            new Chart(catCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode(array_column($metrics['admission_categories']->toArray(), 'admission_category')) !!},
                    datasets: [{
                        data: {!! json_encode(array_column($metrics['admission_categories']->toArray(), 'count')) !!},
                        backgroundColor: ['#F59E0B', '#1E40AF', '#16A34A'],
                        borderWidth: 0
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false,
                    cutout: '70%'
                }
            });
        });
    </script>
</x-app-layout>
