<x-app-layout title="{{ request()->get('view') === 'control_numbers' ? 'Government Control Numbers Desk' : 'Payments & Government Control Numbers Desk' }}">
    <x-slot name="header">
        {{ request()->get('view') === 'control_numbers' ? 'Government Control Numbers Desk' : 'Finance & Government Control Numbers Desk' }}
    </x-slot>

    <div class="w-full space-y-8" x-data="{
        search: '{{ $filters['search'] ?? '' }}',
        statusFilter: '{{ $filters['status'] ?? '' }}',
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        selectedPayment: null,

        paymentsList: [
            @foreach($payments as $p)
            {
                id: {{ $p->id }},
                control_number: '{{ $p->control_number }}',
                applicant_name: {{ json_encode($p->application->applicant->user->name ?? 'Baraka Ally Juma') }},
                programme: {{ json_encode($p->application->programme->code ?? 'BAED') }},
                amount: {{ $p->amount ?? 20000 }},
                currency: '{{ $p->currency ?? 'TZS' }}',
                payment_status: '{{ $p->payment_status ?? 'pending' }}',
                payment_method: '{{ $p->payment_method ?? 'M-Pesa' }}',
                paid_at: '{{ $p->paid_at ? $p->paid_at->format('M d, Y H:i') : '-' }}'
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
                const matchQuery = !this.search.trim() || p.control_number.includes(this.search) || p.applicant_name.toLowerCase().includes(this.search.toLowerCase());
                const matchStatus = !this.statusFilter || p.payment_status.toLowerCase() === this.statusFilter.toLowerCase();
                return matchQuery && matchStatus;
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
                control_number: generatedControl,
                applicant_name: this.newControl.applicant_name,
                programme: this.newControl.programme,
                amount: this.newControl.amount,
                currency: 'TZS',
                payment_status: 'pending',
                payment_method: this.newControl.payment_method,
                paid_at: '-'
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
        
        <!-- Controls & Filters Bar -->
        <form method="GET" action="{{ route('admin.payments.index') }}" class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
            @if(request()->has('view'))
                <input type="hidden" name="view" value="{{ request()->get('view') }}">
            @endif

            <div class="flex items-center space-x-3 w-full sm:w-auto">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search Control # or Name..." 
                       class="px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-semibold outline-none focus:ring-2 focus:ring-amber-500 w-full sm:w-64">
                
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

            <div class="flex items-center space-x-3 w-full sm:w-auto justify-end">
                <button type="button" @click="showCreateModal = true" class="gradient-btn-gold px-6 py-3 rounded-2xl text-slate-950 font-black text-xs shadow-md hover:scale-105 transition-transform flex items-center gap-2">
                    <span>+ Generate Control Number</span>
                </button>
            </div>
        </form>

        <!-- Payments Data Table -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-500 uppercase text-[10px] font-extrabold tracking-wider">
                        <th class="py-3.5 px-4">Control Number</th>
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
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-4 px-4 font-black text-blue-600" x-text="p.control_number"></td>
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
                            <td class="py-4 px-4 text-right space-x-1">
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

        <!-- GENERATE CONTROL NUMBER MODAL -->
        <div x-show="showCreateModal" class="fixed inset-0 bg-white/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white max-w-lg w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-4">
                <h3 class="text-xl font-extrabold text-slate-900">Generate Government Control Number</h3>

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

    </div>
</x-app-layout>
