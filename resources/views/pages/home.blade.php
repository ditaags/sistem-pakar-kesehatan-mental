@extends('layouts.app')

@section('content')
<div class="space-y-20 sm:space-y-28 py-6 sm:py-12">
    <!-- 2b. Hero Section Dua Kolom -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 reveal-fade-up">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
            <!-- Kolom Kiri: Teks & Aksi -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <!-- Badge Pil Kecil -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-[#E3EDE6] text-[#1F2A27] border border-[#DCE5E0]/70">
                    <span class="w-2 h-2 rounded-full bg-[#1A3831]"></span>
                    Sistem Pakar Certainty Factor
                </div>

                <!-- Judul Serif Besar (Clamp 44px sampai 72px) dengan Satu Kata Kunci Aksen -->
                <h1 class="font-serif text-4xl sm:text-5xl lg:text-[56px] xl:text-[62px] text-[#1F2A27] font-semibold leading-[1.08] tracking-tight">
                    Kenali Kecenderungan Kondisi Mental Anda Secara <span class="text-teal-800 italic">Rasional</span>
                </h1>

                <!-- Paragraf Penjelas Tenang -->
                <p class="text-base sm:text-lg text-[#52605B] leading-relaxed max-w-xl mx-auto lg:mx-0">
                    Instrumen skrining terstruktur untuk membantu mendeteksi indikasi awal gangguan suasana hati dan kecemasan menggunakan kaidah penalaran klinis yang transparan dan dapat dipertanggungjawabkan.
                </p>

                <!-- Dua Tombol Pil -->
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3.5 pt-2">
                    <a href="{{ route('konsultasi.index') }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-full text-sm font-semibold text-white bg-[#1A3831] hover:bg-[#142621] transition-all duration-200 hover:-translate-y-0.5 shadow-sm">
                        Mulai Konsultasi Mandiri
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                    <a href="{{ route('about') }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3.5 rounded-full text-sm font-semibold text-[#1F2A27] bg-white border border-[#DCE5E0] hover:bg-stone-50 transition-all duration-200 hover:-translate-y-0.5 shadow-2xs">
                        Pelajari Metode &amp; Evaluasi
                    </a>
                </div>

                <!-- Tiga Statistik dengan Angka Serif Besar & Label Kecil -->
                <div class="grid grid-cols-3 gap-4 sm:gap-6 pt-8 border-t border-[#DCE5E0] text-left">
                    <div>
                        <div class="font-serif text-3xl sm:text-4xl font-bold text-[#1F2A27]">
                            {{ $symptoms->count() > 0 ? $symptoms->count() : 25 }}
                        </div>
                        <div class="text-xs text-[#52605B] mt-1 font-medium leading-snug">Gejala Klinis Terstandar</div>
                    </div>
                    <div>
                        <div class="font-serif text-3xl sm:text-4xl font-bold text-[#1F2A27]">
                            {{ $conditions->count() > 0 ? $conditions->count() : 4 }}
                        </div>
                        <div class="text-xs text-[#52605B] mt-1 font-medium leading-snug">Kondisi Hipotesis</div>
                    </div>
                    <div>
                        <div class="font-serif text-3xl sm:text-4xl font-bold text-teal-800">
                            CF
                        </div>
                        <div class="text-xs text-[#52605B] mt-1 font-medium leading-snug">Shortliffe &amp; Buchanan</div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Ilustrasi SVG Orisinal Tenang dengan Blob Sage & Kartu Melayang -->
            <div class="lg:col-span-5 flex justify-center">
                <div class="relative w-full max-w-[420px] sm:max-w-[460px] aspect-[4/3.8] flex items-center justify-center">
                    <!-- Bentuk Organik Besar Berwarna Sage (Blob Latar Belakang) -->
                    <div class="absolute inset-0 bg-[#E3EDE6] rounded-[48%_52%_45%_55%/50%_48%_52%_50%] transition-transform duration-700 animate-float-slow opacity-95"></div>

                    <!-- ILUSTRASI SVG ORISINAL BUATAN SENDIRI: Sosok Tenang di Bawah Matahari Terbit & Daun Botani -->
                    <svg viewBox="0 0 460 400" fill="none" xmlns="http://www.w3.org/2000/svg" class="relative z-10 w-full h-full p-4" aria-hidden="true">
                        <defs>
                            <!-- Gradasi Matahari Terbit Persik Hangat -->
                            <linearGradient id="warmSun" x1="0%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stop-color="#F9DDD0" />
                                <stop offset="100%" stop-color="#F3CDBB" />
                            </linearGradient>
                            <!-- Gradasi Lengkung Bukit Sage Lembut -->
                            <linearGradient id="sageHillBack" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#D9E6DC" />
                                <stop offset="100%" stop-color="#C8DACD" />
                            </linearGradient>
                            <linearGradient id="sageHillFront" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#C2D6C7" />
                                <stop offset="100%" stop-color="#AECAB5" />
                            </linearGradient>
                        </defs>

                        <!-- Lingkaran Matahari Terbit Bulat (Persik Hangat) -->
                        <circle cx="230" cy="180" r="76" fill="url(#warmSun)" />
                        <!-- Cincin Aura Cahaya Tenang -->
                        <circle cx="230" cy="180" r="102" stroke="#FBF1EC" stroke-width="2" stroke-dasharray="6 6" opacity="0.8" />

                        <!-- Lengkung Bukit Latar Belakang -->
                        <path d="M40 330C100 240 180 250 250 280C320 310 390 280 430 330L430 360L40 360Z" fill="url(#sageHillBack)" />

                        <!-- Lengkung Bukit Depan yang Tenang -->
                        <path d="M30 350C120 280 200 300 280 290C360 280 410 320 440 350L440 370L30 370Z" fill="url(#sageHillFront)" />

                        <!-- Batang & Daun Botani Elegan di Sisi Kiri (Hijau Tua Klinis & Abu Kehijauan) -->
                        <g opacity="0.9">
                            <path d="M70 340C75 270 95 210 130 160" stroke="#1A3831" stroke-width="2.5" stroke-linecap="round" />
                            <!-- Daun-daun Melengkung Halus -->
                            <path d="M85 290C70 280 65 260 75 255C85 260 90 275 85 290Z" fill="#1A3831" />
                            <path d="M102 250C118 245 125 230 118 222C108 225 100 238 102 250Z" fill="#1F423A" />
                            <path d="M115 210C100 202 95 185 106 180C116 185 120 198 115 210Z" fill="#1A3831" />
                            <path d="M130 165C138 150 152 148 152 158C145 168 135 170 130 165Z" fill="#1F423A" />
                        </g>

                        <!-- Batang & Daun Botani di Sisi Kanan -->
                        <g opacity="0.85">
                            <path d="M390 350C380 280 355 220 320 170" stroke="#52605B" stroke-width="2" stroke-linecap="round" />
                            <path d="M375 295C390 285 392 270 382 265C372 270 370 285 375 295Z" fill="#52605B" />
                            <path d="M355 250C340 242 336 228 344 220C352 222 358 236 355 250Z" fill="#1A3831" />
                            <path d="M338 210C352 202 355 188 345 182C336 186 333 198 338 210Z" fill="#52605B" />
                        </g>

                        <!-- Sosok Manusia Sederhana Duduk Santai Menikmati Ketenangan (Gaya Flat Tenang) -->
                        <g transform="translate(185, 205)">
                            <!-- Kepala Siluet Halus -->
                            <circle cx="45" cy="22" r="14" fill="#FAF7F2" stroke="#1A3831" stroke-width="2" />
                            <!-- Rambut Rapi -->
                            <path d="M33 20C33 12 39 8 47 8C55 8 59 13 58 20C52 16 42 16 33 20Z" fill="#1A3831" />

                            <!-- Tubuh / Baju Santai Hijau Tua -->
                            <path d="M35 38C30 45 28 65 30 85L62 85C64 68 60 45 55 38C52 35 38 35 35 38Z" fill="#1A3831" />
                            <!-- Kerah / Detail Halus -->
                            <path d="M42 36L45 46L48 36" stroke="#FAF7F2" stroke-width="1.5" stroke-linecap="round" />

                            <!-- Posisi Lengan Santai di Pangkuan -->
                            <path d="M32 50C25 58 22 72 32 78C38 82 48 80 55 78C62 76 68 62 60 50" stroke="#FAF7F2" stroke-width="2" stroke-linecap="round" fill="none" opacity="0.6" />

                            <!-- Kaki Bersila Santai -->
                            <path d="M18 85C22 78 30 75 45 76C60 75 68 78 72 85C74 89 65 94 45 94C25 94 16 89 18 85Z" fill="#142621" />

                            <!-- Daun Kecil yang Mengambang Ringan di Udara (Simbol Kejernihan Pikiran) -->
                            <path d="M80 32C86 26 94 28 92 34C86 38 80 36 80 32Z" fill="#F3CDBB" />
                            <path d="M10 45C16 40 22 43 20 48C15 51 10 49 10 45Z" fill="#C2D6C7" />
                        </g>
                    </svg>

                    <!-- Kartu Melayang 1 (Kiri Atas): "25 Gejala Dikaji" -->
                    <div class="absolute -top-3 -left-2 sm:-left-6 bg-white/95 backdrop-blur-md border border-[#DCE5E0] rounded-2xl px-3.5 py-2.5 shadow-[0_10px_30px_rgba(31,42,39,0.08)] flex items-center gap-2.5 animate-float-slow z-20">
                        <div class="w-7 h-7 rounded-full bg-[#E3EDE6] text-[#1A3831] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-[#1F2A27]">25 Gejala Dikaji</div>
                            <div class="text-[10px] text-[#52605B]">Kriteria Standar DSM-5</div>
                        </div>
                    </div>

                    <!-- Kartu Melayang 2 (Kanan Bawah): "4 Kondisi Dikenali" -->
                    <div class="absolute -bottom-2 -right-2 sm:-right-6 bg-white/95 backdrop-blur-md border border-[#DCE5E0] rounded-2xl px-3.5 py-2.5 shadow-[0_10px_30px_rgba(31,42,39,0.08)] flex items-center gap-2.5 animate-float-reverse z-20">
                        <div class="w-7 h-7 rounded-full bg-[#FBF1EC] text-[#8C4A2F] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-[#1F2A27]">4 Kondisi Dikenali</div>
                            <div class="text-[10px] text-[#52605B]">Perhitungan Deterministik</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. BANNER DISCLAIMER: Strip Lembut Berlatar Persik Muda dengan Ikon Info Garis -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 reveal-fade-up">
        <div class="bg-[#FBF1EC] border border-[#F3CDBB]/70 rounded-2xl p-4 sm:p-5 flex items-center gap-3.5 text-xs sm:text-sm text-[#4A3B32] shadow-[0_2px_12px_rgba(243,205,187,0.15)]">
            <!-- Ikon Info Garis (Bukan Emoji) -->
            <div class="w-8 h-8 rounded-full bg-[#F3CDBB]/60 text-[#8C4A2F] flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="16" x2="12" y2="12" />
                    <line x1="12" y1="8" x2="12.01" y2="8" />
                </svg>
            </div>
            <div class="leading-relaxed">
                <strong>Instrumen Skrining Edukatif:</strong> Sistem ini menggunakan metode Certainty Factor untuk penelusuran indikasi awal suasana hati dan <strong>bukan pengganti diagnosis klinis</strong> psikolog atau psikiater.
            </div>
        </div>
    </section>

    <!-- 4. ALUR KONSULTASI (4 LANGKAH): Kartu Sejajar, Angka Outline Serif, Garis Putus Penghubung -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 reveal-fade-up">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="inline-block text-xs font-semibold uppercase tracking-wider text-teal-900 bg-[#E3EDE6] px-3 py-1 rounded-full mb-2.5">
                Alur Pemeriksaan
            </span>
            <h2 class="font-serif text-2xl sm:text-3xl lg:text-4xl font-semibold tracking-tight text-[#1F2A27]">
                Empat Langkah Sederhana Menuju Hasil Skrining
            </h2>
        </div>

        <div class="relative">
            <!-- Garis Putus-Putus Tipis Penghubung di Desktop -->
            <div class="hidden md:block absolute top-12 left-12 right-12 h-[1px] border-t border-dashed border-[#DCE5E0] z-0 pointer-events-none"></div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 relative z-10">
                <!-- Langkah 01 -->
                <div class="bg-white p-6 sm:p-7 rounded-[22px] border border-[#DCE5E0] shadow-[0_4px_20px_rgba(31,42,39,0.03)] hover:shadow-[0_12px_32px_rgba(31,42,39,0.06)] hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                    <div>
                        <div class="font-serif text-3xl font-semibold text-outline-serif leading-none">01</div>
                        <h3 class="text-base font-semibold text-[#1F2A27] mt-4">Isi Data Diri</h3>
                        <p class="text-xs sm:text-sm text-[#52605B] leading-relaxed mt-2">
                            Masukkan inisial atau nama panggilan, rentang usia, dan jenis kelamin. Sistem tidak merekam identitas privat.
                        </p>
                    </div>
                </div>

                <!-- Langkah 02 -->
                <div class="bg-white p-6 sm:p-7 rounded-[22px] border border-[#DCE5E0] shadow-[0_4px_20px_rgba(31,42,39,0.03)] hover:shadow-[0_12px_32px_rgba(31,42,39,0.06)] hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                    <div>
                        <div class="font-serif text-3xl font-semibold text-outline-serif leading-none">02</div>
                        <h3 class="text-base font-semibold text-[#1F2A27] mt-4">Pilih Gejala</h3>
                        <p class="text-xs sm:text-sm text-[#52605B] leading-relaxed mt-2">
                            Tentukan keluhan yang dirasakan beserta skala keyakinan Anda (mulai dari Tidak hingga Sangat Yakin).
                        </p>
                    </div>
                </div>

                <!-- Langkah 03 -->
                <div class="bg-white p-6 sm:p-7 rounded-[22px] border border-[#DCE5E0] shadow-[0_4px_20px_rgba(31,42,39,0.03)] hover:shadow-[0_12px_32px_rgba(31,42,39,0.06)] hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                    <div>
                        <div class="font-serif text-3xl font-semibold text-outline-serif leading-none">03</div>
                        <h3 class="text-base font-semibold text-[#1F2A27] mt-4">Kalkulasi CF</h3>
                        <p class="text-xs sm:text-sm text-[#52605B] leading-relaxed mt-2">
                            Mesin inferensi mengalikan keyakinan pengguna dengan bobot pakar serta mengakumulasi bukti secara deterministik.
                        </p>
                    </div>
                </div>

                <!-- Langkah 04 -->
                <div class="bg-white p-6 sm:p-7 rounded-[22px] border border-[#DCE5E0] shadow-[0_4px_20px_rgba(31,42,39,0.03)] hover:shadow-[0_12px_32px_rgba(31,42,39,0.06)] hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                    <div>
                        <div class="font-serif text-3xl font-semibold text-outline-serif leading-none">04</div>
                        <h3 class="text-base font-semibold text-[#1F2A27] mt-4">Hasil &amp; Saran</h3>
                        <p class="text-xs sm:text-sm text-[#52605B] leading-relaxed mt-2">
                            Dapatkan persentase kecenderungan, transparansi langkah hitung matematis, saran penanganan, dan opsi unduh PDF.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. KATEGORI KONDISI: Kartu Besar Bersudut 24px, Tint Lembut Berbeda, Tanpa Gambar Stok -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 reveal-fade-up">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
            <div>
                <span class="inline-block text-xs font-semibold uppercase tracking-wider text-teal-900 bg-[#E3EDE6] px-3 py-1 rounded-full mb-2">
                    Kondisi yang Diuji
                </span>
                <h2 class="font-serif text-2xl sm:text-3xl lg:text-4xl font-semibold tracking-tight text-[#1F2A27]">
                    Empat Kategori Gangguan Kesehatan Mental
                </h2>
            </div>
            <div class="text-xs sm:text-sm text-[#52605B] max-w-md">
                Kondisi diidentifikasi berdasarkan kriteria klinis DSM-5 dan data distribusi pengobatan pada dataset riset pendukung.
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @php
                // Palet tint lembut satu keluarga warna hangat & tenang
                $tints = [
                    'K01' => ['bg' => 'bg-[#EBF2ED]', 'border' => 'border-[#D6E3D9]', 'badge' => 'bg-[#E3EDE6] text-[#1A3831]'],
                    'K02' => ['bg' => 'bg-[#FBF1EC]', 'border' => 'border-[#F3CDBB]/70', 'badge' => 'bg-[#F3CDBB]/60 text-[#8C4A2F]'],
                    'K03' => ['bg' => 'bg-[#E6F0EE]', 'border' => 'border-[#CFE1DE]', 'badge' => 'bg-[#CFE1DE] text-[#1F423A]'],
                    'K04' => ['bg' => 'bg-[#F4EFEA]', 'border' => 'border-[#E5DDD4]', 'badge' => 'bg-[#E5DDD4] text-[#4A3B32]'],
                ];
            @endphp

            @forelse($conditions as $c)
                @php
                    $t = $tints[$c->code] ?? ['bg' => 'bg-white', 'border' => 'border-[#DCE5E0]', 'badge' => 'bg-[#E3EDE6] text-[#1F2A27]'];
                @endphp
                <div class="{{ $t['bg'] }} {{ $t['border'] }} p-7 rounded-[24px] border hover:shadow-[0_16px_40px_rgba(31,42,39,0.08)] hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-mono font-bold {{ $t['badge'] }} border border-black/5">
                                {{ $c->code }}
                            </span>
                            <span class="text-[11px] text-[#52605B] font-medium">DSM-5 Kriteria</span>
                        </div>

                        <div>
                            <h3 class="font-serif text-xl sm:text-2xl font-semibold text-[#1F2A27]">
                                {{ $c->name }}
                            </h3>
                            <div class="text-xs text-[#52605B] italic mt-0.5">
                                {{ $c->original_name }}
                            </div>
                        </div>

                        <p class="text-xs sm:text-sm text-[#52605B] leading-relaxed pt-1">
                            {{ Str::limit($c->description, 175) }}
                        </p>
                    </div>

                    @if(!empty($c->general_advice) && is_array($c->general_advice))
                    <div class="mt-5 pt-4 border-t border-black/5">
                        <div class="text-[11px] font-semibold text-[#1F2A27] uppercase tracking-wider mb-2">Fokus Penanganan:</div>
                        <ul class="text-xs text-[#52605B] space-y-1 list-disc list-inside">
                            @foreach(array_slice($c->general_advice, 0, 2) as $adv)
                            <li>{{ $adv }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            @empty
                <div class="col-span-2 text-center py-12 text-[#52605B] text-sm bg-white rounded-2xl border border-[#DCE5E0]">
                    Data kondisi belum dimuat. Jalankan perintah <code>php artisan pakar:bangun-data</code>.
                </div>
            @endforelse
        </div>
    </section>

    <!-- 6. PANEL STATISTIK: Satu Panel Lebar Hijau Tua Bersudut 24px, Angka Serif Besar Warna Krem -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 reveal-fade-up">
        <div class="bg-[#1A3831] text-[#FAF7F2] rounded-[24px] p-8 sm:p-12 shadow-[0_16px_48px_rgba(26,56,49,0.18)] border border-[#142621]">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-0">
                <!-- Stat 1 -->
                <div class="md:border-r md:border-[#FAF7F2]/15 md:pr-8">
                    <div class="font-serif text-4xl sm:text-5xl font-bold text-[#FAF7F2] tracking-tight">500</div>
                    <div class="text-xs sm:text-sm text-[#FAF7F2]/75 mt-2 font-medium">Responden Kasus Riset</div>
                </div>

                <!-- Stat 2 -->
                <div class="md:border-r md:border-[#FAF7F2]/15 md:px-8">
                    <div class="font-serif text-4xl sm:text-5xl font-bold text-[#FAF7F2] tracking-tight">25</div>
                    <div class="text-xs sm:text-sm text-[#FAF7F2]/75 mt-2 font-medium">Gejala Terpetakan DSM-5</div>
                </div>

                <!-- Stat 3 -->
                <div class="md:border-r md:border-[#FAF7F2]/15 md:px-8">
                    <div class="font-serif text-4xl sm:text-5xl font-bold text-[#FAF7F2] tracking-tight">4</div>
                    <div class="text-xs sm:text-sm text-[#FAF7F2]/75 mt-2 font-medium">Kategori Gangguan Klinis</div>
                </div>

                <!-- Stat 4 -->
                <div class="md:pl-8">
                    <div class="font-serif text-4xl sm:text-5xl font-bold text-[#F3CDBB] tracking-tight">100%</div>
                    <div class="text-xs sm:text-sm text-[#FAF7F2]/75 mt-2 font-medium">Transparansi Deterministik</div>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. CTA AKHIR: Panel Sage Bersudut 28px dengan Bentuk Organik Dekoratif & Judul Serif -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 reveal-fade-up">
        <div class="bg-[#E3EDE6] rounded-[28px] p-8 sm:p-14 relative overflow-hidden border border-[#D3E2D8] shadow-[0_12px_40px_rgba(31,42,39,0.05)]">
            <!-- Bentuk Organik Dekoratif di Sisi Kanan -->
            <div class="absolute -right-16 -bottom-16 w-80 h-80 rounded-full bg-[#D4E3D8]/60 blur-2xl pointer-events-none"></div>
            <svg class="absolute right-4 bottom-0 w-64 h-64 text-[#CBDDD0] pointer-events-none opacity-40" viewBox="0 0 200 200" fill="currentColor" aria-hidden="true">
                <path d="M45,-60C58,-52,68,-40,73,-26C78,-11,78,6,73,21C68,36,58,49,46,59C34,69,20,76,4,74C-12,71,-25,60,-37,49C-49,38,-61,27,-67,13C-73,-1,-73,-18,-66,-32C-59,-46,-45,-57,-31,-65C-17,-72,-2,-76,13,-73C27,-71,32,-68,45,-60Z" transform="translate(100 100)" />
            </svg>

            <div class="relative z-10 max-w-xl text-center sm:text-left space-y-4">
                <h2 class="font-serif text-2xl sm:text-3xl lg:text-4xl font-semibold tracking-tight text-[#1F2A27]">
                    Siap Memeriksa Kondisi Emosional Anda?
                </h2>
                <p class="text-xs sm:text-sm text-[#52605B] leading-relaxed max-w-lg">
                    Skrining berlangsung cepat dalam 3–5 menit, tidak memungut biaya, dan data yang Anda masukkan terlindungi tanpa pencatatan identitas privat.
                </p>
                <div class="pt-2">
                    <a href="{{ route('konsultasi.index') }}"
                       class="inline-flex items-center gap-2 px-7 py-3.5 rounded-full text-sm font-semibold text-white bg-[#1A3831] hover:bg-[#142621] transition-all duration-200 hover:-translate-y-0.5 shadow-sm">
                        Mulai Skrining Sekarang
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
