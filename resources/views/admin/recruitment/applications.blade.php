<x-app-layout title="Job Applications Management & Screening">
    <x-slot name="header">Recruitment Applications Manager</x-slot>

    <div class="w-full space-y-6" x-data="{ 
        selectedApps: [],
        selectAll: false,
        bulkStage: 'Shortlisted',
        bulkComments: '',
        bulkModalOpen: false,
        quickDrawerOpen: false,
        activeApp: null,
        statusUpdating: null,

        toggleSelectAll() {
            this.selectAll = !this.selectAll;
            if (this.selectAll) {
                this.selectedApps = {!! json_encode($applications->pluck('id')->toArray()) !!};
            } else {
                this.selectedApps = [];
            }
        },

        openQuickView(appData) {
            this.activeApp = appData;
            this.quickDrawerOpen = true;
        },

        async updateSingleStage(appId, newStage) {
            this.statusUpdating = appId;
            try {
                const response = await axios.post('{{ url('/admin/recruitment/applications') }}/' + appId + '/stage', {
                    stage: newStage,
                    comments: 'Stage updated via quick selector from Applications Manager.'
                });
                
                if (typeof toast === 'function') {
                    toast('Stage updated to ' + newStage + ' successfully!', 'success');
                }
                
                setTimeout(() => window.location.reload(), 700);
            } catch (err) {
                if (typeof toast === 'function') {
                    toast(err.response?.data?.message || 'Failed to update application stage.', 'error');
                } else {
                    alert('Failed to update application stage.');
                }
            } finally {
                this.statusUpdating = null;
            }
        },

        async executeBulkAction() {
            if (this.selectedApps.length === 0) {
                if (typeof toast === 'function') toast('Please select at least one applicant.', 'error');
                return;
            }

            try {
                const response = await axios.post('{{ route('admin.recruitment.applications.bulk') }}', {
                    application_ids: this.selectedApps,
                    action: this.bulkStage,
                    comments: this.bulkComments || ('Bulk stage transition to ' + this.bulkStage)
                });

                if (typeof toast === 'function') {
                    toast(response.data.message || 'Bulk update completed successfully!', 'success');
                }
                this.bulkModalOpen = false;
                setTimeout(() => window.location.reload(), 800);
            } catch (err) {
                if (typeof toast === 'function') {
                    toast(err.response?.data?.message || 'Bulk stage update failed.', 'error');
                } else {
                    alert('Bulk stage update failed.');
                }
            }
        }
    }">

        <!-- Top Header & Action Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
            <div class="space-y-1">
                <div class="flex items-center space-x-2">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-800">
                        ATS Screening Hub
                    </span>
                    <span class="text-xs font-bold text-slate-500">Cycle 2026/2027</span>
                </div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">Candidate Applications & Screening Manager</h1>
                <p class="text-xs text-slate-500 font-medium">Review submitted applicant dossiers, transition recruitment stages, schedule assessments, and perform bulk operations.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('admin.recruitment.applications.export', request()->query()) }}" 
                   class="inline-flex items-center px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-extrabold text-xs transition-all shadow-sm">
                    <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export to CSV
                </a>
                <a href="{{ route('admin.recruitment.ats') }}" 
                   class="inline-flex items-center px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-extrabold text-xs transition-all shadow-sm">
                    <svg class="w-4 h-4 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                    Kanban Pipeline
                </a>
                <a href="{{ route('admin.recruitment.vacancies') }}" 
                   class="gradient-btn inline-flex items-center px-4 py-2.5 rounded-xl text-white font-extrabold text-xs shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Manage Vacancies
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 rounded-2xl text-xs font-bold flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-black">&times;</button>
            </div>
        @endif

        <!-- Quick Summary KPI Metric Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <a href="{{ route('admin.recruitment.applications.index') }}" 
               class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm space-y-1 hover:border-blue-300 transition-all block">
                <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest block">Total Applications</span>
                <span class="text-2xl font-black text-slate-900 block">{{ $metrics['total'] ?? 0 }}</span>
                <span class="text-[10px] text-blue-600 font-extrabold">All Submissions</span>
            </a>

            <a href="{{ route('admin.recruitment.applications.index', ['status' => 'Under Review']) }}" 
               class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm space-y-1 hover:border-amber-300 transition-all block">
                <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest block">Pending Review</span>
                <span class="text-2xl font-black text-amber-500 block">{{ $metrics['pending'] ?? 0 }}</span>
                <span class="text-[10px] text-amber-600 font-extrabold">Requires Screening</span>
            </a>

            <a href="{{ route('admin.recruitment.applications.index', ['status' => 'Shortlisted']) }}" 
               class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm space-y-1 hover:border-indigo-300 transition-all block">
                <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest block">Shortlisted</span>
                <span class="text-2xl font-black text-indigo-600 block">{{ $metrics['shortlisted'] ?? 0 }}</span>
                <span class="text-[10px] text-indigo-600 font-extrabold">Qualified Candidates</span>
            </a>

            <a href="{{ route('admin.recruitment.applications.index', ['status' => 'Interview Scheduled']) }}" 
               class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm space-y-1 hover:border-purple-300 transition-all block">
                <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest block">In Assessment</span>
                <span class="text-2xl font-black text-purple-600 block">{{ $metrics['assessment'] ?? 0 }}</span>
                <span class="text-[10px] text-purple-600 font-extrabold">Tests & Interviews</span>
            </a>

            <a href="{{ route('admin.recruitment.applications.index', ['status' => 'Hired']) }}" 
               class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm space-y-1 hover:border-emerald-300 transition-all block col-span-2 sm:col-span-1">
                <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest block">Selected & Hired</span>
                <span class="text-2xl font-black text-emerald-600 block">{{ $metrics['hired'] ?? 0 }}</span>
                <span class="text-[10px] text-emerald-600 font-extrabold">Offers & Placement</span>
            </a>
        </div>

        <!-- Stage Filter Navigation Tabs -->
        <div class="bg-white rounded-2xl border border-slate-200 p-2 shadow-sm overflow-x-auto">
            <div class="flex items-center space-x-1 min-w-max">
                @php
                    $currentStatus = $filters['status'] ?? '';
                    $allStages = [
                        '' => ['label' => 'All Applications', 'count' => $statusCounts['All'] ?? 0],
                        'Applied' => ['label' => 'Applied', 'count' => $statusCounts['Applied'] ?? 0],
                        'Under Review' => ['label' => 'Under Review', 'count' => $statusCounts['Under Review'] ?? 0],
                        'Shortlisted' => ['label' => 'Shortlisted', 'count' => $statusCounts['Shortlisted'] ?? 0],
                        'Written Test' => ['label' => 'Written Test', 'count' => $statusCounts['Written Test'] ?? 0],
                        'Interview Scheduled' => ['label' => 'Interview Scheduled', 'count' => $statusCounts['Interview Scheduled'] ?? 0],
                        'Final Interview' => ['label' => 'Final Interview', 'count' => $statusCounts['Final Interview'] ?? 0],
                        'Selected' => ['label' => 'Selected', 'count' => $statusCounts['Selected'] ?? 0],
                        'Offer Letter' => ['label' => 'Offer Letter', 'count' => $statusCounts['Offer Letter'] ?? 0],
                        'Hired' => ['label' => 'Hired', 'count' => $statusCounts['Hired'] ?? 0],
                        'Rejected' => ['label' => 'Rejected', 'count' => $statusCounts['Rejected'] ?? 0],
                    ];
                @endphp

                @foreach($allStages as $stageKey => $stageInfo)
                    @php
                        $isActive = ($currentStatus === $stageKey);
                        $query = request()->except(['status', 'page']);
                        if ($stageKey !== '') {
                            $query['status'] = $stageKey;
                        }
                    @endphp
                    <a href="{{ route('admin.recruitment.applications.index', $query) }}" 
                       class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition-all flex items-center space-x-2 {{ $isActive ? 'bg-blue-800 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <span>{{ $stageInfo['label'] }}</span>
                        <span class="text-[10px] px-1.5 py-0.2 rounded-full font-black {{ $isActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                            {{ $stageInfo['count'] }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Filter & Search Query Panel -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 text-xs font-semibold">
            <form action="{{ route('admin.recruitment.applications.index') }}" method="GET" class="space-y-4">
                @if(!empty($filters['status']))
                    <input type="hidden" name="status" value="{{ $filters['status'] }}">
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-slate-500 font-bold">Search Keyword</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Name, Email, Phone, App #, NIDA..." class="w-full pl-9 pr-3 py-2.5 bg-slate-50 rounded-xl border border-slate-200 text-slate-950 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-slate-500 font-bold">Job Vacancy</label>
                        <select name="vacancy_id" class="w-full p-2.5 bg-slate-50 rounded-xl border border-slate-200 text-slate-950 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">All Vacancies</option>
                            @foreach($vacancies as $vac)
                                <option value="{{ $vac->id }}" {{ ($filters['vacancy_id'] ?? '') == $vac->id ? 'selected' : '' }}>{{ $vac->job_title }} ({{ $vac->vacancy_number }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-slate-500 font-bold">Designation</label>
                        <select name="designation_id" class="w-full p-2.5 bg-slate-50 rounded-xl border border-slate-200 text-slate-950 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">All Designations</option>
                            @foreach($designations as $desig)
                                <option value="{{ $desig->id }}" {{ ($filters['designation_id'] ?? '') == $desig->id ? 'selected' : '' }}>{{ $desig->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-slate-500 font-bold">Position</label>
                        <select name="position_id" class="w-full p-2.5 bg-slate-50 rounded-xl border border-slate-200 text-slate-950 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">All Positions</option>
                            @foreach($positions as $pos)
                                <option value="{{ $pos->id }}" {{ ($filters['position_id'] ?? '') == $pos->id ? 'selected' : '' }}>{{ $pos->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Secondary Filters Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 pt-3 border-t border-slate-100">
                    <div class="space-y-1.5">
                        <label class="block text-slate-500 font-bold">Gender</label>
                        <select name="gender" class="w-full p-2.5 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                            <option value="">Any Gender</option>
                            <option value="male" {{ ($filters['gender'] ?? '') === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ ($filters['gender'] ?? '') === 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-slate-500 font-bold">Region</label>
                        <input type="text" name="region" value="{{ $filters['region'] ?? '' }}" placeholder="e.g. Singida, Dar..." class="w-full p-2.5 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-slate-500 font-bold">District</label>
                        <input type="text" name="district" value="{{ $filters['district'] ?? '' }}" placeholder="e.g. Iramba, Ilala..." class="w-full p-2.5 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-slate-500 font-bold">Results Per Page</label>
                        <select name="per_page" class="w-full p-2.5 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 Applicants</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 Applicants</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Applicants</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 Applicants</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="gradient-btn flex-1 py-2.5 rounded-xl text-white font-extrabold text-xs shadow-md">
                            Filter
                        </button>
                        <a href="{{ route('admin.recruitment.applications.index') }}" class="px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-center font-bold">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Multi-Select Bulk Actions Floating / Top Bar -->
        <div x-show="selectedApps.length > 0" x-transition class="bg-blue-900 text-white rounded-2xl p-4 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-4" x-cloak>
            <div class="flex items-center space-x-3">
                <span class="w-7 h-7 rounded-full bg-amber-500 text-slate-900 font-black flex items-center justify-center text-xs" x-text="selectedApps.length"></span>
                <div>
                    <span class="font-extrabold text-sm block">Candidates Selected</span>
                    <span class="text-xs text-blue-200">Choose a bulk operation to execute on all selected applicants.</span>
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <select x-model="bulkStage" class="p-2 bg-blue-800 text-white rounded-xl border border-blue-700 text-xs font-bold focus:outline-none">
                    <option value="Under Review">Move to Under Review</option>
                    <option value="Shortlisted">Mark as Shortlisted</option>
                    <option value="Written Test">Assign Written Test</option>
                    <option value="Interview Scheduled">Schedule Interview</option>
                    <option value="Selected">Mark as Selected</option>
                    <option value="Offer Letter">Advance to Offer Letter</option>
                    <option value="Rejected">Mark as Rejected</option>
                </select>

                <button type="button" @click="bulkModalOpen = true" class="px-4 py-2 bg-amber-500 text-slate-950 font-black text-xs rounded-xl shadow-sm hover:bg-amber-400 transition-all">
                    Process Bulk Action &rarr;
                </button>

                <button type="button" @click="selectedApps = []; selectAll = false" class="px-3 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-bold">
                    Cancel
                </button>
            </div>
        </div>

        <!-- Applications Data Table Card -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 overflow-hidden space-y-4">
            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-2 text-xs font-bold border-b border-slate-100 pb-3">
                <div class="text-slate-500">
                    Showing <span class="text-slate-900 font-black">{{ $applications->firstItem() ?? 0 }}</span> to <span class="text-slate-900 font-black">{{ $applications->lastItem() ?? 0 }}</span> of <span class="text-slate-900 font-black">{{ $applications->total() }}</span> submitted applications
                </div>
                <div class="text-slate-500 flex items-center space-x-3">
                    <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5"></span> Selected/Hired</span>
                    <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-amber-500 mr-1.5"></span> In Screening/Test</span>
                    <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-red-500 mr-1.5"></span> Rejected</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold tracking-wider bg-slate-50/50">
                            <th class="py-3.5 px-4 w-10">
                                <input type="checkbox" @click="toggleSelectAll()" :checked="selectAll" class="rounded border-slate-300 text-blue-800 focus:ring-blue-500">
                            </th>
                            <th class="py-3.5 px-4">Applicant & Contact</th>
                            <th class="py-3.5 px-4">Applied Vacancy</th>
                            <th class="py-3.5 px-4">Academic & Experience</th>
                            <th class="py-3.5 px-4">Location</th>
                            <th class="py-3.5 px-4">Submitted At</th>
                            <th class="py-3.5 px-4">Recruitment Stage</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                        @forelse($applications as $app)
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="py-4 px-4 align-top">
                                    <input type="checkbox" :value="{{ $app->id }}" x-model="selectedApps" class="rounded border-slate-300 text-blue-800 focus:ring-blue-500">
                                </td>

                                <!-- Applicant & Contact -->
                                <td class="py-4 px-4 align-top">
                                    <div class="flex items-start space-x-3">
                                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-500 to-blue-900 text-white font-black flex items-center justify-center text-sm shadow-sm overflow-hidden shrink-0">
                                            @if(isset($app->attachments['passport_photo']))
                                                <img src="{{ asset('storage/' . $app->attachments['passport_photo']) }}" alt="Photo" class="w-full h-full object-cover">
                                            @else
                                                {{ strtoupper(substr($app->full_name, 0, 1)) }}
                                            @endif
                                        </div>
                                        <div class="space-y-0.5">
                                            <a href="{{ route('admin.recruitment.applications.show', $app->id) }}" class="font-extrabold text-slate-900 hover:text-blue-700 block text-xs">
                                                {{ $app->full_name }}
                                            </a>
                                            <div class="text-[10px] text-blue-600 font-mono font-black">{{ $app->application_number }}</div>
                                            <div class="text-[11px] text-slate-500 space-x-2">
                                                <a href="mailto:{{ $app->email }}" class="hover:underline text-slate-600" title="Send Email">📧 {{ $app->email }}</a>
                                            </div>
                                            <div class="text-[11px] text-slate-500 space-x-2 flex items-center">
                                                <a href="tel:{{ $app->phone }}" class="hover:underline text-slate-600">📞 {{ $app->phone }}</a>
                                                @if(!empty($app->whatsapp_number))
                                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $app->whatsapp_number) }}" target="_blank" class="text-emerald-600 font-bold hover:underline" title="WhatsApp Chat">
                                                        💬 WA
                                                    </a>
                                                @endif
                                            </div>
                                            @if(!empty($app->nida_number))
                                                <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-600">
                                                    NIDA: {{ $app->nida_number }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Applied Vacancy -->
                                <td class="py-4 px-4 align-top">
                                    <div class="space-y-1">
                                        <div class="font-extrabold text-slate-900 text-xs">{{ $app->vacancy->job_title ?? 'General Application' }}</div>
                                        <div class="text-[10px] text-slate-500 font-mono">{{ $app->vacancy->vacancy_number ?? 'VAC-OPEN' }}</div>
                                        @if(isset($app->vacancy->designation))
                                            <span class="inline-block px-2 py-0.5 rounded text-[9px] font-extrabold bg-blue-50 text-blue-700">
                                                {{ $app->vacancy->designation->name }}
                                            </span>
                                        @endif
                                        @if(isset($app->vacancy->position))
                                            <div class="text-[10px] text-slate-500">{{ $app->vacancy->position->name }}</div>
                                        @endif
                                    </div>
                                </td>

                                <!-- Academic & Experience -->
                                <td class="py-4 px-4 align-top">
                                    <div class="space-y-1">
                                        @php
                                            $eduList = is_array($app->education_history) ? $app->education_history : [];
                                            $highestEdu = !empty($eduList) ? end($eduList) : null;
                                        @endphp
                                        @if($highestEdu)
                                            <div class="font-bold text-slate-800 text-[11px] truncate max-w-[180px]" title="{{ $highestEdu['degree_title'] ?? $highestEdu['qualification'] ?? '' }}">
                                                🎓 {{ $highestEdu['degree_title'] ?? $highestEdu['qualification'] ?? 'Education Listed' }}
                                            </div>
                                            <div class="text-[10px] text-slate-500 truncate max-w-[180px]">{{ $highestEdu['institution'] ?? '' }}</div>
                                        @else
                                            <span class="text-slate-400 text-[11px]">No formal record</span>
                                        @endif

                                        @if($app->worked_at_sttc)
                                            <span class="inline-block px-2 py-0.5 rounded text-[9px] font-black bg-amber-100 text-amber-900">
                                                ⭐ STTC Experience
                                            </span>
                                        @endif

                                        @if(!empty($app->attachments['cv']))
                                            <div class="pt-0.5">
                                                <a href="{{ asset('storage/' . $app->attachments['cv']) }}" target="_blank" class="inline-flex items-center text-[10px] text-blue-700 font-extrabold hover:underline">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    View CV Document
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- Location -->
                                <td class="py-4 px-4 align-top">
                                    <div class="space-y-0.5">
                                        <div class="text-slate-800 font-bold">{{ $app->region ?? 'Tanzania' }}</div>
                                        <div class="text-[10px] text-slate-500">{{ $app->district ?? 'N/A' }}</div>
                                        <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.2 rounded bg-slate-100 text-slate-600">
                                            {{ $app->gender ?? 'N/A' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Submitted Date -->
                                <td class="py-4 px-4 align-top">
                                    <div class="space-y-0.5">
                                        <div class="text-slate-800 font-bold">{{ $app->created_at->format('d M Y') }}</div>
                                        <div class="text-[10px] text-slate-500">{{ $app->created_at->diffForHumans() }}</div>
                                    </div>
                                </td>

                                <!-- Stage Status & Quick Switcher -->
                                <td class="py-4 px-4 align-top">
                                    <div class="space-y-1.5" x-data="{ localStage: '{{ $app->status }}' }">
                                        @php
                                            $badgeClass = match($app->status) {
                                                'Selected', 'Hired' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
                                                'Rejected' => 'bg-red-100 text-red-800 border border-red-200',
                                                'Shortlisted' => 'bg-indigo-100 text-indigo-800 border border-indigo-200',
                                                'Written Test', 'Interview Scheduled', 'Final Interview' => 'bg-purple-100 text-purple-800 border border-purple-200',
                                                'Offer Letter' => 'bg-blue-100 text-blue-800 border border-blue-200',
                                                default => 'bg-amber-100 text-amber-800 border border-amber-200'
                                            };
                                        @endphp

                                        <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase {{ $badgeClass }}">
                                            {{ $app->status }}
                                        </span>

                                        <!-- Instant Inline Stage Selector -->
                                        <div>
                                            <select @change="updateSingleStage({{ $app->id }}, $event.target.value)" 
                                                    :disabled="statusUpdating === {{ $app->id }}"
                                                    class="text-[10px] font-bold p-1 bg-slate-50 rounded-lg border border-slate-200 text-slate-700 focus:ring-1 focus:ring-blue-500 focus:outline-none w-full">
                                                <option value="" disabled>Change Stage...</option>
                                                @foreach(['Applied', 'Under Review', 'Shortlisted', 'Written Test', 'Interview Scheduled', 'Final Interview', 'Selected', 'Offer Letter', 'Hired', 'Rejected'] as $st)
                                                    <option value="{{ $st }}" {{ $app->status === $st ? 'selected' : '' }}>&rarr; {{ $st }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </td>

                                <!-- Action Buttons -->
                                <td class="py-4 px-4 align-top text-right space-y-1.5">
                                    <a href="{{ route('admin.recruitment.applications.show', $app->id) }}" 
                                       class="gradient-btn px-3.5 py-1.5 rounded-xl text-white font-extrabold text-[10px] shadow-sm inline-flex items-center">
                                        Dossier &rarr;
                                    </a>

                                    <div>
                                        <button type="button" 
                                                @click="openQuickView({
                                                    id: {{ $app->id }},
                                                    application_number: '{{ $app->application_number }}',
                                                    full_name: '{{ addslashes($app->full_name) }}',
                                                    email: '{{ $app->email }}',
                                                    phone: '{{ $app->phone }}',
                                                    whatsapp_number: '{{ $app->whatsapp_number ?? '' }}',
                                                    gender: '{{ $app->gender ?? '' }}',
                                                    nida_number: '{{ $app->nida_number ?? '' }}',
                                                    tin_number: '{{ $app->tin_number ?? '' }}',
                                                    nssf_number: '{{ $app->nssf_number ?? '' }}',
                                                    region: '{{ $app->region ?? '' }}',
                                                    district: '{{ $app->district ?? '' }}',
                                                    physical_address: '{{ addslashes($app->physical_address ?? '') }}',
                                                    status: '{{ $app->status }}',
                                                    vacancy_title: '{{ addslashes($app->vacancy->job_title ?? 'N/A') }}',
                                                    vacancy_number: '{{ $app->vacancy->vacancy_number ?? '' }}',
                                                    worked_at_sttc: {{ $app->worked_at_sttc ? 'true' : 'false' }},
                                                    education_history: {!! json_encode($app->education_history ?? []) !!},
                                                    experience_history: {!! json_encode($app->experience_history ?? []) !!},
                                                    referees: {!! json_encode($app->referees ?? []) !!},
                                                    motivation_letter: {!! json_encode($app->motivation_letter ?? '') !!},
                                                    attachments: {!! json_encode($app->attachments ?? []) !!},
                                                    created_at: '{{ $app->created_at->format('d M Y H:i') }}'
                                                })"
                                                class="px-2.5 py-1 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-bold text-[10px] inline-flex items-center">
                                            Quick View
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-500">
                                    <div class="max-w-xs mx-auto space-y-2">
                                        <svg class="w-10 h-10 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <p class="font-extrabold text-slate-800 text-sm">No applications found.</p>
                                        <p class="text-xs text-slate-500">Try adjusting your filters or search keywords.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-slate-500 font-semibold">
                    Page {{ $applications->currentPage() }} of {{ $applications->lastPage() }}
                </div>
                <div>
                    {{ $applications->links() }}
                </div>
            </div>
        </div>

        <!-- Slide-over Quick Drawer for Candidate Profile -->
        <div x-show="quickDrawerOpen" 
             class="fixed inset-0 z-50 overflow-hidden" 
             x-cloak>
            <div class="absolute inset-0 bg-slate-950/50 backdrop-blur-xs transition-opacity" @click="quickDrawerOpen = false"></div>

            <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
                <div class="w-screen max-w-2xl bg-white shadow-2xl flex flex-col overflow-y-auto" @click.stop>
                    
                    <!-- Drawer Header -->
                    <div class="p-6 bg-gradient-to-r from-blue-900 to-slate-900 text-white flex items-center justify-between sticky top-0 z-10 shadow-md">
                        <div class="space-y-1">
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase bg-amber-500 text-slate-950" x-text="activeApp?.status"></span>
                                <span class="text-xs text-blue-200 font-mono font-bold" x-text="activeApp?.application_number"></span>
                            </div>
                            <h2 class="text-lg font-black tracking-tight" x-text="activeApp?.full_name"></h2>
                            <p class="text-xs text-blue-200" x-text="'Applied for: ' + (activeApp?.vacancy_title || 'N/A')"></p>
                        </div>
                        <button type="button" @click="quickDrawerOpen = false" class="p-2 rounded-xl bg-white/10 hover:bg-white/20 text-white transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Drawer Body -->
                    <div class="p-6 space-y-6 flex-1 text-xs" x-show="activeApp">
                        
                        <!-- Quick Contact Bar -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-200 font-medium text-slate-700">
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold block uppercase">Email Address</span>
                                <span class="font-bold text-slate-900 break-all" x-text="activeApp?.email"></span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold block uppercase">Phone</span>
                                <span class="font-bold text-slate-900" x-text="activeApp?.phone"></span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold block uppercase">WhatsApp</span>
                                <span class="font-bold text-slate-900" x-text="activeApp?.whatsapp_number || 'N/A'"></span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold block uppercase">NIDA ID</span>
                                <span class="font-bold text-slate-900" x-text="activeApp?.nida_number || 'N/A'"></span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold block uppercase">Gender / Region</span>
                                <span class="font-bold text-slate-900" x-text="(activeApp?.gender || 'N/A') + ' • ' + (activeApp?.region || 'N/A')"></span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold block uppercase">Applied On</span>
                                <span class="font-bold text-slate-900" x-text="activeApp?.created_at"></span>
                            </div>
                        </div>

                        <!-- Quick Stage Change & Direct Actions -->
                        <div class="p-4 bg-blue-50 border border-blue-200 rounded-2xl space-y-3">
                            <div class="font-extrabold text-blue-900 flex items-center justify-between">
                                <span>Recruitment Stage Action</span>
                                <a :href="'{{ url('/admin/recruitment/applications') }}/' + activeApp?.id" class="text-blue-700 underline font-black text-[11px]">
                                    Open Full Dossier &rarr;
                                </a>
                            </div>
                            <div class="flex items-center space-x-2">
                                <select id="drawer-stage-select" class="p-2 bg-white rounded-xl border border-blue-300 text-xs font-bold text-slate-800 flex-1">
                                    @foreach(['Applied', 'Under Review', 'Shortlisted', 'Written Test', 'Interview Scheduled', 'Final Interview', 'Selected', 'Offer Letter', 'Hired', 'Rejected'] as $st)
                                        <option value="{{ $st }}">{{ $st }}</option>
                                    @endforeach
                                </select>
                                <button type="button" 
                                        @click="updateSingleStage(activeApp.id, document.getElementById('drawer-stage-select').value)"
                                        class="px-4 py-2 bg-blue-800 hover:bg-blue-900 text-white font-extrabold rounded-xl shadow-sm text-xs">
                                    Update Stage
                                </button>
                            </div>
                        </div>

                        <!-- Attachments & Documents -->
                        <div class="space-y-3">
                            <h3 class="font-extrabold text-slate-900 text-sm border-b pb-2">Uploaded Documents & Files</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" x-show="activeApp?.attachments && Object.keys(activeApp.attachments).length > 0">
                                <template x-for="(filePath, key) in (activeApp?.attachments || {})" :key="key">
                                    <a :href="'{{ asset('storage') }}/' + filePath" target="_blank" 
                                       class="flex items-center p-3 rounded-xl border border-slate-200 hover:bg-slate-50 transition-all font-bold text-slate-800">
                                        <svg class="w-4 h-4 mr-2.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        <div class="overflow-hidden">
                                            <span class="block truncate uppercase text-[10px]" x-text="key.replace(/_/g, ' ')"></span>
                                            <span class="text-[9px] text-blue-600 font-extrabold">Download / Preview &rarr;</span>
                                        </div>
                                    </a>
                                </template>
                            </div>
                            <div x-show="!activeApp?.attachments || Object.keys(activeApp.attachments).length === 0" class="text-slate-400 italic">
                                No uploaded documents found for this candidate.
                            </div>
                        </div>

                        <!-- Education History -->
                        <div class="space-y-3">
                            <h3 class="font-extrabold text-slate-900 text-sm border-b pb-2">Academic Qualifications</h3>
                            <div class="space-y-2" x-show="activeApp?.education_history && activeApp.education_history.length > 0">
                                <template x-for="(edu, idx) in (activeApp?.education_history || [])" :key="idx">
                                    <div class="p-3 rounded-xl border border-slate-200 bg-slate-50 space-y-1">
                                        <div class="font-extrabold text-slate-900" x-text="edu.degree_title || edu.qualification || 'Qualification'"></div>
                                        <div class="text-slate-600" x-text="edu.institution || ''"></div>
                                        <div class="text-[10px] text-slate-400" x-text="(edu.start_year || '') + ' - ' + (edu.graduation_year || edu.end_year || 'Completed')"></div>
                                    </div>
                                </template>
                            </div>
                            <div x-show="!activeApp?.education_history || activeApp.education_history.length === 0" class="text-slate-400 italic">
                                No education history provided.
                            </div>
                        </div>

                        <!-- Work Experience -->
                        <div class="space-y-3">
                            <h3 class="font-extrabold text-slate-900 text-sm border-b pb-2">Work Experience</h3>
                            <div class="space-y-2" x-show="activeApp?.experience_history && activeApp.experience_history.length > 0">
                                <template x-for="(exp, idx) in (activeApp?.experience_history || [])" :key="idx">
                                    <div class="p-3 rounded-xl border border-slate-200 bg-slate-50 space-y-1">
                                        <div class="font-extrabold text-slate-900" x-text="exp.position_title || exp.job_title || 'Position'"></div>
                                        <div class="text-slate-600 font-bold" x-text="exp.employer_name || exp.company || ''"></div>
                                        <div class="text-[10px] text-slate-400" x-text="(exp.start_date || '') + ' to ' + (exp.end_date || 'Present')"></div>
                                        <p class="text-slate-600 mt-1" x-text="exp.responsibilities || exp.description || ''"></p>
                                    </div>
                                </template>
                            </div>
                            <div x-show="!activeApp?.experience_history || activeApp.experience_history.length === 0" class="text-slate-400 italic">
                                No work experience listed.
                            </div>
                        </div>

                        <!-- Referees -->
                        <div class="space-y-3">
                            <h3 class="font-extrabold text-slate-900 text-sm border-b pb-2">Professional Referees</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" x-show="activeApp?.referees && activeApp.referees.length > 0">
                                <template x-for="(ref, idx) in (activeApp?.referees || [])" :key="idx">
                                    <div class="p-3 rounded-xl border border-slate-200 bg-slate-50 space-y-1">
                                        <div class="font-extrabold text-slate-900" x-text="ref.name || 'Referee'"></div>
                                        <div class="text-slate-600" x-text="(ref.title || '') + ' • ' + (ref.organization || '')"></div>
                                        <div class="text-blue-700 font-bold" x-text="'📞 ' + (ref.phone || 'N/A')"></div>
                                        <div class="text-slate-500" x-text="'📧 ' + (ref.email || 'N/A')"></div>
                                    </div>
                                </template>
                            </div>
                            <div x-show="!activeApp?.referees || activeApp.referees.length === 0" class="text-slate-400 italic">
                                No professional referees submitted.
                            </div>
                        </div>

                        <!-- Motivation Letter -->
                        <div class="space-y-2" x-show="activeApp?.motivation_letter">
                            <h3 class="font-extrabold text-slate-900 text-sm border-b pb-2">Cover / Motivation Letter</h3>
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-slate-700 whitespace-pre-line leading-relaxed text-xs" x-text="activeApp?.motivation_letter"></div>
                        </div>

                    </div>

                    <!-- Drawer Footer -->
                    <div class="p-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between sticky bottom-0">
                        <button type="button" @click="quickDrawerOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 font-extrabold text-xs text-slate-700 hover:bg-slate-100">
                            Close Preview
                        </button>
                        <a :href="'{{ url('/admin/recruitment/applications') }}/' + activeApp?.id" class="gradient-btn px-4 py-2 rounded-xl text-white font-black text-xs shadow-md">
                            Open Detailed Dossier &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Action Confirmation Modal -->
        <div x-show="bulkModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs" x-cloak>
            <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 space-y-5" @click.stop>
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="font-black text-slate-900 text-base">Confirm Bulk Candidate Processing</h3>
                    <button type="button" @click="bulkModalOpen = false" class="text-slate-400 hover:text-slate-600 font-black text-lg">&times;</button>
                </div>

                <div class="space-y-3 text-xs">
                    <p class="text-slate-600">
                        You have selected <span class="font-black text-blue-600" x-text="selectedApps.length"></span> applicants. They will all be transitioned to:
                    </p>
                    <div class="p-3 bg-amber-500/10 border border-amber-500/20 text-amber-900 font-extrabold rounded-xl text-sm" x-text="bulkStage"></div>

                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Internal Audit Notes & Comments (Optional)</label>
                        <textarea x-model="bulkComments" rows="3" placeholder="Add any comments or reasoning for this bulk transition..." class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-900 focus:outline-none"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button type="button" @click="bulkModalOpen = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs">
                        Cancel
                    </button>
                    <button type="button" @click="executeBulkAction()" class="gradient-btn px-5 py-2.5 rounded-xl text-white font-extrabold text-xs shadow-md">
                        Confirm & Execute
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
