<x-app-layout title="Recruitment Reports">
    <x-slot name="header">Recruitment Reports & Analytics</x-slot>

    <div class="w-full space-y-8">
        <!-- Export Panel -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4 text-xs font-semibold">
                <h3 class="font-extrabold text-slate-900 text-base">Export Raw Applications</h3>
                <p class="text-slate-500">Download a complete CSV spreadsheet containing applicant details, positions, vacancy tracking numbers, and application statuses.</p>
                <div class="pt-2">
                    <a href="{{ route('admin.recruitment.reports.export', ['type' => 'csv']) }}" class="gradient-btn px-6 py-3.5 rounded-2xl text-white font-extrabold shadow-md inline-block">
                        Download Excel / CSV &darr;
                    </a>
                </div>
            </div>

            <!-- Pipeline statistics summaries -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4 text-xs font-semibold">
                <h3 class="font-extrabold text-slate-900 text-base">Current Status Summary</h3>
                <div class="space-y-2">
                    @foreach($stageStats as $stat)
                        <div class="flex justify-between items-center py-2 border-b">
                            <span>{{ $stat->status }}</span>
                            <span class="font-black text-amber-500">{{ $stat->count }} candidates</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Vacancy Performance Analytics Table -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 overflow-hidden">
            <h3 class="font-extrabold text-slate-900 text-base mb-4">Vacancy Performance Metrics</h3>
            <div class="overflow-x-auto text-xs font-semibold">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold tracking-wider">
                            <th class="py-3.5 px-4">Vacancy Code</th>
                            <th class="py-3.5 px-4">Job Title</th>
                            <th class="py-3.5 px-4">Total Submissions</th>
                            <th class="py-3.5 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($vacancyPerformance as $vp)
                            <tr class="hover:bg-slate-50">
                                <td class="py-4 px-4 font-black text-blue-600">{{ $vp->vacancy_number }}</td>
                                <td class="py-4 px-4 font-bold text-slate-900">{{ $vp->job_title }}</td>
                                <td class="py-4 px-4 font-black">{{ $vp->applicant_count }} Applicants</td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-emerald-100 text-emerald-800">Active</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-500">No vacancy statistics compiled yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
