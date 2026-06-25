<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Masuk</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .primary-bg { background-color: #670075; }
        .primary-text { color: #670075; }
        .primary-border { border-color: #670075; }
        .primary-hover:hover { background-color: #550060; }
        .primary-focus:focus-within { border-color: #670075; ring-color: #670075; }
        
        /* Modern Input Animation */
        .input-group input:placeholder-shown + label {
            opacity: 0;
            transform: translateY(10px);
        }
        .input-group input:not(:placeholder-shown) + label,
        .input-group input:focus + label {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body class="h-full font-sans antialiased text-gray-900 bg-gray-50 flex items-center justify-center p-4 sm:p-0">

    <div class="w-full max-w-[1000px] bg-white shadow-2xl rounded-2xl overflow-hidden flex flex-col md:flex-row min-h-[600px]">
        
        <!-- Left Side: Branding (Hidden on small mobile if needed, but good for identity) -->
        <div class="hidden md:flex md:w-1/2 primary-bg p-12 flex-col justify-between relative overflow-hidden">
            <!-- Decorative circle -->
            <div class="absolute -top-20 -left-20 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-80 h-80 bg-black opacity-10 rounded-full blur-3xl translate-x-1/3 translate-y-1/3"></div>
            
            <div class="relative z-10">
                <h2 class="text-white text-3xl font-bold tracking-tight">Lembaga Pendamping Proses Produk Halal (LP3H)</h2>
                <p class="text-white/80 mt-2 text-lg">UIN Sunan Kalijaga Yogyakarta</p>
            </div>

            <div class="relative z-10">
                <blockquote class="text-white/90 text-lg font-medium italic">
                    "Memastikan kehalalan, menjaga keberkahan untuk umat dan bangsa."
                </blockquote>
            </div>

            <div class="relative z-10 text-xs text-white/50">
                &copy; {{ date('Y') }} LP3H UIN Sunan Kalijaga
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center relative">
            
            <!-- Mobile Branding (Visible only on small screens) -->
            <div class="md:hidden mb-8 text-center">
                <h2 class="primary-text text-2xl font-bold">Halal Center</h2>
                <p class="text-gray-500 text-sm">UIN Sunan Kalijaga Yogyakarta</p>
            </div>

            <div class="mb-10">
                <h3 class="text-2xl font-bold text-gray-900">Selamat Datang</h3>
                <p class="text-gray-500 mt-2 text-sm">Silakan masuk untuk mengakses dashboard Anda.</p>
            </div>

            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-xl text-sm border border-red-200">
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- Username / No. Registrasi Input -->
                <div class="input-group relative">
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username / No. Registrasi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                        <input type="text" name="username" id="username" value="{{ old('username') }}" required 
                            class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#670075]/20 focus:border-[#670075] transition-all bg-gray-50 focus:bg-white placeholder-gray-400 sm:text-sm"
                            placeholder="Masukkan username atau nomor registrasi">
                    </div>
                </div>

                <!-- Password Input -->
                <div class="input-group relative">
                    <div class="flex justify-between items-center mb-1">
                        <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi</label>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                        <input type="password" name="password" id="password" required 
                            class="block w-full pl-10 pr-12 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#670075]/20 focus:border-[#670075] transition-all bg-gray-50 focus:bg-white placeholder-gray-400 sm:text-sm"
                            placeholder="Masukkan kata sandi">
                        <button type="button" id="toggle-password-btn"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                            <!-- Eye icon -->
                            <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <!-- Eye slash icon (hidden by default) -->
                            <svg id="eye-slash-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                    <!-- Forgot Password Link? Layout is simple, so maybe later -->
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" 
                        class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white primary-bg hover:bg-[#550060] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#670075] transition-all duration-200 transform hover:-translate-y-0.5 shadow-lg shadow-[#670075]/30">
                        Masuk
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 ml-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </button>
                </div>
            </form>
            
            <div class="mt-8 text-center hidden">
                <p class="text-sm text-gray-500">
                    Belum punya akun? <a href="#" class="font-medium primary-text hover:underline">Hubungi admin</a>
                </p>
            </div>
        </div>
    </div>
    
    <!-- Background Pattern/Decoration -->
    <div class="fixed top-0 left-0 w-full h-full -z-10 bg-gray-50 pointer-events-none">
        <div class="absolute top-0 right-0 w-1/3 h-full bg-gradient-to-bl from-[#670075]/5 to-transparent"></div>
        <div class="absolute bottom-0 left-0 w-1/3 h-full bg-gradient-to-tr from-[#670075]/5 to-transparent"></div>
    </div>

    <!-- Password Toggle Script -->
    <script>
        const passwordInput = document.getElementById('password');
        const toggleBtn = document.getElementById('toggle-password-btn');
        const eyeIcon = document.getElementById('eye-icon');
        const eyeSlashIcon = document.getElementById('eye-slash-icon');

        function showPassword(e) {
            if (e) e.preventDefault(); // Prevent double triggering on mobile devices
            passwordInput.type = 'text';
            eyeIcon.classList.add('hidden');
            eyeSlashIcon.classList.remove('hidden');
        }

        function hidePassword() {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('hidden');
            eyeSlashIcon.classList.add('hidden');
        }

        // Desktop mouse events
        toggleBtn.addEventListener('mousedown', showPassword);
        toggleBtn.addEventListener('mouseup', hidePassword);
        toggleBtn.addEventListener('mouseleave', hidePassword);

        // Mobile touch events
        toggleBtn.addEventListener('touchstart', showPassword);
        toggleBtn.addEventListener('touchend', hidePassword);
        toggleBtn.addEventListener('touchcancel', hidePassword);
    </script>
</body>
</html>
