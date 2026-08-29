<x-app-layout title="Positions Management">
    <x-slot name="header">Positions</x-slot>

    <div class="w-full space-y-8" x-data="{ addModal: false, editModal: false, editData: { id: '', name: '', designation_id: '', job_category_id: '', campus_id: '', employment_type: 'Full-time', reports_to_position_id: '', salary_grade: '', status: 'active' } }">
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-2xl text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-center">
            <h2 class="text-base font-extrabold text-slate-800">Manage Job Positions</h2>
            <button @click="addModal = true" class="gradient-btn px-5 py-3 rounded-2xl text-white font-extrabold text-xs shadow-md">
                + Add Position
            </button>
        </div>

        <!-- Positions Table -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold tracking-wider">
                            <th class="py-3.5 px-4">Position Name</th>
                            <th class="py-3.5 px-4">Designation</th>
                            <th class="py-3.5 px-4">Campus</th>
                            <th class="py-3.5 px-4">Type</th>
                            <th class="py-3.5 px-4">Reports To</th>
                            <th class="py-3.5 px-4">Grade</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                        @forelse($positions as $pos)
                            <tr class="hover:bg-slate-50">
                                <td class="py-4 px-4 font-bold text-slate-900">{{ $pos->name }}</td>
                                <td class="py-4 px-4">{{ $pos->designation->name ?? 'N/A' }}</td>
                                <td class="py-4 px-4 text-slate-500 font-bold">{{ $pos->campus->name ?? 'All Campuses' }}</td>
                                <td class="py-4 px-4">{{ $pos->employment_type }}</td>
                                <td class="py-4 px-4">{{ $pos->reportsTo->name ?? 'None' }}</td>
                                <td class="py-4 px-4 font-black">{{ $pos->salary_grade ?: 'N/A' }}</td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase {{ $pos->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $pos->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end">
                                        <button @click="editData = { id: {{ json_encode($pos->id) }}, name: {{ json_encode($pos->name) }}, designation_id: {{ json_encode($pos->designation_id) }}, job_category_id: {{ json_encode($pos->job_category_id) }}, campus_id: {{ json_encode($pos->campus_id) }}, employment_type: {{ json_encode($pos->employment_type) }}, reports_to_position_id: {{ json_encode($pos->reports_to_position_id) }}, salary_grade: {{ json_encode($pos->salary_grade) }}, status: {{ json_encode($pos->status) }} }; editModal = true" class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 text-[11px] font-bold transition-all duration-200">Edit</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-8 text-center text-slate-500">No positions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Position Modal -->
        <div x-show="addModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/40 backdrop-blur-sm" x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-md p-6 border border-slate-200 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="font-extrabold text-slate-900">Add New Position</h3>
                    <button @click="addModal = false" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <form action="{{ route('admin.recruitment.positions.store') }}" method="POST" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Position Name</label>
                        <input type="text" name="name" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5 col-span-2">
                            <label class="block text-slate-500">Designation</label>
                            <select name="designation_id" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                <option value="">Select Designation</option>
                                @foreach($designations as $desig)
                                    <option value="{{ $desig->id }}">{{ $desig->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Campus Assignment</label>
                            <select name="campus_id" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                <option value="">All Campuses</option>
                                @foreach($campuses as $campus)
                                    <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Employment Type</label>
                            <select name="employment_type" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                <option value="Full-time">Full-time</option>
                                <option value="Part-time">Part-time</option>
                                <option value="Contract">Contract</option>
                                <option value="Internship">Internship</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Reports To (Position)</label>
                            <select name="reports_to_position_id" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                <option value="">None (Top Level)</option>
                                @foreach($positions as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Salary Grade</label>
                            <input type="text" name="salary_grade" placeholder="e.g. PG 8" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Status</label>
                        <select name="status" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                            <option value="active">Active</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="addModal = false" class="px-4 py-2.5 rounded-xl border hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="gradient-btn px-5 py-2.5 rounded-xl text-white">Save Position</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Position Modal -->
        <div x-show="editModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/40 backdrop-blur-sm" x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-md p-6 border border-slate-200 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="font-extrabold text-slate-900">Edit Position</h3>
                    <button @click="editModal = false" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <form :action="'{{ url('/admin/recruitment/positions') }}/' + editData.id" method="POST" class="space-y-4 text-xs font-semibold">
                    @csrf
                    @method('PUT')
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Position Name</label>
                        <input type="text" name="name" x-model="editData.name" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5 col-span-2">
                            <label class="block text-slate-500">Designation</label>
                            <select name="designation_id" x-model="editData.designation_id" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                <option value="">Select Designation</option>
                                @foreach($designations as $desig)
                                    <option value="{{ $desig->id }}">{{ $desig->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Campus Assignment</label>
                            <select name="campus_id" x-model="editData.campus_id" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                <option value="">All Campuses</option>
                                @foreach($campuses as $campus)
                                    <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Employment Type</label>
                            <select name="employment_type" x-model="editData.employment_type" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                <option value="Full-time">Full-time</option>
                                <option value="Part-time">Part-time</option>
                                <option value="Contract">Contract</option>
                                <option value="Internship">Internship</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Reports To (Position)</label>
                            <select name="reports_to_position_id" x-model="editData.reports_to_position_id" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                <option value="">None (Top Level)</option>
                                @foreach($positions as $p)
                                    @if($p->id != $pos->id)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Salary Grade</label>
                            <input type="text" name="salary_grade" x-model="editData.salary_grade" placeholder="e.g. PG 8" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Status</label>
                        <select name="status" x-model="editData.status" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                            <option value="active">Active</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="editModal = false" class="px-4 py-2.5 rounded-xl border hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="gradient-btn px-5 py-2.5 rounded-xl text-white">Update Position</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
