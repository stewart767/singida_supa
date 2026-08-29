<x-app-layout title="Job Categories Management">
    <x-slot name="header">Job Categories</x-slot>

    <div class="w-full space-y-8" x-data="{ addModal: false, editModal: false, editData: { id: '', name: '', description: '', status: 'active', display_order: 0 } }">
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-2xl text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-center">
            <h2 class="text-base font-extrabold text-slate-800">Manage Category Directory</h2>
            <button @click="addModal = true" class="gradient-btn px-5 py-3 rounded-2xl text-white font-extrabold text-xs shadow-md">
                + Create Category
            </button>
        </div>

        <!-- Categories Table -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold tracking-wider">
                            <th class="py-3.5 px-4">Display Order</th>
                            <th class="py-3.5 px-4">Name</th>
                            <th class="py-3.5 px-4">Description</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                        @forelse($categories as $category)
                            <tr class="hover:bg-slate-50">
                                <td class="py-4 px-4 font-black">{{ $category->display_order }}</td>
                                <td class="py-4 px-4 font-bold text-slate-900">{{ $category->name }}</td>
                                <td class="py-4 px-4 max-w-xs truncate">{{ $category->description ?: 'N/A' }}</td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase {{ $category->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $category->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button @click="editData = { id: {{ json_encode($category->id) }}, name: {{ json_encode($category->name) }}, description: {{ json_encode($category->description) }}, status: {{ json_encode($category->status) }}, display_order: {{ json_encode($category->display_order) }} }; editModal = true" class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 text-[11px] font-bold transition-all duration-200">Edit</button>
                                        <form action="{{ route('admin.recruitment.categories.destroy', $category->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this category?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 text-[11px] font-bold transition-all duration-200">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-500">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Category Modal -->
        <div x-show="addModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/40 backdrop-blur-sm" x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-md p-6 border border-slate-200 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="font-extrabold text-slate-900">Create New Job Category</h3>
                    <button @click="addModal = false" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <form action="{{ route('admin.recruitment.categories.store') }}" method="POST" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Category Name</label>
                        <input type="text" name="name" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Description</label>
                        <textarea name="description" rows="3" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Status</label>
                            <select name="status" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                <option value="active">Active</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Display Order</label>
                            <input type="number" name="display_order" value="0" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="addModal = false" class="px-4 py-2.5 rounded-xl border hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="gradient-btn px-5 py-2.5 rounded-xl text-white">Save Category</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Category Modal -->
        <div x-show="editModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/40 backdrop-blur-sm" x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-md p-6 border border-slate-200 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="font-extrabold text-slate-900">Edit Job Category</h3>
                    <button @click="editModal = false" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <form :action="'{{ url('/admin/recruitment/categories') }}/' + editData.id" method="POST" class="space-y-4 text-xs font-semibold">
                    @csrf
                    @method('PUT')
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Category Name</label>
                        <input type="text" name="name" x-model="editData.name" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Description</label>
                        <textarea name="description" x-model="editData.description" rows="3" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Status</label>
                            <select name="status" x-model="editData.status" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                                <option value="active">Active</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Display Order</label>
                            <input type="number" name="display_order" x-model="editData.display_order" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="editModal = false" class="px-4 py-2.5 rounded-xl border hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="gradient-btn px-5 py-2.5 rounded-xl text-white">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
