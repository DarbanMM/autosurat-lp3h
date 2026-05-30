@extends('layouts.user')

@section('title', 'Buat Surat Baru')

@section('content')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <div x-data="buatSuratManager()">
        
        <div x-show="currentView === 'list'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0">
            
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-950">Buat Surat</h2>
                    <p class="text-sm text-gray-500 mt-1">Pilih jenis templat surat yang ingin Anda terbitkan di bawah ini.</p>
                </div>
                
                <div class="relative w-full sm:w-80 flex-shrink-0">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" x-model="searchQuery" 
                        class="w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-xl pl-10 pr-4 py-3 outline-none focus:ring-2 focus:ring-purple-100 focus:border-[#670075] transition-all shadow-sm placeholder-gray-400" 
                        placeholder="Cari surat spesifik...">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <template x-for="surat in filteredSurat" :key="surat.id">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between overflow-hidden group">
                        
                        <div class="p-6">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-purple-50 text-[#670075] mb-4 group-hover:bg-[#670075] group-hover:text-white transition-colors duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 leading-snug" x-text="surat.nama"></h3>
                            <p class="text-xs text-gray-400 mt-2" x-text="surat.inputManual ? 'Memerlukan isian data di halaman keperluan.' : 'Instan (Dapat langsung diunduh tanpa isian manual).'"></p>
                        </div>
                        
                        <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between">
                            <button @click="pilihSurat(surat)" class="text-sm font-bold text-[#670075] hover:text-[#4f0059] flex items-center gap-2 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                Buat
                            </button>
                            <svg class="w-4 h-4 text-gray-300 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                        
                    </div>
                </template>

            </div>

            <div x-show="filteredSurat.length === 0" class="text-center py-16 bg-white rounded-2xl border border-dashed border-gray-200 mt-6" style="display: none;">
                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-sm font-medium text-gray-500">Surat yang Anda cari tidak ditemukan.</p>
            </div>
        </div>

        <div x-show="currentView === 'keperluan'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;">
            
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-950">Keperluan Surat</h2>
                <p class="text-sm text-gray-500 mt-1 flex items-center gap-1.5">
                    Mengonfigurasi dokumen: 
                    <span class="font-bold text-[#670075]" x-text="selectedLetterData?.nama"></span>
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden max-w-2xl">
                
                <div class="p-6 bg-gray-50/50 border-b border-gray-100">
                    <p class="text-sm font-semibold text-gray-800">Silakan isi parameter keperluan di bawah ini untuk merender draf dokumen.</p>
                </div>

                <form @submit.prevent="prosesUnduhSurat()">
                    <div class="p-6 space-y-5 max-h-[60vh] overflow-y-auto">
                        
                        <template x-if="selectedLetterData?.slug === 'surat-pengantar'">
                            <div class="space-y-5 animate-fade-in">
                                <div>
                                    <label class="block mb-2 text-sm font-semibold text-gray-900">Tujuan Kepada</label>
                                    <input type="text" x-model="formKeperluan.tujuan_kepada" required
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-purple-200 focus:border-[#670075] block w-full p-3 outline-none transition-all" 
                                        placeholder="Contoh: Kepala Dinas Perindustrian">
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-semibold text-gray-900">Daerah / Wilayah Tujuan</label>
                                    <input type="text" x-model="formKeperluan.daerah" required
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-purple-200 focus:border-[#670075] block w-full p-3 outline-none transition-all" 
                                        placeholder="Contoh: Kota Yogyakarta">
                                </div>
                            </div>
                        </template>

                        <template x-if="selectedLetterData?.slug === 'surat-tugas'">
                            <div class="space-y-5 animate-fade-in">
                                <div>
                                    <label class="block mb-2 text-sm font-semibold text-gray-900">Wilayah Penugasan</label>
                                    <input type="text" x-model="formKeperluan.wilayah" required
                                        class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-purple-200 focus:border-[#670075] block w-full p-3 outline-none transition-all" 
                                        placeholder="Contoh: Kecamatan Imogiri, Bantul">
                                </div>
                                
                                <div>
                                    <label class="block mb-2 text-sm font-semibold text-gray-900">Masa Penugasan</label>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <span class="block text-xs font-medium text-gray-400 mb-1">Dari Tanggal</span>
                                            <input type="date" x-model="formKeperluan.tanggal_mulai" required
                                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-purple-200 focus:border-[#670075] block w-full p-3 outline-none transition-all">
                                        </div>
                                        <div>
                                            <span class="block text-xs font-medium text-gray-400 mb-1">Sampai Tanggal</span>
                                            <input type="date" x-model="formKeperluan.tanggal_selesai" required
                                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-purple-200 focus:border-[#670075] block w-full p-3 outline-none transition-all">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                    </div>

                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
                        <button type="button" @click="kembaliKeList()" class="text-sm font-bold text-gray-600 hover:text-gray-900 flex items-center gap-2 transition-colors py-2 px-3 hover:bg-gray-100 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Kembali
                        </button>
                        
                        <button type="submit" class="bg-[#670075] hover:bg-[#4f0059] text-white font-bold py-2.5 px-6 rounded-xl text-sm shadow-sm transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Buat & Unduh Surat
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>

    <script>
        function buatSuratManager() {
            return {
                currentView: 'list', // 'list' atau 'keperluan'
                searchQuery: '',
                selectedLetterData: null,

                // List Master Data Jenis Surat
                masterSurat: [
                    { id: 1, slug: 'surat-keterangan-p3h', nama: 'Surat Keterangan Pendampingan (SK P3H)', inputManual: false },
                    { id: 2, slug: 'surat-pengantar', nama: 'Surat Pengantar Kegiatan Instansi', inputManual: true },
                    { id: 3, slug: 'surat-tugas', nama: 'Surat Tugas Pendampingan Lapangan', inputManual: true }
                ],

                // Form State Model
                formKeperluan: {
                    tujuan_kepada: '',
                    daerah: '',
                    wilayah: '',
                    tanggal_mulai: '',
                    tanggal_selesai: ''
                },

                // Getter untuk memfilter surat berdasarkan kotak pencarian (search box)
                get filteredSurat() {
                    if (this.searchQuery.trim() === '') {
                        return this.masterSurat;
                    }
                    return this.masterSurat.filter(s => 
                        s.nama.toLowerCase().includes(this.searchQuery.toLowerCase())
                    );
                },

                // Logika ketika tombol 'Buat' pada card diklik
                pilihSurat(surat) {
                    if (surat.inputManual) {
                        // Jika butuh input, alihkan view ke halaman pengisian keperluan
                        this.selectedLetterData = surat;
                        this.currentView = 'keperluan';
                    } else {
                        // Khusus surat-keterangan-p3h: tidak perlu input, langsung unduh instan
                        alert(`Memproses pembuatan dokumen instan:\n"${surat.nama}"\n\nSurat akan otomatis diunduh berdasarkan identitas Anda.`);
                        // Di sini nantinya ditambahkan fungsi window.location.href ke endpoint download Laravel
                    }
                },

                kembaliKeList() {
                    this.currentView = 'list';
                    // Reset isi form
                    this.formKeperluan = { tujuan_kepada: '', daerah: '', wilayah: '', tanggal_mulai: '', tanggal_selesai: '' };
                    this.selectedLetterData = null;
                },

                prosesUnduhSurat() {
                    // Logic kirim data form via AJAX / Form Submit ke Backend Laravel untuk digenerate
                    alert(`Dokumen Berhasil Dibuat!\n\nSistem sedang mengunduh file surat "${this.selectedLetterData.nama}" dengan parameter keperluan yang Anda masukkan.`);
                    this.kembaliKeList();
                }
            }
        }
    </script>
@endsection