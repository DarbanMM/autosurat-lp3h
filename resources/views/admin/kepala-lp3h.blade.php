@extends('layouts.admin')

@section('title', 'Data Kepala LP3H')

@section('content')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

    <div x-data="kepalaManager()" x-init="init()">
        
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-brand">Data Kepala LP3H</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola informasi profil dan tanda tangan digital Kepala Lembaga.</p>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden max-w-3xl relative">
            
            <div x-show="isLoading" class="absolute inset-0 bg-white bg-opacity-75 z-10 flex items-center justify-center">
                <span class="text-gray-500 font-medium">Memuat data...</span>
            </div>

            <div class="p-6 sm:p-8 space-y-6" x-show="!isLoading">
                
                <div class="grid grid-cols-1 sm:grid-cols-[max-content_auto_1fr] gap-y-4 gap-x-2 sm:gap-x-4 text-sm items-center sm:items-start">
                    
                    <div class="font-bold text-gray-600">NIP</div>
                    <div class="hidden sm:block font-bold text-gray-600">:</div>
                    <div class="font-medium text-gray-900">
                        <span x-text="dataAsli.nip || '-'"></span>
                    </div>

                    <div class="font-bold text-gray-600 mt-2 sm:mt-0">Nama</div>
                    <div class="hidden sm:block font-bold text-gray-600">:</div>
                    <div class="font-medium text-gray-900">
                        <span x-text="dataAsli.nama || '-'"></span>
                    </div>

                    <div class="font-bold text-gray-600 mt-2 sm:mt-0">Jabatan</div>
                    <div class="hidden sm:block font-bold text-gray-600">:</div>
                    <div class="font-medium text-gray-900">
                        <span x-text="dataAsli.jabatan || '-'"></span>
                    </div>

                    <div class="font-bold text-gray-600 mt-2 sm:mt-0">Tanda Tangan Digital / Barcode</div>
                    <div class="hidden sm:block font-bold text-gray-600">:</div>
                    <div class="font-medium text-gray-900">
                        <div class="flex items-center gap-2">
                            <template x-if="dataAsli.barcode_ttd">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                    <a :href="dataAsli.ttd_url" target="_blank" class="text-brand font-mono bg-purple-50 px-2 py-0.5 rounded border border-purple-100 hover:underline" x-text="dataAsli.barcode_ttd"></a>
                                </div>
                            </template>
                            <template x-if="!dataAsli.barcode_ttd">
                                <span class="text-gray-400 italic">Belum ada file diunggah</span>
                            </template>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-200">

                <div class="flex justify-end">
                    <button @click="openEditModal()" class="bg-brand hover:bg-brand-dark text-white font-semibold py-2.5 px-6 rounded-lg shadow-sm transition-all duration-200 flex items-center gap-2 text-sm focus:ring-4 focus:ring-purple-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Edit Data
                    </button>
                </div>

            </div>
        </div>


        <div x-show="isEditModalOpen" style="display: none;" class="fixed inset-0 z-40 bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity duration-300">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg flex flex-col max-h-[95vh] overflow-hidden">
                
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-900">Edit Data Kepala LP3H</h3>
                    <button @click="closeEditModal()" class="text-gray-400 hover:text-gray-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <form action="#" method="POST" class="flex flex-col flex-1 overflow-hidden" @submit.prevent="submitForm">
                    <div class="p-6 overflow-y-auto flex-1 max-h-[70vh] bg-gray-50/50 space-y-5">
                        
                        <div>
                            <label class="block mb-2 text-sm font-semibold text-gray-900">NIP</label>
                            <input type="text" x-model="formData.nip" required 
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-purple-200 focus:border-brand block w-full p-3 outline-none transition-all" 
                                placeholder="Masukkan NIP">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-gray-900">Nama</label>
                            <input type="text" x-model="formData.nama" required 
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-purple-200 focus:border-brand block w-full p-3 outline-none transition-all" 
                                placeholder="Masukkan Nama Lengkap beserta Gelar">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-gray-900">Jabatan</label>
                            <input type="text" x-model="formData.jabatan" required 
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-2 focus:ring-purple-200 focus:border-brand block w-full p-3 outline-none transition-all" 
                                placeholder="Masukkan Jabatan">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-gray-900">Tanda Tangan Digital / Barcode (1:1)</label>
                            
                            <div class="mt-1 flex items-center gap-4">
                                <div class="w-16 h-16 rounded border border-gray-300 bg-white flex items-center justify-center overflow-hidden flex-shrink-0 shadow-sm p-1">
                                    <template x-if="formData.ttd_preview">
                                        <img :src="formData.ttd_preview" class="w-full h-full object-contain">
                                    </template>
                                    <template x-if="!formData.ttd_preview">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </template>
                                </div>
                                
                                <div class="flex-1">
                                    <input type="file" id="fileUpload" accept=".jpg, .jpeg, .png, .svg" class="hidden" @change="handleFileSelect">
                                    <button type="button" @click="document.getElementById('fileUpload').click()" 
                                        class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2 px-4 rounded shadow-sm text-xs transition-colors flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        Pilih Gambar Baru
                                    </button>
                                    <p class="text-xs text-gray-500 mt-1.5 leading-relaxed" x-text="formData.ttd_name ? 'File terpilih: ' + formData.ttd_name : 'Format: JPG, JPEG, PNG, SVG.'"></p>
                                </div>
                            </div>
                        </div>

                    </div>
                    
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3 flex-shrink-0">
                        <button type="button" @click="closeEditModal()" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">
                            Batalkan
                        </button>
                        <button type="submit" :disabled="isSubmitting" class="px-5 py-2.5 text-sm font-medium text-white bg-brand rounded-lg hover:bg-brand-dark shadow-sm transition-colors disabled:opacity-50">
                            <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="isCropModalOpen" style="display: none;" class="fixed inset-0 z-50 bg-gray-900 bg-opacity-90 flex items-center justify-center p-4 transition-opacity duration-300">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl flex flex-col overflow-hidden">
                
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Sesuaikan Ukuran (Rasio 1:1)</h3>
                    <button type="button" @click="closeCropModal()" class="text-gray-400 hover:text-gray-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div class="p-4 bg-gray-100 h-[50vh] sm:h-[60vh] flex items-center justify-center">
                    <div class="w-full h-full max-h-full">
                        <img x-ref="imageToCrop" src="" class="max-w-full block">
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
                    <button type="button" @click="closeCropModal()" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">
                        Batal
                    </button>
                    <button type="button" @click="confirmCrop()" class="px-5 py-2.5 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 shadow-sm transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Konfirmasi Potongan
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script>
        function kepalaManager() {
            return {
                isLoading: false,
                isSubmitting: false,
                isEditModalOpen: false,
                isCropModalOpen: false,
                cropperInstance: null,
                selectedFileName: '',
                
                // State Data Saat Ini
                dataAsli: {
                    nip: '',
                    nama: '',
                    jabatan: '',
                    barcode_ttd: '',
                    ttd_url: null 
                },

                // State Form yang Sedang Diedit
                formData: {
                    nip: '',
                    nama: '',
                    jabatan: '',
                    ttd_name: '',
                    ttd_preview: null
                },

                init() {
                    this.loadData();
                },

                csrfHeaders() {
                    return {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    };
                },

                async loadData() {
                    this.isLoading = true;
                    try {
                        const response = await fetch('{{ route("kepala-lp3h.data") }}', {
                            headers: this.csrfHeaders()
                        });
                        const result = await response.json();
                        
                        if (result.success && result.data) {
                            this.dataAsli = {
                                nip: result.data.nip,
                                nama: result.data.nama,
                                jabatan: result.data.jabatan,
                                barcode_ttd: result.data.barcode_ttd,
                                ttd_url: result.data.ttd_url
                            };
                        }
                    } catch (error) {
                        console.error('Error fetching data:', error);
                    } finally {
                        this.isLoading = false;
                    }
                },

                openEditModal() {
                    this.formData.nip = this.dataAsli.nip || '';
                    this.formData.nama = this.dataAsli.nama || '';
                    this.formData.jabatan = this.dataAsli.jabatan || '';
                    this.formData.ttd_name = this.dataAsli.barcode_ttd || '';
                    this.formData.ttd_preview = this.dataAsli.ttd_url || null;
                    
                    this.isEditModalOpen = true;
                    document.body.style.overflow = 'hidden';
                },

                closeEditModal() {
                    this.isEditModalOpen = false;
                    document.body.style.overflow = '';
                    // Reset input file agar bisa memilih file yang sama lagi jika batal
                    document.getElementById('fileUpload').value = ''; 
                },

                handleFileSelect(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    this.selectedFileName = file.name;
                    
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        // Buka modal Cropper dulu agar div-nya tampil
                        this.isCropModalOpen = true;
                        
                        const img = this.$refs.imageToCrop;
                        
                        // Tunggu gambar selesai dimuat oleh browser
                        img.onload = () => {
                            // Beri sedikit jeda agar animasi transisi Alpine selesai (modal fully visible)
                            setTimeout(() => {
                                if (this.cropperInstance) {
                                    this.cropperInstance.destroy();
                                }
                                
                                this.cropperInstance = new Cropper(img, {
                                    aspectRatio: 1 / 1, // Kunci rasio 1:1 (Persegi)
                                    viewMode: 1, // Membatasi area crop tidak melebihi ukuran gambar asli
                                    background: false,
                                    autoCropArea: 0.8,
                                    responsive: true,
                                });
                            }, 150); // 150ms cukup untuk memastikan elemen sudah dirender penuh
                        };
                        
                        // Setel src setelah event handler onload dipasang
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                },

                closeCropModal() {
                    this.isCropModalOpen = false;
                    if (this.cropperInstance) {
                        this.cropperInstance.destroy();
                        this.cropperInstance = null;
                    }
                    document.getElementById('fileUpload').value = ''; 
                },

                // Logic Saat Tombol Konfirmasi Crop Ditekan
                confirmCrop() {
                    if (!this.cropperInstance) return;

                    // Mengambil hasil crop ke dalam bentuk Base64 Data URL
                    const canvas = this.cropperInstance.getCroppedCanvas({
                        width: 500, // Standar resolusi output
                        height: 500,
                    });
                    
                    const croppedBase64 = canvas.toDataURL('image/png');
                    
                    // Simpan ke form data
                    this.formData.ttd_preview = croppedBase64;
                    this.formData.ttd_name = this.selectedFileName;
                    
                    // Tutup modal cropper, form edit tetap menyala di belakang
                    this.closeCropModal();
                },

                async submitForm() {
                    this.isSubmitting = true;

                    try {
                        // Post form data to API
                        const payload = {
                            nip: this.formData.nip,
                            nama: this.formData.nama,
                            jabatan: this.formData.jabatan,
                            ttd_name: this.formData.ttd_name,
                        };

                        // Send image base64 if a NEW image was cropped (base64 string starts with data:image)
                        if (this.formData.ttd_preview && this.formData.ttd_preview.startsWith('data:image')) {
                            payload.ttd_preview = this.formData.ttd_preview;
                        }

                        const response = await fetch('{{ route("kepala-lp3h.update") }}', {
                            method: 'POST',
                            headers: {
                                ...this.csrfHeaders(),
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        });

                        const result = await response.json();

                        if (!response.ok || !result.success) {
                            throw new Error(result.message || 'Gagal menyimpan data.');
                        }

                        // Success
                        alert(result.message);
                        this.loadData();
                        this.closeEditModal();

                    } catch (error) {
                        alert('Error: ' + error.message);
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }
        }
    </script>
@endsection