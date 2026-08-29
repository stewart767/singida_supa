<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consent Audit Log Report - {{ $refNumber }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif, 'DejaVu Sans';
            font-size: 10px;
            color: #0f172a;
            line-height: 1.5;
            background: #f8fafc;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 100%;
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
            padding: 10px 22px;
            font-size: 11px;
            font-weight: 800;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
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

        .meta-grid {
            display: grid;
            grid-template-cols: 1fr 1fr;
            margin-bottom: 20px;
            padding: 12px 18px;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 700;
            color: #334155;
        }
        .meta-right {
            text-align: right;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            text-align: left;
        }
        .report-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 9px;
            padding: 10px 8px;
            border: 1px solid #334155;
            letter-spacing: 0.5px;
        }
        .report-table td {
            padding: 8px;
            border: 1px solid #e2e8f0;
            font-weight: 600;
        }
        .report-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .mono {
            font-family: monospace;
            font-size: 9px;
        }
        .footer-sec {
            margin-top: 40px;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 9px;
            color: #64748b;
            font-weight: 700;
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
                <span style="font-weight:800; font-size:12px;">Data Consent Audit Log Report</span>
            </div>
            <div>
                <button onclick="window.print()" class="btn-print">
                    Print PDF Report
                </button>
            </div>
        </div>

        <!-- Institutional Letterhead -->
        <div class="letterhead">
            @if(!empty($logos['sttc_logo']))
                <img src="{{ $logos['sttc_logo'] }}" class="logo-img" alt="STTC">
            @else
                <div style="width: 60px;"></div>
            @endif

            <div class="header-title">
                <h1>{{ $logos['university_name'] }}</h1>
                <h2>JOINT OUT-STTC ADMISSION & UDAHILI COMPLIANCE</h2>
                <p>Personal Data Protection Act, 2022 Compliance Registry</p>
            </div>

            @if(!empty($logos['out_logo']))
                <img src="{{ $logos['out_logo'] }}" class="logo-img" alt="OUT">
            @else
                <div style="width: 60px;"></div>
            @endif
        </div>

        <!-- Metadata Grid -->
        <div class="meta-grid">
            <div>
                REPORT TYPE: PRIVACY & PERSONAL DATA CONSENT AUDIT
            </div>
            <div class="meta-right">
                GENERATED: {{ $generatedAt }} | REF: {{ $refNumber }}
            </div>
        </div>

        <!-- Table -->
        <table class="report-table">
            <thead>
                <tr>
                    <th>Applicant Name</th>
                    <th>Email Address</th>
                    <th>Application No.</th>
                    <th>Policy Version</th>
                    <th>Terms Version</th>
                    <th>Language</th>
                    <th>Device & OS</th>
                    <th>IP Address</th>
                    <th>Timestamp</th>
                    <th>Digital Consent Hash</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->application->applicant->user->name ?? 'N/A' }}</td>
                        <td>{{ $log->application->applicant->user->email ?? 'N/A' }}</td>
                        <td style="color: #1e3a8a; font-weight:800;">{{ $log->application->application_number ?? 'N/A' }}</td>
                        <td>v{{ $log->privacyPolicy->version ?? $log->consent_version }}</td>
                        <td>v{{ $log->termsCondition->version ?? $log->consent_version }}</td>
                        <td>{{ strtoupper($log->consent_language) }}</td>
                        <td>{{ $log->device_type }} ({{ $log->browser_name }}/{{ $log->operating_system }})</td>
                        <td class="mono">{{ $log->ip_address }}</td>
                        <td>{{ $log->consented_at ? $log->consented_at->format('d M Y, h:i A') : 'N/A' }}</td>
                        <td class="mono" style="color: #475569;">{{ substr($log->consent_hash, 0, 12) }}...</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 20px; color: #64748b;">No consent records matching the filters were found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer-sec">
            <div>
                SUPA Joint Admission Portal Compliance Registry System
            </div>
            <div>
                Page 1 of 1 (Filtered Export)
            </div>
        </div>

    </div>

</body>
</html>
