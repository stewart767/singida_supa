<x-public-layout title="Frequently Asked Questions - SUPA University">

    <section class="relative text-white py-20 text-center space-y-4 border-b border-slate-200 bg-cover bg-center bg-no-repeat bg-gradient-to-r from-slate-950 via-blue-950 to-slate-900"
             @if(\App\Models\Setting::get('banner_faqs')) style="background-image: linear-gradient(to right, rgba(2, 6, 23, 0.95), rgba(30, 58, 138, 0.85)), url('{{ asset('storage/' . \App\Models\Setting::get('banner_faqs')) }}');" @endif>
        <span class="px-3.5 py-1.5 rounded-full bg-purple-500/20 border border-purple-500/30 text-purple-400 text-xs font-extrabold uppercase tracking-wider">
            Applicant Help Center
        </span>
        <h1 class="hero-title font-extrabold tracking-tight">Frequently Asked Questions</h1>
        <p class="body-text text-slate-300 max-w-2xl mx-auto">
            Everything you need to know about online applications, admission entry categories, payment control numbers, and registration.
        </p>
    </section>

    <section class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @foreach($faqs as $faq)
                <div x-data="{ open: false }" class="bg-white rounded-3xl border border-slate-200 p-6 shadow-md transition-all">
                    <button @click="open = !open" class="w-full flex justify-between items-center text-left font-extrabold text-slate-900 text-base">
                        <span>{{ $faq->question }}</span>
                        <div class="w-8 h-8 rounded-xl bg-slate-100 text-amber-500 flex items-center justify-center shrink-0 ml-4">
                            <svg class="w-4 h-4 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </button>
                    <div x-show="open" class="mt-4 pt-4 border-t border-slate-100 text-xs text-slate-600 leading-relaxed space-y-2" x-cloak>
                        {{ $faq->answer }}
                    </div>
                </div>
            @endforeach
        </div>
    </section>

</x-public-layout>
