<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportTitle }} - {{ $refNumber }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif, 'DejaVu Sans';
            font-size: 11px;
            color: #0f172a;
            line-height: 1.5;
            background: #f8fafc;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
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
            font-size: 12px;
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
        
        /* Header & Institutional Logos */
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
            height: 75px;
            max-width: 110px;
            object-fit: contain;
        }
        .crest-logo {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #1e3a8a;
            color: #fbbf24;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 900;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .header-title {
            text-align: center;
            flex-grow: 1;
        }
        .header-title h1 {
            font-size: 15px;
            margin: 0;
            color: #1e3a8a;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-title h2 {
            font-size: 12px;
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
            align-items: center;
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 10px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 10.5px;
        }
        .meta-bar div strong {
            color: #1e3a8a;
        }

        /* KPI Metric Cards */
        .metrics-grid {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
        }
        .metric-card {
            flex: 1;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 14px;
            text-align: center;
        }
        .metric-card .title {
            font-size: 9px;
            text-transform: uppercase;
            font-weight: 800;
            color: #64748b;
            letter-spacing: 0.5px;
        }
        .metric-card .value {
            font-size: 18px;
            font-weight: 900;
            color: #1e3a8a;
            margin-top: 2px;
        }

        /* Report Table */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10px;
        }
        .report-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 800;
            text-transform: uppercase;
            padding: 8px 10px;
            font-size: 9px;
            letter-spacing: 0.5px;
            border: 1px solid #0f172a;
            text-align: left;
        }
        .report-table td {
            padding: 7px 10px;
            border: 1px solid #cbd5e1;
            color: #1e293b;
        }
        .report-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 8.5px;
            font-weight: 800;
            text-transform: uppercase;
        }
        .badge-active, .badge-approved, .badge-paid {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-pending, .badge-review {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Signatures Footer */
        .signatures-area {
            margin-top: 35px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            page-break-inside: avoid;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 1px dashed #64748b;
            margin-top: 40px;
            padding-top: 4px;
            font-size: 10px;
            font-weight: 700;
            color: #334155;
        }
        .official-seal {
            width: 80px;
            height: 80px;
            object-fit: contain;
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
                <span style="font-weight: 800; font-size: 14px; color: #fbbf24;">📄 {{ $reportTitle }}</span>
                <span style="font-size: 11px; opacity: 0.8; display: block;">Official System PDF Report • Ref: {{ $refNumber }}</span>
            </div>
            <button onclick="window.print()" class="btn-print">
                🖨️ Download / Save PDF
            </button>
        </div>

        <!-- Official Letterhead Header with System Logo -->
        <div class="letterhead">
            @if(!empty($logos['sttc_logo']))
                <img src="{{ $logos['sttc_logo'] }}" alt="STTC Logo" class="logo-img">
            @elseif(!empty($logos['system_logo']))
                <img src="{{ $logos['system_logo'] }}" alt="System Logo" class="logo-img">
            @else
                <div class="crest-logo">STTC</div>
            @endif

            <div class="header-title">
                <h1>{{ $logos['university_name'] ?? "SINGIDA TEACHERS' TRAINING COLLEGE (STTC)" }}</h1>
                <h2>IN COLLABORATION WITH THE OPEN UNIVERSITY OF TANZANIA (OUT)</h2>
                <p>P.O. Box 240, Singida, Tanzania | Phone: +255 26 232 2098 | Email: admissions@singidattc.ac.tz</p>
                <p style="font-weight: 800; color: #1e3a8a; margin-top: 3px; font-size: 11px; text-transform: uppercase;">
                    DIRECTORATE OF ADMISSIONS & ACADEMIC SUPPORT (SUPA)
                </p>
            </div>

            @if(!empty($logos['out_logo']))
                <img src="{{ $logos['out_logo'] }}" alt="OUT Logo" class="logo-img">
            @else
                <div class="crest-logo" style="background: #065f46; color: #ffffff;">OUT</div>
            @endif
        </div>

        <!-- Meta Bar -->
        <div class="meta-bar">
            <div><strong>DOCUMENT:</strong> {{ strtoupper($reportTitle) }}</div>
            <div><strong>REF NO:</strong> {{ $refNumber }}</div>
            <div><strong>GENERATED AT:</strong> {{ $generatedAt }}</div>
        </div>

        <!-- KPI Executive Summary Cards -->
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="title">Total Records</div>
                <div class="value">{{ number_format($metrics['total_records'] ?? count($records)) }}</div>
            </div>
            @if($type === 'applications')
                <div class="metric-card">
                    <div class="title">Approved Udahili</div>
                    <div class="value" style="color: #059669;">{{ number_format($metrics['approved'] ?? 0) }}</div>
                </div>
                <div class="metric-card">
                    <div class="title">Under Review</div>
                    <div class="value" style="color: #d97706;">{{ number_format($metrics['pending'] ?? 0) }}</div>
                </div>
            @elseif($type === 'payments')
                <div class="metric-card">
                    <div class="title">Total Collections</div>
                    <div class="value" style="color: #059669;">TZS {{ number_format($metrics['total_amount'] ?? 0) }}</div>
                </div>
                <div class="metric-card">
                    <div class="title">Verified Receipts</div>
                    <div class="value" style="color: #2563eb;">{{ number_format($metrics['verified'] ?? 0) }}</div>
                </div>
            @elseif($type === 'admitted')
                <div class="metric-card">
                    <div class="title">Letters Issued</div>
                    <div class="value" style="color: #059669;">{{ number_format($metrics['total_records'] ?? count($records)) }}</div>
                </div>
                <div class="metric-card">
                    <div class="title">Degree Candidates</div>
                    <div class="value" style="color: #2563eb;">{{ number_format($metrics['degree_count'] ?? 0) }}</div>
                </div>
            @endif
        </div>

        <!-- Main Formatted Report Table -->
        <table class="report-table">
            <thead>
                @if($type === 'applications')
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 15%;">App Number</th>
                        <th style="width: 22%;">Applicant Name & Contact</th>
                        <th style="width: 12%;">Code</th>
                        <th style="width: 26%;">Sifa za Kujiunga</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 10%;">Date</th>
                    </tr>
                @elseif($type === 'payments')
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 20%;">Control Number</th>
                        <th style="width: 25%;">Applicant Name</th>
                        <th style="width: 18%;">Amount (TZS)</th>
                        <th style="width: 12%;">Currency</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 10%;">Date</th>
                    </tr>
                @elseif($type === 'admitted')
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 18%;">Admission No.</th>
                        <th style="width: 25%;">Student Full Name</th>
                        <th style="width: 10%;">Gender</th>
                        <th style="width: 22%;">Programme Admitted</th>
                        <th style="width: 10%;">Verification</th>
                        <th style="width: 10%;">Date</th>
                    </tr>
                @endif
            </thead>
            <tbody>
                @forelse($records as $index => $row)
                    @if($type === 'applications')
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td style="font-weight: 800; color: #1e3a8a;">{{ $row->application_number }}</td>
                            <td>
                                <strong>{{ $row->applicant->user->name ?? 'N/A' }}</strong><br>
                                <span style="font-size: 9px; color: #64748b;">{{ $row->applicant->user->email ?? 'N/A' }}</span>
                            </td>
                            <td style="font-weight: 800; color: #d97706;">{{ $row->programme->code ?? 'N/A' }}</td>
                            <td style="font-size: 9px; color: #334155;">{{ $row->programme->entry_requirements ?? 'Diploma GPA 3.0+ / Form VI' }}</td>
                            <td>
                                <span class="badge {{ strtolower($row->status) === 'approved' ? 'badge-approved' : (strtolower($row->status) === 'rejected' ? 'badge-rejected' : 'badge-pending') }}">
                                    {{ $row->status }}
                                </span>
                            </td>
                            <td>{{ $row->created_at ? $row->created_at->format('Y-m-d') : 'N/A' }}</td>
                        </tr>
                    @elseif($type === 'payments')
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td style="font-weight: 800; color: #1e3a8a;">{{ $row->control_number }}</td>
                            <td><strong>{{ $row->application->applicant->user->name ?? 'N/A' }}</strong></td>
                            <td style="font-weight: 800; color: #059669;">TZS {{ number_format($row->amount) }}</td>
                            <td>{{ $row->currency ?? 'TZS' }}</td>
                            <td>
                                <span class="badge {{ strtolower($row->payment_status) === 'paid' ? 'badge-paid' : 'badge-pending' }}">
                                    {{ strtoupper($row->payment_status) }}
                                </span>
                            </td>
                            <td>{{ $row->updated_at ? $row->updated_at->format('Y-m-d') : 'N/A' }}</td>
                        </tr>
                    @elseif($type === 'admitted')
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td style="font-weight: 800; color: #059669;">{{ $row->admission_number ?? ('SUPA/ADM/' . date('Y') . '/' . str_pad($row->id, 4, '0', STR_PAD_LEFT)) }}</td>
                            <td><strong>{{ $row->applicant->user->name ?? 'N/A' }}</strong></td>
                            <td>{{ $row->applicant->gender ?? 'N/A' }}</td>
                            <td style="font-weight: 700; color: #1e3a8a;">{{ $row->programme->name ?? 'Bachelor Degree' }}</td>
                            <td><span class="badge badge-approved">VERIFIED</span></td>
                            <td>{{ $row->updated_at ? $row->updated_at->format('Y-m-d') : date('Y-m-d') }}</td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px; color: #64748b;">No records found for this report.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Signatures & Verification Area -->
        <div class="signatures-area">
            <div class="signature-box">
                @if(!empty($logos['registrar_signature']))
                    <img src="{{ $logos['registrar_signature'] }}" alt="Registrar Signature" style="height: 40px; object-fit: contain;">
                @endif
                <div class="signature-line">
                    Prof. Josephat K.<br>
                    <span style="font-weight: 400; font-size: 9px; color: #64748b;">Academic Registrar, SUPA / OUT</span>
                </div>
            </div>

            @if(!empty($logos['official_seal']))
                <img src="{{ $logos['official_seal'] }}" alt="Official Seal" class="official-seal">
            @else
                <div style="border: 2px dashed #1e3a8a; border-radius: 50%; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; font-size: 8px; font-weight: 900; color: #1e3a8a; text-align: center; text-transform: uppercase;">
                    OFFICIAL<br>SEAL
                </div>
            @endif

            <div class="signature-box">
                <div class="signature-line">
                    Dr. Emmanuel M.<br>
                    <span style="font-weight: 400; font-size: 9px; color: #64748b;">Superadmin / Director of Admissions</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Auto-Print Script for Direct PDF Save -->
    @if(request()->has('download') || request()->has('print'))
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => window.print(), 500);
            });
        </script>
    @endif

</body>
</html>
