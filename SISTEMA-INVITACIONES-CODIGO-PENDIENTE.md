# 📧 Sistema de Invitaciones - Código Pendiente de Implementar

## Estado: 60% Completado

**Completado**:
- ✅ Modelo `InvitationToken` (existente)
- ✅ API Controller `InvitationController` (existente)
- ✅ Componentes Livewire creados (Index, Create, Accept)
- ✅ Mailable `InvitationMail` con vista de email
- ✅ Vista de email `emails/invitation.blade.php`
- ✅ Lógica PHP de Index, Create (parcial Accept)

**Pendiente**:
- ⏳ Vistas blade completas (index, create, accept)
- ⏳ Componente Accept (lógica completa)
- ⏳ Rutas web
- ⏳ Actualizar sidebar
- ⏳ Pruebas

---

## 1. Vista Index Completa (`invitations/index.blade.php`)

**REEMPLAZAR** el contenido de `resources/views/livewire/invitations/index.blade.php` con:

```blade
<div>
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Invitaciones</h1>
                <p class="mt-1 text-sm text-gray-500">Gestiona las invitaciones enviadas a usuarios</p>
            </div>
            <a href="{{ route('invitations.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                + Nueva Invitación
            </a>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Búsqueda --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Token, liga o equipo..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            {{-- Filtro por tipo --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                <select wire:model.live="tokenTypeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Todos los tipos</option>
                    <option value="league_manager">Encargado de Liga</option>
                    <option value="coach">Entrenador</option>
                    <option value="player">Jugador</option>
                    <option value="referee">Árbitro</option>
                </select>
            </div>

            {{-- Filtro por liga --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Liga</label>
                <select wire:model.live="leagueFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Todas las ligas</option>
                    @foreach($leagues as $league)
                        <option value="{{ $league->id }}">{{ $league->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro por estado --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select wire:model.live="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Todos los estados</option>
                    <option value="valid">Válidos</option>
                    <option value="expired">Expirados</option>
                    <option value="exhausted">Agotados</option>
                </select>
            </div>
        </div>

        {{-- Botón limpiar filtros --}}
        @if($search || $tokenTypeFilter || $leagueFilter || $statusFilter)
            <div class="mt-3">
                <button wire:click="clearFilters" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                    Limpiar filtros
                </button>
            </div>
        @endif
    </div>

    {{-- Lista de tokens --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($tokens->isEmpty())
            <div class="p-12 text-center">
                <div class="text-gray-400 mb-3">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <p class="text-gray-600 text-lg mb-2">No hay invitaciones</p>
                <p class="text-sm text-gray-500">Crea una nueva invitación para invitar usuarios</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Token</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Liga/Equipo</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Usos</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Expira</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($tokens as $token)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ Str::limit($token->token, 12) }}</code>
                                        <button 
                                            wire:click="copyToken({{ $token->id }})"
                                            class="text-gray-400 hover:text-gray-600"
                                            title="Copiar enlace"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        @if($token->token_type === 'league_manager') bg-purple-100 text-purple-800
                                        @elseif($token->token_type === 'coach') bg-blue-100 text-blue-800
                                        @elseif($token->token_type === 'player') bg-green-100 text-green-800
                                        @elseif($token->token_type === 'referee') bg-yellow-100 text-yellow-800
                                        @endif
                                    ">
                                        @if($token->token_type === 'league_manager') Encargado
                                        @elseif($token->token_type === 'coach') Entrenador
                                        @elseif($token->token_type === 'player') Jugador
                                        @elseif($token->token_type === 'referee') Árbitro
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900">{{ $token->targetLeague->name ?? '-' }}</div>
                                        @if($token->targetTeam)
                                            <div class="text-gray-500">{{ $token->targetTeam->name }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm {{ $token->current_uses >= $token->max_uses ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                        {{ $token->current_uses }} / {{ $token->max_uses }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                    {{ $token->expires_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($token->current_uses >= $token->max_uses)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                            Agotado
                                        </span>
                                    @elseif($token->expires_at->isPast())
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                            Expirado
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            Activo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <button 
                                        wire:click="revokeToken({{ $token->id }})"
                                        wire:confirm="¿Estás seguro de revocar este token? Esta acción no se puede deshacer."
                                        class="text-red-600 hover:text-red-700 text-sm font-medium"
                                    >
                                        Revocar
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $tokens->links() }}
            </div>
        @endif
    </div>

    {{-- Scripts para copiar token --}}
    @script
    <script>
        $wire.on('token-copied', (event) => {
            const url = event[0].url;
            navigator.clipboard.writeText(url).then(() => {
                alert('Enlace de invitación copiado al portapapeles:\n\n' + url);
            });
        });

        $wire.on('success', (event) => {
            alert(event[0]);
        });

        $wire.on('error', (event) => {
            alert('Error: ' + event[0]);
        });
    </script>
    @endscript
</div>
```

