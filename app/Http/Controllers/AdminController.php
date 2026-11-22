<?php

namespace App\Http\Controllers;

use App\Models\Symptom;
use App\Models\Rule;
use App\Models\Condition;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Controller untuk Portal Pakar (Admin Minimal)
 * Fitur: Login Session, Tabel Gejala (CRUD), Tabel Aturan CF dengan Inline Edit
 */
class AdminController extends Controller
{
    /**
     * Menampilkan form login admin
     */
    public function loginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('admin.index');
        }

        return view('admin.login');
    }

    /**
     * Memproses autentikasi sesi login admin
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Autentikasi sesi murni tanpa parameter remember
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $request->session()->put('admin_last_activity', now()->timestamp);
            return redirect()->intended(route('admin.index'))->with('success', 'Selamat datang di Portal Pakar.');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi tidak cocok.',
        ])->onlyInput('email');
    }

    /**
     * Logout admin dan bersihkan sesi
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        if ($recaller = Auth::getRecallerName()) {
            \Illuminate\Support\Facades\Cookie::queue(\Illuminate\Support\Facades\Cookie::forget($recaller));
        }

        $request->session()->forget('admin_last_activity');
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah keluar dari sesi admin.');
    }

    /**
     * Halaman Utama Portal Admin Minimal (Tabel Gejala & Tabel Aturan CF)
     */
    public function index(): View
    {
        try {
            $symptoms = Symptom::orderBy('code')->get();
            $rules = Rule::with(['condition', 'symptom'])->orderBy('id')->get();
            $conditions = Condition::orderBy('code')->get();
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
            $rules = collect($kb['rules'] ?? [])->map(fn($r) => (object)[
                'id' => $r['id'],
                'condition_code' => $r['conditionCode'],
                'symptom_code' => $r['symptomCode'],
                'cf_pakar' => $r['cfPakar'],
                'source_literature' => $r['sourceLiterature'] ?? '',
                'condition' => (object)['name' => $r['conditionCode']],
                'symptom' => (object)['text' => $r['symptomCode']],
            ]);
            $conditions = collect($kb['conditions'] ?? [])->map(fn($c) => (object)[
                'code' => $c['code'],
                'name' => $c['name'],
                'original_name' => $c['originalName'],
                'description' => $c['description'],
                'general_advice' => $c['generalAdvice'] ?? [],
                'seeking_help_advice' => $c['seekingHelpAdvice'] ?? '',
                'dataset_insights' => $c['datasetInsights'] ?? null,
            ]);
        }

        return view('admin.index', compact('symptoms', 'rules', 'conditions'));
    }

    /**
     * Tambah gejala baru (CRUD Gejala)
     */
    public function storeSymptom(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:symptoms,code',
            'text' => 'required|string',
            'category' => 'required|string|max:50',
            'clinical_explanation' => 'nullable|string',
            'is_safety_trigger' => 'nullable|boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_safety_trigger'] = $request->boolean('is_safety_trigger');

        Symptom::create($validated);

        return back()->with('success', "Gejala {$validated['code']} berhasil ditambahkan.");
    }

    /**
     * Perbarui data gejala (CRUD Gejala)
     */
    public function updateSymptom(Request $request, string $code): RedirectResponse
    {
        $symptom = Symptom::findOrFail($code);

        $validated = $request->validate([
            'text' => 'required|string',
            'category' => 'required|string|max:50',
            'clinical_explanation' => 'nullable|string',
            'is_safety_trigger' => 'nullable|boolean',
        ]);

        $validated['is_safety_trigger'] = $request->boolean('is_safety_trigger');
        $symptom->update($validated);

        return back()->with('success', "Gejala {$code} berhasil diperbarui.");
    }

    /**
     * Hapus gejala (CRUD Gejala)
     */
    public function destroySymptom(Request $request, string $code): RedirectResponse
    {
        $symptom = Symptom::findOrFail($code);
        $usedInRules = Rule::where('symptom_code', $code)->count();

        if ($usedInRules > 0 && !$request->boolean('confirm_delete')) {
            return back()->with('error', "Gejala {$code} masih digunakan oleh {$usedInRules} aturan pakar. Harap konfirmasi penghapusan.");
        }

        Rule::where('symptom_code', $code)->delete();
        $symptom->delete();

        return back()->with('success', "Gejala {$code} dan relasi aturannya berhasil dihapus.");
    }

    /**
     * Edit inline nilai CF Pakar pada aturan (CRUD Aturan)
     */
    public function updateRule(Request $request, string $id): RedirectResponse
    {
        $rule = Rule::findOrFail($id);

        $validated = $request->validate([
            'cf_pakar' => 'required|numeric|min:0.0|max:1.0',
            'source_literature' => 'nullable|string',
        ]);

        $rule->update($validated);

        return back()->with('success', "Bobot CF aturan {$id} berhasil diperbarui menjadi {$validated['cf_pakar']}.");
    }

    /**
     * Perbarui deskripsi dan saran umum kondisi (Kondisi)
     */
    public function updateCondition(Request $request, string $code): RedirectResponse
    {
        $condition = Condition::findOrFail($code);

        $validated = $request->validate([
            'description' => 'required|string',
            'seeking_help_advice' => 'nullable|string',
            'general_advice' => 'nullable|string',
        ]);

        $adviceArray = array_filter(array_map('trim', explode("\n", $request->input('general_advice', ''))));

        $condition->update([
            'description' => $validated['description'],
            'seeking_help_advice' => $validated['seeking_help_advice'] ?? '',
            'general_advice' => count($adviceArray) > 0 ? array_values($adviceArray) : $condition->general_advice,
        ]);

        return back()->with('success', "Data kondisi {$code} ({$condition->name}) berhasil diperbarui.");
    }
}
