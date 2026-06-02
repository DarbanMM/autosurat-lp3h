<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Admin') - AutoSurat LP3H</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { brand: { DEFAULT: '#670075', dark: '#4f0059', light: '#851a94', } } } }
        }
    </script>
</head>
<body class="bg-gray-100 font-sans antialiased overflow-hidden text-gray-800">
    <div class="flex h-screen w-full">

        @include('layouts.sidebar')

        <div class="flex-1 flex flex-col overflow-hidden bg-gray-50">
            <header class="h-16 bg-white shadow-sm flex items-center justify-between px-4 lg:px-8 z-10">
                <button onclick="toggleSidebar()" class="text-gray-600 hover:text-brand focus:outline-none lg:hidden p-2 rounded-md hover:bg-gray-100 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <div class="flex-1 flex justify-end">
                    <div class="flex items-center space-x-3 cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-colors border border-transparent hover:border-gray-200">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold text-brand leading-none">Admin LP3H</p>
                            <p class="text-xs text-gray-500 mt-1">Administrator</p>
                        </div>
                        <div class="h-9 w-9 rounded-full bg-brand flex items-center justify-center text-white font-bold shadow-md">
                            A
                        </div>
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
</body>
</html>