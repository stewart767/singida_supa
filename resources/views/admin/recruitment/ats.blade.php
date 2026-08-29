<x-app-layout title="Applicant Tracking System (ATS) Board">
    <x-slot name="header">ATS Visual Pipeline</x-slot>

    <div class="w-full space-y-6" x-data="{
        moveModal: false,
        activeApp: {},
        newStage: '',
        comments: '',
        openMove(app) {
            this.activeApp = app;
            this.newStage = app.status;
            this.comments = '';
            this.moveModal = true;
        },
        submitMove() {
            axios.post('{{ url('/admin/recruitment/applications') }}/' + this.activeApp.id + '/stage', {
                stage: this.newStage,
                comments: this.comments
            })
            .then(res => {
                toast('Applicant moved successfully!', 'success');
                setTimeout(() => window.location.reload(), 1000);
            })
            .catch(err => {
                toast('Failed to move applicant.', 'error');
            });
        }
    }">
        <!-- Pipeline Header Info -->
        <div class="flex justify-between items-center bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
            <div>
                <h3 class="font-extrabold text-slate-900 text-base">Visual Recruitment Board</h3>
                <p class="text-xs text-slate-500">Drag or click on candidates to transition them through stages.</p>
            </div>
            <a href="{{ route('admin.recruitment.applications.index') }}" class="text-xs font-extrabold text-amber-500 hover:underline">&larr; Back to Directory</a>
        </div>

        <!-- Kanban Board Container -->
        <div class="flex space-x-4 overflow-x-auto pb-6 scrollbar-thin scrollbar-thumb-slate-300 min-h-[60vh] text-xs">
            @foreach($stages as $stage)
                <div class="w-72 shrink-0 flex flex-col bg-slate-100 border border-slate-200 rounded-3xl p-4 space-y-3">
                    <!-- Column Header -->
                    <div class="flex justify-between items-center border-b pb-2">
                        <span class="font-extrabold text-slate-900">{{ $stage }}</span>
                        <span class="px-2 py-0.5 rounded-full bg-slate-200 text-[10px] font-extrabold">{{ count($applicationsByStage[$stage]) }}</span>
                    </div>

                    <!-- Column Cards -->
                    <div class="flex-grow space-y-3 overflow-y-auto max-h-[50vh] pr-1">
                        @forelse($applicationsByStage[$stage] as $app)
                            <div @click="openMove({{ json_encode($app) }})" class="bg-white p-4 rounded-2xl border border-slate-200/60 shadow-sm cursor-pointer hover:border-amber-500/50 hover:shadow-md transition-all space-y-2 card-hover-effect">
                                <div class="font-bold text-slate-900">{{ $app->full_name }}</div>
                                <div class="text-[10px] text-blue-600 font-extrabold">{{ $app->application_number }}</div>
                                <div class="text-[10px] text-slate-500 font-semibold truncate">{{ $app->vacancy->job_title ?? 'N/A' }}</div>
                                <div class="flex justify-between items-center text-[9px] text-slate-500 font-bold pt-1 border-t">
                                    <span>Applied: {{ $app->created_at->format('d M') }}</span>
                                    <span class="text-amber-500">Move &rarr;</span>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-slate-500 italic">No candidates</div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Transition/Move Stage Modal -->
        <div x-show="moveModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/40 backdrop-blur-sm" x-cloak>
            <div class="bg-white rounded-3xl w-full max-w-md p-6 border border-slate-200 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b pb-3">
                    <div>
                        <h3 class="font-extrabold text-slate-900">Transition Application Stage</h3>
                        <p class="text-[10px] text-slate-500" x-text="activeApp.full_name + ' (' + activeApp.application_number + ')'"></p>
                    </div>
                    <button @click="moveModal = false" class="text-slate-500 hover:text-slate-600">&times;</button>
                </div>
                <form @submit.prevent="submitMove()" class="space-y-4 text-xs font-semibold">
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">Select New Stage</label>
                        <select name="stage" x-model="newStage" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950">
                            @foreach($stages as $st)
                                <option value="{{ $st }}">{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-slate-500">HR Comments / Instructions</label>
                        <textarea name="comments" x-model="comments" placeholder="e.g. Scheduled for technical interview. Send notification alert." rows="3" required class="w-full p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-950"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="moveModal = false" class="px-4 py-2.5 rounded-xl border hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="gradient-btn px-5 py-2.5 rounded-xl text-white">Save Stage Change</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
