@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-8 bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:p-8 relative overflow-hidden">
        <div class="relative z-10">
            <h2 class="text-3xl font-extrabold text-brand mb-2">Selamat Datang di Dashboard!</h2>
            <p class="text-gray-600 max-w-2xl">
                Ini adalah pusat kendali untuk mengelola dan memonitor pembuatan seluruh dokumen AutoSurat LP3H. Pilih menu di sebelah kiri untuk mulai membuat surat.
            </p>
        </div>
        <div class="absolute right-0 top-0 h-full w-32 bg-brand opacity-5 transform skew-x-12 translate-x-10"></div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 border-l-4 border-l-brand hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-purple-50 text-brand">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">Surat Pengantar</h3>
                    <span class="text-2xl font-black text-gray-800">12</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 border-l-4 border-l-brand hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-purple-50 text-brand">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">Surat Tugas</h3>
                    <span class="text-2xl font-black text-gray-800">5</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 border-l-4 border-l-brand hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-purple-50 text-brand">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">SK P3H</h3>
                    <span class="text-2xl font-black text-gray-800">28</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-800">Aksi Cepat</h3>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <a href="/surat-pengantar" class="group flex flex-col items-center justify-center p-6 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200 hover:bg-brand hover:border-brand hover:shadow-lg transition-all duration-300">
                <div class="bg-white p-3 rounded-full shadow-sm mb-3 group-hover:bg-brand-light transition-colors">
                    <svg class="w-6 h-6 text-brand group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 group-hover:text-white transition-colors">Buat Surat Pengantar</span>
            </a>
            
            <a href="/surat-tugas" class="group flex flex-col items-center justify-center p-6 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200 hover:bg-brand hover:border-brand hover:shadow-lg transition-all duration-300">
                <div class="bg-white p-3 rounded-full shadow-sm mb-3 group-hover:bg-brand-light transition-colors">
                    <svg class="w-6 h-6 text-brand group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 group-hover:text-white transition-colors">Buat Surat Tugas</span>
            </a>
            
            <a href="/surat-keterangan-p3h" class="group flex flex-col items-center justify-center p-6 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200 hover:bg-brand hover:border-brand hover:shadow-lg transition-all duration-300">
                <div class="bg-white p-3 rounded-full shadow-sm mb-3 group-hover:bg-brand-light transition-colors">
                    <svg class="w-6 h-6 text-brand group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
                <span class="text-sm font-semibold text-gray-700 group-hover:text-white transition-colors">Buat SK P3H</span>
            </a>
        </div>
    </div>
@endsection