---

## 2. Vista Create Completa (`invitations/create.blade.php`)

**REEMPLAZAR** el contenido de `resources/views/livewire/invitations/create.blade.php` con:

```blade
<div>
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Crear Invitación</h1>
                <p class="mt-1 text-sm text-gray-500">Genera un enlace de invitación para nuevos usuarios</p>
            </div>
            <a href="{{ route('invitations.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg font-medium transition-colors">
                ← Volver
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Formulario --}}
        <div class="lg:col-span-2">
            <form wire:submit="create" class="bg-white rounded-lg shadow-sm p-6 space-y-6">
                {{-- Tipo de Invitación --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Invitación *</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all {{ $tokenType === 'league_manager' ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <input type="radio" wire:model.live="tokenType" value="league_manager" class="sr-only">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">👔</span>
                                    <span class="font-semibold">Encargado de Liga</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Gestión completa de la liga</p>
                            </div>
                        </label>

                        <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all {{ $tokenType === 'coach' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <input type="radio" wire:model.live="tokenType" value="coach" class="sr-only">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">🎯</span>
                                    <span class="font-semibold">Entrenador</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Gestión de un equipo</p>
                            </div>
                        </label>

                        <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all {{ $tokenType === 'player' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <input type="radio" wire:model.live="tokenType" value="player" class="sr-only">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">⚽</span>
                                    <span class="font-semibold">Jugador</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Jugador de un equipo</p>
                            </div>
                        </label>

                        <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all {{ $tokenType === 'referee' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <input type="radio" wire:model.live="tokenType" value="referee" class="sr-only">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl">🟨</span>
                                    <span class="font-semibold">Árbitro</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Arbitraje de partidos</p>
                            </div>
                        </label>
                    </div>
                    @error('tokenType') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Liga --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Liga *</label>
                    <select wire:model.live="leagueId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Selecciona una liga</option>
                        @foreach($leagues as $league)
                            <option value="{{ $league->id }}">{{ $league->name }}</option>
                        @endforeach
                    </select>
                    @error('leagueId') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Equipo (solo para coach y player) --}}
                @if(in_array($tokenType, ['coach', 'player']))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Equipo *</label>
                        <select wire:model="teamId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Selecciona un equipo</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                        @error('teamId') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                @endif

                {{-- Configuración --}}
                <div class="border-t pt-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Configuración del Token</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Máximo de Usos</label>
                            <input 
                                type="number" 
                                wire:model="maxUses" 
                                min="1" 
                                max="100"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            @error('maxUses') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Expira en (días)</label>
                            <input 
                                type="number" 
                                wire:model="expiresInDays" 
                                min="1" 
                                max="365"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            @error('expiresInDays') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Enviar por Email --}}
                <div class="border-t pt-6">
                    <div class="flex items-center gap-3 mb-4">
                        <input 
                            type="checkbox" 
                            wire:model.live="sendEmail" 
                            id="sendEmail"
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        >
                        <label for="sendEmail" class="text-sm font-medium text-gray-700 cursor-pointer">
                            Enviar invitación por email
                        </label>
                    </div>

                    @if($sendEmail)
                        <div class="space-y-4 pl-7">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email del destinatario *</label>
                                <input 
                                    type="email" 
                                    wire:model="recipientEmail"
                                    placeholder="usuario@ejemplo.com"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                @error('recipientEmail') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del destinatario (opcional)</label>
                                <input 
                                    type="text" 
                                    wire:model="recipientName"
                                    placeholder="Juan Pérez"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Botón Crear --}}
                <div class="flex gap-3 pt-6 border-t">
                    <button 
                        type="submit"
                        class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors"
                    >
                        Crear Invitación
                    </button>
                    <a 
                        href="{{ route('invitations.index') }}"
                        class="px-6 py-3 border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold rounded-lg transition-colors"
                    >
                        Cancelar
                    </a>
                </div>
            </form>
        </div>

        {{-- Información --}}
        <div class="space-y-6">
            {{-- Info Card --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-2">ℹ️ Información</h3>
                <ul class="text-sm text-blue-800 space-y-2">
                    <li>• El token se genera automáticamente</li>
                    <li>• Puedes configurar usos y expiración</li>
                    <li>• El enlace se puede copiar o enviar por email</li>
                    <li>• Los tokens pueden ser revocados en cualquier momento</li>
                </ul>
            </div>

            {{-- Roles Info --}}
            <div class="bg-white rounded-lg shadow-sm p-4">
                <h3 class="font-semibold text-gray-900 mb-3">Permisos por Rol</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <div class="font-medium text-purple-700">👔 Encargado de Liga</div>
                        <p class="text-gray-600 text-xs mt-1">Gestión completa de la liga: equipos, temporadas, partidos, finanzas</p>
                    </div>
                    <div>
                        <div class="font-medium text-blue-700">🎯 Entrenador</div>
                        <p class="text-gray-600 text-xs mt-1">Gestión de su equipo: jugadores, alineaciones</p>
                    </div>
                    <div>
                        <div class="font-medium text-green-700">⚽ Jugador</div>
                        <p class="text-gray-600 text-xs mt-1">Ver partidos, estadísticas personales</p>
                    </div>
                    <div>
                        <div class="font-medium text-yellow-700">🟨 Árbitro</div>
                        <p class="text-gray-600 text-xs mt-1">Gestionar partidos asignados, registrar resultados</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de éxito --}}
    @script
    <script>
        $wire.on('invitation-created', (event) => {
            const data = event[0];
            const message = data.sentEmail 
                ? 'Invitación creada y enviada por email exitosamente!'
                : 'Invitación creada exitosamente!';
            
            alert(message + '\n\nEnlace: ' + data.url);
            window.location.href = '{{ route("invitations.index") }}';
        });

        $wire.on('error', (event) => {
            alert('Error: ' + event[0]);
        });
    </script>
    @endscript
</div>
```

