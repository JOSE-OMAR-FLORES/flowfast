<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FlowFast SaaS') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Google Material Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    
    <!-- Google Poppins Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" />
    
    <!-- Tailwind CSS CDN (temporal) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js x-cloak -->
    <style>[x-cloak] { display: none !important; }</style>
    
    <!-- Modern Sidebar Styles -->
    <link rel="stylesheet" href="/css/sidebar.css">
    
    <!-- Alpine.js - COMENTADO porque Livewire 3 ya lo incluye -->
    <!-- <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script> -->
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Livewire Styles -->
    @livewireStyles
</head>
<body class="bg-gray-50 font-sans antialiased" x-data="{ sidebarOpen: false }">
    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" 
         @click="sidebarOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="mobile-overlay fixed inset-0 bg-black bg-opacity-50 z-40"
         style="display: none;"></div>
    
    <!-- Modern Sidebar -->
    <aside class="modern-sidebar" :class="{ 'mobile-open': sidebarOpen }">
        <!-- Close Button (Mobile Only) -->
        <button @click="sidebarOpen = false" 
                class="mobile-close-btn absolute top-6 right-4 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/10 transition-colors">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        
        <!-- Logo/Header -->
        <div class="sidebar-header">
            @php
                $brandLogoUrl = null;
                $brandName = 'FlowFast SaaS';
                $user = auth()->user();
                
                if ($user) {
                    // Obtener el admin asociado a este usuario
                    $associatedAdmin = $user->getAssociatedAdmin();
                    
                    if ($associatedAdmin) {
                        if ($associatedAdmin->brand_logo) {
                            $brandLogoUrl = $associatedAdmin->brand_logo_url;
                        }
                        if ($associatedAdmin->company_name) {
                            $brandName = $associatedAdmin->company_name;
                        }
                    }
                }
            @endphp
            
            @if($brandLogoUrl)
                <img src="{{ $brandLogoUrl }}" alt="logo" class="brand-logo-img" style="width: 42px; height: 42px; object-fit: cover; border-radius: 8px;" />
            @else
                <img src="https://ui-avatars.com/api/?name=FS&background=667eea&color=fff&size=42" alt="logo" />
            @endif
            <h2>{{ $brandName }}</h2>
        </div>
        
        <!-- Navigation -->
        @auth
            @include('layouts.partials.sidebar-nav')
        @endauth
        
        <!-- User Account at Bottom -->
        <div class="user-account">
            <div class="user-profile">
                @if(auth()->check())
                    @if(auth()->user()->profile_photo)
                        <img src="{{ auth()->user()->profile_photo_url }}" alt="Foto de perfil" class="user-photo" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                    @else
                        <div class="user-initial">
                            {{ auth()->user()->initials }}
                        </div>
                    @endif
                    <div class="user-detail">
                        <h3>{{ auth()->user()->name }}</h3>
                        <span>{{ ucfirst(str_replace('_', ' ', auth()->user()->user_type)) }}</span>
                    </div>
                @else
                    <div class="user-initial">G</div>
                    <div class="user-detail">
                        <h3>Guest</h3>
                        <span>Visitor</span>
                    </div>
                @endif
            </div>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="main-content-with-sidebar" style="min-height: 100vh; background: #F0F4FF;">
        
        <!-- Top Bar -->
        <header class="bg-white shadow-sm border-b border-gray-200 relative z-10">
            <div class="flex items-center justify-between px-3 sm:px-4 lg:px-6 py-3 sm:py-4">
                <div class="flex items-center gap-2 sm:gap-3 lg:gap-4 flex-1 min-w-0">
                    <!-- Hamburger Menu (Mobile Only) -->
                    <button @click="sidebarOpen = true" 
                            class="mobile-menu-btn flex-shrink-0 flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-lg hover:bg-gray-100 transition-colors active:bg-gray-200">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    
                    <!-- Page Title -->
                    <h2 class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-800 truncate">
                        @yield('page-title', 'Dashboard')
                    </h2>
                </div>
                
                <!-- User Menu -->
                @auth
                    <div x-data="{ userMenuOpen: false }" class="relative flex-shrink-0">
                        <button @click="userMenuOpen = !userMenuOpen" 
                                class="flex items-center gap-1.5 sm:gap-2 text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500 px-2 sm:px-3 py-1.5 sm:py-2 hover:bg-gray-100 transition-colors active:bg-gray-200">
                            @if(auth()->user()->profile_photo)
                                <img src="{{ auth()->user()->profile_photo_url }}" alt="Foto de perfil" class="w-7 h-7 sm:w-8 sm:h-8 rounded-full object-cover">
                            @else
                                <div class="w-7 h-7 sm:w-8 sm:h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-medium text-xs sm:text-sm">
                                    {{ auth()->user()->initials }}
                                </div>
                            @endif
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-gray-600 hidden xs:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Dropdown -->
                        <div x-show="userMenuOpen" 
                             @click.away="userMenuOpen = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 sm:w-56 bg-white rounded-lg shadow-xl border border-gray-100 z-50">
                            <div class="py-2">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <div class="font-semibold text-gray-800">{{ auth()->user()->name }}</div>
                                    <div class="text-xs text-gray-500 mt-1">{{ auth()->user()->email }}</div>
                                    <div class="text-xs text-indigo-600 mt-0.5">{{ ucfirst(str_replace('_', ' ', auth()->user()->user_type)) }}</div>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                    <span class="inline-flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Mi Perfil
                                    </span>
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <span class="inline-flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            Cerrar Sesión
                                        </span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endauth
            </div>
        </header>
        
        <!-- Main Content Area -->
        <main class="p-3 sm:p-4 lg:p-6">
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-transition 
                         class="mb-4 sm:mb-6 bg-green-100 border border-green-400 text-green-700 px-3 py-2 sm:px-4 sm:py-3 rounded-lg relative text-sm sm:text-base">
                        <span class="block sm:inline pr-8">{{ session('success') }}</span>
                        <button @click="show = false" class="absolute top-2 right-2 sm:top-3 sm:right-3 p-1 hover:bg-green-200 rounded transition-colors">
                            <svg class="fill-current h-4 w-4 sm:h-5 sm:w-5 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                            </svg>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" x-transition 
                         class="mb-4 sm:mb-6 bg-red-100 border border-red-400 text-red-700 px-3 py-2 sm:px-4 sm:py-3 rounded-lg relative text-sm sm:text-base">
                        <span class="block sm:inline pr-8">{{ session('error') }}</span>
                        <button @click="show = false" class="absolute top-2 right-2 sm:top-3 sm:right-3 p-1 hover:bg-red-200 rounded transition-colors">
                            <svg class="fill-current h-4 w-4 sm:h-5 sm:w-5 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                            </svg>
                        </button>
                    </div>
                @endif
                
                @yield('content')
                {{ $slot ?? '' }}
        </main>
    </div>

    <!-- Livewire Scripts -->
    @livewireScripts
    
    <!-- Mobile Sidebar Auto-Close Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delegación de eventos para cerrar sidebar al hacer clic en enlaces reales (no triggers)
            document.addEventListener('click', function(e) {
                // Solo en móviles
                if (window.innerWidth > 1024) return;
                
                // Verificar si es un enlace del sidebar
                const link = e.target.closest('.sidebar-links a');
                if (!link) return;
                
                // Ignorar si es un trigger de acordeón (javascript:void(0))
                const href = link.getAttribute('href');
                if (!href || href === '#' || href.startsWith('javascript:')) return;
                
                // Cerrar el sidebar después de un pequeño delay
                setTimeout(() => {
                    const body = document.querySelector('body');
                    if (body && body.__x) {
                        body.__x.$data.sidebarOpen = false;
                    }
                }, 100);
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>
