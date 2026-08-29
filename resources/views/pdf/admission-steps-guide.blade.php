<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mwongozo wa Hatua na Mahitaji ya Udahili (SUPA Admission Steps Guide)</title>
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
            background: #d97706;
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
            background: #b45309;
        }
        .btn-excel {
            background: #059669;
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
        .btn-excel:hover {
            background: #047857;
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
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .header-title {
            text-align: center;
        }
        .header-title h1 {
            font-size: 16px;
            font-weight: 900;
            margin: 0;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-title h2 {
            font-size: 13px;
            font-weight: 800;
            margin: 4px 0 0 0;
            color: #0f172a;
        }
        .header-title p {
            font-size: 10px;
            margin: 3px 0 0 0;
            color: #64748b;
            font-weight: 600;
        }

        .summary-card {
            background: #eff6ff;
            border: 1.5px solid #bfdbfe;
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
            color: #1e40af;
            letter-spacing: 0.5px;
        }
        .summary-item .value {
            font-size: 13px;
            font-weight: 900;
            color: #0f172a;
        }
        .summary-item .badge {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 900;
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
            gap: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        .step-number {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: #1e3a8a;
            color: #ffffff;
            font-weight: 900;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .step-title {
            font-size: 12px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }
        .step-body {
            padding: 12px 16px;
        }
        .step-body p {
            margin: 0 0 8px 0;
            color: #334155;
            font-size: 10.5px;
        }
        
        table.req-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            font-size: 10.5px;
        }
        table.req-table th {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
            text-align: left;
            font-weight: 800;
            color: #334155;
        }
        table.req-table td {
            border: 1px solid #e2e8f0;
            padding: 6px 10px;
            color: #1e293b;
        }
        table.req-table tr:nth-child(even) {
            background: #fbfcfe;
        }

        .tag-mandatory {
            display: inline-block;
            background: #fee2e2;
            color: #991b1b;
            font-size: 9px;
            font-weight: 800;
            padding: 1px 6px;
            border-radius: 4px;
        }
        .tag-optional {
            display: inline-block;
            background: #f1f5f9;
            color: #475569;
            font-size: 9px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 4px;
        }

        .checklist-item {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 5px;
            font-size: 10.5px;
            color: #1e293b;
        }
        .check-box {
            width: 13px;
            height: 13px;
            border: 1.5px solid #64748b;
            border-radius: 3px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .note-box {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 10px 14px;
            border-radius: 4px;
            margin-top: 16px;
            font-size: 10.5px;
            color: #78350f;
        }

        .footer-sign {
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid #cbd5e1;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #64748b;
        }

        @media print {
            .no-print-bar {
                display: none !important;
            }
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
        }
    </style>
</head>
<body>

<div class="container">

    <!-- Top Action Bar for Web View -->
    <div class="no-print-bar">
        <div>
            <strong style="font-size: 13px;">📄 Mwongozo Rasmi wa Hatua za Fomu ya Udahili (SUPA)</strong>
            <div style="font-size: 11px; color: #cbd5e1;">Pakua au chapisha mwongozo huu uwe na kumbukumbu ya hatua na nyaraka zote zinazohitajika.</div>
        </div>
        <div class="btn-group">
            <button type="button" onclick="window.print()" class="btn-print">
                🖨️ Chapisha / Hifadhi PDF
            </button>
            <a href="{{ route('public.download.admission-excel') }}" class="btn-excel">
                📊 Pakua Fomu ya Excel
            </a>
            <a href="{{ route('applicant.wizard') }}" class="btn-back">
                📝 Fomu ya Mtandao &rarr;
            </a>
        </div>
    </div>

    <!-- Official Header -->
    <table class="header-table">
        <tr>
            <td style="width: 100%;">
                <div class="header-title">
                    <h1>CHUO KIKUU HURIA CHA TANZANIA (OUT)</h1>
                    <div style="font-size: 11px; font-weight: 800; color: #0284c7; text-transform: uppercase;">kwa kushirikiana na</div>
                    <h2>CHUO CHA UALIMU SINGIDA (STTC)</h2>
                    <p>SUPA ADMISSION PORTAL — MWONGOZO NA ORODHA YA HATUA ZA MAOMBI YA UDAHILI</p>
                </div>
            </td>
        </tr>
    </table>

    <!-- Quick Summary Badge Card -->
    <div class="summary-card">
        <div class="summary-item">
            <span class="label">Ada ya Fomu ya Maombi</span>
            <span class="value badge">TZS 20,000/= (Inalipwa mara moja)</span>
        </div>
        <div class="summary-item">
            <span class="label">Tovuti ya Udahili</span>
            <span class="value" style="color: #1e3a8a;">supa.ac.tz</span>
        </div>
        <div class="summary-item">
            <span class="label">Njia za Malipo</span>
            <span class="value">NMB Control Number (Benki au Simu)</span>
        </div>
        <div class="summary-item">
            <span class="label">Jumla ya Hatua</span>
            <span class="value">Hatua 7 (Step 1 hadi Step 7)</span>
        </div>
    </div>

    <!-- Step 1 -->
    <div class="step-block">
        <div class="step-header">
            <div class="step-number">1</div>
            <h3 class="step-title">Step 1: Account Verification & Consent (Uthibitisho wa Akaunti na Ridhaa)</h3>
        </div>
        <div class="step-body">
            <p>Hatua hii inamsajili mwombaji kwenye mfumo, kumtengenezea akaunti, na kukusanya ridhaa ya kisheria ya usindikaji wa taarifa binafsi.</p>
            <div class="checklist-item"><div class="check-box"></div> <span><strong>Ridhi ya Sera ya Faragha na Masharti:</strong> Kusoma na kukubali sera ya ulinzi wa taarifa binafsi (Personal Data Protection).</span></div>
            <div class="checklist-item"><div class="check-box"></div> <span><strong>Barua Pepe (Email):</strong> Barua pepe hai ya mwombaji kwa ajili ya kupokea mrejesho na namba ya udahili.</span></div>
            <div class="checklist-item"><div class="check-box"></div> <span><strong>Namba ya Simu / WhatsApp:</strong> Namba ya simu inayopatikana WhatsApp kwa ajili ya mawasiliano ya haraka.</span></div>
            <div class="checklist-item"><div class="check-box"></div> <span><strong>Nenosiri (Password):</strong> Kutengeneza nenosiri imara lenye herufi zisizopungua 8.</span></div>
        </div>
    </div>

    <!-- Step 2 -->
    <div class="step-block">
        <div class="step-header">
            <div class="step-number">2</div>
            <h3 class="step-title">Step 2: Taarifa Binafsi za Mwombaji (Personal Information)</h3>
        </div>
        <div class="step-body">
            <p>Ujazaji wa taarifa rasmi za utambulisho kulingana na nyaraka za serikali na vyeti vyako.</p>
            <table class="req-table">
                <thead>
                    <tr>
                        <th style="width: 30%;">Kipengele</th>
                        <th style="width: 45%;">Maelezo & Mfano</th>
                        <th style="width: 25%;">Hali</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Majina Kamili</strong></td>
                        <td>Jina la Kwanza, la Kati, na la Ukoo (Kama lilivyo kwenye vyeti)</td>
                        <td><span class="tag-mandatory">Lazima</span></td>
                    </tr>
                    <tr>
                        <td><strong>Jinsia & Tarehe ya Kuzaliwa</strong></td>
                        <td>Mme / Mke (Male/Female) na Tarehe (YYYY-MM-DD)</td>
                        <td><span class="tag-mandatory">Lazima</span></td>
                    </tr>
                    <tr>
                        <td><strong>Namba ya Utambulisho</strong></td>
                        <td>Namba ya NIDA (tarakimu 20) au Kitambulisho cha Kura au Kazi</td>
                        <td><span class="tag-mandatory">Lazima kimoja</span></td>
                    </tr>
                    <tr>
                        <td><strong>Makazi ya Mwombaji</strong></td>
                        <td>Mkoa, Wilaya na Kata unapoishi kwa sasa</td>
                        <td><span class="tag-mandatory">Lazima</span></td>
                    </tr>
                    <tr>
                        <td><strong>Mzazi / Mlezi (Under 18)</strong></td>
                        <td>Jina na namba ya simu ya mzazi iwapo mwombaji ana umri chini ya miaka 18</td>
                        <td><span class="tag-optional">Chini ya miaka 18</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Step 3 -->
    <div class="step-block">
        <div class="step-header">
            <div class="step-number">3</div>
            <h3 class="step-title">Step 3: Taarifa za Taaluma na Elimu (Academic Qualifications)</h3>
        </div>
        <div class="step-body">
            <p>Kuweka matokeo na sifa za kitaaluma ambazo mfumo utatumia kukuchuja kwenye kundi sahihi la udahili (Direct Entry au Foundation Course).</p>
            <table class="req-table">
                <thead>
                    <tr>
                        <th style="width: 30%;">Aina ya Mwombaji</th>
                        <th style="width: 45%;">Taarifa Zinazohitajika</th>
                        <th style="width: 25%;">Vigezo vya Ushindi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Mwenye Stashahada (Diploma)</strong></td>
                        <td>Jina la Chuo, Jina la Programu, Mwaka, GPA Score, na NACTVET AVN</td>
                        <td>GPA 3.0+ (Direct Entry)<br>GPA 2.0–2.9 (Foundation)</td>
                    </tr>
                    <tr>
                        <td><strong>Mhitimu wa Kidato cha 6 (ACSEE)</strong></td>
                        <td>Namba ya Mtihani ya Kidato cha 6 (ACSEE Index No), Mwaka, na Points</td>
                        <td>Points 5+ (Direct Entry)<br>Chini ya 5 (Foundation)</td>
                    </tr>
                    <tr>
                        <td><strong>Mhitimu wa Kidato cha 4 (CSEE)</strong></td>
                        <td>Namba ya Mtihani ya Kidato cha 4 (CSEE Index No) na Mwaka wa Kuhitimu</td>
                        <td>Ufaulu wa angalau D nne</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Step 4 -->
    <div class="step-block">
        <div class="step-header">
            <div class="step-number">4</div>
            <h3 class="step-title">Step 4: Uchaguzi wa Programu na Kundi (Programme & Intake Selection)</h3>
        </div>
        <div class="step-body">
            <p>Kuchagua kozi unayoomba kulingana na sifa zako za kitaaluma zilizohakikiwa katika Hatua ya 3:</p>
            <div class="checklist-item"><div class="check-box"></div> <span><strong>Bachelor of Arts with Education (BAED):</strong> Shahada ya Sanaa na Elimu (Miaka 3).</span></div>
            <div class="checklist-item"><div class="check-box"></div> <span><strong>Bachelor of Science with Education (BSCED):</strong> Shahada ya Sayansi na Elimu (Miaka 3).</span></div>
            <div class="checklist-item"><div class="check-box"></div> <span><strong>International Master of Pedagogy & Technology (IMPTE):</strong> Shahada ya Uzamili (Miaka 2).</span></div>
            <div class="checklist-item"><div class="check-box"></div> <span><strong>Foundation Course for Higher Education:</strong> Kozi ya Daraja (Bridging Programme) kwa wenye GPA ya 2.0–2.9.</span></div>
            <div class="checklist-item"><div class="check-box"></div> <span><strong>Kipindi cha Masomo (Intake):</strong> Kuchagua awamu ya udahili (March au September Intake).</span></div>
        </div>
    </div>

    <!-- Step 5 -->
    <div class="step-block">
        <div class="step-header">
            <div class="step-number">5</div>
            <h3 class="step-title">Step 5: Malipo ya Ada ya Fomu ya Maombi (TZS 20,000/=)</h3>
        </div>
        <div class="step-body">
            <p>Kufanya malipo ya ada ya fomu ya TZS 20,000/= kupitia NMB Control Number inayotolewa kwenye mfumo:</p>
            <table class="req-table">
                <thead>
                    <tr>
                        <th style="width: 30%;">Njia ya Malipo</th>
                        <th style="width: 45%;">Utaratibu wa Kulipia</th>
                        <th style="width: 25%;">Uthibitisho</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Benki / Wakala wa NMB</strong></td>
                        <td>Wasilisha NMB Control Number kwa mhudumu wa tawi au wakala wa NMB</td>
                        <td>Pata risiti yenye namba ya muamala</td>
                    </tr>
                    <tr>
                        <td><strong>M-Pesa / TigoPesa / Airtel Money</strong></td>
                        <td>Chagua Lipa kwa Control Number / Malipo ya Serikali kisha weka Control Number yako</td>
                        <td>Pata ujumbe wa SMS wa muamala</td>
                    </tr>
                    <tr>
                        <td><strong>Upakiaji wa Risiti (Upload)</strong></td>
                        <td>Weka namba ya muamala na pakia picha au faili la PDF la risiti kwenye mfumo</td>
                        <td>Admin/Finance anathibitisha</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Step 6 -->
    <div class="step-block">
        <div class="step-header">
            <div class="step-number">6</div>
            <h3 class="step-title">Step 6: Orodha ya Vyeti na Nyaraka (Certificates & Document Uploads)</h3>
        </div>
        <div class="step-body">
            <p>Baada ya malipo yako ya TZS 20,000/= kuthibitishwa na Admin, utapakia nyaraka zifuatazo (Format: PDF, PNG, au JPG, isizidi 5MB kila moja):</p>
            <div class="checklist-item"><div class="check-box"></div> <span><strong>Cheti cha Kidato cha 4 (CSEE Certificate / Result Slip):</strong> <span class="tag-mandatory">Lazima</span></span></div>
            <div class="checklist-item"><div class="check-box"></div> <span><strong>Cheti cha Kidato cha 6 au Stashahada (ACSEE / Diploma Certificate):</strong> <span class="tag-mandatory">Lazima</span></span></div>
            <div class="checklist-item"><div class="check-box"></div> <span><strong>Matokeo Kamili ya Diploma (Academic Transcript):</strong> <span class="tag-optional">Kwa waombaji wa Diploma</span></span></div>
            <div class="checklist-item"><div class="check-box"></div> <span><strong>Nakala ya Kitambulisho:</strong> NIDA ID au Kitambulisho cha Kura/Kazi. <span class="tag-mandatory">Lazima</span></span></div>
            <div class="checklist-item"><div class="check-box"></div> <span><strong>Picha ya Pasipoti (Passport Size Photo):</strong> Picha ya rangi yenye mandhari (background) nyeupe. <span class="tag-mandatory">Lazima</span></span></div>
        </div>
    </div>

    <!-- Step 7 -->
    <div class="step-block">
        <div class="step-header">
            <div class="step-number">7</div>
            <h3 class="step-title">Step 7: Tamko la Mwombaji & Kuwasilisha Maombi (Declaration & Submission)</h3>
        </div>
        <div class="step-body">
            <p>Hatua ya mwisho ya kukamilisha maombi:</p>
            <div class="checklist-item"><div class="check-box"></div> <span><strong>Uhakiki wa Taarifa:</strong> Kupitia muhtasari wa taarifa zote ulizojaza ili kuhakikisha hakuna makosa.</span></div>
            <div class="checklist-item"><div class="check-box"></div> <span><strong>Saini ya Kidigitali (Digital Signature):</strong> Kuweka saini au jina lako rasmi kama uthibitisho wa kisheria.</span></div>
            <div class="checklist-item"><div class="check-box"></div> <span><strong>Kutuma Maombi (Submit Final):</strong> Kubofya kitufe cha kutuma maombi na kupewa Namba ya Maombi (Application Number).</span></div>
            <div class="checklist-item"><div class="check-box"></div> <span><strong>Kufuatilia Udahili:</strong> Kufuatilia hatua ya usahili kupitia ukurasa wa <em>Track Application</em> au kuingia kwenye akaunti yako.</span></div>
        </div>
    </div>

    <!-- Warning / Note -->
    <div class="note-box">
        ⚠️ <strong>ANGALIZO MUHIMU:</strong> Hakikisha taarifa na vyeti vyote unavyopakia ni halali na vinasomeka vizuri. Kuweka taarifa au nyaraka za kughushi ni kosa la jinai na kutasababisha kufutiwa maombi mara moja bila kurejeshewa ada.
    </div>

    <!-- Footer -->
    <div class="footer-sign">
        <div>
            <strong>Ofisi ya Udahili (Directorate of Admissions)</strong><br>
            Chuo Kikuu Huria cha Tanzania (OUT) & Chuo cha Ualimu Singida (STTC)
        </div>
        <div style="text-align: right;">
            <strong>Mawasiliano ya Msaada:</strong><br>
            Barua Pepe: admissions@supa.ac.tz | Simu: +255 22 266 8820
        </div>
    </div>

</div>

</body>
</html>
