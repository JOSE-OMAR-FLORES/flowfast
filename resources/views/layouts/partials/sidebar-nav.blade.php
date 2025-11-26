@php
$userType = strtolower(auth()->user()->user_type ?? 'guest');
$user = auth()->user();
@endphp

<ul class="sidebar-links" x-data="{ 
    openMenu: null,
    toggle(menu) {
        this.openMenu = this.openMenu === menu ? null : menu;
    }
}">
@if($userType === 'admin')
    <h4>
        <span>Menú Principal</span>
        <div class="menu-separator"></div>
    </h4>
    <li>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Dashboard</span>
        </a>
    </li>
    
    <h4>
        <span>Gestión</span>
        <div class="menu-separator"></div>
    </h4>
    
    <!-- Ligas con Submenú -->
    <li class="has-submenu">
        <a href="javascript:void(0)" @click="toggle('leagues')" class="{{ request()->routeIs('leagues.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <span>Ligas</span>
            <svg class="submenu-arrow" :class="{ 'rotate-180': openMenu === 'leagues' }" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-left: auto; transition: transform 0.3s;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </a>
        <ul class="submenu" x-show="openMenu === 'leagues'" x-collapse>
            <li><a href="{{ route('leagues.index') }}">📋 Ver Todas</a></li>
            <li><a href="{{ route('leagues.create') }}">➕ Crear Nueva</a></li>
        </ul>
    </li>
    
    <!-- Temporadas con Submenú -->
    <li class="has-submenu">
        <a href="javascript:void(0)" @click="toggle('seasons')" class="{{ request()->routeIs('seasons.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>Temporadas</span>
            <svg class="submenu-arrow" :class="{ 'rotate-180': openMenu === 'seasons' }" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-left: auto; transition: transform 0.3s;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </a>
        <ul class="submenu" x-show="openMenu === 'seasons'" x-collapse>
            <li><a href="{{ route('seasons.index') }}">📋 Ver Todas</a></li>
            <li><a href="{{ route('seasons.create') }}">➕ Crear Nueva</a></li>
        </ul>
    </li>
    
    <!-- Equipos con Submenú -->
    <li class="has-submenu">
        <a href="javascript:void(0)" @click="toggle('teams')" class="{{ request()->routeIs('teams.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span>Equipos</span>
            <svg class="submenu-arrow" :class="{ 'rotate-180': openMenu === 'teams' }" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-left: auto; transition: transform 0.3s;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </a>
        <ul class="submenu" x-show="openMenu === 'teams'" x-collapse>
            <li><a href="{{ route('teams.index') }}">📋 Ver Todos</a></li>
            <li><a href="{{ route('teams.create') }}">➕ Crear Nuevo</a></li>
        </ul>
    </li>
    
    <!-- Jugadores con Submenú -->
    <li class="has-submenu">
        <a href="javascript:void(0)" @click="toggle('players')" class="{{ request()->routeIs('players.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span>Jugadores</span>
            <svg class="submenu-arrow" :class="{ 'rotate-180': openMenu === 'players' }" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-left: auto; transition: transform 0.3s;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </a>
        <ul class="submenu" x-show="openMenu === 'players'" x-collapse>
            <li><a href="{{ route('players.index') }}">📋 Ver Todos</a></li>
            <li><a href="{{ route('players.create') }}">➕ Agregar Jugador</a></li>
            <li><a href="{{ route('players.import') }}">📥 Importar CSV/Excel</a></li>
        </ul>
    </li>
    
    <!-- Calendario con Submenú -->
    <li class="has-submenu">
        <a href="javascript:void(0)" @click="toggle('fixtures')" class="{{ request()->routeIs('fixtures.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            <span>Calendario</span>
            <svg class="submenu-arrow" :class="{ 'rotate-180': openMenu === 'fixtures' }" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-left: auto; transition: transform 0.3s;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </a>
        <ul class="submenu" x-show="openMenu === 'fixtures'" x-collapse>
            <li><a href="{{ route('fixtures.index') }}">📋 Ver Calendario</a></li>
            <li><a href="{{ route('fixtures.generate') }}">🎲 Generar Fixtures</a></li>
        </ul>
    </li>
    
    <!-- Tabla de Posiciones -->
    <li>
        <a href="{{ route('standings.index') }}" class="{{ request()->routeIs('standings.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span>Tabla de Posiciones</span>
        </a>
    </li>
    
    <!-- Partidos Amistosos -->
    <li>
        <a href="{{ route('friendly-matches.index') }}" class="{{ request()->routeIs('friendly-matches.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span>Partidos Amistosos</span>
        </a>
    </li>
    
    <!-- Partidos (Próximamente) -->
    <li>
        <a href="#" style="opacity: 0.5; cursor: not-allowed;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Partidos <small>(Próximamente)</small></span>
        </a>
    </li>
    
    <h4>
        <span>Administración</span>
        <div class="menu-separator"></div>
    </h4>
    
    <!-- Invitaciones con Submenú -->
    <li class="has-submenu">
        <a href="javascript:void(0)" @click="toggle('invitations')" class="{{ request()->routeIs('invitations.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <span>Invitaciones</span>
            <svg class="submenu-arrow" :class="{ 'rotate-180': openMenu === 'invitations' }" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; margin-left: auto; transition: transform 0.3s;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </a>
        <ul class="submenu" x-show="openMenu === 'invitations'" x-collapse>
            <li><a href="{{ route('invitations.index') }}">📋 Ver Todas</a></li>
            <li><a href="{{ route('invitations.create') }}">➕ Crear Nueva</a></li>
        </ul>
    </li>
    
    <!-- Árbitros -->
    <li>
        <a href="{{ route('referees.index') }}" class="{{ request()->routeIs('referees.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span>Árbitros</span>
        </a>
    </li>
    
    <!-- Entrenadores -->
    <li>
        <a href="{{ route('coaches.index') }}" class="{{ request()->routeIs('coaches.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span>Entrenadores</span>
        </a>
    </li>
    
    <!-- Apelaciones de Fecha -->
    <li>
        <a href="{{ route('appeals.index') }}" class="{{ request()->routeIs('appeals.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            <span>Apelaciones</span>
        </a>
    </li>
    
    <li>
        <a href="#" style="opacity: 0.5; cursor: not-allowed;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span>Reportes <small>(Próximamente)</small></span>
        </a>
    </li>
    <li>
        <a href="#" style="opacity: 0.5; cursor: not-allowed;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <span>Notificaciones <small>(Próximamente)</small></span>
        </a>
    </li>
    
    <h4>
        <span>Cuenta</span>
        <div class="menu-separator"></div>
    </h4>
    <li>
        <a href="{{ route('profile.edit') }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span>Perfil</span>
        </a>
    </li>
    <li>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span>Cerrar Sesión</span>
            </a>
        </form>
    </li>