---

## 3. Componente Accept (PHP) - COMPLETAR

**Archivo**: `app/Livewire/Invitations/Accept.php`

```php
<?php

namespace App\Livewire\Invitations;

use Livewire\Component;
use App\Models\InvitationToken;
use App\Models\User;
use App\Models\LeagueManager;
use App\Models\Coach;
use App\Models\Player;
use App\Models\Referee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class Accept extends Component
{
    public $token;
    public $invitation;
    public $name = '';
    public $email = '';
    public $password = '';
    public $passwordConfirmation = '';

    public $error = null;

    public function mount($token)
    {
        $this->token = $token;
        
        // Validar token
        $this->invitation = InvitationToken::where('token', $token)
            ->with(['targetLeague', 'targetTeam'])
            ->first();

        if (!$this->invitation) {
            $this->error = 'Token de invitación no válido';
            return;
        }

        // Verificar si está expirado
        if ($this->invitation->expires_at->isPast()) {
            $this->error = 'Esta invitación ha expirado';
            return;
        }

        // Verificar si está agotado
        if ($this->invitation->current_uses >= $this->invitation->max_uses) {
            $this->error = 'Esta invitación ya ha sido utilizada el máximo de veces';
            return;
        }
    }

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8|same:passwordConfirmation',
        'passwordConfirmation' => 'required',
    ];

    protected $messages = [
        'name.required' => 'El nombre es requerido',
        'email.required' => 'El email es requerido',
        'email.email' => 'El email debe ser válido',
        'email.unique' => 'Este email ya está registrado',
        'password.required' => 'La contraseña es requerida',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres',
        'password.same' => 'Las contraseñas no coinciden',
    ];

    public function accept()
    {
        if ($this->error) {
            return;
        }

        $this->validate();

        try {
            DB::beginTransaction();

            // Crear usuario
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'user_type' => $this->invitation->token_type,
            ]);

            // Crear registro específico según el tipo
            switch ($this->invitation->token_type) {
                case 'league_manager':
                    $userable = LeagueManager::create([
                        'user_id' => $user->id,
                    ]);
                    // Asociar con la liga
                    $this->invitation->targetLeague->managers()->attach($userable->id);
                    break;

                case 'coach':
                    $userable = Coach::create([
                        'user_id' => $user->id,
                        'team_id' => $this->invitation->target_team_id,
                    ]);
                    break;

                case 'player':
                    $userable = Player::create([
                        'user_id' => $user->id,
                        'team_id' => $this->invitation->target_team_id,
                    ]);
                    break;

                case 'referee':
                    $userable = Referee::create([
                        'user_id' => $user->id,
                    ]);
                    // Asociar con la liga
                    $this->invitation->targetLeague->referees()->attach($userable->id);
                    break;
            }

            $user->update(['userable_id' => $userable->id]);

            // Incrementar uso del token
            $this->invitation->increment('current_uses');

            DB::commit();

            // Login automático
            auth()->login($user);

            // Redireccionar al dashboard
            return redirect('/admin');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error = 'Error al procesar la invitación: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.invitations.accept')->layout('layouts.public', ['title' => 'Aceptar Invitación']);
    }
}
```

