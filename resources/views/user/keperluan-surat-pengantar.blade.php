@extends('layouts.user')

@section('title', 'Keperluan Surat Pengantar Kegiatan')

@section('content')
    <div class="max-w-2xl">
        <h2 class="text-3xl font-bold text-gray-950 mb-2">Keperluan Surat</h2>
        <p class="text-gray-600 mb-6">Mengonfigurasi dokumen: <span class="font-semibold text-brand">Surat Pengantar Kegiatan Instansi</span></p>

        <p class="text-gray-600 mb-8">Silakan isi parameter keperluan di bawah ini untuk merender draft dokumen.</p>

        <form class="bg-white rounded-lg shadow p-8">
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-900 mb-2">Tujuan Kepada</label>
                <input type="text" placeholder="Contoh: Kepala Dinas Perindustrian" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent">
            </div>

            <div class="mb-8">
                <label class="block text-sm font-semibold text-gray-900 mb-2">Daerah / Wilayah Tujuan</label>
                <input type="text" placeholder="Contoh: Kota Yogyakarta" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent">
            </div>

            <div class="flex justify-between items-center">
                <a href="/buat-surat" class="flex items-center gap-2 text-gray-700 hover:text-brand font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Kembali
                </a>
                <button type="submit" class="flex items-center gap-2 px-6 py-3 bg-brand hover:bg-brand-dark text-white rounded-full font-semibold transition duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    Buat & Unduh Surat
                </button>
            </div>
        </form>
    </div>
@endsection
