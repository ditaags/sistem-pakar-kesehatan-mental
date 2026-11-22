<?php

namespace App\Http\Controllers;

use App\Models\Condition;
use App\Models\Symptom;
use Illuminate\View\View;

/**
 * Controller untuk Halaman Utama (Beranda)
 */
class HomeController extends Controller
{
    public function index(): View
    {
        try {
            $conditions = Condition::orderBy('code')->get();
            $symptoms = Symptom::orderBy('code')->get();
        } catch (\Throwable $e) {
            $kbPath = base_path('data/knowledge_base.json');
            $kb = file_exists($kbPath) ? json_decode(file_get_contents($kbPath), true) : [];

            $conditions = collect($kb['conditions'] ?? [])->map(fn($c) => (object)[
                'code' => $c['code'],
                'name' => $c['name'],
                'original_name' => $c['originalName'],
                'description' => $c['description'],
                'general_advice' => $c['generalAdvice'] ?? [],
                'seeking_help_advice' => $c['seekingHelpAdvice'] ?? '',
                'dataset_insights' => $c['datasetInsights'] ?? null,
            ]);

            $symptoms = collect($kb['symptoms'] ?? [])->map(fn($s) => (object)[
                'code' => $s['code'],
                'text' => $s['text'],
                'category' => $s['category'],
                'clinical_explanation' => $s['clinicalExplanation'] ?? '',
                'is_safety_trigger' => $s['isSafetyTrigger'] ?? false,
            ]);
        }

        return view('pages.home', compact('conditions', 'symptoms'));
    }
}
