<x-app-layout title="Interview Scores & Rankings">
    <x-slot name="header">Interview Scorecards & Rankings</x-slot>

    <div class="w-full space-y-8" x-data="{ scorecardModal: false, activeInterviewId: '' }">
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-2xl text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-center text-xs">
            <h2 class="text-base font-extrabold text-slate-800">Interviews Score Summary</h2>
        </div>

        <!-- Scores List -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 overflow-hidden">
            <div class="overflow-x-auto text-xs font-semibold">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold tracking-wider">
                            <th class="py-3.5 px-4">Candidate</th>
                            <th class="py-3.5 px-4">Job Title</th>
                            <th class="py-3.5 px-4">Type</th>
                            <th class="py-3.5 px-4">Interview Date</th>
                            <th class="py-3.5 px-4">Submitted Scorecards</th>
                            <th class="py-3.5 px-4">Average Rating</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($interviews as $interview)
                            <tr class="hover:bg-slate-50">
                                <td class="py-4 px-4 font-bold text-slate-900">{{ $interview->jobApplication->full_name }}</td>
                                <td class="py-4 px-4">{{ $interview->jobApplication->vacancy->job_title ?? 'N/A' }}</td>
                                <td class="py-4 px-4">{{ $interview->type }}</td>
                                <td class="py-4 px-4">{{ $interview->date->format('d M Y') }}</td>
                                <td class="py-4 px-4 font-bold text-slate-500">{{ count($interview->scorecards) }} Submitted</td>
                                <td class="py-4 px-4 font-black">
                                    @php
                                        $avg = count($interview->scorecards) > 0 ? array_sum(array_map(fn($sc) => $sc->average_score, $interview->scorecards->all())) / count($interview->scorecards) : 0;
                                    @endphp
                                    <span class="text-sm text-amber-500">{{ $avg > 0 ? number_format($avg, 1) . ' / 10' : 'Pending scores' }}</span>
                                </td>
                                <td class="py-4 px-4 text-right space-x-2">
                                    @if(in_array(Auth::id(), $interview->panel_members ?? []))
                                        <button @click="activeInterviewId = '{{ $interview->id }}'; scorecardModal = true" class="text-xs text-blue-500 hover:underline">Submit Scorecard</button>
                                    @endif
                                    <a href="{{ route('admin.recruitment.applications.show', $interview->job_application_id) }}" class="text-xs text-amber-500 hover:underline">Details &rarr;</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-500">No scheduled or scored interviews found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Submit Scorecard Modal -->
        <div x-show="scorecardModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/40 backdrop-blur-sm" x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-md p-6 border border-slate-200 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="font-extrabold text-slate-900">Submit Interview Scorecard</h3>
                    <button @click="scorecardModal = false" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <form action="{{ route('admin.recruitment.scores.store') }}" method="POST" class="space-y-3 text-xs font-semibold max-h-[70vh] overflow-y-auto pr-1">
                    @csrf
                    <input type="hidden" name="interview_id" x-model="activeInterviewId">
                    
                    @foreach(['communication', 'technical_knowledge', 'problem_solving', 'leadership', 'teamwork', 'confidence', 'professionalism'] as $field)
                        <div class="space-y-1.5">
                            <label class="block text-slate-500 capitalize">{{ str_replace('_', ' ', $field) }} Score (1 - 10)</label>
                            <input type="number" name="{{ $field }}" min="1" max="10" required class="w-full p-2.5 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                    @endforeach

                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Interviewer Evaluation Comments</label>
                        <textarea name="comments" rows="2" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="scorecardModal = false" class="px-4 py-2.5 rounded-xl border hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="gradient-btn px-5 py-2.5 rounded-xl text-white">Submit Scorecard</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
