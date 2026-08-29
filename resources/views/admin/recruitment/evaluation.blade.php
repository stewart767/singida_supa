<x-app-layout title="Final Evaluation Desk">
    <x-slot name="header">Final Evaluation Desk</x-slot>

    <div class="w-full space-y-8" x-data="{ decisionModal: false, activeApp: {}, decision: '', comments: '' }">
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-2xl text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-center text-xs">
            <h2 class="text-base font-extrabold text-slate-800">Candidates Awaiting Decision</h2>
        </div>

        <!-- Evaluation List -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 overflow-hidden">
            <div class="overflow-x-auto text-xs font-semibold">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold tracking-wider">
                            <th class="py-3.5 px-4">Candidate</th>
                            <th class="py-3.5 px-4">Job Title</th>
                            <th class="py-3.5 px-4">Current Pipeline Status</th>
                            <th class="py-3.5 px-4">Assessment Metrics</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($applications as $app)
                            <tr class="hover:bg-slate-50">
                                <td class="py-4 px-4 font-bold text-slate-900">{{ $app->full_name }}</td>
                                <td class="py-4 px-4">{{ $app->vacancy->job_title ?? 'N/A' }}</td>
                                <td class="py-4 px-4 font-black text-amber-500">{{ $app->status }}</td>
                                <td class="py-4 px-4 space-y-1">
                                    @php
                                        $avgTotal = 0;
                                        $scCount = 0;
                                        foreach($app->interviews as $iv) {
                                            if (count($iv->scorecards) > 0) {
                                                $avgTotal += array_sum(array_map(fn($sc) => $sc->average_score, $iv->scorecards->all())) / count($iv->scorecards);
                                                $scCount++;
                                            }
                                        }
                                        $overallAvg = $scCount > 0 ? $avgTotal / $scCount : 0;
                                        $writtenTest = $app->writtenTests->first();
                                    @endphp
                                    <div>Interview Rating: <span class="font-black text-amber-500">{{ $overallAvg > 0 ? number_format($overallAvg, 1) . ' / 10' : 'N/A' }}</span></div>
                                    <div>Written Test Score: <span class="font-black text-blue-500">{{ $writtenTest && $writtenTest->marks !== null ? $writtenTest->marks . ' / 100' : 'N/A' }}</span></div>
                                </td>
                                <td class="py-4 px-4 text-right space-x-2">
                                    <button @click="activeApp = {{ json_encode($app) }}; decisionModal = true" class="gradient-btn px-4 py-2 rounded-xl text-white font-extrabold text-[10px] shadow-sm inline-block">Make Decision</button>
                                    <a href="{{ route('admin.recruitment.applications.show', $app->id) }}" class="text-xs text-slate-500 hover:underline">Review &rarr;</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-500">No candidates awaiting final decisions at this stage.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Decision Modal -->
        <div x-show="decisionModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/40 backdrop-blur-sm" x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-md p-6 border border-slate-200 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b pb-3">
                    <div>
                        <h3 class="font-extrabold text-slate-900">Record Final Evaluation Decision</h3>
                        <p class="text-[10px] text-slate-500" x-text="activeApp.full_name + ' - ' + activeApp.application_number"></p>
                    </div>
                    <button @click="decisionModal = false" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <form :action="'{{ url('/admin/recruitment/evaluations') }}/' + activeApp.id + '/decision'" method="POST" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Evaluation Decision</label>
                        <select name="decision" x-model="decision" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                            <option value="">Select Action</option>
                            <option value="Selected">Selected (Recommend Hire)</option>
                            <option value="Rejected">Rejected</option>
                            <option value="Request Another Interview">Request Another Interview</option>
                            <option value="Move Back">Move Back to Previous Stage</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Decision Comments (Required)</label>
                        <textarea name="comments" x-model="comments" placeholder="Comments required to complete evaluation action..." required rows="4" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="decisionModal = false" class="px-4 py-2.5 rounded-xl border hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="gradient-btn px-5 py-2.5 rounded-xl text-white">Save Decision</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
