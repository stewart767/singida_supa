<x-public-layout title="University Events & Academic Calendar - SUPA / OUT Admissions">

    <div x-data="{ 
        activeModal: false,
        modalEvent: { title: '', date: '', location: '', description: '', image: '' },
        openModal(item) {
            this.modalEvent = item;
            this.activeModal = true;
        }
    }">

        <!-- Hero Banner Section -->
        <section class="relative text-white py-20 border-b border-slate-200 overflow-hidden bg-cover bg-center bg-no-repeat bg-gradient-to-r from-slate-950 via-blue-950 to-slate-900"
                 @if(\App\Models\Setting::get('banner_events')) style="background-image: linear-gradient(to right, rgba(2, 6, 23, 0.95), rgba(30, 58, 138, 0.85)), url('{{ asset('storage/' . \App\Models\Setting::get('banner_events')) }}');" @endif>
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#3b82f6_1px,transparent_1px)] [background-size:20px_20px]"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-6">
                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-400 text-xs font-extrabold uppercase tracking-wider shadow-lg">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                    Campus Activities & Academic Timeline
                </span>
                
                <h1 class="hero-title text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight text-white leading-tight">
                    Upcoming Events & Academic Dates
                </h1>
                
                <p class="body-text text-slate-300 max-w-3xl mx-auto text-base sm:text-lg leading-relaxed font-normal">
                    Explore important upcoming dates, student orientation sessions, admission deadlines, workshops, and joint university functions.
                </p>
            </div>
        </section>

        <!-- Events Grid Section -->
        <section class="py-16 bg-slate-50 min-h-[500px]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @if($eventsList->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($eventsList as $event)
                            <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-xl hover:border-blue-300 transition-all duration-300 flex flex-col group">
                                @if($event->image_path)
                                    <div class="relative h-48 w-full overflow-hidden bg-slate-100">
                                        <img src="{{ asset('storage/' . $event->image_path) }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        <div class="absolute top-4 right-4 bg-blue-900/90 text-amber-300 text-xs font-black px-3 py-1.5 rounded-xl backdrop-blur-md border border-white/20 shadow-md">
                                            {{ $event->event_date ? $event->event_date->format('M d, Y') : 'TBA' }}
                                        </div>
                                    </div>
                                @else
                                    <div class="h-32 bg-gradient-to-br from-blue-950 via-slate-900 to-blue-900 p-6 flex items-center justify-between text-white relative">
                                        <div class="w-12 h-12 rounded-2xl bg-amber-400/20 border border-amber-400/40 text-amber-300 flex items-center justify-center font-black text-xl">
                                            📅
                                        </div>
                                        <div class="text-right">
                                            <span class="text-[11px] uppercase tracking-wider text-slate-300 font-bold block">Event Date</span>
                                            <span class="text-sm font-extrabold text-amber-400">{{ $event->event_date ? $event->event_date->format('M d, Y') : 'TBA' }}</span>
                                        </div>
                                    </div>
                                @endif

                                <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                                    <div class="space-y-2">
                                        @if($event->location)
                                            <div class="flex items-center text-xs font-semibold text-slate-700">
                                                <svg class="w-4 h-4 mr-1.5 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                <span>{{ $event->location }}</span>
                                            </div>
                                        @endif

                                        <h3 class="text-xl font-bold text-slate-900 group-hover:text-blue-900 transition-colors leading-snug line-clamp-2">
                                            {{ $event->title }}
                                        </h3>

                                        <p class="text-sm text-slate-800 line-clamp-3 leading-relaxed">
                                            {{ $event->description }}
                                        </p>
                                    </div>

                                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-700">
                                            {{ $event->event_date ? $event->event_date->format('h:i A') : '' }}
                                        </span>
                                        <button type="button" 
                                                @click="openModal({
                                                    title: '{{ addslashes($event->title) }}',
                                                    date: '{{ $event->event_date ? $event->event_date->format('F d, Y - h:i A') : 'TBA' }}',
                                                    location: '{{ addslashes($event->location ?? 'Singida Campus') }}',
                                                    description: '{{ addslashes($event->description) }}',
                                                    image: '{{ $event->image_path ? asset('storage/' . $event->image_path) : '' }}'
                                                })"
                                                class="inline-flex items-center text-xs font-extrabold text-blue-900 hover:text-blue-800 group-hover:translate-x-1 transition-all">
                                            Event Details
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-12">
                        {{ $eventsList->links() }}
                    </div>
                @else
                    <div class="text-center py-20 bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
                        <div class="w-16 h-16 bg-blue-50 text-blue-900 rounded-3xl flex items-center justify-center mx-auto mb-4 text-2xl font-black">
                            🗓️
                        </div>
                        <h3 class="text-xl font-black text-slate-900">No Scheduled Events At The Moment</h3>
                        <p class="text-slate-700 text-sm mt-2 max-w-md mx-auto">
                            Check back periodically for updates on admissions cycles, seminars, open days, and student orientations.
                        </p>
                    </div>
                @endif
            </div>
        </section>

        <!-- Event Details Modal -->
        <div x-show="activeModal" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-100 relative space-y-6"
                 @click.outside="activeModal = false">
                
                <button type="button" @click="activeModal = false" class="absolute top-5 right-5 text-slate-400 hover:text-slate-700 p-2 rounded-full hover:bg-slate-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <div class="space-y-3">
                    <div class="flex items-center gap-2 text-xs font-black text-amber-800 uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span x-text="modalEvent.date"></span>
                    </div>

                    <h2 class="text-2xl font-black text-slate-900 leading-snug" x-text="modalEvent.title"></h2>

                    <div class="flex items-center text-xs font-bold text-slate-700">
                        <svg class="w-4 h-4 mr-1 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span x-text="modalEvent.location"></span>
                    </div>
                </div>

                <template x-if="modalEvent.image">
                    <div class="rounded-2xl overflow-hidden max-h-64 bg-slate-100">
                        <img :src="modalEvent.image" :alt="modalEvent.title" class="w-full h-full object-cover">
                    </div>
                </template>

                <div class="text-slate-800 text-sm leading-relaxed whitespace-pre-line" x-text="modalEvent.description"></div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="button" @click="activeModal = false" class="px-6 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>

    </div>

</x-public-layout>
