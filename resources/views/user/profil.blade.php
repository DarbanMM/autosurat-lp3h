@extends('layouts.user')

@section('title', 'Profil Pendamping')

@section('content')

    <div class="mb-8 rounded-2xl shadow-lg p-6 lg:p-10 relative overflow-hidden text-white" 
         style="background: linear-gradient(135deg, #670075, #851a94);">
        <div class="relative z-10">
            <h2 class="text-2xl sm:text-3xl font-extrabold mb-2 text-white">Selamat Datang di AutoSurat LP3H</h2>
            <p class="text-purple-100 max-w-2xl text-sm lg:text-base leading-relaxed opacity-95">
                Ini adalah portal pendamping untuk mengelola profil dan mencetak dokumen surat secara otomatis. Pastikan data diri Anda di bawah ini sudah sesuai.
            </p>
        </div>
        <div class="absolute right-0 top-0 h-full w-48 bg-white opacity-5 transform skew-x-12 translate-x-10"></div>
    </div>

    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
        
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/80">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-[#670075]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                Informasi Data Diri
            </h3>
        </div>

        <div class="p-6 sm:p-8">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                
                <div class="border-b border-gray-100 pb-4">
                    <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Nama Lengkap</dt>
                    <dd class="text-base font-bold text-gray-900">Agus Susanto</dd>
                </div>

                <div class="border-b border-gray-100 pb-4">
                    <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Nomor Register</dt>
                    <dd class="text-base font-mono font-bold text-[#670075] bg-purple-50 inline-block px-3 py-1 rounded border border-purple-100">
                        2209000871
                    </dd>
                </div>

                <div class="border-b border-gray-100 pb-4 md:col-span-2">
                    <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Alamat Domisili</dt>
                    <dd class="text-base font-medium text-gray-800 leading-relaxed">
                        Jl. Bimosari No 285D, RT 013 RW 003, Tahunan, Umbulharjo, Yogyakarta
                    </dd>
                </div>

                <div class="border-b border-gray-100 pb-4">
                    <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Kecamatan</dt>
                    <dd class="text-base font-medium text-gray-800">Imogiri</dd>
                </div>

                <div class="border-b border-gray-100 pb-4">
                    <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Kabupaten</dt>
                    <dd class="text-base font-medium text-gray-800">KAB. BANTUL</dd>
                </div>

                <div class="border-b border-gray-100 pb-4">
                    <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Provinsi</dt>
                    <dd class="text-base font-medium text-gray-800">DI Yogyakarta</dd>
                </div>

                <div class="border-b border-gray-100 pb-4">
                    <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">No HP / WhatsApp</dt>
                    <dd class="text-base font-medium text-gray-800">83869387080</dd>
                </div>

                <div class="md:col-span-2 mt-2">
                    <dt class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Status Pendamping</dt>
                    <dd>
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold bg-green-100 text-green-800 border border-green-200 shadow-sm">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            DISETUJUI
                        </span>
                    </dd>
                </div>

            </dl>
        </div>
    </div>

@endsection