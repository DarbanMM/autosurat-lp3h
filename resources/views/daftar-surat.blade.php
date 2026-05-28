@extends('layouts.admin')

@section('title', 'Daftar Surat')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-brand">Daftar Surat</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola daftar templat surat dan format penomorannya.</p>
        </div>
        
        <button onclick="openModal('add')" class="bg-brand hover:bg-brand-dark text-white font-semibold py-2.5 px-5 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2 text-sm focus:ring-4 focus:ring-purple-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Surat
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-4 w-16 text-center font-bold">No</th>
                        <th scope="col" class="px-6 py-4 font-bold">Nama Surat</th>
                        <th scope="col" class="px-6 py-4 font-bold">Nomor Surat</th>
                        <th scope="col" class="px-6 py-4 w-40 text-center font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50 transition-colors bg-white">
                        <td class="px-6 py-4 text-center font-medium text-gray-900">1</td>
                        <td class="px-6 py-4 font-semibold text-gray-800">Surat Pengantar Kegiatan P3H</td>
                        <td class="px-6 py-4">
                            <span class="bg-purple-100 text-brand text-xs font-semibold px-3 py-1.5 rounded-md border border-purple-200">
                                001/LP3H/SU/2026
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openModal('edit', 1, 'Surat Pengantar Kegiatan P3H', '001/LP3H/SU/2026')" class="inline-flex items-center justify-center px-3 py-1.5 text-sm font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 focus:ring-2 focus:ring-blue-300 transition-all">
                                    Edit
                                </button>
                                <button class="inline-flex items-center justify-center px-3 py-1.5 text-sm font-medium text-red-700 bg-red-100 rounded-lg hover:bg-red-200 focus:ring-2 focus:ring-red-300 transition-all">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    
                    <tr class="hover:bg-gray-50 transition-colors bg-white">
                        <td class="px-6 py-4 text-center font-medium text-gray-900">2</td>
                        <td class="px-6 py-4 font-semibold text-gray-800">Surat Tugas Pendampingan Lapangan</td>
                        <td class="px-6 py-4">
                            <span class="bg-purple-100 text-brand text-xs font-semibold px-3 py-1.5 rounded-md border border-purple-200">
                                002/LP3H/ST/2026
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openModal('edit', 2, 'Surat Tugas Pendampingan Lapangan', '002/LP3H/ST/2026')" class="inline-flex items-center justify-center px-3 py-1.5 text-sm font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 focus:ring-2 focus:ring-blue-300 transition-all">
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

    <div id="letterModal" class="fixed inset-0 z-50 hidden bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity duration-300">
        
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg flex flex-col max-h-[90vh] overflow-hidden transform transition-all scale-100">
            
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
                <h3 id="modalTitle" class="text-lg font-bold text-gray-900">Surat Baru</h3>
                <button onclick="closeModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center transition-colors">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </button>
            </div>
            
            <form id="formSurat" action="#" method="POST" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <input type="hidden" id="letter_id" name="id">
                
                <div class="p-6 space-y-6 overflow-y-auto flex-1 max-h-[60vh]">
                    
                    <div>
                        <label for="nama_surat" class="block mb-2 text-sm font-semibold text-gray-900">Nama Surat</label>
                        <input type="text" id="nama_surat" name="nama_surat" required 
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-purple-200 focus:border-brand block w-full p-3 outline-none transition-all" 
                            placeholder="Contoh: Surat Tugas Pendampingan">
                    </div>
                    
                    <div>
                        <label for="nomor_surat" class="block mb-2 text-sm font-semibold text-gray-900">Nomor Surat</label>
                        <div class="relative">
                            <select id="nomor_surat" name="nomor_surat" required 
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-purple-200 focus:border-brand block w-full p-3 outline-none transition-all appearance-none pr-10">
                                <option value="" disabled selected>Pilih format nomor surat yang tersedia...</option>
                                <option value="001/LP3H/SU/2026">001/LP3H/SU/2026</option>
                                <option value="002/LP3H/ST/2026">002/LP3H/ST/2026</option>
                                <option value="003/LP3H/SK/2026">003/LP3H/SK/2026</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>

                </div>
                
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end items-center gap-3 flex-shrink-0">
                    <button type="button" onclick="closeModal()" 
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
        function openModal(mode, id = '', nama = '', nomor = '') {
            const modal = document.getElementById('letterModal');
            const title = document.getElementById('modalTitle');
            const form = document.getElementById('formSurat');
            
            const inputId = document.getElementById('letter_id');
            const inputNama = document.getElementById('nama_surat');
            const inputNomor = document.getElementById('nomor_surat');
            
            if (mode === 'add') {
                title.innerText = 'Surat Baru';
                form.reset();
                inputId.value = '';
            } else if (mode === 'edit') {
                title.innerText = 'Edit Surat';
                inputId.value = id;
                inputNama.value = nama;
                inputNomor.value = nomor;
            }
            
            // Hapus class hidden dan paksa class flex agar modal muncul di tengah
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden'; 
        }
        
        function closeModal() {
            const modal = document.getElementById('letterModal');
            
            // Sembunyikan modal kembali
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = ''; 
        }

        // Menutup modal jika area background (luar form) diklik
        window.onclick = function(event) {
            const modal = document.getElementById('letterModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
@endsection