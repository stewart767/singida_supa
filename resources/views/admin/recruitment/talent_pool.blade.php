<x-app-layout title="Talent Pool Database">
    <x-slot name="header">Talent Pool Database</x-slot>

    <div class="w-full space-y-8">
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-2xl text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filter / Search Header -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm text-xs font-semibold">
            <form action="{{ route('admin.recruitment.talent-pool') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                <div class="space-y-1.5">
                    <label class="block text-slate-500">Search Candidate</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email..." class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                </div>
                <div class="space-y-1.5">
                    <label class="block text-slate-500">Talent Category</label>
                    <select name="category" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        <option value="">All Pools</option>
                        @foreach(['Graduate Pool', 'Technical Pool', 'Academic Pool', 'Administrative Pool'] as $cat)
                            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="gradient-btn flex-1 py-3 rounded-xl text-white font-extrabold text-xs shadow-md">
                        Search Pool
                    </button>
                    <a href="{{ route('admin.recruitment.talent-pool') }}" class="px-4 py-3 rounded-xl border text-center hover:bg-slate-50">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Talent Pool Table -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 overflow-hidden">
            <div class="overflow-x-auto text-xs font-semibold">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold tracking-wider">
                            <th class="py-3.5 px-4">Candidate Name</th>
                            <th class="py-3.5 px-4">Email</th>
                            <th class="py-3.5 px-4">Phone</th>
                            <th class="py-3.5 px-4">Talent Pool Category</th>
                            <th class="py-3.5 px-4">Enrollment Comments</th>
                            <th class="py-3.5 px-4">Date Added</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($candidates as $candidate)
                            <tr class="hover:bg-slate-50">
                                <td class="py-4 px-4 font-bold text-slate-900">{{ $candidate->user->name ?? 'N/A' }}</td>
                                <td class="py-4 px-4">{{ $candidate->user->email ?? 'N/A' }}</td>
                                <td class="py-4 px-4">{{ $candidate->user->phone ?? 'N/A' }}</td>
                                <td class="py-4 px-4 font-bold text-blue-600">{{ $candidate->category }}</td>
                                <td class="py-4 px-4 max-w-xs truncate">{{ $candidate->comments ?: 'None' }}</td>
                                <td class="py-4 px-4">{{ $candidate->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-500">No candidates in the talent pool database.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-4 border-t">
                {{ $candidates->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
