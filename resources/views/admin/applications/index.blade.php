<x-app-layout title="Applicants Directory - Superadmin">
    <x-slot name="header">Applicants Directory & Verification Hub</x-slot>

    <div class="w-full space-y-8" x-data="{
        selectedApps: [],
        selectAll: false,
        bulkLoading: false,
        showAddStudentModal: false,
        addStudentLoading: false,
        newStudent: {
            name: '',
            email: '',
            phone: '',
            programme_id: '',
            academic_year_id: '',
            intake_id: '',
            admission_type: 'Form Six',
            admission_category: 'Direct Entry',
            status: 'Approved',
            gender: 'male',
            date_of_birth: ''
        },

        toggleSelectAll() {
            this.selectAll = !this.selectAll;
            if (this.selectAll) {
                this.selectedApps = [{{ implode(',', $applications->pluck('id')->toArray()) }}];
            } else {
                this.selectedApps = [];
            }
        },

        bulkApprove() {
            if (this.selectedApps.length === 0) {
                toast('Please select at least one application to approve.', 'error');
                return;
            }
            if (!confirm('Are you sure you want to bulk approve ' + this.selectedApps.length + ' applications?')) {
                return;
            }

            this.bulkLoading = true;
            axios.post('{{ url('/api/v1/admin/applications/bulk-approve') }}', {
                application_ids: this.selectedApps
            })
            .then(res => {
                this.bulkLoading = false;
                toast(res.data.message || 'Applications approved successfully!', 'success');
                setTimeout(() => window.location.reload(), 1200);
            })
            .catch(err => {
                this.bulkLoading = false;
                toast(err.response?.data?.message || 'Error processing bulk approval', 'error');
            });
        },

        addStudent() {
            if (!this.newStudent.name || !this.newStudent.email || !this.newStudent.phone || !this.newStudent.programme_id || !this.newStudent.academic_year_id || !this.newStudent.intake_id || !this.newStudent.date_of_birth) {
                toast('Please fill out all required fields.', 'error');
                return;
            }
            this.addStudentLoading = true;
            axios.post('{{ route('admin.applications.store') }}', this.newStudent)
            .then(res => {
                this.addStudentLoading = false;
                this.showAddStudentModal = false;
                toast(res.data.message || 'Student added successfully!', 'success');
                setTimeout(() => window.location.reload(), 1200);
            })
            .catch(err => {
                this.addStudentLoading = false;
                toast(err.response?.data?.message || 'Error adding student', 'error');
            });
        }
    }">

        <!-- Stat Metric Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-2 card-hover-effect">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500 block">Total Applicants</span>
                <span class="text-3xl font-black text-slate-900 block">{{ number_format($stats['total'] ?? 0) }}</span>
                <span class="text-[11px] font-bold text-blue-500">All Submitted & Drafts</span>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-2 card-hover-effect">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500 block">Approved Udahili</span>
                <span class="text-3xl font-black text-emerald-600 block">{{ number_format($stats['approved'] ?? 0) }}</span>
                <span class="text-[11px] font-bold text-emerald-500">Admission Granted</span>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-2 card-hover-effect">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500 block">Pending Review</span>
                <span class="text-3xl font-black text-amber-500 block">{{ number_format($stats['pending'] ?? 0) }}</span>
                <span class="text-[11px] font-bold text-amber-500">Requires Decision</span>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-2 card-hover-effect">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500 block">Rejected</span>
                <span class="text-3xl font-black text-red-500 block">{{ number_format($stats['rejected'] ?? 0) }}</span>
                <span class="text-[11px] font-bold text-red-400">Applications</span>
            </div>
        </div>
        
        <!-- Search & Multi-Filter Bar -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
            
            <form method="GET" action="{{ route('admin.applications.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-3">
                
                <!-- Search Input -->
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-extrabold text-slate-500 uppercase mb-1">Search Keywords</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Name, Email, Phone, App #" 
                           class="w-full px-4 py-2.5 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-[10px] font-extrabold text-slate-500 uppercase mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-2.5 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">All Statuses</option>
                        <option value="Draft" {{ ($filters['status'] ?? '') === 'Draft' ? 'selected' : '' }}>Draft</option>
                        <option value="Pending Payment" {{ ($filters['status'] ?? '') === 'Pending Payment' ? 'selected' : '' }}>Pending Payment</option>
                        <option value="Under Review" {{ ($filters['status'] ?? '') === 'Under Review' ? 'selected' : '' }}>Under Review</option>
                        <option value="Pending" {{ ($filters['status'] ?? '') === 'Pending' ? 'selected' : '' }}>Pending (All)</option>
                        <option value="Approved" {{ ($filters['status'] ?? '') === 'Approved' ? 'selected' : '' }}>Approved</option>
                        <option value="Rejected" {{ ($filters['status'] ?? '') === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <!-- Programme Filter -->
                <div>
                    <label class="block text-[10px] font-extrabold text-slate-500 uppercase mb-1">Programme</label>
                    <select name="programme_id" class="w-full px-4 py-2.5 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">All Programmes</option>
                        @foreach($programmes as $p)
                            <option value="{{ $p->id }}" {{ ($filters['programme_id'] ?? '') == $p->id ? 'selected' : '' }}>{{ $p->code }} - {{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="block text-[10px] font-extrabold text-slate-500 uppercase mb-1">Category</label>
                    <select name="admission_category" class="w-full px-4 py-2.5 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">All Categories</option>
                        <option value="Direct Entry" {{ ($filters['admission_category'] ?? '') === 'Direct Entry' ? 'selected' : '' }}>Direct Entry (OUT)</option>
                        <option value="Foundation Programme" {{ ($filters['admission_category'] ?? '') === 'Foundation Programme' ? 'selected' : '' }}>Foundation (SUPA)</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full gradient-btn py-2.5 rounded-2xl text-white font-extrabold text-xs shadow-md">
                        Filter
                    </button>
                    @if(!empty(array_filter($filters)))
                        <a href="{{ route('admin.applications.index') }}" class="px-3 py-2.5 rounded-2xl bg-slate-200 text-slate-800 font-extrabold text-xs hover:bg-slate-300 transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            <div class="flex flex-wrap justify-between items-center pt-3 border-t border-slate-100 text-xs">
                <!-- Bulk Actions Bar -->
                <div class="flex items-center space-x-3">
                    <button type="button" @click="bulkApprove()" :disabled="selectedApps.length === 0 || bulkLoading" 
                            class="px-5 py-2 rounded-2xl bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-extrabold text-xs shadow-md flex items-center gap-2">
                        <span x-show="!bulkLoading">✓ Bulk Approve Selected (<span x-text="selectedApps.length"></span>)</span>
                        <span x-show="bulkLoading">Approving...</span>
                    </button>
                    <button type="button" @click="showAddStudentModal = true" 
                            class="px-5 py-2 rounded-2xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs shadow-md flex items-center gap-2">
                        + Add Student
                    </button>
                </div>

                <!-- Export PDF & CSV Actions -->
                <div class="flex items-center space-x-2">
                    @if(auth()->user()->hasPermissionTo('download_reports'))
                    <a href="{{ route('admin.reports.pdf', array_merge(['type' => 'applications', 'download' => 1], request()->query())) }}" target="_blank" class="px-5 py-2 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md flex items-center gap-1.5 transition-all">
                        📄 Download PDF Report (With Logo)
                    </a>
                    <a href="{{ url('/api/v1/admin/export-report?' . http_build_query(array_merge(['type' => 'applications'], request()->query()))) }}" target="_blank" class="px-4 py-2 rounded-2xl bg-white hover:bg-slate-800 text-slate-800 hover:text-white border border-slate-200 font-extrabold text-xs shadow-md flex items-center gap-2">
                        📊 Export CSV
                    </a>
                    @else
                    <span class="text-xs text-slate-400 font-bold italic flex items-center gap-1">
                        🔒 Downloads Disabled (Requires permission)
                    </span>
                    @endif
                </div>
            </div>

        </div>

        <!-- Applications Data Directory Table -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold tracking-wider">
                        <th class="py-3.5 px-3 w-10 text-center">
                            <input type="checkbox" @change="toggleSelectAll()" :checked="selectAll" class="rounded border-slate-300 text-amber-500 focus:ring-amber-500 cursor-pointer">
                        </th>
                        <th class="py-3.5 px-4">Application #</th>
                        <th class="py-3.5 px-4">Applicant Profile</th>
                        <th class="py-3.5 px-4">Programme & Category</th>
                        <th class="py-3.5 px-4">Payment & Uploads</th>
                        <th class="py-3.5 px-4">Review Status</th>
                        <th class="py-3.5 px-4">Submitted</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-semibold">
                    @forelse($applications as $app)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-4 px-3 text-center">
                                <input type="checkbox" value="{{ $app->id }}" x-model="selectedApps" class="rounded border-slate-300 text-amber-500 focus:ring-amber-500 cursor-pointer">
                            </td>

                            <td class="py-4 px-4 font-black text-blue-600">
                                <span class="block">{{ $app->application_number }}</span>
                                <span class="text-[10px] text-slate-500 font-normal">ID: {{ $app->id }}</span>
                            </td>

                            <td class="py-4 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center font-extrabold text-slate-500 shrink-0 overflow-hidden">
                                        @if($app->applicant && $app->applicant->passport_photo_path)
                                            <img src="{{ asset('storage/' . $app->applicant->passport_photo_path) }}" alt="Passport" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr($app->applicant->user->name ?? 'A', 0, 1)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <span class="font-extrabold text-slate-900 block">{{ $app->applicant->user->name ?? 'N/A' }}</span>
                                        <span class="text-[10px] text-slate-500 font-medium block">{{ $app->applicant->user->email ?? '' }}</span>
                                        <span class="text-[10px] text-slate-500 font-medium block">Phone: {{ $app->applicant->user->phone ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="py-4 px-4">
                                <span class="block font-extrabold text-slate-900">{{ $app->programme->code ?? 'N/A' }}</span>
                                <span class="text-[10px] font-bold text-amber-500 uppercase block">{{ $app->admission_category }}</span>
                                <span class="text-[10px] text-slate-500">Entry: {{ $app->admission_type }}</span>
                            </td>

                            <td class="py-4 px-4 space-y-1">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase block w-max {{ ($app->payment->payment_status ?? '') === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    Pay: {{ strtoupper($app->payment->payment_status ?? 'Pending') }}
                                </span>
                                <span class="text-[10px] font-bold text-blue-500 block">
                                    📄 {{ $app->documents->count() }} Files Attached
                                </span>
                            </td>

                            <td class="py-4 px-4">
                                <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase {{ $app->status === 'Approved' ? 'bg-emerald-100 text-emerald-800' : ($app->status === 'Rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ $app->status }}
                                </span>
                            </td>

                            <td class="py-4 px-4 text-slate-500 text-[10px]">
                                {{ $app->submitted_at ? $app->submitted_at->format('M d, Y') : 'Draft' }}
                            </td>

                            <td class="py-4 px-4 text-right">
                                <a href="{{ route('admin.applications.show', $app->id) }}" class="gradient-btn px-4 py-2 rounded-xl text-white font-extrabold text-[10px] shadow-sm inline-flex items-center gap-1 hover:scale-105 transition-transform">
                                    360° Review & Uploads &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500">
                                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-500 flex items-center justify-center mx-auto text-xl font-bold mb-2">!</div>
                                <p class="font-bold text-sm text-slate-700">No applicants found matching the selected filter criteria.</p>
                                <p class="text-xs text-slate-500">Try resetting search filters or keywords.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-6">
                {{ $applications->withQueryString()->links() }}
            </div>
        </div>

        <!-- MODAL: ADD STUDENT ADMISSION -->
        <div x-show="showAddStudentModal" class="fixed inset-0 bg-white/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white max-w-lg w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-4 max-h-[90vh] overflow-y-auto">
                <h3 class="text-xl font-extrabold text-slate-900">Add Student Admission</h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Student Full Name *</label>
                        <input type="text" x-model="newStudent.name" placeholder="e.g. John Doe" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Email Address *</label>
                        <input type="email" x-model="newStudent.email" placeholder="john.doe@example.com" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Phone Number *</label>
                        <input type="text" x-model="newStudent.phone" placeholder="e.g. 0754123456" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Gender *</label>
                        <select x-model="newStudent.gender" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Date of Birth *</label>
                        <input type="date" x-model="newStudent.date_of_birth" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Programme *</label>
                        <select x-model="newStudent.programme_id" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="">Select Programme</option>
                            @foreach($programmes as $p)
                                <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Academic Year *</label>
                        <select x-model="newStudent.academic_year_id" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="">Select Academic Year</option>
                            @foreach($academicYears as $y)
                                <option value="{{ $y->id }}">{{ $y->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Intake *</label>
                        <select x-model="newStudent.intake_id" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="">Select Intake</option>
                            @foreach($intakes as $intake)
                                <option value="{{ $intake->id }}">{{ $intake->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Admission Type *</label>
                        <select x-model="newStudent.admission_type" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="Form Six">Form Six</option>
                            <option value="Diploma">Diploma</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Admission Category *</label>
                        <select x-model="newStudent.admission_category" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="Direct Entry">Direct Entry</option>
                            <option value="Foundation">Foundation</option>
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Admission Status *</label>
                        <select x-model="newStudent.status" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="Draft">Draft</option>
                            <option value="Pending Payment">Pending Payment</option>
                            <option value="Under Review">Under Review</option>
                            <option value="Verified">Verified</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                            <option value="Waitlist">Waitlist</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                    <button @click="showAddStudentModal = false" :disabled="addStudentLoading" class="px-5 py-2.5 rounded-2xl bg-slate-200 text-xs font-extrabold hover:bg-slate-300 transition-colors">Cancel</button>
                    <button @click="addStudent()" :disabled="addStudentLoading" class="gradient-btn px-6 py-2.5 rounded-2xl text-white font-extrabold text-xs shadow-md flex items-center gap-2">
                        <span x-show="!addStudentLoading">Create Admission</span>
                        <span x-show="addStudentLoading">Creating...</span>
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
