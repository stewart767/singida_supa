<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Taarifa ya Faragha &amp; Ridhaa ya Data Binafsi - SUPA Admission Portal</title>

    <!-- Favicon -->
    @if(\App\Models\Setting::get('system_logo'))
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . \App\Models\Setting::get('system_logo')) }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    <!-- Google Fonts: Plus Jakarta Sans & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
        h1, h2, h3, h4, .font-heading {
            font-family: 'Outfit', sans-serif !important;
        }
        [x-cloak] { display: none !important; }

        /* Custom scrollbar for print container */
        #print-container::-webkit-scrollbar {
            width: 8px;
        }
        #print-container::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.03);
            border-radius: 10px;
        }
        #print-container::-webkit-scrollbar-thumb {
            background-color: rgba(217, 119, 6, 0.3);
            border-radius: 10px;
            border: 2px solid transparent;
            background-clip: padding-box;
        }
        #print-container::-webkit-scrollbar-thumb:hover {
            background-color: rgba(217, 119, 6, 0.5);
        }
        
        .no-select {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        
        @media print {
            body, html, #print-container, * {
                display: none !important;
                visibility: hidden !important;
            }
        }

        /* --- 3D Design Tokens & Custom CSS --- */
        
        /* 3D Glass Card */
        .card-3d {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.7);
            box-shadow: 
                0 4px 6px -1px rgba(0, 0, 0, 0.03),
                0 20px 40px -15px rgba(0, 0, 0, 0.12),
                0 35px 60px -20px rgba(0, 0, 0, 0.08),
                inset 0 1px 0 rgba(255, 255, 255, 0.9),
                inset 0 -3px 0 rgba(0, 0, 0, 0.04);
            border-radius: 28px;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .card-3d:hover {
            transform: translateY(-2px);
            box-shadow: 
                0 6px 10px -3px rgba(0, 0, 0, 0.04),
                0 25px 50px -12px rgba(0, 0, 0, 0.15),
                0 35px 70px -15px rgba(0, 0, 0, 0.08),
                inset 0 1px 0 rgba(255, 255, 255, 0.95),
                inset 0 -3px 0 rgba(0, 0, 0, 0.04);
        }

        /* 3D Physical Accept/Primary Button */
        .btn-3d-accept {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 28px;
            font-weight: 800;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #ffffff;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border-radius: 16px;
            border: 1px solid #b45309;
            box-shadow: 
                0 0 0 1px rgba(245, 158, 11, 0.3),
                0 5px 0 0 #92400e,
                0 10px 18px rgba(146, 64, 14, 0.35);
            transition: all 0.1s ease;
            cursor: pointer;
        }
        .btn-3d-accept:hover:not(:disabled) {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            box-shadow: 
                0 0 0 1px rgba(245, 158, 11, 0.4),
                0 7px 0 0 #92400e,
                0 12px 22px rgba(146, 64, 14, 0.4);
        }
        .btn-3d-accept:active:not(:disabled) {
            transform: translateY(4px);
            box-shadow: 
                0 0 0 1px rgba(245, 158, 11, 0.3),
                0 1px 0 0 #92400e,
                0 3px 6px rgba(146, 64, 14, 0.25);
        }
        .btn-3d-accept:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
            border-color: #cbd5e1;
            background: #94a3b8;
        }

        /* 3D Physical Decline/Secondary Button */
        .btn-3d-decline {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 28px;
            font-weight: 800;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #475569;
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            border-radius: 16px;
            border: 1px solid #cbd5e1;
            box-shadow: 
                0 0 0 1px rgba(226, 232, 240, 0.4),
                0 5px 0 0 #94a3b8,
                0 10px 18px rgba(148, 163, 184, 0.2);
            transition: all 0.1s ease;
            cursor: pointer;
        }
        .btn-3d-decline:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            box-shadow: 
                0 0 0 1px rgba(226, 232, 240, 0.5),
                0 7px 0 0 #94a3b8,
                0 12px 22px rgba(148, 163, 184, 0.25);
        }
        .btn-3d-decline:active {
            transform: translateY(4px);
            box-shadow: 
                0 0 0 1px rgba(226, 232, 240, 0.4),
                0 1px 0 0 #94a3b8,
                0 3px 6px rgba(148, 163, 184, 0.15);
        }

        /* 3D Inset Digital Screen */
        .screen-inset {
            background: #ffffff;
            box-shadow: 
                inset 0 6px 15px rgba(0, 0, 0, 0.07),
                inset 0 1px 3px rgba(0, 0, 0, 0.04),
                0 1px 0 rgba(255, 255, 255, 0.8);
            border: 1px solid #e5e5e0;
        }

        /* 3D Checklist Items */
        .checklist-item-3d {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 16px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            background: linear-gradient(180deg, #ffffff, #f8fafc);
            box-shadow: 
                0 4px 0 0 #cbd5e1,
                0 4px 8px rgba(0, 0, 0, 0.03);
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.15);
        }
        .checklist-item-3d:hover {
            transform: translateY(-2px);
            box-shadow: 
                0 6px 0 0 #cbd5e1,
                0 8px 12px rgba(0, 0, 0, 0.05);
        }
        .checklist-item-3d-checked {
            border-color: #fbbf24;
            background: linear-gradient(180deg, #fffbeb, #fef3c7);
            box-shadow: 
                0 4px 0 0 #d97706,
                0 4px 8px rgba(245, 158, 11, 0.05);
        }
        .checklist-item-3d-checked:hover {
            transform: translateY(-2px);
            box-shadow: 
                0 6px 0 0 #d97706,
                0 8px 12px rgba(245, 158, 11, 0.08);
        }

        /* Master Toggle Container */
        .master-toggle-3d {
            background: linear-gradient(135deg, #fffbeb, #fef3c7);
            border: 1px solid #fde047;
            box-shadow: 
                0 5px 0 0 #d97706,
                0 8px 16px -4px rgba(245, 158, 11, 0.2);
        }

        /* Custom Grid Overlay Background */
        .grid-bg {
            background-color: #f6f5f0;
            background-image: 
                radial-gradient(at 0% 0%, hsla(36,92%,90%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(43,96%,92%,1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(38,90%,88%,1) 0, transparent 50%),
                linear-gradient(135deg, rgba(255, 253, 250, 0.94), rgba(247, 244, 234, 0.90)), 
                url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M30 0H0V30H30V0ZM29 1V29H1V1H29Z' fill='%23e8e6df' fill-opacity='0.6'/%3E%3C/svg%3E"),
                url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=2070');
            background-size: auto, auto, auto, auto, 30px 30px, cover;
            background-position: center;
            background-attachment: fixed;
        }
    </style>
</head>
<body class="h-full text-slate-800 antialiased no-select selection:bg-transparent grid-bg">

    <div class="min-h-screen flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8" x-data="{
        showWarning: false,
        check1: false,
        check2: false,
        check3: false,
        check4: false,
        toggleAll(checked) {
            this.check1 = checked;
            this.check2 = checked;
            this.check3 = checked;
            this.check4 = checked;
        },
        allChecked() {
            return this.check1 && this.check2 && this.check3 && this.check4;
        }
    }">
        <!-- Top Title & Logo Shield -->
        <div class="sm:mx-auto sm:w-full sm:max-w-4xl flex flex-col items-center">
            @if(\App\Models\Setting::get('system_logo'))
                <img class="mx-auto h-20 w-auto object-contain drop-shadow-[0_10px_15px_rgba(0,0,0,0.15)] transition-transform hover:scale-105 duration-300" src="{{ asset('storage/' . \App\Models\Setting::get('system_logo')) }}" alt="SUPA Logo">
            @else
                <div class="h-16 w-16 bg-gradient-to-tr from-amber-500 to-amber-600 rounded-3xl flex items-center justify-center font-black text-2xl text-slate-950 shadow-[0_8px_16px_rgba(245,158,11,0.3)] transition-transform hover:scale-105 duration-300 border border-amber-400">S</div>
            @endif
            <h2 class="mt-5 text-center text-3xl font-black uppercase tracking-tight text-stone-900 sm:text-4xl drop-shadow-sm">
                Mfumo wa Udahili wa SUPA
            </h2>
            <p class="mt-2 text-center text-xs font-bold text-amber-700 uppercase tracking-widest bg-amber-500/10 px-4 py-1.5 rounded-full border border-amber-500/10">
                {{ \App\Models\Setting::get('university_name', "Singida Teachers' Training College & OUT") }}
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-4xl">
            <!-- Main Consent Notice Screen -->
            <div x-show="!showWarning" 
                class="card-3d py-8 px-4 sm:px-10 space-y-8">
                
                @if(session('error'))
                    <div class="p-4 bg-red-500/10 border border-red-500/20 text-red-600 rounded-2xl text-xs font-bold shadow-inner">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Page Header Plaque -->
                <div class="border-b border-stone-200/80 pb-5 text-center">
                    <h3 class="text-2xl font-black text-stone-950 tracking-tight">Taarifa ya Faragha &amp; Ridhaa ya Data Binafsi</h3>
                    <p class="text-xs text-slate-500 mt-1.5 font-medium">Tafadhali kagua maelezo hapa chini kuhusu jinsi taarifa zako binafsi zinavyosimamiwa.</p>
                </div>

                <!-- Document Viewer Section (Expanded to fit the single column layout) -->
                <div class="space-y-4">
                    <!-- 3D Inset Digital Screen Content Viewport -->
                    <div class="screen-inset rounded-2xl p-5 space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-stone-150">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-black text-stone-400 uppercase tracking-wider block">Maudhui ya Hati:</span>
                                <span class="px-2.5 py-0.5 rounded-full bg-amber-100/60 text-amber-900 border border-amber-200/50 text-[9px] font-extrabold uppercase">{{ $activePolicy->title ?? 'Sera ya Faragha' }}</span>
                                @if(isset($activePolicy->version))
                                    <span class="text-[11px] font-bold text-stone-500 bg-stone-100 px-2 py-0.5 rounded border border-stone-200">Toleo {{ $activePolicy->version }}</span>
                                @endif
                            </div>
                            <span class="px-3 py-1 rounded-xl bg-red-50 text-red-750 text-[10px] font-bold border border-red-200/30 flex items-center gap-1 shadow-sm font-heading">
                                🔒 Ulinzi Umewezeshwa
                            </span>
                        </div>
                        @if($activePolicy && $activePolicy->content)
                            <div id="print-container" class="h-64 overflow-y-auto bg-white/50 p-5 rounded-xl border border-stone-200/40 text-[11.5px] text-stone-600 font-medium leading-relaxed space-y-4 no-select">
                                <!-- Privacy Policy Content -->
                                <div class="prose prose-stone max-w-none text-[11.5px] font-medium text-stone-700">
                                    <h4 class="font-black text-stone-900 text-xs uppercase mb-2">{{ $activePolicy->title }}</h4>
                                    <div class="prose-content no-select">
                                        {!! $activePolicy->content !!}
                                    </div>
                                </div>
                            </div>
                        @elseif($activePolicy && $activePolicy->file_path)
                            <div class="w-full rounded-2xl overflow-hidden border border-stone-200/50 shadow-inner bg-white h-[720px]">
                                <iframe src="{{ asset('storage/' . $activePolicy->file_path) }}#toolbar=0&navpanes=0&scrollbar=1" class="w-full h-full border-0" style="user-select: none;"></iframe>
                            </div>
                        @else
                            <div id="print-container" class="h-64 overflow-y-auto bg-white/50 p-5 rounded-xl border border-stone-200/40 text-[11.5px] text-stone-600 font-medium leading-relaxed space-y-4 no-select">
                                <div class="prose prose-stone max-w-none text-[11.5px] font-medium text-stone-700">
                                    <p class="italic text-stone-500 text-center py-8">Maudhui ya hati hayapatikani.</p>
                                </div>
                            </div>
                        @endif
                    </div>

                </div>

                <!-- Form Submit and Checklist -->
                <form action="{{ route('applicant.consent.accept') }}" method="POST" class="space-y-6 pt-4 border-t border-stone-200/60">
                    @csrf

                    <!-- Declarations Checklist -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between pb-2 border-b border-stone-200/60">
                            <span class="text-xs font-black uppercase text-stone-600 tracking-wider">Tamko na Ridhaa Zinazohitajika</span>
                            <span class="text-[10px] text-stone-500 font-bold uppercase">Chaguzi zote lazima ziwekewe alama</span>
                        </div>

                        <!-- Individual checklist items in 3D tiles -->
                        <div class="space-y-3 pt-2">
                            <!-- Checklist Item 1 -->
                            <label for="check_accurate" class="checklist-item-3d" :class="check1 && 'checklist-item-3d-checked'">
                                <div class="flex-shrink-0 mt-0.5">
                                    <input type="checkbox" id="check_accurate" name="confirm_accurate" x-model="check1" 
                                        class="rounded border-stone-300 text-amber-600 focus:ring-amber-500 cursor-pointer w-4.5 h-4.5">
                                </div>
                                <span class="leading-normal text-xs font-semibold text-stone-700 select-none">
                                    Nathibitisha kuwa taarifa ninazotoa ni sahihi na kamili kwa uelewa wangu wote.
                                </span>
                            </label>

                            <!-- Checklist Item 2 -->
                            <label for="check_read_privacy" class="checklist-item-3d" :class="check2 && 'checklist-item-3d-checked'">
                                <div class="flex-shrink-0 mt-0.5">
                                    <input type="checkbox" id="check_read_privacy" name="read_privacy" x-model="check2" 
                                        class="rounded border-stone-300 text-amber-600 focus:ring-amber-500 cursor-pointer w-4.5 h-4.5">
                                </div>
                                <span class="leading-normal text-xs font-semibold text-stone-700 select-none">
                                    Nimesoma, nimeelewa, na ninakubaliana na Sera ya Faragha ya Chuo.
                                </span>
                            </label>

                            <!-- Checklist Item 3 -->
                            <label for="check_consent_given" class="checklist-item-3d" :class="check3 && 'checklist-item-3d-checked'">
                                <div class="flex-shrink-0 mt-0.5">
                                    <input type="checkbox" id="check_consent_given" name="consent_given" x-model="check3" 
                                        class="rounded border-stone-300 text-amber-600 focus:ring-amber-500 cursor-pointer w-4.5 h-4.5">
                                </div>
                                <span class="leading-normal text-xs font-semibold text-stone-700 select-none">
                                    Ninatoa idhini kwa Chuo kukusanya, kuhifadhi, kuthibitisha, kuchakata, na kushiriki taarifa zangu binafsi kwa ajili ya udahili na usimamizi wa kitaaluma.
                                </span>
                            </label>

                            <!-- Checklist Item 4 -->
                            <label for="check_understand_rights" class="checklist-item-3d" :class="check4 && 'checklist-item-3d-checked'">
                                <div class="flex-shrink-0 mt-0.5">
                                    <input type="checkbox" id="check_understand_rights" name="understand_rights" x-model="check4" 
                                        class="rounded border-stone-300 text-amber-600 focus:ring-amber-500 cursor-pointer w-4.5 h-4.5">
                                </div>
                                <span class="leading-normal text-xs font-semibold text-stone-700 select-none">
                                    Naelewa haki zangu chini ya Sheria ya Ulinzi wa Taarifa Binafsi ya Mwaka 2022.
                                </span>
                            </label>

                            <!-- 3D Master Toggle ("Accept All") stays LAST -->
                            <div class="master-toggle-3d p-4 rounded-2xl flex items-center justify-between gap-6 transition-all duration-300 mt-4">
                                <div class="space-y-0.5">
                                    <span class="text-xs font-black text-amber-950 uppercase tracking-wider block">Kubali Tamko Zote (Accept All)</span>
                                    <span class="text-[10px] text-amber-900 font-medium leading-normal block">Kukubali kwa haraka na kuridhia masharti yote, sera, na taarifa za usahihi hapo juu.</span>
                                </div>
                                <div class="flex-shrink-0">
                                    <label class="relative inline-flex items-center cursor-pointer select-none">
                                        <input type="checkbox" :checked="allChecked()" @change="toggleAll($event.target.checked)" class="sr-only peer">
                                        <div class="w-14 h-7 bg-stone-300/80 rounded-full transition-all duration-300 peer-focus:outline-none border border-stone-300 shadow-[inset_0_3px_5px_rgba(0,0,0,0.1)] peer-checked:bg-amber-600 peer-checked:border-amber-700 relative after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-stone-300 after:rounded-full after:h-5 after:w-5 after:transition-all after:duration-300 peer-checked:after:translate-x-7 shadow-sm"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-2">
                        <button type="button" @click="showWarning = true" 
                            class="btn-3d-decline w-full sm:w-auto">
                            Kataa &amp; Ondoka
                        </button>
                        
                        <button type="submit" :disabled="!allChecked()" 
                            class="btn-3d-accept w-full sm:w-auto">
                            Endelea &rarr;
                        </button>
                    </div>
                </form>
            </div>

            <!-- Decline Confirmation Warning Card -->
            <div x-show="showWarning" x-cloak 
                class="card-3d bg-white/95 backdrop-blur-md border border-red-200/80 py-12 px-6 shadow-2xl rounded-[2.5rem] text-center space-y-6 sm:px-10 transition-all duration-300 max-w-xl mx-auto">
                <div class="w-16 h-16 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-2 border border-red-200 shadow-[0_8px_16px_rgba(220,38,38,0.15)] animate-pulse">
                    <span class="text-2xl font-bold">⚠️</span>
                </div>
                <h3 class="text-xl font-black text-stone-900 uppercase tracking-wide">Ridhaa ya Maombi Inahitajika</h3>
                <p class="text-xs text-stone-500 max-w-md mx-auto leading-relaxed font-medium">
                    Ili kuendelea na maombi yako, lazima ukague na kukubaliana na Sera ya Faragha. Chuo kinatakiwa kisheria kupata ridhaa yako chini ya Sheria ya Ulinzi wa Taarifa Binafsi ya Mwaka 2022.
                </p>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row justify-center items-center gap-4 pt-4">
                    <button type="button" @click="showWarning = false" 
                        class="btn-3d-decline w-full sm:w-auto">
                        Kagua Masharti
                    </button>
                    <form action="{{ route('applicant.consent.decline') }}" method="POST" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit" 
                            class="btn-3d-accept w-full sm:w-auto bg-gradient-to-r from-red-500 to-red-650 hover:from-red-600 hover:to-red-700 border-red-700 shadow-[0_5px_0_0_#991b1b,_0_10px_18px_rgba(220,38,38,0.2)]">
                            Ondoka kwenye Maombi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Restrictions to block Printing, Saving, and Copying -->
    <script>
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        document.addEventListener('keydown', function(e) {
            // Disable Print (Ctrl+P or Cmd+P)
            if ((e.ctrlKey || e.metaKey) && (e.key === 'p' || e.key === 'P' || e.keyCode === 80)) {
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
            // Disable Save (Ctrl+S or Cmd+S)
            if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'S' || e.keyCode === 83)) {
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
            // Disable Copy (Ctrl+C or Cmd+C)
            if ((e.ctrlKey || e.metaKey) && (e.key === 'c' || e.key === 'C' || e.keyCode === 67)) {
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
        });
    </script>
</body>
</html>
