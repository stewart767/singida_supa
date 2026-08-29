<x-app-layout title="Campuses Management">
    <x-slot name="header">Campuses</x-slot>

    <div class="w-full space-y-8" x-data="{ addModal: false, editModal: false, editData: { id: '', name: '', code: '', location: '', status: 'active' } }">
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-2xl text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-center">
            <h2 class="text-base font-extrabold text-slate-800">Manage Campuses</h2>
            <button @click="addModal = true" class="gradient-btn px-5 py-3 rounded-2xl text-white font-extrabold text-xs shadow-md">
                + Add Campus
            </button>
        </div>

        <!-- Campuses Table -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold tracking-wider">
                            <th class="py-3.5 px-4">Code</th>
                            <th class="py-3.5 px-4">Campus Name</th>
                            <th class="py-3.5 px-4">Location</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                        @forelse($campuses as $campus)
                            <tr class="hover:bg-slate-50">
                                <td class="py-4 px-4 font-black text-amber-500">{{ $campus->code }}</td>
                                <td class="py-4 px-4 font-bold text-slate-900">{{ $campus->name }}</td>
                                <td class="py-4 px-4 text-slate-500">{{ $campus->location ?? 'N/A' }}</td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase {{ $campus->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $campus->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button @click="editData = { id: {{ json_encode($campus->id) }}, name: {{ json_encode($campus->name) }}, code: {{ json_encode($campus->code) }}, location: {{ json_encode($campus->location) }}, status: {{ json_encode($campus->status) }} }; editModal = true" class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 text-[11px] font-bold transition-all duration-200">Edit</button>
                                        <form action="{{ route('admin.recruitment.campuses.delete', $campus->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Are you sure you want to delete this campus?')" class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 text-[11px] font-bold transition-all duration-200">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-500">No campuses configured.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Campus Modal -->
        <div x-show="addModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/40 backdrop-blur-sm" x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-md p-6 border border-slate-200 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="font-extrabold text-slate-900">Add New Campus</h3>
                    <button @click="addModal = false" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <form action="{{ route('admin.recruitment.campuses.store') }}" method="POST" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Campus Name</label>
                        <input type="text" name="name" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Campus Code (Unique)</label>
                        <input type="text" name="code" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Location</label>
                        <input type="text" name="location" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
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
                        <button type="submit" class="gradient-btn px-5 py-2.5 rounded-xl text-white">Save Campus</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Campus Modal -->
        <div x-show="editModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/40 backdrop-blur-sm" x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-md p-6 border border-slate-200 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="font-extrabold text-slate-900">Edit Campus</h3>
                    <button @click="editModal = false" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <form :action="'/admin/recruitment/campuses/' + editData.id" method="POST" class="space-y-4 text-xs font-semibold">
                    @csrf
                    @method('PUT')
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Campus Name</label>
                        <input type="text" name="name" x-model="editData.name" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Campus Code</label>
                        <input type="text" name="code" x-model="editData.code" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Location</label>
                        <input type="text" name="location" x-model="editData.location" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
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
                        <button type="submit" class="gradient-btn px-5 py-2.5 rounded-xl text-white">Update Campus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
