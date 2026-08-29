<x-app-layout title="Applicant Profile Review">
    <x-slot name="header">Applicant Profile Review</x-slot>

    <div class="w-full grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="{
        scheduleModal: false,
        scoreModal: false,
        evalModal: false,
        testModal: false,
        letterModal: false,
        credentialsModal: false,
        passwordOption: 'generate',
        activeInterviewId: '',
        signatureData: '',
        signaturePad: null,
        
        initSignature() {
            this.$nextTick(() => {
                const canvas = document.getElementById('signature-canvas');
                if (canvas) {
                    const ctx = canvas.getContext('2d');
                    ctx.strokeStyle = '#1E3A8A';
                    ctx.lineWidth = 2;
                    let drawing = false;
                    
                    canvas.addEventListener('mousedown', (e) => {
                        drawing = true;
                        ctx.beginPath();
                        ctx.moveTo(e.offsetX, e.offsetY);
                    });
                    
                    canvas.addEventListener('mousemove', (e) => {
                        if (drawing) {
                            ctx.lineTo(e.offsetX, e.offsetY);
                            ctx.stroke();
                        }
                    });
                    
                    canvas.addEventListener('mouseup', () => drawing = false);
                    canvas.addEventListener('mouseleave', () => drawing = false);
                }
            });
        },
        
        clearSignature() {
            const canvas = document.getElementById('signature-canvas');
            if (canvas) {
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                this.signatureData = '';
            }
        },
        
        saveSignature() {
            const canvas = document.getElementById('signature-canvas');
            if (canvas) {
                this.signatureData = canvas.toDataURL('image/png');
                toast('Signature saved!', 'success');
            }
        }
    }">
        <!-- Left 2 Columns: Applicant Details -->
        <div class="lg:col-span-2 space-y-8">
            @if(session('success'))
                <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-2xl text-xs font-bold">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Main Personal Header Card -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 flex flex-col sm:flex-row items-center gap-6">
                <div class="w-24 h-24 rounded-3xl bg-gradient-to-br from-amber-500 to-blue-900 text-white font-extrabold flex items-center justify-center text-4xl shadow-xl overflow-hidden shrink-0">
                    @if(isset($application->attachments['passport_photo']))
                        <img src="{{ asset('storage/' . $application->attachments['passport_photo']) }}" alt="Passport" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($application->full_name, 0, 1)) }}
                    @endif
                </div>

                <div class="space-y-2 text-center sm:text-left overflow-hidden">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2.5">
                        <h2 class="text-xl font-black text-slate-900">{{ $application->full_name }}</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-amber-100 text-amber-800 self-center">
                            {{ $application->status }}
                        </span>
                    </div>
                    <p class="text-xs font-bold text-blue-600">{{ $application->application_number }} &bull; Applied for: {{ $application->vacancy->job_title ?? 'N/A' }}</p>
                    <div class="text-[11px] text-slate-500 font-semibold space-x-4">
                        <span>📧 {{ $application->email }}</span>
                        <span>📞 {{ $application->phone }}</span>
                    </div>
                </div>
            </div>

            <!-- Education & Experience -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
                <!-- Education -->
                <div class="space-y-4">
                    <h3 class="font-extrabold text-slate-900 border-b pb-2">Academic Profile (Education)</h3>
                    <div class="space-y-4 text-xs">
                        @foreach(($application->education_history ?? []) as $edu)
                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 font-bold shrink-0">🎓</div>
                                <div class="font-semibold space-y-0.5">
                                    <h4 class="font-bold text-slate-900 text-xs">{{ $edu['level'] ?? 'N/A' }} in {{ $edu['programme'] ?? $edu['field'] ?? 'N/A' }}</h4>
                                    <p class="text-slate-500">{{ $edu['institution'] ?? 'N/A' }} &bull; {{ $edu['start_year'] ?? 'N/A' }} - {{ $edu['completion_year'] ?? $edu['end_year'] ?? 'Present' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                               <!-- Experience -->
                <div class="space-y-4 pt-4 border-t">
                    <h3 class="font-extrabold text-slate-900 border-b pb-2">Professional Experience</h3>
                    <div class="space-y-4 text-xs">
                        @forelse(($application->experience_history ?? []) as $exp)
                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-full bg-amber-50 flex items-center justify-center text-amber-500 font-bold shrink-0">💼</div>
                                <div class="font-semibold space-y-0.5 w-full">
                                    <div class="flex justify-between items-start">
                                        <h4 class="font-bold text-slate-900 text-xs">{{ $exp['position'] ?? 'N/A' }} at {{ $exp['employer'] ?? $exp['company'] ?? 'N/A' }}</h4>
                                        @if(!empty($exp['employment_type']))
                                            <span class="px-2 py-0.5 rounded-full bg-slate-100 text-[9px] text-slate-500 font-extrabold">{{ $exp['employment_type'] }}</span>
                                        @endif
                                    </div>
                                    <p class="text-slate-500">
                                        {{ $exp['start_year'] ?? (isset($exp['start_date']) ? date('M Y', strtotime($exp['start_date'])) : 'N/A') }} - 
                                        {{ $exp['end_year'] ?? (!empty($exp['end_date']) ? date('M Y', strtotime($exp['end_date'])) : 'Present') }}
                                    </p>
                                    <p class="text-slate-500 text-[11px] pt-1">{{ $exp['responsibilities'] ?? $exp['description'] ?? '' }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-slate-500 italic">No professional work experience listed.</p>
                        @endforelse

                        <!-- Singida Teachers' Training College Previous Work -->
                        @if($application->worked_at_sttc && is_array($application->sttc_experience))
                            @php $sttc = $application->sttc_experience; @endphp
                            <div class="p-5 rounded-2xl bg-amber-500/5 border border-amber-500/10 space-y-3 mt-4">
                                <h4 class="font-extrabold text-amber-600 text-xs uppercase tracking-wider flex items-center gap-1.5">
                                    <span>🏛️</span> Singida Teachers' Training College Experience
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs font-semibold text-slate-600">
                                    <div>Kampasi / Kituo: <span class="text-slate-900 font-bold">{{ $sttc['campus'] ?? 'N/A' }}</span></div>
                                    <div>Idara (Department): <span class="text-slate-900 font-bold">{{ $sttc['department'] ?? 'N/A' }}</span></div>
                                    <div>Cheo (Designation): <span class="text-slate-900 font-bold">{{ $sttc['position_held'] ?? 'N/A' }}</span></div>
                                    <div>Kipindi (Period): <span class="text-slate-900 font-bold">{{ $sttc['start_year'] ?? 'N/A' }} - {{ $sttc['end_year'] ?? 'N/A' }}</span></div>
                                    @if(!empty($sttc['reason_for_leaving']))
                                        <div class="col-span-2 pt-1 border-t mt-1">
                                            Sababu ya Kuondoka (Reason for Leaving): <span class="text-slate-900 font-bold">{{ $sttc['reason_for_leaving'] }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>      </div>

                <!-- Skills & Languages -->
                <div class="grid grid-cols-2 gap-6 pt-4 border-t">
                    <div class="space-y-3">
                        <h4 class="font-bold text-slate-900">Required / Listed Skills</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach(($application->ict_skills ?? []) as $skill)
                                <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-[10px] font-bold">
                                    {{ is_array($skill) ? (($skill['skill'] ?? '') . (isset($skill['level']) ? ' (' . $skill['level'] . ')' : '')) : $skill }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <div class="space-y-3">
                        <h4 class="font-bold text-slate-900">Candidate Details</h4>
                        <div class="text-[11px] text-slate-500 font-semibold space-y-1">
                            <div>Gender: <span class="text-slate-800 uppercase font-black">{{ $application->gender ?? 'N/A' }}</span></div>
                            <div>DOB: <span class="text-slate-800 font-bold">{{ $application->date_of_birth ? $application->date_of_birth->format('Y-m-d') : 'N/A' }}</span></div>
                            <div>Region: <span class="text-slate-800 font-bold">{{ $application->region ?? 'N/A' }}</span></div>
                            <div>District: <span class="text-slate-800 font-bold">{{ $application->district ?? 'N/A' }}</span></div>
                            <div>NIDA Number: <span class="text-slate-800 font-bold font-mono">{{ $application->nida_number ?? 'N/A' }}</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Qualifications & Teaching Experience Card -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
                <h3 class="font-extrabold text-slate-900 border-b pb-2">Qualifications & Teaching Experience</h3>
                
                @php
                    $profQuals = $application->professional_qualifications;
                    $hasTeaching = isset($profQuals['teaching_details']) && !empty($profQuals['teaching_details']['years_experience']);
                    $hasQualifications = isset($profQuals['qualifications']) && count($profQuals['qualifications']) > 0;
                @endphp

                @if(!$hasTeaching && !$hasQualifications)
                    <p class="text-slate-500 italic text-xs">No teaching specialization or professional qualifications listed.</p>
                @endif

                @if($hasTeaching)
                    @php $td = $profQuals['teaching_details']; @endphp
                    <div class="space-y-3">
                        <h4 class="font-bold text-xs text-amber-500 uppercase tracking-wider">Teaching Specialization Details</h4>
                        <div class="grid grid-cols-2 gap-4 text-xs font-semibold text-slate-600">
                            <div>Subjects: <span class="text-slate-900 font-bold">{{ implode(', ', $td['specialized_subjects'] ?? []) }}{{ !empty($td['other_subjects']) ? ', ' . $td['other_subjects'] : '' }}</span></div>
                            <div>Years of Experience: <span class="text-slate-900 font-bold">{{ $td['years_experience'] ?? '0' }} years</span></div>
                            <div>Level Taught: <span class="text-slate-900 font-bold">{{ $td['level_taught'] ?? $td['level'] ?? 'N/A' }}</span></div>
                            <div>Institution Taught: <span class="text-slate-900 font-bold">{{ $td['institution_taught'] ?? 'N/A' }}</span></div>
                        </div>
                    </div>
                @endif

                @if($hasQualifications)
                    <div class="space-y-3 @if($hasTeaching) pt-4 border-t @endif">
                        <h4 class="font-bold text-xs text-blue-500 uppercase tracking-wider">Professional Board Registrations & Certificates</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-semibold">
                            @foreach($profQuals['qualifications'] as $q)
                                <div class="p-4 rounded-2xl border space-y-2 bg-slate-50/50">
                                    <div class="font-bold text-slate-900 text-xs">{{ $q['name'] ?? 'N/A' }}</div>
                                    <div class="grid grid-cols-2 gap-2 text-[10px] text-slate-500">
                                        <div>Reg No: <span class="font-bold text-slate-700">{{ $q['registration_number'] ?? 'N/A' }}</span></div>
                                        <div>Issued: <span class="font-bold text-slate-700">{{ $q['date_issued'] ?? 'N/A' }}</span></div>
                                        <div class="col-span-2">Expiry: <span class="font-bold text-slate-700">{{ $q['expiry_date'] ?: 'N/A' }}</span></div>
                                    </div>
                                    @if(!empty($q['certificate_path']))
                                        <div class="pt-1.5 border-t text-right mt-1.5">
                                            <a href="{{ asset('storage/' . $q['certificate_path']) }}" target="_blank" class="text-blue-500 hover:underline font-bold text-[10px]">View Certificate &rarr;</a>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Documents Uploaded Card -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-4">
                <h3 class="font-extrabold text-slate-900">Attached Files & Verification Documents</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-semibold">
                    @if(isset($application->attachments['cv']))
                        <a href="{{ asset('storage/' . $application->attachments['cv']) }}" target="_blank" class="p-4 rounded-2xl border flex items-center justify-between hover:bg-slate-50">
                            <span>CV (Curriculum Vitae)</span>
                            <span class="text-blue-500 hover:underline">View File &rarr;</span>
                        </a>
                    @endif
                    @if(isset($application->attachments['cover_letter']))
                        <a href="{{ asset('storage/' . $application->attachments['cover_letter']) }}" target="_blank" class="p-4 rounded-2xl border flex items-center justify-between hover:bg-slate-50">
                            <span>Cover Letter</span>
                            <span class="text-blue-500 hover:underline">View File &rarr;</span>
                        </a>
                    @endif
                    @if(isset($application->attachments['nida']))
                        <a href="{{ asset('storage/' . $application->attachments['nida']) }}" target="_blank" class="p-4 rounded-2xl border flex items-center justify-between hover:bg-slate-50">
                            <span>National ID Card</span>
                            <span class="text-blue-500 hover:underline">View File &rarr;</span>
                        </a>
                    @endif
                </div>
            </div>



            <!-- Motivation Letter Card -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-4">
                <h3 class="font-extrabold text-slate-900">Candidate Motivation Statement</h3>
                <div class="p-5 bg-slate-50 rounded-2xl border">
                    <p class="text-xs font-semibold text-slate-700 leading-relaxed whitespace-pre-wrap italic">
                        "{{ $application->motivation_letter ?? 'No motivation statement submitted.' }}"
                    </p>
                </div>
            </div>

            <!-- Evaluation Timeline and Scorecards -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
                <h3 class="font-extrabold text-slate-900 border-b pb-2">Evaluations & Assessments</h3>

                <!-- Interviews Scheduled / Conducted -->
                <div class="space-y-3">
                    <h4 class="font-bold text-xs text-slate-500">Interviews History</h4>
                    @forelse($application->interviews as $interview)
                        <div class="p-4 rounded-2xl border space-y-2 text-xs">
                            <div class="flex justify-between items-center font-bold">
                                <span>Type: {{ $interview->type }} Interview</span>
                                <span class="text-amber-500">{{ $interview->date->format('d M Y') }} at {{ $interview->time }}</span>
                            </div>
                            <div class="text-[11px] text-slate-500">Venue/Link: <span class="text-slate-800">{{ $interview->venue ?: $interview->meeting_link }}</span></div>
                            <p class="text-[11px] text-slate-500">{{ $interview->instructions }}</p>

                            <!-- Scorecards list -->
                            <div class="pt-2 border-t space-y-2">
                                <span class="font-bold text-[10px] uppercase text-slate-500 block">Interviewer Scorecards</span>
                                @forelse($interview->scorecards as $sc)
                                    <div class="bg-slate-50 p-3 rounded-xl flex justify-between items-center">
                                        <div>
                                            <span class="font-bold">{{ $sc->interviewer->name }}</span>
                                            <span class="text-[10px] text-slate-500 italic block">"{{ $sc->comments }}"</span>
                                        </div>
                                        <span class="font-black text-amber-500 text-sm">{{ number_format($sc->average_score, 1) }} / 10</span>
                                    </div>
                                @empty
                                    <p class="text-slate-500 text-[10px] italic">No scorecards submitted yet.</p>
                                @endforelse
                                @if(in_array(Auth::id(), $interview->panel_members ?? []))
                                    <button @click="activeInterviewId = '{{ $interview->id }}'; scoreModal = true" class="gradient-btn px-4 py-1.5 rounded-lg text-white text-[10px] mt-2 font-extrabold">Submit Scorecard</button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-500 italic text-xs">No interviews scheduled yet.</p>
                    @endforelse
                </div>

                <!-- Written Tests -->
                <div class="space-y-3 pt-4 border-t">
                    <h4 class="font-bold text-xs text-slate-500">Written Tests</h4>
                    @forelse($application->writtenTests as $test)
                        <div class="p-4 bg-slate-50 rounded-2xl flex justify-between items-center text-xs">
                            <div>
                                <div class="font-bold text-slate-900">{{ $test->test_name }}</div>
                                <div class="text-[10px] text-slate-500">Assigned: {{ $test->assigned_date->format('d M Y') }}</div>
                                @if($test->comments)
                                    <div class="text-[10px] text-slate-500 italic">"{{ $test->comments }}"</div>
                                @endif
                            </div>
                            <div class="text-right">
                                @if($test->status === 'Completed')
                                    <span class="font-black text-emerald-500 text-lg">{{ $test->marks }} / 100</span>
                                @else
                                    <button @click="testModal = true" class="gradient-btn px-4 py-1.5 rounded-lg text-white text-[10px]">Record Marks</button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-500 italic text-xs">No written tests assigned.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column: Status Transition Panel & Audit Timeline -->
        <div class="space-y-8">
            <!-- Stage transition triggers -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4 text-xs font-semibold">
                <h3 class="font-extrabold text-slate-900 border-b pb-2">HR Actions Desk</h3>
                
                <div class="space-y-2">
                    <button @click="evalModal = true" class="w-full gradient-btn py-3 rounded-2xl text-white font-extrabold text-center shadow-md">
                        Transition Pipeline Stage
                    </button>

                    <button @click="credentialsModal = true" class="w-full px-4 py-3 rounded-2xl border border-blue-500/30 text-blue-500 bg-blue-500/5 text-center hover:bg-blue-500 hover:text-white transition-colors">
                        Manage Login Credentials
                    </button>
                    
                    <button @click="scheduleModal = true" class="w-full px-4 py-3 rounded-2xl border text-center hover:bg-slate-50">
                        Schedule Interview
                    </button>
                    
                    <button @click="testModal = true" class="w-full px-4 py-3 rounded-2xl border text-center hover:bg-slate-50">
                        Assign Written Test
                    </button>

                    @if($application->status === 'Selected')
                        <button @click="letterModal = true; initSignature();" class="w-full px-4 py-3 rounded-2xl bg-amber-500/20 text-amber-500 border border-amber-500/30 text-center hover:bg-amber-500 hover:text-white transition-colors">
                            Generate Offer Letter
                        </button>
                    @endif
                </div>
            </div>

            <!-- Historical Progression Stage Logs (ATS History) -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4 text-xs">
                <h3 class="font-extrabold text-slate-900 border-b pb-2">Stage Timeline History</h3>
                <div class="space-y-6">
                    @foreach($application->stages as $stg)
                        <div class="flex items-start gap-4 text-xs relative">
                            <div class="w-2.5 h-2.5 rounded-full bg-amber-500 mt-1 shrink-0"></div>
                            <div class="font-semibold space-y-0.5">
                                <div class="font-bold text-slate-900">{{ $stg->stage }}</div>
                                <p class="text-slate-500 text-[10px]">{{ $stg->created_at->format('d M Y, h:i A') }}</p>
                                <p class="text-slate-500 text-[11px] font-medium">"{{ $stg->comments }}"</p>
                                <div class="text-[9px] text-slate-500">Processed by: {{ $stg->assignedHrOfficer->name ?? 'System' }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Schedule Interview Modal -->
        <div x-show="scheduleModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/40 backdrop-blur-sm" x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-md p-6 border border-slate-200 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="font-extrabold text-slate-900">Schedule Interview</h3>
                    <button @click="scheduleModal = false" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <form action="{{ route('admin.recruitment.interviews.schedule') }}" method="POST" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <input type="hidden" name="job_application_id" value="{{ $application->id }}">
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Interview Type</label>
                        <select name="type" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                            <option value="Physical">Physical Interview</option>
                            <option value="Online">Online Interview</option>
                            <option value="Phone">Phone Interview</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Date</label>
                            <input type="date" name="date" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Time</label>
                            <input type="time" name="time" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Venue / Meeting Link</label>
                        <input type="text" name="venue" placeholder="e.g. Conference Room A or Zoom link" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Panel Members</label>
                        <select name="panel_members[]" multiple required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950 h-24">
                            @foreach($staff as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Instructions</label>
                        <textarea name="instructions" rows="2" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="scheduleModal = false" class="px-4 py-2.5 rounded-xl border hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="gradient-btn px-5 py-2.5 rounded-xl text-white">Schedule</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Submit Scorecard Modal -->
        <div x-show="scoreModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/40 backdrop-blur-sm" x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-md p-6 border border-slate-200 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="font-extrabold text-slate-900">Submit Interview Scorecard</h3>
                    <button @click="scoreModal = false" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <form action="{{ route('admin.recruitment.scores.store') }}" method="POST" class="space-y-3 text-xs font-semibold max-h-[70vh] overflow-y-auto pr-1">
                    @csrf
                    <input type="hidden" name="interview_id" x-model="activeInterviewId">
                    
                    @foreach(['communication', 'technical_knowledge', 'problem_solving', 'leadership', 'teamwork', 'confidence', 'professionalism'] as $field)
                        <div class="space-y-1.5">
                            <label class="block text-slate-500 capitalize">{{ str_replace('_', ' ', $field) }} Score (1 - 10)</label>
                            <input type="number" name="{{ $field }}" min="1" max="10" required class="w-full p-2.5 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                    @endforeach

                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Interviewer Comments</label>
                        <textarea name="comments" rows="2" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="scoreModal = false" class="px-4 py-2.5 rounded-xl border hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="gradient-btn px-5 py-2.5 rounded-xl text-white">Submit</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Assign Written Test Modal -->
        <div x-show="testModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/40 backdrop-blur-sm" x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-md p-6 border border-slate-200 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="font-extrabold text-slate-900">Assign Written Test</h3>
                    <button @click="testModal = false" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <form action="{{ route('admin.recruitment.written-tests.assign') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <input type="hidden" name="job_application_id" value="{{ $application->id }}">
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Test / Exam Name</label>
                        <input type="text" name="test_name" placeholder="e.g. Technical Coding and Algorithmic Exam" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Date Scheduled</label>
                        <input type="date" name="assigned_date" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Questions File / Script</label>
                        <input type="file" name="questions_file" class="w-full p-2 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="testModal = false" class="px-4 py-2.5 rounded-xl border hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="gradient-btn px-5 py-2.5 rounded-xl text-white">Assign Test</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Offer Letter Generator Modal -->
        <div x-show="letterModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/40 backdrop-blur-sm overflow-y-auto" x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-md p-6 border border-slate-200 shadow-2xl space-y-4 my-8">
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="font-extrabold text-slate-900">Generate Offer Letter</h3>
                    <button @click="letterModal = false" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <form action="{{ route('admin.recruitment.offer-letters.generate') }}" method="POST" class="space-y-4 text-xs font-semibold max-h-[70vh] overflow-y-auto pr-1">
                    @csrf
                    <input type="hidden" name="job_application_id" value="{{ $application->id }}">
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Basic Monthly Salary</label>
                        <input type="text" name="salary" placeholder="e.g. TZS 1,800,000 / month" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Benefits & Allowances</label>
                        <textarea name="benefits" placeholder="Medical insurance, travel allowance, etc." required rows="2" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950"></textarea>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Reporting Date</label>
                        <input type="date" name="reporting_date" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Employment / Contract Terms</label>
                        <textarea name="employment_terms" placeholder="Probation periods, standard hours..." required rows="3" class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="letterModal = false" class="px-4 py-2.5 rounded-xl border hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="gradient-btn px-5 py-2.5 rounded-xl text-white">Generate & Send</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Pipeline Transition Stage Modal -->
        <div x-show="evalModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/40 backdrop-blur-sm" x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-md p-6 border border-slate-200 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="font-extrabold text-slate-900">Transition Pipeline Stage</h3>
                    <button @click="evalModal = false" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <form action="{{ route('admin.recruitment.applications.stage', $application->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Select New Stage</label>
                        <select name="stage" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                            @foreach(['Applied', 'Under Review', 'Shortlisted', 'Interview Scheduled', 'Written Test', 'Final Interview', 'Reference Check', 'Medical Examination', 'Selected', 'Offer Letter', 'Hired', 'Rejected'] as $stg)
                                <option value="{{ $stg }}" {{ $application->status === $stg ? 'selected' : '' }}>{{ $stg }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Comments / Notes</label>
                        <textarea name="comments" rows="3" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="evalModal = false" class="px-4 py-2.5 rounded-xl border hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="gradient-btn px-5 py-2.5 rounded-xl text-white">Save Stage</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Manage Login Credentials Modal -->
        <div x-show="credentialsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/40 backdrop-blur-sm" x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-md p-6 border border-slate-200 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="font-extrabold text-slate-900">Manage Login Credentials</h3>
                    <button @click="credentialsModal = false" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <form action="{{ route('admin.recruitment.applications.credentials', $application->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
                    @csrf

                    <!-- Contact Details Form Prefilled fields -->
                    <div class="space-y-3">
                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Applicant Login Identity (Phone Number)</label>
                            <input type="text" name="phone" value="{{ old('phone', $application->phone) }}" required 
                                   class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-slate-500">WhatsApp Number</label>
                            <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $application->whatsapp_number ?? $application->phone) }}" required 
                                   class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-slate-500">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $application->email) }}" required 
                                   class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                        </div>
                    </div>

                    <!-- Password Option Selection -->
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Password Option</label>
                        <select name="password_option" x-model="passwordOption" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                            <option value="generate">Generate Random Password</option>
                            <option value="custom">Enter Custom Password</option>
                            <option value="keep">Keep Current Password (If User Exists)</option>
                        </select>
                    </div>

                    <!-- Custom Password Input -->
                    <div class="space-y-1.5" x-show="passwordOption === 'custom'">
                        <label class="block text-slate-500">Custom Password</label>
                        <input type="text" name="custom_password" placeholder="Min 6 characters" :required="passwordOption === 'custom'" 
                               class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                    </div>

                    <!-- Notification Channel Checkboxes -->
                    <div class="space-y-2">
                        <label class="block text-slate-500">Send Credentials Via</label>
                        <div class="flex flex-col gap-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="channels[]" value="sms" checked class="rounded text-blue-500 focus:ring-blue-500">
                                <span class="text-slate-800">SMS Notification</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="channels[]" value="whatsapp" checked class="rounded text-blue-500 focus:ring-blue-500">
                                <span class="text-slate-800">WhatsApp Notification</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="channels[]" value="email" checked class="rounded text-blue-500 focus:ring-blue-500">
                                <span class="text-slate-800">Email Notification</span>
                            </label>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="credentialsModal = false" class="px-4 py-2.5 rounded-xl border hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="gradient-btn px-5 py-2.5 rounded-xl text-white">Create & Send Credentials</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
