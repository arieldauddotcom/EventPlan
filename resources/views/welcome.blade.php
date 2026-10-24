<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventPlan — Solusi Perencanaan &amp; Manajemen Waktu Acara Berbasis Critical Path Method</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="EventPlan adalah aplikasi manajemen proyek dan linimasa acara berbasis Critical Path Method (CPM). Hitung jalur kritis otomatis, visualisasi Gantt chart, diagram PERT, dan alokasi tim secara akurat.">
    <meta name="keywords" content="EventPlan, critical path method, CPM, gantt chart, diagram pert, manajemen proyek acara, event planner, linimasa acara, manajemen jadwal event">
    <meta name="author" content="EventPlan">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="EventPlan — Solusi Perencanaan &amp; Manajemen Waktu Acara Berbasis Critical Path Method">
    <meta property="og:description" content="Perencanaan linimasa acara presisi tinggi dengan algoritma Critical Path Method (CPM), visualisasi interaktif Gantt Chart, PERT Network, dan optimalisasi beban kerja tim.">
    <meta property="og:image" content="{{ asset('images/logo.png') }}">
    <meta property="og:locale" content="id_ID">
    <meta property="og:site_name" content="EventPlan">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url('/') }}">
    <meta name="twitter:title" content="EventPlan — Manajemen Waktu Acara Berbasis Critical Path Method">
    <meta name="twitter:description" content="Optimalkan jadwal dan hindari keterlambatan event dengan analisis jalur kritis (CPM) dan visualisasi Gantt timeline cerdas.">
    <meta name="twitter:image" content="{{ asset('images/logo.png') }}">
    <meta name="theme-color" content="#030712">

    <!-- Structured Data (JSON-LD Schema) -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@graph": [
        {
          "@@type": "Organization",
          "@@id": "{{ url('/#organization') }}",
          "name": "EventPlan",
          "url": "{{ url('/') }}",
          "logo": {
            "@@type": "ImageObject",
            "url": "{{ asset('images/logo.png') }}"
          }
        },
        {
          "@@type": "WebSite",
          "@@id": "{{ url('/#website') }}",
          "url": "{{ url('/') }}",
          "name": "EventPlan",
          "description": "Platform Manajemen Proyek Acara & Penjadwalan Critical Path Method (CPM)",
          "publisher": {
            "@@id": "{{ url('/#organization') }}"
          },
          "inLanguage": "id-ID"
        },
        {
          "@@type": "SoftwareApplication",
          "@@id": "{{ url('/#software') }}",
          "name": "EventPlan",
          "applicationCategory": "BusinessApplication",
          "operatingSystem": "Web Browser",
          "description": "Aplikasi manajemen linimasa dan penjadwalan proyek acara berbasis algoritma Critical Path Method (CPM) dengan diagram Gantt dan PERT interaktif.",
          "url": "{{ url('/') }}",
          "image": "{{ asset('images/logo.png') }}",
          "offers": {
            "@@type": "Offer",
            "price": "0",
            "priceCurrency": "IDR"
          },
          "featureList": [
            "Critical Path Method (CPM) Forward & Backward Pass Otomatis",
            "Deteksi Siklus Ketergantungan DAG (Directed Acyclic Graph)",
            "Visualisasi Gantt Chart Dinamis",
            "Diagram Jaringan PERT Interaktif",
            "Manajemen Beban Kerja & Resource Acara",
            "Kalender Kerja & Hari Libur Kustom",
            "Ekspor Laporan PDF & CSV dengan Proteksi Formula Injection"
          ]
        }
      ]
    }
    </script>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes float-slow {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-12px) rotate(0.8deg); }
        }
        @keyframes float-reverse {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(10px) rotate(-0.8deg); }
        }
        @keyframes pulse-glow {
            0%, 100% { opacity: 0.35; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(1.1); }
        }
        @keyframes gradient-shift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        @keyframes cpm-dash {
            to { stroke-dashoffset: -48; }
        }
        @keyframes radar-pulse {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.15); opacity: 0.2; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }
        @keyframes shimmer {
            100% { transform: translateX(100%); }
        }
        .animate-float-slow {
            animation: float-slow 7s ease-in-out infinite;
        }
        .animate-float-reverse {
            animation: float-reverse 6s ease-in-out infinite;
        }
        .animate-pulse-glow {
            animation: pulse-glow 8s ease-in-out infinite;
        }
        .animate-cpm-dash {
            stroke-dasharray: 6 6;
            animation: cpm-dash 2.2s linear infinite;
        }
        .animate-radar {
            animation: radar-pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        .gradient-text-animated {
            background: linear-gradient(135deg, #ffffff 0%, #e9d5ff 25%, #c084fc 50%, #f472b6 80%, #a855f7 100%);
            background-size: 250% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradient-shift 7s ease infinite;
        }
        .grid-pattern {
            background-image: radial-gradient(rgba(192, 132, 252, 0.14) 1px, transparent 1px);
            background-size: 32px 32px;
        }
        .glow-card {
            box-shadow: 0 0 35px -5px rgba(168, 85, 247, 0.2);
        }
        .glow-card:hover {
            box-shadow: 0 0 50px 0px rgba(168, 85, 247, 0.35);
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-full bg-slate-950 text-slate-100 font-sans selection:bg-purple-600 selection:text-white antialiased overflow-x-hidden relative"
      x-data="{ 
          mobileNav: false, 
          activeTab: 'gantt',
          simStep: 1,
          simDelay: 0,
          activeFaq: 1,
          eventTab: 'konser',
          cpmDuration() {
              return 24 + parseInt(this.simDelay);
          }
      }">

    <!-- Ambient Glowing Orbs Background -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden -z-10">
        <div class="absolute -top-40 -left-40 w-[650px] h-[650px] bg-purple-700/25 rounded-full blur-[140px] animate-pulse-glow"></div>
        <div class="absolute top-1/3 -right-40 w-[600px] h-[600px] bg-violet-600/20 rounded-full blur-[160px] animate-pulse-glow" style="animation-delay: -3s;"></div>
        <div class="absolute -bottom-40 left-1/3 w-[700px] h-[700px] bg-fuchsia-700/20 rounded-full blur-[170px] animate-pulse-glow" style="animation-delay: -5s;"></div>
        <div class="absolute inset-0 grid-pattern opacity-40"></div>
    </div>


    <!-- Navigation Bar -->
    <header class="sticky top-0 z-50 backdrop-blur-xl bg-slate-950/80 border-b border-purple-900/30 transition duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand Logo & Name -->
                <a href="{{ route('welcome') }}" class="flex items-center space-x-3 group">
                    <div class="relative">
                        <div class="absolute -inset-1 bg-gradient-to-r from-purple-600 to-fuchsia-600 rounded-xl blur-sm opacity-70 group-hover:opacity-100 transition duration-300"></div>
                        <img src="{{ asset('images/logo.png') }}" alt="EventPlan Logo" class="relative w-11 h-11 rounded-xl object-cover ring-1 ring-white/20 shadow-lg shadow-purple-950/60">
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xl font-black tracking-tight text-white group-hover:text-purple-300 transition">EventPlan</span>
                        <span class="text-[10px] uppercase font-bold tracking-widest text-purple-400 -mt-1">CPM Event Management</span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden lg:flex items-center space-x-8 text-sm font-medium text-slate-300">
                    <a href="#fitur" class="hover:text-purple-300 transition duration-200">Fitur Utama</a>
                    <a href="#simulator" class="hover:text-purple-300 transition duration-200 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-fuchsia-400 animate-ping"></span>
                        Simulasi CPM
                    </a>
                    <a href="#visualisasi" class="hover:text-purple-300 transition duration-200">Gantt &amp; PERT</a>
                    <a href="#kegunaan" class="hover:text-purple-300 transition duration-200">Solusi Acara</a>
                    <a href="#faq" class="hover:text-purple-300 transition duration-200">FAQ</a>
                </nav>

                <!-- Auth Buttons -->
                <div class="hidden sm:flex items-center space-x-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-purple-600 hover:bg-purple-700 shadow-lg shadow-purple-900/50 transition duration-200 transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Buka Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-slate-200 hover:text-white transition duration-200">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-purple-600 to-fuchsia-600 hover:from-purple-500 hover:to-fuchsia-500 shadow-lg shadow-purple-900/40 transition duration-200 transform hover:-translate-y-0.5 hover:shadow-purple-700/50">
                            Daftar Sekarang
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <div class="lg:hidden flex items-center">
                    <button @click="mobileNav = !mobileNav" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-purple-950/40 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileNav" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path x-show="mobileNav" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-show="mobileNav" x-cloak class="lg:hidden border-b border-purple-900/40 bg-slate-950/95 px-4 pt-2 pb-6 space-y-3">
            <a @click="mobileNav = false" href="#fitur" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-300 hover:bg-purple-950/50 hover:text-white">Fitur Utama</a>
            <a @click="mobileNav = false" href="#simulator" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-300 hover:bg-purple-950/50 hover:text-white">Simulasi CPM</a>
            <a @click="mobileNav = false" href="#visualisasi" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-300 hover:bg-purple-950/50 hover:text-white">Gantt &amp; PERT</a>
            <a @click="mobileNav = false" href="#kegunaan" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-300 hover:bg-purple-950/50 hover:text-white">Solusi Acara</a>
            <a @click="mobileNav = false" href="#faq" class="block px-3 py-2 rounded-lg text-base font-medium text-slate-300 hover:bg-purple-950/50 hover:text-white">FAQ</a>
            <div class="pt-4 border-t border-purple-900/30 flex flex-col space-y-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="w-full text-center px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-purple-600 hover:bg-purple-700">
                        Buka Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="w-full text-center px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-200 border border-purple-700/50 hover:bg-purple-950/40">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="w-full text-center px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-purple-600 hover:bg-purple-700">
                        Daftar Akun Baru
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative pt-12 pb-24 lg:pt-20 lg:pb-36 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                
                <!-- Hero Left: Headline & CTA -->
                <div class="lg:col-span-7 text-center lg:text-left">
                    <!-- Badge Notification Pill -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-purple-950/70 border border-purple-500/40 text-purple-200 text-xs sm:text-sm font-medium mb-6 shadow-inner backdrop-blur-md">
                        <span class="flex h-2.5 w-2.5 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-purple-500"></span>
                        </span>
                        <span class="font-bold text-white tracking-wide">Critical Path Method Engine</span>
                        <span class="text-purple-400 hidden sm:inline">&bull; 0 Hari Keterlambatan</span>
                    </div>

                    <!-- Main Catchy Title -->
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight text-white leading-[1.12]">
                        Jamin Sukses Hari-H Acara dengan
                        <span class="gradient-text-animated block mt-1 sm:mt-2">Jalur Kritis (CPM) Otomatis.</span>
                    </h1>

                    <p class="mt-6 text-base sm:text-lg text-slate-300 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        Platform manajemen kepanitiaan festival, konser, konferensi, dan expo. 
                        Kalkulasi ilmiah <span class="text-purple-300 font-semibold">Forward &amp; Backward Pass</span>, 
                        temukan tugas berisiko tinggi tanpa cadangan waktu (Float = 0), dan cegah kegagalan acara sebelum terjadi.
                    </p>

                    <!-- CTAs -->
                    <div class="mt-8 sm:mt-10 space-y-3.5">
                        <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                            @auth
                                <a href="{{ route('dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 rounded-xl text-base font-extrabold text-white bg-gradient-to-r from-purple-600 via-purple-700 to-fuchsia-600 hover:from-purple-500 hover:to-fuchsia-500 shadow-xl shadow-purple-900/60 transition duration-300 transform hover:-translate-y-1 hover:shadow-purple-700/80 group">
                                    Buka Dashboard Proyek
                                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-1.5 transition duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 rounded-xl text-base font-extrabold text-white bg-gradient-to-r from-purple-600 via-purple-700 to-fuchsia-600 hover:from-purple-500 hover:to-fuchsia-500 shadow-xl shadow-purple-900/60 transition duration-300 transform hover:-translate-y-1 hover:shadow-purple-700/80 group">
                                    Mulai Rencanakan Gratis
                                    <svg class="w-5 h-5 ml-2 group-hover:translate-x-1.5 transition duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                </a>
                                <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-4 rounded-xl text-base font-semibold text-purple-200 hover:text-white bg-purple-950/50 hover:bg-purple-900/60 border border-purple-500/40 backdrop-blur-md transition duration-200 group shadow-md hover:border-purple-400">
                                    <svg class="w-5 h-5 mr-2 text-purple-300 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                    Sudah Punya Akun? Masuk
                                </a>
                            @endauth
                        </div>

                        <!-- Tombol Baris Bawah: Simulasi CPM -->
                        <div class="flex items-center justify-center lg:justify-start">
                            <a href="#simulator" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-slate-300 hover:text-white bg-slate-900/60 hover:bg-purple-950/40 border border-purple-500/25 hover:border-purple-400/50 backdrop-blur-md transition duration-200 group">
                                <svg class="w-4 h-4 mr-2 text-fuchsia-400 group-hover:rotate-12 transition duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Ingin coba dulu tanpa registrasi? <span class="text-purple-300 font-bold ml-1.5 underline decoration-purple-400/50 group-hover:text-purple-200">Coba Simulasi Interaktif &darr;</span>
                            </a>
                        </div>
                    </div>

                    <!-- Trust Metrics / Highlights -->
                    <div class="mt-12 pt-8 border-t border-purple-900/40 grid grid-cols-3 gap-3 sm:gap-6 text-left">
                        <div class="bg-purple-950/30 p-3 sm:p-4 rounded-2xl border border-purple-800/30 backdrop-blur-sm transform hover:scale-105 transition duration-200">
                            <div class="text-2xl sm:text-3xl font-black text-white flex items-center">
                                100<span class="text-purple-400 text-lg sm:text-xl">%</span>
                            </div>
                            <div class="text-xs text-purple-300/90 mt-1 font-medium">Akurasi CPM Matematis</div>
                        </div>
                        <div class="bg-purple-950/30 p-3 sm:p-4 rounded-2xl border border-purple-800/30 backdrop-blur-sm transform hover:scale-105 transition duration-200">
                            <div class="text-2xl sm:text-3xl font-black text-rose-400 flex items-center">
                                0 <span class="text-rose-300 text-sm sm:text-base ml-1">Float</span>
                            </div>
                            <div class="text-xs text-purple-300/90 mt-1 font-medium">Deteksi Titik Kritis Acara</div>
                        </div>
                        <div class="bg-purple-950/30 p-3 sm:p-4 rounded-2xl border border-purple-800/30 backdrop-blur-sm transform hover:scale-105 transition duration-200">
                            <div class="text-2xl sm:text-3xl font-black text-fuchsia-400 flex items-center">
                                360<span class="text-fuchsia-300 text-base">&deg;</span>
                            </div>
                            <div class="text-xs text-purple-300/90 mt-1 font-medium">Visual Gantt &amp; PERT</div>
                        </div>
                    </div>
                </div>

                <!-- Hero Right: Interactive CPM Mockup & Visual Graphic -->
                <div class="lg:col-span-5 relative">
                    <!-- Glow Behind Showcase -->
                    <div class="absolute -inset-4 bg-gradient-to-tr from-purple-600/40 via-fuchsia-600/30 to-violet-500/40 rounded-3xl blur-2xl opacity-75"></div>

                    <!-- Glassmorphism Mockup Card -->
                    <div class="relative rounded-3xl bg-slate-900/90 border border-purple-400/30 shadow-2xl shadow-purple-950/80 p-6 backdrop-blur-xl animate-float-slow glow-card">
                        <!-- Top Bar Mockup -->
                        <div class="flex items-center justify-between pb-4 mb-4 border-b border-purple-900/40">
                            <div class="flex items-center space-x-2">
                                <span class="w-3 h-3 rounded-full bg-rose-500/80"></span>
                                <span class="w-3 h-3 rounded-full bg-amber-500/80"></span>
                                <span class="w-3 h-3 rounded-full bg-emerald-500/80"></span>
                                <span class="text-xs font-mono text-purple-200 ml-2 font-semibold">Soundfest Nusantara 2026</span>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-500/20 border border-rose-500/50 text-rose-300 animate-pulse">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                CPM AKTIF
                            </span>
                        </div>

                        <!-- CPM Critical Path Nodes Simulation -->
                        <div class="space-y-3.5 text-xs">
                            <!-- Task Node 1: Critical -->
                            <div class="p-3.5 rounded-2xl bg-purple-950/70 border border-purple-500/50 shadow-md flex items-center justify-between transition hover:border-purple-400">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-purple-600 to-indigo-600 flex items-center justify-center font-black text-white text-xs shadow-md">
                                        T1
                                    </div>
                                    <div>
                                        <div class="font-bold text-white flex items-center gap-1.5">
                                            Sewa Venue &amp; Perizinan Polsek
                                            <span class="px-1.5 py-0.5 text-[9px] font-black rounded-md bg-rose-500/25 text-rose-300 border border-rose-500/40">KRITIS</span>
                                        </div>
                                        <div class="text-[11px] text-purple-300/80 mt-0.5">ES: Hr 1 &bull; EF: Hr 14 &bull; Float: <strong class="text-rose-400">0 hari</strong></div>
                                    </div>
                                </div>
                                <span class="text-xs font-black text-emerald-400 bg-emerald-950/60 px-2 py-0.5 rounded-lg border border-emerald-500/30">100%</span>
                            </div>

                            <!-- Animated Connecting Arrow -->
                            <div class="flex items-center justify-center -my-1 text-purple-400">
                                <svg class="w-5 h-5 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                            </div>

                            <!-- Task Node 2: Critical -->
                            <div class="p-3.5 rounded-2xl bg-purple-950/70 border border-purple-500/50 shadow-md flex items-center justify-between transition hover:border-purple-400">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-purple-600 to-fuchsia-600 flex items-center justify-center font-black text-white text-xs shadow-md">
                                        T2
                                    </div>
                                    <div>
                                        <div class="font-bold text-white flex items-center gap-1.5">
                                            Rigging Stage &amp; Main Sound Rig
                                            <span class="px-1.5 py-0.5 text-[9px] font-black rounded-md bg-rose-500/25 text-rose-300 border border-rose-500/40">KRITIS</span>
                                        </div>
                                        <div class="text-[11px] text-purple-300/80 mt-0.5">ES: Hr 15 &bull; EF: Hr 22 &bull; Float: <strong class="text-rose-400">0 hari</strong></div>
                                    </div>
                                </div>
                                <span class="text-xs font-black text-purple-200 bg-purple-900/60 px-2 py-0.5 rounded-lg border border-purple-500/30">70%</span>
                            </div>

                            <!-- Animated Connecting Arrow -->
                            <div class="flex items-center justify-center -my-1 text-purple-400">
                                <svg class="w-5 h-5 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                            </div>

                            <!-- Task Node 3: Non-Critical With Slack -->
                            <div class="p-3.5 rounded-2xl bg-slate-900/70 border border-slate-700/50 shadow-sm flex items-center justify-between opacity-85 transition hover:opacity-100">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-xl bg-slate-800 flex items-center justify-center font-bold text-slate-300 text-xs">
                                        T3
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-200">Cetak Lanyard &amp; Merchandise</div>
                                        <div class="text-[11px] text-slate-400 mt-0.5">ES: Hr 15 &bull; EF: Hr 18 &bull; Float: <span class="text-emerald-400 font-semibold">+4 hari (Aman)</span></div>
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-slate-400 bg-slate-800 px-2 py-0.5 rounded-lg">40%</span>
                            </div>
                        </div>

                        <!-- Mini Floating Card -->
                        <div class="mt-4 p-3.5 rounded-2xl bg-gradient-to-r from-purple-900/60 via-fuchsia-950/40 to-indigo-900/60 border border-purple-400/30 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 rounded-xl bg-purple-500/30 text-purple-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <div class="text-white font-bold text-xs">Estimasi Durasi Acara</div>
                                    <div class="text-[11px] text-purple-300/90 font-medium">24 Hari Kerja (Melompati Akhir Pekan &amp; Libur)</div>
                                </div>
                            </div>
                            <span class="text-xs font-mono font-black text-emerald-400 px-2.5 py-1 rounded-lg bg-emerald-950/80 border border-emerald-500/40">TEPAT WAKTU</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Interactive CPM Simulator (Live Playground) -->
    <section id="simulator" class="py-20 bg-slate-900/70 border-y border-purple-900/40 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-fuchsia-950/70 border border-fuchsia-500/30 text-fuchsia-300 text-xs font-bold uppercase tracking-wider mb-3">
                    Interactive Playground
                </div>
                <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight">
                    Coba Langsung Simulasi Jalur Kritis (CPM)
                </h2>
                <p class="mt-3 text-base text-slate-300 leading-relaxed">
                    Geser slider di bawah ini untuk melihat bagaimana keterlambatan satu tugas kritis berdampak langsung pada seluruh target tanggal Hari-H acara.
                </p>
            </div>

            <!-- Simulator Widget Container -->
            <div class="max-w-4xl mx-auto rounded-3xl bg-slate-950/80 border border-purple-500/40 p-6 sm:p-10 shadow-2xl shadow-purple-950/60 backdrop-blur-xl">
                <!-- Control Panel -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center pb-8 border-b border-purple-900/40">
                    <div class="md:col-span-7">
                        <label class="block text-sm font-bold text-white mb-2">
                            Simulasi Delay pada Tugas Kritis (Stage &amp; Sound Rigging):
                        </label>
                        <div class="flex items-center space-x-4">
                            <input type="range" min="0" max="10" step="1" x-model="simDelay" class="w-full h-2.5 bg-slate-800 rounded-lg appearance-none cursor-pointer accent-purple-500">
                            <span class="px-3 py-1.5 rounded-xl bg-purple-950 border border-purple-500/50 text-white font-mono font-bold text-sm min-w-[70px] text-center" x-text="'+' + simDelay + ' Hari'">
                                +0 Hari
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-2">
                            Tugas di Jalur Kritis memiliki Total Float = 0. Setiap penambahan 1 hari akan langsung menggeser target Hari-H acara.
                        </p>
                    </div>

                    <div class="md:col-span-5 grid grid-cols-2 gap-3 text-center">
                        <div class="p-3.5 rounded-2xl bg-purple-950/50 border border-purple-800/40">
                            <div class="text-[11px] font-semibold text-purple-300 uppercase">Target Awal</div>
                            <div class="text-xl font-black text-white mt-0.5">24 Hari</div>
                        </div>
                        <div class="p-3.5 rounded-2xl border transition duration-300"
                             :class="simDelay > 0 ? 'bg-rose-950/60 border-rose-500/50 text-rose-300' : 'bg-emerald-950/60 border-emerald-500/50 text-emerald-300'">
                            <div class="text-[11px] font-semibold uppercase" x-text="simDelay > 0 ? 'Hari-H Molor' : 'Status Acara'">Status</div>
                            <div class="text-xl font-black mt-0.5" x-text="cpmDuration() + ' Hari'">24 Hari</div>
                        </div>
                    </div>
                </div>

                <!-- Live Path Visualization Comparison -->
                <div class="pt-8 space-y-5">
                    <div class="text-xs font-bold uppercase tracking-wider text-purple-400 flex items-center justify-between">
                        <span>Alur Rantai Aktivitas Proyek:</span>
                        <span x-show="simDelay > 0" class="text-rose-400 font-bold" x-cloak>
                            ⚠️ Peringatan: Acara Terancam Mundur <span x-text="simDelay"></span> Hari!
                        </span>
                        <span x-show="simDelay == 0" class="text-emerald-400 font-bold">
                            ✓ Jadwal Jalur Kritis Sempurna
                        </span>
                    </div>

                    <!-- Flow Diagram -->
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
                        <!-- Step 1 -->
                        <div class="p-4 rounded-2xl bg-purple-950/40 border border-purple-500/30 relative">
                            <div class="text-[10px] font-bold text-purple-400 uppercase">Aktivitas 1</div>
                            <div class="font-bold text-white mt-1">Perizinan &amp; Venue</div>
                            <div class="text-[11px] text-purple-200/80 mt-1">Durasi: 14 Hari</div>
                            <div class="mt-2 text-[10px] px-2 py-0.5 rounded bg-rose-500/20 text-rose-300 inline-block font-bold">KRITIS (Float 0)</div>
                        </div>

                        <!-- Step 2 (Affected by Delay) -->
                        <div class="p-4 rounded-2xl transition duration-300 border relative"
                             :class="simDelay > 0 ? 'bg-rose-950/40 border-rose-500/60' : 'bg-purple-950/40 border-purple-500/30'">
                            <div class="text-[10px] font-bold uppercase" :class="simDelay > 0 ? 'text-rose-300' : 'text-purple-400'">Aktivitas 2</div>
                            <div class="font-bold text-white mt-1">Sound &amp; Panggung</div>
                            <div class="text-[11px] mt-1" :class="simDelay > 0 ? 'text-rose-200 font-bold' : 'text-purple-200/80'"
                                 x-text="'Durasi: ' + (10 + parseInt(simDelay)) + ' Hari'">Durasi: 10 Hari</div>
                            <div class="mt-2 text-[10px] px-2 py-0.5 rounded inline-block font-bold"
                                 :class="simDelay > 0 ? 'bg-rose-500 text-white' : 'bg-rose-500/20 text-rose-300'">
                                <span x-text="simDelay > 0 ? 'TERLAMBAT +' + simDelay : 'KRITIS (Float 0)'"></span>
                            </div>
                        </div>

                        <!-- Step 3 (Non-Critical Comparison) -->
                        <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-700/50 relative">
                            <div class="text-[10px] font-bold text-slate-400 uppercase">Aktivitas Paralel</div>
                            <div class="font-bold text-slate-200 mt-1">Cetak Merchandise</div>
                            <div class="text-[11px] text-slate-400 mt-1">Durasi: 5 Hari</div>
                            <div class="mt-2 text-[10px] px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 inline-block font-bold">NON-KRITIS (Float +4)</div>
                        </div>

                        <!-- Step 4 (Final D-Day) -->
                        <div class="p-4 rounded-2xl border transition duration-300 relative"
                             :class="simDelay > 0 ? 'bg-rose-950/50 border-rose-500/60' : 'bg-gradient-to-br from-purple-900/60 to-indigo-900/60 border-purple-400/40'">
                            <div class="text-[10px] font-bold uppercase" :class="simDelay > 0 ? 'text-rose-300' : 'text-purple-300'">Hari-H Acara</div>
                            <div class="font-black text-white mt-1">Pembukaan Acara</div>
                            <div class="text-[11px] mt-1" :class="simDelay > 0 ? 'text-rose-300 font-bold' : 'text-purple-200'"
                                 x-text="'Mulai di Hari ke-' + cpmDuration()">Mulai Hari ke-24</div>
                            <div class="mt-2 text-[10px] px-2 py-0.5 rounded inline-block font-bold"
                                 :class="simDelay > 0 ? 'bg-rose-500/30 text-rose-200' : 'bg-purple-500/30 text-purple-200'">
                                <span x-text="simDelay > 0 ? 'Terseret Delay' : 'Siap Dieksekusi'"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Comprehensive Features Grid Section -->
    <section id="fitur" class="py-24 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-950/60 border border-purple-500/30 text-purple-300 text-xs font-bold uppercase tracking-wider mb-3">
                    Fitur Inti EventPlan
                </div>
                <h2 class="text-3xl sm:text-5xl font-black text-white tracking-tight leading-tight">
                    Teknologi Penjadwalan Tingkat Industri untuk Setiap Panitia.
                </h2>
                <p class="mt-4 text-base sm:text-lg text-slate-300 leading-relaxed">
                    EventPlan menggabungkan teori manajemen operasi ilmiah dengan kemudahan visual aplikasi modern.
                </p>
            </div>

            <!-- Features Grid 6 Items -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="group relative rounded-3xl p-8 bg-slate-900/60 border border-purple-900/40 hover:border-purple-500/60 shadow-lg hover:shadow-purple-950/80 transition duration-300 glow-card">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-600 to-indigo-600 flex items-center justify-center text-white mb-6 group-hover:scale-110 shadow-lg shadow-purple-950/50 transition duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2.5">Critical Path Method (CPM)</h3>
                    <p class="text-sm text-slate-300 leading-relaxed">
                        Mesin Forward &amp; Backward Pass otomatis menghitung ES, EF, LS, LF, serta Total Float untuk mendeteksi bottleneck sebelum menjadi masalah.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="group relative rounded-3xl p-8 bg-slate-900/60 border border-purple-900/40 hover:border-purple-500/60 shadow-lg hover:shadow-purple-950/80 transition duration-300 glow-card">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-600 to-fuchsia-600 flex items-center justify-center text-white mb-6 group-hover:scale-110 shadow-lg shadow-purple-950/50 transition duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2.5">Kalender Kerja &amp; Libur Kustom</h3>
                    <p class="text-sm text-slate-300 leading-relaxed">
                        Atur hari kerja per minggu, jam kerja panitia harian, dan tanggal cuti/libur nasional. Estimasi target tanggal akhir tidak akan meleset.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="group relative rounded-3xl p-8 bg-slate-900/60 border border-purple-900/40 hover:border-purple-500/60 shadow-lg hover:shadow-purple-950/80 transition duration-300 glow-card">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-fuchsia-600 to-rose-600 flex items-center justify-center text-white mb-6 group-hover:scale-110 shadow-lg shadow-purple-950/50 transition duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2.5">Gantt &amp; PERT Interaktif</h3>
                    <p class="text-sm text-slate-300 leading-relaxed">
                        Diagram linimasa batang dengan warna kontras jalur kritis dan graf simpul Activity-on-Node SVG yang dilengkapi fungsi zoom dan pan interaktif.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="group relative rounded-3xl p-8 bg-slate-900/60 border border-purple-900/40 hover:border-purple-500/60 shadow-lg hover:shadow-purple-950/80 transition duration-300 glow-card">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center text-white mb-6 group-hover:scale-110 shadow-lg shadow-purple-950/50 transition duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2.5">Resource Workload Heatmap</h3>
                    <p class="text-sm text-slate-300 leading-relaxed">
                        Hindari beban kerja berlebih (over-allocation &gt; 100%) dan bentrok cuti personel dengan matriks beban kerja harian terintegrasi.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="group relative rounded-3xl p-8 bg-slate-900/60 border border-purple-900/40 hover:border-purple-500/60 shadow-lg hover:shadow-purple-950/80 transition duration-300 glow-card">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-violet-600 to-purple-800 flex items-center justify-center text-white mb-6 group-hover:scale-110 shadow-lg shadow-purple-950/50 transition duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2.5">Anti-Deadlock Cycle (DAG)</h3>
                    <p class="text-sm text-slate-300 leading-relaxed">
                        Algoritma deteksi siklus terarah (Directed Acyclic Graph) mencegah kesalahan logika dependensi tugas yang saling mengunci.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="group relative rounded-3xl p-8 bg-slate-900/60 border border-purple-900/40 hover:border-purple-500/60 shadow-lg hover:shadow-purple-950/80 transition duration-300 glow-card">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-700 to-indigo-800 flex items-center justify-center text-white mb-6 group-hover:scale-110 shadow-lg shadow-purple-950/50 transition duration-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2.5">Ekspor Resmi PDF &amp; CSV</h3>
                    <p class="text-sm text-slate-300 leading-relaxed">
                        Cetak lembar kendali profesional untuk sponsor, pimpinan instansi, atau rapat panitia dengan proteksi keamanan injeksi formula.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Visualizations Showcase Section with Dynamic Tabs -->
    <section id="visualisasi" class="py-24 bg-slate-900/50 border-t border-purple-900/30 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-950/60 border border-purple-500/30 text-purple-300 text-xs font-bold uppercase tracking-wider mb-3">
                    Visualisasi 360 Derajat
                </div>
                <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight">
                    Pilih Cara Anda Memantau Acara
                </h2>
                <p class="mt-3 text-base text-slate-300">
                    Beralih antara diagram PERT, Gantt Chart, dan Matriks Tim dalam satu klik.
                </p>

                <!-- Visualization Tabs -->
                <div class="mt-8 inline-flex p-1.5 rounded-2xl bg-slate-950/90 border border-purple-500/30">
                    <button @click="activeTab = 'gantt'"
                            :class="activeTab === 'gantt' ? 'bg-purple-600 text-white shadow-lg shadow-purple-900/50' : 'text-slate-400 hover:text-white'"
                            class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition duration-200">
                        Gantt Chart Linimasa
                    </button>
                    <button @click="activeTab = 'pert'"
                            :class="activeTab === 'pert' ? 'bg-purple-600 text-white shadow-lg shadow-purple-900/50' : 'text-slate-400 hover:text-white'"
                            class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition duration-200">
                        Diagram PERT (Network)
                    </button>
                    <button @click="activeTab = 'resources'"
                            :class="activeTab === 'resources' ? 'bg-purple-600 text-white shadow-lg shadow-purple-900/50' : 'text-slate-400 hover:text-white'"
                            class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition duration-200">
                        Beban Kerja Tim
                    </button>
                </div>
            </div>

            <!-- Tab Content Container -->
            <div class="max-w-5xl mx-auto rounded-3xl bg-slate-950/90 border border-purple-500/40 p-6 sm:p-10 shadow-2xl shadow-purple-950/70">
                
                <!-- TAB 1: PERT DIAGRAM -->
                <div x-show="activeTab === 'pert'" x-cloak class="space-y-6">
                    <div class="flex items-center justify-between border-b border-purple-900/40 pb-4">
                        <div>
                            <h3 class="text-lg font-bold text-white">Activity-on-Node PERT Diagram</h3>
                            <p class="text-xs text-purple-300/80">Jalur merah menunjukkan tugas kritis dengan Total Float = 0</p>
                        </div>
                        <span class="text-xs px-3 py-1 rounded-full bg-purple-950 text-purple-300 border border-purple-500/30">Auto Computed</span>
                    </div>

                    <div class="relative w-full h-80 bg-slate-900/60 rounded-2xl border border-purple-950 p-4 flex items-center justify-center overflow-hidden">
                        <svg class="w-full h-full" viewBox="0 0 600 240" fill="none">
                            <!-- Background connecting paths -->
                            <path d="M 80 120 L 220 60" stroke="#f43f5e" stroke-width="3" class="animate-cpm-dash" />
                            <path d="M 80 120 L 220 180" stroke="#64748b" stroke-width="2" stroke-dasharray="4 4" />
                            <path d="M 220 60 L 380 60" stroke="#f43f5e" stroke-width="3" class="animate-cpm-dash" />
                            <path d="M 220 180 L 380 180" stroke="#64748b" stroke-width="2" stroke-dasharray="4 4" />
                            <path d="M 380 60 L 520 120" stroke="#f43f5e" stroke-width="3" class="animate-cpm-dash" />
                            <path d="M 380 180 L 520 120" stroke="#64748b" stroke-width="2" stroke-dasharray="4 4" />

                            <!-- Node 1 (Start) -->
                            <g transform="translate(30, 90)">
                                <rect width="70" height="60" rx="14" fill="#581c87" stroke="#c084fc" stroke-width="2"/>
                                <text x="35" y="28" fill="#ffffff" font-size="12" font-weight="bold" text-anchor="middle">START</text>
                                <text x="35" y="44" fill="#d8b4fe" font-size="10" text-anchor="middle">Hari 0</text>
                            </g>

                            <!-- Node 2 (Critical: Booking Artis & Riders) -->
                            <g transform="translate(180, 30)">
                                <rect width="110" height="60" rx="14" fill="#6b21a8" stroke="#f43f5e" stroke-width="2.5"/>
                                <text x="55" y="26" fill="#ffffff" font-size="11" font-weight="bold" text-anchor="middle">Booking Artis</text>
                                <text x="55" y="44" fill="#fca5a5" font-size="10" font-weight="bold" text-anchor="middle">Float: 0 (Kritis)</text>
                            </g>

                            <!-- Node 3 (Non-Critical: Desain Poster) -->
                            <g transform="translate(180, 150)">
                                <rect width="110" height="60" rx="14" fill="#1e293b" stroke="#64748b" stroke-width="1.5"/>
                                <text x="55" y="26" fill="#e2e8f0" font-size="11" font-weight="bold" text-anchor="middle">Desain Poster</text>
                                <text x="55" y="44" fill="#94a3b8" font-size="10" text-anchor="middle">Float: +5 Hari</text>
                            </g>

                            <!-- Node 4 (Critical: Panggung Utama) -->
                            <g transform="translate(350, 30)">
                                <rect width="110" height="60" rx="14" fill="#6b21a8" stroke="#f43f5e" stroke-width="2.5"/>
                                <text x="55" y="26" fill="#ffffff" font-size="11" font-weight="bold" text-anchor="middle">Stage Rigging</text>
                                <text x="55" y="44" fill="#fca5a5" font-size="10" font-weight="bold" text-anchor="middle">Float: 0 (Kritis)</text>
                            </g>

                            <!-- Node 5 (Non-Critical: Jual Tiket Early) -->
                            <g transform="translate(350, 150)">
                                <rect width="110" height="60" rx="14" fill="#1e293b" stroke="#64748b" stroke-width="1.5"/>
                                <text x="55" y="26" fill="#e2e8f0" font-size="11" font-weight="bold" text-anchor="middle">Tiket Presale</text>
                                <text x="55" y="44" fill="#94a3b8" font-size="10" text-anchor="middle">Float: +3 Hari</text>
                            </g>

                            <!-- Node 6 (Finish: Hari H) -->
                            <g transform="translate(500, 90)">
                                <rect width="70" height="60" rx="14" fill="#581c87" stroke="#c084fc" stroke-width="2"/>
                                <text x="35" y="28" fill="#ffffff" font-size="12" font-weight="bold" text-anchor="middle">D-DAY</text>
                                <text x="35" y="44" fill="#d8b4fe" font-size="10" text-anchor="middle">Selesai</text>
                            </g>
                        </svg>
                    </div>

                    <div class="flex flex-wrap items-center justify-between text-xs text-slate-300 gap-4 pt-2">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                            <span>Jalur Kritis (CPM Utama - Jangan Sampai Terlambat)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-slate-500"></span>
                            <span>Jalur Berslack (Memiliki Toleransi Keterlambatan)</span>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: GANTT CHART -->
                <div x-show="activeTab === 'gantt'" x-cloak class="space-y-6">
                    <div class="flex items-center justify-between border-b border-purple-900/40 pb-4">
                        <div>
                            <h3 class="text-lg font-bold text-white">Linimasa Jadwal Interaktif Gantt Chart</h3>
                            <p class="text-xs text-purple-300/80">Visualisasi kalender kerja riil, garis ketergantungan (predecessors), dan sorotan merah jalur kritis.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] px-2.5 py-1 rounded-md bg-purple-950 border border-purple-500/30 text-purple-300 font-mono font-bold">14 Hari Proyek</span>
                            <span class="text-[11px] px-2.5 py-1 rounded-md bg-rose-950/70 border border-rose-500/40 text-rose-300 font-bold">Jalur Kritis Highlight</span>
                        </div>
                    </div>

                    <!-- Gantt Interactive Grid Container -->
                    <div class="rounded-2xl bg-slate-900/90 border border-purple-900/50 p-4 overflow-x-auto select-none shadow-inner">
                        <div class="min-w-[720px]">
                            <!-- Header Kalender Gantt (Bulan & Tanggal) -->
                            <div class="flex border-b border-purple-800/40 pb-3 text-xs">
                                <div class="w-[260px] shrink-0 pr-4 font-bold uppercase tracking-wider text-purple-300 flex items-center justify-between">
                                    <span>Tugas &amp; Aktivitas Acara</span>
                                    <span class="text-[10px] text-purple-400 font-normal">Progress</span>
                                </div>
                                <div class="grid grid-cols-14 flex-1 gap-1 text-center font-mono">
                                    <div class="p-1 rounded bg-slate-950/60 border border-purple-950 text-purple-300 text-[10px]"><span class="block text-[8px] text-slate-500">SEN</span>01</div>
                                    <div class="p-1 rounded bg-slate-950/60 border border-purple-950 text-purple-300 text-[10px]"><span class="block text-[8px] text-slate-500">SEL</span>02</div>
                                    <div class="p-1 rounded bg-slate-950/60 border border-purple-950 text-purple-300 text-[10px]"><span class="block text-[8px] text-slate-500">RAB</span>03</div>
                                    <div class="p-1 rounded bg-slate-950/60 border border-purple-950 text-purple-300 text-[10px]"><span class="block text-[8px] text-slate-500">KAM</span>04</div>
                                    <div class="p-1 rounded bg-slate-950/60 border border-purple-950 text-purple-300 text-[10px]"><span class="block text-[8px] text-slate-500">JUM</span>05</div>
                                    <div class="p-1 rounded bg-purple-950/40 border border-purple-900/50 text-slate-400 text-[10px]"><span class="block text-[8px] text-rose-400">SAB</span>06</div>
                                    <div class="p-1 rounded bg-purple-950/40 border border-purple-900/50 text-slate-400 text-[10px]"><span class="block text-[8px] text-rose-400">MIN</span>07</div>
                                    <div class="p-1 rounded bg-slate-950/60 border border-purple-950 text-purple-300 text-[10px]"><span class="block text-[8px] text-slate-500">SEN</span>08</div>
                                    <div class="p-1 rounded bg-slate-950/60 border border-purple-950 text-purple-300 text-[10px]"><span class="block text-[8px] text-slate-500">SEL</span>09</div>
                                    <div class="p-1 rounded bg-slate-950/60 border border-purple-950 text-purple-300 text-[10px]"><span class="block text-[8px] text-slate-500">RAB</span>10</div>
                                    <div class="p-1 rounded bg-slate-950/60 border border-purple-950 text-purple-300 text-[10px]"><span class="block text-[8px] text-slate-500">KAM</span>11</div>
                                    <div class="p-1 rounded bg-slate-950/60 border border-purple-950 text-purple-300 text-[10px]"><span class="block text-[8px] text-slate-500">JUM</span>12</div>
                                    <div class="p-1 rounded bg-purple-950/40 border border-purple-900/50 text-slate-400 text-[10px]"><span class="block text-[8px] text-rose-400">SAB</span>13</div>
                                    <div class="p-1 rounded bg-purple-950/40 border border-purple-900/50 text-slate-400 text-[10px]"><span class="block text-[8px] text-rose-400">MIN</span>14</div>
                                </div>
                            </div>

                            <!-- Gantt Task Rows & Timeline Bars -->
                            <div class="space-y-3.5 pt-3">
                                
                                <!-- Task 1: Kritis (Hari 1 - 4) -->
                                <div class="flex items-center text-xs group hover:bg-purple-950/30 p-1.5 rounded-xl transition">
                                    <div class="w-[260px] shrink-0 pr-4 flex items-center justify-between">
                                        <div class="flex items-center space-x-2 truncate">
                                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shrink-0 ring-2 ring-rose-400/40" title="Jalur Kritis"></span>
                                            <span class="font-semibold text-white truncate">1. Sewa Venue &amp; Perizinan</span>
                                        </div>
                                        <span class="text-xs font-bold text-emerald-400">100%</span>
                                    </div>
                                    <div class="grid grid-cols-14 flex-1 gap-1 relative h-8 items-center bg-slate-950/40 rounded-xl p-0.5 border border-purple-950">
                                        <!-- Bar Span 4 cols (Hari 1-4) -->
                                        <div class="col-start-1 col-span-4 h-6 rounded-lg bg-gradient-to-r from-rose-600 to-rose-500 text-white font-semibold flex items-center justify-between px-2.5 shadow-md shadow-rose-950/80 ring-1 ring-rose-400/60 relative overflow-hidden text-[10px]">
                                            <div class="absolute inset-y-0 left-0 bg-black/20 w-full rounded-l-lg"></div>
                                            <span class="relative z-10 truncate">Venue &amp; Izin</span>
                                            <span class="relative z-10 text-[9px] font-black uppercase text-rose-200">KRITIS</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Task 2: Non-Kritis / Parallel (Hari 2 - 6, ada Float) -->
                                <div class="flex items-center text-xs group hover:bg-purple-950/30 p-1.5 rounded-xl transition">
                                    <div class="w-[260px] shrink-0 pr-4 flex items-center justify-between">
                                        <div class="flex items-center space-x-2 truncate">
                                            <span class="w-2.5 h-2.5 rounded-full bg-purple-400 shrink-0"></span>
                                            <span class="font-semibold text-slate-200 truncate">2. Desain Materi &amp; Medsos</span>
                                        </div>
                                        <span class="text-xs font-bold text-purple-300">75%</span>
                                    </div>
                                    <div class="grid grid-cols-14 flex-1 gap-1 relative h-8 items-center bg-slate-950/40 rounded-xl p-0.5 border border-purple-950">
                                        <!-- Bar Span 4 cols (Hari 2-5) -->
                                        <div class="col-start-2 col-span-4 h-6 rounded-lg bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold flex items-center justify-between px-2.5 shadow-md shadow-purple-950/80 ring-1 ring-purple-400/50 relative overflow-hidden text-[10px]">
                                            <div class="absolute inset-y-0 left-0 bg-black/25 w-3/4 rounded-l-lg"></div>
                                            <span class="relative z-10 truncate">Desain Poster</span>
                                            <span class="relative z-10 text-[9px] font-semibold text-purple-200">Float +3h</span>
                                        </div>
                                        <!-- Slack / Float space indicator -->
                                        <div class="col-start-6 col-span-2 h-2 rounded bg-purple-900/30 border border-dashed border-purple-500/40" title="Kelonggaran Waktu (Slack Float)"></div>
                                    </div>
                                </div>

                                <!-- Task 3: Kritis (Hari 5 - 9, Menunggu Task 1) -->
                                <div class="flex items-center text-xs group hover:bg-purple-950/30 p-1.5 rounded-xl transition">
                                    <div class="w-[260px] shrink-0 pr-4 flex items-center justify-between">
                                        <div class="flex items-center space-x-2 truncate">
                                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shrink-0 ring-2 ring-rose-400/40" title="Jalur Kritis"></span>
                                            <span class="font-semibold text-white truncate">3. Sound System &amp; Panggung</span>
                                        </div>
                                        <span class="text-xs font-bold text-rose-300">60%</span>
                                    </div>
                                    <div class="grid grid-cols-14 flex-1 gap-1 relative h-8 items-center bg-slate-950/40 rounded-xl p-0.5 border border-purple-950">
                                        <!-- Bar Span 5 cols (Hari 5-9) -->
                                        <div class="col-start-5 col-span-5 h-6 rounded-lg bg-gradient-to-r from-rose-600 to-rose-500 text-white font-semibold flex items-center justify-between px-2.5 shadow-md shadow-rose-950/80 ring-1 ring-rose-400/60 relative overflow-hidden text-[10px]">
                                            <div class="absolute inset-y-0 left-0 bg-black/25 w-3/5 rounded-l-lg"></div>
                                            <span class="relative z-10 truncate">Panggung Utama</span>
                                            <span class="relative z-10 text-[9px] font-black uppercase text-rose-200">KRITIS</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Task 4: Kritis (Hari 10 - 13, Gladi Resik) -->
                                <div class="flex items-center text-xs group hover:bg-purple-950/30 p-1.5 rounded-xl transition">
                                    <div class="w-[260px] shrink-0 pr-4 flex items-center justify-between">
                                        <div class="flex items-center space-x-2 truncate">
                                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shrink-0 ring-2 ring-rose-400/40" title="Jalur Kritis"></span>
                                            <span class="font-semibold text-white truncate">4. Gladi Resik &amp; Soundcheck</span>
                                        </div>
                                        <span class="text-xs font-bold text-slate-400">20%</span>
                                    </div>
                                    <div class="grid grid-cols-14 flex-1 gap-1 relative h-8 items-center bg-slate-950/40 rounded-xl p-0.5 border border-purple-950">
                                        <!-- Bar Span 4 cols (Hari 10-13) -->
                                        <div class="col-start-10 col-span-4 h-6 rounded-lg bg-gradient-to-r from-rose-600 to-rose-500 text-white font-semibold flex items-center justify-between px-2.5 shadow-md shadow-rose-950/80 ring-1 ring-rose-400/60 relative overflow-hidden text-[10px]">
                                            <div class="absolute inset-y-0 left-0 bg-black/25 w-1/5 rounded-l-lg"></div>
                                            <span class="relative z-10 truncate">Gladi Resik</span>
                                            <span class="relative z-10 text-[9px] font-black uppercase text-rose-200">KRITIS</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Task 5: Milestone / Hari H (Hari 14) -->
                                <div class="flex items-center text-xs group hover:bg-purple-950/30 p-1.5 rounded-xl transition">
                                    <div class="w-[260px] shrink-0 pr-4 flex items-center justify-between">
                                        <div class="flex items-center space-x-2 truncate">
                                            <span class="w-2.5 h-2.5 rounded-full bg-fuchsia-400 shrink-0"></span>
                                            <span class="font-bold text-fuchsia-300 truncate">5. Pembukaan Acara (Hari-H)</span>
                                        </div>
                                        <span class="text-xs font-bold text-fuchsia-300">Siap</span>
                                    </div>
                                    <div class="grid grid-cols-14 flex-1 gap-1 relative h-8 items-center bg-slate-950/40 rounded-xl p-0.5 border border-purple-950">
                                        <!-- Milestone Diamond Marker (Hari 14) -->
                                        <div class="col-start-14 col-span-1 h-6 rounded-lg bg-gradient-to-r from-fuchsia-600 to-purple-600 text-white font-extrabold flex items-center justify-center shadow-lg shadow-fuchsia-950/80 ring-2 ring-fuchsia-400 text-[10px] animate-pulse">
                                            D-DAY
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Keterangan Legend Gantt -->
                    <div class="flex flex-wrap items-center justify-between text-xs text-slate-300 gap-4 pt-2">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-rose-500 ring-2 ring-rose-400/40"></span>
                            <span>Bar Kritis (Total Float = 0, Jangan Sampai Mundur)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-purple-600"></span>
                            <span>Bar Normal (Memiliki Cadangan Waktu Slack)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-md bg-purple-900/40 border border-dashed border-purple-500/50"></span>
                            <span>Ruang Toleransi Slack (Float)</span>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: RESOURCE WORKLOAD -->
                <div x-show="activeTab === 'resources'" x-cloak class="space-y-6">
                    <div class="flex items-center justify-between border-b border-purple-900/40 pb-4">
                        <div>
                            <h3 class="text-lg font-bold text-white">Pencegahan Over-allocation Tim</h3>
                            <p class="text-xs text-purple-300/80">Pantau kapasitas beban kerja panitia agar tidak burnout</p>
                        </div>
                        <span class="text-xs px-3 py-1 rounded-full bg-purple-950 text-purple-300 border border-purple-500/30">Workload Engine</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                        <div class="p-4 rounded-2xl bg-purple-950/40 border border-purple-500/30">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-white">Divisi Acara</span>
                                <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 font-bold">85% Kapasitas</span>
                            </div>
                            <p class="text-[11px] text-slate-300 mt-2">Beban kerja optimal, siap eksekusi rundown acara.</p>
                        </div>

                        <div class="p-4 rounded-2xl bg-rose-950/40 border border-rose-500/40">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-white">Divisi Perlengkapan</span>
                                <span class="px-2 py-0.5 rounded bg-rose-500/30 text-rose-300 font-bold">120% OVERLOAD</span>
                            </div>
                            <p class="text-[11px] text-rose-200 mt-2">Peringatan: Tambahkan staf pendukung untuk load-in panggung.</p>
                        </div>

                        <div class="p-4 rounded-2xl bg-purple-950/40 border border-purple-500/30">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-white">Divisi Konsumsi &amp; Medis</span>
                                <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 font-bold">60% Kapasitas</span>
                            </div>
                            <p class="text-[11px] text-slate-300 mt-2">Kapasitas aman dengan jadwal cuti telah terakomodasi.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Event Types / Solutions Showcase -->
    <section id="kegunaan" class="py-24 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-950/60 border border-purple-500/30 text-purple-300 text-xs font-bold uppercase tracking-wider mb-3">
                    Berbagai Jenis Acara
                </div>
                <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight">
                    Dirancang untuk Segala Skala Produksi Acara
                </h2>
                <p class="mt-3 text-base text-slate-300">
                    Dari gig kampus lokal hingga festival internasional ribuan pengunjung.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Card 1 -->
                <div class="p-6 rounded-3xl bg-slate-900/70 border border-purple-900/40 hover:border-purple-500/50 transition duration-300 flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-purple-950 border border-purple-500/40 flex items-center justify-center text-purple-300 mb-4 font-bold text-xl">
                            🎸
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Konser &amp; Festival Musik</h3>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Kelola riders artis, perizinan kepolisian, loading genset, soundcheck hingga crowd control tanpa resiko delay.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-purple-900/40 text-[11px] text-purple-300 font-semibold">
                        Critical: Vendor Audio &amp; Izin
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="p-6 rounded-3xl bg-slate-900/70 border border-purple-900/40 hover:border-purple-500/50 transition duration-300 flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-purple-950 border border-purple-500/40 flex items-center justify-center text-purple-300 mb-4 font-bold text-xl">
                            🎓
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Konferensi &amp; Seminar</h3>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Sinkronisasi pembicara keynote, setup live-streaming multi-kamera, registrasi peserta, dan cetak sertifikat.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-purple-900/40 text-[11px] text-purple-300 font-semibold">
                        Critical: Keynote Speakers &amp; Venue
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="p-6 rounded-3xl bg-slate-900/70 border border-purple-900/40 hover:border-purple-500/50 transition duration-300 flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-purple-950 border border-purple-500/40 flex items-center justify-center text-purple-300 mb-4 font-bold text-xl">
                            🎪
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Pameran &amp; Expo UMKM</h3>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Penataan booth peserta, instalasi partisi listrik, registrasi barcode tiket, dan seremoni pembukaan resmi.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-purple-900/40 text-[11px] text-purple-300 font-semibold">
                        Critical: Konstruksi Booth &amp; Kelistrikan
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="p-6 rounded-3xl bg-slate-900/70 border border-purple-900/40 hover:border-purple-500/50 transition duration-300 flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-purple-950 border border-purple-500/40 flex items-center justify-center text-purple-300 mb-4 font-bold text-xl">
                            🏆
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Turnamen &amp; Acara Olahraga</h3>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Skema jadwal babak kualifikasi, penyediaan tim medis darurat, medali juara, dan fasilitas atlet.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-purple-900/40 text-[11px] text-purple-300 font-semibold">
                        Critical: Arena Standar &amp; Wasit
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Accordion Section -->
    <section id="faq" class="py-24 bg-slate-900/40 border-t border-purple-900/30">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-950/60 border border-purple-500/30 text-purple-300 text-xs font-bold uppercase tracking-wider mb-3">
                    Tanya Jawab
                </div>
                <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight">
                    Pertanyaan yang Sering Diajukan (FAQ)
                </h2>
                <p class="mt-3 text-base text-slate-300">
                    Semua yang perlu Anda ketahui mengenai EventPlan dan Critical Path Method.
                </p>
            </div>

            <!-- Accordion Items -->
            <div class="space-y-4">
                <!-- FAQ 1 -->
                <div class="rounded-2xl bg-slate-950/80 border border-purple-900/40 overflow-hidden">
                    <button @click="activeFaq = (activeFaq === 1 ? null : 1)"
                            class="w-full px-6 py-5 text-left flex items-center justify-between text-base font-bold text-white hover:text-purple-300 transition">
                        <span>Apa itu Critical Path Method (CPM) dan mengapa krusial untuk event?</span>
                        <svg class="w-5 h-5 transform transition duration-200 text-purple-400" :class="activeFaq === 1 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="activeFaq === 1" x-cloak class="px-6 pb-5 text-sm text-slate-300 leading-relaxed border-t border-purple-950/60 pt-3">
                        CPM adalah algoritma penjadwalan matematis yang mengidentifikasi urutan tugas berantai terpanjang penentu tanggal akhir proyek. Tugas dalam jalur ini memiliki Total Float bernilai 0, artinya jika tugas tersebut terlambat 1 hari saja, seluruh tanggal Hari-H acara otomatis mundur 1 hari.
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="rounded-2xl bg-slate-950/80 border border-purple-900/40 overflow-hidden">
                    <button @click="activeFaq = (activeFaq === 2 ? null : 2)"
                            class="w-full px-6 py-5 text-left flex items-center justify-between text-base font-bold text-white hover:text-purple-300 transition">
                        <span>Bagaimana EventPlan memperhitungkan hari libur dan akhir pekan?</span>
                        <svg class="w-5 h-5 transform transition duration-200 text-purple-400" :class="activeFaq === 2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="activeFaq === 2" x-cloak class="px-6 pb-5 text-sm text-slate-300 leading-relaxed border-t border-purple-950/60 pt-3">
                        Setiap proyek memiliki Work Calendar khusus. Anda bebas menentukan hari kerja efektif (misal: Senin - Sabtu atau Senin - Jumat) serta memasukkan daftar hari libur nasional atau cuti panitia. Mesin CPM secara otomatis melompati hari non-kerja saat menghitung tanggal penyelesaian tugas.
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="rounded-2xl bg-slate-950/80 border border-purple-900/40 overflow-hidden">
                    <button @click="activeFaq = (activeFaq === 3 ? null : 3)"
                            class="w-full px-6 py-5 text-left flex items-center justify-between text-base font-bold text-white hover:text-purple-300 transition">
                        <span>Apakah saya bisa mengundang anggota panitia lain ke proyek?</span>
                        <svg class="w-5 h-5 transform transition duration-200 text-purple-400" :class="activeFaq === 3 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="activeFaq === 3" x-cloak class="px-6 pb-5 text-sm text-slate-300 leading-relaxed border-t border-purple-950/60 pt-3">
                        Ya. Pemilik (Project Leader) dapat mengirim undangan kolaborasi melalui email ke anggota terdaftar. Anggota dapat melihat linimasa Gantt, diagram PERT, dan memperbarui progres tugas yang didelegasikan kepada mereka secara aman.
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="rounded-2xl bg-slate-950/80 border border-purple-900/40 overflow-hidden">
                    <button @click="activeFaq = (activeFaq === 4 ? null : 4)"
                            class="w-full px-6 py-5 text-left flex items-center justify-between text-base font-bold text-white hover:text-purple-300 transition">
                        <span>Format ekspor apa saja yang didukung oleh EventPlan?</span>
                        <svg class="w-5 h-5 transform transition duration-200 text-purple-400" :class="activeFaq === 4 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="activeFaq === 4" x-cloak class="px-6 pb-5 text-sm text-slate-300 leading-relaxed border-t border-purple-950/60 pt-3">
                        Anda dapat mengunduh dokumen laporan resmi dalam format PDF siap cetak lengkap dengan ringkasan status CPM, atau mengekspor berkas spreadsheet CSV yang telah dibentengi dengan mitigasi injeksi formula spreadsheet.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Banner -->
    <section class="py-24 relative overflow-hidden">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative rounded-3xl p-8 sm:p-14 bg-gradient-to-r from-purple-950/90 via-slate-900/95 to-purple-900/90 border border-purple-400/40 shadow-2xl shadow-purple-950/90 text-center overflow-hidden glow-card">
                <!-- Background Glow Flare -->
                <div class="absolute -top-24 -right-24 w-80 h-80 bg-fuchsia-500/25 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-purple-500/25 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 text-xs font-bold mb-4 border border-purple-500/30">
                        ⚡ Akses Instan Tanpa Batas
                    </span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight">
                        Siap Mewujudkan Acara Impian Tanpa Overdue?
                    </h2>
                    <p class="mt-4 text-base sm:text-lg text-purple-200/90 max-w-2xl mx-auto leading-relaxed">
                        Bergabunglah dengan panitia, event organizer, dan manajer proyek yang telah merasakan kemudahan koordinasi berbasis CPM akurat.
                    </p>
                    <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-9 py-4 rounded-xl text-base font-extrabold text-white bg-gradient-to-r from-purple-600 to-fuchsia-600 hover:from-purple-500 hover:to-fuchsia-500 shadow-xl shadow-purple-900/60 transition duration-300 transform hover:-translate-y-1">
                            Daftar Akun Sekarang &rarr;
                        </a>
                        <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-4 rounded-xl text-base font-semibold text-slate-200 hover:text-white bg-slate-900/80 border border-purple-500/30 hover:bg-slate-800 transition duration-200">
                            Masuk ke Akun
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-purple-900/30 bg-slate-950/95 py-14 text-slate-400 text-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center space-x-3">
                        <img src="{{ asset('images/logo.png') }}" alt="EventPlan Logo" class="w-8 h-8 rounded-xl object-cover ring-1 ring-white/20">
                        <span class="font-extrabold text-white text-lg tracking-tight">EventPlan</span>
                    </div>
                    <p class="text-xs text-slate-400 max-w-sm leading-relaxed">
                        Sistem Perencanaan &amp; Manajemen Waktu Acara Berbasis Critical Path Method (CPM) dan visualisasi linimasa interaktif.
                    </p>
                </div>

                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-purple-300 mb-3">Navigasi</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="#fitur" class="hover:text-purple-300 transition">Fitur Utama</a></li>
                        <li><a href="#simulator" class="hover:text-purple-300 transition">Simulasi CPM</a></li>
                        <li><a href="#visualisasi" class="hover:text-purple-300 transition">Gantt &amp; PERT</a></li>
                        <li><a href="#faq" class="hover:text-purple-300 transition">Tanya Jawab (FAQ)</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-purple-300 mb-3">Akses Akun</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('login') }}" class="hover:text-purple-300 transition">Masuk ke Portal</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-purple-300 transition">Registrasi Akun Baru</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-purple-300 transition">Login dengan Google</a></li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 border-t border-purple-900/40 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 gap-4">
                <div>
                    &copy; {{ date('Y') }} EventPlan. Semua hak cipta dilindungi undang-undang.
                </div>
                <div class="flex items-center space-x-6 text-slate-400">
                    <span>Berbasis Laravel 13 &amp; Tailwind CSS v4</span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
