@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8" x-data="{ showMathDetails: false }">
    <!-- Header Hasil & Aksi Cetak -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-stone-200 pb-6">
        <div>
            <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-800 uppercase tracking-wider mb-1">
                <span class="w-2 h-2 rounded-full bg-teal-600"></span>
                Laporan Hasil Skrining
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-stone-900 tracking-tight">Ringkasan Analisis Pakar</h1>
            <p class="text-xs text-stone-600 mt-1">
                Responden: <strong>{{ $consultation->patient_name }}</strong> ({{ $consultation->patient_age }} Tahun, {{ $consultation->patient_gender }}) &bull; Waktu: {{ \Carbon\Carbon::parse($consultation->created_at)->translatedFormat('d F Y, H:i') }} WIB
            </p>
        </div>

        <div class="flex items-center gap-2 no-print">
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-full border border-[#DCE5E0] text-[#1F2A27] bg-white hover:bg-stone-50 transition-all duration-200 hover:-translate-y-0.5 shadow-2xs">
                <svg class="w-4 h-4 text-[#52605B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Unduh PDF / Cetak
            </button>
            <a href="{{ route('konsultasi.index') }}" class="px-5 py-2 text-xs font-semibold rounded-full text-white bg-[#1A3831] hover:bg-[#142621] transition-all duration-200 hover:-translate-y-0.5 shadow-sm">
                Konsultasi Ulang
            </a>
        </div>
    </div>

    <!-- KOTAK BANTUAN DARURAT (SAFETY RULE) JIKA TERPICU -->
    @if ($consultation->has_safety_alert)
    <div class="bg-red-50 border-2 border-red-300 rounded-xl p-6 text-red-900 space-y-4 shadow-xs">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-full bg-red-200 text-red-800 flex items-center justify-center font-bold shrink-0">!</div>
            <div class="space-y-1">
                <h3 class="text-base font-bold text-red-950">PERHATIAN KESELAMATAN MENDESAK</h3>
                <p class="text-xs sm:text-sm leading-relaxed text-red-900">
                    Anda mengindikasikan adanya pikiran terkait kematian atau dorongan untuk menyakiti diri sendiri. Keselamatan Anda adalah prioritas utama. Indikasi ini diaktifkan secara mandiri sebagai <strong>aturan keselamatan</strong> tanpa memandang besaran nilai persentase Certainty Factor.
                </p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg border border-red-200 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs sm:text-sm">
            <div>
                <span class="text-xs text-stone-600 block">Layanan Darurat Krisis Kejiwaan Kemenkes RI:</span>
                <span class="text-lg font-bold text-teal-800">Hotline 119 (ekstensi 8)</span>
                <span class="text-[11px] text-stone-600 block">Layanan SEJIWA bebas pulsa (24 Jam)</span>
            </div>
            <div>
                <span class="text-xs text-stone-600 block">Tindakan Cepat:</span>
                <p class="text-stone-800 text-xs">
                    Segera hubungi orang terdekat yang Anda percayai atau kunjungi Instalasi Gawat Darurat (IGD) rumah sakit terdekat.
                </p>
            </div>
        </div>
        <div class="text-[11px] text-stone-600 italic">
            * Catatan rilis: Pastikan ketersediaan nomor darurat setempat di wilayah Anda sebelum menggunakan rujukan di atas.
        </div>
    </div>
    @endif

    <!-- KARTU HASIL UTAMA -->
    @php
        $top = $result['topResult'] ?? null;
        $isSufficient = $result['isIndicationSufficient'] ?? false;
    @endphp

    <div class="bg-white border border-stone-200 rounded-xl p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div class="space-y-1">
                <span class="text-xs font-semibold uppercase tracking-wider text-stone-600">Hasil Skrining Utama</span>
                @if ($isSufficient && $top)
                    <div class="text-2xl sm:text-3xl font-bold text-stone-900">
                        {{ $top['conditionName'] }}
                    </div>
                    <div class="text-xs sm:text-sm text-stone-600 italic">
                        Kode Hipotesis: <strong>{{ $top['conditionCode'] }}</strong> &bull; {{ $top['originalName'] }}
                    </div>
                @else
                    <div class="text-2xl font-bold text-stone-900">
                        Belum Cukup Indikasi
                    </div>
                    <div class="text-xs sm:text-sm text-stone-600">
                        Nilai Certainty Factor tertinggi berada di bawah ambang batas signifikan (&lt; 30%).
                    </div>
                @endif
            </div>

            <!-- Persentase Badge -->
            @if ($isSufficient && $top)
            <div class="sm:text-right shrink-0">
                <div class="text-4xl font-extrabold text-teal-800 font-mono">
                    {{ $top['percentage'] }}%
                </div>
                <span class="inline-block px-2.5 py-0.5 rounded text-xs font-semibold bg-teal-50 text-teal-900 border border-teal-200 mt-1">
                    {{ $top['verbalLevel'] }}
                </span>
            </div>
            @endif
        </div>

        @if ($isSufficient && $top)
        <p class="text-xs sm:text-sm text-stone-700 leading-relaxed border-t border-stone-100 pt-4">
            {{ $top['description'] }}
        </p>
        @else
        <div class="p-4 bg-stone-50 rounded-lg text-xs sm:text-sm text-stone-600 leading-relaxed border-t border-stone-100">
            Gejala yang Anda masukkan belum menunjukkan pola dominan pada 4 kategori gangguan yang diuji. Disarankan untuk memilih gejala secara lebih spesifik jika Anda merasakan gangguan emosional atau konsultasikan langsung ke fasilitas kesehatan primer.
        </div>
        @endif
    </div>

    <!-- GRAFIK BATANG HORIZONTAL SELURUH KONDISI -->
    <div class="bg-white border border-stone-200 rounded-xl p-6 sm:p-8 space-y-6">
        <div>
            <h2 class="text-base font-bold text-stone-900">Peringkat Kecenderungan Seluruh Kondisi</h2>
            <p class="text-xs text-stone-600 mt-0.5">
                Perbandingan nilai Certainty Factor kumulatif untuk keempat hipotesis klinis.
            </p>
        </div>

        <div class="space-y-4">
            @foreach ($result['allResults'] as $res)
            <div class="space-y-1.5">
                <div class="flex items-center justify-between text-xs sm:text-sm font-medium">
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-xs font-bold text-stone-600">{{ $res['conditionCode'] }}</span>
                        <span class="text-stone-900">{{ $res['conditionName'] }}</span>
                    </div>
                    <div class="font-mono font-bold text-stone-900">
                        {{ $res['percentage'] }}% <span class="text-xs font-normal text-stone-600">({{ $res['cfScore'] }})</span>
                    </div>
                </div>

                <!-- Horizontal Bar -->
                <div class="w-full bg-stone-100 rounded-full h-3 overflow-hidden">
                    <div class="bg-teal-700 h-3 rounded-full transition-all duration-500"
                         style="width: {{ min(100, max(2, $res['percentage'])) }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- DAFTAR GEJALA TERPILIH & KONTRIBUSI ATURAN -->
    @if ($top && count($top['steps']) > 0)
    <div class="bg-white border border-stone-200 rounded-xl p-6 sm:p-8 space-y-4">
        <div>
            <h2 class="text-base font-bold text-stone-900">Gejala yang Berkontribusi pada Kondisi Teratas</h2>
            <p class="text-xs text-stone-600 mt-0.5">
                Rincian gejala yang Anda pilih dan kecocokannya dengan aturan pakar untuk {{ $top['conditionName'] }}.
            </p>
        </div>

        <div class="divide-y divide-stone-100 border border-stone-200 rounded-lg overflow-hidden text-xs sm:text-sm">
            @foreach ($top['steps'] as $st)
            <div class="p-3.5 bg-white flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="space-y-0.5">
                    <div class="flex items-center gap-2">
                        <span class="font-mono font-bold text-[11px] text-teal-800 bg-teal-50 px-1.5 py-0.5 rounded">{{ $st['symptomCode'] }}</span>
                        <span class="font-medium text-stone-900">{{ $st['symptomText'] }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-4 text-xs font-mono shrink-0">
                    <span class="text-stone-600">CF User: <strong>{{ $st['cfUser'] }}</strong></span>
                    <span class="text-stone-600">&times; Pakar: <strong>{{ $st['cfPakar'] }}</strong></span>
                    <span class="font-bold text-teal-800">= {{ $st['cfRule'] }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- ACCORDION TRANSPARANSI HITUNG CF (NILAI JUAL TUGAS KULIAH) -->
    <div class="bg-white border border-stone-200 rounded-xl overflow-hidden">
        <div @click="showMathDetails = !showMathDetails"
             class="w-full p-5 sm:p-6 text-left flex items-center justify-between gap-4 hover:bg-stone-50/75 transition-colors cursor-pointer select-none">
            <div>
                <h3 class="text-base font-bold text-stone-900">Transparansi Perhitungan Matematis Certainty Factor</h3>
                <p class="text-xs text-stone-600 mt-0.5">
                    Rincian langkah kalkulasi rumus Shortliffe & Buchanan secara deterministik.
                </p>
            </div>

            <!-- Tombol Ikon Mata Kotak 36x36px -->
            <button type="button"
                    @click.stop="showMathDetails = !showMathDetails"
                    :aria-label="showMathDetails ? 'Sembunyikan rincian perhitungan' : 'Tampilkan rincian perhitungan'"
                    :aria-expanded="showMathDetails ? 'true' : 'false'"
                    :title="showMathDetails ? 'Sembunyikan rincian perhitungan' : 'Tampilkan rincian perhitungan'"
                    aria-label="Tampilkan rincian perhitungan"
                    aria-expanded="false"
                    title="Tampilkan rincian perhitungan"
                    class="w-9 h-9 shrink-0 flex items-center justify-center bg-white border border-stone-200 rounded-lg text-stone-600 hover:text-stone-900 hover:bg-stone-100/80 transition-colors duration-150 focus-visible:ring-2 focus-visible:ring-teal-700 focus-visible:ring-offset-2 outline-none cursor-pointer">
                <!-- Ikon Mata Dicoret (Eye-Off) saat Rincian Sedang Tampil -->
                <svg x-show="showMathDetails" x-cloak class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
                    <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68" />
                    <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61" />
                    <line x1="2" y1="2" x2="22" y2="22" />
                </svg>

                <!-- Ikon Mata Biasa (Eye) saat Rincian Sedang Tersembunyi -->
                <svg x-show="!showMathDetails" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                    <circle cx="12" cy="12" r="3" />
                </svg>
            </button>
        </div>

        <div x-show="showMathDetails" x-transition class="p-6 pt-0 border-t border-stone-100 space-y-6">
            <!-- Rumus Dasar -->
            <div class="bg-stone-50 p-4 rounded-lg font-mono text-xs space-y-2 border border-stone-200">
                <div class="font-bold text-stone-900">Rumus yang Digunakan:</div>
                <div>1. Bobot Aturan Tunggal: <code>CF(H, E) = CF(user) &times; CF(pakar)</code></div>
                <div>2. Kombinasi Dua Aturan: <code>CF_combine = CF_old + CF_new &times; (1 &minus; CF_old)</code></div>
                <div>3. Persentase Keyakinan: <code>Persentase = CF_combine &times; 100%</code></div>
            </div>

            <!-- Tabel Langkah Per Aturan untuk Tiap Kondisi -->
            @foreach ($result['allResults'] as $res)
            <div class="space-y-3">
                <div class="flex items-center justify-between text-xs font-semibold text-stone-800 border-b border-stone-200 pb-1">
                    <span>{{ $res['conditionCode'] }} — {{ $res['conditionName'] }}</span>
                    <span class="font-mono text-teal-800">Hasil Akhir: {{ $res['cfScore'] }} ({{ $res['percentage'] }}%)</span>
                </div>

                @if (count($res['steps']) > 0)
                <div class="space-y-2 text-xs font-mono">
                    @foreach ($res['steps'] as $s)
                    <div class="p-2.5 bg-stone-50 rounded border border-stone-200/80 leading-relaxed">
                        <div class="text-stone-700">{{ $s['formulaDescription'] }}</div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-xs text-stone-600 italic">
                    Tidak ada gejala yang relevan terpilih untuk kondisi ini (CF = 0.00).
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <!-- SARAN UMUM & KAPAN MENCARI BANTUAN -->
    @if ($top)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Saran Gaya Hidup -->
        <div class="bg-white border border-stone-200 rounded-xl p-6 space-y-3">
            <h3 class="text-sm font-bold text-stone-900 uppercase tracking-wide">Saran Penanganan Awal</h3>
            <ul class="text-xs sm:text-sm text-stone-700 space-y-2 list-disc list-inside leading-relaxed">
                @if (!empty($top['generalAdvice']) && is_array($top['generalAdvice']))
                    @foreach ($top['generalAdvice'] as $adv)
                        <li>{{ $adv }}</li>
                    @endforeach
                @else
                    <li>Jaga keteraturan pola tidur dan nutrisi seimbang.</li>
                    <li>Luangkan waktu untuk relaksasi dan berbicara dengan sahabat terpercaya.</li>
                @endif
            </ul>
        </div>

        <!-- Kapan Mencari Bantuan Profesional -->
        <div class="bg-white border border-stone-200 rounded-xl p-6 space-y-3">
            <h3 class="text-sm font-bold text-stone-900 uppercase tracking-wide">Kapan Mencari Bantuan?</h3>
            <p class="text-xs sm:text-sm text-stone-700 leading-relaxed">
                {{ $top['seekingHelpAdvice'] ?? 'Jika gejala berlanjut lebih dari dua minggu, terasa semakin membebani aktivitas kerja/kuliah, atau mengganggu hubungan sosial, jadwalkan sesi konseling dengan psikolog klinis di puskesmas atau rumah sakit.' }}
            </p>
            @if (!empty($top['datasetInsights']))
            <div class="mt-4 pt-3 border-t border-stone-100 text-[11px] text-stone-600">
                <strong>Catatan Observasi Dataset:</strong> Di antara pasien dengan profil serupa pada dataset riset, jenis terapi pendukung umum meliputi <em>{{ $top['datasetInsights']['commonTherapy'] ?? 'CBT' }}</em>. (Bukan rekomendasi resep obat medis).
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Tombol Aksi Bawah -->
    <div class="flex items-center justify-center gap-4 pt-6 no-print">
        <a href="{{ route('konsultasi.index') }}" class="inline-flex items-center gap-2 px-7 py-3 text-xs sm:text-sm font-semibold rounded-full text-white bg-[#1A3831] hover:bg-[#142621] transition-all duration-200 hover:-translate-y-0.5 shadow-sm">
            Mulai Konsultasi Baru <span aria-hidden="true">&rarr;</span>
        </a>
        <a href="{{ route('home') }}" class="px-6 py-3 text-xs sm:text-sm font-semibold rounded-full border border-[#DCE5E0] text-[#1F2A27] bg-white hover:bg-stone-50 transition-all duration-200 hover:-translate-y-0.5 shadow-2xs">
            Kembali ke Beranda
        </a>
    </div>
</div>
@endsection
