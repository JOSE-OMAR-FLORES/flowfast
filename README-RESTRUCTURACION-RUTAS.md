# Restructuración de Rutas por Roles

## 📋 Resumen de Cambios

Se ha reorganizado completamente la estructura de rutas para que cada tipo de usuario tenga su propia área con URLs específicas, siguiendo las mejores prácticas de segregación por roles.

## 🗺️ Nueva Estructura de Rutas

### **1. Administradores y Encargados de Liga**
**Área:** `/admin/*`

```php
Route::middleware(['role:admin,league_manager'])->group(function () {
    Route::get('/admin', AdminDashboard::class)->name('admin.dashboard');
    Route::get('/admin/leagues', LeaguesIndex::class)->name('leagues.index');
    Route::get('/admin/seasons', SeasonsIndex::class)->name('seasons.index');
    Route::get('/admin/teams', TeamsIndex::class)->name('teams.index');
    Route::get('/admin/fixtures', FixturesIndex::class)->name('fixtures.index');
    Route::get('/admin/matches/{matchId}/live', Live::class)->name('matches.live');
    Route::get('/admin/invitations', InvitationsIndex::class)->name('invitations.index');
    Route::get('/admin/players', PlayersIndex::class)->name('players.index');
    Route::get('/admin/financial/*', ...)->name('financial.*');
});
```

### **2. Árbitros (Referees)**
**Área:** `/referee/*`

```php
Route::middleware(['role:referee'])->prefix('referee')->name('referee.')->group(function () {
    Route::get('/matches', FixturesIndex::class)->name('matches.index');
    Route::get('/matches/{matchId}/live', Live::class)->name('matches.live');
});
```

**URLs de Árbitros:**
- 🏠 Dashboard: `http://flowfast-saas.test/referee/matches`
- 🎮 Partido en vivo: `http://flowfast-saas.test/referee/matches/46/live`

### **3. Entrenadores (Coaches)**
**Área:** `/coach/*`

```php
Route::middleware(['role:coach'])->prefix('coach')->name('coach.')->group(function () {
    Route::get('/teams', TeamsIndex::class)->name('teams.index');
    Route::get('/teams/{team}/edit', TeamsEdit::class)->name('teams.edit');
    Route::get('/players', PlayersIndex::class)->name('players.index');
    Route::get('/fixtures', FixturesIndex::class)->name('fixtures.index');
});
```

**URLs de Entrenadores:**
- 🏠 Dashboard: `http://flowfast-saas.test/coach/teams`
- 👥 Jugadores: `http://flowfast-saas.test/coach/players`
- 📅 Partidos: `http://flowfast-saas.test/coach/fixtures`

### **4. Jugadores (Players)**
**Área:** `/player/*`

```php
Route::middleware(['role:player'])->prefix('player')->name('player.')->group(function () {
    Route::get('/team', TeamsIndex::class)->name('team.index');
    Route::get('/fixtures', FixturesIndex::class)->name('fixtures.index');
    Route::get('/standings', StandingsIndex::class)->name('standings.index');
});
```

**URLs de Jugadores:**
- 🏠 Dashboard: `http://flowfast-saas.test/player/team`
- 📅 Partidos: `http://flowfast-saas.test/player/fixtures`
- 📊 Tabla: `http://flowfast-saas.test/player/standings`

## 🔄 Redirects después de Registro

### Archivo: `app/Livewire/Invitations/Accept.php`

```php
$redirectUrl = match($this->invitation->token_type) {
    'league_manager' => route('admin.dashboard'),          // /admin
    'coach' => route('coach.teams.index'),                  // /coach/teams
    'player' => route('player.team.index'),                 // /player/team
    'referee' => route('referee.matches.index'),            // /referee/matches
    default => route('admin.dashboard'),
};
```

## 🎨 Sidebar Actualizado

### Archivo: `resources/views/layouts/partials/sidebar-nav.blade.php`

Cada tipo de usuario ahora tiene su propio menú con rutas correctas:

#### **Árbitros:**
```blade
@if($userType === 'referee')
    <li>
        <a href="{{ route('referee.matches.index') }}">
            Mis Partidos
        </a>
    </li>
    <li>
        <a href="#">Perfil</a>
    </li>
    <li>
        <form method="POST" action="{{ route('logout') }}">
            Cerrar Sesión
        </form>
    </li>
@endif
```

#### **Entrenadores:**
```blade
@if($userType === 'coach')
    <li>
        <a href="{{ route('coach.teams.index') }}">Mi Equipo</a>
    </li>
    <li>
        <a href="{{ route('coach.players.index') }}">Jugadores</a>
    </li>
    <li>
        <a href="{{ route('coach.fixtures.index') }}">Partidos</a>
    </li>
@endif
```

#### **Jugadores:**
```blade
@if($userType === 'player')
    <li>
        <a href="{{ route('player.team.index') }}">Mi Equipo</a>
    </li>
    <li>
        <a href="{{ route('player.fixtures.index') }}">Partidos</a>
    </li>
    <li>
        <a href="{{ route('player.standings.index') }}">Tabla</a>
    </li>
@endif
```

## 🔧 Corrección de Asignación de Árbitros

### Problema Original:
- Al abrir el modal de asignar árbitro, solo aparecía "main" en el dropdown
- No se filtraban árbitros por liga

### Solución Implementada:

#### Archivo: `app/Livewire/Matches/Live.php`

