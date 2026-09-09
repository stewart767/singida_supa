<x-app-layout title="Edit Student Application - {{ $application->application_number }}">
    <x-slot name="header">Edit Student Profile & Application Details</x-slot>

    @php
        $academic = $application->academicProfile;
        $applicant = $application->applicant;
        $user = $applicant?->user;
    @endphp

    <div class="w-full space-y-8" x-data="editStudentData()">

        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <a href="{{ route('admin.applications.show', $application->id) }}" class="text-xs font-extrabold text-blue-600 hover:text-amber-500 flex items-center gap-2 transition-colors">
                &larr; Back to Applicant 360° Review Desk
            </a>
            <div class="flex items-center gap-2">
                <span class="px-3.5 py-1 rounded-full text-xs font-black uppercase bg-blue-100 text-blue-800 border border-blue-200">
                    {{ $application->application_number }}
                </span>
                <span class="px-3.5 py-1 rounded-full text-xs font-black uppercase {{ $application->status === 'Approved' ? 'bg-emerald-100 text-emerald-800' : ($application->status === 'Rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                    Status: {{ strtoupper($application->status) }}
                </span>
            </div>
        </div>

        <!-- Validation Errors Alert -->
        @if ($errors->any())
            <div class="p-5 rounded-2xl bg-red-50 border border-red-200 text-red-800 shadow-sm">
                <div class="flex items-center gap-3 font-black text-sm mb-2">
                    <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Please correct the following errors:
                </div>
                <ul class="list-disc pl-6 text-xs space-y-1 font-semibold">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold text-sm shadow-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Main Edit Form -->
        <form action="{{ route('admin.applications.update', $application->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Section 1: Personal & Bio Information -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-black">
                            1
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-900">Personal & Bio Information</h2>
                            <p class="text-xs text-slate-500 font-medium">Full student demographic and identity credentials</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- Full Name -->
                    <div class="space-y-1.5 md:col-span-2">
                        <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Full Name (Jina Kamili) <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required
                               class="w-full px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Gender -->
                    <div class="space-y-1.5">
                        <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Gender (Jinsia) <span class="text-red-500">*</span></label>
                        <select name="gender" required class="w-full px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900 outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="male" {{ old('gender', $applicant->gender ?? '') === 'male' ? 'selected' : '' }}>Male (Mwanaume)</option>
                            <option value="female" {{ old('gender', $applicant->gender ?? '') === 'female' ? 'selected' : '' }}>Female (Mwanamke)</option>
                            <option value="other" {{ old('gender', $applicant->gender ?? '') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <!-- Email -->
                    <div class="space-y-1.5">
                        <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required
                               class="w-full px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Phone -->
                    <div class="space-y-1.5">
                        <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Phone Number <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}" required
                               class="w-full px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- WhatsApp Number -->
                    <div class="space-y-1.5">
                        <label class="block font-extrabold text-slate-700 uppercase text-[10px]">WhatsApp Number</label>
                        <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $applicant->whatsapp_number ?? '') }}"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Date of Birth -->
                    <div class="space-y-1.5">
                        <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Date of Birth</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($applicant->date_of_birth)->format('Y-m-d') ?? '') }}"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- NIDA Number -->
                    <div class="space-y-1.5">
                        <label class="block font-extrabold text-slate-700 uppercase text-[10px]">National ID (NIDA)</label>
                        <input type="text" name="nida_number" value="{{ old('nida_number', $applicant->nida_number ?? '') }}" placeholder="20-digit NIDA number"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Voter ID Number -->
                    <div class="space-y-1.5">
                        <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Voter ID Number</label>
                        <input type="text" name="voter_id_number" value="{{ old('voter_id_number', $applicant->voter_id_number ?? '') }}" placeholder="Voter registration number"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Work ID Number -->
                    <div class="space-y-1.5">
                        <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Work ID / Employment ID</label>
                        <input type="text" name="work_id_number" value="{{ old('work_id_number', $applicant->work_id_number ?? '') }}" placeholder="Employee check / ID"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Nationality -->
                    <div class="space-y-1.5">
                        <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Nationality</label>
                        <input type="text" name="nationality" value="{{ old('nationality', $applicant->nationality ?? 'Tanzanian') }}"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Location: Region -->
                    <div class="space-y-1.5">
                        <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Region (Mkoa)</label>
                        <input type="text" name="region" value="{{ old('region', $applicant->region ?? '') }}" placeholder="e.g. Singida, Dodoma"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Location: District -->
                    <div class="space-y-1.5">
                        <label class="block font-extrabold text-slate-700 uppercase text-[10px]">District (Wilaya)</label>
                        <input type="text" name="district" value="{{ old('district', $applicant->district ?? '') }}" placeholder="e.g. Singida Mjini, Iramba"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Location: Ward -->
                    <div class="space-y-1.5">
                        <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Ward (Kata)</label>
                        <input type="text" name="ward" value="{{ old('ward', $applicant->ward ?? '') }}" placeholder="e.g. Mandewa"
                               class="w-full px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900 outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Passport Photo Upload -->
                    <div class="space-y-1.5 md:col-span-2">
                        <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Update Passport Photo</label>
                        <div class="flex items-center gap-4">
                            @if ($applicant && $applicant->passport_photo_path)
                                <img src="{{ asset('storage/' . $applicant->passport_photo_path) }}" alt="Passport" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shadow-sm">
                            @endif
                            <input type="file" name="passport_photo" accept="image/*"
                                   class="w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                    </div>
                </div>

                <!-- Next of Kin Sub-section -->
                <div class="pt-4 border-t border-slate-100">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-500 mb-4">Next of Kin / Mtu wa Karibu</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="space-y-1.5">
                            <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Next of Kin Name</label>
                            <input type="text" name="next_of_kin_name" value="{{ old('next_of_kin_name', $applicant->next_of_kin_name ?? '') }}"
                                   class="w-full px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900 outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Next of Kin Phone</label>
                            <input type="text" name="next_of_kin_phone" value="{{ old('next_of_kin_phone', $applicant->next_of_kin_phone ?? '') }}"
                                   class="w-full px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900 outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Relationship (Uhusiano)</label>
                            <input type="text" name="next_of_kin_relation" value="{{ old('next_of_kin_relation', $applicant->next_of_kin_relation ?? '') }}" placeholder="e.g. Parent, Guardian, Sibling"
                                   class="w-full px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900 outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Programme & Application Status -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-black">
                            2
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-900">Programme & Application Settings</h2>
                            <p class="text-xs text-slate-500 font-medium">Selected course, academic intake, and admission status</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- Programme Selection -->
                    <div class="space-y-1.5 md:col-span-2">
                        <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Selected Programme <span class="text-red-500">*</span></label>
                        <select name="programme_id" required class="w-full px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900 outline-none focus:ring-2 focus:ring-amber-500">
                            @foreach ($programmes as $prog)
                                <option value="{{ $prog->id }}" {{ old('programme_id', $application->programme_id) == $prog->id ? 'selected' : '' }}>
                                    [{{ $prog->code }}] {{ $prog->name }} ({{ $prog->degree_level ?? $prog->level }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Admission Type -->
                    <div class="space-y-1.5">
                        <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Entry Qualification Type <span class="text-red-500">*</span></label>
                        <select name="admission_type" x-model="admissionType" required class="w-full px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900 outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="Form Six">Form Six (Direct Entry)</option>
                            <option value="Diploma">Diploma (Equivalent Entry)</option>
                        </select>
                    </div>

                    <!-- Academic Year -->
                    <div class="space-y-1.5">
                        <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Academic Year</label>
                        <select name="academic_year_id" class="w-full px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900 outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="">-- Default Active Year --</option>
                            @foreach ($academicYears as $year)
                                <option value="{{ $year->id }}" {{ old('academic_year_id', $application->academic_year_id) == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Intake -->
                    <div class="space-y-1.5">
                        <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Intake</label>
                        <select name="intake_id" class="w-full px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900 outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="">-- Default Active Intake --</option>
                            @foreach ($intakes as $intake)
                                <option value="{{ $intake->id }}" {{ old('intake_id', $application->intake_id) == $intake->id ? 'selected' : '' }}>
                                    {{ $intake->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status -->
                    <div class="space-y-1.5">
                        <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Application Status</label>
                        <select name="status" class="w-full px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900 outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="Draft" {{ old('status', $application->status) === 'Draft' ? 'selected' : '' }}>Draft</option>
                            <option value="Pending Payment" {{ old('status', $application->status) === 'Pending Payment' ? 'selected' : '' }}>Pending Payment</option>
                            <option value="Under Review" {{ old('status', $application->status) === 'Under Review' ? 'selected' : '' }}>Under Review</option>
                            <option value="Approved" {{ old('status', $application->status) === 'Approved' ? 'selected' : '' }}>Approved (Admitted)</option>
                            <option value="Rejected" {{ old('status', $application->status) === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="Waitlist" {{ old('status', $application->status) === 'Waitlist' ? 'selected' : '' }}>Waitlist</option>
                        </select>
                    </div>

                    <!-- Rejection / Reviewer Notes -->
                    <div class="space-y-1.5 md:col-span-3">
                        <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Reviewer Notes / Rejection Reason</label>
                        <textarea name="rejection_reason" rows="2" placeholder="Optional notes for applicant regarding deficiencies or remarks"
                                  class="w-full px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900 outline-none focus:ring-2 focus:ring-amber-500">{{ old('rejection_reason', $application->rejection_reason ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section 3: Academic Qualifications & Auto-Category Recalculation -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div class="flex flex-wrap items-center justify-between border-b border-slate-100 pb-4 gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center font-black">
                            3
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-900">Academic Qualifications</h2>
                            <p class="text-xs text-slate-500 font-medium">NECTA secondary or NTA diploma records & entry category recalculation</p>
                        </div>
                    </div>

                    <!-- Live Calculated Category Pill -->
                    <div class="flex items-center gap-2 bg-slate-50 p-2 rounded-2xl border border-slate-200">
                        <span class="text-[10px] font-black uppercase text-slate-400">Calculated Admission Category:</span>
                        <span class="px-3 py-1 rounded-xl text-xs font-black"
                              :class="computedCategory === 'Direct Entry' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
                              x-text="computedCategory">
                            {{ $application->admission_category ?? 'Direct Entry' }}
                        </span>
                    </div>
                </div>

                <!-- DIPLOMA FIELDS (Shown if admissionType === 'Diploma') -->
                <div x-show="admissionType === 'Diploma'" class="space-y-5" x-cloak>
                    <div class="p-4 rounded-2xl bg-blue-50/50 border border-blue-100 text-blue-900 text-xs font-semibold flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Diploma Qualification Rules: GPA &ge; 3.0 qualifies for <strong>Direct Entry</strong>. GPA &lt; 3.0 is assigned to <strong>Foundation</strong>.
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="space-y-1.5 md:col-span-2">
                            <label class="block font-extrabold text-slate-700 uppercase text-[10px]">College / Institution Name</label>
                            <input type="text" name="college_name" :disabled="admissionType !== 'Diploma'" value="{{ old('college_name', $academic->college_name ?? '') }}" placeholder="e.g. Singida Teachers Training College"
                                   class="w-full px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900 outline-none focus:ring-2 focus:ring-purple-500 disabled:opacity-50">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Diploma GPA (Grade Point Average)</label>
                            <input type="number" step="0.01" min="0" max="5" name="gpa" :disabled="admissionType !== 'Diploma'" x-model="gpa" @input="recalc()" value="{{ old('gpa', $academic->gpa ?? '') }}" placeholder="e.g. 3.50"
                                   class="w-full px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900 outline-none focus:ring-2 focus:ring-purple-500 disabled:opacity-50">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Diploma Programme Award Name</label>
                            <input type="text" name="diploma_programme_name" :disabled="admissionType !== 'Diploma'" value="{{ old('diploma_programme_name', $academic->diploma_programme_name ?? '') }}" placeholder="e.g. Diploma in Primary Education"
                                   class="w-full px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900 outline-none focus:ring-2 focus:ring-purple-500 disabled:opacity-50">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Registration / Award Number</label>
                            <input type="text" name="diploma_registration_number" :disabled="admissionType !== 'Diploma'" value="{{ old('diploma_registration_number', $academic->diploma_registration_number ?? '') }}" placeholder="e.g. REG/DPE/2024/001"
                                   class="w-full px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900 outline-none focus:ring-2 focus:ring-purple-500 disabled:opacity-50">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Graduation Year</label>
                            <input type="number" min="1990" max="{{ date('Y') }}" name="diploma_graduation_year" :disabled="admissionType !== 'Diploma'" value="{{ old('diploma_graduation_year', $academic->diploma_graduation_year ?? '') }}" placeholder="e.g. {{ date('Y') - 1 }}"
                                   class="w-full px-4 py-3 rounded-2xl border border-slate-300 bg-slate-50 font-bold text-slate-900 outline-none focus:ring-2 focus:ring-purple-500 disabled:opacity-50">
                        </div>
                    </div>
                </div>

                <!-- FORM SIX FIELDS (Shown if admissionType === 'Form Six') -->
                <div x-show="admissionType === 'Form Six'" class="space-y-6" x-cloak>
                    <!-- O-Level (CSEE) -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-600">Form Four (CSEE / O-Level)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-1.5">
                                <label class="block font-extrabold text-slate-700 uppercase text-[10px]">CSEE Index Number</label>
                                <input type="text" name="csee_number" :disabled="admissionType !== 'Form Six'" value="{{ old('csee_number', $academic->csee_number ?? '') }}" placeholder="e.g. S0101/0001/2020"
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 bg-white font-bold text-slate-900 text-xs disabled:opacity-50">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block font-extrabold text-slate-700 uppercase text-[10px]">CSEE Completion Year</label>
                                <input type="number" min="1990" max="{{ date('Y') }}" name="csee_year" :disabled="admissionType !== 'Form Six'" value="{{ old('csee_year', $academic->csee_year ?? '') }}" placeholder="e.g. 2020"
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 bg-white font-bold text-slate-900 text-xs disabled:opacity-50">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block font-extrabold text-slate-700 uppercase text-[10px]">CSEE School Name</label>
                                <input type="text" name="csee_school" :disabled="admissionType !== 'Form Six'" value="{{ old('csee_school', $academic->csee_school ?? '') }}" placeholder="e.g. Singida Secondary School"
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 bg-white font-bold text-slate-900 text-xs disabled:opacity-50">
                            </div>
                        </div>
                    </div>

                    <!-- A-Level (ACSEE) -->
                    <div class="p-5 rounded-2xl bg-purple-50/40 border border-purple-100 space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <h4 class="text-xs font-black uppercase tracking-wider text-purple-900">Form Six (ACSEE / A-Level) & Principal Passes</h4>
                            <span class="text-[11px] font-bold text-purple-700">Calculated Points: <strong x-text="computedPoints"></strong></span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="space-y-1.5">
                                <label class="block font-extrabold text-slate-700 uppercase text-[10px]">ACSEE Index Number</label>
                                <input type="text" name="acsee_number" :disabled="admissionType !== 'Form Six'" value="{{ old('acsee_number', $academic->acsee_number ?? '') }}" placeholder="e.g. S0101/0501/2022"
                                       class="w-full px-4 py-2.5 rounded-xl border border-purple-200 bg-white font-bold text-slate-900 text-xs disabled:opacity-50">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block font-extrabold text-slate-700 uppercase text-[10px]">ACSEE Completion Year</label>
                                <input type="number" min="1990" max="{{ date('Y') }}" name="acsee_year" :disabled="admissionType !== 'Form Six'" value="{{ old('acsee_year', $academic->acsee_year ?? '') }}" placeholder="e.g. 2022"
                                       class="w-full px-4 py-2.5 rounded-xl border border-purple-200 bg-white font-bold text-slate-900 text-xs disabled:opacity-50">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block font-extrabold text-slate-700 uppercase text-[10px]">ACSEE School Name</label>
                                <input type="text" name="acsee_school" :disabled="admissionType !== 'Form Six'" value="{{ old('acsee_school', $academic->acsee_school ?? '') }}" placeholder="e.g. Singida High School"
                                       class="w-full px-4 py-2.5 rounded-xl border border-purple-200 bg-white font-bold text-slate-900 text-xs disabled:opacity-50">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Combination</label>
                                <input type="text" name="acsee_combination" :disabled="admissionType !== 'Form Six'" value="{{ old('acsee_combination', $academic->acsee_combination ?? '') }}" placeholder="e.g. HGL, PCB, EGM"
                                       class="w-full px-4 py-2.5 rounded-xl border border-purple-200 bg-white font-bold text-slate-900 text-xs disabled:opacity-50">
                            </div>
                        </div>

                        <!-- 3 Principal Subjects & Grades -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                            <!-- Subject 1 -->
                            <div class="p-3 bg-white rounded-xl border border-purple-100 space-y-2">
                                <label class="block font-black text-slate-700 uppercase text-[10px]">Principal Subject 1</label>
                                <input type="text" name="acsee_subject1" :disabled="admissionType !== 'Form Six'" value="{{ old('acsee_subject1', $academic->acsee_subject1 ?? '') }}" placeholder="Subject (e.g. History)"
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 font-semibold text-xs disabled:opacity-50">
                                <label class="block font-black text-slate-700 uppercase text-[10px]">Grade</label>
                                <select name="acsee_grade1" :disabled="admissionType !== 'Form Six'" x-model="g1" @change="recalc()" class="w-full px-3 py-2 rounded-lg border border-slate-200 font-bold text-xs disabled:opacity-50">
                                    <option value="">-- Select Grade --</option>
                                    @foreach(['A', 'B', 'C', 'D', 'E', 'S', 'F'] as $g)
                                        <option value="{{ $g }}" {{ old('acsee_grade1', $academic->acsee_grade1 ?? '') === $g ? 'selected' : '' }}>Grade {{ $g }} ({{ $g === 'A' ? '5 pts' : ($g === 'B' ? '4 pts' : ($g === 'C' ? '3 pts' : ($g === 'D' ? '2 pts' : ($g === 'E' ? '1 pt' : ($g === 'S' ? '0.5 pt' : '0 pts'))))) }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Subject 2 -->
                            <div class="p-3 bg-white rounded-xl border border-purple-100 space-y-2">
                                <label class="block font-black text-slate-700 uppercase text-[10px]">Principal Subject 2</label>
                                <input type="text" name="acsee_subject2" :disabled="admissionType !== 'Form Six'" value="{{ old('acsee_subject2', $academic->acsee_subject2 ?? '') }}" placeholder="Subject (e.g. Geography)"
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 font-semibold text-xs disabled:opacity-50">
                                <label class="block font-black text-slate-700 uppercase text-[10px]">Grade</label>
                                <select name="acsee_grade2" :disabled="admissionType !== 'Form Six'" x-model="g2" @change="recalc()" class="w-full px-3 py-2 rounded-lg border border-slate-200 font-bold text-xs disabled:opacity-50">
                                    <option value="">-- Select Grade --</option>
                                    @foreach(['A', 'B', 'C', 'D', 'E', 'S', 'F'] as $g)
                                        <option value="{{ $g }}" {{ old('acsee_grade2', $academic->acsee_grade2 ?? '') === $g ? 'selected' : '' }}>Grade {{ $g }} ({{ $g === 'A' ? '5 pts' : ($g === 'B' ? '4 pts' : ($g === 'C' ? '3 pts' : ($g === 'D' ? '2 pts' : ($g === 'E' ? '1 pt' : ($g === 'S' ? '0.5 pt' : '0 pts'))))) }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Subject 3 -->
                            <div class="p-3 bg-white rounded-xl border border-purple-100 space-y-2">
                                <label class="block font-black text-slate-700 uppercase text-[10px]">Principal Subject 3</label>
                                <input type="text" name="acsee_subject3" :disabled="admissionType !== 'Form Six'" value="{{ old('acsee_subject3', $academic->acsee_subject3 ?? '') }}" placeholder="Subject (e.g. Language)"
                                       class="w-full px-3 py-2 rounded-lg border border-slate-200 font-semibold text-xs disabled:opacity-50">
                                <label class="block font-black text-slate-700 uppercase text-[10px]">Grade</label>
                                <select name="acsee_grade3" :disabled="admissionType !== 'Form Six'" x-model="g3" @change="recalc()" class="w-full px-3 py-2 rounded-lg border border-slate-200 font-bold text-xs disabled:opacity-50">
                                    <option value="">-- Select Grade --</option>
                                    @foreach(['A', 'B', 'C', 'D', 'E', 'S', 'F'] as $g)
                                        <option value="{{ $g }}" {{ old('acsee_grade3', $academic->acsee_grade3 ?? '') === $g ? 'selected' : '' }}>Grade {{ $g }} ({{ $g === 'A' ? '5 pts' : ($g === 'B' ? '4 pts' : ($g === 'C' ? '3 pts' : ($g === 'D' ? '2 pts' : ($g === 'E' ? '1 pt' : ($g === 'S' ? '0.5 pt' : '0 pts'))))) }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- GS Grade & Points override -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                            <div class="space-y-1.5">
                                <label class="block font-extrabold text-slate-700 uppercase text-[10px]">General Studies (GS) Grade</label>
                                <select name="acsee_gs_grade" :disabled="admissionType !== 'Form Six'" class="w-full px-4 py-2.5 rounded-xl border border-purple-200 bg-white font-bold text-slate-900 text-xs disabled:opacity-50">
                                    <option value="">-- Select GS Grade --</option>
                                    @foreach(['A', 'B', 'C', 'D', 'E', 'S', 'F'] as $g)
                                        <option value="{{ $g }}" {{ old('acsee_gs_grade', $academic->acsee_gs_grade ?? '') === $g ? 'selected' : '' }}>Grade {{ $g }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Total Points (Auto-computed / Editable)</label>
                                <input type="number" step="0.5" min="0" max="35" name="acsee_points" :disabled="admissionType !== 'Form Six'" x-model="points" @input="recalc()" value="{{ old('acsee_points', $academic->acsee_points ?? '') }}"
                                       class="w-full px-4 py-2.5 rounded-xl border border-purple-200 bg-white font-bold text-slate-900 text-xs disabled:opacity-50">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save Changes Bar -->
            <div class="flex items-center justify-between bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
                <a href="{{ route('admin.applications.show', $application->id) }}" class="px-6 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-black text-xs transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-8 py-3.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs uppercase tracking-wider shadow-lg shadow-blue-500/20 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Save All Changes
                </button>
            </div>
        </form>

        <!-- Section 4: Certificates & Documents Management (Separate Card & Modals) -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
            <div class="flex flex-wrap items-center justify-between border-b border-slate-100 pb-4 gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-black">
                        4
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-900">Certificates & Document Attachments</h2>
                        <p class="text-xs text-slate-500 font-medium">Upload, replace, inspect, verify, and manage student certificates</p>
                    </div>
                </div>

                <button type="button" @click="showUploadModal = true" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs flex items-center gap-2 shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Upload New Certificate
                </button>
            </div>

            <!-- Documents Table -->
            @if ($application->documents->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 text-[10px] font-black uppercase tracking-wider text-slate-400">
                                <th class="py-3 px-4">Document Type</th>
                                <th class="py-3 px-4">Original Filename</th>
                                <th class="py-3 px-4">Size</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs font-semibold">
                            @foreach ($application->documents as $doc)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-3.5 px-4 font-bold text-slate-900">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            {{ ucwords(str_replace('_', ' ', $doc->document_type)) }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-600 truncate max-w-xs">
                                        {{ $doc->original_filename }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-500 font-mono text-[11px]">
                                        {{ number_format(($doc->file_size_bytes ?? 0) / 1024, 1) }} KB
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if ($doc->verification_status === 'verified')
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-800">Verified</span>
                                        @elseif ($doc->verification_status === 'rejected')
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-red-100 text-red-800">Rejected</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-amber-100 text-amber-800">Pending</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right space-x-1">
                                        <!-- Preview -->
                                        <button type="button" @click="previewDoc('{{ ucwords(str_replace('_', ' ', $doc->document_type)) }}', '{{ asset('storage/' . $doc->file_path) }}', '{{ $doc->original_filename }}', '{{ $doc->mime_type }}')"
                                                class="px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 text-[11px] font-bold">
                                            Preview
                                        </button>

                                        <!-- Replace -->
                                        <button type="button" @click="openReplaceModal({{ $doc->id }}, '{{ ucwords(str_replace('_', ' ', $doc->document_type)) }}')"
                                                class="px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 text-[11px] font-bold">
                                            Replace
                                        </button>

                                        <!-- Delete -->
                                        <form action="{{ route('admin.documents.destroy', $doc->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this document?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 text-[11px] font-bold">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-10 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50/50">
                    <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <p class="text-xs font-bold text-slate-500">No certificates or documents attached yet.</p>
                    <button type="button" @click="showUploadModal = true" class="mt-3 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs inline-flex items-center gap-2">
                        Upload First Document
                    </button>
                </div>
            @endif
        </div>

        <!-- Upload Document Modal -->
        <div x-show="showUploadModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
            <div class="bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl border border-slate-100 space-y-5" @click.outside="showUploadModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-black text-slate-900">Upload Certificate / Attachment</h3>
                    <button type="button" @click="showUploadModal = false" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>

                <form action="{{ route('admin.applications.documents.store', $application->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Document Type <span class="text-red-500">*</span></label>
                        <select name="document_type" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 font-bold text-xs bg-slate-50">
                            <option value="form_four_certificate">Form Four Certificate (CSEE)</option>
                            <option value="form_six_certificate">Form Six Certificate (ACSEE)</option>
                            <option value="diploma_certificate">Diploma Certificate</option>
                            <option value="diploma_transcript">Diploma Academic Transcript</option>
                            <option value="birth_certificate">Birth Certificate (Cheti cha Kuzaliwa)</option>
                            <option value="nida_id">National ID / NIDA Card</option>
                            <option value="leaving_certificate">Leaving Certificate</option>
                            <option value="other_certificate">Other Certificate / Equivalent Qualification</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Select File (PDF, PNG, JPG, WEBP - Max 10MB) <span class="text-red-500">*</span></label>
                        <input type="file" name="document" required accept=".pdf,.png,.jpg,.jpeg,.webp"
                               class="w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Verification Status</label>
                        <select name="verification_status" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 font-bold text-xs bg-slate-50">
                            <option value="verified" selected>Auto-Verify Immediately</option>
                            <option value="pending">Keep as Pending Verification</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="showUploadModal = false" class="px-4 py-2 rounded-xl bg-slate-100 font-bold text-xs text-slate-600">Cancel</button>
                        <button type="submit" class="px-6 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md">Upload</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Replace Document Modal -->
        <div x-show="showReplaceModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
            <div class="bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl border border-slate-100 space-y-5" @click.outside="showReplaceModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-black text-slate-900">Replace Document: <span x-text="replaceDocType" class="text-blue-600"></span></h3>
                    <button type="button" @click="showReplaceModal = false" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>

                <form :action="'{{ url('/admin/documents') }}/' + replaceDocId + '/replace'" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Select New Replacement File <span class="text-red-500">*</span></label>
                        <input type="file" name="document" required accept=".pdf,.png,.jpg,.jpeg,.webp"
                               class="w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block font-extrabold text-slate-700 uppercase text-[10px]">Verification Status</label>
                        <select name="verification_status" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 font-bold text-xs bg-slate-50">
                            <option value="verified" selected>Mark as Verified</option>
                            <option value="pending">Mark as Pending</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="showReplaceModal = false" class="px-4 py-2 rounded-xl bg-slate-100 font-bold text-xs text-slate-600">Cancel</button>
                        <button type="submit" class="px-6 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-black text-xs shadow-md">Replace File</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Document Preview Modal -->
        <div x-show="showPreviewModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm" x-cloak>
            <div class="bg-white rounded-3xl p-6 max-w-4xl w-full max-h-[90vh] flex flex-col shadow-2xl border border-slate-100" @click.outside="showPreviewModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-sm font-black text-slate-900" x-text="previewTitle"></h3>
                        <p class="text-[11px] text-slate-500 truncate" x-text="previewFilename"></p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a :href="previewUrl" download class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">Download</a>
                        <button type="button" @click="showPreviewModal = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold px-2">&times;</button>
                    </div>
                </div>

                <div class="flex-1 overflow-auto py-4 flex items-center justify-center min-h-[400px]">
                    <template x-if="previewUrl.endsWith('.pdf') || previewMime === 'application/pdf'">
                        <iframe :src="previewUrl" class="w-full h-[600px] rounded-2xl border border-slate-200"></iframe>
                    </template>
                    <template x-if="!previewUrl.endsWith('.pdf') && previewMime !== 'application/pdf'">
                        <img :src="previewUrl" alt="Document Preview" class="max-h-[600px] w-auto rounded-2xl object-contain shadow-sm">
                    </template>
                </div>
            </div>
        </div>

    </div>

    <!-- Alpine.js Script for Live Recalculation and UI Interactivity -->
    <script>
        function editStudentData() {
            return {
                admissionType: '{{ old('admission_type', $application->admission_type ?? 'Form Six') }}',
                gpa: '{{ old('gpa', $academic->gpa ?? '') }}',
                points: '{{ old('acsee_points', $academic->acsee_points ?? '') }}',
                g1: '{{ old('acsee_grade1', $academic->acsee_grade1 ?? '') }}',
                g2: '{{ old('acsee_grade2', $academic->acsee_grade2 ?? '') }}',
                g3: '{{ old('acsee_grade3', $academic->acsee_grade3 ?? '') }}',

                computedCategory: '{{ $application->admission_category ?? 'Direct Entry' }}',
                computedPoints: '{{ old('acsee_points', $academic->acsee_points ?? '') }}',

                showUploadModal: false,
                showReplaceModal: false,
                showPreviewModal: false,

                replaceDocId: null,
                replaceDocType: '',

                previewTitle: '',
                previewUrl: '',
                previewFilename: '',
                previewMime: '',

                init() {
                    this.recalc();
                },

                recalc() {
                    if (this.admissionType === 'Diploma') {
                        if (this.gpa && parseFloat(this.gpa) >= 3.0) {
                            this.computedCategory = 'Direct Entry';
                        } else {
                            this.computedCategory = 'Foundation';
                        }
                    } else {
                        // Form Six
                        const gradePointMap = { 'A': 5, 'B': 4, 'C': 3, 'D': 2, 'E': 1, 'S': 0.5, 'F': 0 };
                        let calcPoints = 0;
                        let principals = 0;
                        const principalGrades = ['A', 'B', 'C', 'D', 'E'];

                        if (this.g1) {
                            const u1 = this.g1.toUpperCase();
                            if (gradePointMap[u1] !== undefined) calcPoints += gradePointMap[u1];
                            if (principalGrades.includes(u1)) principals++;
                        }
                        if (this.g2) {
                            const u2 = this.g2.toUpperCase();
                            if (gradePointMap[u2] !== undefined) calcPoints += gradePointMap[u2];
                            if (principalGrades.includes(u2)) principals++;
                        }
                        if (this.g3) {
                            const u3 = this.g3.toUpperCase();
                            if (gradePointMap[u3] !== undefined) calcPoints += gradePointMap[u3];
                            if (principalGrades.includes(u3)) principals++;
                        }

                        if (calcPoints > 0) {
                            this.points = calcPoints;
                            this.computedPoints = calcPoints;
                        } else {
                            this.computedPoints = this.points;
                        }

                        if (principals >= 2) {
                            this.computedCategory = 'Direct Entry';
                        } else if (this.points && this.points <= 17 && this.points >= 5) {
                            this.computedCategory = 'Direct Entry';
                        } else {
                            this.computedCategory = 'Foundation';
                        }
                    }
                },

                openReplaceModal(id, type) {
                    this.replaceDocId = id;
                    this.replaceDocType = type;
                    this.showReplaceModal = true;
                },

                previewDoc(title, url, filename, mime) {
                    this.previewTitle = title;
                    this.previewUrl = url;
                    this.previewFilename = filename;
                    this.previewMime = mime;
                    this.showPreviewModal = true;
                }
            };
        }
    </script>
</x-app-layout>
