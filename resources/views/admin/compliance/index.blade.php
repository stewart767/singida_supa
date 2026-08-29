<x-app-layout title="Personal Data Compliance Dashboard">
    <x-slot name="header">Personal Data & GDPR Compliance Portal</x-slot>

    <div class="w-full space-y-8" x-data="{ activeTab: '{{ request()->has('pending_page') ? 'pending' : 'logs' }}' }">
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-2xl text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-red-500/10 border border-red-500/20 text-red-500 rounded-2xl text-xs font-bold">
                {{ session('error') }}
            </div>
        @endif

        <!-- Compliance Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-2 card-hover-effect">
                <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest block">Total Consents Recorded</span>
                <span class="text-3xl font-black text-slate-900 block">{{ $stats['total_accepted'] }}</span>
                <span class="text-[10px] text-blue-500 font-extrabold">Active consents in system</span>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-2 card-hover-effect">
                <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest block">Active Policy Version</span>
                <span class="text-3xl font-black text-amber-500 block">
                    {{ $policies->where('status', 'Published')->first()->version ?? 'None' }}
                </span>
                <span class="text-[10px] text-amber-500 font-extrabold">Currently presented to applicants</span>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-2 card-hover-effect">
                <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest block">Active Terms Version</span>
                <span class="text-3xl font-black text-emerald-500 block">
                    {{ $terms->where('status', 'Published')->first()->version ?? 'None' }}
                </span>
                <span class="text-[10px] text-emerald-500 font-extrabold">Mandatory agreement version</span>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-2 card-hover-effect">
                <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest block">Withdrawal Requests</span>
                <span class="text-3xl font-black text-red-500 block">{{ $stats['total_withdrawn'] }}</span>
                <span class="text-[10px] text-red-400 font-extrabold">Data deletion / opt-outs</span>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex border-b border-slate-200 space-x-6 text-sm font-extrabold">
            <button @click="activeTab = 'logs'" :class="activeTab === 'logs' ? 'border-b-2 border-blue-800 text-blue-800 pb-3' : 'text-slate-500 pb-3 hover:text-slate-900'">
                Consent Audit Logs
            </button>
            <button @click="activeTab = 'pending'" :class="activeTab === 'pending' ? 'border-b-2 border-blue-800 text-blue-800 pb-3' : 'text-slate-500 pb-3 hover:text-slate-900'">
                Pending Acceptance
            </button>
            <button @click="activeTab = 'privacy'" :class="activeTab === 'privacy' ? 'border-b-2 border-blue-800 text-blue-800 pb-3' : 'text-slate-500 pb-3 hover:text-slate-900'">
                Privacy Policies
            </button>
            <button @click="activeTab = 'terms'" :class="activeTab === 'terms' ? 'border-b-2 border-blue-800 text-blue-800 pb-3' : 'text-slate-500 pb-3 hover:text-slate-900'">
                Terms & Conditions
            </button>
            <button @click="activeTab = 'stats'" :class="activeTab === 'stats' ? 'border-b-2 border-blue-800 text-blue-800 pb-3' : 'text-slate-500 pb-3 hover:text-slate-900'">
                Consent Stats
            </button>
        </div>

        <!-- Tab 1: Audit Logs -->
        <div x-show="activeTab === 'logs'" class="space-y-6">
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                
                @if(request()->has('applicant_id'))
                    <div class="p-3 bg-amber-500/10 border border-amber-500/20 text-amber-600 rounded-2xl text-xs font-bold flex justify-between items-center">
                        <span>Showing consent history for selected applicant.</span>
                        <a href="{{ route('admin.compliance.index') }}" class="underline hover:text-amber-800">Clear History Filter</a>
                    </div>
                @endif

                <!-- Search & Dropdown Filters Bar -->
                <form method="GET" action="{{ route('admin.compliance.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end text-xs font-bold text-slate-700">
                    <div>
                        <label class="block text-slate-500 uppercase text-[9px] mb-1 font-extrabold tracking-wider">Search</label>
                        <input type="text" name="search" value="{{ request()->search ?? '' }}" placeholder="Name, Email or App No..." 
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-slate-50 text-xs outline-none focus:ring-2 focus:ring-amber-500 font-semibold text-slate-900">
                    </div>
                    <div>
                        <label class="block text-slate-500 uppercase text-[9px] mb-1 font-extrabold tracking-wider">Admission Cycle</label>
                        <select name="cycle" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-slate-50 text-xs outline-none focus:ring-2 focus:ring-amber-500 font-semibold text-slate-900">
                            <option value="">All Cycles</option>
                            @foreach($cyclesList as $c)
                                <option value="{{ $c->id }}" {{ request()->cycle == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-500 uppercase text-[9px] mb-1 font-extrabold tracking-wider">Programme</label>
                        <select name="programme" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-slate-50 text-xs outline-none focus:ring-2 focus:ring-amber-500 font-semibold text-slate-900">
                            <option value="">All Programmes</option>
                            @foreach($programmesList as $p)
                                <option value="{{ $p->id }}" {{ request()->programme == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-500 uppercase text-[9px] mb-1 font-extrabold tracking-wider">Policy Version</label>
                        <select name="policy_version" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-slate-50 text-xs outline-none focus:ring-2 focus:ring-amber-500 font-semibold text-slate-900">
                            <option value="">All Versions</option>
                            @foreach($distinctVersions as $v)
                                <option value="{{ $v }}" {{ request()->policy_version == $v ? 'selected' : '' }}>Version {{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-grow gradient-btn py-2 rounded-xl text-white font-extrabold text-xs">Apply Filters</button>
                        <a href="{{ route('admin.compliance.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-extrabold hover:bg-slate-200 border border-slate-200 transition-colors text-center flex items-center justify-center">Reset</a>
                    </div>
                </form>

                <!-- Export Reports Buttons -->
                <div class="flex justify-end gap-3 pt-2 border-t border-slate-100">
                    <a href="{{ route('admin.compliance.logs.export', request()->query()) }}" target="_blank" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-sm transition-colors">
                        📥 Export CSV Report
                    </a>
                    <a href="{{ route('admin.compliance.logs.pdf', request()->query()) }}" target="_blank" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-extrabold text-xs shadow-sm transition-colors">
                        🖨️ Export PDF / Print
                    </a>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold tracking-wider">
                                <th class="py-3.5 px-4">Applicant</th>
                                <th class="py-3.5 px-4">Application</th>
                                <th class="py-3.5 px-4">Versions</th>
                                <th class="py-3.5 px-4">Language</th>
                                <th class="py-3.5 px-4">Source & Device</th>
                                <th class="py-3.5 px-4">Audit Metadata</th>
                                <th class="py-3.5 px-4">Digital Hash</th>
                                <th class="py-3.5 px-4 text-right">Timestamp</th>
                                <th class="py-3.5 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                            @forelse($logs as $log)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-4 px-4 font-bold text-slate-900">
                                        <span class="font-extrabold block text-slate-900">{{ $log->application->applicant->user->name ?? 'N/A' }}</span>
                                        <span class="text-[10px] text-slate-500 block">{{ $log->application->applicant->user->email ?? 'N/A' }}</span>
                                    </td>
                                    <td class="py-4 px-4 text-blue-600 font-bold">
                                        @if($log->application)
                                            <a href="{{ route('admin.applications.show', $log->application_id) }}" class="hover:underline">
                                                {{ $log->application->application_number ?? 'N/A' }}
                                            </a>
                                        @else
                                            <span class="text-slate-400 italic">Pre-Application</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-slate-900">
                                        <span class="block text-[11px]">Policy: <strong>{{ $log->privacyPolicy->version ?? $log->consent_version }}</strong></span>
                                        <span class="block text-[11px]">Terms: <strong>{{ $log->termsCondition->version ?? $log->consent_version }}</strong></span>
                                    </td>
                                    <td class="py-4 px-4 uppercase text-[10px] text-slate-800">
                                        {{ $log->consent_language }}
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="block uppercase text-[10px] text-indigo-600 font-black">{{ $log->consent_source }}</span>
                                        <span class="text-slate-500 text-[10px] font-normal block">{{ $log->device_type }} / {{ $log->browser_name }}</span>
                                    </td>
                                    <td class="py-4 px-4 font-mono text-[10px] text-slate-500">
                                        <span class="block font-bold">IP: {{ $log->ip_address }}</span>
                                        <span class="block truncate max-w-xs font-normal" title="{{ $log->user_agent }}">{{ $log->user_agent }}</span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="font-mono text-[10px] px-2 py-0.5 rounded bg-slate-100 block max-w-xs truncate" title="{{ $log->consent_hash }}">
                                            {{ substr($log->consent_hash, 0, 10) }}...
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-right text-slate-500">
                                        {{ $log->consented_at ? $log->consented_at->format('d M Y, h:i A') : 'N/A' }}
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        <a href="{{ route('admin.compliance.index', ['applicant_id' => $log->applicant_id]) }}" class="px-2.5 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-800 text-[10px] font-extrabold inline-block transition-colors">
                                            View History
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-8 text-center text-slate-400 font-bold">No compliance records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pt-4">
                    {{ $logs->appends(request()->query())->links() }}
                </div>
            </div>
        </div>

        <!-- Tab 2: Pending Acceptance -->
        <div x-show="activeTab === 'pending'" class="space-y-6" x-cloak>
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-slate-900">Applicants Pending Latest Policy Version (v{{ $latestVersion ?? 'None' }})</h3>
                    <span class="px-3 py-1 bg-red-50 text-red-600 rounded-full font-black text-xs">
                        {{ $pendingApplicants->total() }} Outdated Consents
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold tracking-wider">
                                <th class="py-3.5 px-4">Applicant Name</th>
                                <th class="py-3.5 px-4">Email</th>
                                <th class="py-3.5 px-4">Last Accepted Policy</th>
                                <th class="py-3.5 px-4">Last Accepted Terms</th>
                                <th class="py-3.5 px-4">Consent Status</th>
                                <th class="py-3.5 px-4 text-right">Last Consent Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                            @forelse($pendingApplicants as $p)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-4 px-4 font-extrabold text-slate-900">
                                        {{ $p->user->name ?? 'N/A' }}
                                    </td>
                                    <td class="py-4 px-4">{{ $p->user->email ?? 'N/A' }}</td>
                                    <td class="py-4 px-4 text-red-500 font-bold">{{ $p->privacy_policy_version ?? 'Never' }}</td>
                                    <td class="py-4 px-4 text-red-500 font-bold">{{ $p->terms_version ?? 'Never' }}</td>
                                    <td class="py-4 px-4">
                                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase {{ $p->consent_status === 'accepted' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $p->consent_status ?? 'Pending' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-right text-slate-500">{{ $p->consented_at ? $p->consented_at->format('d M Y, h:i A') : 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400 font-bold">All applicants are up-to-date with the latest compliance requirements.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pt-4">
                    {{ $pendingApplicants->appends(request()->query())->links() }}
                </div>
            </div>
        </div>

        <!-- Tab 3: Privacy Policies -->
        <div x-show="activeTab === 'privacy'" class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-cloak>
            <!-- Add New Policy Draft Form -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4 h-fit" x-data="{ docType: 'file', fileName: '' }">
                <h3 class="text-base font-extrabold text-slate-900">Create Privacy Policy Draft</h3>
                <form action="{{ route('admin.compliance.privacy.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-slate-900 font-extrabold mb-1">Policy Version</label>
                            <input type="text" name="version" required placeholder="e.g. 2.2" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 bg-slate-50 text-slate-900 outline-none focus:ring-2 focus:ring-amber-500 font-medium">
                        </div>
                        <div>
                            <label class="block text-slate-900 font-extrabold mb-1">Language</label>
                            <select name="language" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 bg-slate-50 text-slate-900 outline-none focus:ring-2 focus:ring-amber-500 font-medium">
                                <option value="en">English (en)</option>
                                <option value="sw">Swahili (sw)</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-slate-900 font-extrabold mb-1">Document Title</label>
                        <input type="text" name="title" required placeholder="e.g. Privacy Policy & Compliance Notice" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 bg-slate-50 text-slate-900 outline-none focus:ring-2 focus:ring-amber-500 font-medium">
                    </div>
                    
                    <div class="space-y-3 pt-1">
                        <label class="block text-slate-900 font-extrabold mb-1">Document Content Source</label>
                        <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100/80 rounded-xl">
                            <button type="button" @click="docType = 'file'" :class="docType === 'file' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/50' : 'text-slate-500 hover:text-slate-800'" class="py-2 text-[10px] font-black rounded-lg transition-all flex items-center justify-center gap-1.5 uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                Upload PDF
                            </button>
                            <button type="button" @click="docType = 'text'" :class="docType === 'text' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/50' : 'text-slate-500 hover:text-slate-800'" class="py-2 text-[10px] font-black rounded-lg transition-all flex items-center justify-center gap-1.5 uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Write Text
                            </button>
                        </div>

                        <!-- PDF Upload Container -->
                        <div x-show="docType === 'file'" x-transition class="space-y-2">
                            <div class="relative group border-2 border-dashed border-slate-300 hover:border-amber-500 rounded-2xl p-5 bg-slate-50/50 transition-all flex flex-col items-center justify-center text-center cursor-pointer min-h-[140px]">
                                <input type="file" name="consent_file" accept=".pdf" @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <svg class="w-8 h-8 text-slate-400 group-hover:text-amber-500 transition-colors mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                <span class="text-[11px] font-extrabold text-slate-700">Choose PDF Document or Drag here</span>
                                <span class="text-[9px] text-slate-400 mt-0.5">Maximum size: 10MB</span>
                            </div>
                            @error('consent_file')
                                <span class="text-red-500 text-[10px] block mt-1">{{ $message }}</span>
                            @enderror
                            <div x-show="fileName" class="text-[10px] text-slate-700 font-extrabold bg-blue-50/70 border border-blue-200/50 rounded-xl px-3 py-2 flex items-center justify-between shadow-sm" x-cloak>
                                <span class="truncate max-w-[200px]" x-text="'Selected: ' + fileName"></span>
                                <button type="button" @click="fileName = ''; $el.closest('form').querySelector('input[type=file]').value = ''" class="text-red-500 hover:text-red-700 font-black text-xs px-1">✕</button>
                            </div>
                        </div>

                        <!-- Content Textarea -->
                        <div x-show="docType === 'text'" x-transition class="space-y-2">
                            <textarea name="content" rows="8" placeholder="Type or paste document HTML or text content here..." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 bg-slate-50 text-slate-900 outline-none focus:ring-2 focus:ring-amber-500 font-mono text-[10px] font-medium leading-relaxed"></textarea>
                            @error('content')
                                <span class="text-red-500 text-[10px] block mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="w-full gradient-btn py-2.5 rounded-xl text-white font-extrabold text-xs shadow-sm uppercase tracking-wider transition-all">Save Draft Policy</button>
                </form>
            </div>

            <!-- Policy History & Publishing -->
            <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-base font-extrabold text-slate-900">Privacy Policy Document Repository</h3>
                <div class="divide-y divide-slate-100">
                    @forelse($policies as $p)
                        <div class="py-4 flex justify-between items-start gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-extrabold text-slate-900">{{ $p->title }}</span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase {{ $p->status === 'Published' ? 'bg-emerald-100 text-emerald-800' : ($p->status === 'Archived' ? 'bg-slate-100 text-slate-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $p->status }}
                                    </span>
                                </div>
                                @if($p->content)
                                    <p class="text-[11px] text-slate-500 font-medium leading-relaxed max-w-xl truncate">{{ strip_tags($p->content) }}</p>
                                @else
                                    <p class="text-[11px] text-slate-400 italic font-medium leading-relaxed max-w-xl">No text content provided (Written document attached).</p>
                                @endif
                                @if($p->file_path)
                                    <div class="pt-1 flex items-center gap-1 text-[11px] font-extrabold text-blue-800">
                                        <span>📄 Attached Document:</span>
                                        <a href="{{ asset('storage/' . $p->file_path) }}" target="_blank" class="underline hover:text-blue-950 flex items-center">
                                            View Uploaded File
                                        </a>
                                    </div>
                                @endif
                                <div class="flex space-x-3 text-[10px] text-slate-400 font-medium pt-1">
                                    <span>Version: <strong>{{ $p->version }}</strong></span>
                                    <span>Language: <strong>{{ strtoupper($p->language ?? 'en') }}</strong></span>
                                    <span>Effective: {{ $p->effective_date ?? 'Not Set' }}</span>
                                    <span>By: {{ $p->publisher->name ?? 'System' }}</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.compliance.privacy.preview', $p->id) }}" class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-[10px] transition-all">
                                    Preview
                                </a>
                                @if($p->status === 'Draft')
                                    <a href="{{ route('admin.compliance.privacy.edit', $p->id) }}" class="px-2.5 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-800 font-extrabold text-[10px] transition-all">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.compliance.privacy.publish', $p->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Are you sure you want to publish this policy version? This will automatically archive the current active version.')" class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-[10px] transition-all whitespace-nowrap">
                                            ✓ Publish
                                        </button>
                                    </form>
                                @elseif($p->status === 'Archived')
                                    <form action="{{ route('admin.compliance.privacy.rollback', $p->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Are you sure you want to roll back to this policy version? This will archive the current published policy.')" class="px-2.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-[10px] transition-all whitespace-nowrap">
                                            ↺ Rollback
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.compliance.privacy.destroy', $p->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('{{ $p->status === 'Published' ? 'WARNING: This is the active published policy. Deleting it will leave the system without a published policy and might affect applicant registration. Are you sure you want to delete it?' : 'Are you sure you want to delete this policy version? This action cannot be undone.' }}')" class="px-2.5 py-1.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 font-extrabold text-[10px] transition-all whitespace-nowrap">
                                        🗑 Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <span class="text-xs text-slate-400 block py-4">No policy records registered.</span>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Tab 4: Terms & Conditions -->
        <div x-show="activeTab === 'terms'" class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-cloak>
            <!-- Add New Terms Draft Form -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4 h-fit" x-data="{ docType: 'file', fileName: '' }">
                <h3 class="text-base font-extrabold text-slate-900">Create Terms & Conditions Draft</h3>
                <form action="{{ route('admin.compliance.terms.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-slate-900 font-extrabold mb-1">Terms Version</label>
                            <input type="text" name="version" required placeholder="e.g. 2.2" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 bg-slate-50 text-slate-900 outline-none focus:ring-2 focus:ring-amber-500 font-medium">
                        </div>
                        <div>
                            <label class="block text-slate-900 font-extrabold mb-1">Language</label>
                            <select name="language" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 bg-slate-50 text-slate-900 outline-none focus:ring-2 focus:ring-amber-500 font-medium">
                                <option value="en">English (en)</option>
                                <option value="sw">Swahili (sw)</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-slate-900 font-extrabold mb-1">Document Title</label>
                        <input type="text" name="title" required placeholder="e.g. Terms and Conditions of Admission Application" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 bg-slate-50 text-slate-900 outline-none focus:ring-2 focus:ring-amber-500 font-medium">
                    </div>
                    
                    <div class="space-y-3 pt-1">
                        <label class="block text-slate-900 font-extrabold mb-1">Document Content Source</label>
                        <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100/80 rounded-xl">
                            <button type="button" @click="docType = 'file'" :class="docType === 'file' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/50' : 'text-slate-500 hover:text-slate-800'" class="py-2 text-[10px] font-black rounded-lg transition-all flex items-center justify-center gap-1.5 uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                Upload PDF
                            </button>
                            <button type="button" @click="docType = 'text'" :class="docType === 'text' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/50' : 'text-slate-500 hover:text-slate-800'" class="py-2 text-[10px] font-black rounded-lg transition-all flex items-center justify-center gap-1.5 uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Write Text
                            </button>
                        </div>

                        <!-- PDF Upload Container -->
                        <div x-show="docType === 'file'" x-transition class="space-y-2">
                            <div class="relative group border-2 border-dashed border-slate-300 hover:border-amber-500 rounded-2xl p-5 bg-slate-50/50 transition-all flex flex-col items-center justify-center text-center cursor-pointer min-h-[140px]">
                                <input type="file" name="consent_file" accept=".pdf" @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <svg class="w-8 h-8 text-slate-400 group-hover:text-amber-500 transition-colors mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                <span class="text-[11px] font-extrabold text-slate-700">Choose PDF Document or Drag here</span>
                                <span class="text-[9px] text-slate-400 mt-0.5">Maximum size: 10MB</span>
                            </div>
                            @error('consent_file')
                                <span class="text-red-500 text-[10px] block mt-1">{{ $message }}</span>
                            @enderror
                            <div x-show="fileName" class="text-[10px] text-slate-700 font-extrabold bg-blue-50/70 border border-blue-200/50 rounded-xl px-3 py-2 flex items-center justify-between shadow-sm" x-cloak>
                                <span class="truncate max-w-[200px]" x-text="'Selected: ' + fileName"></span>
                                <button type="button" @click="fileName = ''; $el.closest('form').querySelector('input[type=file]').value = ''" class="text-red-500 hover:text-red-700 font-black text-xs px-1">✕</button>
                            </div>
                        </div>

                        <!-- Content Textarea -->
                        <div x-show="docType === 'text'" x-transition class="space-y-2">
                            <textarea name="content" rows="8" placeholder="Type or paste document HTML or text content here..." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 bg-slate-50 text-slate-900 outline-none focus:ring-2 focus:ring-amber-500 font-mono text-[10px] font-medium leading-relaxed"></textarea>
                            @error('content')
                                <span class="text-red-500 text-[10px] block mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="w-full gradient-btn py-2.5 rounded-xl text-white font-extrabold text-xs shadow-sm uppercase tracking-wider transition-all">Save Draft Terms</button>
                </form>
            </div>

            <!-- Terms History & Publishing -->
            <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-base font-extrabold text-slate-900">Terms & Conditions Document Repository</h3>
                <div class="divide-y divide-slate-100">
                    @forelse($terms as $t)
                        <div class="py-4 flex justify-between items-start gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-extrabold text-slate-900">{{ $t->title }}</span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase {{ $t->status === 'Published' ? 'bg-emerald-100 text-emerald-800' : ($t->status === 'Archived' ? 'bg-slate-100 text-slate-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $t->status }}
                                    </span>
                                </div>
                                @if($t->content)
                                    <p class="text-[11px] text-slate-500 font-medium leading-relaxed max-w-xl truncate">{{ strip_tags($t->content) }}</p>
                                @else
                                    <p class="text-[11px] text-slate-400 italic font-medium leading-relaxed max-w-xl">No text content provided (Written document attached).</p>
                                @endif
                                @if($t->file_path)
                                    <div class="pt-1 flex items-center gap-1 text-[11px] font-extrabold text-blue-800">
                                        <span>📄 Attached Document:</span>
                                        <a href="{{ asset('storage/' . $t->file_path) }}" target="_blank" class="underline hover:text-blue-950 flex items-center">
                                            View Uploaded File
                                        </a>
                                    </div>
                                @endif
                                <div class="flex space-x-3 text-[10px] text-slate-400 font-medium pt-1">
                                    <span>Version: <strong>{{ $t->version }}</strong></span>
                                    <span>Language: <strong>{{ strtoupper($t->language ?? 'en') }}</strong></span>
                                    <span>Effective: {{ $t->effective_date ?? 'Not Set' }}</span>
                                    <span>By: {{ $t->publisher->name ?? 'System' }}</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.compliance.terms.preview', $t->id) }}" class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-[10px] transition-all">
                                    Preview
                                </a>
                                @if($t->status === 'Draft')
                                    <a href="{{ route('admin.compliance.terms.edit', $t->id) }}" class="px-2.5 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-800 font-extrabold text-[10px] transition-all">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.compliance.terms.publish', $t->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Are you sure you want to publish this terms version? This will automatically archive the current active version.')" class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-[10px] transition-all whitespace-nowrap">
                                            ✓ Publish
                                        </button>
                                    </form>
                                @elseif($t->status === 'Archived')
                                    <form action="{{ route('admin.compliance.terms.rollback', $t->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Are you sure you want to roll back to this terms version? This will archive the current published terms.')" class="px-2.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-[10px] transition-all whitespace-nowrap">
                                            ↺ Rollback
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.compliance.terms.destroy', $t->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('{{ $t->status === 'Published' ? 'WARNING: This is the active published terms. Deleting it will leave the system without a published terms and might affect applicant registration. Are you sure you want to delete it?' : 'Are you sure you want to delete this terms version? This action cannot be undone.' }}')" class="px-2.5 py-1.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 font-extrabold text-[10px] transition-all whitespace-nowrap">
                                        🗑 Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <span class="text-xs text-slate-400 block py-4">No terms records registered.</span>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Tab 5: Stats -->
        <div x-show="activeTab === 'stats'" class="grid grid-cols-1 lg:grid-cols-2 gap-8" x-cloak>
            <!-- Consent By Version Chart -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                <h3 class="font-extrabold text-slate-900 text-base">Consent Records by Policy Version</h3>
                <div class="overflow-hidden">
                    <table class="w-full text-left text-xs font-semibold text-slate-700">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-500 uppercase text-[10px] tracking-wider">
                                <th class="py-2.5">Version</th>
                                <th class="py-2.5 text-right">Acceptance Count</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($stats['by_policy_version'] as $s)
                                <tr>
                                    <td class="py-3 font-bold text-slate-900">Version {{ $s->consent_version }}</td>
                                    <td class="py-3 text-right font-black text-blue-600">{{ $s->count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-4 text-center text-slate-400">No statistics recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Consent By Academic Cycle -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                <h3 class="font-extrabold text-slate-900 text-base">Consent Records by Admission Cycle</h3>
                <div class="overflow-hidden">
                    <table class="w-full text-left text-xs font-semibold text-slate-700">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-500 uppercase text-[10px] tracking-wider">
                                <th class="py-2.5">Admission Cycle</th>
                                <th class="py-2.5 text-right">Acceptance Count</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($stats['by_cycle'] as $s)
                                <tr>
                                    <td class="py-3 font-bold text-slate-900">{{ $s->cycle }}</td>
                                    <td class="py-3 text-right font-black text-emerald-600">{{ $s->count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-4 text-center text-slate-400">No statistics recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
