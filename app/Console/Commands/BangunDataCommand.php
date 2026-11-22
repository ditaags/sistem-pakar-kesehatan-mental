<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Models\Condition;
use App\Models\Symptom;
use App\Models\Rule;
use App\Services\CertaintyFactorService;

/**
 * Artisan Command: php artisan pakar:bangun-data
 * Membaca data CSV dan menyusun data/knowledge_base.json serta menyelaraskan database MySQL.
 */
class BangunDataCommand extends Command
{
    protected $signature = 'pakar:bangun-data';
    protected $description = 'Membangun basis pengetahuan dari dataset CSV ke knowledge_base.json dan database';

    public function handle(): int
    {
        $csvPath = base_path('data/mental_health_diagnosis_treatment_.csv');
        if (!File::exists($csvPath)) {
            $this->error("Dataset CSV tidak ditemukan di: {$csvPath}");
            return 1;
        }

        $this->info("Membaca CSV dataset dari: {$csvPath}");
        $content = File::get($csvPath);
        $lines = explode("\n", trim($content));

        // 1. Hitung Statistik Dataset
        $totalRecords = count($lines) - 1;
        $distribution = [];
        $perDiagStats = [];

        for ($i = 1; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if (empty($line)) continue;
            $parts = str_getcsv($line);
            if (count($parts) < 17) continue;

            $diag = trim($parts[3]);
            $distribution[$diag] = ($distribution[$diag] ?? 0) + 1;

            if (!isset($perDiagStats[$diag])) {
                $perDiagStats[$diag] = [
                    'count' => 0,
                    'moodSum' => 0,
                    'sleepSum' => 0,
                    'stressSum' => 0,
                    'sevSum' => 0,
                    'therapies' => [],
                    'meds' => [],
                ];
            }

            $st = &$perDiagStats[$diag];
            $st['count']++;
            $st['moodSum'] += (float) $parts[5];
            $st['sleepSum'] += (float) $parts[6];
            $st['stressSum'] += (float) $parts[12];
            $st['sevSum'] += (float) $parts[4];

            $therapy = trim($parts[9]);
            $med = trim($parts[8]);
            $st['therapies'][$therapy] = ($st['therapies'][$therapy] ?? 0) + 1;
            $st['meds'][$med] = ($st['meds'][$med] ?? 0) + 1;
        }

        $getTopItem = function (array $map): string {
            arsort($map);
            return array_key_first($map) ?? 'N/A';
        };

        // 2. Definisi 4 Kondisi (K01 - K04)
        $conditions = [
            [
                'code' => 'K01',
                'name' => 'Depresi Mayor',
                'originalName' => 'Major Depressive Disorder',
                'description' => 'Kondisi suasana hati persisten yang ditandai dengan rasa sedih mendalam, kehilangan minat atau kegembiraan secara menyeluruh (anhedonia), penurunan energi, rasa tidak berharga, dan keputusasaan yang berlangsung minimal 2 minggu.',
                'generalAdvice' => [
                    'Pertahankan jadwal tidur dan bangun yang konsisten setiap hari.',
                    'Lakukan aktivitas fisik ringan seperti jalan kaki santai 20-30 menit.',
                    'Tetap terhubung dengan orang terdekat atau kelompok pendukung, hindari mengisolasi diri.',
                    'Pecah tugas besar menjadi langkah-langkah kecil yang mudah dikerjakan.',
                ],
                'seekingHelpAdvice' => 'Jika rasa sedih, hampa, atau putus asa berlangsung lebih dari 2 minggu berturut-turut dan mengganggu fungsi harian atau pekerjaan, sangat disarankan berkonsultasi dengan psikolog klinis atau psikiater.',
                'datasetInsights' => [
                    'avgMood' => round($perDiagStats['Major Depressive Disorder']['moodSum'] / $perDiagStats['Major Depressive Disorder']['count'], 2),
                    'avgSleep' => round($perDiagStats['Major Depressive Disorder']['sleepSum'] / $perDiagStats['Major Depressive Disorder']['count'], 2),
                    'avgStress' => round($perDiagStats['Major Depressive Disorder']['stressSum'] / $perDiagStats['Major Depressive Disorder']['count'], 2),
                    'commonTherapy' => $getTopItem($perDiagStats['Major Depressive Disorder']['therapies']),
                    'commonMedication' => $getTopItem($perDiagStats['Major Depressive Disorder']['meds']),
                ],
            ],
            [
                'code' => 'K02',
                'name' => 'Gangguan Kecemasan Umum',
                'originalName' => 'Generalized Anxiety',
                'description' => 'Kondisi kecemasan dan kekhawatiran berlebihan yang menetap dan sulit dikendalikan mengenai berbagai peristiwa atau aktivitas sehari-hari, sering kali disertai ketegangan otot, kegelisahan, dan rasa lekas lelah.',
                'generalAdvice' => [
                    'Praktikkan latihan pernapasan diafragma atau relaksasi otot progresif saat rasa cemas meningkat.',
                    'Batasi konsumsi stimulan seperti kafein, minuman berenergi, dan nikotin yang dapat memicu detak jantung.',
                    'Terapkan batasan waktu khusus untuk memikirkan kekhawatiran (worry time) maksimal 15 menit per hari.',
                    'Jadwalkan jeda istirahat teratur dari beban pekerjaan atau konsumsi berita negatif.',
                ],
                'seekingHelpAdvice' => 'Jika kekhawatiran terasa tidak terkendali, membuat tubuh terus-menerus tegang, atau mengganggu konsentrasi selama berbulan-bulan, konsultasikan ke tenaga profesional kesehatan mental.',
                'datasetInsights' => [
                    'avgMood' => round($perDiagStats['Generalized Anxiety']['moodSum'] / $perDiagStats['Generalized Anxiety']['count'], 2),
                    'avgSleep' => round($perDiagStats['Generalized Anxiety']['sleepSum'] / $perDiagStats['Generalized Anxiety']['count'], 2),
                    'avgStress' => round($perDiagStats['Generalized Anxiety']['stressSum'] / $perDiagStats['Generalized Anxiety']['count'], 2),
                    'commonTherapy' => $getTopItem($perDiagStats['Generalized Anxiety']['therapies']),
                    'commonMedication' => $getTopItem($perDiagStats['Generalized Anxiety']['meds']),
                ],
            ],
            [
                'code' => 'K03',
                'name' => 'Gangguan Bipolar',
                'originalName' => 'Bipolar Disorder',
                'description' => 'Kondisi kesehatan mental yang ditandai oleh fluktuasi suasana hati ekstrem antara episode manik/hipomanik (energi meluap-luap, percaya diri berlebihan, kebutuhan tidur berkurang drastis, impulsif) dan episode depresi (murung, hampa, tidak berenergi).',
                'generalAdvice' => [
                    'Sangat penting menjaga rutinitas sirkadian (siklus bangun-tidur) yang teratur dan ketat.',
                    'Catat jurnal harian suasana hati untuk mengenali tanda-tanda awal pergeseran fase manik atau depresi.',
                    'Hindari mengambil keputusan finansial atau komitmen besar saat merasa sedang dalam fase energi sangat tinggi.',
                    'Bangun jejaring orang terpercaya yang dapat mengingatkan ketika perilaku mulai tampak tidak biasa.',
                ],
                'seekingHelpAdvice' => 'Gangguan bipolar memerlukan diagnosis diferensial medis dan penanganan profesional jangka panjang. Segera jadwalkan evaluasi komprehensif ke psikiater.',
                'datasetInsights' => [
                    'avgMood' => round($perDiagStats['Bipolar Disorder']['moodSum'] / $perDiagStats['Bipolar Disorder']['count'], 2),
                    'avgSleep' => round($perDiagStats['Bipolar Disorder']['sleepSum'] / $perDiagStats['Bipolar Disorder']['count'], 2),
                    'avgStress' => round($perDiagStats['Bipolar Disorder']['stressSum'] / $perDiagStats['Bipolar Disorder']['count'], 2),
                    'commonTherapy' => $getTopItem($perDiagStats['Bipolar Disorder']['therapies']),
                    'commonMedication' => $getTopItem($perDiagStats['Bipolar Disorder']['meds']),
                ],
            ],
            [
                'code' => 'K04',
                'name' => 'Gangguan Panik',
                'originalName' => 'Panic Disorder',
                'description' => 'Kondisi yang ditandai dengan serangan rasa takut hebat yang datang mendadak tanpa sebab jelas, memuncak dalam beberapa menit disertai gejala fisik intens (jantung berdebar keras, sesak napas, pusing), dan kekhawatiran persisten akan serangan berikutnya.',
                'generalAdvice' => [
                    'Ketahui dan yakinkan diri bahwa serangan panik tidak mematikan dan biasanya mereda dalam 10-20 menit.',
                    'Gunakan teknik grounding panca indera 5-4-3-2-1 untuk mengembalikan fokus ke saat ini.',
                    'Tarik napas perlahan melalui hidung (4 detik) dan hembuskan perlahan lewat mulut (6 detik).',
                    'Hindari pelarian total dari situasi yang memicu rasa takut agar tidak berkembang menjadi agorafobia.',
                ],
                'seekingHelpAdvice' => 'Jika Anda berulang kali mengalami serangan panik mendadak dan mulai membatasi bepergian karena takut serangan berulang, carilah bantuan psikoterapi spesialis kecemasan.',
                'datasetInsights' => [
                    'avgMood' => round($perDiagStats['Panic Disorder']['moodSum'] / $perDiagStats['Panic Disorder']['count'], 2),
                    'avgSleep' => round($perDiagStats['Panic Disorder']['sleepSum'] / $perDiagStats['Panic Disorder']['count'], 2),
                    'avgStress' => round($perDiagStats['Panic Disorder']['stressSum'] / $perDiagStats['Panic Disorder']['count'], 2),
                    'commonTherapy' => $getTopItem($perDiagStats['Panic Disorder']['therapies']),
                    'commonMedication' => $getTopItem($perDiagStats['Panic Disorder']['meds']),
                ],
            ],
        ];

        // 3. Definisi 25 Gejala Klinis (G01 - G25)
        $symptoms = [
            ['code' => 'G01', 'text' => 'Merasa murung, sedih mendalam, atau hampa hampir sepanjang hari selama minimal 2 minggu', 'category' => 'Suasana Hati', 'clinicalExplanation' => 'Kriteria inti episode depresi mayor (DSM-5 Kriteria A1).', 'isSafetyTrigger' => false],
            ['code' => 'G02', 'text' => 'Kehilangan minat atau kesenangan secara nyata pada hampir semua aktivitas yang biasanya disukai', 'category' => 'Suasana Hati', 'clinicalExplanation' => 'Anhedonia klinis, ciri patognomonik depresi mayor (DSM-5 Kriteria A2).', 'isSafetyTrigger' => false],
            ['code' => 'G03', 'text' => 'Merasa diri tidak berharga atau merasa bersalah secara berlebihan dan tidak realistis', 'category' => 'Suasana Hati', 'clinicalExplanation' => 'Distorsi kognitif rasa bersalah yang tidak proporsional (DSM-5 A7).', 'isSafetyTrigger' => false],
            ['code' => 'G04', 'text' => 'Perubahan suasana hati yang sangat drastis: dari fase sangat terpuruk menjadi fase penuh energi luar biasa', 'category' => 'Suasana Hati', 'clinicalExplanation' => 'Pergeseran polaritas afektif khas gangguan bipolar siklus cepat atau bertahap.', 'isSafetyTrigger' => false],
            ['code' => 'G05', 'text' => 'Merasa sangat gembira meluap-luap (euforia), rasa percaya diri luar biasa tinggi, atau mudah tersinggung tanpa alasan jelas', 'category' => 'Suasana Hati', 'clinicalExplanation' => 'Gejala utama episode mania atau hipomania (DSM-5 Kriteria Bipolar A).', 'isSafetyTrigger' => false],
            ['code' => 'G06', 'text' => 'Perasaan gelisah, mudah tegang, dan tidak tenang yang menetap hampir setiap hari', 'category' => 'Suasana Hati', 'clinicalExplanation' => 'Ketegangan emosional persisten pada generalized anxiety.', 'isSafetyTrigger' => false],

            ['code' => 'G07', 'text' => 'Kesulitan untuk tidur (insomnia), sering terbangun di tengah malam, atau justru tidur berlebihan', 'category' => 'Tidur dan Energi', 'clinicalExplanation' => 'Gangguan arsitektur tidur yang umum pada depresi dan gangguan kecemasan.', 'isSafetyTrigger' => false],
            ['code' => 'G08', 'text' => 'Hanya tidur 2-3 jam semalam namun tetap merasa bugar, segar, dan berenergi melimpah', 'category' => 'Tidur dan Energi', 'clinicalExplanation' => 'Penurunan kebutuhan tidur yang tidak menimbulkan kelelahan, sangat spesifik episode mania (DSM-5 B2).', 'isSafetyTrigger' => false],
            ['code' => 'G09', 'text' => 'Kelelahan ekstrem atau kehilangan energi hampir setiap hari meski tidak beraktivitas berat', 'category' => 'Tidur dan Energi', 'clinicalExplanation' => 'Anergia fisik dan psikologis persisten (DSM-5 A6).', 'isSafetyTrigger' => false],
            ['code' => 'G10', 'text' => 'Penurunan nafsu makan drastis atau makan berlebihan tanpa terkendali', 'category' => 'Tidur dan Energi', 'clinicalExplanation' => 'Perubahan nafsu makan metabolik akibat disregulasi neurokimia afektif.', 'isSafetyTrigger' => false],

            ['code' => 'G11', 'text' => 'Rasa khawatir dan cemas yang berlebihan dan sangat sulit dikendalikan mengenai berbagai hal sehari-hari', 'category' => 'Kecemasan dan Panik', 'clinicalExplanation' => 'Kriteria kardinal Gangguan Kecemasan Umum (GAD, DSM-5 A & B).', 'isSafetyTrigger' => false],
            ['code' => 'G12', 'text' => 'Ketegangan otot pada tubuh (misalnya leher kaku, rahang merapat erat, pundak pegal terus-menerus)', 'category' => 'Kecemasan dan Panik', 'clinicalExplanation' => 'Manifestasi somatik ketegangan motorik kronis (DSM-5 GAD C6).', 'isSafetyTrigger' => false],
            ['code' => 'G13', 'text' => 'Mudah terkejut, merasa serba was-was, atau seperti berada di ambang bahaya', 'category' => 'Kecemasan dan Panik', 'clinicalExplanation' => 'Hiperarousal sistem saraf simpatis otonom.', 'isSafetyTrigger' => false],
            ['code' => 'G14', 'text' => 'Serangan rasa takut atau cemas intens mendadak yang memuncak dalam hitungan menit tanpa pemicu nyata', 'category' => 'Kecemasan dan Panik', 'clinicalExplanation' => 'Kriteria primer serangan panik tak terduga (DSM-5 Panic Attack Kriteria A).', 'isSafetyTrigger' => false],
            ['code' => 'G15', 'text' => 'Jantung berdebar keras (palpitasi), dada sesak, atau rasa tercekik saat serangan kecemasan', 'category' => 'Kecemasan dan Panik', 'clinicalExplanation' => 'Gejala kardiovaskular dan pernapasan otonom saat serangan panik.', 'isSafetyTrigger' => false],
            ['code' => 'G16', 'text' => 'Pusing, limbung, sensasi melayang, atau gemetar hebat disertai keringat dingin', 'category' => 'Kecemasan dan Panik', 'clinicalExplanation' => 'Respons vasovagal dan aktivasi simpatis akut pada episode panik.', 'isSafetyTrigger' => false],
            ['code' => 'G17', 'text' => 'Ketakutan hebat bahwa diri sedang kehilangan kendali, akan menjadi gila, atau akan segera meninggal', 'category' => 'Kecemasan dan Panik', 'clinicalExplanation' => 'Ketakutan katastrofik kognitif saat puncak episode panik (DSM-5 Panic A11-12).', 'isSafetyTrigger' => false],
            ['code' => 'G18', 'text' => 'Kekhawatiran terus-menerus selama minimal sebulan akan kemungkinan terjadinya serangan panik berikutnya', 'category' => 'Kecemasan dan Panik', 'clinicalExplanation' => 'Anticipatory anxiety yang menjadi kriteria penegak Gangguan Panik (DSM-5 Kriteria B1).', 'isSafetyTrigger' => false],

            ['code' => 'G19', 'text' => 'Sulit berkonsentrasi, pikiran mudah buyar, atau kesulitan membuat keputusan sehari-hari', 'category' => 'Perilaku dan Pikiran', 'clinicalExplanation' => 'Gangguan fungsi eksekutif kognitif pada depresi dan kecemasan.', 'isSafetyTrigger' => false],
            ['code' => 'G20', 'text' => 'Bicara jauh lebih cepat dari biasanya, melompat-lompat topik pikiran, dan sulit disela orang lain', 'category' => 'Perilaku dan Pikiran', 'clinicalExplanation' => 'Flight of ideas dan pressured speech khas fase mania bipolar (DSM-5 B3-B4).', 'isSafetyTrigger' => false],
            ['code' => 'G21', 'text' => 'Keterlibatan berlebihan dalam aktivitas berisiko tinggi tanpa memikirkan konsekuensi (misal belanja berlebihan, investasi gegabah)', 'category' => 'Perilaku dan Pikiran', 'clinicalExplanation' => 'Impulsivitas dengan potensi konsekuensi merugikan pada episode mania (DSM-5 B7).', 'isSafetyTrigger' => false],
            ['code' => 'G22', 'text' => 'Menghindari tempat keramaian, transportasi umum, atau ruang terbuka karena takut panik melanda', 'category' => 'Perilaku dan Pikiran', 'clinicalExplanation' => 'Perilaku penghindaran maladaptif / agorafobia sekunder pada gangguan panik.', 'isSafetyTrigger' => false],
            ['code' => 'G23', 'text' => 'Gerakan tubuh dan proses berpikir terasa melambat drastis (atau sebaliknya sangat gelisah tidak bisa diam)', 'category' => 'Perilaku dan Pikiran', 'clinicalExplanation' => 'Retardasi atau agitasi psikomotor terobservasi (DSM-5 A5).', 'isSafetyTrigger' => false],

            ['code' => 'G24', 'text' => 'Tingkat stres sehari-hari sangat tinggi (sering merasa kewalahan dengan beban hidup dan jarang beraktivitas fisik)', 'category' => 'Indikator Fisik & Stres', 'clinicalExplanation' => 'Kombinasi stres tinggi (>=8) dan sedentari dari pola dataset numerik.', 'isSafetyTrigger' => false],
            ['code' => 'G25', 'text' => 'Pernah terpikir berulang tentang kematian, merasa lebih baik tiada, atau dorongan menyakiti diri sendiri', 'category' => 'Keselamatan', 'clinicalExplanation' => 'ATURAN KESELAMATAN KRITIS: Tidak bergantung pada CF, wajib menampilkan kontak darurat 119.', 'isSafetyTrigger' => true],
        ];

        // 4. Basis Aturan Relasi (32 Aturan CF)
        $rules = [
            ['id' => 'R01', 'conditionCode' => 'K01', 'symptomCode' => 'G01', 'cfPakar' => 0.85, 'sourceLiterature' => 'DSM-5 Kriteria A1 (Gejala Inti Depresi)'],
            ['id' => 'R02', 'conditionCode' => 'K01', 'symptomCode' => 'G02', 'cfPakar' => 0.85, 'sourceLiterature' => 'DSM-5 Kriteria A2 (Anhedonia Inti)'],
            ['id' => 'R03', 'conditionCode' => 'K01', 'symptomCode' => 'G03', 'cfPakar' => 0.70, 'sourceLiterature' => 'DSM-5 Kriteria A7 (Rasa Tidak Berharga)'],
            ['id' => 'R04', 'conditionCode' => 'K01', 'symptomCode' => 'G07', 'cfPakar' => 0.50, 'sourceLiterature' => 'DSM-5 Kriteria A4 (Insomnia/Hipersomnia)'],
            ['id' => 'R05', 'conditionCode' => 'K01', 'symptomCode' => 'G09', 'cfPakar' => 0.65, 'sourceLiterature' => 'DSM-5 Kriteria A6 (Anergia/Kelelahan)'],
            ['id' => 'R06', 'conditionCode' => 'K01', 'symptomCode' => 'G10', 'cfPakar' => 0.50, 'sourceLiterature' => 'DSM-5 Kriteria A3 (Nafsu Makan)'],
            ['id' => 'R07', 'conditionCode' => 'K01', 'symptomCode' => 'G19', 'cfPakar' => 0.50, 'sourceLiterature' => 'DSM-5 Kriteria A8 (Konsentrasi)'],
            ['id' => 'R08', 'conditionCode' => 'K01', 'symptomCode' => 'G23', 'cfPakar' => 0.60, 'sourceLiterature' => 'DSM-5 Kriteria A5 (Retardasi Psikomotor)'],
            ['id' => 'R09', 'conditionCode' => 'K01', 'symptomCode' => 'G24', 'cfPakar' => 0.25, 'sourceLiterature' => 'Indikator Stres Tambahan Dataset'],

            ['id' => 'R10', 'conditionCode' => 'K02', 'symptomCode' => 'G06', 'cfPakar' => 0.70, 'sourceLiterature' => 'DSM-5 GAD Kriteria A (Ketegangan Menetap)'],
            ['id' => 'R11', 'conditionCode' => 'K02', 'symptomCode' => 'G07', 'cfPakar' => 0.40, 'sourceLiterature' => 'DSM-5 GAD C5 (Gangguan Tidur Akibat Cemas)'],
            ['id' => 'R12', 'conditionCode' => 'K02', 'symptomCode' => 'G09', 'cfPakar' => 0.45, 'sourceLiterature' => 'DSM-5 GAD C2 (Mudah Lelah)'],
            ['id' => 'R13', 'conditionCode' => 'K02', 'symptomCode' => 'G11', 'cfPakar' => 0.85, 'sourceLiterature' => 'DSM-5 GAD Kriteria Inti B (Kekhawatiran Berlebih)'],
            ['id' => 'R14', 'conditionCode' => 'K02', 'symptomCode' => 'G12', 'cfPakar' => 0.65, 'sourceLiterature' => 'DSM-5 GAD C6 (Ketegangan Otot Motorik)'],
            ['id' => 'R15', 'conditionCode' => 'K02', 'symptomCode' => 'G13', 'cfPakar' => 0.65, 'sourceLiterature' => 'DSM-5 GAD C1 (Gelisah / On Edge)'],
            ['id' => 'R16', 'conditionCode' => 'K02', 'symptomCode' => 'G15', 'cfPakar' => 0.40, 'sourceLiterature' => 'Gejala Otonom Cemas Ringan'],
            ['id' => 'R17', 'conditionCode' => 'K02', 'symptomCode' => 'G19', 'cfPakar' => 0.50, 'sourceLiterature' => 'DSM-5 GAD C3 (Pikiran Menjadi Kosong)'],
            ['id' => 'R18', 'conditionCode' => 'K02', 'symptomCode' => 'G24', 'cfPakar' => 0.30, 'sourceLiterature' => 'Indikator Stres Tambahan Dataset'],

            ['id' => 'R19', 'conditionCode' => 'K03', 'symptomCode' => 'G01', 'cfPakar' => 0.50, 'sourceLiterature' => 'Episode Depresif dalam Siklus Bipolar'],
            ['id' => 'R20', 'conditionCode' => 'K03', 'symptomCode' => 'G04', 'cfPakar' => 0.85, 'sourceLiterature' => 'DSM-5 Fluktuasi Siklus Polaritas Afektif'],
            ['id' => 'R21', 'conditionCode' => 'K03', 'symptomCode' => 'G05', 'cfPakar' => 0.80, 'sourceLiterature' => 'DSM-5 Kriteria Mania A (Euforia / Iritabilitas)'],
            ['id' => 'R22', 'conditionCode' => 'K03', 'symptomCode' => 'G08', 'cfPakar' => 0.85, 'sourceLiterature' => 'DSM-5 Kriteria Mania B2 (Kebutuhan Tidur Menurun)'],
            ['id' => 'R23', 'conditionCode' => 'K03', 'symptomCode' => 'G20', 'cfPakar' => 0.80, 'sourceLiterature' => 'DSM-5 Kriteria Mania B3 (Flight of Ideas / Tekanan Bicara)'],
            ['id' => 'R24', 'conditionCode' => 'K03', 'symptomCode' => 'G21', 'cfPakar' => 0.75, 'sourceLiterature' => 'DSM-5 Kriteria Mania B7 (Impulsivitas Berisiko)'],
            ['id' => 'R25', 'conditionCode' => 'K03', 'symptomCode' => 'G24', 'cfPakar' => 0.20, 'sourceLiterature' => 'Indikator Stres Tambahan Dataset'],

            ['id' => 'R26', 'conditionCode' => 'K04', 'symptomCode' => 'G14', 'cfPakar' => 0.90, 'sourceLiterature' => 'DSM-5 Panic Kriteria A (Serangan Panik Mendadak)'],
            ['id' => 'R27', 'conditionCode' => 'K04', 'symptomCode' => 'G15', 'cfPakar' => 0.80, 'sourceLiterature' => 'DSM-5 Panic A1 & A3 (Palpitasi & Sesak Napas)'],
            ['id' => 'R28', 'conditionCode' => 'K04', 'symptomCode' => 'G16', 'cfPakar' => 0.75, 'sourceLiterature' => 'DSM-5 Panic A5 & A8 (Gemetar & Pusing/Limbung)'],
            ['id' => 'R29', 'conditionCode' => 'K04', 'symptomCode' => 'G17', 'cfPakar' => 0.85, 'sourceLiterature' => 'DSM-5 Panic A11 & A12 (Takut Mati / Hilang Kendali)'],
            ['id' => 'R30', 'conditionCode' => 'K04', 'symptomCode' => 'G18', 'cfPakar' => 0.85, 'sourceLiterature' => 'DSM-5 Panic Kriteria B1 (Anticipatory Panic)'],
            ['id' => 'R31', 'conditionCode' => 'K04', 'symptomCode' => 'G22', 'cfPakar' => 0.70, 'sourceLiterature' => 'DSM-5 Panic Kriteria B2 (Perilaku Penghindaran)'],
            ['id' => 'R32', 'conditionCode' => 'K04', 'symptomCode' => 'G24', 'cfPakar' => 0.25, 'sourceLiterature' => 'Indikator Stres Tambahan Dataset'],
        ];

        $kb = [
            'conditions' => $conditions,
            'symptoms' => $symptoms,
            'rules' => $rules,
            'confidenceScale' => CertaintyFactorService::CONFIDENCE_OPTIONS,
            'datasetMetadata' => [
                'totalRecords' => $totalRecords,
                'distribution' => $distribution,
                'numericalThresholds' => [
                    'moodLowThreshold' => 4,
                    'sleepBadThreshold' => 5,
                    'stressHighThreshold' => 8,
                    'physicalActivityLowThreshold' => 3,
                ],
            ],
        ];

        // Tulis ke data/knowledge_base.json
        $outputPath = base_path('data/knowledge_base.json');
        File::put($outputPath, json_encode($kb, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $this->info("✅ Berhasil membuat file: {$outputPath}");

        // Sinkronisasi ke Database MySQL
        $this->info('Menyelaraskan data ke MySQL...');
        foreach ($conditions as $c) {
            Condition::updateOrCreate(
                ['code' => $c['code']],
                [
                    'name' => $c['name'],
                    'original_name' => $c['originalName'],
                    'description' => $c['description'],
                    'general_advice' => $c['generalAdvice'],
                    'seeking_help_advice' => $c['seekingHelpAdvice'],
                    'dataset_insights' => $c['datasetInsights'],
                ]
            );
        }

        foreach ($symptoms as $s) {
            Symptom::updateOrCreate(
                ['code' => $s['code']],
                [
                    'text' => $s['text'],
                    'category' => $s['category'],
                    'clinical_explanation' => $s['clinicalExplanation'],
                    'is_safety_trigger' => $s['isSafetyTrigger'],
                ]
            );
        }

        foreach ($rules as $r) {
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

        $this->info('✅ Basis pengetahuan sukses dibangun: 4 Kondisi, 25 Gejala, 32 Aturan CF.');
        return 0;
    }
}