---

## 4. Vista Accept (`invitations/accept.blade.php`)

**REEMPLAZAR** el contenido de `resources/views/livewire/invitations/accept.blade.php` con:

```blade
<div class="min-h-screen bg-gradient-to-br from-blue-500 to-indigo-700 flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full">
        @if($error)
            {{-- Error Card --}}
            <div class="bg-white rounded-lg shadow-xl p-8 text-center">
                <div class="text-red-500 mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Invitación No Válida</h2>
                <p class="text-gray-600 mb-6">{{ $error }}</p>
                <a href="/" class="inline-block px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                    Ir al Inicio
                </a>
            </div>
        @else
            {{-- Formulario de Registro --}}
            <div class="bg-white rounded-lg shadow-xl p-8">
                {{-- Logo --}}
                <div class="text-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">FlowFast</h1>
                    <p class="text-gray-600 mt-2">Has sido invitado a unirte</p>
                </div>

                {{-- Info de la Invitación --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <span class="text-3xl">
                            @if($invitation->token_type === 'league_manager') 👔
                            @elseif($invitation->token_type === 'coach') 🎯
                            @elseif($invitation->token_type === 'player') ⚽
                            @elseif($invitation->token_type === 'referee') 🟨
                            @endif
                        </span>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900">
                                @if($invitation->token_type === 'league_manager') Encargado de Liga
                                @elseif($invitation->token_type === 'coach') Entrenador
                                @elseif($invitation->token_type === 'player') Jugador
                                @elseif($invitation->token_type === 'referee') Árbitro
                                @endif
                            </div>
                            <div class="text-sm text-gray-700 mt-1">
                                <strong>{{ $invitation->targetLeague->name }}</strong>
                                @if($invitation->targetTeam)
                                    <br>Equipo: {{ $invitation->targetTeam->name }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Formulario --}}
                <form wire:submit="accept" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo *</label>
                        <input 
                            type="text" 
                            wire:model="name"
                            placeholder="Juan Pérez"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input 
                            type="email" 
                            wire:model="email"
                            placeholder="tu@email.com"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                        @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña *</label>
                        <input 
                            type="password" 
                            wire:model="password"
                            placeholder="Mínimo 8 caracteres"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                        @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña *</label>
                        <input 
                            type="password" 
                            wire:model="passwordConfirmation"
                            placeholder="Repite tu contraseña"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                        @error('passwordConfirmation') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <button 
                        type="submit"
                        class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors mt-6"
                    >
                        Aceptar Invitación y Crear Cuenta
                    </button>
                </form>

                <p class="text-xs text-gray-500 text-center mt-6">
                    Al aceptar, te registrarás en FlowFast y serás asignado automáticamente al rol correspondiente.
                </p>
            </div>
        @endif
    </div>
</div>
```

