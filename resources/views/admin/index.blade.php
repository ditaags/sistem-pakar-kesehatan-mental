@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8" x-data="{
    activeTab: 'rules',
    showAddSymptomModal: false,
    editSymptomData: null
}">
    <!-- Header Admin & Logout -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-stone-200 pb-6">
        <div>
            <div class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-800 uppercase tracking-wider mb-1">
                <span class="w-2 h-2 rounded-full bg-teal-600"></span>
                Administrator Terotentikasi
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-stone-900 tracking-tight">Pengelolaan Basis Pengetahuan Pakar</h1>
            <p class="text-xs text-stone-600 mt-1">
                Perubahan pada data gejala, aturan CF, dan kondisi langsung berlaku pada kalkulasi konsultasi berikutnya.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}" class="text-xs font-semibold text-teal-800 hover:text-teal-950 underline mr-1">
                &larr; Beranda Publik
            </a>
            <span class="text-xs text-stone-600 hidden sm:inline">Masuk sebagai: <strong>{{ Auth::user()->name ?? 'Admin' }}</strong></span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 transition-colors cursor-pointer">
                    Keluar
                </button>
            </form>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if (session('success'))
    <div class="bg-teal-50 border border-teal-200 text-teal-900 px-4 py-3 rounded-lg text-xs sm:text-sm">
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="bg-red-50 border border-red-200 text-red-900 px-4 py-3 rounded-lg text-xs sm:text-sm">
        {{ session('error') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-900 px-4 py-3 rounded-lg text-xs">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Navigasi Tab Sederhana -->
    <div class="flex border-b border-stone-200 gap-2 text-xs sm:text-sm font-semibold">
        <button type="button" @click="activeTab = 'rules'"
                class="px-4 py-2.5 border-b-2 transition-colors"
                :class="activeTab === 'rules' ? 'border-teal-800 text-teal-900' : 'border-transparent text-stone-600 hover:text-stone-900'">
            Aturan CF Pakar ({{ $rules->count() }})
        </button>
        <button type="button" @click="activeTab = 'symptoms'"
                class="px-4 py-2.5 border-b-2 transition-colors"
                :class="activeTab === 'symptoms' ? 'border-teal-800 text-teal-900' : 'border-transparent text-stone-600 hover:text-stone-900'">
            Daftar Gejala ({{ $symptoms->count() }})
        </button>
        <button type="button" @click="activeTab = 'conditions'"
                class="px-4 py-2.5 border-b-2 transition-colors"
                :class="activeTab === 'conditions' ? 'border-teal-800 text-teal-900' : 'border-transparent text-stone-600 hover:text-stone-900'">
            Data Kondisi ({{ $conditions->count() }})
        </button>
    </div>

    <!-- TAB 1: TABEL ATURAN (INLINE EDIT CF PAKAR) -->
    <div x-show="activeTab === 'rules'" class="space-y-4">
        <div class="flex items-center justify-between text-xs text-stone-600">
            <span>Ubah nilai bobot CF pakar (0.10 sampai 1.00) dan tekan tombol simpan pada baris terkait.</span>
            <span class="font-mono text-teal-800">Total: {{ $rules->count() }} Aturan</span>
        </div>

        <div class="bg-white border border-stone-200 rounded-xl overflow-x-auto shadow-xs">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-stone-50 border-b border-stone-200 text-stone-700 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="p-3 w-16">ID</th>
                        <th class="p-3 w-32">Kondisi</th>
                        <th class="p-3">Gejala & Keterangan</th>
                        <th class="p-3 w-36">Bobot CF Pakar</th>
                        <th class="p-3 w-48 hidden md:table-cell">Rujukan Literatur</th>
                        <th class="p-3 w-24 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach ($rules as $r)
                    <tr class="hover:bg-stone-50/70 transition-colors">
                        <td class="p-3 font-mono font-bold text-stone-700">{{ $r->id }}</td>
                        <td class="p-3">
                            <span class="font-mono font-bold text-teal-800 bg-teal-50 px-1.5 py-0.5 rounded text-xs">{{ $r->condition_code }}</span>
                            <div class="text-[11px] text-stone-600 mt-0.5">{{ $r->condition->name ?? '' }}</div>
                        </td>
                        <td class="p-3">
                            <span class="font-mono font-semibold text-stone-800">{{ $r->symptom_code }}</span>:
                            <span class="text-stone-700">{{ $r->symptom->text ?? '-' }}</span>
                        </td>
                        <td class="p-3">
                            <form action="{{ route('admin.rules.update', $r->id) }}" method="POST" id="form-rule-{{ $r->id }}">
                                @csrf
                                @method('PUT')
                                <input type="number" step="0.05" min="0.0" max="1.0" name="cf_pakar" value="{{ $r->cf_pakar }}"
                                       class="w-24 px-2.5 py-1 text-xs font-mono font-bold bg-white border border-stone-300 rounded focus:ring-1 focus:ring-teal-700"
                                       required>
                        </td>
                        <td class="p-3 hidden md:table-cell text-xs text-stone-600">
                                <input type="text" name="source_literature" value="{{ $r->source_literature }}"
                                       class="w-full px-2 py-1 text-xs bg-white border border-stone-200 rounded text-stone-600">
                        </td>
                        <td class="p-3 text-right">
                                <button type="submit" class="px-3 py-1 text-xs font-semibold rounded bg-teal-800 text-white hover:bg-teal-900">
                                    Simpan
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 2: TABEL GEJALA (CRUD) -->
    <div x-show="activeTab === 'symptoms'" class="space-y-6" style="display: none;">
        <div class="flex items-center justify-between">
            <div class="text-xs text-stone-600">
                Kelola 25 gejala klinis terstandar. Penghapusan gejala yang berelasi dengan aturan memerlukan konfirmasi.
            </div>
            <button type="button" @click="showAddSymptomModal = true"
                    class="px-4 py-2 text-xs font-semibold rounded-lg text-white bg-teal-800 hover:bg-teal-900 shadow-xs">
                + Tambah Gejala Baru
            </button>
        </div>

        <!-- Modal Tambah Gejala -->
        <div x-show="showAddSymptomModal" class="p-6 bg-white border border-teal-200 rounded-xl space-y-4 shadow-sm">
            <h3 class="text-sm font-bold text-stone-900">Formulir Tambah Gejala Baru</h3>
            <form action="{{ route('admin.symptoms.store') }}" method="POST" class="space-y-4 text-xs sm:text-sm">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-semibold text-stone-700 mb-1">Kode Gejala (misal: G26)</label>
                        <input type="text" name="code" class="w-full p-2 border border-stone-300 rounded text-xs uppercase" placeholder="G26" required>
                    </div>
                    <div>
                        <label class="block font-semibold text-stone-700 mb-1">Kelompok Kategori</label>
                        <select name="category" class="w-full p-2 border border-stone-300 rounded text-xs">
                            <option value="Suasana Hati">Suasana Hati</option>
                            <option value="Tidur dan Energi">Tidur dan Energi</option>
                            <option value="Kecemasan dan Panik">Kecemasan dan Panik</option>
                            <option value="Perilaku dan Pikiran">Perilaku dan Pikiran</option>
                            <option value="Keselamatan">Keselamatan</option>
                        </select>
                    </div>
                    <div class="flex items-center pt-6 gap-2">
                        <label class="flex items-center gap-2 cursor-pointer font-semibold text-red-700 text-xs">
                            <input type="checkbox" name="is_safety_trigger" value="1" class="rounded border-red-300 text-red-700">
                            Gejala Pemicu Darurat (Safety Rule)
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Kalimat Gejala (Bahasa Awam)</label>
                    <textarea name="text" rows="2" class="w-full p-2 border border-stone-300 rounded text-xs" placeholder="Deskripsikan gejala yang mudah dipahami pengguna..." required></textarea>
                </div>
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Penjelasan Klinis (DSM-5 / PPDGJ)</label>
                    <input type="text" name="clinical_explanation" class="w-full p-2 border border-stone-300 rounded text-xs" placeholder="Kriteria DSM-5...">
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="showAddSymptomModal = false" class="px-4 py-1.5 rounded border border-stone-300 text-stone-700 text-xs">Batal</button>
                    <button type="submit" class="px-5 py-1.5 rounded bg-teal-800 text-white text-xs font-semibold">Simpan Gejala</button>
                </div>
            </form>
        </div>

        <!-- Tabel Gejala -->
        <div class="bg-white border border-stone-200 rounded-xl overflow-x-auto shadow-xs">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead class="bg-stone-50 border-b border-stone-200 text-stone-700 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="p-3 w-16">Kode</th>
                        <th class="p-3 w-36">Kelompok</th>
                        <th class="p-3">Kalimat Gejala (Awam)</th>
                        <th class="p-3 w-28 text-center">Safety Rule</th>
                        <th class="p-3 w-36 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach ($symptoms as $s)
                    <tr class="hover:bg-stone-50/70 transition-colors">
                        <td class="p-3 font-mono font-bold text-stone-900">{{ $s->code }}</td>
                        <td class="p-3">
                            <span class="inline-block px-2 py-0.5 rounded text-[11px] bg-stone-100 text-stone-700">
                                {{ $s->category }}
                            </span>
                        </td>
                        <td class="p-3 text-stone-800">{{ $s->text }}</td>
                        <td class="p-3 text-center">
                            @if ($s->is_safety_trigger)
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-red-100 text-red-800">Ya (119)</span>
                            @else
                            <span class="text-stone-600 text-xs">-</span>
                            @endif
                        </td>
                        <td class="p-3 text-right">
                            <form action="{{ route('admin.symptoms.destroy', $s->code) }}" method="POST"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus gejala {{ $s->code }}? Jika masih dipakai oleh aturan pakar, relasi aturan juga akan dihapus.');"
                                  class="inline-block">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="confirm_delete" value="1">
                                <button type="submit" class="text-red-700 hover:text-red-900 text-xs font-semibold px-2 py-1 rounded hover:bg-red-50">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 3: DATA KONDISI (LIHAT & UBAH DESKRIPSI SERTA SARAN) -->
    <div x-show="activeTab === 'conditions'" class="space-y-6" style="display: none;">
        <div class="text-xs text-stone-600">
            Perbarui narasi deskripsi kondisi dan butir saran umum yang tampil pada kartu Beranda dan halaman Hasil konsultasi.
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach ($conditions as $c)
            <div class="bg-white p-6 rounded-xl border border-stone-200 space-y-4 shadow-xs">
                <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                    <div>
                        <span class="font-mono text-xs font-bold text-teal-800 bg-teal-50 px-2 py-0.5 rounded">{{ $c->code }}</span>
                        <h3 class="text-base font-bold text-stone-900 mt-1">{{ $c->name }}</h3>
                        <div class="text-xs text-stone-600 italic">{{ $c->original_name }}</div>
                    </div>
                </div>

                <form action="{{ route('admin.conditions.update', $c->code) }}" method="POST" class="space-y-4 text-xs sm:text-sm">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block font-semibold text-stone-700 mb-1 text-xs">Deskripsi Kondisi:</label>
                        <textarea name="description" rows="4" class="w-full p-2.5 border border-stone-300 rounded-lg text-xs leading-relaxed focus:ring-1 focus:ring-teal-700" required>{{ $c->description }}</textarea>
                    </div>

                    <div>
                        <label class="block font-semibold text-stone-700 mb-1 text-xs">Saran Umum (Satu saran per baris):</label>
                        <textarea name="general_advice" rows="4" class="w-full p-2.5 border border-stone-300 rounded-lg text-xs font-mono leading-relaxed focus:ring-1 focus:ring-teal-700">@if(is_array($c->general_advice)){{ implode("\n", $c->general_advice) }}@endif</textarea>
                    </div>

                    <div>
                        <label class="block font-semibold text-stone-700 mb-1 text-xs">Panduan Mencari Bantuan:</label>
                        <textarea name="seeking_help_advice" rows="2" class="w-full p-2.5 border border-stone-300 rounded-lg text-xs leading-relaxed focus:ring-1 focus:ring-teal-700">{{ $c->seeking_help_advice }}</textarea>
                    </div>

                    <div class="text-right pt-2 border-t border-stone-100">
                        <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-lg text-white bg-teal-800 hover:bg-teal-900">
                            Simpan Perubahan {{ $c->code }}
                        </button>
                    </div>
                </form>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
