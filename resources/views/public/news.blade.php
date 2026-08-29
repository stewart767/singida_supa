<x-public-layout title="News, Circulars & Events - SUPA / OUT Admissions Portal">

    <div x-data="{ 
        searchQuery: '{{ $search ?? '' }}', 
        selectedCategory: 'all',
        activeModal: false,
        modalNews: { title: '', date: '', summary: '', content: '', image: '', slug: '' },
        openModal(item) {
            this.modalNews = item;
            this.activeModal = true;
        }
    }">

        <!-- Hero Banner Section -->
        <section class="relative text-white py-20 border-b border-slate-200 overflow-hidden bg-cover bg-center bg-no-repeat bg-gradient-to-r from-slate-950 via-blue-950 to-slate-900"
                 @if(\App\Models\Setting::get('banner_news')) style="background-image: linear-gradient(to right, rgba(2, 6, 23, 0.95), rgba(30, 58, 138, 0.85)), url('{{ asset('storage/' . \App\Models\Setting::get('banner_news')) }}');" @endif>
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#3b82f6_1px,transparent_1px)] [background-size:20px_20px]"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-6">
                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-400 text-xs font-extrabold uppercase tracking-wider shadow-lg">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                    Official Portal Bulletins & Circulars
                </span>
                
                <h1 class="hero-title text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight text-white leading-tight">
                    News, Circulars & Announcements
                </h1>
                
                <p class="body-text text-slate-300 max-w-3xl mx-auto text-base sm:text-lg leading-relaxed font-normal">
                    Stay fully informed with official academic notices, admission guidelines, entry requirements, control number instructions, and university updates.
                </p>

                <!-- Search & Quick Filter Input Box -->
                <div class="max-w-2xl mx-auto pt-4">
                    <form method="GET" action="{{ route('public.news') }}" class="relative">
                        <input type="text" name="search" x-model="searchQuery" placeholder="Search notices, e.g. Admission 2026, Foundation Course, Fee payment..."
                               class="w-full pl-12 pr-24 py-4 rounded-2xl border border-slate-200 bg-white/90 text-slate-900 text-sm font-semibold placeholder-slate-400 outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent shadow-2xl transition-all">
                        <svg class="w-5 h-5 text-slate-500 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center space-x-2">
                            <button type="button" x-show="searchQuery" @if(empty($search)) x-cloak @endif @click="searchQuery = ''; window.location.href='{{ route('public.news') }}'" class="text-slate-500 hover:text-slate-900 text-xs font-bold">✕ Clear</button>
                            <button type="submit" class="gradient-btn-gold px-3.5 py-1.5 rounded-xl text-slate-950 font-extrabold text-[10px] uppercase shadow-sm">Search</button>
                        </div>
                    </form>
                </div>

                <!-- Stats Bar -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 max-w-4xl mx-auto pt-6 text-left">
                    <div class="bg-white/10 backdrop-blur-md border border-white/10 p-4 rounded-2xl flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center font-extrabold text-lg shrink-0">
                            {{ count($newsList) }}
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-300 font-bold uppercase tracking-wider block">Total Bulletins</span>
                            <span class="text-xs font-black text-white">Active Notices</span>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-md border border-white/10 p-4 rounded-2xl flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-extrabold text-lg shrink-0">
                            {{ count($newsList->where('is_featured', true)) }}
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-300 font-bold uppercase tracking-wider block">Featured</span>
                            <span class="text-xs font-black text-white">Top Priorities</span>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-md border border-white/10 p-4 rounded-2xl flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-extrabold text-lg shrink-0">
                            2026
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-300 font-bold uppercase tracking-wider block">Academic Session</span>
                            <span class="text-xs font-black text-white">2026 / 2027 Intake</span>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-md border border-white/10 p-4 rounded-2xl flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-extrabold text-lg shrink-0">
                            100%
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-300 font-bold uppercase tracking-wider block">Verification</span>
                            <span class="text-xs font-black text-white">Verified Notices</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main News Catalog Section -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                
                @php
                    $featuredNews = $newsList->firstWhere('is_featured', true) ?? $newsList->first();
                @endphp

                <!-- Featured News Banner Card -->
                @if($featuredNews)
                    <div x-show="!searchQuery" @if(!empty($search)) x-cloak @endif class="bg-gradient-to-br from-slate-900 via-blue-950 to-slate-950 text-white rounded-3xl border border-slate-200 shadow-2xl p-8 sm:p-12 relative overflow-hidden group">
                        <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
                        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-3 gap-8 items-center">
                            
                            <div class="lg:col-span-2 space-y-4">
                                <div class="flex items-center space-x-3 text-xs">
                                    <span class="px-3.5 py-1 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-400 font-extrabold uppercase tracking-widest text-[10px]">
                                        Featured Circular
                                    </span>
                                    <span class="text-slate-500">•</span>
                                    <span class="text-slate-300 font-bold">
                                        {{ $featuredNews->published_at ? $featuredNews->published_at->format('F d, Y') : 'Official Notice' }}
                                    </span>
                                </div>

                                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white leading-snug group-hover:text-amber-300 transition-colors">
                                    {{ $featuredNews->title }}
                                </h2>

                                <p class="text-slate-300 text-sm sm:text-base leading-relaxed line-clamp-3">
                                    {{ $featuredNews->summary }}
                                </p>

                                <div class="pt-4 flex flex-wrap items-center gap-4">
                                    <a href="{{ route('public.news.show', $featuredNews->slug) }}" class="gradient-btn-gold px-6 py-3 rounded-2xl text-slate-950 font-extrabold text-xs shadow-xl flex items-center gap-2">
                                        <span>Read Full Announcement</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>

                                    <button @click="openModal({
                                        title: {{ json_encode($featuredNews->title) }},
                                        date: {{ json_encode($featuredNews->published_at ? $featuredNews->published_at->format('F d, Y') : 'Official Notice') }},
                                        summary: {{ json_encode($featuredNews->summary) }},
                                        content: {{ json_encode($featuredNews->content) }},
                                        image: {{ json_encode($featuredNews->image_path ? (\Illuminate\Support\Str::startsWith($featuredNews->image_path, 'http') ? $featuredNews->image_path : asset('storage/' . $featuredNews->image_path)) : 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?q=80&w=800') }},
                                        slug: {{ json_encode($featuredNews->slug) }}
                                    })" class="px-5 py-3 rounded-2xl bg-slate-800/80 border border-slate-200 text-white font-bold text-xs hover:bg-slate-700 transition-colors flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Quick Preview
                                    </button>
                                </div>
                            </div>

                            <div class="bg-white/90 border border-slate-200 p-6 rounded-2xl space-y-4 shadow-xl">
                                <div class="flex items-center space-x-3 pb-3 border-b border-slate-200">
                                    <div class="w-10 h-10 rounded-xl bg-blue-600/20 text-blue-400 flex items-center justify-center font-bold text-sm shrink-0">
                                        ST
                                    </div>
                                    <div>
                                        <span class="text-xs font-bold text-slate-900 block">Admissions Registry</span>
                                        <span class="text-[10px] text-slate-500">Verified Official Notice</span>
                                    </div>
                                </div>
                                <div class="space-y-2 text-xs text-slate-700">
                                    <p class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>Target: All Applicants & Public</span>
                                    </p>
                                    <p class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Effective: 2026/2027 Academic Session</span>
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>
                @endif

                <!-- Section Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-6">
                    <div>
                        <h3 class="text-2xl font-black text-slate-900 tracking-tight">Recent Notices & Updates</h3>
                        <p class="text-xs text-slate-500 mt-1">Browse all official university circulars and news bulletins</p>
                    </div>
                    <span class="text-xs font-extrabold text-slate-600 bg-slate-200 px-3.5 py-1.5 rounded-full w-max">
                        Showing {{ count($newsList) }} Notices
                    </span>
                </div>

                <!-- News Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($newsList as $item)
                        @if($featuredNews && $item->id === $featuredNews->id && empty($search))
                            @continue
                        @endif
                        <div data-search-text="{{ strtolower($item->title . ' ' . $item->summary) }}"
                             x-show="!searchQuery || $el.getAttribute('data-search-text').includes(searchQuery.toLowerCase())"
                             class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xl hover:shadow-2xl transition-all duration-300 space-y-5 flex flex-col justify-between group hover:-translate-y-1">
                            
                            <div class="space-y-4">
                                <!-- Photo Header/Cover Image -->
                                <div class="relative h-48 overflow-hidden rounded-2xl bg-slate-100 shrink-0">
                                    <img src="{{ $item->image_path ? (\Illuminate\Support\Str::startsWith($item->image_path, 'http') ? $item->image_path : asset('storage/' . $item->image_path)) : 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?q=80&w=800' }}" 
                                         alt="{{ $item->title }}" 
                                         loading="lazy" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                </div>

                                <div class="space-y-3">
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-[10px] font-extrabold text-amber-500 uppercase tracking-widest bg-amber-500/10 px-2.5 py-1 rounded-lg">
                                            {{ $item->published_at ? $item->published_at->format('M d, Y') : 'Announcement' }}
                                        </span>
                                        <span class="text-[11px] text-slate-500 font-medium">Official Notice</span>
                                    </div>

                                    <h3 class="text-lg font-extrabold text-slate-900 group-hover:text-blue-600 transition-colors leading-snug line-clamp-2">
                                        {{ $item->title }}
                                    </h3>

                                    <p class="text-xs text-slate-600 leading-relaxed line-clamp-3 font-normal">
                                        {{ $item->summary }}
                                    </p>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3 text-xs">
                                <a href="{{ route('public.news.show', $item->slug) }}" class="font-extrabold text-blue-600 hover:underline flex items-center gap-1">
                                    <span>Read Notice</span>
                                    <span>&rarr;</span>
                                </a>

                                <button @click="openModal({
                                    title: {{ json_encode($item->title) }},
                                    date: {{ json_encode($item->published_at ? $item->published_at->format('F d, Y') : 'Official Notice') }},
                                    summary: {{ json_encode($item->summary) }},
                                    content: {{ json_encode($item->content) }},
                                    image: {{ json_encode($item->image_path ? (\Illuminate\Support\Str::startsWith($item->image_path, 'http') ? $item->image_path : asset('storage/' . $item->image_path)) : 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?q=80&w=800') }},
                                    slug: {{ json_encode($item->slug) }}
                                })" class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold text-[11px] transition-colors">
                                    Quick View
                                </button>
                            </div>

                        </div>
                    @endforeach
                </div>

                <!-- Pagination Links -->
                @if($newsList instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $newsList->hasPages())
                    <div class="pt-4 pb-8 flex justify-center">
                        {{ $newsList->links() }}
                    </div>
                @endif

                <!-- Newsletter Subscription Card -->
                <div class="bg-gradient-to-r from-blue-900/90 via-slate-900 to-indigo-950 text-white rounded-3xl p-8 sm:p-12 border border-blue-800/60 shadow-2xl flex flex-col md:flex-row items-center justify-between gap-8">
                    <div class="space-y-2 max-w-xl">
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-amber-400 bg-amber-500/20 px-3 py-1 rounded-full">
                            Admission Alerts
                        </span>
                        <h3 class="text-2xl sm:text-3xl font-black text-white">Subscribe for Direct Circular Alerts</h3>
                        <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                            Receive real-time notifications about application deadlines, selection lists, fee control numbers, and registration dates directly in your inbox.
                        </p>
                    </div>

                    <form @submit.prevent="toast('Subscribed successfully to university circulars!', 'success')" class="w-full md:w-auto flex flex-col sm:flex-row gap-3">
                        <input type="email" placeholder="Enter your email address" required class="px-5 py-3.5 rounded-2xl bg-white border border-slate-200 text-slate-900 text-xs font-semibold placeholder-slate-400 outline-none focus:ring-2 focus:ring-amber-500 min-w-[260px]">
                        <button type="submit" class="gradient-btn-gold px-6 py-3.5 rounded-2xl text-slate-950 font-extrabold text-xs shadow-xl shrink-0">
                            Subscribe Now
                        </button>
                    </form>
                </div>

            </div>
        </section>

        <!-- Dynamic Notice Modal -->
        <div x-show="activeModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
            <div @click="activeModal = false" class="fixed inset-0 bg-white/80 backdrop-blur-md"></div>
            <div class="relative bg-white border border-slate-200 max-w-2xl w-full p-8 rounded-3xl shadow-2xl z-50 space-y-6 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-start border-b border-slate-100 pb-4">
                    <div class="space-y-1 pr-4">
                        <span class="text-[10px] font-extrabold text-amber-500 uppercase tracking-widest" x-text="modalNews.date"></span>
                        <h3 class="font-extrabold text-slate-900 text-xl leading-snug" x-text="modalNews.title"></h3>
                    </div>
                    <button @click="activeModal = false" class="p-2 text-slate-500 hover:text-slate-600 font-extrabold text-lg">✕</button>
                </div>

                <template x-if="modalNews.image">
                    <div class="w-full h-56 overflow-hidden rounded-2xl border border-slate-200 shadow-sm shrink-0">
                        <img :src="modalNews.image" alt="News Cover" class="w-full h-full object-cover">
                    </div>
                </template>

                <div class="space-y-4 text-xs sm:text-sm text-slate-700 leading-relaxed">
                    <p class="font-bold text-slate-900 p-4 rounded-2xl bg-white border border-slate-200" x-text="modalNews.summary"></p>
                    <div class="whitespace-pre-line text-slate-600 leading-relaxed font-normal" x-text="modalNews.content"></div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex flex-wrap justify-between items-center gap-4">
                    <a :href="'{{ url('/news') }}/' + modalNews.slug" class="gradient-btn px-5 py-2.5 rounded-xl text-white font-extrabold text-xs shadow-md">
                        Open Full Notice Page &rarr;
                    </a>
                    <button @click="activeModal = false" class="px-4 py-2.5 rounded-xl bg-slate-200 text-slate-700 font-bold text-xs">
                        Close Preview
                    </button>
                </div>
            </div>
        </div>

    </div>

</x-public-layout>
