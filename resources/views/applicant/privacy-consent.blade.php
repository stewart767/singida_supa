<x-app-layout title="My Privacy & Consent">
    <x-slot name="header">My Privacy & Personal Data Consent Records</x-slot>

    <div class="max-w-4xl mx-auto space-y-8">
        <div class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-900 rounded-3xl p-6 border border-blue-950 text-white shadow-xl">
            <h2 class="text-base font-black text-white uppercase tracking-wider">Personal Data Protection Compliance (PDPA Act, 2022)</h2>
            <p class="text-xs text-blue-100 mt-2 leading-relaxed">
                As an applicant of Singida Teachers' Training College (STTC), you have rights regarding the personal data we collect. This portal displays the history of your consent decisions, the terms you accepted, and allows you to download an official signed Consent Receipt for auditing.
            </p>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
            <h3 class="font-extrabold text-slate-900 text-base">Historical Consent Logs</h3>
            <div class="divide-y divide-slate-100 text-xs font-semibold text-slate-700">
                @forelse($consents as $consent)
                    <div class="py-5 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-extrabold text-slate-900">Consent Provided for Application: <span class="text-blue-600">{{ $consent->application->application_number ?? 'N/A' }}</span></span>
                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase bg-emerald-100 text-emerald-800">
                                    ✓ Active
                                </span>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-2 pt-2 text-[11px] text-slate-500 font-medium">
                                <div>Privacy Version: <strong class="text-slate-800">{{ $consent->privacyPolicy->version ?? $consent->consent_version }}</strong></div>
                                <div>Terms Version: <strong class="text-slate-800">{{ $consent->termsCondition->version ?? $consent->consent_version }}</strong></div>
                                <div>IP Address: <strong class="text-slate-800">{{ $consent->ip_address }}</strong></div>
                                <div>Browser Used: <strong class="text-slate-800">{{ $consent->browser_name }}</strong></div>
                                <div>Device / OS: <strong class="text-slate-800">{{ $consent->device_type }} / {{ $consent->operating_system }}</strong></div>
                                <div>Accepted Date: <strong class="text-slate-800">{{ $consent->consented_at ? $consent->consented_at->format('d M Y, h:i A') : '' }}</strong></div>
                            </div>
                            <div class="pt-2">
                                <span class="text-[10px] text-slate-400 font-mono block truncate max-w-lg" title="Digital Integrity Hash">
                                    Hash: {{ $consent->consent_hash }}
                                </span>
                            </div>
                        </div>
                        <div class="shrink-0">
                            <a href="{{ route('applicant.privacy-consent.receipt', $consent->id) }}" target="_blank" class="gradient-btn-gold px-4 py-2.5 rounded-xl text-slate-950 font-black text-xs shadow-md inline-block hover:scale-105 transition-transform">
                                📄 Download Receipt
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400">
                        No historical consent records registered on your account yet. Complete your application submission to record your first consent.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
