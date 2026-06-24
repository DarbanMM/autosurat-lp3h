@extends('layouts.admin')

@section('title', 'Daftar User')

@section('content')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <div x-data="userManager()" x-init="init()">
        
        <div class="flex flex-col xl:flex-row xl:items-center justify-between mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-brand">Daftar User</h2>
                <p class="text-sm text-gray-500 mt-1">Kelola akun pengguna, hak akses, dan sinkronisasi data pendamping.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                
                <button @click="openModal('import')" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2.5 px-4 rounded-lg shadow-sm transition-all duration-200 flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Import User
                </button>

                <div class="relative" @click.away="exportDropdown = false">
                    <button @click="exportDropdown = !exportDropdown" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2.5 px-4 rounded-lg shadow-sm transition-all duration-200 flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Export User
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="exportDropdown" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-100 z-10 py-1" style="display: none;">
                        <a :href="exportUrl('csv')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-brand flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm3 1h6v4H7V5z" clip-rule="evenodd"></path></svg>
                            CSV (.csv)
                        </a>
                        <a :href="exportUrl('xlsx')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-brand flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>
                            Excel (.xlsx)
                        </a>
                    </div>
                </div>

                <button @click="syncData()" :disabled="isSyncing" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2.5 px-4 rounded-lg shadow-sm transition-all duration-200 flex items-center gap-2 text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4 text-indigo-600" :class="isSyncing ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    <span x-text="isSyncing ? 'Menyinkronkan...' : 'Sinkronisasi'"></span>
                </button>

                <button @click="openModal('form', 'add')" class="bg-brand hover:bg-brand-dark text-white font-semibold py-2.5 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2 text-sm focus:ring-4 focus:ring-purple-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah User
                </button>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-4 w-16 text-center font-bold">No</th>
                            <th scope="col" class="px-6 py-4 font-bold">Username</th>
                            <th scope="col" class="px-6 py-4 font-bold w-1/4">Password</th>
                            <th scope="col" class="px-6 py-4 font-bold text-center">Role</th>
                            <th scope="col" class="px-6 py-4 w-40 text-center font-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr x-show="listLoading">
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Memuat data...</td>
                        </tr>
                        <tr x-show="!listLoading && sortedUsers.length === 0">
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada data user. Gunakan Sinkronisasi atau tambah user baru.</td>
                        </tr>
                        <template x-for="(user, index) in sortedUsers" :key="user.id">
                            <tr class="hover:bg-gray-50 transition-colors bg-white">
                                <td class="px-6 py-4 text-center font-medium text-gray-900" x-text="index + 1"></td>
                                
                                <td class="px-6 py-4 font-semibold text-gray-800" x-text="user.username"></td>
                                
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="font-mono bg-gray-100 px-2 py-1 rounded text-gray-600 tracking-wider flex-1 max-w-[150px] overflow-hidden text-ellipsis whitespace-nowrap">
                                            <span x-show="!user.has_password" class="text-gray-400 italic text-xs tracking-normal">Belum di-assign</span>
                                            <span x-show="user.has_password">••••••••</span>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 text-xs font-bold uppercase rounded-md border"
                                        :class="user.role === 'admin' ? 'bg-purple-100 text-brand border-purple-200' : 'bg-gray-100 text-gray-600 border-gray-200'"
                                        x-text="user.role">
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="openModal('form', 'edit', user)" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded hover:bg-blue-200 focus:ring-2 focus:ring-blue-300 transition-all">
                                            Edit
                                        </button>
                                        <button @click="deleteUser(user)" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 rounded hover:bg-red-200 focus:ring-2 focus:ring-red-300 transition-all">
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

        {{-- Form Modal (Add/Edit) --}}
        <div x-show="activeModal === 'form'" style="display: none;" class="fixed inset-0 z-50 bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity duration-300">
            <div @click.away="closeModal()" class="bg-white rounded-xl shadow-2xl w-full max-w-lg flex flex-col max-h-[95vh] overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-900" x-text="formMode === 'add' ? 'User Baru' : 'Edit User'"></h3>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-900"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                
                <form action="#" method="POST" class="flex flex-col flex-1 overflow-hidden" @submit.prevent="submitForm">
                    @csrf
                    <div class="p-6 overflow-y-auto flex-1 max-h-[70vh] bg-gray-50/50 space-y-5">
                        <div>
                            <label class="block mb-2 text-sm font-semibold text-gray-900">Username</label>
                            <input type="text" x-model="formData.username" required 
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-purple-200 focus:border-brand block w-full p-3 outline-none transition-all" 
                                placeholder="Masukkan username unik">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-gray-900" x-text="formMode === 'edit' ? 'Password Baru (Opsional)' : 'Password'"></label>
                            <input type="password" x-model="formData.password" :required="formMode === 'add'" 
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-purple-200 focus:border-brand block w-full p-3 outline-none transition-all" 
                                placeholder="Masukkan password">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-gray-900">Ulangi Isi Password</label>
                            <input type="password" x-model="formData.password_confirm" :required="formMode === 'add'" 
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-purple-200 focus:border-brand block w-full p-3 outline-none transition-all" 
                                placeholder="Ketik ulang password">
                            <p x-show="formData.password && formData.password_confirm && formData.password !== formData.password_confirm" class="text-red-500 text-xs mt-1 font-medium">
                                Password tidak cocok!
                            </p>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-gray-900">Role</label>
                            <div class="relative">
                                <select x-model="formData.role" required 
                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-purple-200 focus:border-brand block w-full p-3 outline-none transition-all appearance-none pr-10">
                                    <option value="" disabled selected>Pilih Role Pengguna...</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">User</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3 flex-shrink-0">
                        <button type="button" @click="closeModal()" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSubmitting || (formData.password && formData.password !== formData.password_confirm)" 
                            class="px-5 py-2.5 text-sm font-medium text-white bg-brand rounded-lg hover:bg-brand-dark shadow-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Import Modal --}}
        <div x-show="activeModal === 'import'" style="display: none;" class="fixed inset-0 z-50 bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity duration-300">
            <div @click.away="closeModal()" class="bg-white rounded-xl shadow-2xl w-full max-w-2xl flex flex-col overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Import Data User</h3>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-900"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                
                <div class="p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Format / Template Nama Kolom:</label>
                        <div class="bg-gray-800 text-green-400 font-mono text-xs p-4 rounded-lg flex flex-wrap gap-2 shadow-inner">
                            <span>username,</span>
                            <span>password,</span>
                            <span>role (Diisi admin atau user)</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Pastikan baris pertama (Header) di Excel/CSV Anda sama persis dengan urutan nama di atas.</p>
                    </div>

                    <div x-show="importLoading" class="space-y-2">
                        <div class="flex justify-between text-xs font-semibold text-gray-600">
                            <span x-text="importStatusText"></span>
                            <span x-text="importProgress + '%'"></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-brand h-2.5 rounded-full transition-all duration-300" :style="'width:' + importProgress + '%'"></div>
                        </div>
                        <p class="text-xs text-gray-500" x-text="importDetailText"></p>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('user.download-template', 'csv') }}" class="flex items-center gap-2 text-sm bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 px-4 py-2 rounded-lg font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Download Template CSV
                        </a>
                        <a href="{{ route('user.download-template', 'xlsx') }}" class="flex items-center gap-2 text-sm bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 px-4 py-2 rounded-lg font-medium transition-colors">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>
                            Download Template Excel
                        </a>
                    </div>

                    <div class="border-2 border-dashed border-gray-300 bg-gray-50 rounded-xl p-8 text-center hover:bg-gray-100 hover:border-brand transition-colors cursor-pointer relative">
                        <input type="file" @change="handleFileSelect" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept=".xlsx, .csv, .xls" x-ref="importFileInput">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <p class="text-sm font-semibold text-gray-700" x-text="importFile ? importFile.name : 'Klik di sini untuk mengunggah file'"></p>
                        <p class="text-xs text-gray-500 mt-1">atau drag & drop file Excel/CSV ke area ini.</p>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
                    <button @click="closeModal()" :disabled="importLoading" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors disabled:opacity-50">
                        Batalkan
                    </button>
                    <button @click="submitImport" :disabled="!importFile || importLoading" class="px-5 py-2.5 text-sm font-medium text-white bg-brand rounded-lg hover:bg-brand-dark shadow-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-text="importLoading ? 'Sedang Import...' : 'Import'"></span>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script>
        function userManager() {
            return {
                activeModal: '',
                formMode: 'add',
                exportDropdown: false,
                isSyncing: false,
                isSubmitting: false,
                listLoading: false,
                importFile: null,
                importLoading: false,
                importProgress: 0,
                importStatusText: '',
                importDetailText: '',

                formData: {
                    id: '',
                    username: '',
                    password: '',
                    password_confirm: '',
                    role: ''
                },

                users: [],

                get sortedUsers() {
                    return [...this.users].sort((a, b) => {
                        // 1. Admin first
                        if (a.role !== b.role) {
                            return a.role === 'admin' ? -1 : 1; 
                        }
                        // 2. Belum di-assign first
                        if (a.has_password !== b.has_password) {
                            return a.has_password ? 1 : -1;
                        }
                        // 3. Username alphabetical
                        return a.username.localeCompare(b.username);
                    });
                },

                init() {
                    this.loadUsers();
                },

                csrfHeaders() {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    return {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    };
                },

                exportUrl(format) {
                    return `{{ url('/user/export') }}/${format}`;
                },

                async loadUsers() {
                    this.listLoading = true;
                    try {
                        const response = await fetch('{{ route("user.data") }}', {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        });
                        if (!response.ok) throw new Error('Gagal memuat data user.');
                        const result = await response.json();
                        this.users = result.data || [];
                    } catch (error) {
                        console.error(error);
                        alert('Gagal memuat daftar user: ' + error.message);
                    } finally {
                        this.listLoading = false;
                    }
                },

                openModal(type, mode = 'add', userData = null) {
                    this.activeModal = type;
                    document.body.style.overflow = 'hidden';

                    if (type === 'form') {
                        this.formMode = mode;
                        if (mode === 'edit' && userData) {
                            this.formData = {
                                id: userData.id,
                                username: userData.username,
                                password: '',
                                password_confirm: '',
                                role: userData.role
                            };
                        } else {
                            this.formData = { id: '', username: '', password: '', password_confirm: '', role: '' };
                        }
                    } else if (type === 'import') {
                        this.importFile = null;
                        this.importLoading = false;
                        this.importProgress = 0;
                        this.importStatusText = '';
                        this.importDetailText = '';
                    }
                },

                closeModal() {
                    if (this.importLoading) return;
                    this.activeModal = '';
                    document.body.style.overflow = '';
                },

                updateImportProgress(result) {
                    this.importProgress = result.progress_percent ?? 0;
                    this.importStatusText = `${result.processed_rows ?? 0} / ${result.total_rows ?? 0} baris`;
                    this.importDetailText = `Berhasil: ${result.imported_count ?? 0}, dilewatkan: ${result.skipped_count ?? 0}`;
                },

                handleFileSelect(event) {
                    this.importFile = event.target.files[0] || null;
                },

                async submitForm() {
                    if (this.formData.password && this.formData.password !== this.formData.password_confirm) {
                        alert("Password tidak cocok!");
                        return;
                    }

                    this.isSubmitting = true;

                    try {
                        let url, method;
                        if (this.formMode === 'edit') {
                            url = `{{ url('/user') }}/${this.formData.id}`;
                            method = 'PUT';
                        } else {
                            url = '{{ route("user.store") }}';
                            method = 'POST';
                        }

                        const body = {
                            username: this.formData.username,
                            role: this.formData.role,
                        };
                        if (this.formData.password) {
                            body.password = this.formData.password;
                        }

                        const response = await fetch(url, {
                            method,
                            headers: {
                                ...this.csrfHeaders(),
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(body),
                        });

                        const result = await response.json();

                        if (!response.ok || !result.success) {
                            throw new Error(result.message || 'Gagal menyimpan user.');
                        }

                        alert(result.message);
                        this.closeModal();
                        this.loadUsers();
                    } catch (error) {
                        alert('Error: ' + error.message);
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                async deleteUser(user) {
                    if (!confirm(`Hapus user "${user.username}"?`)) return;

                    try {
                        const response = await fetch(`{{ url('/user') }}/${user.id}`, {
                            method: 'DELETE',
                            headers: this.csrfHeaders(),
                        });

                        const result = await response.json();

                        if (!response.ok || !result.success) {
                            throw new Error(result.message || 'Gagal menghapus user.');
                        }

                        alert(result.message);
                        this.loadUsers();
                    } catch (error) {
                        alert('Error: ' + error.message);
                    }
                },

                async syncData() {
                    this.isSyncing = true;

                    try {
                        const response = await fetch('{{ route("user.sync") }}', {
                            method: 'POST',
                            headers: this.csrfHeaders(),
                        });

                        const result = await response.json();

                        if (!response.ok || !result.success) {
                            throw new Error(result.message || 'Gagal sinkronisasi.');
                        }

                        alert(result.message);
                        this.loadUsers();
                    } catch (error) {
                        alert('Error: ' + error.message);
                    } finally {
                        this.isSyncing = false;
                    }
                },

                async submitImport() {
                    if (!this.importFile) return;

                    this.importLoading = true;
                    this.importProgress = 0;
                    this.importStatusText = 'Mengunggah file...';
                    this.importDetailText = '';

                    try {
                        const formData = new FormData();
                        formData.append('file', this.importFile);
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        if (csrfToken) formData.append('_token', csrfToken);

                        // Step 1: Prepare (upload file)
                        const prepareResponse = await fetch('{{ route("user.import.prepare") }}', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: formData,
                        });

                        const prepare = await prepareResponse.json();
                        if (!prepareResponse.ok || !prepare.success) {
                            throw new Error(prepare.message || 'Gagal menyiapkan file.');
                        }

                        const importId = prepare.import_id;
                        const totalRows = prepare.total_rows;
                        const chunkSize = prepare.chunk_size;
                        let offset = 0;

                        // Step 2: Process chunks
                        while (offset < totalRows) {
                            this.importStatusText = `Memproses batch ${Math.floor(offset / chunkSize) + 1}...`;

                            const chunkResponse = await fetch(`{{ url('/user/import') }}/${importId}/chunk`, {
                                method: 'POST',
                                headers: {
                                    ...this.csrfHeaders(),
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({ offset }),
                            });

                            const chunk = await chunkResponse.json();
                            if (!chunkResponse.ok || !chunk.success) {
                                throw new Error(chunk.message || 'Gagal memproses batch.');
                            }

                            this.updateImportProgress(chunk);
                            offset += chunkSize;

                            if (chunk.finished) {
                                const detail = (chunk.errors?.length) ? '\n\nDetail:\n' + chunk.errors.join('\n') : '';
                                alert(`Berhasil import ${chunk.imported_count} user.${chunk.skipped_count > 0 ? ' ' + chunk.skipped_count + ' baris dilewatkan.' : ''}${detail}`);
                                this.importLoading = false;
                                this.closeModal();
                                this.loadUsers();
                                return;
                            }
                        }
                    } catch (error) {
                        alert('Gagal mengimport: ' + error.message);
                    } finally {
                        this.importLoading = false;
                    }
                }
            }
        }
    </script>
@endsection