@extends('layouts.admin')

@section('title', 'Riwayat Surat Keluar')

@section('content')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <div x-data="riwayatSurat()" x-init="init()">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-brand">Riwayat Surat Keluar</h2>
                <p class="text-sm text-gray-500 mt-1">Daftar semua surat yang telah dicetak atau diterbitkan oleh sistem.</p>
            </div>
            
            <button @click="openModal()" class="bg-brand hover:bg-brand-dark text-white font-semibold py-2.5 px-5 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2 text-sm focus:ring-4 focus:ring-purple-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Riwayat
            </button>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="flex flex-col sm:flex-row gap-4 items-end">
                
                <div class="w-full sm:w-48">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Filter Rentang Waktu</label>
                    <select x-model="filterTipe" @change="onFilterTipeChange" class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-200 focus:border-brand p-2.5 bg-gray-50 outline-none transition-colors">
                        <option value="">Semua Waktu</option>
                        <option value="minggu">Minggu Ini</option>
                        <option value="bulan">Bulan Tertentu</option>
                        <option value="tahun">Tahun Tertentu</option>
                    </select>
                </div>

                <div x-show="filterTipe === 'bulan'" x-transition class="w-full sm:w-48">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Pilih Bulan</label>
                    <select x-model="filterBulan" class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-200 focus:border-brand p-2.5 bg-gray-50 outline-none transition-colors">
                        <option value="" disabled selected>Pilih Bulan...</option>
                        <option value="1">Januari</option>
                        <option value="2">Februari</option>
                        <option value="3">Maret</option>
                        <option value="4">April</option>
                        <option value="5">Mei</option>
                        <option value="6">Juni</option>
                        <option value="7">Juli</option>
                        <option value="8">Agustus</option>
                        <option value="9">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                </div>

                <div x-show="filterTipe === 'bulan' || filterTipe === 'tahun'" x-transition class="w-full sm:w-32">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Pilih Tahun</label>
                    <select x-model="filterTahun" class="w-full text-sm border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-200 focus:border-brand p-2.5 bg-gray-50 outline-none transition-colors">
                        <option value="" disabled selected>Tahun...</option>
                        <option value="2026">2026</option>
                        <option value="2025">2025</option>
                        <option value="2024">2024</option>
                    </select>
                </div>

                <div x-show="filterTipe !== ''" x-transition>
                    <button @click="applyFilter()" class="bg-gray-800 hover:bg-gray-900 text-white font-medium py-2.5 px-5 rounded-lg text-sm transition-colors shadow-sm focus:ring-4 focus:ring-gray-300">
                        Terapkan
                    </button>
                </div>

                <div x-show="filterTipe !== ''" x-transition>
                    <button @click="resetFilter()" class="bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-medium py-2.5 px-5 rounded-lg text-sm transition-colors shadow-sm focus:ring-4 focus:ring-gray-200">
                        Reset
                    </button>
                </div>

            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-4 w-16 text-center font-bold">No</th>
                            <th scope="col" class="px-6 py-4 font-bold w-36 whitespace-nowrap">Tanggal Dibuat</th>
                            <th scope="col" class="px-6 py-4 font-bold w-1/4">Nama Surat</th>
                            <th scope="col" class="px-6 py-4 font-bold whitespace-nowrap">Nomor Surat</th>
                            <th scope="col" class="px-6 py-4 font-bold">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 align-top">
                        
                        <tr x-show="listLoading">
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Memuat data...</td>
                        </tr>
                        
                        <tr x-show="!listLoading && items.length === 0">
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada riwayat surat.</td>
                        </tr>

                        <template x-for="(item, index) in items" :key="item.id_riwayat">
                            <tr class="hover:bg-gray-50 transition-colors bg-white">
                                <td class="px-6 py-4 text-center font-medium text-gray-900" x-text="getCounter(index)"></td>
                                <td class="px-6 py-4 font-semibold text-gray-700">
                                    <span x-text="formatDate(item.tgl_dibuat)"></span><br>
                                    <span class="text-xs text-gray-400 font-normal" x-text="formatTime(item.tgl_dibuat)"></span>
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-800" x-text="item.nama_surat || '-'"></td>
                                <td class="px-6 py-4">
                                    <span class="bg-purple-100 text-brand text-xs font-semibold px-2.5 py-1.5 rounded-md border border-purple-200 whitespace-nowrap" x-text="item.nomor_surat || '-'">
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <ul class="text-xs text-gray-700 space-y-1">
                                        <template x-if="item.keterangan && typeof item.keterangan === 'object'">
                                            <template x-for="[key, val] in Object.entries(item.keterangan)" :key="key">
                                                <li><span class="font-bold text-gray-900" x-text="key + ':'"></span> <span x-text="val"></span></li>
                                            </template>
                                        </template>
                                        <template x-if="!item.keterangan || typeof item.keterangan !== 'object'">
                                            <li><span x-text="item.keterangan || '-'"></span></li>
                                        </template>
                                    </ul>
                                </td>
                            </tr>
                        </template>

                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row items-center justify-between gap-4">
                <span class="text-sm text-gray-700">
                    Menampilkan <span class="font-semibold text-gray-900" x-text="pagination.from || 0"></span> hingga <span class="font-semibold text-gray-900" x-text="pagination.to || 0"></span> dari <span class="font-semibold text-gray-900" x-text="pagination.total"></span> entri (Max 100/Page)
                </span>
                <div class="inline-flex mt-2 xs:mt-0 shadow-sm rounded-md" x-show="pagination.last_page > 1">
                    <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page <= 1" class="flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        Prev
                    </button>
                    <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page >= pagination.last_page" class="flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 border-s-0 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        Next
                        <svg class="w-4 h-4 ms-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="isModalOpen" 
             style="display: none;"
             class="fixed inset-0 z-50 bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity duration-300">
            
            <div @click.away="closeModal()" class="bg-white rounded-xl shadow-2xl w-full max-w-md flex flex-col overflow-hidden transform transition-all scale-100">
                
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-900">Pilih Rentang Waktu Cetak</h3>
                    <button @click="closeModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition-colors">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>
                
                <form action="#" method="POST" @submit.prevent="cetakRiwayat()">
                    <div class="p-6 space-y-5">
                        
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-900">Rentang Waktu</label>
                            <select x-model="cetakTipe" required class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-purple-200 focus:border-brand block w-full p-3 outline-none transition-all appearance-none">
                                <option value="" disabled selected>Pilih rentang...</option>
                                <option value="minggu">Seminggu Terakhir</option>
                                <option value="bulan">Per Bulan</option>
                                <option value="tahun">Per Tahun</option>
                            </select>
                        </div>

                        <div x-show="cetakTipe === 'bulan'" x-transition>
                            <label class="block mb-2 text-sm font-bold text-gray-900">Bulan</label>
                            <select x-model="cetakBulan" :required="cetakTipe === 'bulan'" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-purple-200 focus:border-brand block w-full p-3 outline-none transition-all appearance-none">
                                <option value="" disabled selected>Pilih Bulan...</option>
                                <option value="1">Januari</option>
                                <option value="2">Februari</option>
                                <option value="3">Maret</option>
                                <option value="4">April</option>
                                <option value="5">Mei</option>
                                <option value="6">Juni</option>
                                <option value="7">Juli</option>
                                <option value="8">Agustus</option>
                                <option value="9">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>

                        <div x-show="cetakTipe === 'bulan' || cetakTipe === 'tahun'" x-transition>
                            <label class="block mb-2 text-sm font-bold text-gray-900">Tahun</label>
                            <select x-model="cetakTahun" :required="cetakTipe === 'bulan' || cetakTipe === 'tahun'" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-purple-200 focus:border-brand block w-full p-3 outline-none transition-all appearance-none">
                                <option value="" disabled selected>Pilih Tahun...</option>
                                <option value="2026">2026</option>
                                <option value="2025">2025</option>
                                <option value="2024">2024</option>
                            </select>
                        </div>

                        <div x-show="cetakTipe === 'minggu'" class="bg-blue-50 text-blue-800 p-3 rounded-lg text-xs font-medium border border-blue-100 flex items-start gap-2">
                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Sistem akan mencetak data riwayat surat yang keluar selama 7 hari terakhir dari hari ini secara otomatis.
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-900">Format Cetak</label>
                            <select x-model="cetakFormat" required class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-purple-200 focus:border-brand block w-full p-3 outline-none transition-all appearance-none">
                                <option value="" disabled selected>Pilih format...</option>
                                <option value="xlsx">Excel (.xlsx)</option>
                                <option value="csv">CSV (.csv)</option>
                                <option value="pdf">PDF (.pdf)</option>
                            </select>
                        </div>

                    </div>
                    
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end items-center gap-3">
                        <button type="button" @click="closeModal()" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-brand rounded-lg hover:bg-brand-dark focus:ring-4 focus:outline-none focus:ring-purple-300 shadow-sm transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Cetak
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function riwayatSurat() {
            return {
                // List State
                items: [],
                listLoading: false,
                pagination: {
                    current_page: 1,
                    last_page: 1,
                    per_page: 100,
                    total: 0,
                    from: 0,
                    to: 0
                },

                // State untuk Filter di Halaman
                filterTipe: '',
                filterBulan: '',
                filterTahun: '',

                // State untuk Modal Cetak
                isModalOpen: false,
                cetakTipe: '',
                cetakBulan: '',
                cetakTahun: '',
                cetakFormat: 'xlsx',

                init() {
                    this.loadData();
                },

                async loadData(page = 1) {
                    this.listLoading = true;
                    try {
                        const params = new URLSearchParams({
                            page: page,
                            tipe: this.filterTipe,
                            bulan: this.filterBulan,
                            tahun: this.filterTahun
                        });

                        const response = await fetch(`{{ route('riwayat-surat.data') }}?${params.toString()}`, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        });

                        if (!response.ok) throw new Error('Gagal memuat data');
                        
                        const result = await response.json();
                        this.items = result.data || [];
                        this.pagination = result.pagination || this.pagination;
                        
                        // Calculate from and to manually for display
                        if (this.pagination.total > 0) {
                            this.pagination.from = (this.pagination.current_page - 1) * this.pagination.per_page + 1;
                            this.pagination.to = Math.min(this.pagination.current_page * this.pagination.per_page, this.pagination.total);
                        } else {
                            this.pagination.from = 0;
                            this.pagination.to = 0;
                        }

                    } catch (error) {
                        console.error(error);
                        alert('Gagal memuat riwayat: ' + error.message);
                    } finally {
                        this.listLoading = false;
                    }
                },

                getCounter(index) {
                    return (this.pagination.current_page - 1) * this.pagination.per_page + index + 1;
                },

                changePage(page) {
                    if (page >= 1 && page <= this.pagination.last_page) {
                        this.loadData(page);
                    }
                },

                onFilterTipeChange() {
                    // Just reset the dropdowns but don't automatically load, user must click 'Terapkan'
                    this.filterBulan = '';
                    this.filterTahun = '';
                    if (this.filterTipe === '') {
                        this.loadData(1);
                    }
                },

                applyFilter() {
                    if (this.filterTipe === 'bulan' && (!this.filterBulan || !this.filterTahun)) {
                        alert('Silakan pilih bulan dan tahun');
                        return;
                    }
                    if (this.filterTipe === 'tahun' && !this.filterTahun) {
                        alert('Silakan pilih tahun');
                        return;
                    }
                    this.loadData(1);
                },

                resetFilter() {
                    this.filterTipe = '';
                    this.filterBulan = '';
                    this.filterTahun = '';
                    this.loadData(1);
                },

                formatDate(dateString) {
                    if (!dateString) return '-';
                    const d = new Date(dateString);
                    if (isNaN(d.getTime())) return dateString;
                    const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
                },

                formatTime(dateString) {
                    if (!dateString) return '';
                    const d = new Date(dateString);
                    if (isNaN(d.getTime())) return '';
                    const h = String(d.getHours()).padStart(2, '0');
                    const m = String(d.getMinutes()).padStart(2, '0');
                    return `${h}:${m} WIB`;
                },

                // Logic Modal
                openModal() {
                    this.isModalOpen = true;
                    // Reset pilihan saat dibuka
                    this.cetakTipe = '';
                    this.cetakBulan = '';
                    this.cetakTahun = '';
                    this.cetakFormat = 'xlsx';
                    document.body.style.overflow = 'hidden'; // Mengunci scroll background
                },

                closeModal() {
                    this.isModalOpen = false;
                    document.body.style.overflow = ''; 
                },

                cetakRiwayat() {
                    if (this.cetakTipe === 'bulan' && (!this.cetakBulan || !this.cetakTahun)) {
                        alert('Silakan pilih bulan dan tahun');
                        return;
                    }
                    if (this.cetakTipe === 'tahun' && !this.cetakTahun) {
                        alert('Silakan pilih tahun');
                        return;
                    }
                    if (!this.cetakFormat) {
                        alert('Silakan pilih format cetak');
                        return;
                    }

                    const params = new URLSearchParams({
                        tipe: this.cetakTipe,
                        bulan: this.cetakBulan,
                        tahun: this.cetakTahun,
                        format: this.cetakFormat
                    });

                    // Redirect to the export route to trigger file download
                    window.location.href = `{{ route('riwayat-surat.export') }}?${params.toString()}`;
                    
                    this.closeModal();
                }
            }
        }
    </script>
@endsection