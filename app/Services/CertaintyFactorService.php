<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Service Mesin Inferensi Certainty Factor (CF)
 * Menerapkan penalaran inexact reasoning deterministik sesuai rumus baku Shortliffe & Buchanan.
 */
class CertaintyFactorService
{
    /**
     * Skala keyakinan pengguna CF(user)
     */
    public const CONFIDENCE_OPTIONS = [
        ['label' => 'Tidak', 'value' => 0.0, 'description' => 'Tidak merasakan gejala sama sekali'],
        ['label' => 'Tidak yakin', 'value' => 0.2, 'description' => 'Mungkin pernah samar-samar terasa'],
        ['label' => 'Sedikit yakin', 'value' => 0.4, 'description' => 'Kadang terasa dalam intensitas ringan'],
        ['label' => 'Cukup yakin', 'value' => 0.6, 'description' => 'Cukup sering dirasakan'],
        ['label' => 'Yakin', 'value' => 0.8, 'description' => 'Sangat sering dirasakan dan mengganggu'],
        ['label' => 'Sangat yakin', 'value' => 1.0, 'description' => 'Hampir selalu dirasakan setiap saat'],
    ];

    /**
     * Menghitung Certainty Factor untuk satu aturan:
     * CF(H, E) = CF(user) × CF(pakar)
     */
    public function calculateRuleCF(float $cfUser, float $cfPakar): float
    {
        return round($cfUser * $cfPakar, 4);
    }

    /**
     * Mengkombinasikan dua nilai Certainty Factor positif secara berurutan:
     * CF_combine = CF_old + CF_new × (1 − CF_old)
     */
    public function combineCF(float $cfOld, float $cfNew): float
    {
        if ($cfOld == 0.0) return $cfNew;
        if ($cfNew == 0.0) return $cfOld;
        $combined = $cfOld + $cfNew * (1 - $cfOld);
        return round($combined, 4);
    }

    /**
     * Menentukan interpretasi verbal dari nilai persentase CF
     */
    public function getVerbalConfidence(float $percentage): string
    {
        if ($percentage >= 85) return 'Tingkat Keyakinan Sangat Kuat';
        if ($percentage >= 70) return 'Tingkat Keyakinan Kuat';
        if ($percentage >= 50) return 'Tingkat Keyakinan Cukup Kuat';
        if ($percentage >= 30) return 'Indikasi Awal / Ringan';
        return 'Belum Cukup Indikasi';
    }

