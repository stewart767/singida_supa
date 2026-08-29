<x-app-layout title="Interview Schedule">
    <x-slot name="header">Interview Schedule Calendar</x-slot>

    <div class="w-full space-y-8" x-data="{ addModal: false }">
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-2xl text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-center text-xs">
            <h2 class="text-base font-extrabold text-slate-800">Scheduled Interviews</h2>
        </div>

        <!-- Interviews List -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 overflow-hidden">
            <div class="overflow-x-auto text-xs font-semibold">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold tracking-wider">
                            <th class="py-3.5 px-4">Candidate</th>
                            <th class="py-3.5 px-4">Job Title</th>
                            <th class="py-3.5 px-4">Interview Type</th>
                            <th class="py-3.5 px-4">Date / Time</th>
                            <th class="py-3.5 px-4">Venue / Link</th>
                            <th class="py-3.5 px-4">Panel Size</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($interviews as $interview)
                            <tr class="hover:bg-slate-50">
                                <td class="py-4 px-4 font-bold text-slate-900">{{ $interview->jobApplication->full_name }}</td>
                                <td class="py-4 px-4">{{ $interview->jobApplication->vacancy->job_title ?? 'N/A' }}</td>
                                <td class="py-4 px-4 font-bold text-blue-600">{{ $interview->type }}</td>
                                <td class="py-4 px-4 font-bold text-slate-900">{{ $interview->date->format('d M Y') }} at {{ $interview->time }}</td>
                                <td class="py-4 px-4 max-w-xs truncate">{{ $interview->venue ?: $interview->meeting_link }}</td>
                                <td class="py-4 px-4 font-black">{{ count($interview->panel_members ?? []) }} Members</td>
                                <td class="py-4 px-4 text-right">
                                    <a href="{{ route('admin.recruitment.applications.show', $interview->job_application_id) }}" class="text-xs text-amber-500 hover:underline">View Review &rarr;</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-500">No scheduled interviews found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
