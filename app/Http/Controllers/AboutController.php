<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\View\View;

/**
 * Controller untuk Halaman Tentang Kami, Metode CF & Evaluasi
 */
class AboutController extends Controller
{
    public function index(): View
    {
        $evalPath = base_path('data/evaluation_summary.json');
        $evaluationData = [
            'totalRecords' => 500,
            'accuracy' => 28,
            'confusionMatrix' => [
                'K01' => ['K01' => 43, 'K02' => 63, 'K03' => 19, 'K04' => 0],
                'K02' => ['K01' => 39, 'K02' => 79, 'K03' => 17, 'K04' => 0],
                'K03' => ['K01' => 28, 'K02' => 78, 'K03' => 18, 'K04' => 0],
                'K04' => ['K01' => 28, 'K02' => 76, 'K03' => 12, 'K04' => 0],
            ],
            'classMetrics' => [
                'K01' => ['totalActual' => 125, 'totalPredicted' => 138, 'truePositive' => 43, 'precision' => 31.16, 'recall' => 34.4],
                'K02' => ['totalActual' => 135, 'totalPredicted' => 296, 'truePositive' => 79, 'precision' => 26.69, 'recall' => 58.52],
                'K03' => ['totalActual' => 124, 'totalPredicted' => 66, 'truePositive' => 18, 'precision' => 27.27, 'recall' => 14.52],
                'K04' => ['totalActual' => 116, 'totalPredicted' => 0, 'truePositive' => 0, 'precision' => 0, 'recall' => 0],
            ],
            'limitationsAnalysis' => [
                'Distribusi fitur numerik di dataset hampir identik pada 4 kelompok diagnosis.',
                'Probabilitas kemunculan fitur P(E|H) hanya berselisih 8% - 18% antar kelas.',
                'Dataset publik Kaggle ini berkarakteristik sintetis buatan (simulasi).',
                'Solusi akademik: Menggunakan Pendekatan Hibrida (Aturan Klinis DSM-5 + Dataset Deskriptif).',
            ],
        ];

        if (File::exists($evalPath)) {
            $parsed = json_decode(File::get($evalPath), true);
            if ($parsed) {
                $evaluationData = $parsed;
            }
        }

        return view('pages.about', compact('evaluationData'));
    }
}
