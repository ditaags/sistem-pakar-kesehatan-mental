<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Condition;
use App\Models\Symptom;
use App\Models\Rule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Admin Default untuk Login Session
        User::updateOrCreate(
            ['email' => 'admin@mindscreen.expert'],
            [
                'name' => 'Administrator Pakar',
                'password' => Hash::make('adminpakar2026'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Baca Basis Pengetahuan dari data/knowledge_base.json
        $kbPath = base_path('data/knowledge_base.json');
        if (!File::exists($kbPath)) {
            $this->command->error("File knowledge_base.json tidak ditemukan di {$kbPath}");
            return;
        }

        $kb = json_decode(File::get($kbPath), true);

        // Seed Conditions
        foreach ($kb['conditions'] as $c) {
            Condition::updateOrCreate(
                ['code' => $c['code']],
                [
                    'name' => $c['name'],
                    'original_name' => $c['originalName'],
                    'description' => $c['description'],
                    'general_advice' => $c['generalAdvice'],
                    'seeking_help_advice' => $c['seekingHelpAdvice'],
                    'dataset_insights' => $c['datasetInsights'] ?? null,
                ]
            );
        }

        // Seed Symptoms
        foreach ($kb['symptoms'] as $s) {
            Symptom::updateOrCreate(
                ['code' => $s['code']],
                [
                    'text' => $s['text'],
                    'category' => $s['category'],
                    'clinical_explanation' => $s['clinicalExplanation'],
                    'is_safety_trigger' => $s['isSafetyTrigger'] ?? false,
                ]
            );
        }

        // Seed Rules
        foreach ($kb['rules'] as $r) {
            Rule::updateOrCreate(
                ['id' => $r['id']],
                [
                    'condition_code' => $r['conditionCode'],
                    'symptom_code' => $r['symptomCode'],
                    'cf_pakar' => $r['cfPakar'],
                    'source_literature' => $r['sourceLiterature'],
                ]
            );
        }

        $this->command->info('Seeding berhasil: 1 Admin, 4 Kondisi, 25 Gejala, 32 Aturan CF.');
    }
}
