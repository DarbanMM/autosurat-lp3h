<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Pendamping') - AutoSurat LP3H</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { brand: { DEFAULT: '#670075', dark: '#4f0059', light: '#851a94', } } } }
        }
    </script>
</head>
<body class="bg-[#fcfafc] font-sans antialiased overflow-hidden text-gray-800">
    <div class="flex h-screen w-full">

        <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 hidden lg:hidden transition-opacity" onclick="toggleSidebar()"></div>

        <aside id="sidebar" class="bg-white border-r border-gray-200 w-64 flex-shrink-0 fixed inset-y-0 left-0 transform -translate-x-full lg:relative lg:translate-x-0 transition-transform duration-300 ease-in-out z-30 shadow-sm flex flex-col">
            
            <div class="h-20 flex items-center justify-center border-b border-gray-100 px-4 flex-shrink-0 gap-3">
                <div class="w-10 h-10 bg-brand rounded-full flex items-center justify-center text-white font-bold text-xs shadow-inner">
                    UIN
                </div>
                <div class="flex flex-col">
                    <h1 class="text-lg font-extrabold text-gray-900 tracking-wide leading-tight">LP3H</h1>
                    <span class="text-[10px] font-semibold text-brand uppercase tracking-widest">Sunan Kalijaga</span>
                </div>
            </div>

            <div class="px-4 py-5 flex-shrink-0 border-b border-gray-50">
                <a href="/profil" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 {{ Route::is('profil') ? 'bg-purple-50 text-brand font-semibold border border-purple-100 shadow-sm' : 'text-gray-600 hover:text-brand hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Profil Saya
                </a>
            </div>

            <div class="px-4 py-5 flex-shrink-0 border-b border-gray-50">
                <a href="/buat-surat" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 {{ Route::is('buat-surat') ? 'bg-purple-50 text-brand font-semibold border border-purple-100 shadow-sm' : 'text-gray-600 hover:text-brand hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Buat Surat
                </a>
            </div>

            <nav class="flex-1 px-4 pb-4 space-y-1 overflow-y-auto overflow-x-hidden custom-scrollbar">
                </nav>

            <div class="border-t border-gray-100 p-4 flex-shrink-0 bg-gray-50/50">
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="flex items-center justify-center w-full py-2.5 px-4 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 hover:text-red-700 transition duration-200 font-medium text-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="h-16 bg-white/80 backdrop-blur-md border-b border-gray-100 flex items-center justify-between px-4 lg:px-8 z-10">
                <button onclick="toggleSidebar()" class="text-gray-600 hover:text-brand focus:outline-none lg:hidden p-2 rounded-md hover:bg-gray-100">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                
                <div class="flex-1 flex justify-end">
                    <div class="flex items-center gap-3">
                        @php
                            $currentUser = \Illuminate\Support\Facades\Auth::user();
                            $currentPendamping = $currentUser ? \App\Models\Pendamping::where('no_registrasi', $currentUser->username)->first() : null;
                            $displayName = $currentPendamping ? $currentPendamping->nama : ($currentUser ? $currentUser->username : 'User');
                            $initials = substr($displayName, 0, 2);
                        @endphp
                        <span class="text-sm font-semibold text-gray-700 hidden sm:block">{{ $displayName }}</span>
                        <div class="h-8 w-8 rounded-full bg-brand text-white flex items-center justify-center text-xs font-bold uppercase">{{ $initials }}</div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>
    
    <style>
        /* Mempercantik Scrollbar di List Surat */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #e5e7eb; border-radius: 20px; }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb { background-color: #d1d5db; }
    </style>
</body>
</html>