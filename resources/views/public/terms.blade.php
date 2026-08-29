<x-public-layout title="Terms & Conditions of Admission - SUPA University">

    <section class="relative text-white py-20 text-center space-y-4 border-b border-slate-200 bg-cover bg-center bg-no-repeat bg-gradient-to-r from-slate-950 via-blue-950 to-slate-900"
             @if(\App\Models\Setting::get('banner_news')) style="background-image: linear-gradient(to right, rgba(2, 6, 23, 0.95), rgba(30, 58, 138, 0.85)), url('{{ asset('storage/' . \App\Models\Setting::get('banner_news')) }}');" @endif>
        <span class="px-3.5 py-1.5 rounded-full bg-blue-500/20 border border-blue-500/30 text-blue-400 text-xs font-extrabold uppercase tracking-wider">
            Legal & Compliance
        </span>
        <h1 class="hero-title font-extrabold tracking-tight">Terms & Conditions</h1>
        <p class="body-text text-slate-300 max-w-2xl mx-auto">
            Terms of application, admission selection criteria, and payment integrity clauses.
        </p>
    </section>

    <section class="py-20 bg-slate-50/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-8 sm:p-12 rounded-3xl border border-slate-200 shadow-xl max-w-4xl mx-auto">
                <div class="prose max-w-none text-xs text-slate-600 leading-relaxed space-y-4
                            [&>h2]:text-sm [&>h2]:font-black [&>h2]:uppercase [&>h2]:tracking-wider [&>h2]:text-slate-950 [&>h2]:mt-8 [&>h2]:mb-3 [&>h2]:border-b [&>h2]:border-slate-100 [&>h2]:pb-2
                            [&>p]:mb-4 [&>p]:leading-relaxed
                            [&>ul]:list-disc [&>ul]:pl-5 [&>ul]:space-y-2 [&>ul]:mb-4">
                    {!! $content !!}
                </div>
            </div>
        </div>
    </section>

</x-public-layout>
