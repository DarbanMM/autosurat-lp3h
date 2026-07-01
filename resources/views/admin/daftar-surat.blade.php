@extends('layouts.admin')

@section('title', 'Daftar Surat')

@section('content')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <div x-data="daftarSuratManager()" x-init="init()">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-brand">Daftar Surat</h2>
                <p class="text-sm text-gray-500 mt-1">Kelola daftar surat beserta deskripsi penggunaannya.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <input type="text" x-model.debounce.500ms="searchQuery" placeholder="Cari nama atau nomor surat..." class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-brand focus:border-transparent shadow-sm transition-all w-64" />
                
                <button @click="openModal('add')" class="bg-brand hover:bg-brand-dark text-white font-semibold py-2.5 px-5 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2 text-sm focus:ring-4 focus:ring-purple-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Surat
                </button>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-4 w-16 text-center font-bold">No</th>
                            <th scope="col" class="px-6 py-4 font-bold w-1/4">Nama Surat</th>
                            <th scope="col" class="px-6 py-4 font-bold">Nomor Surat</th>
                            <th scope="col" class="px-6 py-4 font-bold w-1/3 text-left">Keterangan</th>
                            <th scope="col" class="px-6 py-4 w-40 text-center font-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        
                        <tr x-show="listLoading">
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Memuat data...</td>
                        </tr>
                        
                        <tr x-show="!listLoading && items.length === 0">
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada daftar surat.</td>
                        </tr>

                        <template x-for="(item, index) in items" :key="item.id_surat">
                            <tr class="hover:bg-gray-50 transition-colors bg-white align-top">
                                <td class="px-6 py-4 text-center font-medium text-gray-900" x-text="(pagination.current_page - 1) * limit + index + 1"></td>
                                <td class="px-6 py-4 font-semibold text-gray-800" x-text="item.nama_surat"></td>
                                <td class="px-6 py-4">
                                    <span x-show="item.format_nomor_surat" class="bg-purple-100 text-brand text-xs font-semibold px-3 py-1.5 rounded-md border border-purple-200 whitespace-nowrap" x-text="item.format_nomor_surat ? item.format_nomor_surat.display_format : ''"></span>
                                    <span x-show="!item.format_nomor_surat" class="text-gray-400 italic text-xs">Belum diatur</span>
                                </td>
                                <td class="px-6 py-4 text-gray-700 leading-relaxed whitespace-pre-line" x-text="item.keterangan || '-'"></td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="openModal('edit', item)" class="inline-flex items-center justify-center px-3 py-1.5 text-sm font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 focus:ring-2 focus:ring-blue-300 transition-all">
                                            Edit
                                        </button>
                                        <button @click="confirmDelete(item.id_surat)" class="inline-flex items-center justify-center px-3 py-1.5 text-sm font-medium text-red-700 bg-red-100 rounded-lg hover:bg-red-200 focus:ring-2 focus:ring-red-300 transition-all">
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

        <!-- Form Modal -->
        <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity duration-300">
            <div @click.away="closeModal()" class="bg-white rounded-xl shadow-2xl w-full max-w-lg flex flex-col max-h-[90vh] overflow-hidden transform transition-all scale-100">
                
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-900" x-text="formMode === 'add' ? 'Surat Baru' : 'Edit Surat'"></h3>
                    <button @click="closeModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition-colors">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>
                
                <form @submit.prevent="submitForm" class="flex flex-col flex-1 overflow-hidden">
                    
                    <div class="p-6 space-y-5 overflow-y-auto flex-1 max-h-[58vh]">
                        
                        <div>
                            <label class="block mb-2 text-sm font-semibold text-gray-900">Nama Surat</label>
                            <input type="text" x-model="formData.nama_surat" required 
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-purple-200 focus:border-brand block w-full p-3 outline-none transition-all" 
                                placeholder="Contoh: Surat Tugas Lapangan">
                        </div>
                        
                        <div>
                            <label class="block mb-2 text-sm font-semibold text-gray-900">Nomor Surat</label>
                            <div class="relative">
                                <select x-model="formData.id_format_surat"
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-purple-200 focus:border-brand block w-full p-3 outline-none transition-all appearance-none pr-10">
                                    <option value="" selected>Pilih format nomor surat yang tersedia...</option>
                                    <template x-for="fmt in formatOptions" :key="fmt.id_format_nomor">
                                        <option :value="fmt.id_format_nomor" x-text="fmt.display_format"></option>
                                    </template>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-gray-900">Keterangan Surat</label>
                            <textarea x-model="formData.keterangan" rows="4" 
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-purple-200 focus:border-brand block w-full p-3 outline-none transition-all resize-y" 
                                placeholder="Tuliskan penjelasan mengenai fungsi surat ini..."></textarea>
                        </div>

                    </div>
                    
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end items-center gap-3 flex-shrink-0">
                        <button type="button" @click="closeModal()" 
                            class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 transition-colors">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSaving"
                            class="px-5 py-2.5 text-sm font-medium text-white bg-brand rounded-lg hover:bg-brand-dark focus:ring-4 focus:outline-none focus:ring-purple-300 shadow-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-text="isSaving ? 'Menyimpan...' : 'Simpan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function daftarSuratManager() {
            return {
                items: [],
                formatOptions: [],
                searchQuery: '',

                listLoading: false,
                isModalOpen: false,
                formMode: 'add',
                isSaving: false,

                formData: {
                    id_surat: '',
                    nama_surat: '',
                    id_format_surat: '',
                    keterangan: ''
                },

                init() {
                    this.$watch('searchQuery', () => {
                        this.loadData();
                    });
                    
                    this.loadData();
                    this.loadFormats();
                },

                headers() {
                    return {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    };
                },

                async loadData() {
                    this.listLoading = true;
                    try {
                        const params = new URLSearchParams({
                            search: this.searchQuery
                        });
                        const response = await fetch(`{{ route('daftar-surat.data') }}?${params.toString()}`, {
                            headers: this.headers()
                        });
                        if (!response.ok) throw new Error('Gagal memuat data');
                        const result = await response.json();
                        this.items = result.data || [];
                    } catch (error) {
                        console.error(error);
                        alert('Error: ' + error.message);
                    } finally {
                        this.listLoading = false;
                    }
                },

                async loadFormats() {
                    try {
                        const response = await fetch(`{{ route('daftar-surat.formats') }}`, {
                            headers: this.headers()
                        });
                        if (!response.ok) throw new Error('Gagal memuat format nomor');
                        const result = await response.json();
                        this.formatOptions = result.data || [];
                    } catch (error) {
                        console.error(error);
                    }
                },

                openModal(mode, item = null) {
                    this.formMode = mode;
                    if (mode === 'edit' && item) {
                        this.formData = {
                            id_surat: item.id_surat,
                            nama_surat: item.nama_surat,
                            id_format_surat: item.id_format_surat || '',
                            keterangan: item.keterangan || ''
                        };
                    } else {
                        this.formData = {
                            id_surat: '',
                            nama_surat: '',
                            id_format_surat: '',
                            keterangan: ''
                        };
                    }
                    this.isModalOpen = true;
                    document.body.style.overflow = 'hidden';
                },

                closeModal() {
                    this.isModalOpen = false;
                    document.body.style.overflow = '';
                },

                async submitForm() {
                    this.isSaving = true;
                    try {
                        const isEdit = this.formMode === 'edit';
                        const url = isEdit 
                            ? `{{ url('/daftar-surat') }}/${this.formData.id_surat}`
                            : `{{ route('daftar-surat.store') }}`;
                        
                        const method = isEdit ? 'PUT' : 'POST';

                        const response = await fetch(url, {
                            method: method,
                            headers: this.headers(),
                            body: JSON.stringify(this.formData)
                        });

                        const result = await response.json();
                        if (!response.ok) throw new Error(result.message || 'Gagal menyimpan data');
                        
                        this.closeModal();
                        this.loadData();
                    } catch (error) {
                        alert('Error: ' + error.message);
                    } finally {
                        this.isSaving = false;
                    }
                },

                async confirmDelete(id) {
                    if (!confirm('Apakah Anda yakin ingin menghapus surat ini?')) return;

                    try {
                        const response = await fetch(`{{ url('/daftar-surat') }}/${id}`, {
                            method: 'DELETE',
                            headers: this.headers()
                        });
                        
                        const result = await response.json();
                        if (!response.ok) throw new Error(result.message || 'Gagal menghapus data');
                        
                        this.loadData();
                    } catch (error) {
                        alert('Error: ' + error.message);
                    }
                }
            }
        }
    </script>
@endsection