```php
public function loadAvailableReferees()
{
    // Obtener la liga del partido
    $leagueId = $this->match->season->league_id;
    
    // Obtener todos los referees asignados a esta liga
    $this->availableReferees = User::where('user_type', 'referee')
        ->whereHas('userable', function($query) use ($leagueId) {
            $query->where('league_id', $leagueId);
        })
        ->with('userable')
        ->get()
        ->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'full_name' => $user->userable->first_name . ' ' . $user->userable->last_name,
            ];
        });
}
```

#### Archivo: `resources/views/livewire/matches/live.blade.php`

```blade
<select wire:model="selectedRefereeId" class="...">
    <option value="">Selecciona un árbitro</option>
    @foreach($availableReferees as $ref)
        <option value="{{ $ref['id'] }}">
            {{ $ref['full_name'] }}
        </option>
    @endforeach
</select>

@if(count($availableReferees) === 0)
    <p class="text-red-600">
        ⚠️ No hay árbitros asignados a esta liga. 
        Primero debes invitar árbitros a la liga.
    </p>
@endif
```

## 📊 Tabla de Acceso por Rol

| Ruta | Admin | League Manager | Coach | Player | Referee |
|------|-------|----------------|-------|--------|---------|
| `/admin` | ✅ | ✅ | ❌ | ❌ | ❌ |
| `/admin/leagues` | ✅ | ✅ | ❌ | ❌ | ❌ |
| `/admin/teams` | ✅ | ✅ | ✅ | ❌ | ❌ |
| `/admin/fixtures` | ✅ | ✅ | ❌ | ❌ | ❌ |
| `/admin/matches/{id}/live` | ✅ | ✅ | ❌ | ❌ | ❌ |
| `/referee/matches` | ❌ | ❌ | ❌ | ❌ | ✅ |
| `/referee/matches/{id}/live` | ❌ | ❌ | ❌ | ❌ | ✅ |
| `/coach/teams` | ❌ | ❌ | ✅ | ❌ | ❌ |
| `/coach/players` | ❌ | ❌ | ✅ | ❌ | ❌ |
| `/player/team` | ❌ | ❌ | ❌ | ✅ | ❌ |
| `/player/fixtures` | ❌ | ❌ | ❌ | ✅ | ❌ |

## 🎯 Beneficios de la Restructuración

### **1. Claridad y Seguridad**
- ✅ URLs claras que indican el rol del usuario
- ✅ Middleware específico por área
- ✅ No se puede acceder a rutas de otros roles

### **2. Mantenibilidad**
- ✅ Código organizado por roles
- ✅ Fácil agregar nuevas rutas por rol
- ✅ Prefijos consistentes (`/admin`, `/referee`, `/coach`, `/player`)

### **3. Escalabilidad**
- ✅ Fácil agregar nuevos roles
- ✅ Separación de responsabilidades
- ✅ Testing más sencillo por rol

### **4. UX Mejorada**
- ✅ Cada usuario ve solo lo que necesita
- ✅ URLs significativas
- ✅ Sidebar específico por rol

## 🔐 Middleware de Roles

El middleware `role` valida el acceso:

```php
// app/Http/Middleware/RoleMiddleware.php
public function handle($request, Closure $next, ...$roles)
{
    $user = auth()->user();
    
    if (!$user || !in_array($user->user_type, $roles)) {
        abort(403, 'No tienes permisos para acceder a esta área');
    }
    
    return $next($request);
}
```

## 📝 Cambios en Archivos

### Archivos Modificados:
1. ✅ `routes/web.php` - Restructuración completa de rutas
2. ✅ `app/Livewire/Invitations/Accept.php` - Redirects actualizados
3. ✅ `resources/views/layouts/partials/sidebar-nav.blade.php` - Rutas de sidebar
4. ✅ `app/Livewire/Matches/Live.php` - Filtrado de árbitros por liga
5. ✅ `resources/views/livewire/matches/live.blade.php` - Modal de árbitros

### Archivos Creados:
1. 📄 `README-RESTRUCTURACION-RUTAS.md` - Este documento

## 🚀 Testing

### Referee:
```bash
# 1. Registrarse como referee con invitación
http://flowfast-saas.test/invite/{token}

# 2. Después del registro, debería ir a:
http://flowfast-saas.test/referee/matches

# 3. Click en un partido asignado:
http://flowfast-saas.test/referee/matches/46/live
```

### Coach:
```bash
# 1. Registrarse como coach con invitación
http://flowfast-saas.test/invite/{token}

# 2. Después del registro, debería ir a:
http://flowfast-saas.test/coach/teams
```

### Player:
```bash
# 1. Registrarse como player con invitación
http://flowfast-saas.test/invite/{token}

# 2. Después del registro, debería ir a:
http://flowfast-saas.test/player/team
```

## ⚠️ Notas Importantes

1. **Árbitros por Liga**: Los árbitros se filtran por `league_id` en la tabla `referees`
2. **Primera Asignación**: Debes invitar árbitros a la liga ANTES de poder asignarlos a partidos
3. **Validación de Inicio**: Un partido NO puede iniciar sin al menos un árbitro asignado
4. **Caché**: Después de cambios en rutas, ejecutar `php artisan optimize:clear`

## 🔄 Migración de Usuarios Existentes

Si ya tienes usuarios registrados con las rutas antiguas, no hay problema:
- Las redirecciones automáticas los llevarán al área correcta
- El sidebar se actualiza dinámicamente según `$userType`
- El middleware valida y bloquea accesos no autorizados

## 📖 Referencias

- [README-AUTH.md](README-AUTH.md) - Sistema completo de autenticación
- [README-ASIGNACION-ARBITROS.md](README-ASIGNACION-ARBITROS.md) - Sistema de asignación de árbitros