---

## 5. Registrar Rutas

**Agregar** en `routes/web.php`:

```php
use App\Livewire\Invitations\Index as InvitationsIndex;
use App\Livewire\Invitations\Create as InvitationsCreate;
use App\Livewire\Invitations\Accept as InvitationsAccept;

// Ruta pública para aceptar invitaciones
Route::get('/invite/{token}', InvitationsAccept::class)->name('invite.accept');

// Rutas autenticadas de invitaciones
Route::middleware(['auth'])->group(function () {
    Route::middleware(['role:admin,league_manager'])->group(function () {
        Route::get('/admin/invitations', InvitationsIndex::class)->name('invitations.index');
        Route::get('/admin/invitations/create', InvitationsCreate::class)->name('invitations.create');
    });
});
```

---

## 6. Actualizar Sidebar

**Agregar** en `resources/views/components/sidebar-nav.blade.php` dentro del bloque de Admin/League Manager:

```blade
{{-- Invitaciones --}}
<a href="{{ route('invitations.index') }}" 
   class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('invitations.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
    </svg>
    <span>Invitaciones</span>
</a>
```

---

## 7. Pruebas Manuales

### Flujo Completo

1. **Login como Admin**:
   ```
   http://localhost/login
   ```

2. **Ir a Invitaciones**:
   ```
   http://localhost/admin/invitations
   ```

3. **Crear Nueva Invitación**:
   - Tipo: Jugador
   - Liga: Liga Premier
   - Equipo: Tigres FC
   - Usos: 5
   - Expira: 7 días
   - Email: (opcional) jugador@test.com

4. **Copiar Enlace**:
   ```
   http://localhost/invite/ABC123...
   ```

5. **Abrir Enlace (en navegador privado)**:
   - Ver formulario de registro
   - Completar datos
   - Aceptar invitación

6. **Verificar**:
   - Usuario creado
   - Rol asignado
   - Login automático
   - Redirección a dashboard

---

## 8. Archivos Creados/Modificados

| Archivo | Estado | Descripción |
|---------|--------|-------------|
| `app/Livewire/Invitations/Index.php` | ✅ | Lógica del listado |
| `app/Livewire/Invitations/Create.php` | ✅ | Lógica de creación |
| `app/Livewire/Invitations/Accept.php` | ⏳ | Lógica de aceptación (pendiente completar) |
| `app/Mail/InvitationMail.php` | ✅ | Mailable para emails |
| `resources/views/emails/invitation.blade.php` | ✅ | Vista del email |
| `resources/views/livewire/invitations/index.blade.php` | ⏳ | Vista del listado (pendiente reemplazar) |
| `resources/views/livewire/invitations/create.blade.php` | ⏳ | Vista de creación (pendiente reemplazar) |
| `resources/views/livewire/invitations/accept.blade.php` | ⏳ | Vista de aceptación (pendiente reemplazar) |
| `routes/web.php` | ⏳ | Rutas (pendiente agregar) |
| `resources/views/components/sidebar-nav.blade.php` | ⏳ | Menú (pendiente agregar) |

---

## Resumen

- **Implementado**: 60% (modelos, controladores, lógica PHP parcial, email)
- **Pendiente**: 40% (vistas blade completas, rutas, sidebar, pruebas)

**Próximo Paso**: Copiar y pegar el código de este documento en los archivos correspondientes.
