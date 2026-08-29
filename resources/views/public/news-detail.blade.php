<x-public-layout :title="$news->title . ' - News & Announcements'">

    <!-- Article Hero Section -->
    <section class="relative text-white py-16 border-b border-slate-200 overflow-hidden bg-cover bg-center bg-no-repeat bg-gradient-to-r from-slate-950 via-blue-950 to-slate-900"
             @if(\App\Models\Setting::get('banner_news')) style="background-image: linear-gradient(to right, rgba(2, 6, 23, 0.95), rgba(30, 58, 138, 0.85)), url('{{ asset('storage/' . \App\Models\Setting::get('banner_news')) }}');" @endif>
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#3b82f6_1px,transparent_1px)] [background-size:16px_16px]"></div>
        
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 space-y-6">
            <div class="flex items-center space-x-3 text-xs">
                <a href="{{ route('public.news') }}" class="text-amber-400 font-extrabold hover:underline flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to All News
                </a>
                <span class="text-slate-600">•</span>
                <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-500/30 text-blue-300 font-extrabold uppercase tracking-wider text-[10px]">
                    {{ $news->is_featured ? 'Featured Announcement' : 'Official Circular' }}
                </span>
            </div>

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white leading-tight tracking-tight">
                {{ $news->title }}
            </h1>

            <div class="flex flex-wrap items-center gap-4 text-xs text-slate-300 pt-2 border-t border-slate-200/80">
                <div class="flex items-center space-x-2">
                    <div class="w-7 h-7 rounded-full bg-amber-500 text-slate-950 font-bold flex items-center justify-center text-xs">
                        ST
                    </div>
                    <span class="font-bold text-white">Directorate of Admissions & Communications</span>
                </div>
                <span class="text-slate-600">|</span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ $news->published_at ? $news->published_at->format('F d, Y') : 'Official Announcement' }}
                </span>
                <span class="text-slate-600">|</span>
                <span class="flex items-center gap-1 text-slate-300">
                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    3 min read
                </span>
            </div>
        </div>
    </section>

    <!-- Main Content Body -->
    <section class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            
            <!-- Summary Highlight Box -->
            @if($news->summary)
                <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-blue-900/10 via-amber-500/5 to-transparent border border-blue-200 shadow-sm">
                    <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 block mb-2">Executive Summary</span>
                    <p class="text-base sm:text-lg text-slate-800 font-semibold leading-relaxed">
                        {{ $news->summary }}
                    </p>
                </div>
            @endif

            <!-- Article Content Body -->
            <div class="bg-white rounded-3xl border border-slate-200 p-8 sm:p-12 shadow-xl space-y-6 text-slate-800 leading-relaxed font-normal text-base">
                <div class="prose max-w-none space-y-4">
                    {!! nl2br(e($news->content)) !!}
                </div>

                <!-- Verified Notice Footer Callout -->
                <div class="pt-8 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 text-xs">
                    <div class="flex items-center space-x-2 text-emerald-600 font-extrabold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Official Notice - Verified by Admission Registry</span>
                    </div>

                    <div class="flex items-center space-x-3">
                        <button onclick="navigator.clipboard.writeText(window.location.href); alert('Article URL copied to clipboard!');" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-colors flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            Copy Link
                        </button>
                        <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition-colors flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Print Notice
                        </button>
                    </div>
                </div>
            </div>

            <!-- Related Announcements Grid -->
            @if(count($recentNews) > 0)
                <div class="space-y-6 pt-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-extrabold text-slate-900">Recent Related Circulars</h3>
                        <a href="{{ route('public.news') }}" class="text-xs font-bold text-amber-500 hover:underline">View All &rarr;</a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($recentNews as $recent)
                            <a href="{{ route('public.news.show', $recent->slug) }}" class="bg-white p-6 rounded-3xl border border-slate-200 shadow-lg hover:shadow-2xl transition-all duration-300 flex flex-col justify-between group">
                                <div class="space-y-3">
                                    <span class="text-[10px] font-extrabold text-amber-500 uppercase tracking-widest block">
                                        {{ $recent->published_at ? $recent->published_at->format('M d, Y') : 'Notice' }}
                                    </span>
                                    <h4 class="font-extrabold text-slate-900 text-sm group-hover:text-blue-600 transition-colors line-clamp-2">
                                        {{ $recent->title }}
                                    </h4>
                                    <p class="text-xs text-slate-600 line-clamp-2">
                                        {{ $recent->summary }}
                                    </p>
                                </div>
                                <div class="pt-4 mt-4 border-t border-slate-100 text-xs font-bold text-blue-600 group-hover:translate-x-1 transition-transform flex items-center gap-1">
                                    <span>Read Notice</span>
                                    <span>&rarr;</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </section>

</x-public-layout>
