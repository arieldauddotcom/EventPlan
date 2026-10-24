<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="Daftar akun EventPlan baru untuk memulai perencanaan jadwal acara presisi tinggi.">
    <title>EventPlan - Daftar Akun Baru</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full antialiased font-sans text-slate-800 bg-slate-950 overflow-y-auto lg:overflow-hidden">
    <div class="min-h-screen lg:h-screen w-full flex flex-col lg:flex-row">

        <!-- Kolom Kiri: Visual Event & CPM Atmosphere dengan Glassmorphism Card (Fixed) -->
        <div class="relative hidden lg:flex lg:w-7/12 xl:w-3/5 h-full items-center justify-center p-8 lg:p-12 xl:p-16 overflow-hidden bg-slate-950">
            <!-- Background Image EventPlan -->
            <img src="{{ asset('images/eventplan-login-bg.webp') }}"
                 alt="EventPlan Background Visual"
                 class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none scale-[1.01]" />

            <!-- Soft Atmospheric Ambient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/20 to-slate-950/40 pointer-events-none"></div>

            <!-- Modern Frosted Glass Card -->
            <div class="relative z-10 w-full max-w-lg rounded-3xl p-8 xl:p-10 backdrop-blur-xl bg-slate-900/40 border border-violet-400/30 shadow-2xl shadow-purple-950/50 text-white text-center transform transition duration-500 hover:scale-[1.01]">
                <div class="inline-flex w-14 h-14 rounded-2xl overflow-hidden mb-4 shadow-xl shadow-purple-950/60 border border-white/20">
                    <img src="{{ asset('images/logo.png') }}" alt="EventPlan Logo" class="w-full h-full object-cover">
                </div>
                <h1 class="text-2xl xl:text-3xl font-extrabold tracking-tight text-white drop-shadow-md leading-tight">
                    Bergabung dengan EventPlan
                </h1>
                <p class="mt-2.5 text-xs xl:text-sm text-violet-100/90 font-medium leading-relaxed max-w-md mx-auto drop-shadow-xs">
                    Mulai rancang dan kelola proyek event Anda dengan Critical Path Method (CPM), diagram Gantt, alokasi sumber daya, dan kolaborasi tim yang efisien.
                </p>
                <div class="mt-5 flex flex-wrap items-center justify-center gap-2 text-[11px] text-white/90">
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-violet-950/50 backdrop-blur-md border border-violet-400/30 text-violet-200 shadow-sm">
                        <svg class="w-3.5 h-3.5 mr-1.5 text-fuchsia-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Gratis &amp; Cepat
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-violet-950/50 backdrop-blur-md border border-violet-400/30 text-violet-200 shadow-sm">
                        <svg class="w-3.5 h-3.5 mr-1.5 text-fuchsia-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Kolaborasi Tim
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-violet-950/50 backdrop-blur-md border border-violet-400/30 text-violet-200 shadow-sm">
                        <svg class="w-3.5 h-3.5 mr-1.5 text-fuchsia-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Gantt &amp; CPM Realtime
                    </span>
                </div>
            </div>

            <!-- Footer Hak Cipta Kiri -->
            <div class="absolute bottom-5 left-8 xl:left-12 z-10 text-[11px] text-violet-300/70 font-medium">
                &copy; {{ date('Y') }} EventPlan. Seluruh Hak Cipta Dilindungi.
            </div>
        </div>

        <!-- Kolom Kanan: Form Pendaftaran Bersih & Responsif Mobile -->
        <div class="w-full lg:w-5/12 xl:w-2/5 min-h-screen lg:h-full bg-white flex flex-col justify-between p-5 sm:p-8 lg:p-8 xl:p-10 overflow-y-auto select-text">
            <div class="max-w-md w-full mx-auto my-auto flex flex-col justify-center space-y-3.5">

                <!-- Header Brand & Logo -->
                <div>
                    <div class="w-12 h-12 rounded-2xl overflow-hidden mb-2.5 shadow-lg shadow-purple-500/25 border border-purple-200">
                        <img src="{{ asset('images/logo.png') }}" alt="EventPlan Logo" class="w-full h-full object-cover">
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Daftar Akun Baru</h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">Buat akun untuk mengelola proyek dan linimasa acara.</p>
                </div>

                <!-- Notifikasi Status Error -->
                @if(session('error'))
                    <div class="p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center space-x-2">
                        <svg class="w-4 h-4 shrink-0 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Google OAuth Button -->
                <div>
                    <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center px-4 py-2 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 bg-slate-50 hover:bg-slate-100 hover:border-slate-300 transition">
                        <svg class="w-4 h-4 mr-2.5 shrink-0" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
                            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
                            <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 10.03 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                        </svg>
                        Daftar dengan Akun Google
                    </a>
                </div>

                <!-- Divider -->
                <div class="relative my-1">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
                    <div class="relative flex justify-center text-[10px] uppercase tracking-wider"><span class="bg-white px-2.5 text-slate-400 font-bold">Atau Isi Form Pendaftaran</span></div>
                </div>

                <!-- Form Pendaftaran -->
                <form method="POST" action="{{ route('register.perform') }}" class="space-y-3">
                    @csrf

                    <!-- Nama Lengkap & Username Berdampingan 2 Kolom -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Nama Lengkap
                            </label>
                            <input type="text"
                                   name="name"
                                   value="{{ old('name') }}"
                                   required
                                   autofocus
                                   placeholder="Budi Santoso"
                                   class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:outline-none transition placeholder-slate-400 bg-white" />
                            @error('name')
                                <p class="text-rose-600 text-[10px] mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Username
                            </label>
                            <input type="text"
                                   name="username"
                                   value="{{ old('username') }}"
                                   required
                                   placeholder="budi_s"
                                   class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:outline-none transition placeholder-slate-400 bg-white" />
                            @error('username')
                                <p class="text-rose-600 text-[10px] mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Alamat Email
                        </label>
                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               placeholder="budi@example.com"
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:outline-none transition placeholder-slate-400 bg-white" />
                        @error('email')
                            <p class="text-rose-600 text-[10px] mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kata Sandi & Konfirmasi -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Kata Sandi
                            </label>
                            <input type="password"
                                   name="password"
                                   required
                                   placeholder="Min. 8 karakter"
                                   class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:outline-none transition placeholder-slate-400 bg-white font-mono" />
                            @error('password')
                                <p class="text-rose-600 text-[10px] mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                                Konfirmasi
                            </label>
                            <input type="password"
                                   name="password_confirmation"
                                   required
                                   placeholder="Ulangi kata sandi"
                                   class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:outline-none transition placeholder-slate-400 bg-white font-mono" />
                        </div>
                    </div>

                    <!-- Tombol Daftar Ungu Solid -->
                    <div class="pt-1">
                        <button type="submit"
                                style="background-color: #9333ea !important; color: #ffffff !important;"
                                class="w-full py-2.5 px-4 rounded-xl text-sm font-bold transition flex items-center justify-center space-x-2 shadow-md shadow-purple-600/30 hover:brightness-110 active:scale-[0.99] cursor-pointer">
                            <span class="text-white font-bold">Daftar Sekarang</span>
                            <svg class="w-4 h-4 ml-1 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Link Masuk Kembali -->
                <p class="text-center text-xs text-slate-500">
                    Sudah memiliki akun?
                    <a href="{{ route('login') }}" class="font-bold text-purple-600 hover:text-purple-700 hover:underline">Masuk di sini</a>
                </p>

                <!-- Kembali ke Beranda Public -->
                <div class="text-center pt-0.5">
                    <a href="{{ route('welcome') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-purple-600 transition">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Beranda Public
                    </a>
                </div>

            </div>
        </div>

    </div>
</body>
</html>
