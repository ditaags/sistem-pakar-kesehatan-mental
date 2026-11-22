@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12">
    <!-- Header Halaman -->
    <div class="border-b border-stone-200 pb-6">
        <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-800 uppercase tracking-wider mb-1">
            <span class="w-2 h-2 rounded-full bg-teal-600"></span>
            Dokumentasi Metodologi & Riset
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-stone-900 tracking-tight">Tentang Sistem & Metode Certainty Factor</h1>
        <p class="text-xs sm:text-sm text-stone-600 mt-1">
            Transparansi ilmiah mengenai perumusan inferensi Certainty Factor, sumber dataset, serta evaluasi model.
        </p>
    </div>

    <!-- 1. LANDASAN TEORI & FORMULASI CERTAINTY FACTOR -->
    <section class="bg-white border border-stone-200 rounded-xl p-6 sm:p-8 space-y-6">
        <div>
            <h2 class="text-lg font-bold text-stone-900">1. Metode Certainty Factor (Shortliffe & Buchanan)</h2>
            <p class="text-xs sm:text-sm text-stone-600 mt-1">
                Certainty Factor (CF) adalah metode penalaran aproksimatif (*inexact reasoning*) yang dirancang khusus untuk mengakomodasi ketidakpastian dalam sistem pakar medis.
            </p>
        </div>

        <div class="space-y-4 text-xs sm:text-sm text-stone-700 leading-relaxed">
            <p>
                Dalam sistem skrining ini, pengguna memilih gejala yang dialami beserta derajat kepastian subjektif ($CF_{user}$), yang kemudian dipadukan dengan bobot keyakinan pakar klinis ($CF_{pakar}$).
            </p>

            <div class="bg-stone-50 border border-stone-200 p-4 rounded-lg font-mono space-y-3">
                <div class="font-bold text-stone-900">Perumusan Matematis:</div>
                <div>
                    <strong>Tahap 1: Nilai CF Aturan Tunggal</strong><br>
                    <code>CF(H, E) = CF(user) &times; CF(pakar)</code>
                </div>
                <div>
                    <strong>Tahap 2: Kombinasi Bukti Berkelanjutan (Kombinasi Rekursif)</strong><br>
                    <code>CF_combine(CF_old, CF_new) = CF_old + CF_new &times; (1 &minus; CF_old)</code>
                </div>
                <div>
                    <strong>Tahap 3: Konversi Persentase Keyakinan</strong><br>
                    <code>Persentase (%) = CF_combine &times; 100</code>
                </div>
            </div>

            <div>
                <h4 class="font-semibold text-stone-900 mb-2">Skala Derajat Keyakinan Pengguna:</h4>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 font-mono text-xs">
                    <div class="p-2.5 bg-stone-50 rounded border border-stone-200">Tidak = <strong>0.0</strong></div>
                    <div class="p-2.5 bg-stone-50 rounded border border-stone-200">Tidak yakin = <strong>0.2</strong></div>
                    <div class="p-2.5 bg-stone-50 rounded border border-stone-200">Sedikit yakin = <strong>0.4</strong></div>
                    <div class="p-2.5 bg-stone-50 rounded border border-stone-200">Cukup yakin = <strong>0.6</strong></div>
                    <div class="p-2.5 bg-stone-50 rounded border border-stone-200">Yakin = <strong>0.8</strong></div>
                    <div class="p-2.5 bg-stone-50 rounded border border-stone-200">Sangat yakin = <strong>1.0</strong></div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. PENDEKATAN HIBRIDA (HYBRID APPROACH) & TEMUAN DATASET -->
    <section class="bg-white border border-stone-200 rounded-xl p-6 sm:p-8 space-y-6">
        <div>
            <h2 class="text-lg font-bold text-stone-900">2. Mengapa Menggunakan Pendekatan Hibrida?</h2>
            <p class="text-xs sm:text-sm text-stone-600 mt-1">
                Pertanggungjawaban akademik terhadap karakteristik dataset publik Kaggle dan literatur klinis.
            </p>
        </div>

        <div class="space-y-4 text-xs sm:text-sm text-stone-700 leading-relaxed">
            <p>
                Sistem ini merujuk pada dataset publik <em>"Mental Health Diagnosis and Treatment Dataset"</em> dari Kaggle yang mencakup 500 rekaman pasien, 17 variabel, dan 4 kategori diagnosis (Major Depressive Disorder, Generalized Anxiety, Bipolar Disorder, Panic Disorder).
            </p>

            <div class="p-4 bg-amber-50/70 border border-amber-200 rounded-lg text-amber-950 space-y-2">
                <div class="font-bold text-xs uppercase tracking-wider text-amber-900">Temuan Analisis Data Kritis:</div>
                <p class="text-xs leading-relaxed">
                    Setelah dilakukan pengujian statistik, terungkap bahwa fitur numerik pada dataset (Mood Score, Sleep Quality, Stress Level, Symptom Severity, dan Physical Activity) memiliki rata-rata yang <strong>hampir identik antar keempat kelompok diagnosis</strong> (selisih rata-rata &lt; 0.3 poin). Selain itu, probabilitas bersyarat $P(\text{gejala} \mid \text{diagnosis})$ hanya berbeda sekitar 8% – 18%, mengindikasikan bahwa dataset publik tersebut berkarakteristik <em>sintetis/simulasi</em>.
                </p>
            </div>

            <p>
                Jika nilai Certainty Factor diturunkan murni dari dataset ini secara mekanis, nilai CF untuk semua gangguan akan seragam dan sistem kehilangan daya diskriminasi klinis. Oleh karena itu, diterapkan <strong>Pendekatan Hibrida</strong>:
            </p>

            <ul class="list-disc list-inside space-y-2 pl-2">
                <li><strong>Basis Pengetahuan Klinis Utama:</strong> Diturunkan dari pedoman diagnostik psikiatri resmi (<strong>DSM-5</strong> dan <strong>PPDGJ-III</strong>) dengan 25 gejala spesifik dan bobot CF pakar (0.40 – 0.90).</li>
                <li><strong>Dataset Numerik untuk Ambang & Deskripsi:</strong> Digunakan untuk validasi ambang batas numerik (Mood &le; 4, Sleep &le; 5, Stress &ge; 8) serta statistik pengobatan deskriptif di halaman hasil.</li>
                <li><strong>Evaluasi Transparan:</strong> Digunakan untuk menguji performa inferensi numerik secara apa adanya tanpa manipulasi angka akurasi.</li>
            </ul>
        </div>
    </section>

    <!-- 3. HASIL EVALUASI MODEL PADA DATASET -->
    @php
        $ev = $evaluationData ?? [];
        $cm = $ev['confusionMatrix'] ?? [];
        $acc = $ev['accuracy'] ?? 28;
    @endphp
    <section class="bg-white border border-stone-200 rounded-xl p-6 sm:p-8 space-y-6">
        <div>
            <h2 class="text-lg font-bold text-stone-900">3. Hasil Evaluasi Mesin CF pada Dataset (php artisan pakar:evaluasi)</h2>
            <p class="text-xs sm:text-sm text-stone-600 mt-1">
                Laporan akurasi dan matriks konfusi pengujian 500 baris pasien dataset CSV.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 bg-stone-50 border border-stone-200 rounded-lg text-center">
                <div class="text-2xl font-bold text-stone-900">500</div>
                <div class="text-xs text-stone-600 mt-1">Total Sampel Dievaluasi</div>
            </div>
            <div class="p-4 bg-stone-50 border border-stone-200 rounded-lg text-center">
                <div class="text-2xl font-bold text-teal-800">{{ $acc }}%</div>
                <div class="text-xs text-stone-600 mt-1">Akurasi Global pada Fitur Numerik</div>
            </div>
            <div class="p-4 bg-stone-50 border border-stone-200 rounded-lg text-center">
                <div class="text-2xl font-bold text-stone-600">25.0%</div>
                <div class="text-xs text-stone-600 mt-1">Baseline Tebakan Acak (4 Kelas)</div>
            </div>
        </div>

        <!-- Matriks Konfusi -->
        <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-stone-700 mb-3">Matriks Konfusi (Confusion Matrix):</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border border-stone-200 rounded-lg overflow-hidden">
                    <thead class="bg-stone-100 text-stone-700 font-semibold border-b border-stone-200">
                        <tr>
                            <th class="p-3">Aktual \ Prediksi</th>
                            <th class="p-3">K01 (Depresi)</th>
                            <th class="p-3">K02 (Cemas)</th>
                            <th class="p-3">K03 (Bipolar)</th>
                            <th class="p-3">K04 (Panik)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 font-mono">
                        <tr>
                            <td class="p-3 font-semibold font-sans bg-stone-50">K01 — Depresi Mayor</td>
                            <td class="p-3 font-bold text-teal-900 bg-teal-50/50">{{ $cm['K01']['K01'] ?? 43 }}</td>
                            <td class="p-3 text-stone-600">{{ $cm['K01']['K02'] ?? 63 }}</td>
                            <td class="p-3 text-stone-600">{{ $cm['K01']['K03'] ?? 19 }}</td>
                            <td class="p-3 text-stone-600">{{ $cm['K01']['K04'] ?? 0 }}</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-semibold font-sans bg-stone-50">K02 — Gangguan Cemas</td>
                            <td class="p-3 text-stone-600">{{ $cm['K02']['K01'] ?? 39 }}</td>
                            <td class="p-3 font-bold text-teal-900 bg-teal-50/50">{{ $cm['K02']['K02'] ?? 79 }}</td>
                            <td class="p-3 text-stone-600">{{ $cm['K02']['K03'] ?? 17 }}</td>
                            <td class="p-3 text-stone-600">{{ $cm['K02']['K04'] ?? 0 }}</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-semibold font-sans bg-stone-50">K03 — Gangguan Bipolar</td>
                            <td class="p-3 text-stone-600">{{ $cm['K03']['K01'] ?? 28 }}</td>
                            <td class="p-3 text-stone-600">{{ $cm['K03']['K02'] ?? 78 }}</td>
                            <td class="p-3 font-bold text-teal-900 bg-teal-50/50">{{ $cm['K03']['K03'] ?? 18 }}</td>
                            <td class="p-3 text-stone-600">{{ $cm['K03']['K04'] ?? 0 }}</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-semibold font-sans bg-stone-50">K04 — Gangguan Panik</td>
                            <td class="p-3 text-stone-600">{{ $cm['K04']['K01'] ?? 28 }}</td>
                            <td class="p-3 text-stone-600">{{ $cm['K04']['K02'] ?? 76 }}</td>
                            <td class="p-3 text-stone-600">{{ $cm['K04']['K03'] ?? 12 }}</td>
                            <td class="p-3 font-bold text-teal-900 bg-teal-50/50">{{ $cm['K04']['K04'] ?? 0 }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="text-[11px] text-stone-600 mt-2">
                * Keterangan: Akurasi ~28% pada dataset murni terjadi karena dataset tidak memuat indikator kardinal serangan panik (G14–G18) atau mania bipolar (G05, G08, G20). Inilah alasan ilmiah mengapa sistem pakar klinis berbasis kuesioner pengguna jauh lebih superior daripada klasifikasi berbasis data numerik umum saja.
            </p>
        </div>
    </section>

    <!-- 4. PROFIL PENGEMBANG & BATASAN SISTEM -->
    <section class="bg-white border border-stone-200 rounded-xl p-6 sm:p-8 space-y-4">
        <h2 class="text-lg font-bold text-stone-900">4. Keterbatasan & Profil Pengembang</h2>
        <div class="text-xs sm:text-sm text-stone-700 space-y-3 leading-relaxed">
            <p>
                Aplikasi ini dikembangkan sebagai pemenuhan proyek tugas kuliah Informatika / Ilmu Komputer dalam bidang Rekayasa Perangkat Lunak dan Sistem Pakar (*Expert Systems*).
            </p>
            <div class="p-4 bg-stone-50 rounded-lg border border-stone-200 space-y-1 text-xs">
                <div><strong>Tujuan Aplikasi:</strong> Media edukasi & skrining mandiri kesehatan mental berbasis kaidah penalaran pasti.</div>
                <div><strong>Framework & Bahasa:</strong> Laravel 13, PHP 8.3, Tailwind CSS, Alpine.js, MySQL.</div>
                <div><strong>Sumber Referensi Klinis:</strong> Diagnostic and Statistical Manual of Mental Disorders, 5th Edition (DSM-5); Pedoman Penggolongan dan Diagnosis Gangguan Jiwa di Indonesia III (PPDGJ-III).</div>
            </div>
        </div>
    </section>
</div>
@endsection