@endif

@if($userType === 'league_manager')
    <h4>
        <span>Menú Principal</span>
        <div class="menu-separator"></div>
    </h4>
    <li>
        <a href="{{ route('league-manager.dashboard') }}" class="{{ request()->routeIs('league-manager.dashboard') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>
    </li>
    
    <h4>
        <span>Mi Liga</span>
        <div class="menu-separator"></div>
    </h4>
    <li>
        <a href="{{ route('seasons.index') }}" class="{{ request()->routeIs('seasons.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Temporadas
        </a>
    </li>
    <li>
        <a href="{{ route('teams.index') }}" class="{{ request()->routeIs('teams.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            Equipos
        </a>
    </li>
    <li>
        <a href="{{ route('fixtures.index') }}" class="{{ request()->routeIs('fixtures.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            Calendario
        </a>
    </li>
    <li>
        <a href="{{ route('standings.index') }}" class="{{ request()->routeIs('standings.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Tabla de Posiciones
        </a>
    </li>
    
    <!-- Árbitros -->
    <li>
        <a href="{{ route('referees.index') }}" class="{{ request()->routeIs('referees.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span>Árbitros</span>
        </a>
    </li>
    
    <!-- Entrenadores -->
    <li>
        <a href="{{ route('coaches.index') }}" class="{{ request()->routeIs('coaches.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span>Entrenadores</span>
        </a>
    </li>
    
    <h4>
        <span>Cuenta</span>
        <div class="menu-separator"></div>
    </h4>
    <li>
        <a href="#">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Perfil
        </a>
    </li>
    <li>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Cerrar Sesión
            </a>
        </form>
    </li>
