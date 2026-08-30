<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admissions Report - {{ $refNumber }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 15mm 20mm 15mm;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif, 'DejaVu Sans';
            font-size: 11px;
            color: #1e293b;
            line-height: 1.5;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            position: relative;
        }
        
        /* Top Navigation Bar for Browser Viewing (Hidden on Print) */
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
        
        /* Header styling matching screenshots */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .logo-cell {
            width: 75px;
        }
        .title-cell {
            padding-left: 15px;
        }
        .meta-cell {
            text-align: right;
            font-size: 10px;
            color: #475569;
            line-height: 1.4;
        }
        .header-logo {
            width: 64px;
            height: 64px;
            object-fit: contain;
        }
        .header-logo-placeholder {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #1e3a8a;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 16px;
        }
        .header-title {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }
        .header-subtitle {
            font-size: 11px;
            font-weight: 800;
            color: #2563eb;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin: 4px 0 0 0;
            border-bottom: 2px solid #2563eb;
            display: inline-block;
            padding-bottom: 2px;
        }
        
        /* Report parameters details */
        .report-params {
            margin-bottom: 25px;
            font-size: 10.5px;
            color: #0f172a;
            line-height: 1.6;
        }
        .report-params div {
            margin-bottom: 2px;
        }
        
        /* Section Heading */
        .section-title {
            font-size: 11px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
            margin-top: 25px;
            margin-bottom: 12px;
            page-break-after: avoid;
        }
        
        /* KPI Bullet List */
        .kpi-list {
            list-style: none;
            padding-left: 0;
            margin: 0 0 20px 0;
        }
        .kpi-list li {
            position: relative;
            padding-left: 12px;
            margin-bottom: 5px;
            font-size: 11px;
        }
        .kpi-list li::before {
            content: "•";
            position: absolute;
            left: 0;
            color: #64748b;
        }
        
        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            page-break-inside: auto;
        }
        .data-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        .data-table th {
            background-color: #eff6ff;
            color: #334155;
            font-weight: 700;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 12px;
            border-bottom: 1px solid #bfdbfe;
            text-align: left;
        }
        .data-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
            font-size: 10px;
        }
        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .data-table .total-row td {
            background-color: #eff6ff !important;
            font-weight: 800;
            color: #1e3a8a;
            border-top: 1px solid #bfdbfe;
            border-bottom: 2px solid #bfdbfe;
            font-size: 10px;
        }
        
        /* Pagination/Footer styling for PDF rendering */
        .footer {
            position: fixed;
            bottom: -15mm;
            left: 0;
            right: 0;
            height: 30px;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 8px;
        }
        .footer-left {
            float: left;
            width: 33%;
        }
        .footer-center {
            float: left;
            width: 33%;
            text-align: center;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .footer-right {
            float: right;
            width: 33%;
            text-align: right;
        }
        
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        
        .page-number:after {
            content: counter(page);
        }

        @media print {
            .no-print-bar {
                display: none !important;
            }
            body {
                background: #fff;
            }
            .container {
                max-width: 100%;
            }
            .footer {
                position: fixed;
                bottom: -10mm;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        
        <!-- Top Navigation Bar for Browser Viewing (Hidden on Print) -->
        <div class="no-print-bar">
            <div>
                <span style="font-weight: 800; font-size: 14px; color: #fbbf24;">📄 Admissions Performance Report</span>
                <span style="font-size: 11px; opacity: 0.8; display: block;">Official System PDF Report • Ref: {{ $refNumber }}</span>
            </div>
            <button onclick="window.print()" class="btn-print">
                🖨️ Download / Save PDF
            </button>
        </div>

        <!-- Official Letterhead Header with Logo -->
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    @if(!empty($logos['sttc_logo']))
                        <img src="{{ $logos['sttc_logo'] }}" alt="STTC Logo" class="header-logo">
                    @elseif(!empty($logos['system_logo']))
                        <img src="{{ $logos['system_logo'] }}" alt="System Logo" class="header-logo">
                    @else
                        <div class="header-logo-placeholder">STTC</div>
                    @endif
                </td>
                <td class="title-cell">
                    <h1 class="header-title">{{ \App\Models\Setting::get('university_name', "Singida Teachers Training College") }}</h1>
                    <h2 class="header-subtitle">Admissions Report</h2>
                </td>
                <td class="meta-cell">
                    <strong>Date:</strong> {{ $generationDateFormatted }}<br>
                    <strong>Phone:</strong> {{ \App\Models\Setting::get('support_phone', "+255 123 456 789") }}<br>
                    <strong>Email:</strong> {{ \App\Models\Setting::get('support_email', "info@singidattc.ac.tz") }}<br>
                    <strong>Web:</strong> {{ \App\Models\Setting::get('support_web', "www.singidattc.ac.tz") }}
                </td>
            </tr>
        </table>

        <!-- Report Parameters -->
        <div class="report-params">
            <div><strong>Day:</strong> {{ $generationDay }}</div>
            <div><strong>Date:</strong> {{ $generationDate }}</div>
            <div><strong>Time:</strong> {{ $generationTime }}</div>
            <div><strong>Report Period:</strong> {{ $reportPeriodText }}</div>
        </div>

        <!-- APPLICATIONS SECTION -->
        <div class="section-title">Applications</div>
        <ul class="kpi-list">
            <li>Previous Total: <strong>{{ number_format($kpis['previous_total']) }}</strong></li>
            <li>New (Custom Period): <strong>{{ number_format($kpis['new_total']) }}</strong></li>
            <li>Total Applications: <strong>{{ number_format($kpis['total_applications']) }}</strong></li>
        </ul>

        <!-- PENDING SECTION -->
        <div class="section-title">Pending (Custom Period)</div>
        <ul class="kpi-list">
            <li>Pending (Custom Period): <strong>{{ number_format($kpis['pending_total']) }}</strong></li>
        </ul>

        <!-- TOP ENROLLED PROGRAMS SECTION -->
        <div class="section-title">Top Enrolled Programs</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 8%;">#</th>
                    <th style="width: 72%;">Program</th>
                    <th style="width: 20%; text-align: right;">Enrolled</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topPrograms as $index => $program)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $program['name'] }}</td>
                        <td style="text-align: right; font-weight: bold;">{{ number_format($program['count']) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #64748b;">No data found for this period.</td>
                    </tr>
                @endforelse
                @if(count($topPrograms) > 0)
                    <tr class="total-row">
                        <td colspan="2">TOTAL ENROLLED</td>
                        <td style="text-align: right;">{{ number_format($topProgramsTotal) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- REGIONAL PERFORMANCE SECTION -->
        <div class="section-title">Regional Performance</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 8%;">#</th>
                    <th style="width: 42%;">Region</th>
                    <th style="width: 25%; text-align: right;">Total</th>
                    <th style="width: 25%; text-align: right;">Percentage</th>
                </tr>
            </thead>
            <tbody>
                @forelse($regionalPerformance as $index => $region)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="font-weight: bold; text-transform: uppercase;">{{ $region['name'] }}</td>
                        <td style="text-align: right;">{{ number_format($region['count']) }}</td>
                        <td style="text-align: right;">{{ $region['percentage'] }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #64748b;">No data found for this period.</td>
                    </tr>
                @endforelse
                @if(count($regionalPerformance) > 0)
                    <tr class="total-row">
                        <td colspan="2">TOTAL</td>
                        <td style="text-align: right;">{{ number_format($kpis['new_total']) }}</td>
                        <td style="text-align: right;">100%</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- ALL DISTRICTS SECTION -->
        <div class="section-title">All Districts</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 8%;">#</th>
                    <th style="width: 67%;">District</th>
                    <th style="width: 25%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($districtsPerformance as $index => $district)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="text-transform: uppercase;">{{ $district['name'] }}</td>
                        <td style="text-align: right;">{{ number_format($district['count']) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #64748b;">No data found for this period.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- WARDS WITH ADMISSIONS SECTION -->
        <div class="section-title">Wards with Admissions</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 8%;">#</th>
                    <th style="width: 67%;">Ward</th>
                    <th style="width: 25%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($wardsPerformance as $index => $ward)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="text-transform: uppercase;">{{ $ward['name'] }}</td>
                        <td style="text-align: right;">{{ number_format($ward['count']) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #64748b;">No data found for this period.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- TOP 20 FEE PAYMENT RATES SECTION -->
        <div class="section-title">Top 20 Fee Payment Rates</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 8%;">#</th>
                    <th style="width: 42%;">Region</th>
                    <th style="width: 25%; text-align: right;">Enrolled / Paid</th>
                    <th style="width: 25%; text-align: right;">Percentage</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paymentRates as $index => $rate)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="text-transform: uppercase;">{{ $rate['name'] }}</td>
                        <td style="text-align: right;">{{ $rate['enrolled'] }} / {{ $rate['paid'] }}</td>
                        <td style="text-align: right; font-weight: bold;">{{ $rate['percentage'] }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #64748b;">No data found for this period.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Page Footer styled for printing -->
        <div class="footer clearfix">
            <div class="footer-left">Generated by Admissions Office</div>
            <div class="footer-center">CONFIDENTIAL REPORT</div>
            <div class="footer-right">Page <span class="page-number"></span></div>
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
