<x-app-layout title="Manage Account Profile">
    <x-slot name="header">Account Settings & Profile Management</x-slot>

    <div class="max-w-4xl mx-auto space-y-8">

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-red-500/10 border border-red-500/30 text-red-500 text-xs font-bold">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Profile Information Card -->
        <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200 shadow-xl space-y-6">
            
            <div class="flex items-center space-x-4 border-b border-slate-100 pb-6">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-500 to-blue-900 text-white font-black text-2xl flex items-center justify-center shadow-lg ring-4 ring-amber-500/20">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900">{{ $user->name }}</h2>
                    <p class="text-xs text-slate-500 font-bold mt-0.5">{{ $user->email }} &bull; <span class="uppercase text-amber-500 font-extrabold">{{ $user->role }}</span></p>
                </div>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">Full Legal Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                               class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-white text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required 
                               class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-white text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                           class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-white text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="pt-6 border-t border-slate-100 space-y-4">
                    <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Change Password (Optional)</h3>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">Current Password</label>
                        <input type="password" name="current_password" placeholder="••••••••" 
                               class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-white text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">New Password</label>
                            <input type="password" name="new_password" placeholder="••••••••" 
                                   class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-white text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation" placeholder="••••••••" 
                                   class="w-full px-4 py-3.5 rounded-2xl border border-slate-300 bg-white text-slate-900 text-sm font-semibold outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-4">
                    <button type="submit" class="gradient-btn px-8 py-3.5 rounded-2xl text-white font-extrabold text-sm shadow-xl">
                        Save Profile Changes
                    </button>
                </div>
            </form>

        </div>

    </div>
</x-app-layout>
