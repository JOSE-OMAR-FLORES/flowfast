# 🎨 FlowFast SaaS - Desarrollo del Frontend

## 📋 Índice

1. [Configuración Inicial](#-configuración-inicial)
2. [Estructura de Componentes Livewire](#-estructura-de-componentes-livewire)
3. [Integración Alpine.js](#-integración-alpinejs)
4. [Dashboards por Rol](#-dashboards-por-rol)
5. [Componentes UI Reutilizables](#-componentes-ui-reutilizables)
6. [Responsive Design](#-responsive-design)

---

## 🚀 Configuración Inicial

### **1. Instalación de Dependencias Frontend**

```bash
# Instalar Livewire 3
composer require livewire/livewire

# Publicar configuración de Livewire
php artisan livewire:publish --config

# Instalar dependencias de Node.js
npm install

# Instalar Tailwind CSS y dependencias
npm install -D tailwindcss@latest postcss@latest autoprefixer@latest
npm install alpinejs
npm install @heroicons/react
npm install chart.js

# Generar configuración de Tailwind
npx tailwindcss init -p
```

### **2. Configuración de Vite (vite.config.js)**

```javascript
// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js'
            ],
            refresh: [
                'resources/views/**/*.blade.php',
                'app/Livewire/**/*.php',
            ],
        }),
    ],
    define: {
        'process.env': process.env,
    },
});
```

### **3. Configuración de Tailwind (tailwind.config.js)**

```javascript
// tailwind.config.js
/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./app/Livewire/**/*.php",
        "./vendor/livewire/livewire/dist/livewire.esm.js",
    ],
    theme: {
        extend: {
            colors: {
                // Paleta de colores FlowFast
                primary: {
                    50: '#fef2f2',
                    100: '#fee2e2',
                    200: '#fecaca',
                    300: '#fca5a5',
                    400: '#f87171',
                    500: '#ef4444', // Rojo secundario
                    600: '#dc2626', // Rojo primario
                    700: '#b91c1c',
                    800: '#991b1b', // Rojo oscuro
                    900: '#7f1d1d',
                },
                success: {
                    500: '#10b981',
                    600: '#059669',
                },
                warning: {
                    500: '#f59e0b',
                    600: '#d97706',
                },
                danger: {
                    500: '#ef4444',
                    600: '#dc2626',
                },
            },
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui'],
            },
            animation: {
                'slide-in': 'slideIn 0.3s ease-out',
                'fade-in': 'fadeIn 0.2s ease-out',
                'bounce-in': 'bounceIn 0.5s ease-out',
            },
            keyframes: {
                slideIn: {
                    '0%': { transform: 'translateX(-100%)' },
                    '100%': { transform: 'translateX(0)' },
                },
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                bounceIn: {
                    '0%': { transform: 'scale(0.3)', opacity: '0' },
                    '50%': { transform: 'scale(1.05)' },
                    '70%': { transform: 'scale(0.9)' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
}
```

### **4. Setup de CSS y JavaScript**

```css
/* resources/css/app.css */
@tailwind base;
@tailwind components;
@tailwind utilities;

/* Componentes personalizados */
@layer components {
    .btn {
        @apply px-4 py-2 rounded-lg font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2;
    }
    
    .btn-primary {
        @apply bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500;
    }
    
    .btn-secondary {
        @apply bg-gray-200 text-gray-900 hover:bg-gray-300 focus:ring-gray-500;
    }
    
    .btn-success {
        @apply bg-success-600 text-white hover:bg-success-700 focus:ring-success-500;
    }
    
    .btn-danger {
        @apply bg-danger-600 text-white hover:bg-danger-700 focus:ring-danger-500;
    }
    
    .card {
        @apply bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden;
    }
    
    .card-header {
        @apply px-6 py-4 border-b border-gray-200 bg-gray-50;
    }
    
    .card-body {
        @apply px-6 py-4;
    }
    
    .form-input {
        @apply block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500;
    }
    
    .form-label {
        @apply block text-sm font-medium text-gray-700 mb-1;
    }
    
    .sidebar-item {
        @apply flex items-center px-4 py-3 text-gray-700 hover:bg-primary-50 hover:text-primary-600 rounded-lg transition-colors duration-200;
    }
    
    .sidebar-item.active {
        @apply bg-primary-100 text-primary-700 border-r-2 border-primary-600;
    }
}

/* Animaciones personalizadas */
@layer utilities {
    .animate-pulse-slow {
        animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    .glass {
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.9);
    }
}
```

```javascript
// resources/js/app.js
import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

// Configuración global de Alpine.js
Alpine.data('sidebar', () => ({
    isOpen: false,
    isMobile: window.innerWidth < 768,
    
    init() {
        this.checkMobile();
        window.addEventListener('resize', () => {
            this.checkMobile();
        });
    },
    
    checkMobile() {
        this.isMobile = window.innerWidth < 768;
        if (!this.isMobile) {
            this.isOpen = true;
        }
    },
    
    toggle() {
        this.isOpen = !this.isOpen;
    }
}));

Alpine.data('dropdown', () => ({
    isOpen: false,
    
    toggle() {
        this.isOpen = !this.isOpen;
    },
    
    close() {
        this.isOpen = false;
    }
}));

Alpine.data('modal', () => ({
    isOpen: false,
    
    open() {
        this.isOpen = true;
        document.body.style.overflow = 'hidden';
    },
    
    close() {
        this.isOpen = false;
        document.body.style.overflow = 'auto';
    }
}));

// Componente para notificaciones toast
Alpine.data('toast', () => ({
    notifications: [],
    
    add(message, type = 'info', duration = 5000) {
        const id = Date.now();
        this.notifications.push({ id, message, type });
        
        setTimeout(() => {
            this.remove(id);
        }, duration);
    },
    
    remove(id) {
        this.notifications = this.notifications.filter(n => n.id !== id);
    }
}));

// Configuración global de Chart.js
Chart.defaults.font.family = 'Inter, ui-sans-serif, system-ui';
Chart.defaults.color = '#374151';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();
```

---

## 🧩 Estructura de Componentes Livewire

### **1. Layout Principal**

```php
<?php
// app/Livewire/Layout/AppLayout.php
namespace App\Livewire\Layout;

use Livewire\Component;

class AppLayout extends Component
{
    public $title = 'FlowFast SaaS';
    public $showSidebar = true;
    
    public function render()
    {
        return view('livewire.layout.app-layout')
            ->layout('components.layouts.app', [
                'title' => $this->title
            ]);
    }
}
```

```blade
{{-- resources/views/livewire/layout/app-layout.blade.php --}}
<div class="min-h-screen bg-gray-50" x-data="sidebar()">
    <!-- Sidebar Desktop -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out"
         :class="{ '-translate-x-full': !isOpen && isMobile, 'translate-x-0': isOpen || !isMobile }">
        
        <!-- Header del Sidebar -->
        <div class="flex items-center justify-between h-16 px-6 bg-primary-600">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('images/logo-white.svg') }}" alt="FlowFast" class="h-8 w-auto">
                <span class="text-white font-bold text-lg">FlowFast</span>
            </div>
            
            <button @click="toggle()" class="lg:hidden text-white hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Navegación -->
        <nav class="flex-1 px-4 py-6 space-y-2">
            @livewire('layout.sidebar-navigation')
        </nav>

        <!-- Footer del Sidebar -->
        <div class="p-4 border-t border-gray-200">
            @livewire('layout.user-menu')
        </div>
    </div>

    <!-- Overlay para móvil -->
    <div x-show="isOpen && isMobile" 
         @click="toggle()" 
         class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    <!-- Contenido Principal -->
    <div class="transition-all duration-300 ease-in-out"
         :class="{ 'lg:ml-64': isOpen || !isMobile, 'lg:ml-0': !isOpen && isMobile }">
        
        <!-- Header Superior -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between h-16 px-6">
                <div class="flex items-center space-x-4">
                    <button @click="toggle()" class="lg:hidden text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    
                    <h1 class="text-xl font-semibold text-gray-800">{{ $title }}</h1>
                </div>

                <div class="flex items-center space-x-4">
                    @livewire('layout.notifications')
                    @livewire('layout.user-dropdown')
                </div>
            </div>
        </header>

        <!-- Contenido de la Página -->
        <main class="p-6">
            {{ $slot }}
        </main>
    </div>

    <!-- Toast Notifications -->
    @livewire('components.toast-notifications')
</div>
```

### **2. Navegación del Sidebar**

```php
<?php
// app/Livewire/Layout/SidebarNavigation.php
namespace App\Livewire\Layout;

use Livewire\Component;

class SidebarNavigation extends Component
{
    public function getNavigationItems()
    {
        $user = auth()->user();
        $userType = $user->user_type;
        
        $navigation = [
            'admin' => [
                [
                    'name' => 'Dashboard',
                    'route' => 'admin.dashboard',
                    'icon' => 'home',
                ],
                [
                    'name' => 'Ligas',
                    'route' => 'admin.leagues.index',
                    'icon' => 'trophy',
                ],
                [
                    'name' => 'Usuarios',
                    'route' => 'admin.users.index',
                    'icon' => 'users',
                ],
                [
                    'name' => 'Finanzas',
                    'route' => 'admin.finances.index',
                    'icon' => 'currency-dollar',
                ],
                [
                    'name' => 'Reportes',
                    'route' => 'admin.reports.index',
                    'icon' => 'chart-bar',
                ],
                [
                    'name' => 'Configuración',
                    'route' => 'admin.settings.index',
                    'icon' => 'cog',
                ],
            ],
            'league_manager' => [
                [
                    'name' => 'Dashboard',
                    'route' => 'manager.dashboard',
                    'icon' => 'home',
                ],
                [
                    'name' => 'Mis Ligas',
                    'route' => 'manager.leagues.index',
                    'icon' => 'trophy',
                ],
                [
                    'name' => 'Equipos',
                    'route' => 'manager.teams.index',
                    'icon' => 'user-group',
                ],
                [
                    'name' => 'Partidos',
                    'route' => 'manager.matches.index',
                    'icon' => 'calendar',
                ],
                [
                    'name' => 'Árbitros',
                    'route' => 'manager.referees.index',
                    'icon' => 'whistle',
                ],
                [
                    'name' => 'Finanzas',
                    'route' => 'manager.finances.index',
                    'icon' => 'currency-dollar',
                ],
            ],
            'referee' => [
                [
                    'name' => 'Dashboard',
                    'route' => 'referee.dashboard',
                    'icon' => 'home',
                ],
                [
                    'name' => 'Mis Partidos',
                    'route' => 'referee.matches.index',
                    'icon' => 'calendar',
                ],
                [
                    'name' => 'Pagos',
                    'route' => 'referee.payments.index',
                    'icon' => 'currency-dollar',
                ],
                [
                    'name' => 'Mi Perfil',
                    'route' => 'referee.profile.edit',
                    'icon' => 'user',
                ],
            ],
            'coach' => [
                [
                    'name' => 'Dashboard',
                    'route' => 'coach.dashboard',
                    'icon' => 'home',
                ],
                [
                    'name' => 'Mi Equipo',
                    'route' => 'coach.team.show',
                    'icon' => 'user-group',
                ],
                [
                    'name' => 'Jugadores',
                    'route' => 'coach.players.index',
                    'icon' => 'users',
                ],
                [
                    'name' => 'Calendario',
                    'route' => 'coach.schedule.index',
                    'icon' => 'calendar',
                ],
                [
                    'name' => 'Pagos',
                    'route' => 'coach.payments.index',
                    'icon' => 'currency-dollar',
                ],
            ],
            'player' => [
                [
                    'name' => 'Dashboard',
                    'route' => 'player.dashboard',
                    'icon' => 'home',
                ],
                [
                    'name' => 'Mi Equipo',
                    'route' => 'player.team.show',
                    'icon' => 'user-group',
                ],
                [
                    'name' => 'Calendario',
                    'route' => 'player.schedule.index',
                    'icon' => 'calendar',
                ],
                [
                    'name' => 'Estadísticas',
                    'route' => 'player.stats.index',
                    'icon' => 'chart-bar',
                ],
            ],
        ];

        return $navigation[$userType] ?? [];
    }
    
    public function render()
    {
        return view('livewire.layout.sidebar-navigation', [
            'navigationItems' => $this->getNavigationItems()
        ]);
    }
}
```

```blade
{{-- resources/views/livewire/layout/sidebar-navigation.blade.php --}}
<div class="space-y-1">
    @foreach($navigationItems as $item)
        <a href="{{ route($item['route']) }}" 
           class="sidebar-item group {{ request()->routeIs($item['route']) ? 'active' : '' }}">
            
            <svg class="w-5 h-5 mr-3 flex-shrink-0 transition-colors duration-200 
                        {{ request()->routeIs($item['route']) ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-500' }}"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                @include('components.icons.' . $item['icon'])
            </svg>
            
            <span class="font-medium">{{ $item['name'] }}</span>
        </a>
    @endforeach
</div>
```

### **3. Dashboard por Roles**

```php
<?php
// app/Livewire/Dashboard/AdminDashboard.php
namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\League;
use App\Services\FinancialService;
use App\Services\AnalyticsService;

class AdminDashboard extends Component
{
    public $selectedPeriod = 'month';
    public $stats = [];
    public $recentTransactions = [];
    public $leaguePerformance = [];

    protected $financialService;
    protected $analyticsService;

    public function boot(
        FinancialService $financialService,
        AnalyticsService $analyticsService
    ) {
        $this->financialService = $financialService;
        $this->analyticsService = $analyticsService;
    }

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function updatedSelectedPeriod()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        $admin = auth()->user()->userable;
        
        // Estadísticas generales
        $this->stats = [
            'total_leagues' => $admin->leagues()->count(),
            'active_seasons' => $admin->leagues()->whereHas('seasons', function($q) {
                $q->where('status', 'active');
            })->count(),
            'total_teams' => $admin->leagues()->withCount('teams')->get()->sum('teams_count'),
            'total_matches_today' => $this->getTodayMatches($admin),
        ];

        // Datos financieros
        $financialData = $this->financialService->getAdminSummary(
            $admin, 
            $this->selectedPeriod
        );
        
        $this->stats = array_merge($this->stats, [
            'total_income' => $financialData['total_income'],
            'total_expenses' => $financialData['total_expenses'],
            'net_profit' => $financialData['net_profit'],
            'profit_percentage' => $financialData['profit_percentage'],
        ]);

        // Transacciones recientes
        $this->recentTransactions = $this->financialService->getRecentTransactions($admin, 10);
        
        // Performance de ligas
        $this->leaguePerformance = $this->analyticsService->getLeaguePerformance($admin);
    }

    private function getTodayMatches($admin)
    {
        return \App\Models\Match::whereHas('round.season.league', function($q) use ($admin) {
            $q->where('admin_id', $admin->id);
        })
        ->whereDate('match_date', today())
        ->count();
    }

    public function render()
    {
        return view('livewire.dashboard.admin-dashboard')
            ->layout('components.layouts.app', ['title' => 'Dashboard Administrativo']);
    }
}
```

```blade
{{-- resources/views/livewire/dashboard/admin-dashboard.blade.php --}}
<div class="space-y-6">
    <!-- Header con selección de período -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard Administrativo</h1>
            <p class="mt-1 text-gray-500">Resumen de todas tus ligas y actividades</p>
        </div>
        
        <div class="flex items-center space-x-4">
            <select wire:model.live="selectedPeriod" class="form-input">
                <option value="week">Esta semana</option>
                <option value="month">Este mes</option>
                <option value="quarter">Este trimestre</option>
                <option value="year">Este año</option>
            </select>
            
            <button class="btn btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nueva Liga
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Ligas -->
        <div class="card">
            <div class="card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 bg-primary-100 rounded-lg">
                            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Ligas</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_leagues'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ingresos Totales -->
        <div class="card">
            <div class="card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 bg-success-100 rounded-lg">
                            <svg class="w-6 h-6 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Ingresos Totales</p>
                        <p class="text-2xl font-semibold text-gray-900">${{ number_format($stats['total_income'], 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ganancia Neta -->
        <div class="card">
            <div class="card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 bg-warning-100 rounded-lg">
                            <svg class="w-6 h-6 text-warning-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Ganancia Neta</p>
                        <p class="text-2xl font-semibold {{ $stats['net_profit'] >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                            ${{ number_format($stats['net_profit'], 2) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Partidos Hoy -->
        <div class="card">
            <div class="card-body">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Partidos Hoy</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_matches_today'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos y Tablas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Gráfico de Ingresos -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-medium text-gray-900">Tendencia de Ingresos</h3>
            </div>
            <div class="card-body">
                @livewire('components.income-chart', ['period' => $selectedPeriod])
            </div>
        </div>

        <!-- Transacciones Recientes -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-medium text-gray-900">Transacciones Recientes</h3>
            </div>
            <div class="card-body">
                @livewire('components.recent-transactions-table', ['transactions' => $recentTransactions])
            </div>
        </div>
    </div>

    <!-- Performance de Ligas -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-lg font-medium text-gray-900">Performance de Ligas</h3>
        </div>
        <div class="card-body">
            @livewire('components.league-performance-table', ['leagues' => $leaguePerformance])
        </div>
    </div>
</div>
```

---

## 🎯 Integración Alpine.js

### **1. Componente de Modal Reutilizable**

```blade
{{-- resources/views/components/modal.blade.php --}}
@props([
    'name',
    'show' => false,
    'maxWidth' => 'lg'
])

@php
$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth];
@endphp

<div x-data="{ show: @entangle('show').live }"
     x-show="show"
     x-on:close.stop="show = false"
     x-on:keydown.escape.window="show = false"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
     style="display: none;">
    
    <div x-show="show" class="fixed inset-0 bg-gray-500 opacity-75" x-on:click="show = false"></div>

    <div x-show="show" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:mx-auto {{ $maxWidth }}">
        {{ $slot }}
    </div>
</div>
```

### **2. Componente Dropdown**

```blade
{{-- resources/views/components/dropdown.blade.php --}}
@props([
    'align' => 'right',
    'width' => '48',
    'contentClasses' => 'py-1 bg-white'
])

@php
switch ($align) {
    case 'left':
        $alignmentClasses = 'origin-top-left left-0';
        break;
    case 'top':
        $alignmentClasses = 'origin-top';
        break;
    case 'right':
    default:
        $alignmentClasses = 'origin-top-right right-0';
        break;
}

switch ($width) {
    case '48':
        $width = 'w-48';
        break;
}
@endphp

<div class="relative" x-data="dropdown()">
    <div x-on:click="toggle()">
        {{ $trigger }}
    </div>

    <div x-show="isOpen"
         x-on:click="close()"
         x-on:click.away="close()"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute z-50 mt-2 {{ $width }} rounded-md shadow-lg {{ $alignmentClasses }}"
         style="display: none;">
        <div class="rounded-md ring-1 ring-black ring-opacity-5 {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
```

### **3. Componente de Confirmación**

```javascript
// Agregado a resources/js/app.js
Alpine.data('confirmation', () => ({
    isOpen: false,
    title: '',
    message: '',
    confirmText: 'Confirmar',
    cancelText: 'Cancelar',
    onConfirm: null,
    
    show(options = {}) {
        this.title = options.title || '¿Estás seguro?';
        this.message = options.message || 'Esta acción no se puede deshacer.';
        this.confirmText = options.confirmText || 'Confirmar';
        this.cancelText = options.cancelText || 'Cancelar';
        this.onConfirm = options.onConfirm || (() => {});
        this.isOpen = true;
    },
    
    confirm() {
        if (this.onConfirm) {
            this.onConfirm();
        }
        this.close();
    },
    
    close() {
        this.isOpen = false;
    }
}));
```

```blade
{{-- resources/views/components/confirmation-modal.blade.php --}}
<div x-data="confirmation()" x-on:show-confirmation.window="show($event.detail)">
    <div x-show="isOpen" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-on:click="close()" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="title"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" x-text="message"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" 
                            x-on:click="confirm()"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                            x-text="confirmText">
                    </button>
                    <button type="button" 
                            x-on:click="close()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            x-text="cancelText">
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
```

---

## 🧩 Componentes UI Reutilizables

### **1. Componente de Tabla Dinámica**

```php
<?php
// app/Livewire/Components/DataTable.php
namespace App\Livewire\Components;

use Livewire\Component;
use Livewire\WithPagination;

class DataTable extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;
    
    public $model;
    public $columns = [];
    public $actions = [];
    public $filters = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function getRowsProperty()
    {
        $query = $this->model::query();

        // Aplicar búsqueda
        if ($this->search) {
            $searchableFields = collect($this->columns)
                ->where('searchable', true)
                ->pluck('field')
                ->toArray();

            $query->where(function ($q) use ($searchableFields) {
                foreach ($searchableFields as $field) {
                    $q->orWhere($field, 'LIKE', '%' . $this->search . '%');
                }
            });
        }

        // Aplicar filtros
        foreach ($this->filters as $filter => $value) {
            if ($value) {
                $query->where($filter, $value);
            }
        }

        // Aplicar ordenamiento
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.components.data-table', [
            'rows' => $this->rows
        ]);
    }
}
```

```blade
{{-- resources/views/livewire/components/data-table.blade.php --}}
<div class="card">
    <!-- Header con búsqueda y filtros -->
    <div class="card-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <!-- Búsqueda -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" 
                           type="text" 
                           class="form-input pl-10" 
                           placeholder="Buscar...">
                </div>

                <!-- Filtros adicionales -->
                @if(count($filters) > 0)
                    <div class="flex space-x-2">
                        @foreach($filters as $filter => $value)
                            <select wire:model.live="filters.{{ $filter }}" class="form-input">
                                <option value="">Todos</option>
                                <!-- Opciones dinámicas -->
                            </select>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Acciones globales -->
            <div class="flex space-x-2">
                @foreach($actions['global'] ?? [] as $action)
                    <button wire:click="{{ $action['method'] }}" 
                            class="btn {{ $action['class'] ?? 'btn-primary' }}">
                        @if(isset($action['icon']))
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @include('components.icons.' . $action['icon'])
                            </svg>
                        @endif
                        {{ $action['label'] }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @foreach($columns as $column)
                        <th wire:click="sortBy('{{ $column['field'] }}')"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            <div class="flex items-center space-x-1">
                                <span>{{ $column['label'] }}</span>
                                @if($sortField === $column['field'])
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        @if($sortDirection === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        @endif
                                    </svg>
                                @endif
                            </div>
                        </th>
                    @endforeach
                    @if(count($actions['row'] ?? []) > 0)
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($rows as $row)
                    <tr class="hover:bg-gray-50">
                        @foreach($columns as $column)
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if(isset($column['component']))
                                    @livewire($column['component'], ['value' => data_get($row, $column['field']), 'row' => $row])
                                @else
                                    {{ data_get($row, $column['field']) }}
                                @endif
                            </td>
                        @endforeach
                        @if(count($actions['row'] ?? []) > 0)
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    @foreach($actions['row'] as $action)
                                        <button wire:click="{{ $action['method'] }}({{ $row->id }})" 
                                                class="btn-sm {{ $action['class'] ?? 'btn-secondary' }}">
                                            {{ $action['label'] }}
                                        </button>
                                    @endforeach
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + (count($actions['row'] ?? []) > 0 ? 1 : 0) }}" 
                            class="px-6 py-12 text-center text-gray-500">
                            No hay datos disponibles
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    @if($rows->hasPages())
        <div class="card-body border-t border-gray-200">
            {{ $rows->links() }}
        </div>
    @endif
</div>
```

---

## 📱 Responsive Design

### **1. Breakpoints Específicos**

```css
/* Agregado a resources/css/app.css */

/* Mobile First Design */
@layer utilities {
    /* Mobile (320px - 767px) */
    @media (max-width: 767px) {
        .mobile-stack {
            @apply flex-col space-y-2 space-x-0;
        }
        
        .mobile-full {
            @apply w-full;
        }
        
        .mobile-hide {
            @apply hidden;
        }
        
        .mobile-text-sm {
            @apply text-sm;
        }
    }
    
    /* Tablet (768px - 1023px) */
    @media (min-width: 768px) and (max-width: 1023px) {
        .tablet-grid-2 {
            @apply grid-cols-2;
        }
        
        .tablet-sidebar-mini {
            @apply w-16;
        }
    }
    
    /* Desktop (1024px+) */
    @media (min-width: 1024px) {
        .desktop-grid-4 {
            @apply grid-cols-4;
        }
        
        .desktop-sidebar-full {
            @apply w-64;
        }
    }
}

/* Componentes responsive específicos */
@layer components {
    .responsive-card {
        @apply card;
        /* Mobile */
        @apply p-4;
        /* Tablet y Desktop */
        @apply md:p-6;
    }
    
    .responsive-grid {
        @apply grid gap-4;
        /* Mobile: 1 columna */
        @apply grid-cols-1;
        /* Tablet: 2 columnas */
        @apply md:grid-cols-2;
        /* Desktop: 3-4 columnas */
        @apply lg:grid-cols-3 xl:grid-cols-4;
    }
    
    .responsive-table {
        /* Mobile: Card layout */
        @apply block md:table;
    }
    
    .responsive-table thead,
    .responsive-table tbody,
    .responsive-table th,
    .responsive-table td,
    .responsive-table tr {
        @apply block md:table-header-group md:table-row-group md:table-cell md:table-row;
    }
    
    .responsive-table thead tr {
        @apply hidden md:table-row;
    }
    
    .responsive-table tbody tr {
        @apply border border-gray-200 mb-4 p-4 rounded-lg md:border-none md:mb-0 md:p-0 md:rounded-none;
    }
    
    .responsive-table td {
        @apply border-none relative pl-1/2 md:pl-6;
    }
    
    .responsive-table td:before {
        content: attr(data-label) ": ";
        @apply absolute left-2 font-semibold text-gray-600 md:hidden;
    }
}
```

### **2. Componente Responsive Table**

```blade
{{-- resources/views/components/responsive-table.blade.php --}}
@props(['headers', 'rows'])

<div class="responsive-table w-full">
    <table class="min-w-full">
        <thead>
            <tr class="bg-gray-50">
                @foreach($headers as $header)
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($rows as $row)
                <tr>
                    @foreach($row as $index => $cell)
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" 
                            data-label="{{ $headers[$index] }}">
                            {{ $cell }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
```

---

## 🚀 Próximos Pasos Frontend

1. **Crear todos los componentes Livewire** por rol de usuario
2. **Implementar dashboards específicos** para cada tipo de usuario  
3. **Desarrollar componentes** de formularios dinámicos
4. **Crear sistema** de notificaciones en tiempo real
5. **Implementar** páginas públicas de liga
6. **Testing** de componentes y responsividad

---

*¡El frontend de FlowFast SaaS está diseñado para ser moderno, responsive y fácil de usar en cualquier dispositivo!* 🎨