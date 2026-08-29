<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mwongozo wa Malipo - NMB Bank & Mobile Money (STTC Payment Guideline)</title>
    <style>
        @page {
            size: A4;
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
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .no-print-bar {
            background: #0f172a;
            color: #fff;
            padding: 14px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .btn-group {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .btn-print {
            background: #ff5500;
            color: #ffffff;
            border: none;
            padding: 9px 18px;
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
            background: #e04b00;
        }
        .btn-back {
            background: #334155;
            color: #f8fafc;
            border: none;
            padding: 9px 16px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-back:hover {
            background: #1e293b;
        }
        
        .header-table {
            width: 100%;
            border-bottom: 2px solid #ff5500;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .header-title {
            text-align: center;
        }
        .header-title h1 {
            font-size: 15px;
            font-weight: 900;
            margin: 0;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-title h2 {
            font-size: 12px;
            font-weight: 800;
            margin: 4px 0 0 0;
            color: #ff5500;
        }
        .header-title p {
            font-size: 10px;
            margin: 3px 0 0 0;
            color: #64748b;
            font-weight: 600;
        }

        .summary-card {
            background: #fffbeb;
            border: 1.5px solid #fef3c7;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .summary-item {
            display: flex;
            flex-direction: column;
        }
        .summary-item .label {
            font-size: 9px;
            text-transform: uppercase;
            font-weight: 800;
            color: #b45309;
            letter-spacing: 0.5px;
        }
        .summary-item .value {
            font-size: 12px;
            font-weight: 900;
            color: #0f172a;
        }
        .summary-item .badge {
            background: #ffebd8;
            color: #c2410c;
            border: 1px solid #fed7aa;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 900;
        }

        .channels-grid {
            display: grid;
            grid-template-cols: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }
        .channel-card {
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 12px;
            background: #f8fafc;
        }
        .channel-card h4 {
            margin: 0 0 6px 0;
            font-size: 11px;
            color: #1e3a8a;
            font-weight: 900;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .channel-card p {
            margin: 0;
            color: #475569;
            font-size: 10px;
        }

        .step-block {
            margin-bottom: 16px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            background: #ffffff;
            page-break-inside: avoid;
        }
        .step-header {
            background: #f1f5f9;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e2e8f0;
        }
        .step-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .step-icon {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            background: #1e3a8a;
            color: #ffffff;
            font-weight: 900;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .step-title {
            font-size: 11.5px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }
        .step-badge {
            font-size: 9px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 6px;
            background: #e0f2fe;
            color: #0369a1;
        }
        .step-body {
            padding: 12px 16px;
        }
        .step-body p {
            margin: 0 0 8px 0;
            color: #334155;
            font-size: 10.5px;
        }

        ol.step-list {
            margin: 0;
            padding-left: 20px;
        }
        ol.step-list li {
            margin-bottom: 6px;
            color: #1e293b;
            font-size: 10.5px;
        }
        ol.step-list li strong {
            color: #0f172a;
        }

        .alert-box {
            background: #fff7ed;
            border: 1px solid #ffedd5;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 12px;
            color: #9a3412;
            font-size: 10px;
            font-weight: 600;
        }

        .footer-sign {
            margin-top: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 16px;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #64748b;
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

    <!-- Top Action Bar (Hidden in Print) -->
    <div class="no-print-bar">
        <a href="{{ route('home') }}" class="btn-back">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Rudi Nyumbani / Back Home</span>
        </a>
        <div class="btn-group">
            <button onclick="window.print()" class="btn-print">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Chapa / Pakua PDF (Print)</span>
            </button>
        </div>
    </div>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td class="header-title">
                <h1>Singida Teachers' Training College</h1>
                <h2>NMB BANK PLC – PAYMENT GUIDELINE & INSTRUCTIONS</h2>
                <p>Mwongozo rasmi wa malipo ya Ada ya Fomu (TZS 20,000/=) kupitia Benki na Mitandao ya Simu</p>
            </td>
        </tr>
    </table>

    <!-- Summary Details -->
    <div class="summary-card">
        <div class="summary-item">
            <span class="label">Biller Name / Jina la Mlipwaji</span>
            <span class="value">SINGIDA TEACHERS COLLEGE</span>
        </div>
        <div class="summary-item">
            <span class="label">NMB Business Number</span>
            <span class="value">999999</span>
        </div>
        <div class="summary-item">
            <span class="label">Mobile Business Number</span>
            <span class="value">888999</span>
        </div>
        <div class="summary-item">
            <span class="label">Reference Number format</span>
            <span class="value badge">SASXXXXXXXXXXX</span>
        </div>
    </div>

    <!-- Overview Channels -->
    <h3 style="font-size: 12px; margin: 0 0 10px 0; color: #1e3a8a; text-transform: uppercase;">Njia za Kufanya Malipo (Payment Channels)</h3>
    <div class="channels-grid">
        <div class="channel-card">
            <h4>1. Matawi ya NMB (NMB Branches)</h4>
            <p>Malipo ya ana kwa ana kwenye kaunta ya tawi lolote la NMB nchini.</p>
        </div>
        <div class="channel-card">
            <h4>2. NMB Wakala</h4>
            <p>Malipo kupitia mawakala waliothibitishwa wa NMB kote nchini.</p>
        </div>
        <div class="channel-card">
            <h4>3. NMB Mkononi (USSD & App)</h4>
            <p>Malipo ya kidigitali kwa wateja wenye akaunti ya NMB (*150*66# au NMB App).</p>
        </div>
        <div class="channel-card">
            <h4>4. Mitandao ya Simu (Mobile Money)</h4>
            <p>Kupitia M-Pesa, TigoPesa, na Airtel Money kwenda Business Number 888999.</p>
        </div>
    </div>

    <!-- Detail steps by channel -->
    <h3 style="font-size: 12px; margin: 20px 0 10px 0; color: #1e3a8a; text-transform: uppercase;">Maelekezo ya Hatua kwa Hatua (Step-by-Step Instructions)</h3>

    <!-- NMB Branches -->
    <div class="step-block">
        <div class="step-header">
            <div class="step-header-left">
                <div class="step-icon">A</div>
                <h3 class="step-title">Kupitia Tawi la NMB (NMB Branch Counter)</h3>
            </div>
            <span class="step-badge">Bank Slip</span>
        </div>
        <div class="step-body">
            <div class="alert-box">
                Jaza karatasi ya malipo (Bills Payment Slip) inayopatikana katika matawi yote ya NMB.
            </div>
            <ol class="step-list">
                <li>Andika <strong>Bill Number</strong>: Jaza Namba ya Malipo (Control Number) uliyopewa kwenye mfumo, inayoanza na <strong>SASXXXXXXXXXXX</strong>.</li>
                <li>Andika <strong>Biller Name</strong>: Jaza <strong>SINGIDA TEACHERS COLLEGE</strong>.</li>
                <li>Jaza <strong>Kiasi cha Fedha (Amount)</strong>: TZS 20,000/=.</li>
                <li>Wasilisha karatasi ya malipo na fedha taslimu kwa keshia wa benki ili kukamilisha muamala na kupokea risiti.</li>
            </ol>
        </div>
    </div>

    <!-- NMB Wakala -->
    <div class="step-block">
        <div class="step-header">
            <div class="step-header-left">
                <div class="step-icon">B</div>
                <h3 class="step-title">Kupitia NMB Wakala (NMB Agents)</h3>
            </div>
            <span class="step-badge">Wakala</span>
        </div>
        <div class="step-body">
            <div class="alert-box">
                Hakikisha wakala anatumia mfumo sahihi wa NMB Bills Payment na anakupatia risiti rasmi ya benki.
            </div>
            <ol class="step-list">
                <li>Mpatie wakala Namba ya Malipo (Control Number) inayoanza na <strong>SASXXXXXXXXXXX</strong>.</li>
                <li>Mwambie mlipwaji ni <strong>SINGIDA TEACHERS COLLEGE</strong>.</li>
                <li>Mpatie kiasi cha fedha taslimu (TZS 20,000/=) pamoja na ada ya wakala kama ipo.</li>
                <li>Wakala atakamilisha malipo na kukupatia risiti rasmi iliyochapishwa.</li>
            </ol>
        </div>
    </div>

    <!-- NMB Mkononi USSD -->
    <div class="step-block">
        <div class="step-header">
            <div class="step-header-left">
                <div class="step-icon">C</div>
                <h3 class="step-title">Kupitia NMB Mkononi (USSD *150*66#)</h3>
            </div>
            <span class="step-badge">NMB Account Users</span>
        </div>
        <div class="step-body">
            <ol class="step-list">
                <li>Piga/Dial <strong>*150*66#</strong> kwenye simu yako.</li>
                <li>Weka namba ya siri (PIN) ya NMB Mkononi.</li>
                <li>Chagua namba <strong>2 [LIPA BILI / PAY BILLS]</strong>.</li>
                <li>Chagua namba <strong>5 [CHAGUA BIASHARA / CHOOSE BUSINESS]</strong>.</li>
                <li>Chagua namba <strong>3 [WEKA NAMBA YA BIASHARA / ENTER BUSINESS NUMBER]</strong>.</li>
                <li>Weka namba ya biashara: <strong>999999</strong>.</li>
                <li>Weka kumbukumbu ya malipo (Reference number): Ingiza Namba yako ya Malipo e.g., <strong>SASXXXXXX</strong>.</li>
                <li>Ingiza kiasi cha malipo (Amount): TZS 20,000/= kisha thibitisha kwa kuweka PIN yako.</li>
            </ol>
        </div>
    </div>

    <!-- NMB Mkononi App -->
    <div class="step-block">
        <div class="step-header">
            <div class="step-header-left">
                <div class="step-icon">D</div>
                <h3 class="step-title">Kupitia NMB Mkononi App</h3>
            </div>
            <span class="step-badge">Smartphones</span>
        </div>
        <div class="step-body">
            <ol class="step-list">
                <li>Fungua NMB Mkononi App na uingize PIN yako.</li>
                <li>Chagua <strong>Bill Payment (Malipo ya Bili)</strong>.</li>
                <li>Chagua <strong>Other Billers (Watoa Bili Wengine)</strong>.</li>
                <li>Kwenye sanduku la utafutaji (Search), tafuta na uchague <strong>SINGIDA TEACHERS COLLEGE</strong>.</li>
                <li>Weka Reference Number: Jaza Namba ya Malipo (Control Number) uliyopewa inayoanza na <strong>SASXXXXXXXXXXX</strong>.</li>
                <li>Ingiza kiasi cha malipo, thibitisha taarifa na ukamilishe malipo.</li>
            </ol>
        </div>
    </div>

    <!-- Mobile Money (M-Pesa, TigoPesa, Airtel Money) -->
    <div class="step-block">
        <div class="step-header">
            <div class="step-header-left">
                <div class="step-icon">E</div>
                <h3 class="step-title">Kupitia Mitandao ya Simu (Mobile Money)</h3>
            </div>
            <span class="step-badge">M-Pesa / TigoPesa / Airtel Money</span>
        </div>
        <div class="step-body">
            <div class="alert-box" style="background: #f0fdf4; border-color: #bbf7d0; color: #166534;">
                Mitandao yote inatumia Namba ya Kampuni (Business Number) <strong>888999</strong> na Reference inayoanza na <strong>SASXXXXXXXXXXX</strong>.
            </div>
            
            <h4 style="margin: 8px 0 4px 0; font-size: 10.5px; color: #c2410c;">Vodacom M-Pesa</h4>
            <ol class="step-list">
                <li>Dial <strong>*150*00#</strong>, kisha chagua option <strong>4 [Lipa kwa M-Pesa]</strong>.</li>
                <li>Chagua option <strong>4 [Weka namba ya kampuni / Enter Business Number]</strong>.</li>
                <li>Ingiza Namba ya Kampuni: <strong>888999</strong>.</li>
                <li>Ingiza Kumbukumbu ya Malipo (Reference Number): Jaza Namba ya Malipo inayoanza na <strong>SASXXXXXXXXXXX</strong>.</li>
                <li>Weka kiasi: TZS 20,000/= kisha weka siri (PIN) ya M-Pesa na uthibitishe malipo.</li>
            </ol>

            <h4 style="margin: 12px 0 4px 0; font-size: 10.5px; color: #0369a1;">Tigo Pesa</h4>
            <ol class="step-list">
                <li>Dial <strong>*150*01#</strong>, kisha chagua option <strong>4 [Lipia Bili / Pay Bills]</strong>.</li>
                <li>Chagua option <strong>3 [Ingiza Namba ya Kampuni / Enter Business Number]</strong>.</li>
                <li>Ingiza Namba ya Kampuni: <strong>888999</strong>.</li>
                <li>Ingiza Kumbukumbu ya Malipo (Reference Number): Jaza Namba ya Malipo inayoanza na <strong>SASXXXXXXXXXXX</strong>.</li>
                <li>Ingiza kiasi cha malipo: TZS 20,000/=, weka PIN ya Tigo Pesa na uthibitishe malipo.</li>
            </ol>

            <h4 style="margin: 12px 0 4px 0; font-size: 10.5px; color: #b91c1c;">Airtel Money</h4>
            <ol class="step-list">
                <li>Dial <strong>*150*60#</strong>, kisha chagua option <strong>5 [Lipia Bili / Pay Bills]</strong>.</li>
                <li>Chagua option <strong>4 [Ingiza Namba ya Kampuni / Enter Business Number]</strong>.</li>
                <li>Ingiza Namba ya Kampuni: <strong>888999</strong>.</li>
                <li>Ingiza Kumbukumbu ya Malipo (Reference Number): Jaza Namba ya Malipo inayoanza na <strong>SASXXXXXXXXXXX</strong>.</li>
                <li>Ingiza kiasi cha malipo: TZS 20,000/=, weka PIN ya Airtel Money na uthibitishe malipo.</li>
            </ol>
        </div>
    </div>

    <!-- Final Note -->
    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 12px; font-size: 10px; color: #1e40af; margin-top: 16px;">
        💡 <strong>KUMBUKA:</strong> Baada ya kukamilisha malipo, hakikisha unatunza risiti ya malipo (kama picha au PDF) pamoja na namba ya muamala (Transaction ID). Utahitajika kuingiza namba hiyo na kupakia risiti kwenye mfumo wa SUPA ili maombi yako yaweze kukaguliwa na kuidhinishwa na kitengo cha fedha.
    </div>

    <!-- Footer Signatures -->
    <div class="footer-sign">
        <div>
            <strong>Idara ya Fedha & Mapato (Finance Department)</strong><br>
            Chuo cha Ualimu Singida (Singida Teachers' Training College)
        </div>
        <div style="text-align: right;">
            <strong>Msaada wa Malipo (Payment Support):</strong><br>
            Simu: +255 784 112 233 | admissions@supa.ac.tz
        </div>
    </div>

</div>

<!-- Auto Print Trigger if requested -->
@if(request()->has('download') || request()->has('print'))
<script>
    window.onload = function() {
        window.print();
    }
</script>
@endif

</body>
</html>
