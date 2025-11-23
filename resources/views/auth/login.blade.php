<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'FlowFast') }} - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gradient-to-b from-[#071226] to-[#071b2a] flex items-center justify-center p-8">
        <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-2 gap-7 items-center">
            
            {{-- Columna izquierda: Marca --}}
            <section class="hidden md:block bg-[#0b1220] bg-opacity-95 rounded-[14px] shadow-2xl border border-white/5 p-8 h-full">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-cyan-400 to-blue-300 flex items-center justify-center text-[#042027] font-bold text-xl">FF</div>
                    <div>
                        <h1 class="text-2xl font-extrabold tracking-tight text-white">FlowFast</h1>
                        <p class="text-sm text-slate-400 mt-1">Sistema de Gestión Deportiva</p>
                    </div>
                </div>
                <p class="text-slate-400 text-sm mb-4">Accede a tu cuenta para gestionar ligas, equipos, jugadores y toda la información deportiva de manera profesional.</p>
                <div class="flex gap-2 mb-4">
                    <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs text-slate-400">Gestión</span>
                    <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs text-slate-400">Reportes</span>
                    <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs text-slate-400">Analytics</span>
                </div>
                <div class="h-1 bg-gradient-to-r from-transparent via-white/10 to-transparent rounded my-5"></div>
                <div class="text-sm text-slate-400">
                    <strong class="text-white">Características:</strong>
                    <ul class="list-disc ml-5 mt-2">
                        <li>Gestión completa de ligas y torneos</li>
                        <li>Control financiero y reportes</li>
                        <li>Estadísticas en tiempo real</li>
                    </ul>
                </div>
            </section>

            {{-- Columna derecha: Formulario --}}
            <div class="w-full">
                <div class="bg-[#0b1220] bg-opacity-95 rounded-2xl shadow-2xl overflow-hidden border border-white/5">
                    <div class="px-8 py-8 text-center">
                        <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-cyan-400 to-blue-300 flex items-center justify-center text-[#042027] font-bold text-2xl mx-auto mb-4">FF</div>
                        <h2 class="text-2xl font-bold text-white mb-2">Iniciar Sesión</h2>
                        <p class="text-slate-400 text-sm mb-4">Accede a tu cuenta de FlowFast</p>
                    </div>

                    <div class="p-8 pt-0">
                        {{-- Session Status --}}
                        @if (session('status'))
                            <div class="mb-4 p-3 bg-green-500/10 border border-green-500 rounded-lg text-green-400 text-sm">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" class="space-y-5">
                            @csrf

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-sm font-semibold text-cyan-400 mb-2">Correo electrónico</label>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                       class="w-full px-4 py-2.5 border border-cyan-400 rounded-lg text-sm bg-white/5 text-white focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 transition-all placeholder:text-slate-400"
                                       placeholder="tu@email.com">
                                @error('email')
                                    <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div>
                                <label for="password" class="block text-sm font-semibold text-cyan-400 mb-2">Contraseña</label>
                                <input id="password" type="password" name="password" required autocomplete="current-password"
                                       class="w-full px-4 py-2.5 border border-cyan-400 rounded-lg text-sm bg-white/5 text-white focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 transition-all placeholder:text-slate-400"
                                       placeholder="Tu contraseña">
                                @error('password')
                                    <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Remember Me --}}
                            <div class="flex items-center justify-between">
                                <label for="remember_me" class="inline-flex items-center">
                                    <input id="remember_me" type="checkbox" name="remember" class="rounded border-cyan-400 text-cyan-400 shadow-sm focus:ring-cyan-400 bg-white/5">
                                    <span class="ml-2 text-sm text-slate-400">Recordarme</span>
                                </label>

                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-sm text-cyan-400 hover:text-white underline">
                                        ¿Olvidaste tu contraseña?
                                    </a>
                                @endif
                            </div>

                            <button type="submit"
                                class="w-full px-6 py-3 bg-gradient-to-r from-cyan-400 to-blue-500 hover:from-blue-500 hover:to-blue-700 text-[#042027] text-base font-bold rounded-lg shadow-md border border-cyan-400 transition-all">
                                Iniciar Sesión
                            </button>
                        </form>

                        <div class="mt-6 text-center">
                            <p class="text-sm text-cyan-400">
                                ¿No tienes una cuenta? 
                                <a href="{{ route('register') }}" class="font-bold underline hover:text-white">Regístrate aquí</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>
</html>
