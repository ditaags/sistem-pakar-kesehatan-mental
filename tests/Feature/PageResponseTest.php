<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Condition;
use App\Models\Symptom;
use App\Models\Rule;
use App\Models\Consultation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PageResponseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed 4 kondisi
        Condition::create([
            'code' => 'K01',
            'name' => 'Depresi Mayor',
            'original_name' => 'Major Depressive Disorder',
            'description' => 'Kondisi mood rendah persisten.',
            'general_advice' => ['Tidur teratur', 'Aktivitas fisik ringan'],
            'seeking_help_advice' => 'Hubungi profesional jika berlanjut.',
            'dataset_insights' => ['commonTherapy' => 'CBT'],
        ]);

        Condition::create([
            'code' => 'K02',
            'name' => 'Gangguan Kecemasan Umum',
            'original_name' => 'Generalized Anxiety',
            'description' => 'Kecemasan berlebihan.',
            'general_advice' => ['Relaksasi nafas'],
            'seeking_help_advice' => 'Hubungi psikolog.',
            'dataset_insights' => ['commonTherapy' => 'Mindfulness'],
        ]);

        // Seed gejala
        Symptom::create([
            'code' => 'G01',
            'text' => 'Merasa murung dan sedih mendalam',
            'category' => 'Suasana Hati',
            'clinical_explanation' => 'DSM-5 A1',
            'is_safety_trigger' => false,
        ]);

        Symptom::create([
            'code' => 'G02',
            'text' => 'Kehilangan minat atau kesenangan',
            'category' => 'Suasana Hati',
            'clinical_explanation' => 'DSM-5 A2',
            'is_safety_trigger' => false,
        ]);

        Symptom::create([
            'code' => 'G07',
            'text' => 'Kesulitan untuk tidur atau insomnia',
            'category' => 'Tidur dan Energi',
            'clinical_explanation' => 'DSM-5 A4',
            'is_safety_trigger' => false,
        ]);

        Symptom::create([
            'code' => 'G25',
            'text' => 'Pikiran menyakiti diri sendiri',
            'category' => 'Keselamatan',
            'clinical_explanation' => 'Safety Rule Kritis',
            'is_safety_trigger' => true,
        ]);

        // Seed aturan
        Rule::create([
            'id' => 'R01',
            'condition_code' => 'K01',
            'symptom_code' => 'G01',
            'cf_pakar' => 0.85,
            'source_literature' => 'DSM-5 Kriteria A1',
        ]);

        Rule::create([
            'id' => 'R02',
            'condition_code' => 'K01',
            'symptom_code' => 'G02',
            'cf_pakar' => 0.85,
            'source_literature' => 'DSM-5 Kriteria A2',
        ]);

        Rule::create([
            'id' => 'R04',
            'condition_code' => 'K01',
            'symptom_code' => 'G07',
            'cf_pakar' => 0.50,
            'source_literature' => 'DSM-5 Kriteria A4',
        ]);
    }

    public function test_home_page_renders_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('MindScreen');
        $response->assertSee('Depresi Mayor');
        $response->assertSee('K01');
    }

    public function test_consultation_page_renders_successfully(): void
    {
        $response = $this->get('/konsultasi');
        $response->assertStatus(200);
        $response->assertSee('Konsultasi Skrining');
        $response->assertSee('G01');
        $response->assertSee('Merasa murung dan sedih mendalam');
        // Verifikasi segmented radio buttons di bawah teks gejala
        $response->assertSee('type="radio"', false);
        $response->assertSee('name="symptoms[G01]"', false);
        $response->assertSee('value="0"', false);
        $response->assertSee('value="0.2"', false);
        $response->assertSee('value="0.4"', false);
        $response->assertSee('value="0.6"', false);
        $response->assertSee('value="0.8"', false);
        $response->assertSee('value="1.0"', false);
        $response->assertSee('peer-checked:bg-teal-800', false);
        $response->assertSee('scroll-mt-48', false);
        $response->assertSee('<fieldset', false);
        $response->assertSee('<legend', false);
        $response->assertSee('Tentang kamu');
        $response->assertSee('Reset pilihan');
        $response->assertSee('Batal');
        $response->assertSee('Kirim');
        $response->assertSee('Pilih minimal 3 gejala');
    }

    public function test_about_page_renders_successfully(): void
    {
        $response = $this->get('/tentang-kami');
        $response->assertStatus(200);
        $response->assertSee('Metode Certainty Factor');
        $response->assertSee('Matriks Konfusi');
    }

    public function test_admin_guest_redirected_to_login(): void
    {
        $response = $this->get('/kelola-pakar');
        $response->assertRedirect('/kelola-pakar/login');
    }

    public function test_admin_login_page_renders_successfully(): void
    {
        $response = $this->get('/kelola-pakar/login');
        $response->assertStatus(200);
        $response->assertSee('Portal Pakar Admin');
        $response->assertSee('AUTENTIKASI SESI');
        $response->assertSee('Masuk ke Portal');
        $response->assertSee('Kembali ke Beranda');
        $response->assertSee('name="email"', false);
        $response->assertSee('name="password"', false);
        $response->assertDontSee('name="remember"', false);
        $response->assertDontSee('Ingat sesi saya');
        $response->assertSee('id="admin-email"', false);
        $response->assertSee('id="admin-password"', false);
        $response->assertSee('btn-autofill');
        $response->assertDontSee('[object');
        $response->assertDontSee('[object HTMLInputElement]');
    }

    public function test_admin_login_with_valid_credentials_succeeds(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@mindscreen.expert',
            'password' => bcrypt('adminpakar2026'),
        ]);

        $response = $this->post('/kelola-pakar/login', [
            'email' => 'admin@mindscreen.expert',
            'password' => 'adminpakar2026',
        ]);

        $response->assertRedirect('/kelola-pakar');
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_navigating_to_public_page_is_automatically_logged_out(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@mindscreen.expert',
            'password' => bcrypt('adminpakar2026'),
        ]);

        // Login terlebih dahulu
        $this->post('/kelola-pakar/login', [
            'email' => 'admin@mindscreen.expert',
            'password' => 'adminpakar2026',
        ]);
        $this->assertAuthenticatedAs($admin);

        // Kunjungi halaman publik (Beranda)
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);

        // Pastikan otomatis logout
        $this->assertGuest();

        // Saat buka /kelola-pakar lagi, diarahkan ke login
        $portalResponse = $this->get('/kelola-pakar');
        $portalResponse->assertRedirect('/kelola-pakar/login');
    }

    public function test_admin_area_has_no_store_cache_headers(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@mindscreen.expert',
        ]);

        $response = $this->actingAs($admin)->get('/kelola-pakar');
        $response->assertStatus(200);

        // Verifikasi header anti-cache no-store
        $cacheControl = $response->headers->get('Cache-Control');
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('no-cache', $cacheControl);
        $this->assertStringContainsString('must-revalidate', $cacheControl);
        $this->assertEquals('no-cache', $response->headers->get('Pragma'));
        $this->assertEquals('0', $response->headers->get('Expires'));
    }

    public function test_admin_idle_timeout_logs_out_after_15_minutes(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@mindscreen.expert',
        ]);

        // Set session admin_last_activity 16 menit yang lalu
        $response = $this->actingAs($admin)
            ->withSession(['admin_last_activity' => now()->subMinutes(16)->timestamp])
            ->get('/kelola-pakar');

        $response->assertRedirect('/kelola-pakar/login');
        $response->assertSessionHas('error', 'Sesi berakhir, silakan masuk lagi.');
        $this->assertGuest();
    }

    public function test_prefetch_and_ajax_do_not_trigger_admin_logout_on_public_pages(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@mindscreen.expert',
        ]);

        // Request prefetch
        $this->actingAs($admin)
            ->get('/', ['Purpose' => 'prefetch']);
        $this->assertAuthenticatedAs($admin);

        // Request AJAX / X-Requested-With
        $this->actingAs($admin)
            ->get('/', ['X-Requested-With' => 'XMLHttpRequest']);
        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_can_logout_via_post_logout(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@mindscreen.expert',
        ]);

        $response = $this->actingAs($admin)->post('/logout');
        $response->assertRedirect('/kelola-pakar/login');
        $this->assertGuest();
    }

    public function test_admin_login_with_invalid_credentials_fails(): void
    {
        User::factory()->create([
            'email' => 'admin@mindscreen.expert',
            'password' => bcrypt('adminpakar2026'),
        ]);

        $response = $this->post('/kelola-pakar/login', [
            'email' => 'admin@mindscreen.expert',
            'password' => 'passwordsalah123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_login_with_empty_fields_fails(): void
    {
        $response = $this->post('/kelola-pakar/login', [
            'email' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }

    public function test_admin_login_default_credentials_block_hidden_in_production(): void
    {
        app()->detectEnvironment(fn() => 'production');

        $response = $this->get('/kelola-pakar/login');
        $response->assertStatus(200);
        $response->assertDontSee('Akun Default (Seeder)');
        $response->assertDontSee('id="btn-autofill"', false);

        // Reset back to testing
        app()->detectEnvironment(fn() => 'testing');
    }

    public function test_authenticated_admin_can_access_portal(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@mindscreen.expert',
        ]);

        $response = $this->actingAs($admin)->get('/kelola-pakar');
        $response->assertStatus(200);
        $response->assertSee('Pengelolaan Basis Pengetahuan Pakar');
        $response->assertSee('Aturan CF Pakar');
    }

    public function test_consultation_submission_and_result_page(): void
    {
        $payload = [
            'patient_name' => 'Rina',
            'patient_age' => 22,
            'patient_gender' => 'Female',
            'symptoms' => [
                'G01' => 0.8,
                'G02' => 0.8,
                'G07' => 0.6,
                'G25' => 0.4,
            ],
        ];

        $postResponse = $this->post('/konsultasi', $payload);
        $postResponse->assertSessionHasNoErrors();
        $this->assertDatabaseCount('consultations', 1);

        $consultation = Consultation::first();
        $this->assertNotNull($consultation);
        $this->assertTrue($consultation->has_safety_alert);

        $resultResponse = $this->get("/hasil/{$consultation->id}");
        $resultResponse->assertStatus(200);
        $resultResponse->assertSee('Laporan Hasil Skrining');
        $resultResponse->assertSee('Depresi Mayor');
        $resultResponse->assertSee('PERHATIAN KESELAMATAN MENDESAK');
        $resultResponse->assertSee('Hotline 119');
        // Verifikasi tombol ikon mata pada akordion transparansi
        $resultResponse->assertSee('Tampilkan rincian perhitungan');
        $resultResponse->assertSee('Sembunyikan rincian perhitungan');
        $resultResponse->assertSee('stroke-width="1.75"', false);
        $resultResponse->assertSee('w-9 h-9', false);
        $resultResponse->assertDontSee('[- Sembunyikan]');
        $resultResponse->assertDontSee('[+ Tampilkan]');
    }
}
