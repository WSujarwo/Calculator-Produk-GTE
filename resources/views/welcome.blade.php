<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel - Login</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-[#F3F4F6] text-[#111827] selection:bg-[#FF2D20] selection:text-white">
    
    <div class="relative min-h-screen flex flex-col items-center justify-center overflow-hidden">
        
        <img id="background" class="absolute -left-20 top-0 max-w-[877px] opacity-20 pointer-events-none" src="https://laravel.com/assets/img/welcome/background.svg" />

        <div class="relative z-10 w-full max-w-[440px] px-4">
            
            <div class="bg-white border border-gray-200 rounded-[32px] p-8 shadow-[0_20px_50px_rgba(0,0,0,0.05)]">
                
                <div class="flex flex-col items-center mb-10">
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 mb-4 shadow-sm">
                        <img class="h-12 w-auto" src="{{ asset('img/MasterLogo.png') }}" alt="Logo">
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Selamat Datang</h2>
                    <p class="text-sm text-gray-500 mt-1 text-center">Silakan masuk ke akun Anda</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-gray-700 ml-1 uppercase tracking-wider">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FF2D20]/20 focus:border-[#FF2D20] transition duration-200"
                            placeholder="nama@email.com">
                        @error('email')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-gray-700 ml-1 uppercase tracking-wider">Password</label>
                        <input type="password" name="password" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FF2D20]/20 focus:border-[#FF2D20] transition duration-200"
                            placeholder="••••••••">
                        @error('password')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between px-1">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-[#FF2D20] focus:ring-[#FF2D20]">
                            <span class="ml-2 text-xs text-gray-500 group-hover:text-gray-700 transition">Ingat saya</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs text-[#FF2D20] hover:text-[#e0261b] font-medium transition">Lupa password?</a>
                        @endif
                    </div>

                    <button type="submit"
                        class="w-full bg-[#FF2D20] hover:bg-[#e0261b] text-white font-bold py-3.5 rounded-xl transition duration-300 shadow-lg shadow-[#FF2D20]/25 active:scale-[0.98] mt-2">
                        Log In
                    </button>
                </form>

                @if (Route::has('register'))
                    <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                        <p class="text-xs text-gray-500">
                            Belum punya akun? 
                            <a href="{{ route('register') }}" class="text-gray-900 hover:text-[#FF2D20] font-bold transition">Daftar Sekarang</a>
                        </p>
                    </div>
                @endif
            </div>

            <p class="text-center mt-8 text-[10px] uppercase tracking-[0.2em] text-gray-400 font-medium">
                &copy; {{ date('Y') }} Master App &bull; Secure Access
            </p>
        </div>
    </div>
</body>
</html>