<x-app-layout title="Document Preview - {{ $type }}">
    <x-slot name="header">Document Preview: {{ $type }} (v{{ $document->version }})</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex justify-between items-center bg-white p-6 rounded-3xl border border-slate-200 shadow-md">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">{{ $document->title }}</h3>
                <div class="flex items-center gap-4 text-xs text-slate-500 font-bold mt-1">
                    <span>Version: <strong class="text-slate-800">{{ $document->version }}</strong></span>
                    <span>Language: <strong class="text-slate-800">{{ strtoupper($document->language ?? 'en') }}</strong></span>
                    <span>Status: <strong class="text-amber-500">{{ $document->status }}</strong></span>
                    <span>Effective: <strong class="text-slate-800">{{ $document->effective_date ?? 'Not Set' }}</strong></span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.compliance.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 text-xs font-extrabold rounded-xl hover:bg-slate-200 transition-all">&larr; Back</a>
                @if($document->file_path)
                    <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="gradient-btn-gold px-4 py-2 rounded-xl text-slate-950 text-xs font-black shadow-md">📥 Download PDF</a>
                @endif
                <button onclick="window.print()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-extrabold rounded-xl shadow-md">🖨️ Print Document</button>
            </div>
        </div>

        <div class="bg-white p-8 sm:p-12 rounded-3xl border border-slate-200 shadow-xl print:shadow-none print:border-none">
            @if($document->content)
                <div class="prose max-w-none text-xs text-slate-700 leading-relaxed font-medium space-y-4
                            [&>h2]:text-sm [&>h2]:font-black [&>h2]:uppercase [&>h2]:tracking-wider [&>h2]:text-slate-950 [&>h2]:mt-8 [&>h2]:mb-3 [&>h2]:border-b [&>h2]:border-slate-100 [&>h2]:pb-2
                            [&>p]:mb-4 [&>p]:leading-relaxed
                            [&>ul]:list-disc [&>ul]:pl-5 [&>ul]:space-y-2 [&>ul]:mb-4">
                    {!! $document->content !!}
                </div>
            @else
                <div class="text-center py-16 space-y-4">
                    <div class="w-16 h-16 bg-blue-50 text-blue-800 rounded-full flex items-center justify-center mx-auto border border-blue-100">
                        <span class="text-2xl">📄</span>
                    </div>
                    <h4 class="text-sm font-extrabold text-slate-900">PDF Supporting Document Only</h4>
                    <p class="text-xs text-slate-500 max-w-md mx-auto">This version is backed by an uploaded document only and has no inline text. You can view or download the file using the button below:</p>
                    @if($document->file_path)
                        <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="gradient-btn px-6 py-2.5 rounded-xl text-white font-extrabold text-xs inline-block">
                            View Uploaded File ({{ basename($document->file_path) }})
                        </a>
                    @else
                        <span class="text-red-500 font-extrabold block text-xs">Error: Supporting document file is missing.</span>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
