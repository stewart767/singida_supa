<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sehemu ya 2 - Kiongozi na Maelezo Muhimu kwa Mwanafunzi (SUPA & OUT)</title>
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
            background: #1e293b;
            color: #fff;
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .btn-print {
            background: #2563eb;
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            font-size: 12px;
            font-weight: 700;
            border-radius: 6px;
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
        .btn-back {
            background: #475569;
            color: #ffffff;
            border: none;
            padding: 8px 14px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 6px;
            text-decoration: none;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 16px;
            margin: 0;
            color: #1e3a8a;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header h2 {
            font-size: 13px;
            margin: 4px 0;
            color: #0f172a;
            font-weight: 700;
        }
        .header p {
            margin: 2px 0 0 0;
            font-size: 11px;
            color: #475569;
        }
        .tear-off-banner {
            border: 2px dashed #94a3b8;
            background: #f1f5f9;
            padding: 8px;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            color: #334155;
            margin-bottom: 18px;
            border-radius: 6px;
        }
        .part-header {
            background: #1e3a8a;
            color: #ffffff;
            padding: 8px 14px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 4px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .section-title {
            font-size: 11.5px;
            font-weight: bold;
            background: #f1f5f9;
            color: #0f172a;
            padding: 6px 10px;
            border-left: 4px solid #2563eb;
            margin: 16px 0 8px 0;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 10.5px;
        }
        table th, table td {
            border: 1px solid #cbd5e1;
            padding: 7px 9px;
            text-align: left;
            vertical-align: top;
        }
        table th {
            background-color: #e2e8f0;
            color: #0f172a;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9.5px;
            letter-spacing: 0.3px;
        }
        table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .note-box {
            background: #fffbebf8;
            border: 1px solid #fde68a;
            border-left: 4px solid #d97706;
            padding: 8px 12px;
            font-size: 10.5px;
            border-radius: 4px;
            margin-top: -6px;
            margin-bottom: 14px;
            color: #78350f;
        }
        .footer-note {
            margin-top: 25px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            text-align: center;
            font-size: 9.5px;
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
            .tear-off-banner {
                border-color: #000;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Top Navigation Bar (Hidden during Print) -->
        <div class="no-print-bar">
            <div>
                <span style="font-weight: 700; font-size: 13px;">Mwongozo wa Mwanafunzi — SEHEMU YA 2</span>
                <span style="font-size: 11px; opacity: 0.8; display: block;">Hati Hii ni ya Kumbukumbu ya Mwombaji / Mwanafunzi</span>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('public.requirements') }}" class="btn-back">&larr; Rudi kwenye Tovuti</a>
                <button onclick="window.print()" class="btn-print">
                    🖨️ Chapisha / Hifadhi kama PDF
                </button>
            </div>
        </div>

        <!-- Tear-off Line Banner -->
        <div class="tear-off-banner">
            ✂️ SEHEMU HII ITENGANISHWE NA KUBAKI KWA MWANAFUNZI KAMA KUMBUKUMBU NA MWONGOZO ✂️
        </div>

        <!-- Header -->
        <div class="header" style="display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; margin-bottom: 15px;">
            @if(\App\Models\Setting::get('sttc_logo'))
                <img src="{{ asset('storage/' . \App\Models\Setting::get('sttc_logo')) }}" alt="STTC Logo" style="height: 65px; max-width: 90px; object-fit: contain;">
            @else
                <div style="width: 50px; height: 50px; border-radius: 50%; background: #1e3a8a; color: #fbbf24; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 20px;">S</div>
            @endif
            <div style="text-align: center; flex-grow: 1; padding: 0 10px;">
                <h1 style="font-size: 15px; margin: 0; color: #1e3a8a; font-weight: 900;">{{ \App\Models\Setting::get('university_name', "SINGIDA TEACHERS' TRAINING COLLEGE (STTC)") }} & OUT</h1>
                <h2 style="font-size: 12px; margin: 3px 0; color: #0f172a;">PROGRAMU ZA UALIMU NA DIGRII KUPITIA MFUMO WA SUPA</h2>
                <p style="font-size: 9.5px; margin: 0; color: #475569;">Anwani: P.O. Box 240, Singida | Tovuti: www.singidattc.ac.tz | Mfumo wa Udahili & Support (SUPA)</p>
            </div>
            @if(\App\Models\Setting::get('out_logo'))
                <img src="{{ asset('storage/' . \App\Models\Setting::get('out_logo')) }}" alt="OUT Logo" style="height: 65px; max-width: 90px; object-fit: contain;">
            @else
                <div style="width: 50px; height: 50px; border-radius: 50%; background: #065f46; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 18px;">OUT</div>
            @endif
        </div>

        <div class="part-header">
            SEHEMU YA 2: KIONGOZI NA MAELEZO MUHIMU KWA MWANAFUNZI
        </div>

        <!-- 1. Vigezo vya Udahili -->
        <div class="section-title">1. VIGEZO VYA UDAHILI (ADMISSION CRITERIA)</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Kundi</th>
                    <th style="width: 25%;">Kiwango cha GPA / Sifa</th>
                    <th style="width: 20%;">Hadhi ya Udahili</th>
                    <th style="width: 18%;">Programu Anayostahili</th>
                    <th style="width: 10%;">Chuo Husika</th>
                    <th style="width: 12%;">Muda wa Kuanza</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Kundi la 1</strong></td>
                    <td>GPA 3.0–5.0 / Form VI Direct</td>
                    <td>Udahili wa moja kwa moja</td>
                    <td>BAED, BSCED au IMPTE</td>
                    <td>OUT</td>
                    <td>Oktoba 2026</td>
                </tr>
                <tr>
                    <td><strong>Kundi la 2</strong></td>
                    <td>GPA 2.0–2.9</td>
                    <td>Foundation Programme</td>
                    <td>Foundation kupitia SUPA</td>
                    <td>STTC (SUPA) na OUT</td>
                    <td>Septemba 2026</td>
                </tr>
                <tr>
                    <td><strong>Baada ya Foundation</strong></td>
                    <td>Waliokidhi vigezo vya ufaulu</td>
                    <td>Udahili wa Shahada</td>
                    <td>Shahada husika</td>
                    <td>OUT</td>
                    <td>Septemba 2027</td>
                </tr>
            </tbody>
        </table>

        <!-- 2. Orodha ya Programu na Ada -->
        <div class="section-title">2. ORODHA YA PROGRAMU ZA MASOMO NA ADA ZAKE</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 4%;">Na.</th>
                    <th style="width: 32%;">Jina la Programu</th>
                    <th style="width: 8%;">Muda</th>
                    <th style="width: 26%;">Sifa za Kujiunga</th>
                    <th style="width: 15%;">Ada kwa Mwaka</th>
                    <th style="width: 15%;">Malipo ya Mwezi (miezi 10)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td><strong>Bachelor of Arts with Education (BAED)</strong></td>
                    <td>Miaka 3</td>
                    <td>Diploma GPA 3.0+ / Form VI</td>
                    <td>TZS 1,550,000/=</td>
                    <td>TZS 155,000/=</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td><strong>Bachelor of Science in Education (BSCED)</strong></td>
                    <td>Miaka 3</td>
                    <td>Diploma GPA 3.0+ / Form VI</td>
                    <td>TZS 1,550,000/=</td>
                    <td>TZS 155,000/=</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td><strong>Integrated Master’s in Primary Teachers Education (IMPTE)</strong></td>
                    <td>Miaka 3</td>
                    <td>Diploma GPA 3.0+; Shahada + Uzamili</td>
                    <td>TZS 2,450,000/=</td>
                    <td>TZS 245,000/=</td>
                </tr>
                <tr>
                    <td>4</td>
                    <td><strong>Foundation Programme (SUPA Support)</strong></td>
                    <td>Mwaka 1</td>
                    <td>Diploma ya Ualimu GPA 2.0–2.9</td>
                    <td>TZS 1,550,000/=</td>
                    <td>TZS 155,000/=</td>
                </tr>
            </tbody>
        </table>
        <div class="note-box">
            📌 <strong>Angalizo la Ada:</strong> Ada za masomo zinaweza kulipwa kwa awamu za kila mwezi ndani ya miezi kumi (10) ya mwaka wa masomo.
        </div>

        <!-- 3. Michango Mingine -->
        <div class="section-title">3. GHARAMA ZA MICHANGO MINGINE YA CHUO</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 45%;">Aina ya Mchango</th>
                    <th style="width: 25%;">Kiasi (TZS)</th>
                    <th style="width: 30%;">Utaratibu wa Ulipaji</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Fomu ya Maombi ya Udahili</td>
                    <td><strong>20,000/=</strong></td>
                    <td>Mara moja wakati wa kuomba</td>
                </tr>
                <tr>
                    <td>TCU Quality Assurance</td>
                    <td><strong>20,000/=</strong></td>
                    <td>Kila mwaka</td>
                </tr>
                <tr>
                    <td>Kitambulisho cha Mwanafunzi</td>
                    <td><strong>20,000/=</strong></td>
                    <td>Mara moja tu</td>
                </tr>
                <tr>
                    <td>Ada ya Mitihani</td>
                    <td><strong>10,000/=</strong></td>
                    <td>Kwa kila mtihani</td>
                </tr>
                <tr>
                    <td>Teaching Practice (Ualimu kwa Vitendo)</td>
                    <td><strong>100,000/=</strong></td>
                    <td>Kwa kila awamu</td>
                </tr>
                <tr>
                    <td>Serikali ya Wanafunzi (OUTSO)</td>
                    <td><strong>20,000/=</strong></td>
                    <td>Kila mwaka</td>
                </tr>
            </tbody>
        </table>

        <!-- 4. Ratiba na Tarehe Muhimu -->
        <div class="section-title">4. RATIBA NA TAREHE MUHIMU ZA KITAALUMA — 2026</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 4%;">Na.</th>
                    <th style="width: 30%;">Shughuli</th>
                    <th style="width: 20%;">Tarehe</th>
                    <th style="width: 46%;">Maelezo Muhimu</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Mwisho wa Kupokea Maombi</td>
                    <td><strong>30 Agosti 2026</strong></td>
                    <td>Maombi kupitia www.singidattc.ac.tz</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Kutangaza Waliodahiliwa</td>
                    <td><strong>05 Septemba 2026</strong></td>
                    <td>Majina yatatolewa kwenye tovuti ya chuo</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Mafunzo ya Utangulizi (Orientation)</td>
                    <td><strong>10 Septemba 2026</strong></td>
                    <td>Vituo vya masomo katika Halmashauri zote</td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>Kuanza Rasmi Masomo</td>
                    <td><strong>25 Septemba 2026</strong></td>
                    <td>Mfumo wa ODL: LMS mtandaoni na ana kwa ana</td>
                </tr>
                <tr>
                    <td>5</td>
                    <td>Mwisho wa Usajili wa Kozi</td>
                    <td><strong>30 Septemba 2026</strong></td>
                    <td>Kukamilisha usajili wa masomo yote</td>
                </tr>
            </tbody>
        </table>

        <!-- 5. Huduma za Kitaaluma -->
        <div class="section-title">5. HUDUMA ZA KITAALUMA ZITAKAZOTOLEWA NA SUPA</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 35%;">Huduma</th>
                    <th style="width: 65%;">Maelezo</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Academic English Enhancement</strong></td>
                    <td>Kukuza lugha ya Kiingereza cha kitaaluma.</td>
                </tr>
                <tr>
                    <td><strong>Digital Learning & LMS Orientation</strong></td>
                    <td>Mafunzo ya mfumo wa kujifunzia mtandaoni.</td>
                </tr>
                <tr>
                    <td><strong>Academic Writing & Research Skills</strong></td>
                    <td>Mbinu za uandishi wa kitaaluma, insha na utafiti.</td>
                </tr>
                <tr>
                    <td><strong>Study Skills & Exam Readiness</strong></td>
                    <td>Mbinu za kujisomea katika Distance Learning na kujiandaa na mitihani.</td>
                </tr>
                <tr>
                    <td><strong>Academic Counselling & Mentorship</strong></td>
                    <td>Ushauri na usimamizi wa maendeleo ya masomo.</td>
                </tr>
            </tbody>
        </table>

        <!-- 6. Mwongozo wa Mamlaka za Taasisi -->
        <div class="section-title">6. MWONGOZO WA MAMLAKA ZA TAASISI</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 30%;">Taasisi</th>
                    <th style="width: 70%;">Majukumu na Mamlaka</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>STTC kupitia SUPA</strong></td>
                    <td>Inahusika na uratibu wa masomo ya ana kwa ana, kutoa usaidizi wa kitaaluma (Academic Support), maandalizi ya wanafunzi na ufuatiliaji wa maendeleo yao.</td>
                </tr>
                <tr>
                    <td><strong>OUT — Chuo Kikuu Huria</strong></td>
                    <td>Ndiyo taasisi yenye mamlaka ya kisheria ya, kusimamia mitaala, kuendesha mitihani rasmi, kutangaza matokeo na kutunuku shahada au vyeti.</td>
                </tr>
            </tbody>
        </table>

        <div class="footer-note">
            &copy; 2026 Singida Teachers' Training College (STTC) & Open University of Tanzania (OUT) - SUPA Admission Office. Hati Hii ni Mwongozo Rasmi.
        </div>

    </div>

</body>
</html>
