<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Letter - {{ $letter->admission_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 15mm 20mm 20mm 20mm;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif, 'DejaVu Sans';
            font-size: 11.5px;
            color: #0f172a;
            line-height: 1.6;
            background: #f8fafc;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 850px;
            margin: 0 auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            position: relative;
        }
        .no-print-bar {
            background: #0f172a;
            color: #fff;
            padding: 12px 24px;
            border-radius: 10px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 14px rgba(0,0,0,0.15);
        }
        .btn-print {
            background: #2563eb;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            font-size: 12px;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
        }
        .btn-print:hover {
            background: #1d4ed8;
        }
        
        /* Header & Crest */
        .letterhead {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px double #1e3a8a;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .crest-logo {
            width: 75 h-75;
            height: 75px;
            border-radius: 50%;
            background: #1e3a8a;
            color: #fbbf24;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 900;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .header-title {
            text-align: center;
            flex-grow: 1;
            padding: 0 15px;
        }
        .header-title h1 {
            font-size: 16px;
            margin: 0;
            color: #1e3a8a;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-title h2 {
            font-size: 12.5px;
            margin: 3px 0;
            color: #0f172a;
            font-weight: 700;
        }
        .header-title p {
            margin: 2px 0 0 0;
            font-size: 10px;
            color: #475569;
        }

        .meta-bar {
            display: flex;
            justify-content: space-between;
            background: #f1f5f9;
            padding: 10px 15px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 20px;
            border: 1px solid #cbd5e1;
        }

        .grant-banner {
            background: linear-gradient(135deg, #064e3b 0%, #047857 100%);
            color: #ffffff;
            text-align: center;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(4, 120, 87, 0.2);
        }
        .grant-banner h3 {
            margin: 0;
            font-size: 15px;
            font-weight: 900;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .grant-banner p {
            margin: 3px 0 0 0;
            font-size: 11px;
            opacity: 0.9;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 11px;
        }
        .details-table td {
            padding: 8px 12px;
            border: 1px solid #cbd5e1;
        }
        .details-table td.label {
            width: 32%;
            background-color: #f8fafc;
            font-weight: 700;
            color: #334155;
        }
        .details-table td.value {
            font-weight: 700;
            color: #0f172a;
        }

        .content-body {
            font-size: 11.5px;
            color: #1e293b;
            margin-bottom: 25px;
        }
        .content-body p {
            margin-bottom: 12px;
            text-align: justify;
        }

        .instruction-box {
            background: #f8fafc;
            border-left: 4px solid #2563eb;
            padding: 12px 16px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
            font-size: 10.5px;
        }
        .instruction-box ul {
            margin: 5px 0 0 0;
            padding-left: 18px;
        }
        .instruction-box li {
            margin-bottom: 4px;
        }

        /* Sign-off & Verification Bar */
        .signoff-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 35px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }
        .signature-box {
            text-align: left;
        }
        .stamp-mark {
            width: 100px;
            height: 100px;
            border: 2px dashed #059669;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #059669;
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            text-align: center;
            padding: 5px;
            transform: rotate(-10deg);
            opacity: 0.85;
        }
        .qr-verify-box {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 10px 14px;
            border-radius: 8px;
            text-align: center;
            font-size: 9.5px;
            max-width: 220px;
        }
        .qr-verify-box strong {
            display: block;
            font-size: 10.5px;
            color: #1e3a8a;
            margin-bottom: 4px;
        }

        .watermark {
            position: absolute;
            top: 55%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 75px;
            font-weight: 900;
            color: rgba(30, 58, 138, 0.04);
            pointer-events: none;
            text-transform: uppercase;
            white-space: nowrap;
            user-select: none;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .container {
                box-shadow: none;
                border: none;
                padding: 0;
                max-width: 100%;
            }
            .no-print-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        
        <!-- Top Navigation Bar for Browser Viewing (Hidden on Print) -->
        <div class="no-print-bar">
            <div>
                <span style="font-weight: 700; font-size: 14px;">Official Admission Letter</span>
                <span style="font-size: 11px; opacity: 0.8; display: block;">Verification Code: {{ $letter->verification_code }}</span>
            </div>
            <button onclick="window.print()" class="btn-print">
                🖨️ Print / Save Official Admission PDF
            </button>
        </div>

        <!-- Watermark -->
        <div class="watermark">STTC & OUT ADMISSION</div>

        <!-- Official Letterhead Header -->
        <div class="letterhead">
            @if(\App\Models\Setting::get('sttc_logo'))
                <img src="{{ asset('storage/' . \App\Models\Setting::get('sttc_logo')) }}" alt="STTC Logo" style="height: 75px; max-width: 110px; object-fit: contain;">
            @else
                <div class="crest-logo">S</div>
            @endif
            <div class="header-title">
                <h1>{{ \App\Models\Setting::get('university_name', "SINGIDA TEACHERS' TRAINING COLLEGE (STTC)") }}</h1>
                <h2>IN COLLABORATION WITH THE OPEN UNIVERSITY OF TANZANIA (OUT)</h2>
                <p>P.O. Box 240, Singida, Tanzania | Phone: +255 26 232 2098 | Email: admissions@singidattc.ac.tz</p>
                <p style="font-weight: bold; color: #1e3a8a; margin-top: 3px;">DIRECTORATE OF ADMISSIONS & ACADEMIC SUPPORT (SUPA)</p>
            </div>
            @if(\App\Models\Setting::get('out_logo'))
                <img src="{{ asset('storage/' . \App\Models\Setting::get('out_logo')) }}" alt="OUT Logo" style="height: 75px; max-width: 110px; object-fit: contain;">
            @else
                <div class="crest-logo" style="background: #065f46; color: #fff;">OUT</div>
            @endif
        </div>

        <!-- Letter Metadata Bar -->
        <div class="meta-bar">
            <div><strong>Ref No:</strong> <span style="color: #1e3a8a;">{{ $letter->admission_number }}</span></div>
            <div><strong>Date of Issue:</strong> {{ $letter->generated_at ? $letter->generated_at->format('d F Y') : now()->format('d F Y') }}</div>
            <div><strong>Verification Hash:</strong> <span style="font-family: monospace;">{{ $letter->verification_code }}</span></div>
        </div>

        <!-- Grant Banner -->
        <div class="grant-banner">
            <h3>🎉 OFFICIAL ADMISSION OFFER LETTER</h3>
            <p>ACADEMIC YEAR 2026/2027 INTAKE</p>
        </div>

        <!-- Content Body -->
        <div class="content-body">
            <p><strong>To:</strong> {{ $letter->application->applicant->user->name }}<br>
            <strong>Email:</strong> {{ $letter->application->applicant->user->email }}<br>
            <strong>Phone:</strong> {{ $letter->application->applicant->user->phone ?? 'N/A' }}</p>

            <p><strong>Dear Student,</strong></p>

            <p>
                We are pleased to inform you that following your application and successful verification of your academic credentials, 
                the Admission Board of <strong>Singida Teachers' Training College (STTC)</strong> in partnership with 
                <strong>The Open University of Tanzania (OUT)</strong> has granted you official admission under the SUPA Distance Learning System for the 2026/2027 Academic Year.
            </p>

            <!-- Admission Details Table -->
            <table class="details-table">
                <tr>
                    <td class="label">Admission Control Number:</td>
                    <td class="value" style="color: #2563eb; font-family: monospace;">{{ $letter->admission_number }}</td>
                </tr>
                <tr>
                    <td class="label">Programme Admitted Into:</td>
                    <td class="value" style="font-size: 12px; color: #0f172a;">{{ $letter->application->programme->name }} ({{ $letter->application->programme->code }})</td>
                </tr>
                <tr>
                    <td class="label">Admission Category:</td>
                    <td class="value">
                        <span style="color: #065f46; font-weight: 800;">{{ $letter->application->admission_category }}</span>
                        ({{ $letter->application->admission_category === 'Direct Entry' ? 'Direct Degree Admission - OUT' : 'Foundation Bridging Programme - STTC/SUPA' }})
                    </td>
                </tr>
                <tr>
                    <td class="label">Study Duration & Mode:</td>
                    <td class="value">3 Academic Years | Open & Distance Learning (ODL / LMS + Face-to-Face)</td>
                </tr>
                <tr>
                    <td class="label">Official Reporting Date:</td>
                    <td class="value" style="color: #d97706;">{{ $letter->reporting_date ? $letter->reporting_date->format('d F Y') : '25 September 2026' }}</td>
                </tr>
                <tr>
                    <td class="label">Study Center & Location:</td>
                    <td class="value">STTC Main Campus / Halmashauri Regional Learning Center</td>
                </tr>
            </table>

            <!-- Important Instructions Box -->
            <div class="instruction-box">
                <strong style="color: #1e3a8a; text-transform: uppercase; font-size: 11px;">📌 Important Admission Conditions & Next Steps:</strong>
                <ul>
                    <li><strong>Orientation & Registration:</strong> Orientation will begin on <strong>10 September 2026</strong> across all Halmashauri regional study centers.</li>
                    <li><strong>Fee Payment Schedule:</strong> Tuition fees may be paid in 10 monthly installments of <strong>TZS 155,000/=</strong> per month via your generated Control Number.</li>
                    <li><strong>Academic Support:</strong> You will receive full access to SUPA Digital Learning (LMS), Academic English Enhancement, and Exam Preparation mentorship.</li>
                    <li><strong>Medical Clearance:</strong> Please ensure you submit your completed Medical Clearance Form upon physical orientation reporting.</li>
                </ul>
            </div>

            <p>
                Please accept our congratulations on your admission. We look forward to welcoming you to STTC and OUT as you pursue academic excellence.
            </p>
        </div>

        <!-- Sign-off & Verification Footer Section -->
        <div class="signoff-section">
            <div class="signature-box">
                <p style="margin: 0; font-size: 11px; font-weight: bold; color: #475569;">Yours Sincerely,</p>
                @if(\App\Models\Setting::get('registrar_signature'))
                    <img src="{{ asset('storage/' . \App\Models\Setting::get('registrar_signature')) }}" alt="Registrar Signature" style="height: 48px; margin: 4px 0; object-fit: contain; display: block;">
                @else
                    <div style="margin: 8px 0; height: 35px; font-serif italic font-bold font-size: 16px; color: #1e3a8a;">
                        Dr. M. K. Rashidi
                    </div>
                @endif
                <p style="margin: 0; font-weight: 800; color: #0f172a; text-transform: uppercase; font-size: 10.5px;">Director of Admissions & Academic Registrar</p>
                <p style="margin: 0; font-size: 10px; color: #64748b;">Singida Teachers' Training College & OUT SUPA Office</p>
            </div>

            <!-- Official Stamp -->
            @if(\App\Models\Setting::get('official_seal'))
                <img src="{{ asset('storage/' . \App\Models\Setting::get('official_seal')) }}" alt="Official Admission Seal" style="height: 90px; width: 90px; object-fit: contain; transform: rotate(-10deg);">
            @else
                <div class="stamp-mark">
                    <span>STTC & OUT</span>
                    <span style="font-size: 7.5px; margin: 2px 0;">ADMISSION OFFICE</span>
                    <span>APPROVED</span>
                </div>
            @endif

            <!-- QR Verification Box -->
            <div class="qr-verify-box">
                <strong>🔒 OFFICIAL VERIFICATION</strong>
                Scan or verify online at:<br>
                <span style="font-family: monospace; font-size: 9px; font-weight: bold; color: #0f172a;">www.singidattc.ac.tz/track</span><br>
                <span style="color: #64748b; font-size: 8.5px;">Hash: {{ substr($letter->verification_code, 0, 12) }}...</span>
            </div>
        </div>

    </div>

</body>
</html>
