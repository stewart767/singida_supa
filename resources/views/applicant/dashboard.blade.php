<x-app-layout title="Applicant Dashboard">

    <x-slot name="header">Applicant Dashboard</x-slot>

    <div class="max-w-7xl mx-auto space-y-8" x-data="{
        uploadModal: false,
        paymentId: '{{ $application?->payment?->id ?? '' }}',
        transactionRef: '{{ $application?->payment?->transaction_reference ?? '' }}',
        receiptFile: null,
        loading: false,

        handleFileSelect(event) {
            this.receiptFile = event.target.files[0];
        },

        submitReceipt() {
            if (!this.paymentId) {
                toast('No active payment control number found.', 'error');
                return;
            }
            if (!this.receiptFile && !'{{ $application?->payment?->receipt_path ?? '' }}') {
                toast('Tafadhali chagua faili la risiti.', 'error');
                return;
            }

            this.loading = true;
            let formData = new FormData();
            formData.append('payment_id', this.paymentId);
            if (this.receiptFile) {
                formData.append('receipt', this.receiptFile);
            }
            if (this.transactionRef) {
                formData.append('transaction_reference', this.transactionRef);
            }

            axios.post('{{ url('/applicant/submit-payment') }}', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            })
            .then(res => {
                this.loading = false;
                this.uploadModal = false;
                toast('Risiti ya malipo imewasilishwa kikamilifu na inakaguliwa!', 'success');
                setTimeout(() => window.location.reload(), 1500);
            })
            .catch(err => {
                this.loading = false;
                const msg = err.response?.data?.message || 'Kosa limetokea wakati wa kupakia risiti.';
                toast(msg, 'error');
            });
        }
    }">
        
        <!-- Welcome Card Banner -->
        <div class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-900 rounded-3xl p-8 sm:p-10 text-white shadow-xl flex flex-col md:flex-row justify-between items-center gap-6 border border-blue-950">
            <div class="space-y-2">
                <span class="px-3.5 py-1 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-400 text-xs font-extrabold uppercase tracking-wider">
                    Academic Year 2026/2027
                </span>
                <h2 class="text-3xl font-black tracking-tight">Welcome back, {{ $user->name }}!</h2>
                <p class="text-xs text-blue-100">Track your university admission application status and access official verification documents.</p>
            </div>

            <div>
                <a href="{{ route('applicant.wizard') }}" class="gradient-btn-gold px-8 py-4 rounded-2xl text-slate-950 font-extrabold text-sm shadow-md hover:scale-105 transition-transform inline-block">
                    {{ $application ? 'Manage Application' : 'Start Application' }} &rarr;
                </a>
            </div>
        </div>

        @if(isset($hasRejectedDocs) && $hasRejectedDocs)
            <!-- Rejected Documents Alert -->
            <div class="bg-red-50 border border-red-200 rounded-3xl p-6 sm:p-8 flex items-start gap-4 text-red-800 shadow-sm">
                <span class="text-3xl mt-0.5 shrink-0">⚠️</span>
                <div class="space-y-2 w-full">
                    <h4 class="font-black text-sm uppercase tracking-wider text-red-700">Action Required: Rejected Documents / Certificate(s)</h4>
                    <p class="text-xs font-semibold text-slate-700">The admission team has reviewed your documents and rejected the following file(s). You must re-upload them to proceed with your application:</p>
                    <ul class="list-disc pl-5 text-xs font-bold space-y-1.5 mt-2">
                        @foreach($rejectedDocs as $doc)
                            <li class="text-slate-800">
                                <span class="uppercase text-red-700">{{ str_replace('_', ' ', $doc->document_type) }}</span>: 
                                <span class="italic font-medium text-slate-600">"{{ $doc->rejection_comment ?: 'No reason provided' }}"</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="pt-3">
                        <a href="{{ route('applicant.wizard') }}?step=5" class="gradient-btn px-6 py-2.5 rounded-2xl text-white font-extrabold text-xs shadow-md inline-block hover:scale-105 transition-transform">
                            Re-upload Rejected Files (Step 5) &rarr;
                        </a>
                    </div>
                </div>
            </div>
        @endif

        @if($application)
            <!-- Application Progress Overview Widgets -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Application Control Number & Status -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-3 card-hover-effect">
                    <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest block">Application Control Number</span>
                    <span class="text-xl font-black text-blue-800 block">{{ $application->application_number }}</span>
                    
                    <div class="pt-3 flex items-center justify-between border-t border-slate-100">
                        <span class="text-xs text-slate-500 font-semibold">Review Status:</span>
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase {{ $application->status === 'Approved' ? 'bg-emerald-100 text-emerald-800' : ($application->status === 'Rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                            {{ $application->status }}
                        </span>
                    </div>
                </div>

                <!-- Programme & Category -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-3 card-hover-effect">
                    <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest block">Chosen Programme</span>
                    <span class="text-lg font-bold text-slate-900 block truncate">{{ $application->programme->name ?? 'N/A' }}</span>
                    
                    <div class="pt-3 flex items-center justify-between border-t border-slate-100">
                        <span class="text-xs text-slate-500 font-semibold">Category:</span>
                        <span class="text-xs font-bold text-slate-800">{{ $application->admission_category }}</span>
                    </div>
                </div>

                <!-- Payment Status Card -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-3 card-hover-effect">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-widest block">Application Fee Payment</span>
                            <span class="text-lg font-bold text-slate-900 block">
                                Control #: {{ $application->payment->control_number ?? 'Pending' }}
                            </span>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase {{ ($application->payment->payment_status ?? '') === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ strtoupper($application->payment->payment_status ?? 'pending') }}
                        </span>
                    </div>
                    
                    <div class="pt-3 flex items-center justify-between border-t border-slate-100">
                        <span class="text-xs text-slate-500 font-semibold">Payment Detection:</span>
                        <span class="text-xs font-bold {{ ($application->payment->payment_status ?? '') === 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">
                            {{ ($application->payment->payment_status ?? '') === 'paid' ? '✓ Automatic Verified' : '⏳ Auto-Detecting Online' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Progressive Save Checklist & Progress Bar -->
            @if(in_array($application->status, ['Draft', 'IN_PROGRESS', 'Pending Payment', 'PAYMENT_PENDING'], true))
                <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-sm">Progression Step Check-List</h3>
                            <p class="text-[10px] text-slate-500">Your application progress is saved progressively. You can resume at any time.</p>
                        </div>
                        <span class="px-2.5 py-1 bg-amber-500/10 text-amber-600 rounded-lg text-xs font-extrabold tracking-wider">{{ $application->completion_percentage }}% Completed</span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-slate-100 rounded-full h-2.5 shadow-inner">
                        <div class="bg-gradient-to-r from-amber-500 to-amber-600 h-2.5 rounded-full shadow" style="width: {{ $application->completion_percentage }}%"></div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 pt-2">
                        <!-- Step 1 -->
                        <div class="p-4 rounded-2xl border flex items-center space-x-3 {{ $application->current_step >= 2 ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-slate-50 border-slate-200 text-slate-500' }}">
                            <span class="text-lg font-black">{{ $application->current_step >= 2 ? '✓' : '○' }}</span>
                            <div class="text-xs">
                                <span class="font-black block">Step 1</span>
                                <span class="font-medium text-[10px] {{ $application->current_step >= 2 ? 'text-emerald-600' : 'text-slate-400' }}">Consent & Account Info</span>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="p-4 rounded-2xl border flex items-center space-x-3 {{ $application->current_step >= 3 ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-slate-50 border-slate-200 text-slate-500' }}">
                            <span class="text-lg font-black">{{ $application->current_step >= 3 ? '✓' : '○' }}</span>
                            <div class="text-xs">
                                <span class="font-black block">Step 2</span>
                                <span class="font-medium text-[10px] {{ $application->current_step >= 3 ? 'text-emerald-600' : 'text-slate-400' }}">Personal Details</span>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="p-4 rounded-2xl border flex items-center space-x-3 {{ $application->current_step >= 4 ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-slate-50 border-slate-200 text-slate-500' }}">
                            <span class="text-lg font-black">{{ $application->current_step >= 4 ? '✓' : '○' }}</span>
                            <div class="text-xs">
                                <span class="font-black block">Step 3</span>
                                <span class="font-medium text-[10px] {{ $application->current_step >= 4 ? 'text-emerald-600' : 'text-slate-400' }}">Academic Profile</span>
                            </div>
                        </div>

                        <!-- Step 4 -->
                        <div class="p-4 rounded-2xl border flex items-center space-x-3 {{ $application->current_step >= 5 ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-slate-50 border-slate-200 text-slate-500' }}">
                            <span class="text-lg font-black">{{ $application->current_step >= 5 ? '✓' : '○' }}</span>
                            <div class="text-xs">
                                <span class="font-black block">Step 4</span>
                                <span class="font-medium text-[10px] {{ $application->current_step >= 5 ? 'text-emerald-600' : 'text-slate-400' }}">Programme Selection</span>
                            </div>
                        </div>

                        <!-- Step 5 -->
                        <div class="p-4 rounded-2xl border flex items-center space-x-3 {{ $application->current_step >= 6 ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-slate-50 border-slate-200 text-slate-500' }}">
                            <span class="text-lg font-black">{{ $application->current_step >= 6 ? '✓' : '○' }}</span>
                            <div class="text-xs">
                                <span class="font-black block">Step 5</span>
                                <span class="font-medium text-[10px] {{ $application->current_step >= 6 ? 'text-emerald-600' : 'text-slate-400' }}">Admissions Fee Payment</span>
                            </div>
                        </div>

                        <!-- Step 6 -->
                        <div class="p-4 rounded-2xl border flex items-center space-x-3 {{ $application->current_step >= 7 ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-slate-50 border-slate-200 text-slate-500' }}">
                            <span class="text-lg font-black">{{ $application->current_step >= 7 ? '✓' : '○' }}</span>
                            <div class="text-xs">
                                <span class="font-black block">Step 6</span>
                                <span class="font-medium text-[10px] {{ $application->current_step >= 7 ? 'text-emerald-600' : 'text-slate-400' }}">Documents Upload</span>
                            </div>
                        </div>

                        <!-- Step 7 -->
                        <div class="p-4 rounded-2xl border flex items-center space-x-3 {{ $application->status === 'SUBMITTED' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-slate-50 border-slate-200 text-slate-500' }}">
                            <span class="text-lg font-black">{{ $application->status === 'SUBMITTED' ? '✓' : '○' }}</span>
                            <div class="text-xs">
                                <span class="font-black block">Step 7</span>
                                <span class="font-medium text-[10px] {{ $application->status === 'SUBMITTED' ? 'text-emerald-600' : 'text-slate-400' }}">Review & Signature</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-3">
                        <a href="{{ route('applicant.wizard') }}?step={{ $application->current_step }}" class="gradient-btn-gold px-8 py-3.5 rounded-2xl text-slate-950 font-black text-xs tracking-wider shadow-md hover:scale-105 transition-transform inline-block">
                            Fungua Hatua ya {{ $application->current_step }} (Continue Application Step {{ $application->current_step }}) &rarr;
                        </a>
                    </div>
                </div>
            @endif

            <!-- Automatic Payment Status / Action Banner Card -->
            @if(($application->payment->payment_status ?? '') !== 'paid')
                <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-blue-900 p-6 sm:p-8 rounded-3xl border border-blue-950 text-white flex flex-col sm:flex-row items-center justify-between gap-4 shadow-xl">
                    <div class="space-y-1 text-center sm:text-left">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-400">Ada ya Fomu ya Maombi</span>
                        <h3 class="text-xl font-extrabold">Malipo ya Ada ya Maombi (TZS 20,000/=)</h3>
                        <p class="text-xs text-blue-100">Lipa ada yako kwa Control Number: <strong>{{ $application->payment->control_number ?? '---' }}</strong>. Mfumo utatambua malipo yako kiotomatiki bila kuhitaji kupakia risiti.</p>
                    </div>
                    <a href="{{ route('applicant.wizard') }}" class="gradient-btn-gold px-8 py-3.5 rounded-2xl text-slate-950 font-black text-xs shadow-md hover:scale-105 transition-transform shrink-0 inline-block">
                        Fungua Hatua ya Malipo &rarr;
                    </a>
                </div>
            @endif

            <!-- Admission Letter Hub Card -->
            @if($application->admissionLetter)
                <div class="bg-emerald-50 border-2 border-emerald-100 p-8 rounded-3xl space-y-4 shadow-sm">
                    <div class="flex items-center space-x-3 text-emerald-600">
                        <svg class="w-8 h-8 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <h3 class="text-2xl font-black text-emerald-950">Congratulations! Official Admission Granted</h3>
                    </div>
                    <p class="text-xs text-slate-700">Your admission letter has been generated with official QR verification code. Admission Number: <strong>{{ $application->admissionLetter->admission_number }}</strong></p>
                    
                    <a href="{{ route('api.admission-letter.download', ['verificationCode' => $application->admissionLetter->verification_code]) }}" target="_blank" class="gradient-btn-gold px-8 py-3.5 rounded-2xl text-slate-950 font-black text-xs shadow-md inline-block hover:scale-105 transition-transform">
                        Download Official Admission Letter (PDF) &rarr;
                    </a>
                </div>
            @endif

        @else
            <!-- Empty Application State -->
            <div class="bg-white p-12 rounded-3xl border border-slate-200 text-center space-y-4 shadow-sm">
                <div class="w-16 h-16 rounded-2xl bg-amber-500/10 text-amber-500 flex items-center justify-center mx-auto text-2xl font-bold">!</div>
                <h3 class="text-xl font-bold text-slate-900">No Application Started Yet</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto">Complete your 8-step application to get admitted into SUPA / OUT University for the 2026/2027 Academic Year.</p>
                
                <a href="{{ route('applicant.wizard') }}" class="gradient-btn-gold px-8 py-3.5 rounded-2xl text-slate-950 font-extrabold text-sm shadow-md inline-block hover:scale-105 transition-transform">
                    Start Application Now &rarr;
                </a>
            </div>
        @endif

    </div>
</x-app-layout>
