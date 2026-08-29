<x-public-layout title="Applicant Portal Dashboard">


    <div class="w-full bg-white text-white py-12 px-4 border-b border-slate-200">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <span class="text-xs font-bold text-amber-500 uppercase tracking-widest block mb-1">Candidate Portal</span>
                <h1 class="text-3xl font-black">Welcome, {{ Auth::user()->name }}</h1>
            </div>
            <div class="text-slate-500 text-xs font-semibold">
                Logged in as: <span class="text-white">{{ Auth::user()->email }}</span>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8" x-data="{
        signModal: false,
        activeLetterId: '',
        activeLetterSalary: '',
        activeLetterDate: '',
        signatureData: '',
        drawing: false,
        
        initCanvas() {
            this.$nextTick(() => {
                const canvas = document.getElementById('applicant-sig-canvas');
                if (canvas) {
                    const ctx = canvas.getContext('2d');
                    ctx.strokeStyle = '#1E3A8A';
                    ctx.lineWidth = 3;
                    
                    canvas.addEventListener('mousedown', (e) => {
                        this.drawing = true;
                        ctx.beginPath();
                        ctx.moveTo(e.offsetX, e.offsetY);
                    });
                    
                    canvas.addEventListener('mousemove', (e) => {
                        if (this.drawing) {
                            ctx.lineTo(e.offsetX, e.offsetY);
                            ctx.stroke();
                        }
                    });
                    
                    canvas.addEventListener('mouseup', () => this.drawing = false);
                    canvas.addEventListener('mouseleave', () => this.drawing = false);
                    
                    // Touch support for mobiles
                    canvas.addEventListener('touchstart', (e) => {
                        const touch = e.touches[0];
                        const rect = canvas.getBoundingClientRect();
                        ctx.beginPath();
                        ctx.moveTo(touch.clientX - rect.left, touch.clientY - rect.top);
                        this.drawing = true;
                    });
                    canvas.addEventListener('touchmove', (e) => {
                        if (this.drawing) {
                            const touch = e.touches[0];
                            const rect = canvas.getBoundingClientRect();
                            ctx.lineTo(touch.clientX - rect.left, touch.clientY - rect.top);
                            ctx.stroke();
                        }
                    });
                    canvas.addEventListener('touchend', () => this.drawing = false);
                }
            });
        },
        clearCanvas() {
            const canvas = document.getElementById('applicant-sig-canvas');
            if (canvas) {
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                this.signatureData = '';
            }
        },
        submitSign() {
            const canvas = document.getElementById('applicant-sig-canvas');
            if (canvas) {
                this.signatureData = canvas.toDataURL('image/png');
                if (this.signatureData.length < 1000) {
                    toast('Please draw your signature before submitting.', 'error');
                    return;
                }
                document.getElementById('sig-input-' + this.activeLetterId).value = this.signatureData;
                document.getElementById('sig-form-' + this.activeLetterId).submit();
            }
        }
    }">
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 rounded-2xl text-xs font-bold shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <h2 class="text-base font-extrabold text-slate-800">Your Submitted Job Applications</h2>

        <!-- Applications Grid -->
        <div class="grid grid-cols-1 gap-6">
            @forelse($applications as $app)
                <div class="bg-white rounded-3xl border border-slate-200 shadow-md p-6 sm:p-8 space-y-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                        <div class="space-y-1">
                            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-blue-50 text-blue-600">
                                {{ $app->status }}
                            </span>
                            <h3 class="text-lg font-extrabold text-slate-900">{{ $app->vacancy->job_title ?? 'N/A' }}</h3>
                            <p class="text-xs text-slate-500 font-semibold">Ref: {{ $app->vacancy->vacancy_number ?? '' }} &bull; Applied: {{ $app->created_at->format('d M Y') }}</p>
                        </div>
                        <div class="text-slate-500 text-xs font-semibold">
                            Application #: <span class="text-slate-900 font-mono">{{ $app->application_number }}</span>
                        </div>
                    </div>

                    <!-- Pipeline History -->
                    <div class="pt-4 border-t space-y-3">
                        <span class="font-bold text-[10px] text-slate-500 uppercase tracking-widest block">Process Milestones</span>
                        <div class="flex flex-wrap gap-4 text-[11px] font-bold text-slate-500">
                            @foreach($app->stages as $stg)
                                <div class="px-3 py-1.5 rounded-xl bg-slate-50 flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    <span>{{ $stg->stage }} ({{ $stg->created_at->format('d M') }})</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Associated Scheduled Interviews -->
                    @if($app->interviews->isNotEmpty())
                        <div class="pt-4 border-t space-y-3">
                            <span class="font-bold text-[10px] text-slate-500 uppercase tracking-widest block">Scheduled Interviews</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-semibold">
                                @foreach($app->interviews as $interview)
                                    <div class="p-4 bg-amber-500/5 border border-amber-500/20 rounded-2xl space-y-1">
                                        <div class="flex justify-between items-center">
                                            <span class="text-amber-500 font-black">{{ $interview->type }} Interview</span>
                                            <span class="text-slate-500 text-[10px]">{{ $interview->date->format('d M Y') }} at {{ $interview->time }}</span>
                                        </div>
                                        <div class="text-[11px] text-slate-500">Venue/Link: <span class="text-slate-800">{{ $interview->venue ?: $interview->meeting_link }}</span></div>
                                        @if($interview->instructions)
                                            <p class="text-[10px] text-slate-500 italic pt-1">"{{ $interview->instructions }}"</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Associated Offer Letter -->
                    @if($app->offerLetter)
                        <div class="pt-4 border-t space-y-4">
                            <span class="font-bold text-[10px] text-slate-500 uppercase tracking-widest block">Job Offer Letter</span>
                            <div class="p-6 bg-emerald-500/5 border border-emerald-500/20 rounded-3xl flex flex-col sm:flex-row justify-between items-center gap-6">
                                <div class="space-y-1.5 text-xs font-semibold text-slate-600">
                                    <h4 class="font-extrabold text-slate-900 text-sm">Employment Offer Letter Issued</h4>
                                    <div>Monthly Salary: <span class="text-slate-900 font-bold">{{ $app->offerLetter->salary }}</span></div>
                                    <div>Start Date: <span class="text-slate-900 font-bold">{{ $app->offerLetter->reporting_date->format('d M Y') }}</span></div>
                                    <div>Status: <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-100 text-amber-800 uppercase">{{ $app->offerLetter->status }}</span></div>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ asset('storage/' . $app->offerLetter->pdf_path) }}" target="_blank" class="px-4 py-2.5 rounded-xl border font-extrabold text-xs">View Offer Document</a>
                                    @if($app->offerLetter->status === 'Issued')
                                        <button @click="activeLetterId = '{{ $app->offerLetter->id }}'; activeLetterSalary = '{{ $app->offerLetter->salary }}'; activeLetterDate = '{{ $app->offerLetter->reporting_date->format('d M Y') }}'; signModal = true; initCanvas();" class="gradient-btn px-5 py-2.5 rounded-xl text-white font-extrabold text-xs shadow-md">
                                            Accept & Sign Digitally
                                        </button>
                                        <form id="sig-form-{{ $app->offerLetter->id }}" action="{{ route('public.careers.offer-letter.sign', $app->offerLetter->id) }}" method="POST" class="hidden">
                                            @csrf
                                            <input type="hidden" name="signature" id="sig-input-{{ $app->offerLetter->id }}">
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="py-16 text-center text-slate-500 space-y-4">
                    <span class="text-5xl block">📝</span>
                    <h3 class="font-bold">You haven't submitted any job applications yet.</h3>
                    <p class="text-xs text-slate-500">Go to our career listing page and apply for active vacancies.</p>
                    <a href="{{ route('public.careers.index') }}" class="gradient-btn px-6 py-3 rounded-2xl text-white font-extrabold text-xs shadow-md inline-block">Browse Jobs</a>
                </div>
            @endforelse
        </div>

        <!-- Canvas Sign Modal -->
        <div x-show="signModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/40 backdrop-blur-sm" x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-md p-6 border border-slate-200 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b pb-3">
                    <div>
                        <h3 class="font-extrabold text-slate-900">Sign Job Offer Letter</h3>
                        <p class="text-[10px] text-slate-500">Accept job with basic salary of <span x-text="activeLetterSalary"></span> starting <span x-text="activeLetterDate"></span></p>
                    </div>
                    <button @click="signModal = false" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <div class="space-y-4 text-xs font-semibold text-slate-600">
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Draw Your Signature Below</label>
                        <div class="border-2 border-dashed rounded-2xl overflow-hidden bg-slate-50">
                            <canvas id="applicant-sig-canvas" width="400" height="150" class="w-full cursor-crosshair"></canvas>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <button type="button" @click="clearCanvas()" class="text-red-500 font-bold hover:underline">Clear Canvas</button>
                        <span class="text-[10px] text-slate-500">Use mouse or touchscreen to draw</span>
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="signModal = false" class="px-4 py-2.5 rounded-xl border hover:bg-slate-50">Cancel</button>
                        <button type="button" @click="submitSign()" class="gradient-btn px-6 py-2.5 rounded-xl text-white font-extrabold shadow-md">Accept & Sign</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-public-layout>
