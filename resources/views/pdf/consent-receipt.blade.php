<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consent Receipt - {{ $consent->application->application_number ?? $consent->user->name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            color: #0f172a;
            line-height: 1.5;
            background: #f8fafc;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px 35px;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            position: relative;
        }
        .no-print-bar {
            background: #0f172a;
            color: #fff;
            padding: 12px 24px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 14px rgba(0,0,0,0.15);
        }
        .btn-print {
            background: #f59e0b;
            color: #0f172a;
            border: none;
            padding: 8px 18px;
            font-size: 11px;
            font-weight: 800;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print:hover {
            background: #d97706;
            color: #ffffff;
        }
        .letterhead {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px double #1e3a8a;
            padding-bottom: 15px;
            margin-bottom: 20px;
            gap: 15px;
        }
        .logo-img {
            height: 60px;
            max-width: 90px;
            object-fit: contain;
        }
        .header-title {
            text-align: center;
            flex-grow: 1;
        }
        .header-title h1 {
            font-size: 14px;
            margin: 0;
            color: #1e3a8a;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-title h2 {
            font-size: 11px;
            margin: 3px 0;
            color: #0f172a;
            font-weight: 700;
        }
        .header-title p {
            margin: 2px 0 0 0;
            font-size: 9px;
            color: #475569;
        }
        .meta-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 10px 16px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-size: 10px;
        }
        .meta-bar div strong {
            color: #1e3a8a;
        }
        .section-title {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            color: #1e3a8a;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
            margin-bottom: 12px;
            letter-spacing: 0.5px;
        }
        .grid {
            display: grid;
            grid-template-cols: 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }
        .row {
            margin-bottom: 6px;
            display: flex;
        }
        .label {
            width: 140px;
            font-weight: 700;
            color: #475569;
        }
        .value {
            flex-grow: 1;
            font-weight: 600;
            color: #0f172a;
        }
        .declaration-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            font-size: 10px;
            color: #334155;
            line-height: 1.6;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 35px;
            padding-top: 15px;
            border-top: 1px solid #f1f5f9;
        }
        .signature-box {
            text-align: center;
            width: 250px;
        }
        .signature-img {
            max-height: 50px;
            max-width: 220px;
            object-fit: contain;
            display: block;
            margin: 0 auto 5px auto;
        }
        .hash-box {
            background-color: #f1f5f9;
            border: 1px dashed #cbd5e1;
            padding: 10px;
            border-radius: 6px;
            font-family: monospace;
            font-size: 9.5px;
            color: #475569;
            word-break: break-all;
            margin-top: 20px;
            text-align: center;
        }
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .container {
                box-shadow: none;
                border: none;
                padding: 0;
            }
            .no-print-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="container">
        
        <!-- Print Header Bar (Hidden in Print) -->
        <div class="no-print-bar">
            <div>
                <span style="font-weight:800; font-size:12px;">Data Consent Audit Receipt</span>
            </div>
            <div>
                <button onclick="window.print()" class="btn-print">
                    Print Consent Receipt
                </button>
            </div>
        </div>

        <!-- Institutional Letterhead -->
        <div class="letterhead">
            @if(\App\Models\Setting::get('sttc_logo'))
                <img src="{{ asset('storage/' . \App\Models\Setting::get('sttc_logo')) }}" class="logo-img" alt="STTC">
            @else
                <div style="width: 60px;"></div>
            @endif

            <div class="header-title">
                <h1>{{ \App\Models\Setting::get('university_name', "SINGIDA TEACHERS' TRAINING COLLEGE (STTC)") }}</h1>
                <h2>JOINT OUT-STTC ADMISSION & UDAHILI COMPLIANCE</h2>
                <p>Personal Data Protection Act, 2022 Compliance Registry</p>
            </div>

            @if(\App\Models\Setting::get('out_logo'))
                <img src="{{ asset('storage/' . \App\Models\Setting::get('out_logo')) }}" class="logo-img" alt="OUT">
            @else
                <div style="width: 60px;"></div>
            @endif
        </div>

        <!-- Metadata Bar -->
        <div class="meta-bar">
            <div>
                Consent Ref: <strong>SUPA/CONSENT/{{ date('Ymd', strtotime($consent->consented_at)) }}/{{ substr($consent->consent_hash, 0, 8) }}</strong>
            </div>
            <div>
                Generated On: <strong>{{ now()->format('d M Y, h:i A') }}</strong>
            </div>
        </div>

        <!-- Section 1: Applicant Info -->
        <div class="section-title">1. Applicant & Program Identification</div>
        <div class="grid">
            <div>
                <div class="row">
                    <div class="label">Applicant Name:</div>
                    <div class="value">{{ $consent->user->name ?? 'N/A' }}</div>
                </div>
                <div class="row">
                    <div class="label">Applicant Email:</div>
                    <div class="value">{{ $consent->user->email ?? 'N/A' }}</div>
                </div>
                <div class="row">
                    <div class="label">Applicant Phone:</div>
                    <div class="value">{{ $consent->user->phone ?? 'N/A' }}</div>
                </div>
            </div>
            <div>
                <div class="row">
                    <div class="label">Application Number:</div>
                    <div class="value">{{ $consent->application->application_number ?? 'Pre-Application' }}</div>
                </div>
                <div class="row">
                    <div class="label">Program Applied:</div>
                    <div class="value">
                        @if($consent->application)
                            {{ $consent->application->programme->code ?? 'N/A' }} - {{ $consent->application->programme->name ?? 'N/A' }}
                        @else
                            Pre-Application
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="label">Admission Category:</div>
                    <div class="value">{{ $consent->application->admission_category ?? 'Pre-Application' }}</div>
                </div>
            </div>
        </div>

        <!-- Section 2: Audit Logs -->
        <div class="section-title">2. Compliance & Consent Details</div>
        <div class="grid">
            <div>
                <div class="row">
                    <div class="label">Privacy Policy Version:</div>
                    <div class="value">{{ $consent->privacyPolicy->version ?? $consent->consent_version }}</div>
                </div>
                <div class="row">
                    <div class="label">Terms & Conditions:</div>
                    <div class="value">{{ $consent->termsCondition->version ?? $consent->consent_version }}</div>
                </div>
                <div class="row">
                    <div class="label">Language & Source:</div>
                    <div class="value">{{ strtoupper($consent->consent_language) }} / {{ $consent->consent_source }}</div>
                </div>
            </div>
            <div>
                <div class="row">
                    <div class="label">IP Address:</div>
                    <div class="value">{{ $consent->ip_address }}</div>
                </div>
                <div class="row">
                    <div class="label">Browser & OS:</div>
                    <div class="value">{{ $consent->browser_name }} on {{ $consent->operating_system }}</div>
                </div>
                <div class="row">
                    <div class="label">Consent Date:</div>
                    <div class="value">{{ $consent->consented_at ? $consent->consented_at->format('d M Y, h:i:s A') : 'N/A' }}</div>
                </div>
            </div>
        </div>

        <!-- Section 3: Declaration Accepted -->
        <div class="section-title">3. Compliance Declarations Accepted</div>
        <div class="declaration-box">
            <strong>Declarations Confirmed by Applicant:</strong><br>
            ✓ I confirm that the information provided is true and accurate.<br>
            ✓ I have read and understood the Privacy Policy (Version {{ $consent->privacyPolicy->version ?? $consent->consent_version }}).<br>
            ✓ I have read and accepted the Terms & Conditions (Version {{ $consent->termsCondition->version ?? $consent->consent_version }}).<br>
            ✓ I consent to the collection, storage, verification, processing and sharing of my personal data for admission and academic administration purposes in accordance with the Personal Data Protection Act, 2022.<br>
            ✓ I understand that submitting false information may lead to rejection or cancellation of my admission.
        </div>

        <!-- Signature section -->
        <div class="signature-section">
            <div class="signature-box">
                @if($consent->application && isset($consent->application->digital_signature_path) && file_exists(storage_path('app/public/' . $consent->application->digital_signature_path)))
                    <img src="{{ asset('storage/' . $consent->application->digital_signature_path) }}" class="signature-img" alt="Digital Signature">
                @else
                    <div style="font-family:'Dancing Script', cursive, sans-serif; font-size:20px; font-weight:bold; color:#1e3a8a; margin-bottom:10px;">
                        {{ $consent->user->name ?? '' }}
                    </div>
                @endif
                <div style="border-top:1px solid #94a3b8; font-size:10px; font-weight:700; color:#475569; padding-top:4px;">
                    Digital Signature (Applicant)
                </div>
            </div>

            <div class="signature-box" style="text-align:right;">
                @if(\App\Models\Setting::get('official_seal'))
                    <img src="{{ asset('storage/' . \App\Models\Setting::get('official_seal')) }}" class="signature-img" style="max-height:60px;" alt="Official Seal">
                @endif
                <div style="font-size:10px; font-weight:700; color:#475569; padding-top:4px;">
                    Official Institutional Verification Seal
                </div>
            </div>
        </div>

        <!-- Digital Integrity Hash -->
        <div class="hash-box">
            <strong>Consent Integrity SHA256 Signature Verification Hash:</strong><br>
            {{ $consent->consent_hash }}
        </div>

    </div>

</body>
</html>
