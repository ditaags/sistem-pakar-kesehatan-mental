<!DOCTYPE html>
<html lang="id" class="h-full bg-[#FAF7F2] text-[#1F2A27] antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'MindScreen.Expert — Skrining Kesehatan Mental Berbasis Certainty Factor' }}</title>

    <!-- Font: Fraunces (Serif Hangat) & Plus Jakarta Sans (Sans Bersih) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400..700;1,9..144,400..700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col font-sans bg-[#FAF7F2] text-[#1F2A27] selection:bg-[#E3EDE6] selection:text-[#1A3831]">
    <!-- 2. NAVBAR: Bar Pil Melayang (16px dari atas, max-w-6xl, latar putih semi-solid, border tipis, bayangan lembut) -->
    <div class="sticky top-4 z-40 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 w-full no-print" x-data="{ mobileOpen: false }">
        <header class="bg-white/90 backdrop-blur-md border border-[#DCE5E0] rounded-full px-5 py-2.5 sm:px-6 sm:py-3 shadow-[0_12px_40px_rgba(31,42,39,0.06)] flex items-center justify-between transition-all">
            <!-- Brand Logo Kiri -->
            <a href="{{ route('home') }}" class="group flex items-baseline gap-2 shrink-0">
                <span class="font-bold text-base sm:text-lg tracking-tight text-[#1F2A27] group-hover:text-teal-900 transition-colors">
                    MindScreen<span class="text-teal-800">.Expert</span>
                </span>
                <span class="hidden sm:inline-block text-[11px] font-medium text-[#52605B] pl-2 border-l border-[#DCE5E0]">
                    Skrining CF
                </span>
            </a>

            <!-- Menu Tengah Desktop (Penanda Aktif Pil Sage) -->
            <nav class="hidden md:flex items-center gap-1.5 text-xs sm:text-sm font-medium">
                <a href="{{ route('home') }}"
                   class="px-4 py-1.5 rounded-full transition-colors {{ request()->routeIs('home') ? 'bg-[#E3EDE6] text-[#1F2A27] font-semibold' : 'text-[#52605B] hover:text-[#1F2A27] hover:bg-stone-100/70' }}">
                    Beranda
                </a>
                <a href="{{ route('about') }}"
                   class="px-4 py-1.5 rounded-full transition-colors {{ request()->routeIs('about') ? 'bg-[#E3EDE6] text-[#1F2A27] font-semibold' : 'text-[#52605B] hover:text-[#1F2A27] hover:bg-stone-100/70' }}">
                    Tentang Kami
                </a>
                <a href="{{ route('admin.index') }}"
                   class="px-4 py-1.5 rounded-full transition-colors {{ request()->is('kelola-pakar*') ? 'bg-[#E3EDE6] text-[#1F2A27] font-semibold' : 'text-[#52605B] hover:text-[#1F2A27] hover:bg-stone-100/70' }}">
                    Portal Pakar
                </a>
            </nav>

            <!-- Kanan: Tombol Mulai Konsultasi Pil Hijau & Hamburger HP -->
            <div class="flex items-center gap-2">
                <a href="{{ route('konsultasi.index') }}"
                   class="inline-flex items-center justify-center px-4 sm:px-5 py-2 text-xs sm:text-sm font-semibold rounded-full text-white bg-[#1A3831] hover:bg-[#142621] transition-all duration-200 hover:-translate-y-0.5 shadow-xs">
                    Mulai Konsultasi
                </a>

                <!-- Hamburger Button Mobile -->
                <button type="button"
                        @click="mobileOpen = !mobileOpen"
                        aria-label="Toggle navigation menu"
                        class="md:hidden p-2 rounded-full text-[#52605B] hover:text-[#1F2A27] hover:bg-stone-100 transition-colors focus:outline-none">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        <path x-show="mobileOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </header>

        <!-- Dropdown Menu Mobile yang Rapi -->
        <div x-show="mobileOpen"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             @click.outside="mobileOpen = false"
             class="md:hidden mt-2 bg-white/95 backdrop-blur-md border border-[#DCE5E0] rounded-2xl p-4 shadow-[0_12px_40px_rgba(31,42,39,0.08)] space-y-1">
            <a href="{{ route('home') }}"
               @click="mobileOpen = false"
               class="block px-4 py-2.5 rounded-xl text-sm transition-colors {{ request()->routeIs('home') ? 'bg-[#E3EDE6] text-[#1F2A27] font-semibold' : 'text-[#52605B] hover:bg-stone-100/70' }}">
                Beranda
            </a>
            <a href="{{ route('about') }}"
               @click="mobileOpen = false"
               class="block px-4 py-2.5 rounded-xl text-sm transition-colors {{ request()->routeIs('about') ? 'bg-[#E3EDE6] text-[#1F2A27] font-semibold' : 'text-[#52605B] hover:bg-stone-100/70' }}">
                Tentang Kami
            </a>
            <a href="{{ route('admin.index') }}"
               @click="mobileOpen = false"
               class="block px-4 py-2.5 rounded-xl text-sm transition-colors {{ request()->is('kelola-pakar*') ? 'bg-[#E3EDE6] text-[#1F2A27] font-semibold' : 'text-[#52605B] hover:bg-stone-100/70' }}">
                Portal Pakar
            </a>
        </div>
    </div>

    <!-- Main Content Slot -->
    <main class="flex-1 mt-4 sm:mt-6">
        @yield('content')
    </main>

    <!-- 8. FOOTER: Latar Hijau Tua, Teks Krem, Empat Kolom Rapi -->
    <footer class="bg-[#142621] text-[#FAF7F2] border-t border-[#1F3A33] py-14 mt-20 no-print">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 sm:gap-10 text-sm">
                <!-- Kolom 1: Merek -->
                <div class="space-y-3.5 md:col-span-1">
                    <div class="font-bold text-lg tracking-tight text-[#FAF7F2]">
                        MindScreen<span class="text-[#E3EDE6]">.Expert</span>
                    </div>
                    <p class="text-xs sm:text-sm text-[#FAF7F2]/70 leading-relaxed">
                        Aplikasi sistem pakar penelusuran indikasi awal kesehatan mental berbasis metode Certainty Factor (CF) Shortliffe & Buchanan yang deterministik, transparan, dan edukatif.
                    </p>
                </div>

                <!-- Kolom 2: Tautan Halaman -->
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-[#E3EDE6] mb-3">Tautan Halaman</h3>
                    <ul class="space-y-2 text-xs sm:text-sm text-[#FAF7F2]/80">
                        <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a></li>
                        <li><a href="{{ route('konsultasi.index') }}" class="hover:text-white transition-colors">Konsultasi Skrining</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-white transition-colors">Metode & Evaluasi</a></li>
                        <li><a href="{{ route('admin.login') }}" class="hover:text-white transition-colors">Masuk Portal Pakar</a></li>
                    </ul>
                </div>

                <!-- Kolom 3: Layanan Krisis (Terbaca Jelas & Terang) -->
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-[#F3CDBB] mb-3">Layanan Krisis</h3>
                    <p class="text-xs text-[#FAF7F2]/75 mb-2.5 leading-relaxed">
                        Jika Anda atau orang terdekat berada dalam krisis psikologis darurat:
                    </p>
                    <div class="text-xs text-[#FAF7F2]/90 font-medium">Hotline Kemenkes RI:</div>
                    <div class="text-lg font-bold text-[#F3CDBB] tracking-tight mt-0.5">119 (ekstensi 8)</div>
                    <div class="text-[11px] text-[#FAF7F2]/60 mt-1">Layanan SEJIWA 24 Jam Bebas Pulsa</div>
                </div>

                <!-- Kolom 4: Pembuat Sistem -->
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-[#E3EDE6] mb-3">Basis Riset</h3>
                    <p class="text-xs text-[#FAF7F2]/75 leading-relaxed">
                        Dikembangkan untuk riset & tugas kuliah informatika medis. Menguji 25 gejala terstandar DSM-5 terhadap 4 kategori gangguan mental dari 500 data rekam klinis.
                    </p>
                    <div class="text-[11px] text-[#FAF7F2]/55 mt-2">
                        Bukan pengganti diagnosis medis resmi.
                    </div>
                </div>
            </div>

            <!-- Baris Hak Cipta Bawah -->
            <div class="pt-6 border-t border-[#FAF7F2]/10 flex flex-col sm:flex-row items-center justify-between text-xs text-[#FAF7F2]/60 gap-3">
                <div>&copy; {{ date('Y') }} MindScreen.Expert. Seluruh hak cipta dilindungi.</div>
                <div>Standar Evaluasi Akademik &bull; Basis Pengetahuan Klinis DSM-5</div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
