<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 hidden lg:hidden transition-opacity" onclick="toggleSidebar()"></div>

<aside id="sidebar" class="bg-brand text-white w-64 flex-shrink-0 fixed inset-y-0 left-0 transform -translate-x-full lg:relative lg:translate-x-0 transition-transform duration-300 ease-in-out z-30 shadow-lg flex flex-col">
    <div class="h-16 flex items-center justify-center border-b border-brand-dark px-4">
        <h1 class="text-xl font-bold tracking-wider truncate">AutoSurat LP3H</h1>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <a href="/" class="flex items-center py-3 px-4 rounded bg-brand-dark text-white font-medium transition duration-200">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            Dashboard
        </a>
        
        <p class="px-4 pt-4 pb-2 text-xs font-semibold text-gray-300 uppercase tracking-wider">Modul Surat</p>
        
        <a href="/surat-pengantar" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-brand-dark text-gray-200 hover:text-white">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Surat Pengantar
        </a>
        <a href="/surat-tugas" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-brand-dark text-gray-200 hover:text-white">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            Surat Tugas
        </a>
        <a href="/surat-keterangan-p3h" class="flex items-center py-2.5 px-4 rounded transition duration-200 hover:bg-brand-dark text-gray-200 hover:text-white">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            SK P3H
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