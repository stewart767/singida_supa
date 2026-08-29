<x-app-layout title="Edit Privacy Policy Draft">
    <x-slot name="header">Edit Privacy Policy Draft (v{{ $policy->version }})</x-slot>

    <div class="max-w-4xl mx-auto bg-white p-8 rounded-3xl border border-slate-200 shadow-xl space-y-6">
        <div class="flex justify-between items-center border-b border-slate-100 pb-4">
            <div class="space-y-1">
                <h3 class="text-lg font-extrabold text-slate-900">Privacy Policy Revision Form</h3>
                <p class="text-xs text-slate-500">Edit version numbers, language, text content or upload an official supporting document.</p>
            </div>
            <a href="{{ route('admin.compliance.index') }}" class="px-4 py-2 bg-slate-100 text-slate-700 text-xs font-extrabold rounded-xl hover:bg-slate-200 transition-all">&larr; Back to Compliance Portal</a>
        </div>

        <form action="{{ route('admin.compliance.privacy.update', $policy->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs font-semibold" x-data="{ docType: '{{ $policy->file_path ? 'file' : ($policy->content ? 'text' : 'file') }}', fileName: '' }">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-slate-700 uppercase mb-1">Version</label>
                    <input type="text" name="version" value="{{ old('version', $policy->version) }}" required class="w-full px-4 py-3 rounded-xl border border-slate-300 bg-slate-50 text-slate-900 outline-none focus:ring-2 focus:ring-amber-500">
                    @error('version') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-slate-700 uppercase mb-1">Language</label>
                    <select name="language" required class="w-full px-4 py-3 rounded-xl border border-slate-300 bg-slate-50 text-slate-900 outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="en" {{ old('language', $policy->language) === 'en' ? 'selected' : '' }}>English (en)</option>
                        <option value="sw" {{ old('language', $policy->language) === 'sw' ? 'selected' : '' }}>Swahili (sw)</option>
                    </select>
                    @error('language') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-slate-700 uppercase mb-1">Document Title</label>
                <input type="text" name="title" value="{{ old('title', $policy->title) }}" required class="w-full px-4 py-3 rounded-xl border border-slate-300 bg-slate-50 text-slate-900 outline-none focus:ring-2 focus:ring-amber-500">
                @error('title') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-3 pt-1">
                <label class="block text-slate-900 font-extrabold mb-1">Document Content Source</label>
                <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100/80 rounded-xl">
                    <button type="button" @click="docType = 'file'" :class="docType === 'file' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/50' : 'text-slate-500 hover:text-slate-800'" class="py-2.5 text-[10px] font-black rounded-lg transition-all flex items-center justify-center gap-1.5 uppercase tracking-wider">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        PDF File Upload
                    </button>
                    <button type="button" @click="docType = 'text'" :class="docType === 'text' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/50' : 'text-slate-500 hover:text-slate-800'" class="py-2.5 text-[10px] font-black rounded-lg transition-all flex items-center justify-center gap-1.5 uppercase tracking-wider">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Direct Text Input
                    </button>
                </div>

                <!-- PDF File Upload Panel -->
                <div x-show="docType === 'file'" x-transition class="space-y-2">
                    @if($policy->file_path)
                        <div class="p-3 bg-blue-50/70 border border-blue-200/50 text-blue-900 rounded-xl mb-3 flex justify-between items-center">
                            <span class="text-[11px] font-bold">📄 Current File: {{ basename($policy->file_path) }}</span>
                            <a href="{{ asset('storage/' . $policy->file_path) }}" target="_blank" class="underline hover:text-blue-950 font-black">View File</a>
                        </div>
                    @endif
                    <div class="relative group border-2 border-dashed border-slate-300 hover:border-amber-500 rounded-2xl p-5 bg-slate-50/50 transition-all flex flex-col items-center justify-center text-center cursor-pointer min-h-[140px]">
                        <input type="file" name="consent_file" accept=".pdf" @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <svg class="w-8 h-8 text-slate-400 group-hover:text-amber-500 transition-colors mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <span class="text-[11px] font-extrabold text-slate-700">Choose PDF Document or Drag here</span>
                        <span class="text-[9px] text-slate-400 mt-0.5">Maximum size: 10MB</span>
                    </div>
                    @error('consent_file') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    <div x-show="fileName" class="text-[10px] text-slate-700 font-extrabold bg-blue-50/70 border border-blue-200/50 rounded-xl px-3 py-2 flex items-center justify-between shadow-sm" x-cloak>
                        <span class="truncate max-w-[200px]" x-text="'Selected: ' + fileName"></span>
                        <button type="button" @click="fileName = ''; $el.closest('form').querySelector('input[type=file]').value = ''" class="text-red-500 hover:text-red-700 font-black text-xs px-1">✕</button>
                    </div>
                </div>

                <!-- Direct Text Input Panel -->
                <div x-show="docType === 'text'" x-transition class="space-y-2">
                    <textarea name="content" rows="12" placeholder="Type or paste document HTML or text content here..." class="w-full px-4 py-3 rounded-xl border border-slate-300 bg-slate-50 text-slate-900 outline-none focus:ring-2 focus:ring-amber-500 font-mono text-[11px] font-medium leading-relaxed">{{ old('content', $policy->content) }}</textarea>
                    @error('content') <span class="text-red-500 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-4 border-t border-slate-100 pt-4">
                <a href="{{ route('admin.compliance.index') }}" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold rounded-xl transition-all">Cancel</a>
                <button type="submit" class="gradient-btn px-8 py-3 rounded-xl text-white font-extrabold shadow-md">Save Changes</button>
            </div>
        </form>
    </div>
</x-app-layout>
