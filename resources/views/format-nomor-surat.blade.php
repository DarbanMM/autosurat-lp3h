@extends('layouts.admin')

@section('title', 'Format Nomor Surat')

@section('content')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-brand">Daftar Format</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola dan sesuaikan daftar penomoran surat aplikasi.</p>
        </div>
        
        <button onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: { mode: 'add' } }))" class="bg-brand hover:bg-brand-dark text-white font-semibold py-2.5 px-5 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2 text-sm focus:ring-4 focus:ring-purple-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Format
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-4 w-16 text-center font-bold">No</th>
                        <th scope="col" class="px-6 py-4 font-bold">Format Nomor Surat</th>
                        <th scope="col" class="px-6 py-4 w-40 text-center font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50 transition-colors bg-white">
                        <td class="px-6 py-4 text-center font-medium text-gray-900">1</td>
                        <td class="px-6 py-4">
                            <span class="bg-purple-100 text-brand font-semibold px-3 py-1.5 rounded-md border border-purple-200 whitespace-nowrap">
                                001/LP3H/Acara/X/2026
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: { mode: 'edit', id: 1 } }))" class="inline-flex items-center justify-center px-3 py-1.5 text-sm font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 focus:ring-2 focus:ring-blue-300 transition-all">
                                    Edit
                                </button>
                                <button class="inline-flex items-center justify-center px-3 py-1.5 text-sm font-medium text-red-700 bg-red-100 rounded-lg hover:bg-red-200 focus:ring-2 focus:ring-red-300 transition-all">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div x-data="formatForm()" 
         @open-modal.window="openModal($event.detail.mode, $event.detail.id)"
         x-show="isOpen" 
         style="display: none;"
         class="fixed inset-0 z-50 bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity duration-300">
        
        <div @click.away="closeModal()" class="bg-white rounded-xl shadow-2xl w-full max-w-4xl flex flex-col max-h-[95vh] overflow-hidden transform transition-all scale-100">
            
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
                <h3 class="text-lg font-bold text-gray-900" x-text="modalTitle"></h3>
                <button @click="closeModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition-colors">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            </div>
            
            <form action="#" method="POST" class="flex flex-col flex-1 overflow-hidden" @submit.prevent="submitForm">
                @csrf
                <input type="hidden" name="id" x-model="formatId">
                
                <div class="p-6 space-y-6 overflow-y-auto flex-1 max-h-[70vh] bg-gray-50/50">
                    
                    <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
                        <label class="block mb-2 text-sm font-semibold text-gray-900">Jumlah Bagian Nomor Surat</label>
                        <div class="flex items-center gap-3">
                            <input type="number" min="1" max="15" x-model.number="jumlahBagianInput" 
                                class="w-24 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-purple-200 focus:border-brand p-2.5 outline-none transition-all" 
                                placeholder="Cth: 5">
                            <button type="button" @click="generateParts()" class="px-4 py-2.5 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 focus:ring-4 focus:ring-gray-300 transition-colors">
                                Konfirmasi
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Ketikkan angka lalu klik konfirmasi. Contoh: "001/LP3H/Acara/X/2026" berarti ada 9 bagian.</p>
                    </div>

                    <template x-if="parts.length > 0">
                        <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm space-y-4 animate-fade-in-up">
                            <h4 class="text-sm font-bold text-brand border-b pb-2">Konfigurasi Detail Bagian</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <template x-for="(part, index) in parts" :key="index">
                                    <div class="p-4 border rounded-lg bg-gray-50 hover:bg-purple-50 transition-colors relative group">
                                        <label class="block text-xs font-bold text-gray-700 mb-2" x-text="`Bagian ${index + 1}`"></label>
                                        
                                        <select x-model="part.type" @change="updatePartExample(index)" class="w-full text-sm border-gray-300 rounded focus:ring-brand focus:border-brand mb-2 p-2 bg-white">
                                            <option value="" disabled>Pilih Tipe...</option>
                                            <option value="separator">Tanda "/"</option>
                                            <option value="nomor">Nomor</option>
                                            <option value="text">Text</option>
                                            <option value="bulan">Bulan</option>
                                            <option value="tahun">Tahun</option>
                                        </select>

                                        <div class="mt-2" x-show="part.type !== '' && part.type !== 'separator'" x-transition>
                                            
                                            <template x-if="part.type === 'nomor'">
                                                <select x-model="part.format" @change="updatePartExample(index)" class="w-full text-xs border-gray-300 rounded p-2 bg-white border-dashed border-2 border-brand">
                                                    <option value="" disabled>Format Nomor</option>
                                                    <option value="0">0</option>
                                                    <option value="00">00</option>
                                                    <option value="000">000</option>
                                                    <option value="0000">0000</option>
                                                </select>
                                            </template>

                                            <template x-if="part.type === 'text'">
                                                <input type="text" x-model="part.format" @input="updatePartExample(index)" placeholder="Ketik teks kustom..." class="w-full text-xs border-gray-300 rounded p-2 bg-white border-dashed border-2 border-brand">
                                            </template>

                                            <template x-if="part.type === 'bulan'">
                                                <select x-model="part.format" @change="updatePartExample(index)" class="w-full text-xs border-gray-300 rounded p-2 bg-white border-dashed border-2 border-brand">
                                                    <option value="" disabled>Format Bulan</option>
                                                    <option value="JAN">JAN</option>
                                                    <option value="Jan">Jan</option>
                                                    <option value="jan">jan</option>
                                                    <option value="JANUARI">JANUARI</option>
                                                    <option value="Januari">Januari</option>
                                                    <option value="januari">januari</option>
                                                    <option value="1">1</option>
                                                    <option value="01">01</option>
                                                    <option value="I">I (Romawi)</option>
                                                </select>
                                            </template>

                                            <template x-if="part.type === 'tahun'">
                                                <select x-model="part.format" @change="updatePartExample(index)" class="w-full text-xs border-gray-300 rounded p-2 bg-white border-dashed border-2 border-brand">
                                                    <option value="" disabled>Format Tahun</option>
                                                    <option value="2026">YYYY (Cth: 2026)</option>
                                                    <option value="26">YY (Cth: 26)</option>
                                                </select>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <template x-if="parts.length > 0 && hasNomor">
                        <div class="space-y-4 animate-fade-in-up">
                            
                            <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
                                <label class="block mb-2 text-sm font-semibold text-gray-900">Perubahan Nomor Surat (Reset Counter)</label>
                                <select x-model="resetPeriod" class="w-full md:w-1/2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-purple-200 focus:border-brand p-2.5 outline-none">
                                    <option value="" disabled selected>Pilih siklus...</option>
                                    <option value="Mingguan">Mingguan</option>
                                    <option value="Bulanan">Bulanan</option>
                                    <option value="Tahunan">Tahunan</option>
                                </select>
                            </div>

                            <div class="bg-[#1f1025] p-6 rounded-xl text-white shadow-inner relative overflow-hidden">
                                <div class="absolute right-0 top-0 h-full w-40 bg-white opacity-5 transform skew-x-12 translate-x-12"></div>
                                
                                <h4 class="text-xs font-semibold text-purple-300 uppercase tracking-wider mb-3 relative z-10">Gambaran Format Nomor:</h4>
                                
                                <div class="relative z-10 space-y-3">
                                    <p class="text-sm">Contoh nomor surat pertama: 
                                        <span class="inline-block mt-2 sm:mt-0 sm:ml-2 font-bold text-lg text-yellow-400 tracking-wider bg-black/40 px-3 py-1.5 rounded-md border border-white/10 shadow-sm" x-text="previewNomorPertama"></span>
                                    </p>
                                    
                                    <template x-if="resetPeriod">
                                        <div class="mt-5 pt-4 border-t border-white/10">
                                            <p class="text-sm text-purple-200 mb-2 font-semibold">Simulasi (Siklus <span x-text="resetPeriod"></span>):</p>
                                            <ul class="text-sm space-y-2 opacity-90 font-mono bg-black/20 p-4 rounded-lg">
                                                <li><span class="text-gray-400">28 Mei 2026 ➔</span> <span class="ml-2 text-white" x-text="getSimulasi(1, 5, 2026)"></span></li>
                                                <li><span class="text-gray-400">31 Mei 2026 ➔</span> <span class="ml-2 text-white" x-text="getSimulasi(2, 5, 2026)"></span></li>
                                                <li>
                                                    <span class="text-gray-400">02 Jun 2026 ➔</span> 
                                                    <span class="ml-2 text-green-400 font-bold" x-text="getSimulasi(1, 6, 2026)"></span> 
                                                    <span class="text-xs text-gray-400 italic ml-2">(Reset kembali ke awal)</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>

                </div>
                
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end items-center gap-3 flex-shrink-0">
                    <button type="button" @click="closeModal()" 
                        class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                        class="px-5 py-2.5 text-sm font-medium text-white bg-brand rounded-lg hover:bg-brand-dark focus:ring-4 focus:outline-none focus:ring-purple-300 shadow-sm transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function formatForm() {
            return {
                isOpen: false,
                mode: 'add',
                modalTitle: 'Format Nomor Baru',
                formatId: '',
                
                jumlahBagianInput: '',
                parts: [],
                resetPeriod: '',

                // Mengecek apakah form punya input 'nomor' untuk memunculkan setting reset
                get hasNomor() {
                    return this.parts.some(p => p.type === 'nomor');
                },

                // Menyatukan seluruh contoh teks dari setiap bagian
                get previewNomorPertama() {
                    if (this.parts.length === 0) return '-';
                    return this.parts.map(p => p.example).join('');
                },

                openModal(mode, id = null) {
                    this.mode = mode;
                    this.isOpen = true;
                    if (mode === 'add') {
                        this.modalTitle = 'Format Nomor Baru';
                        this.resetForm();
                    } else {
                        this.modalTitle = 'Edit Format';
                        this.formatId = id;
                        // Data contoh ketika edit ditekan (nantinya diisi dari database via Laravel)
                        this.jumlahBagianInput = 5;
                        this.generateParts();
                    }
                    document.body.style.overflow = 'hidden';
                },

                closeModal() {
                    this.isOpen = false;
                    document.body.style.overflow = '';
                },

                resetForm() {
                    this.formatId = '';
                    this.jumlahBagianInput = '';
                    this.parts = [];
                    this.resetPeriod = '';
                },

                generateParts() {
                    let jumlah = parseInt(this.jumlahBagianInput);
                    if (isNaN(jumlah) || jumlah < 1) return;
                    
                    this.parts = [];
                    // Generate kotak-kotak form sesuai angka input
                    for (let i = 0; i < jumlah; i++) {
                        this.parts.push({
                            type: '',
                            format: '',
                            example: ''
                        });
                    }
                },

                updatePartExample(index) {
                    let part = this.parts[index];
                    
                    if (part.type === 'separator') {
                        part.example = '/';
                        part.format = '/';
                    } else if (part.type === 'nomor') {
                        if(part.format === '0') part.example = '1';
                        else if(part.format === '00') part.example = '01';
                        else if(part.format === '000') part.example = '001';
                        else if(part.format === '0000') part.example = '0001';
                    } else if (part.type === 'text') {
                        part.example = part.format;
                    } else if (part.type === 'bulan') {
                        if(['JAN', 'Jan', 'jan', 'JANUARI', 'Januari', 'januari'].includes(part.format)) {
                            part.example = part.format; 
                        } else if (part.format === '1') part.example = '1';
                        else if (part.format === '01') part.example = '01';
                        else if (part.format === 'I') part.example = 'I';
                    } else if (part.type === 'tahun') {
                        part.example = part.format;
                    }
                },

                // Fungsi untuk menampilkan hasil text simulasi tanggal berdasarkan aturan yang dipilih
                getSimulasi(nomorUrut, bulanInt, tahunInt) {
                    return this.parts.map(p => {
                        if (p.type === 'separator') return '/';
                        if (p.type === 'text') return p.format;
                        if (p.type === 'nomor') {
                            if(p.format === '0') return nomorUrut;
                            if(p.format === '00') return String(nomorUrut).padStart(2, '0');
                            if(p.format === '000') return String(nomorUrut).padStart(3, '0');
                            if(p.format === '0000') return String(nomorUrut).padStart(4, '0');
                        }
                        if (p.type === 'bulan') {
                            const romawi = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
                            const namaBulan = ['JAN','FEB','MAR','APR','MEI','JUN','JUL','AGS','SEP','OKT','NOV','DES'];
                            
                            if(p.format === 'I') return romawi[bulanInt-1];
                            if(p.format === '01') return String(bulanInt).padStart(2, '0');
                            if(p.format === '1') return bulanInt;
                            // Jika format berupa nama bulan text
                            if(p.format === 'JAN' || p.format === 'JANUARI') return namaBulan[bulanInt-1];
                            return p.format; 
                        }
                        if (p.type === 'tahun') return tahunInt;
                        return '';
                    }).join('');
                },

                submitForm() {
                    // Logic untuk submit via API/Laravel Form
                    // Nilai this.parts dan this.resetPeriod sudah tersimpan secara rapi untuk dikirim
                    console.log('Menyimpan Form. Jumlah:', this.jumlahBagianInput);
                    this.closeModal();
                }
            }
        }
    </script>
@endsection