@endif

@if($userType === 'coach')
    <h4>
        <span>Mi Dashboard</span>
        <div class="menu-separator"></div>
    </h4>
    <li>
        <a href="{{ route('coach.dashboard') }}" class="{{ request()->routeIs('coach.dashboard') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Dashboard</span>
        </a>
    </li>
    
    <h4>
        <span>Gestión</span>
        <div class="menu-separator"></div>
    </h4>
    <li>
        <a href="{{ route('coach.teams.index') }}" class="{{ request()->routeIs('coach.teams.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span>Mis Equipos</span>
        </a>
    </li>
    <li>
        <a href="{{ route('coach.players.index') }}" class="{{ request()->routeIs('coach.players.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span>Mis Jugadores</span>
        </a>
    </li>
    <li>
        <a href="{{ route('coach.fixtures') }}" class="{{ request()->routeIs('coach.fixtures') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>Partidos</span>
        </a>
    </li>
    <li>
        <a href="{{ route('coach.payments.index') }}" class="{{ request()->routeIs('coach.payments.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Pagos</span>
        </a>
    </li>
    <li>
        <a href="{{ route('coach.standings') }}" class="{{ request()->routeIs('coach.standings') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span>Tabla de Posiciones</span>
        </a>
    </li>
    <li>
        <a href="{{ route('coach.appeals') }}" class="{{ request()->routeIs('coach.appeals') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            <span>Apelaciones</span>
        </a>
    </li>
    
    <h4>
        <span>Cuenta</span>
        <div class="menu-separator"></div>
    </h4>
    <li>
        <a href="{{ route('profile.edit') }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span>Perfil</span>
        </a>
    </li>
    <li>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Cerrar Sesión
            </a>
        </form>
    </li>
@endif

@if($userType === 'player')
    <h4>
        <span>Mi Dashboard</span>
        <div class="menu-separator"></div>
    </h4>
    <li>
        <a href="{{ route('teams.index') }}" class="{{ request()->routeIs('teams.index') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Mi Equipo
        </a>
    </li>
    <li>
        <a href="#">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Mis Estadísticas
        </a>
    </li>
    <li>
        <a href="{{ route('standings.index') }}" class="{{ request()->routeIs('standings.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Tabla de Posiciones
        </a>
    </li>
    
    <h4>
        <span>Cuenta</span>
        <div class="menu-separator"></div>
    </h4>
    <li>
        <a href="#">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Perfil
        </a>
    </li>
    <li>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Cerrar Sesión
            </a>
        </form>
    </li>
@endif

@if($userType === 'referee')
    <h4>
        <span>Mi Dashboard</span>
        <div class="menu-separator"></div>
    </h4>
    <li>
        <a href="{{ route('referee.dashboard') }}" class="{{ request()->routeIs('referee.dashboard') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Dashboard</span>
        </a>
    </li>
    <li>
        <a href="{{ route('referee.matches.index') }}" class="{{ request()->routeIs('referee.matches.index') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            <span>Mis Partidos</span>
        </a>
    </li>
    
    <h4>
        <span>Finanzas</span>
        <div class="menu-separator"></div>
    </h4>
    <li>
        <a href="{{ route('referee.my-payments') }}" class="{{ request()->routeIs('referee.my-payments') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Mis Pagos</span>
        </a>
    </li>
    
    <h4>
        <span>Cuenta</span>
        <div class="menu-separator"></div>
    </h4>
    <li>
        <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span>Perfil</span>
        </a>
    </li>
    <li>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span>Cerrar Sesión</span>
            </a>
        </form>
    </li>
@endif

@if($userType === 'observer')
    <h4>
        <span>Dashboard</span>
        <div class="menu-separator"></div>
    </h4>
    <li>
        <a href="{{ route('observer.dashboard') }}" class="{{ request()->routeIs('observer.dashboard') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>
    </li>
    <li>
        <a href="#">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            Ver Ligas
        </a>
    </li>
    
    <h4>
        <span>Cuenta</span>
        <div class="menu-separator"></div>
    </h4>
    <li>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Cerrar Sesión
            </a>
        </form>
    </li>
@endif
</ul>
