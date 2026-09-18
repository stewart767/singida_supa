<x-app-layout title="{{ request()->get('view') === 'duplicates' ? 'Duplicate Control Numbers Detector & Remediation Desk' : (request()->get('view') === 'control_numbers' ? 'Government Control Numbers Desk' : 'Payments & Government Control Numbers Desk') }}">
    <x-slot name="header">
        {{ request()->get('view') === 'duplicates' ? 'Duplicate Control Numbers Detector & Remediation Desk' : (request()->get('view') === 'control_numbers' ? 'Government Control Numbers Desk' : 'Finance & Government Control Numbers Desk') }}
    </x-slot>

    <div class="w-full space-y-8" x-data="{
        currentView: '{{ $filters['view'] ?? 'all' }}',
        search: '{{ $filters['search'] ?? '' }}',
        statusFilter: '{{ $filters['status'] ?? '' }}',
        isSuperAdmin: {{ auth()->user()->isSuperAdmin() ? 'true' : 'false' }},
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        showApproveModal: false,
        showRegenerateModal: false,
        selectedPayment: null,
        approvingPayment: null,
        approvingLoading: false,
        approvalMethod: 'NMB Bank',
        approvalRef: '',

        regeneratingPayment: null,
        regeneratingLoading: false,
        regenMode: 'auto',
        customControlNumber: '',
        regenReason: 'Duplicate control number collision resolution',

        duplicateCount: {{ $duplicateCount ?? 0 }},

        paymentsList: [
            @foreach($payments as $p)
            {
                id: {{ $p->id }},
                application_id: {{ $p->application_id ?? 0 }},
                control_number: '{{ $p->control_number }}',
                applicant_name: {{ json_encode($p->application->applicant->user->name ?? 'Applicant') }},
                applicant_phone: {{ json_encode($p->application->applicant->user->phone ?? '-') }},
                applicant_email: {{ json_encode($p->application->applicant->user->email ?? '-') }},
                application_number: {{ json_encode($p->application->application_number ?? ('#' . $p->application_id)) }},
                programme: {{ json_encode($p->application->programme->code ?? ($p->application->programme->name ?? 'BAED')) }},
                amount: {{ $p->amount ?? 20000 }},
                currency: '{{ $p->currency ?? 'TZS' }}',
                payment_status: '{{ $p->payment_status ?? 'pending' }}',
                payment_method: '{{ $p->payment_method ?? 'Bank Deposit' }}',
                paid_at: '{{ $p->paid_at ? $p->paid_at->format('M d, Y H:i') : '-' }}',
                is_duplicate: {{ in_array($p->control_number, $duplicateGroups->pluck('control_number')->all()) ? 'true' : 'false' }}
            },
            @endforeach
        ],

        newControl: {
            applicant_name: '',
            programme: 'BAED',
            amount: 20000,
            payment_method: 'M-Pesa'
        },

        editPaymentData: { id: null, control_number: '', applicant_name: '', amount: 20000, payment_status: 'pending', payment_method: 'M-Pesa' },

        filteredPayments() {
            return this.paymentsList.filter(p => {
                const matchQuery = !this.search.trim() || p.control_number.includes(this.search) || p.applicant_name.toLowerCase().includes(this.search.toLowerCase()) || p.application_number.toLowerCase().includes(this.search.toLowerCase());
                const matchStatus = !this.statusFilter || p.payment_status.toLowerCase() === this.statusFilter.toLowerCase();
                return matchQuery && matchStatus;
            });
        },

        openApprove(p) {
            if (!this.isSuperAdmin) {
                toast('Only Superadmin has authorization to approve payments.', 'error');
                return;
            }
            this.approvingPayment = p;
            this.approvalMethod = p.payment_method || 'NMB Bank';
            this.approvalRef = 'SUPA-APPR-' + Math.floor(100000 + Math.random() * 900000);
            this.showApproveModal = true;
        },

        confirmApprove() {
            if (!this.approvingPayment || !this.isSuperAdmin) return;
            this.approvingLoading = true;
            axios.post('{{ url('/api/v1/admin/payments') }}/' + this.approvingPayment.id + '/verify', {
                status: 'paid',
                payment_method: this.approvalMethod,
                transaction_reference: this.approvalRef
            })
            .then(res => {
                this.approvingLoading = false;
                this.showApproveModal = false;
                const idx = this.paymentsList.findIndex(p => p.id === this.approvingPayment.id);
                if (idx !== -1) {
                    this.paymentsList[idx].payment_status = 'paid';
                    this.paymentsList[idx].payment_method = this.approvalMethod;
                    this.paymentsList[idx].paid_at = new Date().toLocaleString();
                }
                toast(res.data?.message || 'Payment approved successfully!', 'success');
                setTimeout(() => window.location.reload(), 1000);
            })
            .catch(err => {
                this.approvingLoading = false;
                toast(err.response?.data?.message || 'Failed to approve payment.', 'error');
            });
        },

        openRegenerate(p) {
            if (!this.isSuperAdmin) {
                toast('Only Superadmin has authorization to refresh or regenerate control numbers.', 'error');
                return;
            }
            if (p.payment_status === 'paid') {
                toast('Warning: This applicant has already paid for this control number. Do not regenerate paid records.', 'error');
                return;
            }
            this.regeneratingPayment = p;
            this.regenMode = 'auto';
            this.customControlNumber = '';
            this.regenReason = 'Duplicate control number collision resolution';
            this.showRegenerateModal = true;
        },

        confirmRegenerate() {
            if (!this.regeneratingPayment || !this.isSuperAdmin) return;
            this.regeneratingLoading = true;

            const payload = {
                custom_control_number: this.regenMode === 'custom' ? this.customControlNumber : null,
                reason: this.regenReason
            };

            axios.post('{{ url('/admin/payments') }}/' + this.regeneratingPayment.id + '/regenerate-control-number', payload)
            .then(res => {
                this.regeneratingLoading = false;
                this.showRegenerateModal = false;
                toast(res.data?.message || 'New control number assigned successfully!', 'success');
                setTimeout(() => window.location.reload(), 1200);
            })
            .catch(err => {
                this.regeneratingLoading = false;
                toast(err.response?.data?.message || 'Failed to regenerate control number.', 'error');
            });
        },

        createControlNumber() {
            if (!this.newControl.applicant_name) {
                toast('Please enter Applicant Name', 'error');
                return;
            }
            const generatedControl = '99100' + Math.floor(1000000000 + Math.random() * 9000000000);
            const created = {
                id: Date.now(),
                application_id: 0,
                control_number: generatedControl,
                applicant_name: this.newControl.applicant_name,
                applicant_phone: '-',
                applicant_email: '-',
                application_number: 'MANUAL-' + Date.now(),
                programme: this.newControl.programme,
                amount: this.newControl.amount,
                currency: 'TZS',
                payment_status: 'pending',
                payment_method: this.newControl.payment_method,
                paid_at: '-',
                is_duplicate: false
            };
            this.paymentsList.unshift(created);
            this.showCreateModal = false;
            this.newControl = { applicant_name: '', programme: 'BAED', amount: 20000, payment_method: 'M-Pesa' };
            toast('New Control Number ' + generatedControl + ' generated!', 'success');
        },

        openEdit(p) {
            this.editPaymentData = JSON.parse(JSON.stringify(p));
            this.showEditModal = true;
        },

        updatePayment() {
            const idx = this.paymentsList.findIndex(p => p.id === this.editPaymentData.id);
            if (idx !== -1) {
                this.paymentsList[idx] = { ...this.editPaymentData };
                toast('Payment details updated successfully.', 'success');
            }
            this.showEditModal = false;
        },

        confirmDelete(p) {
            this.selectedPayment = p;
            this.showDeleteModal = true;
        },

        deletePayment() {
            if (this.selectedPayment) {
                this.paymentsList = this.paymentsList.filter(p => p.id !== this.selectedPayment.id);
                toast('Control Number record deleted.', 'success');
            }
            this.showDeleteModal = false;
            this.selectedPayment = null;
        }
    }">

        <!-- DUPLICATE DETECTION HERO ALERT (Rendered whenever duplicates exist) -->
        @if(($duplicateCount ?? 0) > 0)
        <div class="p-6 rounded-3xl bg-gradient-to-r from-amber-500/15 via-red-500/10 to-amber-500/5 border-2 border-amber-400/60 shadow-lg flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="flex items-start space-x-4">
                <div class="w-14 h-14 rounded-2xl bg-amber-500/20 text-amber-700 flex items-center justify-center text-3xl shrink-0 shadow-inner">
                    ⚠️
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-black text-slate-900">Duplicate Control Numbers Detected!</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-red-600 text-white animate-pulse">
                            {{ $duplicateCount }} Duplicate {{ Str::plural('Group', $duplicateCount) }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-700 font-medium max-w-3xl leading-relaxed">
                        The system has identified <strong>{{ $duplicateCount }} control number(s)</strong> that have been given to more than one person. Superadmins can review below, retain the confirmed control number for paying students, and refresh/regenerate a fresh control number for applicants who did not pay.
                    </p>
                </div>
            </div>
            <div class="shrink-0 flex items-center gap-3">
                @if(($filters['view'] ?? '') !== 'duplicates')
                <a href="{{ route('admin.payments.index', ['view' => 'duplicates']) }}" class="px-5 py-3 rounded-2xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-black shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                    <span>⚡ Resolve Conflicts</span>
                </a>
                @endif
            </div>
        </div>
        @endif

        <!-- TAB NAVIGATION / VIEW SELECTOR -->
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-200 pb-4">
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.payments.index') }}" 
                   class="px-5 py-2.5 rounded-2xl text-xs font-extrabold transition-all {{ empty(request()->get('view')) ? 'bg-blue-900 text-white shadow-md' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    All Payments
                </a>

                <a href="{{ route('admin.payments.index', ['view' => 'control_numbers']) }}" 
                   class="px-5 py-2.5 rounded-2xl text-xs font-extrabold transition-all {{ request()->get('view') === 'control_numbers' ? 'bg-blue-900 text-white shadow-md' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    Government Control Numbers
                </a>

                <a href="{{ route('admin.payments.index', ['view' => 'duplicates']) }}" 
                   class="px-5 py-2.5 rounded-2xl text-xs font-extrabold transition-all flex items-center gap-2 {{ request()->get('view') === 'duplicates' ? 'bg-amber-600 text-white shadow-md' : 'bg-amber-50 text-amber-900 hover:bg-amber-100 border border-amber-200' }}">
                    <span>Duplicate Detector</span>
                    @if(($duplicateCount ?? 0) > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ request()->get('view') === 'duplicates' ? 'bg-white text-amber-800' : 'bg-red-600 text-white' }}">
                            {{ $duplicateCount }}
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">
                            0
                        </span>
                    @endif
                </a>
            </div>

            @if(auth()->user()->isSuperAdmin())
            <div class="flex items-center space-x-3">
                <button type="button" @click="showCreateModal = true" class="gradient-btn-gold px-6 py-2.5 rounded-2xl text-slate-950 font-black text-xs shadow-md hover:scale-105 transition-transform flex items-center gap-2">
                    <span>+ Issue Control Number</span>
                </button>
            </div>
            @endif
        </div>

        <!-- DUPLICATE RESOLUTION HUB (Shown on view=duplicates or top of page) -->
        @if(request()->get('view') === 'duplicates')
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                        <span>Duplicate Control Number Resolution Hub</span>
                        <span class="text-xs px-3 py-1 rounded-full font-bold bg-amber-100 text-amber-900 border border-amber-300">
                            {{ $duplicateCount }} {{ Str::plural('Collision', $duplicateCount) }} Found
                        </span>
                    </h3>
                    <p class="text-xs text-slate-500 font-medium">Inspect every duplicated control number below. Paid records are retained, and you can regenerate a unique control number for unpaid records.</p>
                </div>
            </div>

            @if($duplicateGroups->isEmpty())
            <div class="bg-white p-12 rounded-3xl border border-emerald-200 text-center space-y-4 shadow-sm">
                <div class="w-16 h-16 rounded-3xl bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto text-3xl font-black">
                    ✓
                </div>
                <h4 class="text-base font-black text-slate-900">All Clear! No Duplicate Control Numbers Found</h4>
                <p class="text-xs text-slate-500 max-w-md mx-auto">Every applicant and student admission record in the database has a unique, collision-free control number.</p>
                <div class="pt-2">
                    <a href="{{ route('admin.payments.index') }}" class="px-5 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold text-xs inline-block transition-colors">
                        ← Return to All Payments Desk
                    </a>
                </div>
            </div>
            @else
            <div class="space-y-6">
                @foreach($duplicateGroups as $group)
                <div class="bg-white rounded-3xl border-2 border-amber-300/80 shadow-md p-6 space-y-5">
                    <!-- Group Header -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 pb-4 border-b border-slate-100">
                        <div class="flex items-center space-x-3">
                            <div class="px-3 py-1.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 font-mono font-black text-sm">
                                Control #: {{ $group['control_number'] }}
                            </div>
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-red-100 text-red-800 border border-red-200">
                                {{ $group['total_count'] }} Applicants Assigned
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-extrabold text-slate-600">Diagnosis:</span>
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $group['has_paid'] ? 'bg-amber-100 text-amber-900' : 'bg-slate-100 text-slate-700' }}">
                                {{ $group['status_description'] }}
                            </span>
                        </div>
                    </div>

                    <!-- Comparison Table of Students sharing this Control # -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold tracking-wider bg-slate-50/50">
                                    <th class="py-3 px-4 rounded-l-xl">Applicant / User</th>
                                    <th class="py-3 px-4">Application #</th>
                                    <th class="py-3 px-4">Programme</th>
                                    <th class="py-3 px-4">Amount</th>
                                    <th class="py-3 px-4">Payment Status</th>
                                    <th class="py-3 px-4">Receipt / Ref</th>
                                    <th class="py-3 px-4 text-right rounded-r-xl">Conflict Resolution Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-semibold">
                                @foreach($group['payments'] as $gp)
                                @php
                                    $isPaid = strtolower($gp->payment_status) === 'paid';
                                    $gpApplicant = $gp->application?->applicant?->user;
                                    $gpAppNumber = $gp->application?->application_number ?? ('#' . $gp->application_id);
                                    $gpProgramme = $gp->application?->programme?->code ?? ($gp->application?->programme?->name ?? 'BAED');
                                @endphp
                                <tr class="{{ $isPaid ? 'bg-emerald-50/40' : 'hover:bg-slate-50' }} transition-colors">
                                    <td class="py-4 px-4">
                                        <div class="font-black text-slate-900">{{ $gpApplicant->name ?? 'Unknown Applicant' }}</div>
                                        <div class="text-[11px] text-slate-500 font-bold flex items-center gap-2">
                                            <span>📞 {{ $gpApplicant->phone ?? '-' }}</span>
                                            <span>✉️ {{ $gpApplicant->email ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 font-mono font-bold text-slate-700">
                                        @if($gp->application_id)
                                            <a href="{{ route('admin.applications.show', $gp->application_id) }}" class="text-blue-600 hover:underline">
                                                {{ $gpAppNumber }}
                                            </a>
                                        @else
                                            {{ $gpAppNumber }}
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 font-bold text-slate-600">
                                        {{ $gpProgramme }}
                                    </td>
                                    <td class="py-4 px-4 font-black text-slate-900">
                                        {{ $gp->currency }} {{ number_format($gp->amount) }}
                                    </td>
                                    <td class="py-4 px-4">
                                        @if($isPaid)
                                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-900 border border-emerald-300 flex items-center gap-1 w-max">
                                                <span>✓</span> PAID & VERIFIED
                                            </span>
                                        @else
                                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-amber-100 text-amber-900 border border-amber-300 flex items-center gap-1 w-max">
                                                <span>⏳</span> {{ strtoupper($gp->payment_status ?? 'PENDING') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-slate-500 font-mono text-[11px]">
                                        @if($gp->transaction_reference)
                                            <span class="font-bold text-slate-800">{{ $gp->transaction_reference }}</span>
                                        @elseif($gp->receipt_path)
                                            <a href="{{ asset('storage/' . $gp->receipt_path) }}" target="_blank" class="text-blue-600 hover:underline font-bold">
                                                View Receipt ↗
                                            </a>
                                        @else
                                            <span class="text-slate-400">None</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-right whitespace-nowrap">
                                        @if($isPaid)
                                            <span class="px-3.5 py-2 rounded-xl bg-emerald-100 text-emerald-900 font-extrabold text-[11px] inline-flex items-center gap-1.5 shadow-sm border border-emerald-300">
                                                <span>🔒</span> Paid - Retained & Protected
                                            </span>
                                        @else
                                            @if(auth()->user()->isSuperAdmin())
                                                <button type="button" 
                                                        @click="openRegenerate({
                                                            id: {{ $gp->id }},
                                                            application_id: {{ $gp->application_id ?? 0 }},
                                                            control_number: '{{ $gp->control_number }}',
                                                            applicant_name: {{ json_encode($gpApplicant->name ?? 'Applicant') }},
                                                            application_number: {{ json_encode($gpAppNumber) }},
                                                            payment_status: '{{ $gp->payment_status ?? 'pending' }}'
                                                        })" 
                                                        class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-black text-xs shadow-md hover:shadow-lg transition-all inline-flex items-center gap-1.5">
                                                    <span>⚡</span> Regenerate Control Number
                                                </button>
                                            @else
                                                <span class="text-[11px] text-slate-400 font-bold">Requires Superadmin</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endif

        <!-- Controls & Filters Bar (For Standard Listing) -->
        <form method="GET" action="{{ route('admin.payments.index') }}" class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
            @if(request()->has('view'))
                <input type="hidden" name="view" value="{{ request()->get('view') }}">
            @endif

            <div class="flex items-center space-x-3 w-full sm:w-auto">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search Control #, App # or Name..." 
                       class="px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-semibold outline-none focus:ring-2 focus:ring-amber-500 w-full sm:w-72">
                
                <select name="status" onchange="this.form.submit()" class="px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-semibold outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ ($filters['status'] ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>

                <button type="submit" class="gradient-btn px-5 py-3 rounded-2xl text-white font-extrabold text-xs shadow-md">
                    Filter
                </button>

                @if(!empty(array_filter($filters ?? [])))
                    <a href="{{ route('admin.payments.index') }}{{ request()->has('view') ? '?view=' . request()->get('view') : '' }}" class="px-4 py-3 rounded-2xl bg-slate-200 text-slate-800 font-extrabold text-xs hover:bg-slate-300 transition-colors">
                        Reset
                    </a>
                @endif
            </div>

            <div class="text-xs font-bold text-slate-500">
                Showing {{ $payments->total() }} records
            </div>
        </form>

        <!-- Main Payments Data Table -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold tracking-wider">
                        <th class="py-3.5 px-4">Control Number</th>
                        <th class="py-3.5 px-4">Application #</th>
                        <th class="py-3.5 px-4">Applicant Name</th>
                        <th class="py-3.5 px-4">Programme</th>
                        <th class="py-3.5 px-4">Amount</th>
                        <th class="py-3.5 px-4">Method</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Paid Timestamp</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-semibold">
                    <template x-for="p in filteredPayments()" :key="p.id">
                        <tr class="hover:bg-slate-50 transition-colors" :class="p.is_duplicate ? 'bg-amber-50/30' : ''">
                            <td class="py-4 px-4 font-black">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-blue-600 font-mono" x-text="p.control_number"></span>
                                    <template x-if="p.is_duplicate">
                                        <span class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-amber-200 text-amber-900 uppercase tracking-tighter" title="Shared by multiple applicants">
                                            ⚠️ Duplicate
                                        </span>
                                    </template>
                                </div>
                            </td>
                            <td class="py-4 px-4 font-mono font-bold text-slate-700" x-text="p.application_number"></td>
                            <td class="py-4 px-4 font-extrabold text-slate-900" x-text="p.applicant_name"></td>
                            <td class="py-4 px-4 font-bold text-slate-600" x-text="p.programme"></td>
                            <td class="py-4 px-4 font-black text-slate-900" x-text="p.currency + ' ' + Number(p.amount).toLocaleString()"></td>
                            <td class="py-4 px-4 text-slate-500 font-bold" x-text="p.payment_method"></td>
                            <td class="py-4 px-4">
                                <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase"
                                      :class="p.payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : (p.payment_status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800')"
                                      x-text="p.payment_status">
                                </span>
                            </td>
                            <td class="py-4 px-4 text-[10px] text-slate-500 font-bold" x-text="p.paid_at"></td>
                            <td class="py-4 px-4 text-right space-x-1 whitespace-nowrap">
                                @if(auth()->user()->isSuperAdmin())
                                    <template x-if="p.payment_status !== 'paid'">
                                        <button @click="openApprove(p)" class="px-3 py-1.5 rounded-xl bg-emerald-600/10 text-emerald-700 hover:bg-emerald-600 hover:text-white font-extrabold text-[10px] transition-all inline-flex items-center gap-1 shadow-sm">
                                            <span>✓</span> Approve
                                        </button>
                                    </template>
                                    <template x-if="p.payment_status !== 'paid'">
                                        <button @click="openRegenerate(p)" class="px-3 py-1.5 rounded-xl bg-amber-600/10 text-amber-700 hover:bg-amber-600 hover:text-white font-extrabold text-[10px] transition-all inline-flex items-center gap-1 shadow-sm" title="Issue new control number">
                                            <span>⚡</span> Refresh #
                                        </button>
                                    </template>
                                @endif
                                <button @click="openEdit(p)" class="px-3 py-1.5 rounded-xl bg-blue-600/10 text-blue-600 hover:bg-blue-600 hover:text-white font-extrabold text-[10px] transition-all">
                                    Edit
                                </button>
                                <button @click="confirmDelete(p)" class="px-3 py-1.5 rounded-xl bg-red-600/10 text-red-600 hover:bg-red-600 hover:text-white font-extrabold text-[10px] transition-all">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="pt-4">
            {{ $payments->appends(request()->query())->links() }}
        </div>

        <!-- REGENERATE CONTROL NUMBER MODAL -->
        @if(auth()->user()->isSuperAdmin())
        <div x-show="showRegenerateModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white max-w-lg w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-5 text-left" @click.away="!regeneratingLoading && (showRegenerateModal = false)">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/15 text-amber-700 flex items-center justify-center text-2xl font-black">
                        ⚡
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900">Refresh / Regenerate Control Number</h3>
                        <p class="text-xs text-slate-500 font-medium">Superadmin Control Number Conflict Remediation</p>
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2.5 text-xs">
                    <div class="flex justify-between border-b border-slate-200 pb-1.5">
                        <span class="text-slate-500 font-bold">Applicant:</span>
                        <strong class="text-slate-900 font-black" x-text="regeneratingPayment?.applicant_name"></strong>
                    </div>
                    <div class="flex justify-between border-b border-slate-200 pb-1.5">
                        <span class="text-slate-500 font-bold">Application #:</span>
                        <strong class="text-slate-700 font-mono font-extrabold" x-text="regeneratingPayment?.application_number"></strong>
                    </div>
                    <div class="flex justify-between items-center pt-0.5">
                        <span class="text-slate-500 font-bold">Current Control Number:</span>
                        <strong class="text-red-600 font-mono font-black text-sm" x-text="regeneratingPayment?.control_number"></strong>
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-[11px] text-amber-900 font-medium flex items-start gap-2.5">
                    <span class="text-base">💡</span>
                    <span>This applicant has not paid for the duplicate control number. A new, unique control number will be generated and assigned to this applicant without affecting any paid student records.</span>
                </div>

                <!-- Mode Selector -->
                <div class="space-y-3 text-xs">
                    <label class="block font-black text-slate-800 uppercase tracking-wider text-[10px]">Generation Method</label>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" @click="regenMode = 'auto'" 
                                class="p-3.5 rounded-2xl border-2 text-left font-extrabold transition-all"
                                :class="regenMode === 'auto' ? 'border-amber-500 bg-amber-50 text-amber-950 shadow-sm' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'">
                            <div class="font-black text-xs">⚡ Automatic (Unique)</div>
                            <div class="text-[10px] text-slate-500 font-medium mt-0.5">Guaranteed non-colliding standard format</div>
                        </button>

                        <button type="button" @click="regenMode = 'custom'" 
                                class="p-3.5 rounded-2xl border-2 text-left font-extrabold transition-all"
                                :class="regenMode === 'custom' ? 'border-amber-500 bg-amber-50 text-amber-950 shadow-sm' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'">
                            <div class="font-black text-xs">✍️ Custom Control #</div>
                            <div class="text-[10px] text-slate-500 font-medium mt-0.5">Enter specific NMB gateway control number</div>
                        </button>
                    </div>

                    <div x-show="regenMode === 'custom'" class="pt-2">
                        <label class="block font-extrabold text-slate-700 mb-1">New Control Number</label>
                        <input type="text" x-model="customControlNumber" placeholder="e.g. 9910026001234" class="w-full p-3 rounded-2xl border border-slate-300 bg-white font-mono font-bold text-xs focus:ring-2 focus:ring-amber-500 outline-none">
                    </div>

                    <div>
                        <label class="block font-extrabold text-slate-700 mb-1">Audit Reason / Note</label>
                        <input type="text" x-model="regenReason" placeholder="Duplicate control number collision resolution" class="w-full p-3 rounded-2xl border border-slate-300 bg-white font-bold text-xs focus:ring-2 focus:ring-amber-500 outline-none">
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-3">
                    <button type="button" @click="showRegenerateModal = false" :disabled="regeneratingLoading" class="px-5 py-2.5 rounded-2xl bg-slate-200 text-xs font-extrabold hover:bg-slate-300 transition-colors">
                        Cancel
                    </button>
                    <button type="button" @click="confirmRegenerate()" :disabled="regeneratingLoading" class="px-6 py-2.5 rounded-2xl bg-amber-600 hover:bg-amber-700 text-white font-black text-xs shadow-md disabled:opacity-60 transition-all flex items-center gap-2">
                        <span x-show="!regeneratingLoading">⚡ Confirm & Assign New Control #</span>
                        <span x-show="regeneratingLoading">Regenerating...</span>
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- GENERATE CONTROL NUMBER MODAL -->
        <div x-show="showCreateModal" class="fixed inset-0 bg-white/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white max-w-lg w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-4">
                <h3 class="text-xl font-extrabold text-slate-900">Issue Government Control Number</h3>

                <div>
                    <label class="block font-extrabold uppercase mb-1 text-xs">Applicant Full Name</label>
                    <input type="text" x-model="newControl.applicant_name" placeholder="e.g. Ally Mwangi Juma" class="w-full p-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block font-extrabold uppercase mb-1">Programme Code</label>
                        <input type="text" x-model="newControl.programme" placeholder="BAED" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold">
                    </div>
                    <div>
                        <label class="block font-extrabold uppercase mb-1">Amount (TZS)</label>
                        <input type="number" x-model="newControl.amount" value="20000" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold">
                    </div>
                </div>

                <div>
                    <label class="block font-extrabold uppercase mb-1 text-xs">Payment Channel</label>
                    <select x-model="newControl.payment_method" class="w-full p-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold">
                        <option value="M-Pesa">M-Pesa Mobile Money</option>
                        <option value="TigoPesa">TigoPesa Mobile Money</option>
                        <option value="Airtel Money">Airtel Money</option>
                        <option value="CRDB Bank">CRDB Bank Branch</option>
                        <option value="NMB Bank">NMB Bank Branch</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button @click="showCreateModal = false" class="px-5 py-2.5 rounded-2xl bg-slate-200 text-xs font-extrabold">Cancel</button>
                    <button @click="createControlNumber()" class="gradient-btn-gold px-6 py-2.5 rounded-2xl text-slate-950 font-black text-xs shadow-md">Generate Control #</button>
                </div>
            </div>
        </div>

        <!-- EDIT PAYMENT MODAL -->
        <div x-show="showEditModal" class="fixed inset-0 bg-white/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white max-w-lg w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-4">
                <h3 class="text-xl font-extrabold text-slate-900">Edit Payment Details</h3>

                <div>
                    <label class="block font-extrabold uppercase mb-1 text-xs">Control Number</label>
                    <input type="text" x-model="editPaymentData.control_number" readonly class="w-full p-3.5 rounded-2xl border border-slate-300 bg-slate-100 text-xs font-black text-blue-600">
                </div>

                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block font-extrabold uppercase mb-1">Amount (TZS)</label>
                        <input type="number" x-model="editPaymentData.amount" class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold">
                    </div>
                    <div>
                        <label class="block font-extrabold uppercase mb-1">Payment Status</label>
                        <input type="text" x-model="editPaymentData.payment_status" readonly class="w-full p-3 rounded-2xl border border-slate-300 bg-slate-100 font-bold uppercase text-xs text-slate-600">
                    </div>
                </div>

                <div>
                    <label class="block font-extrabold uppercase mb-1 text-xs">Payment Method</label>
                    <select x-model="editPaymentData.payment_method" class="w-full p-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-bold">
                        <option value="M-Pesa">M-Pesa Mobile Money</option>
                        <option value="TigoPesa">TigoPesa Mobile Money</option>
                        <option value="Airtel Money">Airtel Money</option>
                        <option value="CRDB Bank">CRDB Bank Branch</option>
                        <option value="NMB Bank">NMB Bank Branch</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button @click="showEditModal = false" class="px-5 py-2.5 rounded-2xl bg-slate-200 text-xs font-extrabold">Cancel</button>
                    <button @click="updatePayment()" class="gradient-btn px-6 py-2.5 rounded-2xl text-white font-extrabold text-xs shadow-md">Save Payment Changes</button>
                </div>
            </div>
        </div>

        <!-- DELETE CONFIRMATION MODAL -->
        <div x-show="showDeleteModal" class="fixed inset-0 bg-white/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white max-w-md w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-4 text-center">
                <div class="w-14 h-14 rounded-2xl bg-red-500/10 text-red-500 flex items-center justify-center mx-auto text-2xl font-bold">⚠️</div>
                <h3 class="text-lg font-extrabold text-slate-900">Delete Control Number Record?</h3>
                <p class="text-xs text-slate-500">Are you sure you want to revoke Control # <strong class="text-slate-900" x-text="selectedPayment?.control_number"></strong>?</p>
                <div class="flex justify-center space-x-3 pt-2">
                    <button @click="showDeleteModal = false" class="px-5 py-2.5 rounded-2xl bg-slate-200 text-xs font-extrabold">Cancel</button>
                    <button @click="deletePayment()" class="px-6 py-2.5 rounded-2xl bg-red-600 hover:bg-red-700 text-white font-extrabold text-xs shadow-md">Confirm Revoke</button>
                </div>
            </div>
        </div>

        @if(auth()->user()->isSuperAdmin())
        <!-- SUPERADMIN APPROVE PAYMENT MODAL -->
        <div x-show="showApproveModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white max-w-lg w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-5 text-left">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-xl font-black">
                        ✓
                    </div>
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900">Approve Application Payment</h3>
                        <p class="text-xs text-slate-500">Superadmin Manual Verification & Admission Advancement</p>
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2 text-xs">
                    <div class="flex justify-between border-b border-slate-200 pb-1.5">
                        <span class="text-slate-500 font-bold">Applicant:</span>
                        <strong class="text-slate-900 font-extrabold" x-text="approvingPayment?.applicant_name"></strong>
                    </div>
                    <div class="flex justify-between border-b border-slate-200 pb-1.5">
                        <span class="text-slate-500 font-bold">Control Number:</span>
                        <strong class="text-blue-600 font-mono font-black" x-text="approvingPayment?.control_number"></strong>
                    </div>
                    <div class="flex justify-between border-b border-slate-200 pb-1.5">
                        <span class="text-slate-500 font-bold">Programme:</span>
                        <strong class="text-slate-900 font-bold" x-text="approvingPayment?.programme"></strong>
                    </div>
                    <div class="flex justify-between items-center pt-1">
                        <span class="text-slate-500 font-bold">Amount to Verify:</span>
                        <strong class="text-emerald-700 font-black text-sm" x-text="(approvingPayment?.currency || 'TZS') + ' ' + Number(approvingPayment?.amount || 20000).toLocaleString()"></strong>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block font-extrabold uppercase mb-1 text-slate-700">Payment Channel</label>
                        <select x-model="approvalMethod" class="w-full p-3 rounded-2xl border border-slate-300 bg-white font-bold text-xs">
                            <option value="NMB Bank">NMB Bank Branch</option>
                            <option value="M-Pesa">M-Pesa Mobile Money</option>
                            <option value="TigoPesa">TigoPesa Mobile Money</option>
                            <option value="Airtel Money">Airtel Money</option>
                            <option value="CRDB Bank">CRDB Bank</option>
                            <option value="Bank Transfer">Direct Bank Transfer</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-extrabold uppercase mb-1 text-slate-700">Ref / Receipt #</label>
                        <input type="text" x-model="approvalRef" placeholder="e.g. NMB-TRX-10294" class="w-full p-3 rounded-2xl border border-slate-300 bg-white font-mono font-bold text-xs">
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-[11px] text-amber-800 font-semibold flex items-center gap-2">
                    <span>⚠️</span>
                    <span>Approving this payment will mark it as <strong>PAID</strong> and advance the applicant's status to <strong>IN_PROGRESS</strong>.</span>
                </div>

                <div class="flex justify-end space-x-3 pt-2">
                    <button type="button" @click="showApproveModal = false" class="px-5 py-2.5 rounded-2xl bg-slate-200 text-xs font-extrabold hover:bg-slate-300 transition-colors">
                        Cancel
                    </button>
                    <button type="button" @click="confirmApprove()" :disabled="approvingLoading" class="px-6 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md disabled:opacity-60 transition-all flex items-center gap-2">
                        <span x-show="!approvingLoading">✓ Confirm & Approve Payment</span>
                        <span x-show="approvingLoading">Approving...</span>
                    </button>
                </div>
            </div>
        </div>
        @endif

    </div>
</x-app-layout>
