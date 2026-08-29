<x-app-layout title="Application 360° Review - Superadmin Desk">
    <x-slot name="header">Applicant 360° Profile & Document Verification Desk</x-slot>

    <div class="w-full space-y-8" x-data="{
        decisionModal: false,
        decisionType: 'approve',
        reason: '',
        loading: false,

        docRejectModal: false,
        activeDocId: null,
        docRejectionComment: '',

        docPreviewModal: false,
        activeDoc: { title: '', url: '', name: '', mime: '' },

        previewDocument(title, url, name, mime) {
            this.activeDoc = { title: title, url: url, name: name, mime: mime };
            this.docPreviewModal = true;
        },

        verifyDocument(docId, status) {
            if (status === 'rejected') {
                this.activeDocId = docId;
                this.docRejectModal = true;
                return;
            }
            
            axios.post('{{ url('/api/v1/admin/documents') }}/' + docId + '/verify', {
                status: 'verified'
            })
            .then(res => {
                toast('Document verified successfully!', 'success');
                setTimeout(() => window.location.reload(), 1000);
            })
            .catch(err => {
                toast(err.response?.data?.message || 'Error verifying document', 'error');
            });
        },

        confirmDocRejection() {
            if (!this.activeDocId) return;
            axios.post('{{ url('/api/v1/admin/documents') }}/' + this.activeDocId + '/verify', {
                status: 'rejected',
                rejection_comment: this.docRejectionComment
            })
            .then(res => {
                toast('Document marked as rejected.', 'info');
                this.docRejectModal = false;
                setTimeout(() => window.location.reload(), 1000);
            })
            .catch(err => {
                toast(err.response?.data?.message || 'Error updating document status', 'error');
            });
        },

        makeDecision() {
            this.loading = true;
            axios.post('{{ url('/api/v1/admin/applications/' . $application->id . '/decision') }}', {
                decision: this.decisionType,
                reason: this.reason
            })
            .then(res => {
                this.loading = false;
                toast('Admission decision recorded successfully!', 'success');
                window.location.reload();
            })
            .catch(err => {
                this.loading = false;
                toast(err.response?.data?.message || 'Error recording decision', 'error');
            });
        },

        syncingSingida: false,
        syncToSingida() {
            this.syncingSingida = true;
            axios.post('{{ url('/api/v1/admin/applications/' . $application->id . '/sync-singida') }}', { force: 1 })
            .then(res => {
                this.syncingSingida = false;
                toast(res.data.message || 'Synced to Singida.', 'success');
                setTimeout(() => window.location.reload(), 800);
            })
            .catch(err => {
                this.syncingSingida = false;
                toast(err.response?.data?.message || 'Failed to sync with Singida.', 'error');
            });
        },

        verifyingPayment: false,
        verifyPayment(paymentId, status) {
            this.verifyingPayment = true;
            axios.post('{{ url('/api/v1/admin/payments') }}/' + paymentId + '/verify', {
                status: status
            })
            .then(res => {
                this.verifyingPayment = false;
                toast('Payment marked as ' + status.toUpperCase() + '!', 'success');
                setTimeout(() => window.location.reload(), 800);
            })
            .catch(err => {
                this.verifyingPayment = false;
                toast(err.response?.data?.message || 'Error verifying payment', 'error');
            });
        }
    }">

        <!-- Top Navigation Bar -->
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.applications.index') }}" class="text-xs font-extrabold text-blue-500 hover:text-amber-400 flex items-center gap-2 transition-colors">
                &larr; Back to Applications Directory
            </a>
            <span class="text-xs font-bold text-slate-500">
                Submitted At: {{ $application->submitted_at ? $application->submitted_at->format('d M Y, H:i A') : 'Draft Application' }}
            </span>
        </div>
        
        <!-- Header Applicant Profile Card -->
        <div class="bg-gradient-to-r from-slate-950 via-blue-950 to-slate-900 rounded-3xl p-8 border border-slate-200 shadow-2xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6 text-white">
            <div class="flex items-center space-x-6">
                <!-- Passport Photo -->
                <div class="w-20 h-20 rounded-2xl overflow-hidden border-2 border-amber-500/60 shadow-xl shrink-0 bg-white flex items-center justify-center">
                    @if($application->applicant && $application->applicant->passport_photo_path)
                        <img src="{{ asset('storage/' . $application->applicant->passport_photo_path) }}" alt="Passport Photo" class="w-full h-full object-cover">
                    @else
                        <span class="text-2xl font-black text-amber-400">{{ strtoupper(substr($application->applicant->user->name ?? 'A', 0, 2)) }}</span>
                    @endif
                </div>

                <div class="space-y-1.5">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-3.5 py-1 rounded-full text-xs font-black uppercase bg-blue-500/20 border border-blue-500/40 text-blue-300">
                            {{ $application->application_number }}
                        </span>
                        <span class="px-3.5 py-1 rounded-full text-xs font-black uppercase {{ $application->status === 'Approved' ? 'bg-emerald-500/20 border border-emerald-500/40 text-emerald-400' : ($application->status === 'Rejected' ? 'bg-red-500/20 border border-red-500/40 text-red-400' : 'bg-amber-500/20 border border-amber-500/40 text-amber-400') }}">
                            {{ strtoupper($application->status) }}
                        </span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-white">{{ $application->applicant->user->name ?? 'N/A' }}</h2>
                    <p class="text-xs text-slate-700 font-semibold">
                        Chosen Programme: <strong class="text-amber-400">{{ $application->programme->name ?? 'N/A' }} ({{ $application->programme->code ?? 'N/A' }})</strong>
                    </p>
                </div>
            </div>

            <!-- Decision Action Buttons -->
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <button @click="decisionType = 'approve'; decisionModal = true" class="px-5 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs shadow-xl transition-all hover:scale-105 flex items-center gap-1.5">
                    ✓ Approve & Issue Admission
                </button>
                <button @click="decisionType = 'recommend_foundation'; decisionModal = true" class="px-5 py-3 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs shadow-xl transition-all hover:scale-105 flex items-center gap-1.5">
                    🎓 Recommend Foundation
                </button>
                <button @click="decisionType = 'reject'; decisionModal = true" class="px-5 py-3 rounded-2xl bg-red-600 hover:bg-red-500 text-white font-extrabold text-xs shadow-xl transition-all hover:scale-105 flex items-center gap-1.5">
                    ✕ Reject Application
                </button>
            </div>
        </div>

        <!-- Academic & Personal Details Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- Personal Bio & Contact Card -->
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-md space-y-5">
                <h3 class="font-extrabold text-slate-900 text-base border-b border-slate-100 pb-3 flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <span class="text-blue-500">👤</span> Personal Bio & Contact Info
                    </span>
                    <span class="text-xs font-mono font-bold text-slate-500">Applicant ID: {{ $application->applicant->id ?? 'N/A' }}</span>
                </h3>
                
                <div class="space-y-3.5 text-xs">
                    <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                        <span class="text-slate-500 font-bold">Full Name:</span>
                        <strong class="text-slate-900 text-sm font-extrabold">{{ $application->applicant->user->name ?? 'N/A' }}</strong>
                    </div>

                    <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                        <span class="text-slate-500 font-bold">Gender:</span>
                        <strong class="text-slate-900 font-bold">{{ ucfirst($application->applicant->gender ?? 'N/A') }}</strong>
                    </div>

                    <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                        <span class="text-slate-500 font-bold">Date of Birth:</span>
                        <strong class="text-slate-900 font-bold">{{ optional($application->applicant->date_of_birth)->format('d M Y') ?? 'N/A' }}</strong>
                    </div>

                    <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                        <span class="text-slate-500 font-bold">Kitambulisho cha NIDA:</span>
                        <strong class="text-slate-900 font-mono font-extrabold text-xs tracking-wider">{{ $application->applicant->nida_number ?: ($application->applicant->nida_card_number ?? 'N/A') }}</strong>
                    </div>

                    <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                        <span class="text-slate-500 font-bold">Kitambulisho cha Kura:</span>
                        <strong class="text-slate-900 font-mono font-extrabold text-xs tracking-wider">{{ $application->applicant->voter_id_number ?? 'N/A' }}</strong>
                    </div>

                    <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                        <span class="text-slate-500 font-bold">Kitambulisho cha Kazi:</span>
                        <strong class="text-slate-900 font-mono font-extrabold text-xs tracking-wider">{{ $application->applicant->work_id_number ?? 'N/A' }}</strong>
                    </div>

                    <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                        <span class="text-slate-500 font-bold">Nationality:</span>
                        <strong class="text-slate-900 font-bold">{{ $application->applicant->nationality ?? 'Tanzanian' }}</strong>
                    </div>

                    <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                        <span class="text-slate-500 font-bold">Email Address:</span>
                        <strong class="text-blue-600 font-extrabold">{{ $application->applicant->user->email ?? 'N/A' }}</strong>
                    </div>

                    <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                        <span class="text-slate-500 font-bold">Phone Number:</span>
                        <strong class="text-slate-900 font-extrabold">{{ $application->applicant->user->phone ?? 'N/A' }}</strong>
                    </div>

                    <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                        <span class="text-slate-500 font-bold">WhatsApp Number:</span>
                        <strong class="text-emerald-600 font-extrabold">{{ $application->applicant->whatsapp_number ?? 'N/A' }}</strong>
                    </div>

                    <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                        <span class="text-slate-500 font-bold">Region / District / Ward:</span>
                        <strong class="text-slate-900 font-bold">{{ $application->applicant->region ?? 'N/A' }}, {{ $application->applicant->district ?? 'N/A' }} ({{ $application->applicant->ward ?? 'N/A' }})</strong>
                    </div>
                    
                    <div class="pt-2">
                        <span class="text-slate-500 font-extrabold uppercase text-[10px] tracking-wider block mb-1.5">Parent / Guardian Contact Details:</span>
                        <div class="p-4 rounded-2xl bg-white border border-slate-200 text-slate-900 font-semibold space-y-1">
                            <p class="flex justify-between">
                                <span class="text-slate-500">Name:</span>
                                <strong>{{ $application->applicant->next_of_kin_name ?? 'N/A' }}</strong>
                            </p>
                            <p class="flex justify-between">
                                <span class="text-slate-500">Phone:</span>
                                <strong>{{ $application->applicant->next_of_kin_phone ?? 'N/A' }}</strong> ({{ $application->applicant->next_of_kin_relation ?? 'N/A' }})
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Qualifications & Calculated Category Card -->
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-md space-y-5">
                <h3 class="font-extrabold text-slate-900 text-base border-b border-slate-100 pb-3 flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <span class="text-amber-500">🎓</span> Academic Qualifications & Category
                    </span>
                    <span class="px-3.5 py-1 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-500 font-black text-xs">
                        {{ $application->admission_category }}
                    </span>
                </h3>

                <div class="space-y-4 text-xs">
                    <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                        <span class="text-slate-500 font-bold">Admission Entry Mode:</span>
                        <strong class="text-slate-900 font-extrabold text-sm">{{ $application->academicProfile->admission_type ?? $application->admission_type }}</strong>
                    </div>
                    
                    @if(($application->academicProfile->admission_type ?? $application->admission_type) === 'Diploma')
                        <!-- DIPLOMA DETAILS BOX -->
                        <div class="p-5 rounded-2xl bg-blue-950/30 border border-blue-800/40 shadow-inner space-y-2.5">
                            <span class="font-black text-blue-400 uppercase text-[10px] tracking-wider block border-b border-blue-800/40 pb-1.5">
                                STASHAHADA (DIPLOMA) DETAILS
                            </span>
                            <div class="flex justify-between text-slate-700">
                                <span class="text-slate-500 font-bold">Chuo Ulichohitimu:</span>
                                <strong class="text-white font-extrabold">{{ $application->academicProfile->college_name ?? 'N/A' }}</strong>
                            </div>
                            <div class="flex justify-between text-slate-700">
                                <span class="text-slate-500 font-bold">Jina la Kozi:</span>
                                <strong class="text-white font-extrabold">{{ $application->academicProfile->diploma_programme_name ?? 'N/A' }}</strong>
                            </div>
                            <div class="flex justify-between text-slate-700">
                                <span class="text-slate-500 font-bold">Namba ya Usajili / Mtihani:</span>
                                <strong class="text-amber-400 font-mono font-extrabold">{{ $application->academicProfile->diploma_registration_number ?? 'N/A' }}</strong>
                            </div>
                            <div class="flex justify-between text-slate-700">
                                <span class="text-slate-500 font-bold">Mwaka wa Kuhitimu:</span>
                                <strong class="text-white font-extrabold">{{ $application->academicProfile->diploma_graduation_year ?? 'N/A' }}</strong>
                            </div>
                            <div class="flex justify-between items-center text-sm pt-2 border-t border-blue-800/40">
                                <span class="text-white font-extrabold">Kiwango cha GPA:</span>
                                <strong class="text-amber-400 font-black text-xl tracking-wider">{{ number_format($application->academicProfile->gpa ?? 0, 2) }}</strong>
                            </div>
                        </div>
                    @else
                        <!-- FORM SIX DETAILS BOX -->
                        <div class="p-5 rounded-2xl bg-purple-950/30 border border-purple-800/40 shadow-inner space-y-2.5">
                            <span class="font-black text-purple-400 uppercase text-[10px] tracking-wider block border-b border-purple-800/40 pb-1.5">
                                FORM SIX (ACSEE) DETAILS
                            </span>
                            <div class="flex justify-between text-slate-700">
                                <span class="text-slate-500 font-bold">Form IV CSEE Index #:</span>
                                <strong class="text-white font-mono font-bold">{{ $application->academicProfile->csee_number ?? 'N/A' }}</strong>
                            </div>
                            <div class="flex justify-between text-slate-700">
                                <span class="text-slate-500 font-bold">Form IV School / Year:</span>
                                <strong class="text-white font-bold">{{ $application->academicProfile->csee_school ?? 'N/A' }} ({{ $application->academicProfile->csee_year ?? 'N/A' }})</strong>
                            </div>
                            <div class="flex justify-between text-slate-700">
                                <span class="text-slate-500 font-bold">Form VI ACSEE Index #:</span>
                                <strong class="text-amber-400 font-mono font-bold">{{ $application->academicProfile->acsee_number ?? 'N/A' }}</strong>
                            </div>
                            <div class="flex justify-between text-slate-700">
                                <span class="text-slate-500 font-bold">Form VI School / Year:</span>
                                <strong class="text-white font-bold">{{ $application->academicProfile->acsee_school ?? 'N/A' }} ({{ $application->academicProfile->acsee_year ?? 'N/A' }})</strong>
                            </div>
                            <div class="flex justify-between text-slate-700">
                                <span class="text-slate-500 font-bold">Combination:</span>
                                <strong class="text-white font-extrabold">{{ $application->academicProfile->acsee_combination ?? 'N/A' }}</strong>
                            </div>
                            @if($application->academicProfile && $application->academicProfile->acsee_subject1)
                                <div class="border-t border-purple-800/20 pt-2 space-y-1">
                                    <span class="text-[10px] font-black text-purple-400 uppercase tracking-wider block">Principal Grades</span>
                                    <div class="flex justify-between text-xs text-slate-300">
                                        <span>{{ $application->academicProfile->acsee_subject1 }}:</span>
                                        <strong class="text-white">{{ $application->academicProfile->acsee_grade1 ?? 'N/A' }}</strong>
                                    </div>
                                    <div class="flex justify-between text-xs text-slate-300">
                                        <span>{{ $application->academicProfile->acsee_subject2 }}:</span>
                                        <strong class="text-white">{{ $application->academicProfile->acsee_grade2 ?? 'N/A' }}</strong>
                                    </div>
                                    <div class="flex justify-between text-xs text-slate-300">
                                        <span>{{ $application->academicProfile->acsee_subject3 }}:</span>
                                        <strong class="text-white">{{ $application->academicProfile->acsee_grade3 ?? 'N/A' }}</strong>
                                    </div>
                                </div>
                            @endif
                            @if($application->academicProfile && $application->academicProfile->acsee_gs_grade)
                                <div class="flex justify-between text-xs text-slate-300 border-t border-purple-800/20 pt-2">
                                    <span>General Studies (GS):</span>
                                    <strong class="text-white font-extrabold">{{ $application->academicProfile->acsee_gs_grade }}</strong>
                                </div>
                            @endif
                            <div class="flex justify-between items-center text-sm pt-2 border-t border-purple-800/40">
                                <span class="text-white font-extrabold">ACSEE Points Total:</span>
                                <strong class="text-purple-400 font-black text-xl tracking-wider">{{ $application->academicProfile->acsee_points ?? 0 }} Points</strong>
                            </div>
                        </div>
                    @endif

                    <div class="p-5 rounded-2xl bg-white/80 border border-slate-200 space-y-1.5 text-white">
                        <span class="text-[10px] font-extrabold uppercase text-amber-400 block tracking-wider">KUNDI LA UDAHILI LILILOKOKOTOLEWA (CALCULATED ADMISSION CATEGORY):</span>
                        <strong class="text-xl text-white font-black block tracking-tight">{{ $application->admission_category }}</strong>
                        <p class="text-[11px] text-slate-700 leading-relaxed">
                            {{ $application->admission_category === 'Direct Entry' ? 'Ana sifa za kudahiliwa moja kwa moja kwenye Shahada ya Kwanza (OUT).' : 'Anastahili Foundation Programme kupitia SUPA (STTC & OUT).' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- UPLOADED DOCUMENTS INSPECTION & VERIFICATION DESK -->
        <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-md space-y-6">
            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 border-b border-slate-100 pb-4">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-lg flex items-center gap-2">
                        <span>📁 Uploaded Documents & Certificates Hub</span>
                        <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-500/30 text-blue-600 text-xs font-black">
                            {{ $application->documents->count() }} Files Uploaded
                        </span>
                    </h3>
                    <p class="text-xs text-slate-500">Inspect original applicant file uploads, verify authenticity, and approve/reject individual documents.</p>
                </div>
            </div>

            @if($application->documents->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($application->documents as $doc)
                        <div class="p-6 rounded-2xl bg-white border border-slate-200 space-y-4 card-hover-effect">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-600/10 text-blue-600 flex items-center justify-center font-black text-xs uppercase shrink-0">
                                        {{ Str::contains($doc->mime_type ?? '', 'pdf') ? 'PDF' : 'IMG' }}
                                    </div>
                                    <div class="overflow-hidden">
                                        <h4 class="font-extrabold text-slate-900 text-sm truncate uppercase">{{ str_replace('_', ' ', $doc->document_type) }}</h4>
                                        <span class="text-[10px] text-slate-500 font-semibold block truncate" title="{{ $doc->original_filename }}">{{ $doc->original_filename }}</span>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase shrink-0 {{ $doc->verification_status === 'verified' ? 'bg-emerald-100 text-emerald-800' : ($doc->verification_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ $doc->verification_status }}
                                </span>
                            </div>

                            <div class="text-[11px] text-slate-500 space-y-1">
                                <p class="flex justify-between"><span>File Size:</span> <strong class="text-slate-900">{{ round(($doc->file_size_bytes ?? 0) / 1024, 1) }} KB</strong></p>
                                <p class="flex justify-between"><span>Uploaded:</span> <strong class="text-slate-900">{{ $doc->created_at ? $doc->created_at->format('d M Y, H:i') : 'N/A' }}</strong></p>
                                @if($doc->rejection_comment)
                                    <p class="text-red-500 font-bold pt-1">Reason: {{ $doc->rejection_comment }}</p>
                                @endif
                            </div>

                            <!-- Document Verification Controls -->
                            <div class="pt-2 border-t border-slate-200 flex items-center justify-between gap-2">
                                <button type="button" @click="previewDocument('{{ strtoupper(str_replace('_', ' ', $doc->document_type)) }}', '{{ asset('storage/' . $doc->file_path) }}', '{{ $doc->original_filename }}', '{{ $doc->mime_type }}')" 
                                        class="px-3 py-1.5 rounded-xl bg-slate-200 hover:bg-amber-500 hover:text-slate-950 font-extrabold text-[10px] transition-colors flex items-center gap-1">
                                    🔍 Preview / View
                                </button>
                                
                                <div class="flex items-center space-x-1">
                                    <button type="button" @click="verifyDocument({{ $doc->id }}, 'verified')" title="Approve Document" class="p-1.5 rounded-xl bg-emerald-100 hover:bg-emerald-600 hover:text-white text-emerald-800 font-extrabold text-[10px] transition-colors">
                                        ✓ Verify
                                    </button>
                                    <button type="button" @click="verifyDocument({{ $doc->id }}, 'rejected')" title="Reject Document" class="p-1.5 rounded-xl bg-red-100 hover:bg-red-600 hover:text-white text-red-800 font-extrabold text-[10px] transition-colors">
                                        ✕ Reject
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 rounded-2xl bg-slate-50 text-center space-y-2">
                    <span class="text-2xl">⚠️</span>
                    <p class="text-xs font-bold text-slate-500">No application documents attached yet for this applicant.</p>
                </div>
            @endif
        </div>

        <!-- PAYMENT SLIP & CONTROL NUMBER VERIFICATION CARD -->
        <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-md space-y-4">
            <h3 class="font-extrabold text-slate-900 text-base border-b border-slate-100 pb-3 flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <span class="text-emerald-500">💳</span> Application Fee Payment Verification
                </span>
                <span class="px-3.5 py-1 rounded-full text-xs font-black uppercase {{ ($application->payment->payment_status ?? '') === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ strtoupper($application->payment->payment_status ?? 'Pending') }}
                </span>
            </h3>

            @if($application->payment)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                    <div class="space-y-3">
                        <p class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-500 font-bold">NMB Control Number:</span> <strong class="text-slate-900 font-mono text-sm font-extrabold">{{ $application->payment->control_number }}</strong></p>
                        <p class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-500 font-bold">Singida Sync:</span>
                            <strong class="{{ ($application->payment->singida_synced ?? false) ? 'text-emerald-600' : 'text-amber-600' }} font-extrabold">
                                {{ ($application->payment->singida_synced ?? false) ? 'Synced' : 'Not synced' }}
                                @if($application->singida_admission_id)
                                    (#{{ $application->singida_admission_id }})
                                @endif
                            </strong>
                        </p>
                        <p class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-500 font-bold">Amount Required:</span> <strong class="text-amber-500 font-black text-sm">TZS {{ number_format($application->payment->amount) }}</strong></p>
                        <p class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-500 font-bold">Transaction Reference #:</span> <strong class="text-slate-900 font-mono font-extrabold">{{ $application->payment->transaction_reference ?? 'Not Provided' }}</strong></p>
                        <button type="button" @click="syncToSingida()" :disabled="syncingSingida"
                                class="mt-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md disabled:opacity-60">
                            <span x-show="!syncingSingida">Send to Singida / Get Control Number</span>
                            <span x-show="syncingSingida">Syncing with Singida...</span>
                        </button>
                    </div>

                    <div class="p-5 rounded-2xl bg-white border border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div>
                            <span class="text-xs font-extrabold text-slate-900 block">Risiti ya Malipo (Uploaded Receipt Slip)</span>
                            @if($application->payment->receipt_path)
                                <a href="{{ asset('storage/' . $application->payment->receipt_path) }}" target="_blank" class="text-xs text-blue-500 hover:underline font-bold block mt-1">
                                    📄 Fungua / Pakua Faili la Risiti (View Payment Receipt File)
                                </a>
                            @else
                                <span class="text-xs text-amber-500 font-bold block mt-1">⚠️ Risiti haijapakiwa bado</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            @if($application->payment->receipt_path)
                                <button type="button" @click="previewDocument('Risiti ya Malipo (TZS 20,000)', '{{ asset('storage/' . $application->payment->receipt_path) }}', 'Payment_Receipt', 'image/png')" 
                                        class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md shrink-0">
                                    Preview Slip
                                </button>
                            @endif

                            @if($application->payment->payment_status !== 'paid')
                                <button type="button" @click="verifyPayment({{ $application->payment->id }}, 'paid')" :disabled="verifyingPayment"
                                        class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md shrink-0 disabled:opacity-60">
                                    <span x-show="!verifyingPayment">✓ Verify Paid (Allow Certificates)</span>
                                    <span x-show="verifyingPayment">Verifying...</span>
                                </button>
                            @else
                                <span class="px-4 py-2 rounded-xl bg-emerald-100 text-emerald-800 font-extrabold text-xs">
                                    ✓ Payment Verified
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="flex items-center justify-between gap-4">
                    <p class="text-xs text-slate-500">No payment control record generated yet for this application.</p>
                    <button type="button" @click="syncToSingida()" :disabled="syncingSingida"
                            class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md disabled:opacity-60">
                        <span x-show="!syncingSingida">Send to Singida / Get Control Number</span>
                        <span x-show="syncingSingida">Syncing with Singida...</span>
                    </button>
                </div>
            @endif
        </div>

        <!-- DIGITAL SIGNATURE & DECLARATION BOX -->
        <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-md space-y-4">
            <h3 class="font-extrabold text-slate-900 text-base border-b border-slate-100 pb-3 flex items-center gap-2">
                <span>✍️ Applicant Digital Signature & Declaration</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-2">
                    <span class="font-bold text-slate-500 uppercase text-[10px] block">TAMKO LA MWOMBAJI (DECLARATION TEXT)</span>
                    <p class="text-slate-700 leading-relaxed italic">
                        "Mimi {{ $application->applicant->user->name ?? 'Mwombaji' }} nathibitisha kuwa taarifa zote nilizotoa katika fomu hii ni za kweli na sahihi."
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-2">
                    <span class="font-bold text-slate-500 uppercase text-[10px] block">DIGITAL SIGNATURE STAMP</span>
                    @if($application->digital_signature_path)
                        @if(Str::startsWith($application->digital_signature_path, 'signatures/'))
                            <img src="{{ asset('storage/' . $application->digital_signature_path) }}" alt="Digital Signature" class="h-12 object-contain">
                        @else
                            <strong class="text-lg font-serif italic text-blue-600 block">{{ $application->digital_signature_path }}</strong>
                        @endif
                    @else
                        <span class="text-slate-500 italic">No digital signature stamp saved.</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- APPLICATION ACTIVITY TIMELINE TRAIL -->
        <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-md space-y-6">
            <h3 class="font-extrabold text-slate-900 text-base border-b border-slate-100 pb-3 flex items-center gap-2">
                <span>⏳ Application Activity History & Event Trail</span>
            </h3>

            @if($application->activities->count() > 0)
                <div class="flow-root text-xs">
                    <ul role="list" class="-mb-8">
                        @foreach($application->activities as $idx => $activity)
                            <li>
                                <div class="relative pb-8">
                                    @if($idx !== $application->activities->count() - 1)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-blue-100 border border-blue-200 text-blue-600 flex items-center justify-center font-bold text-xs ring-8 ring-white shrink-0">
                                                ⚡
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-xs font-extrabold text-slate-950">
                                                    [{{ strtoupper($activity->action) }}] <span class="font-medium text-slate-700">{{ $activity->description }}</span>
                                                </p>
                                            </div>
                                            <div class="text-right text-[10px] whitespace-nowrap text-slate-500 font-bold">
                                                <time datetime="{{ $activity->created_at->toIso8601String() }}">{{ $activity->created_at->format('d M Y, h:i A') }} ({{ $activity->created_at->diffForHumans() }})</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <div class="p-6 rounded-2xl bg-slate-50 text-center space-y-1.5">
                    <p class="text-xs font-bold text-slate-500">No progressive activity events recorded for this application yet.</p>
                </div>
            @endif
        </div>

        <!-- DOCUMENT REJECTION MODAL -->
        <div x-show="docRejectModal" class="fixed inset-0 bg-white/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white max-w-md w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-4">
                <h3 class="text-base font-extrabold text-slate-900">Reject Document Reason</h3>
                <p class="text-xs text-slate-500">Specify why this document is invalid or requires re-uploading by the applicant.</p>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">Rejection Comment</label>
                    <textarea x-model="docRejectionComment" rows="3" placeholder="e.g. Document image is blurry or certificate name does not match..." 
                              class="w-full p-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-semibold outline-none focus:ring-2 focus:ring-red-500"></textarea>
                </div>

                <div class="flex justify-end space-x-3 pt-2">
                    <button type="button" @click="docRejectModal = false" class="px-5 py-2.5 rounded-2xl bg-slate-200 text-xs font-extrabold">Cancel</button>
                    <button type="button" @click="confirmDocRejection()" class="px-6 py-2.5 rounded-2xl bg-red-600 hover:bg-red-700 text-white font-extrabold text-xs shadow-md">Confirm Rejection</button>
                </div>
            </div>
        </div>

        <!-- DOCUMENT PREVIEW MODAL -->
        <div x-show="docPreviewModal" class="fixed inset-0 bg-white/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white max-w-4xl w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <h3 class="text-base font-extrabold text-slate-900" x-text="activeDoc.title + ' — Preview'"></h3>
                    <button type="button" @click="docPreviewModal = false" class="text-slate-500 hover:text-white font-black text-sm">✕</button>
                </div>

                <div class="p-4 rounded-2xl bg-slate-100 min-h-[400px] flex items-center justify-center">
                    <template x-if="activeDoc.url && (activeDoc.mime.includes('image') || activeDoc.url.match(/\.(jpg|jpeg|png|webp|gif)$/i))">
                        <img :src="activeDoc.url" alt="Document Preview" class="max-h-[600px] w-auto object-contain rounded-xl shadow-lg border border-slate-300 mx-auto">
                    </template>

                    <template x-if="activeDoc.url && activeDoc.mime.includes('pdf')">
                        <iframe :src="activeDoc.url" class="w-full h-[600px] rounded-xl border border-slate-300"></iframe>
                    </template>
                </div>

                <div class="flex justify-between items-center pt-2">
                    <a :href="activeDoc.url" target="_blank" download class="px-5 py-2.5 rounded-2xl bg-slate-200 text-xs font-bold text-slate-800">
                        ⬇️ Download Original File
                    </a>
                    <button type="button" @click="docPreviewModal = false" class="gradient-btn px-6 py-2.5 rounded-2xl text-white font-extrabold text-xs shadow-md">
                        Close Preview
                    </button>
                </div>
            </div>
        </div>

        <!-- ADMISSION DECISION MODAL -->
        <div x-show="decisionModal" class="fixed inset-0 bg-white/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
            <div class="bg-white max-w-md w-full p-8 rounded-3xl shadow-2xl border border-slate-200 space-y-4">
                <h3 class="text-lg font-extrabold text-slate-900">Confirm Admission Decision</h3>
                
                <p class="text-xs text-slate-500 font-bold">Action Decision: <strong class="uppercase text-amber-500" x-text="decisionType"></strong></p>

                <div>
                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-2">Staff Comments / Decision Reason</label>
                    <textarea x-model="reason" rows="3" placeholder="Provide notes..." class="w-full p-3.5 rounded-2xl border border-slate-300 bg-slate-50 text-xs font-semibold outline-none focus:ring-2 focus:ring-amber-500"></textarea>
                </div>

                <div class="flex justify-end space-x-3 pt-2">
                    <button @click="decisionModal = false" class="px-5 py-2.5 rounded-2xl bg-slate-200 text-xs font-extrabold">Cancel</button>
                    <button @click="makeDecision()" :disabled="loading" class="gradient-btn px-6 py-2.5 rounded-2xl text-white font-extrabold text-xs shadow-md">Confirm Decision</button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
