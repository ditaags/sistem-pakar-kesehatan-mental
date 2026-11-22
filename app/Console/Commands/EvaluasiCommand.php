<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Models\Condition;
use App\Models\Symptom;
use App\Models\Rule;
use App\Services\CertaintyFactorService;

/**
 * Artisan Command: php artisan pakar:evaluasi
 * Menjalankan evaluasi akurasi dan matriks konfusi mesin CF pada 500 baris data CSV.
 */
class EvaluasiCommand extends Command
{
    protected $signature = 'pakar:evaluasi';
    protected $description = 'Menjalankan evaluasi akurasi mesin CF pada dataset CSV dan menghasilkan evaluation_summary.json';

    public function handle(CertaintyFactorService $cfService): int
    {
        $csvPath = base_path('data/mental_health_diagnosis_treatment_.csv');
        if (!File::exists($csvPath)) {
            $this->error("Dataset CSV tidak ditemukan di {$csvPath}");
            return 1;
        }

        $this->info('--- MEMULAI EVALUASI DATASET MENGGUNAKAN MESIN CF (LARAVEL) ---');

        $conditions = Condition::all();
        $symptoms = Symptom::all();
        $rules = Rule::all();

        $diagToCode = [
            'Major Depressive Disorder' => 'K01',
            'Generalized Anxiety' => 'K02',
            'Bipolar Disorder' => 'K03',
            'Panic Disorder' => 'K04',
        ];

        $codes = ['K01', 'K02', 'K03', 'K04'];
        $confusionMatrix = [];
        foreach ($codes as $actual) {
            foreach ($codes as $pred) {
                $confusionMatrix[$actual][$pred] = 0;
            }
        }

        $lines = explode("\n", trim(File::get($csvPath)));
        $correctCount = 0;
        $totalEvaluated = 0;

        for ($i = 1; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if (empty($line)) continue;
            $parts = str_getcsv($line);
            if (count($parts) < 13) continue;

            $patientId = $parts[0];
            $age = (int) $parts[1];
            $gender = $parts[2];
            $actualDiagnosis = trim($parts[3]);
            $actualCode = $diagToCode[$actualDiagnosis] ?? null;
            if (!$actualCode) continue;

            $severity = (float) $parts[4];
            $mood = (float) $parts[5];
            $sleep = (float) $parts[6];
            $activity = (float) $parts[7];
            $stress = (float) $parts[12];

            // Pemetaan fitur numerik dataset menjadi keyakinan gejala CF(user)
            $userSymptoms = [];

            // 1. Mood rendah (Mood <= 4) -> G01
            if ($mood <= 4) {
                $userSymptoms['G01'] = round(1 - ($mood - 1) / 3 * 0.4, 1);
            } elseif ($mood >= 7) {
                $userSymptoms['G05'] = 0.6;
            }

            // 2. Kualitas tidur buruk (Sleep <= 5) -> G07
            if ($sleep <= 5) {
                $userSymptoms['G07'] = 0.8;
            }

            // 3. Stres sangat tinggi (Stress >= 8) -> G24 dan G11
            if ($stress >= 8) {
                $userSymptoms['G24'] = 0.8;
                $userSymptoms['G11'] = 0.6;
            }

            // 4. Aktivitas fisik rendah (<= 3 jam)
            if ($activity <= 3) {
                $userSymptoms['G24'] = max($userSymptoms['G24'] ?? 0, 0.6);
            }

            // 5. Keparahan gejala tinggi (Severity >= 8)
            if ($severity >= 8) {
                $userSymptoms['G12'] = 0.6;
                if ($stress >= 9) {
                    $userSymptoms['G15'] = 0.6;
                }
            }

            $patient = [
                'name' => "Patient #{$patientId}",
                'age' => $age,
                'gender' => $gender,
            ];

            $result = $cfService->evaluateScreening($patient, $userSymptoms, $conditions, $symptoms, $rules);
            $predictedCode = $result['topResult']['conditionCode'] ?? 'K01';

            $confusionMatrix[$actualCode][$predictedCode]++;
            if ($actualCode === $predictedCode) {
                $correctCount++;
            }
            $totalEvaluated++;
        }

        $overallAccuracy = round(($correctCount / $totalEvaluated) * 100, 2);

        // Hitung metrik per kelas
        $classMetrics = [];
        foreach ($codes as $code) {
            $totalActual = 0;
            $totalPredicted = 0;
            $truePositive = $confusionMatrix[$code][$code];

            foreach ($codes as $other) {
                $totalActual += $confusionMatrix[$code][$other];
                $totalPredicted += $confusionMatrix[$other][$code];
            }

            $precision = $totalPredicted > 0 ? round(($truePositive / $totalPredicted) * 100, 2) : 0;
            $recall = $totalActual > 0 ? round(($truePositive / $totalActual) * 100, 2) : 0;

            $classMetrics[$code] = [
                'totalActual' => $totalActual,
                'totalPredicted' => $totalPredicted,
                'truePositive' => $truePositive,
                'precision' => $precision,
                'recall' => $recall,
            ];
        }

        $limitations = [
            'Distribusi fitur numerik (Mood, Sleep, Stress, Severity, Physical Activity) di dataset hampir identik pada 4 kelompok diagnosis (selisih rata-rata < 0.3 poin skala 1-10).',
            'Probabilitas kemunculan fitur P(E|H) hanya berselisih 8% - 18% antar kelas, mendekati distribusi seragam acak (random uniform distribution).',
            'Dataset publik Kaggle ini berkarakteristik data sintetis buatan (simulasi) dan tidak mencerminkan data rekam medis klinis nyata di rumah sakit.',
            'Menurunkan nilai CF murni dari dataset ini akan menghasilkan nilai bobot yang hampir sama untuk semua penyakit sehingga tidak mampu membedakan diagnosis.',
            'Solusi akademik yang dapat dipertanggungjawabkan: Menggunakan Pendekatan Hibrida, di mana basis aturan dan bobot CF utama diturunkan dari literatur diagnosis klinis (DSM-5 & PPDGJ III), sedangkan dataset digunakan untuk validasi ambang numerik deskriptif dan analisis keterbatasan sistem.',
        ];

        $summary = [
            'totalRecords' => $totalEvaluated,
            'correctPredictions' => $correctCount,
            'accuracy' => (int) $overallAccuracy,
            'confusionMatrix' => $confusionMatrix,
            'classMetrics' => $classMetrics,
            'limitationsAnalysis' => $limitations,
        ];

        $outputPath = base_path('data/evaluation_summary.json');
        File::put($outputPath, json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("\n--- HASIL EVALUASI MESIN CF PADA DATASET ---");
        $this->line("Total Pasien Dievaluasi : {$totalEvaluated}");
        $this->line("Prediksi Cocok          : {$correctCount}");
        $this->line("Akurasi Global          : {$overallAccuracy}% (Baseline acak 4-kelas = 25%)");

        $this->table(
            ['Aktual \ Prediksi', 'K01 (Depresi)', 'K02 (Cemas)', 'K03 (Bipolar)', 'K04 (Panik)'],
            [
                ['K01', $confusionMatrix['K01']['K01'], $confusionMatrix['K01']['K02'], $confusionMatrix['K01']['K03'], $confusionMatrix['K01']['K04']],
                ['K02', $confusionMatrix['K02']['K01'], $confusionMatrix['K02']['K02'], $confusionMatrix['K02']['K03'], $confusionMatrix['K02']['K04']],
                ['K03', $confusionMatrix['K03']['K01'], $confusionMatrix['K03']['K02'], $confusionMatrix['K03']['K03'], $confusionMatrix['K03']['K04']],
                ['K04', $confusionMatrix['K04']['K01'], $confusionMatrix['K04']['K02'], $confusionMatrix['K04']['K03'], $confusionMatrix['K04']['K04']],
            ]
        );

        $this->info("✅ Ringkasan evaluasi berhasil disimpan ke: {$outputPath}");
        return 0;
    }
}
