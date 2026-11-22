<!DOCTYPE html>
<html lang="id" class="h-full bg-[#1A3831] text-[#1F2A27] antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portal Pakar Admin — MindScreen.Expert</title>

    <!-- Font: Fraunces (Serif Hangat) & Plus Jakarta Sans (Sans Bersih) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400..700;1,9..144,400..700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#1A3831] text-white flex flex-col font-sans selection:bg-[#E3EDE6] selection:text-[#1A3831] relative overflow-x-hidden">
    <!-- 1. Latar Belakang Hijau Tua: Bentuk Organik Besar & Gelembung Transparan Lambat (Float 8-12 Detik) -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0" aria-hidden="true">
        <!-- Bentuk Organik Latar Sedikit Lebih Terang -->
        <div class="absolute -top-36 -left-36 w-[560px] h-[560px] rounded-full bg-[#1F423A]/70 blur-3xl animate-float-slow"></div>
        <div class="absolute -bottom-36 -right-36 w-[620px] h-[620px] rounded-full bg-[#244E43]/50 blur-3xl animate-float-reverse"></div>
        <div class="absolute top-[40%] right-[10%] w-[380px] h-[380px] rounded-full bg-[#1F423A]/40 blur-3xl animate-float-slow"></div>

        <!-- Lingkaran Gelembung Lembut Transparan Berbagai Ukuran -->
        <div class="absolute top-[14%] left-[6%] sm:left-[12%] w-24 h-24 rounded-full bg-white/5 border border-white/10 backdrop-blur-xs animate-float-slow"></div>
        <div class="absolute bottom-[18%] right-[6%] sm:right-[12%] w-32 h-32 rounded-full bg-white/[0.04] border border-white/10 backdrop-blur-xs animate-float-reverse"></div>
        <div class="absolute top-[72%] left-[16%] w-14 h-14 rounded-full bg-[#F3CDBB]/5 border border-white/10 backdrop-blur-xs animate-float-slow"></div>
        <div class="absolute top-[18%] right-[22%] w-18 h-18 rounded-full bg-[#E3EDE6]/5 border border-white/10 backdrop-blur-xs animate-float-reverse"></div>
        <div class="absolute bottom-[38%] left-[28%] w-12 h-12 rounded-full bg-white/5 border border-white/10 backdrop-blur-xs animate-float-slow"></div>
    </div>

    <!-- Tautan Kembali ke Beranda: Kontras AA Tinggi dengan Latar Gelap -->
    <header class="relative z-20 w-full max-w-[1040px] mx-auto px-4 pt-4 sm:pt-6">
        <a href="{{ route('home') }}"
           class="inline-flex items-center gap-2 text-xs sm:text-sm font-medium text-[#FAF7F2] hover:text-white transition-all duration-150 py-1.5 px-3.5 rounded-full bg-white/10 hover:bg-white/15 border border-white/20 backdrop-blur-md shadow-2xs group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#E3EDE6]">
            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Beranda</span>
        </a>
    </header>

    <!-- Pembungkus Utama (Tengah Layar Vertikal & Horizontal, min-height 100vh) -->
    <main class="min-h-[calc(100vh-80px)] flex-1 flex items-center justify-center p-3 sm:p-6 lg:p-8 relative z-10">
        <!-- 2. KARTU UTAMA DUA PANEL (Maksimal 1040px, Desktop ~560px, Radius 32px, Bayangan Lembut) -->
        <div class="w-full max-w-[1040px] lg:min-h-[560px] bg-[#1A3831] rounded-[24px] lg:rounded-[32px] overflow-hidden shadow-[0_25px_60px_rgba(10,20,18,0.45)] border border-white/10 flex flex-col lg:flex-row relative animate-login-enter">

            <!-- ========================================================================= -->
            <!-- PANEL KIRI (55% di Desktop, Header 180px di HP): Latar Krem Putih (#FAF7F2) -->
            <!-- ========================================================================= -->
            <section class="w-full lg:w-[55%] h-[180px] lg:h-auto bg-[#FAF7F2] p-5 sm:p-8 lg:p-10 flex flex-col justify-between relative overflow-hidden shrink-0" aria-label="Informasi MindScreen.Expert">
                <!-- Kurva Organik Pembatas Panel Kiri ke Kanan di Desktop -->
                <div class="hidden lg:block absolute -right-0.5 top-0 bottom-0 w-14 overflow-hidden pointer-events-none z-10" aria-hidden="true">
                    <svg class="h-full w-full text-[#1A3831] fill-current" viewBox="0 0 100 600" preserveAspectRatio="none">
                        <path d="M 0 0 C 45 150, 85 240, 20 360 C -35 460, 55 530, 100 600 L 100 0 Z" />
                    </svg>
                </div>

                <!-- Bagian Atas: Logo Teks MindScreen.Expert -->
                <div class="relative z-10 flex items-center justify-between lg:justify-start">
                    <a href="{{ route('home') }}" class="font-serif text-xl sm:text-2xl font-bold text-[#1A3831] tracking-tight hover:opacity-90 transition-opacity">
                        MindScreen<span class="text-[#0d9488]">.Expert</span>
                    </a>
                    <span class="lg:hidden text-[11px] font-semibold text-[#52605B] px-2.5 py-0.5 rounded-full bg-[#E3EDE6]/80 border border-[#DCE5E0]">
                        Portal Pakar
                    </span>
                </div>

                <!-- Bagian Tengah: Ilustrasi SVG Orisinal dalam Bentuk Blob Sage Lembut -->
                <div class="my-auto py-1 lg:py-6 flex flex-col items-center justify-center relative z-10">
                    <div class="relative w-full max-w-[280px] sm:max-w-[340px] aspect-[16/10] sm:aspect-[4/3] rounded-[24px] lg:rounded-[36px] bg-[#E3EDE6]/60 border border-[#DCE5E0]/60 p-2 sm:p-5 flex items-center justify-center overflow-hidden shadow-inner">
                        <!-- SVG Orisinal: Sosok Membaca Tenang di Bawah Pohon Rindang (Hanya Token Desain Situs) -->
                        <svg class="w-full h-full max-h-[110px] sm:max-h-[180px] lg:max-h-[220px]" viewBox="0 0 320 240" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <!-- Lingkaran Bokeh Lembut Transparan di Latar Belakang -->
                            <circle cx="65" cy="55" r="30" fill="#F3CDBB" fill-opacity="0.45" />
                            <circle cx="255" cy="75" r="34" fill="#E3EDE6" fill-opacity="0.9" />
                            <circle cx="240" cy="180" r="24" fill="#F3CDBB" fill-opacity="0.3" />
                            <circle cx="120" cy="170" r="16" fill="#FAF7F2" fill-opacity="0.7" />

                            <!-- Matahari Bulat Hangat -->
                            <circle cx="215" cy="65" r="22" fill="#F3CDBB" />

                            <!-- Kontur Tanah Melengkung Tenang -->
                            <path d="M 10 215 Q 120 200 310 215" stroke="#1A3831" stroke-width="2.5" stroke-linecap="round" fill="none" />
                            <path d="M 10 215 Q 120 200 310 215 L 310 240 L 10 240 Z" fill="#E3EDE6" fill-opacity="0.5" />

                            <!-- Batang Pohon Rindang Berlekuk Lembut -->
                            <path d="M 75 215 C 75 165, 85 125, 115 95 C 122 88, 130 92, 122 108 C 105 138, 98 175, 102 215 Z" fill="#1A3831" />
                            <path d="M 105 135 C 125 125, 145 130, 160 125" stroke="#1A3831" stroke-width="3" stroke-linecap="round" fill="none" />

                            <!-- Tajuk Rindang Berlapis (Sage Lembut, Hijau Gelap, Peach Highlight) -->
                            <path d="M 70 100 C 45 80, 55 45, 95 50 C 115 25, 160 30, 170 60 C 195 55, 215 80, 200 105 C 205 130, 175 145, 150 135 C 120 150, 85 135, 70 100 Z" fill="#E3EDE6" />
                            <path d="M 85 92 C 68 76, 75 48, 108 52 C 125 32, 158 36, 166 62 C 185 58, 200 78, 188 98 C 192 118, 168 130, 148 122 C 124 134, 98 120, 85 92 Z" fill="#1A3831" fill-opacity="0.9" />
                            <circle cx="165" cy="55" r="14" fill="#F3CDBB" fill-opacity="0.8" />
                            <path d="M 110 65 C 120 48, 145 50, 150 68 C 162 65, 172 78, 165 90 C 150 100, 130 95, 120 85 C 112 85, 108 75, 110 65 Z" fill="#EBF2ED" />

                            <!-- Dedaunan Mengambang Perlahan Jatuh -->
                            <path d="M 175 130 Q 185 136 179 146 Q 169 140 175 130 Z" fill="#1A3831" />
                            <path d="M 200 155 Q 210 162 203 172 Q 194 165 200 155 Z" fill="#F3CDBB" />
                            <path d="M 155 175 Q 165 180 160 188 Q 150 184 155 175 Z" fill="#1A3831" />

                            <!-- Sosok Sederhana Duduk Membaca Buku di Bawah Pohon -->
                            <circle cx="155" cy="155" r="8" fill="#1A3831" />
                            <path d="M 153 163 C 148 175, 145 190, 142 208 C 152 210, 166 208, 166 208" stroke="#1A3831" stroke-width="7" stroke-linecap="round" fill="none" />
                            <path d="M 142 208 Q 158 216 182 214 Q 168 206 148 204" fill="#1A3831" />
                            <path d="M 151 176 L 168 190" stroke="#1A3831" stroke-width="3" stroke-linecap="round" fill="none" />
                            <polygon points="166,192 173,186 180,192 173,196" fill="#FAF7F2" stroke="#1A3831" stroke-width="1.5" />
                        </svg>
                    </div>
                </div>

                <!-- Bagian Bawah: Hak Cipta & Deskripsi (Desktop Saja) -->
                <div class="hidden lg:block pt-4 border-t border-[#DCE5E0]/70 text-left relative z-10">
                    <p class="text-xs font-semibold text-[#1F2A27]">&copy; 2026 MindScreen.Expert</p>
                    <p class="text-[11px] text-[#52605B] mt-0.5">Sistem pakar skrining kesehatan mental</p>
                </div>
            </section>

            <!-- ========================================================================= -->
            <!-- PANEL KANAN (45% di Desktop): Latar Hijau Tua (#1A3831), Form Terpusat -->
            <!-- ========================================================================= -->
            <section class="w-full lg:w-[45%] bg-[#1A3831] p-6 sm:p-8 lg:p-10 flex flex-col justify-center relative" aria-label="Form Autentikasi Admin">
                <div class="w-full max-w-[320px] mx-auto lg:mx-0 space-y-4.5">

                    <!-- Header Form: Badge, Judul Serif, Deskripsi -->
                    <div class="space-y-1.5 text-left">
                        <div>
                            <span class="inline-block px-3 py-1 rounded-full text-[11px] font-semibold bg-white/10 text-[#FAF7F2] border border-white/15 tracking-wider uppercase">
                                AUTENTIKASI SESI
                            </span>
                        </div>
                        <h1 class="font-serif text-2xl sm:text-3xl font-semibold text-white tracking-tight">
                            Portal Pakar Admin
                        </h1>
                        <p class="text-xs sm:text-sm text-[#E3EDE6]/80 leading-relaxed">
                            Kelola basis pengetahuan dan bobot CF pakar dengan aman.
                        </p>
                    </div>

                    <!-- 7. Pesan Error Strip di Atas Form (Email atau Sandi Salah, Sesi Berakhir, dsb) -->
                    @if (session('error'))
                    <div class="bg-red-500/15 border border-red-500/30 text-red-100 rounded-xl p-3 text-xs flex items-start gap-2.5 shadow-2xs" role="alert">
                        <svg class="w-4 h-4 text-red-300 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        <div class="leading-relaxed">
                            {{ session('error') }}
                        </div>
                    </div>
                    @endif

                    @if ($errors->any())
                    <div class="bg-red-500/15 border border-red-500/30 text-red-100 rounded-xl p-3 text-xs flex items-start gap-2.5 shadow-2xs" role="alert">
                        <svg class="w-4 h-4 text-red-300 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        <div class="leading-relaxed">
                            Email atau kata sandi tidak cocok. Silakan periksa kembali kredensial Anda.
                        </div>
                    </div>
                    @endif

                    @if (session('success'))
                    <div class="bg-white/10 border border-white/20 text-[#FAF7F2] rounded-xl p-3 text-xs flex items-center gap-2 shadow-2xs" role="status">
                        <svg class="w-4 h-4 text-emerald-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    @endif

                    <!-- 3. Form Login Admin -->
                    <form id="login-form" action="{{ route('admin.login.submit') }}" method="POST" class="space-y-4">
                        @csrf

                        <!-- Field Email Administrator (Tinggi 48px, Ikon Amplop Garis, Fokus Cincin Tunggal Terang) -->
                        <div class="space-y-1.5 text-left">
                            <label for="admin-email" class="block text-sm font-medium text-[#FAF7F2]">
                                Email Administrator
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#E3EDE6]/60">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                    </svg>
                                </span>
                                <input type="email"
                                       id="admin-email"
                                       name="email"
                                       value="{{ old('email', app()->environment('local') ? 'admin@mindscreen.expert' : '') }}"
                                       autocomplete="username"
                                       required
                                       autofocus
                                       placeholder="admin@mindscreen.expert"
                                       class="w-full h-12 pl-10 pr-3.5 text-sm bg-white/8 border border-white/15 rounded-xl text-white placeholder:text-[#E3EDE6]/45 focus:ring-2 focus:ring-[#E3EDE6] focus:border-[#E3EDE6] outline-none transition-all">
                            </div>
                        </div>

                        <!-- Field Kata Sandi (Tinggi 48px, Ikon Gembok Garis, Tombol Ikon Mata) -->
                        <div class="space-y-1.5 text-left">
                            <label for="admin-password" class="block text-sm font-medium text-[#FAF7F2]">
                                Kata Sandi
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#E3EDE6]/60">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                    </svg>
                                </span>
                                <input type="password"
                                       id="admin-password"
                                       name="password"
                                       value="{{ app()->environment('local') ? 'adminpakar2026' : '' }}"
                                       autocomplete="current-password"
                                       required
                                       placeholder="••••••••••••"
                                       class="w-full h-12 pl-10 pr-11 text-sm bg-white/8 border border-white/15 rounded-xl text-white placeholder:text-[#E3EDE6]/45 focus:ring-2 focus:ring-[#E3EDE6] focus:border-[#E3EDE6] outline-none transition-all">

                                <!-- Tombol Ikon Mata (Lihat / Sembunyikan Kata Sandi) -->
                                <button type="button"
                                        id="btn-toggle-password"
                                        aria-label="Tampilkan kata sandi"
                                        aria-pressed="false"
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#E3EDE6]/60 hover:text-white transition-colors cursor-pointer focus-visible:outline-none focus-visible:text-white">
                                    <svg id="icon-eye-show" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    <svg id="icon-eye-hide" class="w-4 h-4 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                        <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
                                        <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68" />
                                        <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61" />
                                        <line x1="2" x2="22" y1="2" y2="22" />
                                    </svg>
                                </button>
                            </div>
                        </div>



                        <!-- 4. Tombol "Masuk ke Portal": Pil Penuh Sage Terang, Teks Hijau Tua, Hover Naik 1px -->
                        <button type="submit"
                                id="btn-submit-login"
                                class="w-full h-12 px-6 text-sm font-semibold rounded-full bg-[#E3EDE6] text-[#1A3831] hover:bg-[#FAF7F2] transition-all duration-150 hover:-translate-y-0.5 hover:shadow-md active:scale-[0.98] shadow-sm flex items-center justify-center cursor-pointer focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white">
                            <!-- Label Teks Selalu Ada di Render HTML -->
                            <span id="btn-submit-label" class="inline-flex items-center justify-center gap-2">
                                <span>Masuk ke Portal</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </span>
                            <!-- Spinner dan Status Memuat Saat Formulir Dikirim -->
                            <span id="btn-submit-spinner" class="hidden items-center justify-center gap-2">
                                <svg class="animate-spin w-4 h-4 text-[#1A3831]" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span>Memeriksa...</span>
                            </span>
                        </button>
                    </form>

                    <!-- 6. Blok Akun Default: HANYA Dirender Jika app()->environment('local') bernilai True -->
                    @if (app()->environment('local'))
                    <div class="pt-3 border-t border-white/10 text-xs text-left">
                        <div class="bg-white/5 border border-white/10 rounded-xl p-3 text-[#E3EDE6]/80 space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-[11px] uppercase tracking-wider text-[#FAF7F2]">
                                    Akun Default (Seeder):
                                </span>
                                <button type="button"
                                        id="btn-autofill"
                                        data-email="admin@mindscreen.expert"
                                        data-password="adminpakar2026"
                                        class="text-[11px] font-semibold text-[#F3CDBB] hover:text-white underline cursor-pointer">
                                    Isi otomatis
                                </button>
                            </div>
                            <div class="font-mono text-[11px] text-[#FAF7F2]/80 break-all">
                                <code>admin@mindscreen.expert</code> &bull; Sandi: <code>adminpakar2026</code>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </section>
        </div>
    </main>

    <!-- Skrip Interaktif: Terisolasi dalam IIFE, Tanpa Tabrakan Variabel Global -->
    <script>
    (function () {
        'use strict';

        // 1. Tombol "Isi Otomatis" untuk Kredensial Default Lokal
        const emailInput = document.getElementById('admin-email');
        const passwordInput = document.getElementById('admin-password');
        const autoFillBtn = document.getElementById('btn-autofill');

        if (autoFillBtn && emailInput && passwordInput) {
            autoFillBtn.addEventListener('click', function (e) {
                e.preventDefault();
                // Mengambil nilai string murni dari data-attributes, menghindari tabrakan dengan window.email
                const defaultEmail = autoFillBtn.getAttribute('data-email') || 'admin@mindscreen.expert';
                const defaultPassword = autoFillBtn.getAttribute('data-password') || 'adminpakar2026';

                emailInput.value = defaultEmail;
                passwordInput.value = defaultPassword;
                emailInput.focus();
            });
        }

        // 2. Tombol Ikon Mata untuk Menampilkan / Menyembunyikan Kata Sandi
        const togglePasswordBtn = document.getElementById('btn-toggle-password');
        const eyeShowIcon = document.getElementById('icon-eye-show');
        const eyeHideIcon = document.getElementById('icon-eye-hide');

        if (togglePasswordBtn && passwordInput && eyeShowIcon && eyeHideIcon) {
            togglePasswordBtn.addEventListener('click', function (e) {
                e.preventDefault();
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                togglePasswordBtn.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
                togglePasswordBtn.setAttribute('aria-label', isPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
                eyeShowIcon.classList.toggle('hidden', isPassword);
                eyeHideIcon.classList.toggle('hidden', !isPassword);
            });
        }

        // 3. Status Memuat dan Pencegahan Double Submit Form
        const loginForm = document.getElementById('login-form');
        const submitBtn = document.getElementById('btn-submit-login');
        const submitLabel = document.getElementById('btn-submit-label');
        const submitSpinner = document.getElementById('btn-submit-spinner');

        let isSubmitting = false;
        if (loginForm && submitBtn && submitLabel && submitSpinner) {
            loginForm.addEventListener('submit', function (e) {
                if (isSubmitting) {
                    e.preventDefault();
                    return;
                }
                isSubmitting = true;
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-80', 'cursor-not-allowed');
                submitLabel.classList.add('hidden');
                submitSpinner.classList.remove('hidden');
                submitSpinner.classList.add('inline-flex');
            });
        }
    })();
    </script>
</body>
</html>
