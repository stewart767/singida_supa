<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajira Market Portal - Registration</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at top right, #1e3a8a 0%, #0f172a 70%);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between text-slate-100">
    <!-- Header -->
    <header class="border-b border-slate-800 bg-slate-900/60 backdrop-blur-md py-4 px-6 sm:px-12 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center text-slate-950 font-black text-xl shadow-lg shadow-amber-500/20">
                A
            </div>
            <div>
                <h1 class="text-base font-extrabold tracking-tight text-white">AJIRA MARKET PORTAL</h1>
                <p class="text-[10px] text-slate-400 font-bold tracking-widest uppercase">United Republic of Tanzania</p>
            </div>
        </div>
        <div class="px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
            Secure Portal
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center p-6 my-10">
        <div class="w-full max-w-xl bg-slate-900/80 border border-slate-800 rounded-3xl p-8 sm:p-10 shadow-2xl backdrop-blur-md space-y-8">
            <div class="text-center space-y-2">
                <span class="px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-black uppercase tracking-wider">
                    Applicant Registration
                </span>
                <h2 class="text-2xl sm:text-3xl font-black text-white">Link Your Ajira Portal Account</h2>
                <p class="text-xs text-slate-400 leading-relaxed max-w-md mx-auto">
                    Complete your registration on the National Ajira Market Portal to authenticate and submit applications to public institutions.
                </p>
            </div>

            @if($vacancy)
                <!-- Linked Job Summary -->
                <div class="p-4 bg-slate-800/50 border border-slate-700/60 rounded-2xl flex items-center justify-between gap-4">
                    <div class="space-y-1">
                        <span class="text-[9px] font-bold text-amber-500 uppercase tracking-widest">Applying For:</span>
                        <h4 class="text-xs font-black text-white truncate max-w-[280px]">{{ $vacancy->job_title }}</h4>
                        <p class="text-[10px] text-slate-400">Ref: {{ $vacancy->vacancy_number }}</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg bg-blue-500/10 text-blue-400 border border-blue-500/20 text-[10px] font-bold">
                        Pending Link
                    </span>
                </div>
            @endif

            <form action="{{ route('public.careers.ajira.callback') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="job_ref" value="{{ $jobRef }}">

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="space-y-1.5 font-semibold text-slate-300">
                        <label for="name" class="block text-xs font-bold text-slate-400 uppercase">Full Name</label>
                        <input type="text" name="name" id="name" required class="w-full bg-slate-950/40 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/20 transition-all font-semibold" value="{{ $user->name ?? old('name') }}" {{ $user ? 'readonly' : '' }}>
                        @error('name')
                            <p class="text-[10px] text-red-500 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5 font-semibold text-slate-300">
                        <label for="email" class="block text-xs font-bold text-slate-400 uppercase">Email Address</label>
                        <input type="email" name="email" id="email" required class="w-full bg-slate-950/40 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/20 transition-all font-semibold" value="{{ $user->email ?? old('email') }}" {{ $user ? 'readonly' : '' }}>
                        @error('email')
                            <p class="text-[10px] text-red-500 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5 font-semibold text-slate-300">
                        <label for="phone" class="block text-xs font-bold text-slate-400 uppercase">Phone Number</label>
                        <input type="text" name="phone" id="phone" required class="w-full bg-slate-950/40 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/20 transition-all font-semibold" value="{{ $user->phone ?? old('phone') }}" {{ $user ? 'readonly' : '' }}>
                        @error('phone')
                            <p class="text-[10px] text-red-500 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                @if(!$user)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5 font-semibold text-slate-300">
                        <label for="password" class="block text-xs font-bold text-slate-400 uppercase">Password</label>
                        <input type="password" name="password" id="password" required class="w-full bg-slate-950/40 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/20 transition-all font-semibold" placeholder="••••••••">
                        @error('password')
                            <p class="text-[10px] text-red-500 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5 font-semibold text-slate-300">
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-400 uppercase">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required class="w-full bg-slate-950/40 border border-slate-800 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/20 transition-all font-semibold" placeholder="••••••••">
                    </div>
                </div>
                @endif

                <div class="space-y-1.5 font-semibold text-slate-300">
                    <label for="nida_number" class="block text-xs font-bold text-slate-400 uppercase">National ID (NIDA) Number</label>
                    <input type="text" name="nida_number" id="nida_number" required class="w-full bg-slate-950/40 border border-slate-800 rounded-xl px-4 py-3.5 text-xs text-white placeholder-slate-600 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/20 transition-all font-mono font-bold" placeholder="199XXXXXXXXXXXXXXX" value="{{ $user->applicant->nida_number ?? old('nida_number') }}">
                    @error('nida_number')
                        <p class="text-[10px] text-red-500 font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Credential Sharing Warning Info Box -->
                <div class="p-4 bg-amber-500/5 border border-amber-500/20 rounded-2xl space-y-2 text-xs text-slate-300">
                    <h5 class="font-extrabold text-amber-500 flex items-center gap-1">
                        🔑 Account Sync & Authentication
                    </h5>
                    <p class="leading-relaxed font-semibold">
                        Your Ajira Market credentials will be securely synchronized to SUPA University to authenticate your applications without requiring separate local accounts.
                    </p>
                </div>

                <div class="flex items-start gap-2.5 pt-2">
                    <input type="checkbox" id="terms" required class="mt-1 w-4 h-4 rounded bg-slate-950 border-slate-800 text-amber-500 focus:ring-0 focus:ring-offset-0 cursor-pointer">
                    <label for="terms" class="text-[11px] text-slate-400 font-semibold cursor-pointer select-none leading-relaxed">
                        I verify that the details above are correct and approve the synchronization of my Ajira Market credentials with SUPA University.
                    </label>
                </div>

                <button type="submit" class="w-full py-4 rounded-2xl bg-amber-500 text-slate-950 font-black text-xs uppercase tracking-wider hover:bg-amber-400 active:scale-[0.98] transition-all shadow-xl shadow-amber-500/10 mt-4 block text-center">
                    Register & Continue to Application &rarr;
                </button>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800/80 bg-slate-950/40 py-6 px-6 text-center text-[10px] text-slate-500 font-bold uppercase tracking-widest">
        &copy; 2026 Ajira Market Portal. All Rights Reserved. Prime Minister's Office.
    </footer>
</body>
</html>
