<x-app-layout title="Offer Letters Generator">
    <x-slot name="header">Offer Letters Generator</x-slot>

    <div class="w-full space-y-8" x-data="{ generateModal: false }">
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-2xl text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-center text-xs">
            <h2 class="text-base font-extrabold text-slate-800">Generated Employment Offer Letters</h2>
            <button @click="generateModal = true" class="gradient-btn px-5 py-3 rounded-2xl text-white font-extrabold text-xs shadow-md">
                + Generate Offer Letter
            </button>
        </div>

        <!-- Offer Letters Table -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 overflow-hidden">
            <div class="overflow-x-auto text-xs font-semibold">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold tracking-wider">
                            <th class="py-3.5 px-4">Candidate</th>
                            <th class="py-3.5 px-4">Job Title</th>
                            <th class="py-3.5 px-4">Basic Salary</th>
                            <th class="py-3.5 px-4">Reporting Date</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($offerLetters as $letter)
                            <tr class="hover:bg-slate-50">
                                <td class="py-4 px-4 font-bold text-slate-900">{{ $letter->jobApplication->full_name }}</td>
                                <td class="py-4 px-4">{{ $letter->jobApplication->vacancy->job_title ?? 'N/A' }}</td>
                                <td class="py-4 px-4 font-bold text-blue-600">{{ $letter->salary }}</td>
                                <td class="py-4 px-4 font-bold text-slate-900">{{ $letter->reporting_date->format('d M Y') }}</td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase {{ $letter->status === 'Accepted' ? 'bg-emerald-100 text-emerald-800' : ($letter->status === 'Declined' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $letter->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right space-x-2">
                                    <a href="{{ asset('storage/' . $letter->pdf_path) }}" target="_blank" class="text-xs text-blue-500 hover:underline">View Document</a>
                                    <a href="{{ route('admin.recruitment.applications.show', $letter->job_application_id) }}" class="text-xs text-amber-500 hover:underline">Review Candidate &rarr;</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-500">No offer letters generated yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Generate Offer Letter Modal -->
        <div x-show="generateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/40 backdrop-blur-sm" x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-md p-6 border border-slate-200 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="font-extrabold text-slate-900">Generate Job Offer Letter</h3>
                    <button @click="generateModal = false" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <form action="{{ route('admin.recruitment.offer-letters.generate') }}" method="POST" class="space-y-4 text-xs font-semibold max-h-[70vh] overflow-y-auto pr-1">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Select Selected Candidate</label>
                        <select name="job_application_id" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                            <option value="">Select Candidate</option>
                            @foreach($selectedApplications as $app)
                                <option value="{{ $app->id }}">{{ $app->full_name }} ({{ $app->vacancy->job_title ?? '' }} - {{ $app->application_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Monthly Basic Salary</label>
                        <input type="text" name="salary" placeholder="e.g. TZS 2,200,000 / month" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Benefits & Allowances</label>
                        <textarea name="benefits" placeholder="Medical scheme, housing allowance, annual bonus plan..." required rows="2" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950"></textarea>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Reporting Date</label>
                        <input type="date" name="reporting_date" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Contract Employment Terms</label>
                        <textarea name="employment_terms" required rows="3" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="generateModal = false" class="px-4 py-2.5 rounded-xl border hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="gradient-btn px-5 py-2.5 rounded-xl text-white">Generate Offer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
