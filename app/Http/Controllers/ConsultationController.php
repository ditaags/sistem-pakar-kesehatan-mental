<?php

namespace App\Http\Controllers;

use App\Models\Condition;
use App\Models\Symptom;
use App\Models\Rule;
use App\Models\Consultation;
use App\Services\CertaintyFactorService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Controller untuk Alur Konsultasi & Hasil Skrining
 */
class ConsultationController extends Controller
{
    public function __construct(
        protected CertaintyFactorService $cfService
    ) {}

    /**
     * Menampilkan formulir wizard konsultasi 3 langkah
     */
    public function index(): View
    {
        try {
            $symptoms = Symptom::orderBy('code')->get();
        } catch (\Throwable $e) {
            $kbPath = base_path('data/knowledge_base.json');
            $kb = file_exists($kbPath) ? json_decode(file_get_contents($kbPath), true) : [];
            $symptoms = collect($kb['symptoms'] ?? [])->map(fn($s) => (object)[
                'code' => $s['code'],
                'text' => $s['text'],
                'category' => $s['category'],
                'clinical_explanation' => $s['clinicalExplanation'] ?? '',
                'is_safety_trigger' => $s['isSafetyTrigger'] ?? false,
            ]);
        }

        $confidenceOptions = CertaintyFactorService::CONFIDENCE_OPTIONS;

        return view('pages.consultation', compact('symptoms', 'confidenceOptions'));
    }

    /**
     * Memproses data diri dan gejala yang dipilih pengguna
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_name' => 'nullable|string|max:100',
            'patient_age' => 'nullable|integer|min:12|max:100',
            'patient_gender' => 'nullable|string|in:Female,Male,Other',
            'symptoms' => 'required|array',
        ], [
            'symptoms.required' => 'Harap pilih minimal 3 gejala yang Anda rasakan.',
        ]);

        // Filter gejala yang nilainya > 0
        $selectedSymptoms = [];
        foreach ($request->input('symptoms', []) as $code => $val) {
            $valFloat = (float) $val;
            if ($valFloat > 0.0) {
                $selectedSymptoms[$code] = $valFloat;
            }
        }

        if (count($selectedSymptoms) < 3) {
            return back()
                ->withInput()
                ->withErrors(['symptoms' => 'Harap pilih minimal 3 gejala yang pernah Anda rasakan untuk kepastian perhitungan.']);
        }

        $patient = [
            'name' => !empty($validated['patient_name']) ? trim($validated['patient_name']) : 'Anonim',
            'age' => !empty($validated['patient_age']) ? (int) $validated['patient_age'] : 20,
            'gender' => $validated['patient_gender'] ?? 'Female',
        ];

        // Ambil data aktif dari database (dengan fallback ke knowledge_base.json jika DB offline)
        try {
            $conditions = Condition::all();
            $symptoms = Symptom::all();
            $rules = Rule::all();
        } catch (\Throwable $e) {
            $kbPath = base_path('data/knowledge_base.json');
            $kb = file_exists($kbPath) ? json_decode(file_get_contents($kbPath), true) : [];
            $conditions = collect($kb['conditions'] ?? []);
            $symptoms = collect($kb['symptoms'] ?? []);
            $rules = collect($kb['rules'] ?? []);
        }

        // Hitung nilai Certainty Factor
        $result = $this->cfService->evaluateScreening(
            $patient,
            $selectedSymptoms,
            $conditions,
            $symptoms,
            $rules
        );

        // Simpan ke database (dan cache ke session sebagai backup)
        try {
            $consultation = Consultation::create([
                'id' => $result['id'],
                'patient_name' => $patient['name'],
                'patient_age' => $patient['age'],
                'patient_gender' => $patient['gender'],
                'selected_symptoms' => $selectedSymptoms,
                'top_condition_code' => $result['topResult']['conditionCode'] ?? null,
                'top_condition_name' => $result['topResult']['conditionName'] ?? null,
                'top_cf_score' => $result['topResult']['cfScore'] ?? null,
                'top_percentage' => $result['topResult']['percentage'] ?? null,
                'is_indication_sufficient' => $result['isIndicationSufficient'],
                'has_safety_alert' => $result['hasSafetyAlert'],
                'full_results' => $result,
            ]);
            $consultationId = $consultation->id;
        } catch (\Throwable $e) {
            $consultationId = $result['id'];
        }

        $request->session()->put('consultation_' . $consultationId, $result);

        return redirect()->route('hasil.show', $consultationId);
    }

    /**
     * Menampilkan hasil skrining dan transparansi rumus CF
     */
    public function show(Request $request, string $id): View
    {
        try {
            $consultation = Consultation::findOrFail($id);
            $result = $consultation->full_results;
        } catch (\Throwable $e) {
            $cached = $request->session()->get('consultation_' . $id);
            if (!$cached) {
                abort(404, 'Data konsultasi tidak ditemukan.');
            }
            $consultation = (object) [
                'id' => $cached['id'],
                'patient_name' => $cached['patient']['name'],
                'patient_age' => $cached['patient']['age'],
                'patient_gender' => $cached['patient']['gender'],
                'has_safety_alert' => $cached['hasSafetyAlert'],
                'created_at' => $cached['timestamp'],
                'full_results' => $cached,
            ];
            $result = $cached;
        }

        return view('pages.result', compact('consultation', 'result'));
    }
}
