@extends('layouts.admin')

@section('title', 'Daftar User')

@section('content')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <div x-data="userManager()">
        
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

                <button @click="syncData()" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2.5 px-4 rounded-lg shadow-sm transition-all duration-200 flex items-center gap-2 text-sm">
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
                        <template x-for="(user, index) in sortedUsers" :key="user.id">
                            <tr class="hover:bg-gray-50 transition-colors bg-white">
                                <td class="px-6 py-4 text-center font-medium text-gray-900" x-text="index + 1"></td>
                                
                                <td class="px-6 py-4 font-semibold text-gray-800" x-text="user.username"></td>
                                
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="font-mono bg-gray-100 px-2 py-1 rounded text-gray-600 tracking-wider flex-1 max-w-[150px] overflow-hidden text-ellipsis whitespace-nowrap">
                                            <span x-show="!user.password" class="text-gray-400 italic text-xs tracking-normal">Belum di-assign</span>
                                            <span x-show="user.password && !user.showPassword">••••••••</span>
                                            <span x-show="user.password && user.showPassword" x-text="user.password"></span>
                                        </div>
                                        
                                        <button x-show="user.password" @click="user.showPassword = !user.showPassword" class="text-gray-500 hover:text-brand focus:outline-none p-1 rounded-md hover:bg-gray-200 transition-colors">
                                            <svg x-show="!user.showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            <svg x-show="user.showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                        </button>
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
                        <button type="submit" :disabled="formData.password && formData.password !== formData.password_confirm" 
                            class="px-5 py-2.5 text-sm font-medium text-white bg-brand rounded-lg hover:bg-brand-dark shadow-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

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
                            <span>role (Diisi Admin atau User)</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Pastikan baris pertama (Header) di Excel/CSV Anda sama persis dengan urutan nama di atas.</p>
                    </div>

                    <div class="flex gap-3">
                        <button class="flex items-center gap-2 text-sm bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 px-4 py-2 rounded-lg font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Download Template Excel
                        </button>
                        <button class="flex items-center gap-2 text-sm bg-gray-100 text-gray-700 border border-gray-300 hover:bg-gray-200 px-4 py-2 rounded-lg font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Download Template CSV
                        </button>
                    </div>

                    <div class="border-2 border-dashed border-gray-300 bg-gray-50 rounded-xl p-8 text-center hover:bg-gray-100 hover:border-brand transition-colors cursor-pointer relative">
                        <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept=".xlsx, .csv">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <p class="text-sm font-semibold text-gray-700">Klik di sini untuk mengunggah file</p>
                        <p class="text-xs text-gray-500 mt-1">atau drag & drop file Excel/CSV ke area ini.</p>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
                    <button @click="closeModal()" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">
                        Batalkan
                    </button>
                    <button class="px-5 py-2.5 text-sm font-medium text-white bg-brand rounded-lg hover:bg-brand-dark shadow-sm transition-colors">
                        Import
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script>
        function userManager() {
            return {
                activeModal: '', // '', 'form', 'import'
                formMode: 'add', // 'add' atau 'edit'
                exportDropdown: false,
                isSyncing: false,

                // Form Object
                formData: {
                    id: '',
                    username: '',
                    password: '',
                    password_confirm: '',
                    role: ''
                },

                // Data Dummy
                users: [
                    { id: 1, username: 'zaky_p3h', password: 'passwordRahasia123', role: 'user', showPassword: false },
                    { id: 2, username: 'admin_utama', password: 'adminpassword', role: 'admin', showPassword: false },
                    { id: 3, username: 'ahmad_hidayat', password: '', role: 'user', showPassword: false },
                    { id: 4, username: 'budi_santoso', password: 'userbudi12', role: 'user', showPassword: false },
                    { id: 5, username: 'admin_cabang', password: '', role: 'admin', showPassword: false },
                    { id: 6, username: 'citra_ayu', password: '', role: 'user', showPassword: false }
                ],

                // GETTER SORTING: 1. Role Admin atas, 2. Belum Assign Password, 3. Username Alphabet
                get sortedUsers() {
                    return this.users.sort((a, b) => {
                        if (a.role !== b.role) {
                            return a.role === 'admin' ? -1 : 1; 
                        }

                        const aPassEmpty = !a.password || a.password === '';
                        const bPassEmpty = !b.password || b.password === '';
                        
                        if (aPassEmpty !== bPassEmpty) {
                            return aPassEmpty ? -1 : 1;
                        }

                        return a.username.localeCompare(b.username);
                    });
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
                                password: '', // Reset view password for security
                                password_confirm: '',
                                role: userData.role
                            };
                        } else {
                            this.formData = { id: '', username: '', password: '', password_confirm: '', role: '' };
                        }
                    }
                },

                closeModal() {
                    this.activeModal = '';
                    document.body.style.overflow = '';
                },

                submitForm() {
                    if (this.formData.password !== this.formData.password_confirm) {
                        alert("Password tidak cocok!");
                        return;
                    }
                    console.log('Menyimpan Data User:', this.formData);
                    this.closeModal();
                },

                // Logika Mockup untuk Tombol Sinkronisasi
                syncData() {
                    this.isSyncing = true;
                    
                    // Simulasi loading 1,5 detik seakan mengecek data di tabel Pendamping
                    setTimeout(() => {
                        this.isSyncing = false;
                        
                        // Menambahkan data baru hasil sinkronisasi (Sebagai contoh)
                        // Aturan: No registrasi yang belum ada di tabel user masuk dengan password kosong
                        const newUsersFromPendamping = [
                            { id: 7, username: '10001', password: '', role: 'user', showPassword: false },
                            { id: 8, username: '10002', password: '', role: 'user', showPassword: false }
                        ];

                        this.users = [...this.users, ...newUsersFromPendamping];

                        alert("Sinkronisasi Selesai!\n\nData pendamping dengan nomor registrasi yang belum memiliki akun telah berhasil ditambahkan ke Daftar User (Password dikosongkan).");
                    }, 1500);
                }
            }
        }
    </script>
@endsection