<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950 overflow-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="Masuk ke portal EventPlan untuk mengelola proyek acara dan analisis Critical Path Method.">
    <title>Masuk — EventPlan</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full antialiased font-sans text-slate-800 bg-slate-950 overflow-hidden select-none">
    <div class="h-screen w-full flex flex-col lg:flex-row overflow-hidden">

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
                    Sistem Perencanaan &amp; Manajemen Acara
                </h1>
                <p class="mt-2.5 text-xs xl:text-sm text-violet-100/90 font-medium leading-relaxed max-w-md mx-auto drop-shadow-xs">
                    Platform Manajemen Proyek Event, Critical Path Method (CPM), dan Penjadwalan Terpadu yang Modern, Akurat, dan Akuntabel.
                </p>
                <div class="mt-5 flex flex-wrap items-center justify-center gap-2 text-[11px] text-white/90">
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-violet-950/50 backdrop-blur-md border border-violet-400/30 text-violet-200 shadow-sm">
                        <svg class="w-3.5 h-3.5 mr-1.5 text-fuchsia-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        CPM Otomatis
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-violet-950/50 backdrop-blur-md border border-violet-400/30 text-violet-200 shadow-sm">
                        <svg class="w-3.5 h-3.5 mr-1.5 text-fuchsia-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Gantt &amp; PERT
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-violet-950/50 backdrop-blur-md border border-violet-400/30 text-violet-200 shadow-sm">
                        <svg class="w-3.5 h-3.5 mr-1.5 text-fuchsia-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Ekspor PDF
                    </span>
                </div>
            </div>

            <!-- Footer Hak Cipta Kiri -->
            <div class="absolute bottom-5 left-8 xl:left-12 z-10 text-[11px] text-violet-300/70 font-medium">
                &copy; {{ date('Y') }} EventPlan. Seluruh Hak Cipta Dilindungi.
            </div>
        </div>

        <!-- Kolom Kanan: Form Login Bersih, Fixed Tanpa Scroll -->
        <div class="w-full lg:w-5/12 xl:w-2/5 h-full bg-white flex flex-col justify-between p-6 sm:p-8 lg:p-8 xl:p-10 overflow-y-auto lg:overflow-hidden select-text">
            <div class="max-w-md w-full mx-auto my-auto flex flex-col justify-center space-y-4">

                <!-- Header Brand & Logo -->
                <div>
                    <div class="w-12 h-12 rounded-2xl overflow-hidden mb-3 shadow-lg shadow-purple-500/25 border border-purple-200">
                        <img src="{{ asset('images/logo.png') }}" alt="EventPlan Logo" class="w-full h-full object-cover">
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Selamat Datang</h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">Silakan masuk untuk mengakses sistem EventPlan.</p>
                </div>

                <!-- Notifikasi Status -->
                @if(session('error'))
                    <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center space-x-2">
                        <svg class="w-4 h-4 shrink-0 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('success'))
                    <div class="p-3 rounded-xl bg-purple-50 border border-purple-200 text-purple-700 text-xs font-semibold flex items-center space-x-2">
                        <svg class="w-4 h-4 shrink-0 text-purple-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Tombol Google OAuth di Atas Form -->
                <div>
                    <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center px-4 py-2 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 bg-slate-50 hover:bg-slate-100 hover:border-slate-300 transition">
                        <svg class="w-4 h-4 mr-2.5 shrink-0" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
                            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
                            <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 10.03 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                        </svg>
                        Masuk dengan Google
                    </a>
                </div>

                <!-- Divider -->
                <div class="relative my-2">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
                    <div class="relative flex justify-center text-[10px] uppercase tracking-wider"><span class="bg-white px-2.5 text-slate-400 font-bold">Atau Masuk dengan Akun</span></div>
                </div>

                <!-- Form Login Utama -->
                <form method="POST" action="{{ route('login.perform') }}" class="space-y-3.5" x-data="{ showPassword: false }">
                    @csrf

                    @if($errors->has('email'))
                        <div class="p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium">
                            {{ $errors->first('email') }}
                        </div>
                    @endif

                    <!-- Identitas Akun -->
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Identitas Akun
                        </label>
                        <div class="relative flex items-center">
                            <span class="absolute left-3.5 text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </span>
                            <input type="text"
                                   name="email"
                                   id="login-identity"
                                   value="{{ old('email', 'admin@eventplan.local') }}"
                                   required
                                   autofocus
                                   placeholder="Username atau Email"
                                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:outline-none transition placeholder-slate-400 bg-white" />
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1 flex items-center">
                            <svg class="w-3 h-3 mr-1 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Gunakan alamat email atau username Anda.
                        </p>
                    </div>

                    <!-- Kata Sandi -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700">
                                Kata Sandi
                            </label>
                        </div>
                        <div class="relative flex items-center">
                            <span class="absolute left-3.5 text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            <input :type="showPassword ? 'text' : 'password'"
                                   name="password"
                                   id="login-password"
                                   required
                                   placeholder="••••••••"
                                   class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:outline-none transition placeholder-slate-400 bg-white font-mono" />
                            <button type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute right-3 text-slate-400 hover:text-slate-600 focus:outline-none p-1"
                                    tabindex="-1"
                                    title="Tampilkan / Sembunyikan Password">
                                <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        <div class="flex items-center justify-between text-xs pt-1.5">
                            <label class="flex items-center space-x-1.5 text-slate-600 cursor-pointer text-[11px]">
                                <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded text-purple-600 border-slate-300 focus:ring-purple-500">
                                <span>Ingat Saya</span>
                            </label>
                            <span class="text-[11px] text-purple-700 font-semibold hover:underline cursor-pointer">
                                Lupa Kata Sandi?
                            </span>
                        </div>
                    </div>

                    <!-- Tombol Masuk Ungu Solid -->
                    <button type="submit"
                            style="background-color: #9333ea !important; color: #ffffff !important;"
                            class="w-full py-2.5 px-4 rounded-xl text-sm font-bold transition flex items-center justify-center space-x-2 shadow-md shadow-purple-600/30 hover:brightness-110 active:scale-[0.99] cursor-pointer">
                        <span class="text-white font-bold">Masuk ke Sistem</span>
                        <svg class="w-4 h-4 ml-1 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                </form>

                <!-- Link Daftar Akun Baru -->
                <p class="text-center text-xs text-slate-500">
                    Belum memiliki akun?
                    <a href="{{ route('register') }}" class="font-bold text-purple-600 hover:text-purple-700 hover:underline">Daftar sekarang</a>
                </p>

                <!-- Pilihan Akun Demo (Dropdown Cepat) -->
                <div class="py-2 px-3 rounded-xl bg-slate-50 border border-slate-200">
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="demo-account-select" class="text-[11px] font-bold text-slate-700 flex items-center gap-1.5">
                            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Pilih Akun Demo:
                        </label>
                        <span class="text-[10px] text-slate-400 font-mono">(pass: password123)</span>
                    </div>
                    <div class="relative">
                        <select id="demo-account-select"
                                onchange="if(this.value){ document.getElementById('login-identity').value = this.value; document.getElementById('login-password').value = 'password123'; }"
                                class="w-full text-xs bg-white border border-slate-300 rounded-lg px-2.5 py-1.5 pr-8 text-slate-700 font-medium focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:outline-none appearance-none cursor-pointer">
                            <option value="">-- Pilih Akun Demo untuk Auto-Fill --</option>
                            <option value="admin@eventplan.local">👑 Administrator EventPlan (Admin) - admin@eventplan.local</option>
                            <option value="kontak@arieldaud.com">🎯 Mohamad Nazril R. Daud (Lead Planner) - kontak@arieldaud.com</option>
                            <option value="sarah@gemilang.com">📋 Sarah Anindya (Event Coordinator) - sarah@gemilang.com</option>
                            <option value="budi@gemilang.com">💻 Budi Santoso (Tech & Logistics) - budi@gemilang.com</option>
                            <option value="doni@gemilang.com">🎨 Doni Kusuma (Design & Creative) - doni@gemilang.com</option>
                            <option value="maya@gemilang.com">📢 Maya Putri (PR & Marketing) - maya@gemilang.com</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-500">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

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
