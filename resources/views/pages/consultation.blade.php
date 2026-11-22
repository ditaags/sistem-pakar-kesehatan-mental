@extends('layouts.app')

@section('content')
<div class="max-w-[880px] mx-auto px-4 sm:px-6 py-4 sm:py-8 space-y-8 pb-32" x-data="consultationForm()">
    <!-- 1. Eyebrow + Judul + Penjelasan + Accordion Skala Keyakinan -->
    <div class="space-y-3">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-[#E3EDE6] text-[#1A3831] border border-[#DCE5E0]/70">
            <span class="w-1.5 h-1.5 rounded-full bg-[#1A3831]"></span>
            Skrining mandiri, 3-5 menit
        </div>

        <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-semibold text-[#1F2A27] tracking-tight">
            Konsultasi Skrining
        </h1>

        <p class="text-sm sm:text-base text-[#52605B] leading-relaxed max-w-2xl">
            Pilih gejala yang kamu rasakan beberapa minggu terakhir, lalu tentukan seberapa yakin kamu terhadap kondisi tersebut.
        </p>

        <!-- Accordion Kecil: "Apa maksud tingkat keyakinan?" (Tutup secara default) -->
        <div class="pt-1" x-data="{ openGuide: false }">
            <button type="button"
                    @click="openGuide = !openGuide"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-800 hover:text-teal-950 transition-colors cursor-pointer select-none py-1">
                <span>Apa maksud tingkat keyakinan?</span>
                <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="openGuide ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>

            <div x-show="openGuide"
                 x-cloak
                 x-transition
                 class="mt-2.5 bg-white border border-[#DCE5E0] rounded-2xl p-4 sm:p-5 shadow-[0_4px_20px_rgba(31,42,39,0.04)] text-xs text-[#52605B] space-y-3">
                <div class="font-semibold text-[#1F2A27] text-xs">Penjelasan Skala Keyakinan Certainty Factor:</div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                    <div class="p-2.5 rounded-xl bg-stone-100 border border-stone-200">
                        <div class="font-bold text-stone-800">Tidak (0)</div>
                        <div class="text-[11px] text-[#7E8E88] mt-0.5">Tidak pernah kurasakan</div>
                    </div>
                    <div class="p-2.5 rounded-xl bg-teal-50 border border-teal-200">
                        <div class="font-bold text-teal-900">Tidak yakin (0.2)</div>
                        <div class="text-[11px] text-[#7E8E88] mt-0.5">Sedikit sekali kurasakan</div>
                    </div>
                    <div class="p-2.5 rounded-xl bg-teal-100 border border-teal-300">
                        <div class="font-bold text-teal-950">Sedikit yakin (0.4)</div>
                        <div class="text-[11px] text-[#7E8E88] mt-0.5">Sesekali kurasakan</div>
                    </div>
                    <div class="p-2.5 rounded-xl bg-teal-500 text-white border border-teal-600">
                        <div class="font-bold">Cukup yakin (0.6)</div>
                        <div class="text-[11px] text-teal-100 mt-0.5">Lumayan sering kurasakan</div>
                    </div>
                    <div class="p-2.5 rounded-xl bg-teal-800 text-white border border-teal-900">
                        <div class="font-bold">Yakin (0.8)</div>
                        <div class="text-[11px] text-teal-100 mt-0.5">Jelas kurasakan</div>
                    </div>
                    <div class="p-2.5 rounded-xl bg-teal-950 text-white border border-black">
                        <div class="font-bold">Sangat yakin (1.0)</div>
                        <div class="text-[11px] text-teal-100 mt-0.5">Sangat kuat kurasakan</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Kesalahan dari Server Jika Validasi Gagal -->
    @if ($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-900 px-5 py-3.5 rounded-2xl text-xs sm:text-sm">
        <div class="font-semibold mb-1">Periksa kembali data Anda:</div>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('konsultasi.store') }}" method="POST" id="consultationForm" @submit="handleSubmit($event)">
        @csrf

        <!-- 2. Kartu Data Diri ("Tentang kamu") -->
        <section class="bg-white p-6 sm:p-7 rounded-[24px] border border-[#DCE5E0] space-y-5 shadow-[0_4px_20px_rgba(31,42,39,0.03)] mb-8">
            <div class="flex items-center justify-between border-b border-[#F4EFEA] pb-3.5">
                <h2 class="text-base font-semibold text-[#1F2A27]">
                    Tentang kamu
                </h2>
                <span class="text-xs text-[#7E8E88]">Opsional</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
                <!-- Nama atau Inisial -->
                <div>
                    <label for="patient_name" class="block text-xs font-semibold text-[#1F2A27] mb-1.5">
                        Nama atau Inisial
                    </label>
                    <input type="text" id="patient_name" name="patient_name" x-model="patientName"
                           class="w-full h-12 px-3.5 text-sm bg-white border border-[#DCE5E0] rounded-xl placeholder:text-[#7E8E88] focus:outline-none focus:ring-2 focus:ring-[#1A3831] focus:border-[#1A3831] transition-all"
                           placeholder="Misal: Maya atau R.A.">
                </div>

                <!-- Usia -->
                <div>
                    <label for="patient_age" class="block text-xs font-semibold text-[#1F2A27] mb-1.5">
                        Usia (Tahun)
                    </label>
                    <input type="number" id="patient_age" name="patient_age" x-model="patientAge" min="12" max="100"
                           class="w-full h-12 px-3.5 text-sm bg-white border border-[#DCE5E0] rounded-xl placeholder:text-[#7E8E88] focus:outline-none focus:ring-2 focus:ring-[#1A3831] focus:border-[#1A3831] transition-all"
                           placeholder="Misal: 21">
                </div>

                <!-- Jenis Kelamin: tidak ada yang terpilih di awal -->
                <div>
                    <label class="block text-xs font-semibold text-[#1F2A27] mb-1.5">
                        Jenis Kelamin
                    </label>
                    <x-choice-group
                        name="patient_gender"
                        model="patientGender"
                        cols="grid-cols-3"
                        shape="rounded-full"
                        id-prefix="gender"
                        :selected="old('patient_gender', '')"
                        :options="[
                            ['label' => 'Wanita', 'value' => 'Female'],
                            ['label' => 'Pria', 'value' => 'Male'],
                            ['label' => 'Lainnya', 'value' => 'Other'],
                        ]"
                    />
                </div>
            </div>

            <!-- Catatan Privasi dengan Ikon Gembok Garis -->
            <div class="pt-2 border-t border-[#F4EFEA] flex items-center gap-2 text-xs text-[#52605B]">
                <svg class="w-4 h-4 text-[#7E8E88] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                    <path d="M7 11V7a5 5 0 0110 0v4" />
                </svg>
                <span>Data konsultasi dicatat secara anonim untuk laporan skrining dan analisis riset, tanpa identitas sensitif privat.</span>
            </div>
        </section>

        <!-- 3. Bar Progress (Sticky Menempel 12px di Bawah Navbar) -->
        <div id="progressBarContainer"
             class="sticky top-[72px] sm:top-[76px] z-30 mb-8 transition-all scroll-mt-24">
            <div class="bg-white/95 backdrop-blur-md border border-[#DCE5E0] rounded-full px-5 py-2.5 sm:px-6 sm:py-3 shadow-[0_8px_24px_rgba(31,42,39,0.06)] flex items-center justify-between gap-4 h-14"
                 aria-live="polite">
                <!-- Kiri: Counter + Syarat -->
                <div class="flex items-center gap-2 text-xs sm:text-sm overflow-hidden">
                    <span class="font-semibold text-[#1F2A27] truncate"
                          x-text="countSelected() + ' dari ' + totalSymptoms + ' gejala dipilih'">
                        0 dari {{ $symptoms->count() }} gejala dipilih
                    </span>
                    <span class="text-[#DCE5E0] hidden xs:inline">&bull;</span>
                    <!-- Status minimal 3 / Centang hijau jika terpenuhi -->
                    <span x-show="countSelected() < 3" class="text-amber-800 text-xs font-medium whitespace-nowrap">
                        minimal 3
                    </span>
                    <span x-show="countSelected() >= 3" x-cloak class="text-teal-900 text-xs font-semibold whitespace-nowrap flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-teal-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Terpenuhi
                    </span>
                </div>

                <!-- Tengah: Progress Bar Tipis -->
                <div class="hidden sm:block w-32 md:w-44 bg-stone-100 rounded-full h-1.5 overflow-hidden shrink-0">
                    <div class="h-1.5 rounded-full bg-[#1A3831] transition-all duration-300"
                         style="width: 0%;"
                         :style="'width: ' + progressPercentage() + '%'"></div>
                </div>

                <!-- Kanan: Tombol Reset Pilihan -->
                <button type="button" @click="resetOptions()"
                        class="text-xs font-semibold text-[#52605B] hover:text-red-700 transition-colors underline shrink-0 cursor-pointer">
                    Reset pilihan
                </button>
            </div>

            <!-- Pesan Inline Jika Kirim Belum Memenuhi Syarat -->
            <div x-show="errorMessage"
                 x-cloak
                 x-transition
                 class="mt-2.5 text-center text-xs font-semibold text-red-800 bg-red-50 border border-red-200 rounded-full py-1.5 px-4 shadow-2xs">
                <span x-text="errorMessage"></span>
            </div>
        </div>

        @php
            $grouped = $symptoms->groupBy('category');
            $confidenceButtons = [
                [
                    'label' => 'Tidak',
                    'val' => '0',
                    'num' => '0',
                    'activeClass' => 'peer-checked:bg-stone-200 peer-checked:text-stone-900 peer-checked:border-stone-400 font-bold'
                ],
                [
                    'label' => 'Tidak yakin',
                    'val' => '0.2',
                    'num' => '0.2',
                    'activeClass' => 'peer-checked:bg-teal-100 peer-checked:text-teal-950 peer-checked:border-teal-300 font-bold'
                ],
                [
                    'label' => 'Sedikit',
                    'val' => '0.4',
                    'num' => '0.4',
                    'activeClass' => 'peer-checked:bg-teal-300 peer-checked:text-teal-950 peer-checked:border-teal-500 font-bold'
                ],
                [
                    'label' => 'Cukup',
                    'val' => '0.6',
                    'num' => '0.6',
                    'activeClass' => 'peer-checked:bg-teal-600 peer-checked:text-white peer-checked:border-teal-700 font-bold'
                ],
                [
                    'label' => 'Yakin',
                    'val' => '0.8',
                    'num' => '0.8',
                    'activeClass' => 'peer-checked:bg-teal-800 peer-checked:text-white peer-checked:border-teal-900 font-bold'
                ],
                [
                    'label' => 'Sangat yakin',
                    'val' => '1.0',
                    'num' => '1.0',
                    'activeClass' => 'peer-checked:bg-teal-950 peer-checked:text-white peer-checked:border-teal-950 font-bold'
                ],
            ];

            // Konfigurasi tint lembut header & penjelasan per kategori
            $catMeta = [
                'Suasana Hati' => [
                    'headerBg' => 'bg-[#EBF2ED]',
                    'headerBorder' => 'border-[#D6E3D9]',
                    'textTone' => 'text-[#1A3831]',
                    'desc' => 'Perasaan murung, kehilangan minat, hampa, dan fluktuasi emosi.'
                ],
                'Tidur dan Energi' => [
                    'headerBg' => 'bg-[#FBF1EC]',
                    'headerBorder' => 'border-[#F3CDBB]/70',
                    'textTone' => 'text-[#8C4A2F]',
                    'desc' => 'Kualitas istirahat, insomnia, kelelahan kronis, dan ritme fisik tubuh.'
                ],
                'Kecemasan dan Panik' => [
                    'headerBg' => 'bg-[#E6F0EE]',
                    'headerBorder' => 'border-[#CFE1DE]',
                    'textTone' => 'text-[#1F423A]',
                    'desc' => 'Rasa gelisah, tegang berlebih, serangan panik, dan respon saraf otonom.'
                ],
                'Perilaku dan Pikiran' => [
                    'headerBg' => 'bg-[#F4EFEA]',
                    'headerBorder' => 'border-[#E5DDD4]',
                    'textTone' => 'text-[#4A3B32]',
                    'desc' => 'Konsentrasi, pola pikir kritis, impulsivitas, dan keselamatan diri.'
                ],
            ];
        @endphp

        <!-- 4. DAFTAR GEJALA DENGAN HEADER STICKY & KATEGORI DAPAT DILIPAT -->
        <div class="space-y-10">
            @foreach ($grouped as $catName => $items)
                @php
                    $meta = $catMeta[$catName] ?? [
                        'headerBg' => 'bg-white',
                        'headerBorder' => 'border-[#DCE5E0]',
                        'textTone' => 'text-[#1F2A27]',
                        'desc' => 'Kumpulan gejala klinis yang diuji.'
                    ];
                @endphp
                <div class="space-y-3 scroll-mt-48" id="cat-{{ Str::slug($catName) }}">
                    <!-- Header Kategori Sticky dengan Tint Lembut -->
                    <div class="sticky top-[136px] sm:top-[140px] z-20 {{ $meta['headerBg'] }} {{ $meta['headerBorder'] }} border rounded-2xl px-5 py-3.5 shadow-[0_4px_16px_rgba(31,42,39,0.03)] backdrop-blur-xs flex items-center justify-between gap-4 cursor-pointer select-none transition-all"
                         @click="toggleCategory(@json($catName))"
                         @keydown.enter="toggleCategory(@json($catName))"
                         @keydown.space.prevent="toggleCategory(@json($catName))"
                         tabindex="0"
                         role="button"
                         :aria-expanded="isCategoryOpen(@json($catName)) ? 'true' : 'false'">
                        <div class="space-y-0.5">
                            <h2 class="font-serif text-lg font-semibold {{ $meta['textTone'] }} tracking-tight">
                                {{ $catName }}
                            </h2>
                            <p class="text-xs text-[#52605B]">
                                {{ $meta['desc'] }}
                            </p>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            <!-- Penghitung "X dari N terisi" -->
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-white/80 border border-black/5 text-[#1F2A27]"
                                  x-text="countCategory(@json($catName)) + ' dari ' + totalCategory(@json($catName)) + ' terisi'">
                                0 dari {{ $items->count() }} terisi
                            </span>

                            <!-- Ikon Panah Lipat yang Berputar -->
                            <svg class="w-4 h-4 text-[#52605B] transition-transform duration-200"
                                 :class="!isCategoryOpen(@json($catName)) ? '-rotate-90' : ''"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </div>

                    <!-- Daftar Kartu Gejala Dalam Kategori -->
                    <div x-show="isCategoryOpen(@json($catName))" x-transition class="space-y-3 pt-1">
                        @foreach ($items as $sym)
                        <fieldset style="border: 0 !important; padding: 0 !important; margin: 0 !important; min-width: 0 !important; outline: none !important;" class="border-0 p-0 m-0 min-w-0">
                            <legend class="sr-only">Gejala {{ $sym->code }}: {{ $sym->text }}</legend>
                            <div class="p-5 sm:p-6 rounded-[20px] border transition-all duration-200 {{ $sym->is_safety_trigger ? 'bg-[#FBF1EC] border-[#F3CDBB]' : 'bg-white border-[#DCE5E0]' }}"
                                 :class="isFilled('{{ $sym->code }}') ? 'border-l-[3px] border-l-[#1A3831] bg-[#F7FAF8] shadow-[0_4px_16px_rgba(26,56,49,0.04)]' : ''">
                                
                                <div class="mb-3.5 space-y-1.5">
                                    <div class="flex items-center gap-2">
                                        <!-- Pil Abu Kode Gejala -->
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-mono font-bold"
                                              :class="isFilled('{{ $sym->code }}') ? 'bg-[#E3EDE6] text-[#1A3831] border border-[#DCE5E0]' : 'bg-stone-100 text-[#52605B] border border-stone-200'">
                                            {{ $sym->code }}
                                        </span>

                                        <!-- Label Penting Lembut untuk Gejala Keselamatan G25 -->
                                        @if($sym->is_safety_trigger)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#F3CDBB]/70 text-[#8C4A2F] border border-[#F3CDBB]">
                                            Penting
                                        </span>
                                        @endif
                                    </div>

                                    <!-- Teks Gejala 17px Lega -->
                                    <p class="text-[17px] leading-[1.45] text-[#1F2A27] font-medium">
                                        {{ $sym->text }}
                                    </p>

                                    <!-- Kalimat Dukungan Hangat untuk G25 -->
                                    @if($sym->is_safety_trigger)
                                    <div class="pt-1 text-xs text-[#8C4A2F] flex flex-wrap items-center gap-1.5 leading-relaxed">
                                        <span>Jika kamu pernah merasa seperti ini, kamu tidak sendirian. Ada layanan yang siap mendengarkan.</span>
                                        <a href="#layanan-krisis" @click.prevent="scrollToCrisis()" class="underline font-semibold hover:text-[#4A3B32]">
                                            Lihat Layanan Krisis
                                        </a>
                                    </div>
                                    @endif
                                </div>

                                <!-- 6 Tombol Pilihan Pil Sejajar dengan Intensitas Bertingkat -->
                                <x-choice-group
                                    :name="'symptoms[' . $sym->code . ']'"
                                    :model="'symptoms.' . $sym->code"
                                    cols="grid-cols-3 sm:grid-cols-6"
                                    shape="rounded-full"
                                    :id-prefix="'sym_' . $sym->code"
                                    :selected="old('symptoms.' . $sym->code, '0')"
                                    :options="$confidenceButtons"
                                />
                            </div>
                        </fieldset>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- 5. BAR AKSI BAWAH MELAYANG (PIL, DI TENGAH, MAKSIMAL 880PX, JARAK 16PX DARI BAWAH) -->
        <div class="fixed bottom-4 left-4 right-4 z-40 max-w-[880px] mx-auto pointer-events-none">
            <div class="bg-white/95 backdrop-blur-md border border-[#DCE5E0] rounded-full px-5 py-3 sm:px-7 sm:py-3.5 shadow-[0_12px_40px_rgba(31,42,39,0.12)] flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4 pointer-events-auto">
                <!-- Status di Kiri dengan Titik Berwarna -->
                <div class="flex items-center gap-2 text-xs sm:text-sm w-full sm:w-auto">
                    <span class="w-2.5 h-2.5 rounded-full shrink-0"
                          :class="countSelected() >= 3 ? 'bg-teal-700' : 'bg-amber-500'"></span>
                    <span class="font-medium text-[#1F2A27]"
                          x-text="countSelected() >= 3 ? 'Siap dikirim, ' + countSelected() + ' gejala dipilih' : 'Pilih minimal 3 gejala (baru ' + countSelected() + ')'">
                        Pilih minimal 3 gejala (baru 0)
                    </span>
                </div>

                <!-- Tombol Batal & Kirim di Kanan -->
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <!-- Tombol Batal (Pil Outline) -->
                    <button type="button"
                            @click="handleCancelClick()"
                            class="flex-1 sm:flex-initial px-6 py-2.5 text-xs sm:text-sm font-semibold rounded-full border border-[#DCE5E0] bg-white text-[#1F2A27] hover:bg-stone-50 transition-all duration-150 active:scale-[0.97] text-center cursor-pointer shadow-2xs">
                        Batal
                    </button>

                    <!-- Tombol Kirim (Pil Hijau Tua, Panah Kecil, Status Memuat) -->
                    <button type="submit"
                            :disabled="countSelected() < 3 || isSubmitting"
                            :class="(countSelected() < 3 || isSubmitting) ? 'bg-stone-200 text-stone-400 cursor-not-allowed' : 'bg-[#1A3831] hover:bg-[#142621] text-white shadow-sm cursor-pointer hover:-translate-y-0.5 active:scale-[0.97]'"
                            class="flex-1 sm:flex-initial px-7 py-2.5 text-xs sm:text-sm font-bold rounded-full transition-all duration-200 text-center flex items-center justify-center gap-2">
                        <span x-show="!isSubmitting" class="flex items-center gap-1.5">
                            Kirim <span aria-hidden="true">&rarr;</span>
                        </span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center gap-2">
                            <svg class="animate-spin w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            Menghitung...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal Konfirmasi Batal Bergaya Situs (Bukan Confirm Browser) -->
    <div x-show="showCancelModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="bg-white rounded-[24px] border border-[#DCE5E0] max-w-sm w-full p-6 space-y-4 shadow-[0_20px_50px_rgba(31,42,39,0.18)]"
             @click.outside="showCancelModal = false">
            <div class="space-y-1.5">
                <h3 class="font-serif text-xl font-semibold text-[#1F2A27]">
                    Batalkan konsultasi?
                </h3>
                <p class="text-xs sm:text-sm text-[#52605B] leading-relaxed">
                    Pilihan gejala yang sudah kamu tentukan akan dihapus dan kamu akan kembali ke Beranda.
                </p>
            </div>

            <div class="flex items-center gap-2.5 pt-2">
                <button type="button"
                        @click="showCancelModal = false"
                        class="flex-1 px-4 py-2.5 text-xs font-semibold rounded-full border border-[#DCE5E0] bg-white text-[#1F2A27] hover:bg-stone-50 transition-colors text-center">
                    Lanjutkan Mengisi
                </button>
                <button type="button"
                        @click="window.location.href = @json(route('home'))"
                        class="flex-1 px-4 py-2.5 text-xs font-semibold rounded-full bg-red-700 hover:bg-red-800 text-white transition-colors text-center shadow-xs">
                    Ya, Batalkan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function consultationForm() {
    return {
        patientName: @json(old('patient_name', '')),
        patientAge: @json(old('patient_age', '')),
        patientGender: @json(old('patient_gender', '')),
        totalSymptoms: {{ $symptoms->count() }},
        errorMessage: '',
        isSubmitting: false,
        showCancelModal: false,
        collapsedCategories: {},

        symptoms: {
            @foreach ($symptoms as $s)
                '{{ $s->code }}': @json((string) old('symptoms.' . $s->code, '0')),
            @endforeach
        },

        categoryMap: {
            @foreach ($grouped as $catName => $items)
                @json($catName): [
                    @foreach ($items as $sym)
                        @json($sym->code),
                    @endforeach
                ],
            @endforeach
        },

        countSelected() {
            let count = 0;
            for (const code in this.symptoms) {
                if (parseFloat(this.symptoms[code]) > 0) {
                    count++;
                }
            }
            return count;
        },

        countCategory(cat) {
            const list = this.categoryMap[cat] || [];
            let count = 0;
            for (const code of list) {
                if (parseFloat(this.symptoms[code] || 0) > 0) {
                    count++;
                }
            }
            return count;
        },

        totalCategory(cat) {
            return (this.categoryMap[cat] || []).length;
        },

        toggleCategory(cat) {
            this.collapsedCategories[cat] = !this.collapsedCategories[cat];
        },

        isCategoryOpen(cat) {
            return !this.collapsedCategories[cat];
        },

        progressPercentage() {
            if (!this.totalSymptoms || this.totalSymptoms === 0) return 0;
            return Math.min(100, Math.round((this.countSelected() / this.totalSymptoms) * 100));
        },

        isFilled(code) {
            return parseFloat(this.symptoms[code] || 0) > 0;
        },

        resetOptions() {
            if (confirm('Apakah Anda yakin ingin mengatur ulang semua pilihan gejala ke "Tidak (0)"?')) {
                for (const code in this.symptoms) {
                    this.symptoms[code] = '0';
                }
                this.errorMessage = '';
            }
        },

        handleCancelClick() {
            if (this.countSelected() > 0) {
                this.showCancelModal = true;
            } else {
                window.location.href = @json(route('home'));
            }
        },

        scrollToCrisis() {
            const footer = document.querySelector('footer');
            if (footer) {
                footer.scrollIntoView({ behavior: 'smooth' });
            }
        },

        handleSubmit(e) {
            if (this.countSelected() < 3) {
                e.preventDefault();
                this.errorMessage = 'Harap pilih minimal 3 gejala yang Anda rasakan sebelum melanjutkan kalkulasi.';
                const bar = document.getElementById('progressBarContainer');
                if (bar) {
                    bar.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }

            if (this.isSubmitting) {
                e.preventDefault();
                return false;
            }

            this.errorMessage = '';
            this.isSubmitting = true;
            return true;
        }
    };
}
</script>
@endsection
