<x-app-layout title="Designations Management">
    <x-slot name="header">Designations</x-slot>

    <div class="w-full space-y-8" x-data="{ addModal: false, editModal: false, editData: { id: '', name: '', short_code: '', head_of_designation_id: '', description: '', status: 'active' } }">
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-2xl text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-center">
            <h2 class="text-base font-extrabold text-slate-800">Manage Designations</h2>
            <button @click="addModal = true" class="gradient-btn px-5 py-3 rounded-2xl text-white font-extrabold text-xs shadow-md">
                + Add Designation
            </button>
        </div>

        <!-- Designations Table -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold tracking-wider">
                            <th class="py-3.5 px-4">Code</th>
                            <th class="py-3.5 px-4">Designation Name</th>
                            <th class="py-3.5 px-4">Head of Designation</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                        @forelse($designations as $desig)
                            <tr class="hover:bg-slate-50">
                                <td class="py-4 px-4 font-black text-amber-500">{{ $desig->short_code }}</td>
                                <td class="py-4 px-4 font-bold text-slate-900">{{ $desig->name }}</td>
                                <td class="py-4 px-4">{{ $desig->headOfDesignation->name ?? 'None' }}</td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase {{ $desig->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $desig->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end">
                                        <button @click="editData = { id: {{ json_encode($desig->id) }}, name: {{ json_encode($desig->name) }}, short_code: {{ json_encode($desig->short_code) }}, head_of_designation_id: {{ json_encode($desig->head_of_designation_id) }}, description: {{ json_encode($desig->description) }}, status: {{ json_encode($desig->status) }} }; editModal = true" class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 text-[11px] font-bold transition-all duration-200">Edit</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-500">No designations found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Designation Modal -->
        <div x-show="addModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/40 backdrop-blur-sm" x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-md p-6 border border-slate-200 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="font-extrabold text-slate-900">Add New Designation</h3>
                    <button @click="addModal = false" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <form action="{{ route('admin.recruitment.designations.store') }}" method="POST" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-2 space-y-1.5">
                            <label class="block text-slate-500">Designation Name</label>
                            <input type="text" name="name" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Short Code</label>
                            <input type="text" name="short_code" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Head of Designation</label>
                        <select name="head_of_designation_id" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                            <option value="">Select Staff Member</option>
                            @foreach($staff as $member)
                                <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->role }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Description</label>
                        <textarea name="description" rows="3" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950"></textarea>
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
                        <button type="submit" class="gradient-btn px-5 py-2.5 rounded-xl text-white">Save Designation</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Designation Modal -->
        <div x-show="editModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/40 backdrop-blur-sm" x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-md p-6 border border-slate-200 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="font-extrabold text-slate-900">Edit Designation</h3>
                    <button @click="editModal = false" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <form :action="'{{ url('/admin/recruitment/designations') }}/' + editData.id" method="POST" class="space-y-4 text-xs font-semibold">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-2 space-y-1.5">
                            <label class="block text-slate-500">Designation Name</label>
                            <input type="text" name="name" x-model="editData.name" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Short Code</label>
                            <input type="text" name="short_code" x-model="editData.short_code" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Head of Designation</label>
                        <select name="head_of_designation_id" x-model="editData.head_of_designation_id" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                            <option value="">Select Staff Member</option>
                            @foreach($staff as $member)
                                <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->role }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Description</label>
                        <textarea name="description" x-model="editData.description" rows="3" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950"></textarea>
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
                        <button type="submit" class="gradient-btn px-5 py-2.5 rounded-xl text-white">Update Designation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
