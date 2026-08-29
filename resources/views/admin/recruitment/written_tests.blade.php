<x-app-layout title="Written Tests Management">
    <x-slot name="header">Written Tests</x-slot>

    <div class="w-full space-y-8" x-data="{ assignModal: false, marksModal: false, activeTest: {} }">
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-2xl text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-center text-xs">
            <h2 class="text-base font-extrabold text-slate-800">Written Examinations Directory</h2>
            <button @click="assignModal = true" class="gradient-btn px-5 py-3 rounded-2xl text-white font-extrabold text-xs shadow-md">
                + Assign Test
            </button>
        </div>

        <!-- Written Tests List -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 overflow-hidden">
            <div class="overflow-x-auto text-xs font-semibold">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold tracking-wider">
                            <th class="py-3.5 px-4">Candidate</th>
                            <th class="py-3.5 px-4">Job Title</th>
                            <th class="py-3.5 px-4">Test Name</th>
                            <th class="py-3.5 px-4">Assigned Date</th>
                            <th class="py-3.5 px-4">Marks</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($tests as $test)
                            <tr class="hover:bg-slate-50">
                                <td class="py-4 px-4 font-bold text-slate-900">{{ $test->jobApplication->full_name }}</td>
                                <td class="py-4 px-4">{{ $test->jobApplication->vacancy->job_title ?? 'N/A' }}</td>
                                <td class="py-4 px-4 font-bold text-blue-600">{{ $test->test_name }}</td>
                                <td class="py-4 px-4">{{ $test->assigned_date->format('d M Y') }}</td>
                                <td class="py-4 px-4 font-black">
                                    {{ $test->marks !== null ? $test->marks . ' / 100' : 'Pending' }}
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase {{ $test->status === 'Completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $test->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right space-x-2">
                                    @if($test->status !== 'Completed')
                                        <button @click="activeTest = {{ json_encode($test) }}; marksModal = true" class="text-xs text-amber-500 hover:underline">Record Marks</button>
                                    @endif
                                    <a href="{{ route('admin.recruitment.applications.show', $test->job_application_id) }}" class="text-xs text-slate-500 hover:underline">Review &rarr;</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-500">No written tests assigned.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Assign Written Test Modal -->
        <div x-show="assignModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/40 backdrop-blur-sm" x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-md p-6 border border-slate-200 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="font-extrabold text-slate-900">Assign Written Examination</h3>
                    <button @click="assignModal = false" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <form action="{{ route('admin.recruitment.written-tests.assign') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Select Shortlisted Applicant</label>
                        <select name="job_application_id" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                            <option value="">Select Candidate</option>
                            @foreach($shortlistedApplications as $app)
                                <option value="{{ $app->id }}">{{ $app->full_name }} ({{ $app->vacancy->job_title ?? '' }} - {{ $app->application_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Test / Exam Title</label>
                        <input type="text" name="test_name" placeholder="e.g. Technical Coding Assessment" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Assignment Date</label>
                        <input type="date" name="assigned_date" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Upload Question Script (Optional)</label>
                        <input type="file" name="questions_file" class="w-full p-2 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="assignModal = false" class="px-4 py-2.5 rounded-xl border hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="gradient-btn px-5 py-2.5 rounded-xl text-white">Assign Exam</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Record Marks Modal -->
        <div x-show="marksModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/40 backdrop-blur-sm" x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-md p-6 border border-slate-200 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b pb-3">
                    <div>
                        <h3 class="font-extrabold text-slate-900">Record Examination Marks</h3>
                        <p class="text-[10px] text-slate-500" x-text="activeTest.test_name"></p>
                    </div>
                    <button @click="marksModal = false" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <form :action="'{{ url('/admin/recruitment/written-tests') }}/' + activeTest.id + '/marks'" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Score Obtained (0 - 100)</label>
                        <input type="number" name="marks" min="0" max="100" step="0.1" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Remarks / Evaluation Comments</label>
                        <textarea name="comments" rows="2" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950"></textarea>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Upload Completed Answer Script (Optional)</label>
                        <input type="file" name="script_file" class="w-full p-2 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="marksModal = false" class="px-4 py-2.5 rounded-xl border hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="gradient-btn px-5 py-2.5 rounded-xl text-white">Save Marks</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
