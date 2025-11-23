<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'FlowFast') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Public Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span class="text-xl font-bold text-gray-900">FlowFast</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex md:items-center md:space-x-6">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">
                        🏠 Inicio
                    </a>
                    <a href="{{ route('public.leagues') }}" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">
                        🏆 Ligas
                    </a>
                    
                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">
                            📊 Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">
                                🚪 Salir
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 font-medium transition-colors">
                            🔐 Iniciar Sesión
                        </a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            ✨ Registrarse
                        </a>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-700 hover:text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div x-data="{ mobileMenuOpen: false }" x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false" class="md:hidden border-t border-gray-200">
            <div class="px-4 py-3 space-y-3">
                <a href="{{ route('home') }}" class="block text-gray-700 hover:text-blue-600 font-medium">
                    🏠 Inicio
                </a>
                <a href="{{ route('public.leagues') }}" class="block text-gray-700 hover:text-blue-600 font-medium">
                    🏆 Ligas
                </a>
                
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="block text-gray-700 hover:text-blue-600 font-medium">
                        📊 Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left text-gray-700 hover:text-blue-600 font-medium">
                            🚪 Salir
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block text-gray-700 hover:text-blue-600 font-medium">
                        🔐 Iniciar Sesión
                    </a>
                    <a href="{{ route('register') }}" class="block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-center font-medium">
                        ✨ Registrarse
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- About -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span class="text-xl font-bold">FlowFast</span>
                    </div>
                    <p class="text-gray-400 mb-4">
                        Plataforma completa para gestión de ligas deportivas. Organiza torneos, gestiona equipos y mantén a tus aficionados informados.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="font-semibold mb-4">Enlaces Rápidos</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">Inicio</a></li>
                        <li><a href="{{ route('public.leagues') }}" class="hover:text-white transition-colors">Ligas</a></li>
                        @guest
                        <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Iniciar Sesión</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">Registrarse</a></li>
                        @endguest
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h3 class="font-semibold mb-4">Contacto</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li>📧 info@flowfast.com</li>
                        <li>📱 +52 123 456 7890</li>
                        <li>📍 México</li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} FlowFast. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