    /**
     * Mesin Inferensi Skrining Gejala:
     * Menghitung nilai CF untuk setiap kondisi berdasarkan gejala yang dipilih pengguna dan basis aturan pakar.
     */
    public function evaluateScreening(
        array $patient,
        array $selectedSymptoms,
        iterable $conditions,
        iterable $symptoms,
        iterable $rules,
        ?string $id = null
    ): array {
        $id = $id ?? (string) Str::uuid();

        // Buat map pencarian gejala dan kondisi
        $symptomMap = [];
        foreach ($symptoms as $s) {
            $code = is_array($s) ? $s['code'] : $s->code;
            $symptomMap[$code] = $s;
        }

        // 1. Deteksi aturan keselamatan mandiri (Safety Rule: G25 pikiran menyakiti diri / kematian)
        $hasSafetyAlert = false;
        $activeSafetySymptomCode = null;

        foreach ($selectedSymptoms as $code => $userVal) {
            $val = (float) $userVal;
            if ($val > 0 && isset($symptomMap[$code])) {
                $sym = $symptomMap[$code];
                $isSafety = is_array($sym)
                    ? ($sym['is_safety_trigger'] ?? $sym['isSafetyTrigger'] ?? false)
                    : ($sym->is_safety_trigger ?? $sym->isSafetyTrigger ?? false);
                if ($isSafety) {
                    $hasSafetyAlert = true;
                    $activeSafetySymptomCode = $code;
                }
            }
        }

        // 2. Kalkulasi Certainty Factor per kondisi
        $allResults = [];

        foreach ($conditions as $condition) {
            $condCode = is_array($condition) ? ($condition['code'] ?? '') : $condition->code;
            $condName = is_array($condition) ? ($condition['name'] ?? '') : $condition->name;
            $origName = is_array($condition) ? ($condition['original_name'] ?? $condition['originalName'] ?? '') : $condition->original_name;
            $desc = is_array($condition) ? ($condition['description'] ?? '') : $condition->description;
            $generalAdvice = is_array($condition) ? ($condition['general_advice'] ?? $condition['generalAdvice'] ?? []) : $condition->general_advice;
            $seekingAdvice = is_array($condition) ? ($condition['seeking_help_advice'] ?? $condition['seekingHelpAdvice'] ?? '') : $condition->seeking_help_advice;
            $insights = is_array($condition) ? ($condition['dataset_insights'] ?? $condition['datasetInsights'] ?? null) : $condition->dataset_insights;

            // Cari aturan yang relevan dengan kondisi ini
            $relevantRules = [];
            foreach ($rules as $r) {
                $rCond = is_array($r) ? ($r['condition_code'] ?? $r['conditionCode'] ?? '') : $r->condition_code;
                if ($rCond === $condCode) {
                    $relevantRules[] = $r;
                }
            }

            $steps = [];
            $cfOld = 0.0;
            $stepCount = 0;

            foreach ($relevantRules as $rule) {
                $symCode = is_array($rule) ? ($rule['symptom_code'] ?? $rule['symptomCode'] ?? '') : $rule->symptom_code;
                $cfPakar = (float) (is_array($rule) ? ($rule['cf_pakar'] ?? $rule['cfPakar'] ?? 0.0) : $rule->cf_pakar);

                $userVal = isset($selectedSymptoms[$symCode]) ? (float) $selectedSymptoms[$symCode] : 0.0;
                if ($userVal <= 0.0) continue;

                $stepCount++;
                $sym = $symptomMap[$symCode] ?? null;
                $symptomText = $sym ? (is_array($sym) ? $sym['text'] : $sym->text) : $symCode;

                $cfRule = $this->calculateRuleCF($userVal, $cfPakar);
                $cfCombined = $this->combineCF($cfOld, $cfRule);

                $cfOldFmt = number_format($cfOld, 3);
                $cfRuleFmt = number_format($cfRule, 3);
                $cfCombFmt = number_format($cfCombined, 3);

                $formulaDesc = $stepCount === 1
                    ? "Langkah 1: CF({$condCode}, {$symCode}) = CF_user({$userVal}) × CF_pakar({$cfPakar}) = {$cfRuleFmt}. Nilai awal CF = {$cfRuleFmt}."
                    : "Langkah {$stepCount}: CF({$condCode}, {$symCode}) = {$userVal} × {$cfPakar} = {$cfRuleFmt}. Kombinasi: {$cfOldFmt} + {$cfRuleFmt} × (1 − {$cfOldFmt}) = {$cfCombFmt}.";

                $steps[] = [
                    'stepNumber' => $stepCount,
                    'symptomCode' => $symCode,
                    'symptomText' => $symptomText,
                    'cfUser' => $userVal,
                    'cfPakar' => $cfPakar,
                    'cfRule' => $cfRule,
                    'cfOld' => $cfOld,
                    'cfNew' => $cfRule,
                    'cfCombined' => $cfCombined,
                    'formulaDescription' => $formulaDesc,
                ];

                $cfOld = $cfCombined;
            }

            $finalCF = round($cfOld, 4);
            $percentage = round($finalCF * 100, 1);
            $verbalLevel = $this->getVerbalConfidence($percentage);

            $allResults[] = [
                'conditionCode' => $condCode,
                'conditionName' => $condName,
                'originalName' => $origName,
                'description' => $desc,
                'cfScore' => $finalCF,
                'percentage' => $percentage,
                'verbalLevel' => $verbalLevel,
                'matchedSymptomsCount' => $stepCount,
                'steps' => $steps,
                'generalAdvice' => $generalAdvice,
                'seekingHelpAdvice' => $seekingAdvice,
                'datasetInsights' => $insights,
            ];
        }

        // Urutkan ranking berdasarkan CF tertinggi menurun
        usort($allResults, fn($a, $b) => $b['cfScore'] <=> $a['cfScore']);

        $topResult = count($allResults) > 0 ? $allResults[0] : null;
        $isIndicationSufficient = $topResult !== null && $topResult['cfScore'] >= 0.30;

        return [
            'id' => $id,
            'timestamp' => now()->toIso8601String(),
            'patient' => $patient,
            'selectedSymptoms' => $selectedSymptoms,
            'topResult' => $topResult,
            'allResults' => $allResults,
            'isIndicationSufficient' => $isIndicationSufficient,
            'hasSafetyAlert' => $hasSafetyAlert,
            'activeSafetySymptomCode' => $activeSafetySymptomCode,
        ];
    }
}
