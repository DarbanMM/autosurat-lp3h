<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 hidden lg:hidden transition-opacity" onclick="toggleSidebar()"></div>

<aside id="sidebar" class="bg-brand text-white w-64 flex-shrink-0 fixed inset-y-0 left-0 transform -translate-x-full lg:relative lg:translate-x-0 transition-transform duration-300 ease-in-out z-30 shadow-lg flex flex-col">
    <div class="h-16 flex items-center justify-center border-b border-brand-dark px-4">
        <h1 class="text-xl font-bold tracking-wider truncate">AutoSurat LP3H</h1>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <a href="/dashboard" class="flex items-center py-3 px-4 rounded {{ Route::is('dashboard') ? 'bg-brand-dark text-white font-medium' : 'text-gray-200 hover:text-white hover:bg-brand-dark' }} transition duration-200">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            Dashboard
        </a>

        <div class="h-2"></div>

        <a href="/daftar-surat" class="flex items-center py-2.5 px-4 rounded transition duration-200 {{ Route::is('daftar-surat') ? 'bg-brand-dark text-white font-medium' : 'text-gray-200 hover:text-white hover:bg-brand-dark' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            Daftar Surat
        </a>

        <a href="/format-nomor-surat" class="flex items-center py-2.5 px-4 rounded transition duration-200 {{ Route::is('format-nomor-surat') ? 'bg-brand-dark text-white font-medium' : 'text-gray-200 hover:text-white hover:bg-brand-dark' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
            Format Nomor Surat
        </a>

        <a href="/riwayat-surat" class="flex items-center py-2.5 px-4 rounded transition duration-200 {{ Route::is('riwayat-surat') ? 'bg-brand-dark text-white font-medium' : 'text-gray-200 hover:text-white hover:bg-brand-dark' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Riwayat Surat Keluar
        </a>

        <a href="/pendamping" class="flex items-center py-2.5 px-4 rounded transition duration-200 {{ Route::is('pendamping') ? 'bg-brand-dark text-white font-medium' : 'text-gray-200 hover:text-white hover:bg-brand-dark' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Pendamping
        </a>

        <a href="/user" class="flex items-center py-2.5 px-4 rounded transition duration-200 {{ Route::is('user') ? 'bg-brand-dark text-white font-medium' : 'text-gray-200 hover:text-white hover:bg-brand-dark' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            User
        </a>

        <a href="/kepala-lp3h" class="flex items-center py-2.5 px-4 rounded transition duration-200 {{ Route::is('kepala-lp3h') ? 'bg-brand-dark text-white font-medium' : 'text-gray-200 hover:text-white hover:bg-brand-dark' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
            Kepala LP3H
        </a>

        <a href="/profil" class="flex items-center py-2.5 px-4 rounded transition duration-200 {{ Route::is('profil') ? 'bg-brand-dark text-white font-medium' : 'text-gray-200 hover:text-white hover:bg-brand-dark' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            Profil
        </a>

        <a href="/buat-surat" class="flex items-center py-2.5 px-4 rounded transition duration-200 {{ Route::is('buat-surat') ? 'bg-brand-dark text-white font-medium' : 'text-gray-200 hover:text-white hover:bg-brand-dark' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            Buat Surat
        </a>
    </nav>

    <div class="border-t border-brand-dark p-4">
        <form method="POST" action="/logout">
            @csrf
            <button type="submit" class="flex items-center justify-center w-full py-2 px-4 rounded bg-red-500 hover:bg-red-600 text-white transition duration-200 font-medium shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Keluar
            </button>
        </form>
    </div>
</aside>