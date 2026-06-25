@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-8 bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:p-8 relative overflow-hidden">
        <div class="relative z-10">
            <h2 class="text-3xl font-extrabold text-brand mb-2">Selamat Datang di Dashboard!</h2>
            <p class="text-gray-600 max-w-2xl">
                Ini adalah pusat kendali untuk mengelola dan memonitor pembuatan seluruh dokumen AutoSurat LP3H. Pilih menu di sebelah kiri atau gunakan aksi cepat di bawah untuk mulai mengelola surat.
            </p>
        </div>
        <div class="absolute right-0 top-0 h-full w-32 bg-brand opacity-5 transform skew-x-12 translate-x-10"></div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 border-l-4 border-l-brand hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-purple-50 text-brand">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total Daftar Surat</h3>
                    <span class="text-2xl font-black text-gray-800">{{ $totalDaftarSurat }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 border-l-4 border-l-brand hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-purple-50 text-brand">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">Riwayat Keluar</h3>
                    <span class="text-2xl font-black text-gray-800">{{ $totalRiwayat }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 border-l-4 border-l-brand hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-purple-50 text-brand">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total User</h3>
                    <span class="text-2xl font-black text-gray-800">{{ $totalUser }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-800">Aksi Cepat</h3>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            
            <a href="/format-nomor-surat" class="group flex flex-col items-center justify-center p-6 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200 hover:bg-brand hover:border-brand hover:shadow-lg transition-all duration-300">
                <div class="bg-white p-3 rounded-full shadow-sm mb-3 group-hover:bg-brand-light transition-colors">
                    <svg class="w-6 h-6 text-brand group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"></path></svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 group-hover:text-white transition-colors">Ubah Nomor Surat</span>
            </a>
            
            <a href="/daftar-surat" class="group flex flex-col items-center justify-center p-6 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200 hover:bg-brand hover:border-brand hover:shadow-lg transition-all duration-300">
                <div class="bg-white p-3 rounded-full shadow-sm mb-3 group-hover:bg-brand-light transition-colors">
                    <svg class="w-6 h-6 text-brand group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"></path></svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 group-hover:text-white transition-colors">Ubah Surat</span>
            </a>
            
            <a href="/riwayat-surat" class="group flex flex-col items-center justify-center p-6 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200 hover:bg-brand hover:border-brand hover:shadow-lg transition-all duration-300">
                <div class="bg-white p-3 rounded-full shadow-sm mb-3 group-hover:bg-brand-light transition-colors">
                    <svg class="w-6 h-6 text-brand group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7"></path></svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 group-hover:text-white transition-colors">Lihat Histori Surat</span>
            </a>

        </div>
    </div>
@endsection