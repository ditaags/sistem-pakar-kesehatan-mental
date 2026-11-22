<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\CertaintyFactorService;

class CertaintyFactorServiceTest extends TestCase
{
    protected CertaintyFactorService $cfService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cfService = new CertaintyFactorService();
    }

    /**
     * KASUS 1: Hitung manual 2 aturan sesuai rumus kombinasi CF_old + CF_new*(1 - CF_old)
     * G01: CF_user = 0.8, CF_pakar = 0.8 -> CF_rule = 0.64
     * G02: CF_user = 0.6, CF_pakar = 0.7 -> CF_rule = 0.42
     * Hitung manual: CF_combine = 0.64 + 0.42 * (1 - 0.64) = 0.64 + 0.1512 = 0.7912
     */
    public function test_manual_calculation_combination_of_two_rules(): void
    {
        $rule1 = $this->cfService->calculateRuleCF(0.8, 0.8); // 0.64
        $rule2 = $this->cfService->calculateRuleCF(0.6, 0.7); // 0.42
        $combined = $this->cfService->combineCF($rule1, $rule2);

        $expected = 0.7912;
        $this->assertEqualsWithDelta($expected, $combined, 0.0001, 'Kombinasi 2 aturan harus menghasilkan 0.7912');
    }

    /**
     * KASUS 2: Kombinasi beruntun 3 aturan berturut-turut
     * CF1 = 0.5, CF2 = 0.4 -> Step 1: 0.5 + 0.4*(1 - 0.5) = 0.70
     * CF3 = 0.3 -> Step 2: 0.70 + 0.3*(1 - 0.70) = 0.70 + 0.09 = 0.79
     */
    public function test_sequential_three_rules_combination(): void
    {
        $step1 = $this->cfService->combineCF(0.5, 0.4);
        $step2 = $this->cfService->combineCF($step1, 0.3);

        $expected = 0.79;
        $this->assertEqualsWithDelta($expected, $step2, 0.0001, 'Kombinasi 3 aturan berurutan harus menghasilkan 0.79');
    }

    /**
     * KASUS 3: Deteksi "Belum Cukup Indikasi" jika CF < 0.30
     */
    public function test_low_cf_triggers_insufficient_indication(): void
    {
        $mockConditions = [
            (object)[
                'code' => 'K01',
                'name' => 'Depresi Mayor',
                'original_name' => 'Major Depressive Disorder',
                'description' => 'Test',
                'general_advice' => [],
                'seeking_help_advice' => '',
                'dataset_insights' => null,
            ],
        ];

        $mockSymptoms = [
            (object)[
                'code' => 'G01',
                'text' => 'Mood rendah',
                'category' => 'Suasana Hati',
                'clinical_explanation' => '',
                'is_safety_trigger' => false,
            ],
        ];

        $mockRules = [
            (object)[
                'id' => 'R01',
                'condition_code' => 'K01',
                'symptom_code' => 'G01',
                'cf_pakar' => 0.8,
            ],
        ];

        // CF_user = 0.2 -> CF_rule = 0.16 (< 0.30)
        $res = $this->cfService->evaluateScreening(
            ['name' => 'Budi', 'age' => 20, 'gender' => 'Male'],
            ['G01' => 0.2],
            $mockConditions,
            $mockSymptoms,
            $mockRules
        );

        $this->assertFalse($res['isIndicationSufficient']);
        $this->assertEquals(0.16, $res['topResult']['cfScore']);
    }

    /**
     * KASUS 4: Aturan Keselamatan Darurat (Safety Trigger G25)
     */
    public function test_safety_symptom_triggers_emergency_alert(): void
    {
        $mockConditions = [
            (object)[
                'code' => 'K01',
                'name' => 'Depresi Mayor',
                'original_name' => 'Major Depressive Disorder',
                'description' => 'Test',
                'general_advice' => [],
                'seeking_help_advice' => '',
                'dataset_insights' => null,
            ],
        ];

        $mockSymptoms = [
            (object)[
                'code' => 'G01',
                'text' => 'Mood rendah',
                'category' => 'Suasana Hati',
                'clinical_explanation' => '',
                'is_safety_trigger' => false,
            ],
            (object)[
                'code' => 'G25',
                'text' => 'Pikiran menyakiti diri',
                'category' => 'Keselamatan',
                'clinical_explanation' => '',
                'is_safety_trigger' => true,
            ],
        ];

        $mockRules = [
            (object)[
                'id' => 'R01',
                'condition_code' => 'K01',
                'symptom_code' => 'G01',
                'cf_pakar' => 0.8,
            ],
        ];

        $res = $this->cfService->evaluateScreening(
            ['name' => 'Budi', 'age' => 20, 'gender' => 'Male'],
            ['G01' => 0.8, 'G25' => 0.4],
            $mockConditions,
            $mockSymptoms,
            $mockRules
        );

        $this->assertTrue($res['hasSafetyAlert']);
        $this->assertEquals('G25', $res['activeSafetySymptomCode']);
    }

    /**
     * KASUS 5: Pemeringkatan Multi-Kondisi dan Seleksi Kondisi Tertinggi
     */
    public function test_ranking_and_top_condition_selection(): void
    {
        $mockConditions = [
            (object)[
                'code' => 'K01',
                'name' => 'Depresi Mayor',
                'original_name' => 'Major Depressive Disorder',
                'description' => 'Test',
                'general_advice' => [],
                'seeking_help_advice' => '',
                'dataset_insights' => null,
            ],
            (object)[
                'code' => 'K04',
                'name' => 'Gangguan Panik',
                'original_name' => 'Panic Disorder',
                'description' => 'Test',
                'general_advice' => [],
                'seeking_help_advice' => '',
                'dataset_insights' => null,
            ],
        ];

        $mockSymptoms = [
            (object)['code' => 'G01', 'text' => 'Mood rendah', 'category' => 'Suasana Hati', 'clinical_explanation' => '', 'is_safety_trigger' => false],
            (object)['code' => 'G14', 'text' => 'Panik mendadak', 'category' => 'Kecemasan', 'clinical_explanation' => '', 'is_safety_trigger' => false],
            (object)['code' => 'G15', 'text' => 'Palpitasi jantung', 'category' => 'Kecemasan', 'clinical_explanation' => '', 'is_safety_trigger' => false],
        ];

        $mockRules = [
            (object)['id' => 'R01', 'condition_code' => 'K01', 'symptom_code' => 'G01', 'cf_pakar' => 0.8],
            (object)['id' => 'R04', 'condition_code' => 'K04', 'symptom_code' => 'G14', 'cf_pakar' => 0.9],
            (object)['id' => 'R05', 'condition_code' => 'K04', 'symptom_code' => 'G15', 'cf_pakar' => 0.8],
        ];

        // G14 (1.0 * 0.9 = 0.9), G15 (0.8 * 0.8 = 0.64) -> 0.9 + 0.64 * 0.1 = 0.964 (96.4%)
        // G01 (0.4 * 0.8 = 0.32) -> 32%
        $res = $this->cfService->evaluateScreening(
            ['name' => 'Budi', 'age' => 20, 'gender' => 'Male'],
            ['G14' => 1.0, 'G15' => 0.8, 'G01' => 0.4],
            $mockConditions,
            $mockSymptoms,
            $mockRules
        );

        $this->assertEquals('K04', $res['topResult']['conditionCode']);
        $this->assertEqualsWithDelta(0.964, $res['topResult']['cfScore'], 0.001);
        $this->assertEquals('K04', $res['allResults'][0]['conditionCode']);
        $this->assertEquals('K01', $res['allResults'][1]['conditionCode']);
    }

    /**
     * KASUS 6: Interpretasi Verbal Keyakinan
     */
    public function test_verbal_confidence_levels(): void
    {
        $this->assertEquals('Tingkat Keyakinan Sangat Kuat', $this->cfService->getVerbalConfidence(90.0));
        $this->assertEquals('Tingkat Keyakinan Kuat', $this->cfService->getVerbalConfidence(75.0));
        $this->assertEquals('Tingkat Keyakinan Cukup Kuat', $this->cfService->getVerbalConfidence(55.0));
        $this->assertEquals('Indikasi Awal / Ringan', $this->cfService->getVerbalConfidence(35.0));
        $this->assertEquals('Belum Cukup Indikasi', $this->cfService->getVerbalConfidence(20.0));
    }
}
