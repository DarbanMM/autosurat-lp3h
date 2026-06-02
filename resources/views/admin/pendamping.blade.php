@extends('layouts.admin')

@section('title', 'Daftar Pendamping')

@section('content')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <div x-data="pendampingManager()">
        
        <div class="flex flex-col xl:flex-row xl:items-center justify-between mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-brand">Daftar Pendamping</h2>
                <p class="text-sm text-gray-500 mt-1">Kelola data pendamping proses produk halal (P3H).</p>
            </div>

            <div class="flex flex-col sm:flex-row flex-wrap items-center gap-2">

                <button @click="openModal('import')" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2.5 px-4 rounded-lg shadow-sm transition-all duration-200 flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Import Pendamping
                </button>

                <div class="relative" @click.away="exportDropdown = false">
                    <button @click="exportDropdown = !exportDropdown" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2.5 px-4 rounded-lg shadow-sm transition-all duration-200 flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Export Pendamping
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="exportDropdown" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-100 z-10 py-1">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-brand flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>
                            Excel (.xlsx)
                        </a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-brand flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm3 1h6v4H7V5z" clip-rule="evenodd"></path></svg>
                            CSV (.csv)
                        </a>
                    </div>
                </div>

                <button @click="openModal('form', 'add')" class="bg-brand hover:bg-brand-dark text-white font-semibold py-2.5 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2 text-sm focus:ring-4 focus:ring-purple-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Pendamping
                </button>

                <input type="text" @keyup="searchQuery = $event.target.value" placeholder="Cari nama atau no registrasi..." class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-brand focus:border-transparent shadow-sm transition-all" />

            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-4 w-16 text-center font-bold">No</th>
                            <th scope="col" class="px-6 py-4 font-bold">No Registrasi</th>
                            <th scope="col" class="px-6 py-4 font-bold w-1/3">Nama</th>
                            <th scope="col" class="px-6 py-4 text-center font-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <template x-for="(item, index) in filteredPendampingList" :key="item.no_registrasi">
                            <tr class="hover:bg-gray-50 transition-colors bg-white">
                                <td class="px-6 py-4 text-center font-medium text-gray-900" x-text="index + 1"></td>
                                <td class="px-6 py-4 font-mono font-semibold text-brand" x-text="item.no_registrasi"></td>
                                <td class="px-6 py-4 font-semibold text-gray-800" x-text="item.nama"></td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="openModal('detail', null, item)" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-purple-700 bg-purple-100 rounded hover:bg-purple-200 focus:ring-2 focus:ring-purple-300 transition-all">
                                            Selengkapnya
                                        </button>
                                        <button @click="openModal('form', 'edit', item)" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded hover:bg-blue-200 focus:ring-2 focus:ring-blue-300 transition-all">
                                            Edit
                                        </button>
                                        <button class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 rounded hover:bg-red-200 focus:ring-2 focus:ring-red-300 transition-all">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>


        <div x-show="activeModal === 'form'" style="display: none;" class="fixed inset-0 z-50 bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity duration-300">
            <div @click.away="closeModal()" class="bg-white rounded-xl shadow-2xl w-full max-w-5xl flex flex-col max-h-[95vh] overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-900" x-text="formMode === 'add' ? 'Pendamping Baru' : 'Edit Pendamping'"></h3>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <form action="#" method="POST" class="flex flex-col flex-1 overflow-hidden" @submit.prevent="submitForm">
                    <div class="p-6 overflow-y-auto flex-1 max-h-[75vh] bg-gray-50/30">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            
                            <div><label class="block text-xs font-bold text-gray-700 mb-1">No Registrasi</label>
                                <input type="text" x-model="formData.no_registrasi" required class="w-full text-sm border-gray-300 rounded focus:ring-brand focus:border-brand p-2 border"></div>
                            
                            <div><label class="block text-xs font-bold text-gray-700 mb-1">ID Pendamping</label>
                                <input type="text" x-model="formData.id_pendamping" class="w-full text-sm border-gray-300 rounded focus:ring-brand focus:border-brand p-2 border"></div>
                            
                            <div><label class="block text-xs font-bold text-gray-700 mb-1">ID Lembaga</label>
                                <input type="text" x-model="formData.id_lembaga" class="w-full text-sm border-gray-300 rounded focus:ring-brand focus:border-brand p-2 border"></div>
                            
                            <div><label class="block text-xs font-bold text-gray-700 mb-1">No Pendaftaran</label>
                                <input type="text" x-model="formData.no_pendaftaran" class="w-full text-sm border-gray-300 rounded focus:ring-brand focus:border-brand p-2 border"></div>
                            
                            <div><label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Berlaku</label>
                                <input type="date" x-model="formData.tgl_berlaku" class="w-full text-sm border-gray-300 rounded focus:ring-brand focus:border-brand p-2 border"></div>
                            
                            <div><label class="block text-xs font-bold text-gray-700 mb-1">Nama Lengkap</label>
                                <input type="text" x-model="formData.nama" required class="w-full text-sm border-gray-300 rounded focus:ring-brand focus:border-brand p-2 border"></div>
                            
                            <div class="md:col-span-2 lg:col-span-3"><label class="block text-xs font-bold text-gray-700 mb-1">Alamat</label>
                                <input type="text" x-model="formData.alamat" class="w-full text-sm border-gray-300 rounded focus:ring-brand focus:border-brand p-2 border"></div>
                            
                            <div><label class="block text-xs font-bold text-gray-700 mb-1">Kode Pos</label>
                                <input type="text" x-model="formData.kode_pos" class="w-full text-sm border-gray-300 rounded focus:ring-brand focus:border-brand p-2 border"></div>
                            
                            <div><label class="block text-xs font-bold text-gray-700 mb-1">Kecamatan</label>
                                <input type="text" x-model="formData.kecamatan" class="w-full text-sm border-gray-300 rounded focus:ring-brand focus:border-brand p-2 border"></div>
                            
                            <div><label class="block text-xs font-bold text-gray-700 mb-1">Kabupaten</label>
                                <input type="text" x-model="formData.kabupaten" class="w-full text-sm border-gray-300 rounded focus:ring-brand focus:border-brand p-2 border"></div>
                            
                            <div><label class="block text-xs font-bold text-gray-700 mb-1">Provinsi</label>
                                <input type="text" x-model="formData.provinsi" class="w-full text-sm border-gray-300 rounded focus:ring-brand focus:border-brand p-2 border"></div>
                            
                            <div><label class="block text-xs font-bold text-gray-700 mb-1">No HP</label>
                                <input type="text" x-model="formData.no_hp" class="w-full text-sm border-gray-300 rounded focus:ring-brand focus:border-brand p-2 border"></div>
                            
                            <div><label class="block text-xs font-bold text-gray-700 mb-1">Tempat Lahir</label>
                                <input type="text" x-model="formData.tempat_lahir" class="w-full text-sm border-gray-300 rounded focus:ring-brand focus:border-brand p-2 border"></div>
                            
                            <div><label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Lahir</label>
                                <input type="date" x-model="formData.tgl_lahir" class="w-full text-sm border-gray-300 rounded focus:ring-brand focus:border-brand p-2 border"></div>
                            
                            <div><label class="block text-xs font-bold text-gray-700 mb-1">NIK</label>
                                <input type="text" x-model="formData.nik" class="w-full text-sm border-gray-300 rounded focus:ring-brand focus:border-brand p-2 border"></div>
                            
                            <div><label class="block text-xs font-bold text-gray-700 mb-1">Pendidikan</label>
                                <input type="text" x-model="formData.pendidikan" class="w-full text-sm border-gray-300 rounded focus:ring-brand focus:border-brand p-2 border"></div>
                            
                            <div><label class="block text-xs font-bold text-gray-700 mb-1">Universitas</label>
                                <input type="text" x-model="formData.universitas" class="w-full text-sm border-gray-300 rounded focus:ring-brand focus:border-brand p-2 border"></div>
                            
                            <div><label class="block text-xs font-bold text-gray-700 mb-1">Status</label>
                                <input type="text" x-model="formData.status" class="w-full text-sm border-gray-300 rounded focus:ring-brand focus:border-brand p-2 border"></div>
                            
                            <div><label class="block text-xs font-bold text-gray-700 mb-1">Nama Lembaga</label>
                                <input type="text" x-model="formData.nama_lembaga" class="w-full text-sm border-gray-300 rounded focus:ring-brand focus:border-brand p-2 border"></div>
                            
                            <div><label class="block text-xs font-bold text-gray-700 mb-1">Sumber Data</label>
                                <input type="text" x-model="formData.sumber_data" class="w-full text-sm border-gray-300 rounded focus:ring-brand focus:border-brand p-2 border"></div>
                            
                            <div><label class="block text-xs font-bold text-gray-700 mb-1">Jumlah PU</label>
                                <input type="number" x-model="formData.jumlah_pu" class="w-full text-sm border-gray-300 rounded focus:ring-brand focus:border-brand p-2 border"></div>
                            
                            <div><label class="block text-xs font-bold text-gray-700 mb-1">Pekerjaan</label>
                                <input type="text" x-model="formData.pekerjaan" class="w-full text-sm border-gray-300 rounded focus:ring-brand focus:border-brand p-2 border"></div>
                            
                            <div><label class="block text-xs font-bold text-gray-700 mb-1">Pekerjaan Lain</label>
                                <input type="text" x-model="formData.pekerjaan_lain" class="w-full text-sm border-gray-300 rounded focus:ring-brand focus:border-brand p-2 border"></div>
                            
                            <div><label class="block text-xs font-bold text-gray-700 mb-1">Asal Unit Kerja</label>
                                <input type="text" x-model="formData.asal_unit_kerja" class="w-full text-sm border-gray-300 rounded focus:ring-brand focus:border-brand p-2 border"></div>

                            <div class="flex flex-col justify-center bg-gray-100 p-3 rounded-lg border border-gray-200">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" x-model="formData.pns" class="w-4 h-4 text-brand bg-white border-gray-300 rounded focus:ring-brand focus:ring-2">
                                    <span class="text-sm font-bold text-gray-700">Apakah PNS?</span>
                                </label>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1" :class="!formData.pns ? 'text-gray-400' : ''">Golongan PNS</label>
                                <input type="text" x-model="formData.pns_golongan" :disabled="!formData.pns" :placeholder="!formData.pns ? 'Bukan PNS' : 'Contoh: III/a'" 
                                    class="w-full text-sm border-gray-300 rounded focus:ring-brand focus:border-brand p-2 border disabled:bg-gray-100 disabled:cursor-not-allowed transition-colors">
                            </div>

                        </div>
                    </div>
                    
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3 flex-shrink-0">
                        <button type="button" @click="closeModal()" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-brand rounded-lg hover:bg-brand-dark shadow-sm transition-colors">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="activeModal === 'import'" style="display: none;" class="fixed inset-0 z-50 bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity duration-300">
            <div @click.away="closeModal()" class="bg-white rounded-xl shadow-2xl w-full max-w-2xl flex flex-col overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Import Data Pendamping</h3>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-900"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                
                <div class="p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Format / Template Nama Kolom yang Wajib Digunakan:</label>
                        <div class="bg-gray-800 text-green-400 font-mono text-xs p-4 rounded-lg leading-relaxed flex flex-wrap gap-2 shadow-inner">
                            <span>id_pendamping,</span><span>id_lembaga,</span><span>no_pendaftaran,</span><span>no_registrasi,</span><span>tgl_berlaku,</span><span>nama,</span><span>alamat,</span><span>kode_pos,</span><span>kecamatan,</span><span>kabupaten,</span><span>provinsi,</span><span>no_hp,</span><span>tempat_lahir,</span><span>tgl_lahir,</span><span>nik,</span><span>pendidikan,</span><span>universitas,</span><span>status,</span><span>nama_lembaga,</span><span>sumber_data,</span><span>jumlah_pu,</span><span>pekerjaan,</span><span>pekerjaan_lain,</span><span>asal_unit_kerja,</span><span>pns,</span><span>pns_golongan</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Pastikan baris pertama (Header) di Excel/CSV Anda sama persis dengan urutan nama di atas. Kolom PNS: gunakan 1/0, true/false, yes/no. Tanggal: gunakan format d/m/Y atau Y-m-d. File besar (>3000 baris) mungkin memerlukan waktu loading lebih lama.</p>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('pendamping.download-template', 'csv') }}" class="flex items-center gap-2 text-sm bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 px-4 py-2 rounded-lg font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Download Template CSV
                        </a>
                        <a href="{{ route('pendamping.download-template', 'xlsx') }}" class="flex items-center gap-2 text-sm bg-gray-100 text-gray-700 border border-gray-300 hover:bg-gray-200 px-4 py-2 rounded-lg font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Download Template Excel
                        </a>
                    </div>

                    <div class="border-2 border-dashed border-gray-300 bg-gray-50 rounded-xl p-8 text-center hover:bg-gray-100 hover:border-brand transition-colors cursor-pointer relative" @dragover.prevent="importDragover = true" @dragleave.prevent="importDragover = false" @drop.prevent="handleFileDrop">
                        <input type="file" @change="handleFileSelect" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept=".xlsx, .csv, .xls" x-ref="importFileInput">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <p class="text-sm font-semibold text-gray-700" x-text="importFile ? importFile.name : 'Klik di sini untuk mengunggah file'"></p>
                        <p class="text-xs text-gray-500 mt-1">atau drag & drop file Excel/CSV ke area ini.</p>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
                    <button @click="closeModal()" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">
                        Batalkan
                    </button>
                    <button @click="submitImport" :disabled="!importFile || importLoading" :class="{'opacity-50 cursor-not-allowed': !importFile || importLoading}" class="px-5 py-2.5 text-sm font-medium text-white bg-brand rounded-lg hover:bg-brand-dark shadow-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2" x-text="importLoading ? 'Sedang Import...' : 'Import'">
                    </button>
                </div>
            </div>
        </div>

        <div x-show="activeModal === 'detail'" style="display: none;" class="fixed inset-0 z-50 bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity duration-300">
            <div @click.away="closeModal()" class="bg-white rounded-xl shadow-2xl w-full max-w-4xl flex flex-col max-h-[90vh] overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Detail Pendamping</h3>
                        <p class="text-xs text-gray-500 font-mono mt-0.5" x-text="'No. Reg: ' + formData.no_registrasi"></p>
                    </div>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-900"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                
                <div class="p-6 overflow-y-auto flex-1 bg-white">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-y-6 gap-x-4 text-sm">
                        
                        <template x-for="(value, key) in formData" :key="key">
                            <div class="border-b border-gray-100 pb-2">
                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1" x-text="key.replace(/_/g, ' ')"></span>
                                <span class="block font-medium text-gray-800" x-text="value === '' || value === null ? '-' : (key === 'pns' ? (value ? 'Ya' : 'Bukan') : value)"></span>
                            </div>
                        </template>

                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end">
                    <button @click="closeModal()" class="px-6 py-2.5 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 shadow-sm transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script>
        function pendampingManager() {
            return {
                exportDropdown: false,
                activeModal: '', // '', 'form', 'import', 'detail'
                formMode: 'add', // 'add', 'edit'
                searchQuery: '',

                // Import Related
                importFile: null,
                importDragover: false,
                importLoading: false,

                // Dummy Data (Backend akan mengganti ini dengan hasil fetch DB diurutkan berdasarkan No Registrasi)
                pendampingList: [],

                // Schema Data Form Kosong
                emptyForm: {
                    no_registrasi: '', id_pendamping: '', id_lembaga: '', no_pendaftaran: '', tgl_berlaku: '', nama: '', alamat: '', kode_pos: '', kecamatan: '', kabupaten: '', provinsi: '', no_hp: '', tempat_lahir: '', tgl_lahir: '', nik: '', pendidikan: '', universitas: '', status: '', nama_lembaga: '', sumber_data: '', jumlah_pu: '', pekerjaan: '', pekerjaan_lain: '', asal_unit_kerja: '', pns: false, pns_golongan: ''
                },

                // Object Form yang sedang aktif (Edit/Tambah/Detail)
                formData: {},

                get filteredPendampingList() {
                    if (!this.searchQuery.trim()) {
                        return this.pendampingList;
                    }

                    const query = this.searchQuery.toLowerCase();
                    return this.pendampingList.filter(item =>
                        item.nama.toLowerCase().includes(query) ||
                        item.no_registrasi.toLowerCase().includes(query)
                    );
                },

                init() {
                    this.formData = { ...this.emptyForm };
                },

                openModal(type, mode = 'add', data = null) {
                    this.activeModal = type;
                    document.body.style.overflow = 'hidden';

                    if (type === 'form') {
                        this.formMode = mode;
                        if (mode === 'edit' && data) {
                            this.formData = { ...data };
                        } else {
                            this.formData = { ...this.emptyForm };
                        }
                    } else if (type === 'detail' && data) {
                        this.formData = { ...data };
                    } else if (type === 'import') {
                        this.importFile = null;
                        this.importLoading = false;
                    }
                },

                closeModal() {
                    this.activeModal = '';
                    document.body.style.overflow = '';
                },

                handleFileSelect(event) {
                    this.importFile = event.target.files[0] || null;
                },

                handleFileDrop(event) {
                    this.importDragover = false;
                    this.importFile = event.dataTransfer.files[0] || null;
                },

                async submitImport() {
                    if (!this.importFile) return;

                    this.importLoading = true;
                    const formData = new FormData();
                    formData.append('file', this.importFile);

                    try {
                        const response = await fetch('{{ route('pendamping.import') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                            },
                            body: formData
                        });

                        const result = await response.json();

                        if (result.success) {
                            alert(result.message);
                            this.closeModal();
                            // Reload data dari backend
                            this.loadPendampingData();
                        } else {
                            alert('Error: ' + result.message);
                            if (result.errors && result.errors.length > 0) {
                                console.error('Errors:', result.errors);
                            }
                        }
                    } catch (error) {
                        alert('Gagal mengimport: ' + error.message);
                    } finally {
                        this.importLoading = false;
                    }
                },

                async loadPendampingData() {
                    // TODO: Implement fetch data from backend
                    // Untuk sekarang, gunakan dummy data
                    console.log('Load pendamping data dari backend');
                },

                submitForm() {
                    console.log('Data Disimpan:', this.formData);
                    // Panggil fungsi API/Laravel di sini
                    this.closeModal();
                }
            }
        }
    </script>
@endsection 