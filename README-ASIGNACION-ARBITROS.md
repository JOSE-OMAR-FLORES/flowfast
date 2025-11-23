# Sistema de Asignación de Árbitros

## 📋 Resumen
Se ha implementado un sistema completo para asignar uno o más árbitros a los partidos antes de iniciarlos. El sistema previene el inicio de partidos sin al menos un árbitro asignado.

## 🗄️ Estructura de Base de Datos

### Nueva Tabla: `fixture_referees`
Tabla pivot para la relación muchos-a-muchos entre fixtures y árbitros.

```sql
- id (bigint, PK)
- fixture_id (foreign key -> fixtures)
- user_id (foreign key -> users)
- referee_type (enum: main, assistant, fourth_official)
- timestamps
- UNIQUE(fixture_id, user_id) // Un árbitro no puede estar asignado dos veces al mismo partido
```

### Tipos de Árbitro
- **main**: Árbitro principal
- **assistant**: Árbitro asistente  
- **fourth_official**: Cuarto árbitro

## 🔧 Cambios en el Modelo

### `app/Models/Fixture.php`

#### Nueva Relación
```php
public function referees(): BelongsToMany
{
    return $this->belongsToMany(User::class, 'fixture_referees', 'fixture_id', 'user_id')
                ->withPivot('referee_type')
                ->withTimestamps();
}
```

#### Validación Actualizada
```php
public function canStart(): bool
{
    // Solo puede iniciar si está programado Y tiene al menos un árbitro asignado
    return $this->status === 'scheduled' && $this->referees()->count() > 0;
}
```

## 💻 Componente Livewire

### `app/Livewire/Matches/Live.php`

#### Nuevas Propiedades
```php
public $showRefereeModal = false;
public $selectedRefereeId = '';
public $selectedRefereeType = 'main';
public $availableReferees = [];
```

#### Nuevos Métodos

**loadAvailableReferees()**: Carga todos los usuarios tipo referee disponibles

**openRefereeModal()**: Abre el modal de asignación de árbitros

**closeRefereeModal()**: Cierra el modal

**addReferee()**: Asigna un árbitro al partido con validaciones:
- Verifica que el árbitro no esté ya asignado
- Guarda en la tabla pivot `fixture_referees`
- Incluye el tipo de árbitro

**removeReferee($userId)**: Remueve un árbitro del partido

**startMatch()**: Validación mejorada:
```php
if (!$this->match->canStart()) {
    $message = $this->match->referees()->count() === 0 
        ? 'No puedes iniciar el partido sin asignar al menos un árbitro.'
        : 'El partido no puede ser iniciado.';
    session()->flash('error', $message);
    return;
}
```

## 🎨 Interfaz de Usuario

### Vista: `resources/views/livewire/matches/live.blade.php`

#### Sección de Árbitros (Sidebar)
Ubicada al inicio del sidebar, muestra:
- **Lista de árbitros asignados** con:
  - Nombre del árbitro
  - Tipo de árbitro (Principal, Asistente, Cuarto árbitro)
  - Botón para remover (solo si el partido está `scheduled`)
- **Botón "+ Asignar"**: Visible solo si el partido está `scheduled`
- **Mensaje de advertencia**: Si no hay árbitros asignados

#### Modal de Asignación
- **Select de Árbitros**: Todos los usuarios con `user_type = 'referee'`
- **Select de Tipo**: Main, Assistant, Fourth Official
- **Botones**: Asignar / Cancelar

### Estados Visuales
- 🟢 **Principal** (main)
- 🔵 **Asistente** (assistant)
- 🟡 **Cuarto Árbitro** (fourth_official)

## 🔒 Restricciones y Validaciones

### 1. Asignación de Árbitros
- ✅ Solo se puede asignar cuando el partido está en estado `scheduled`
- ✅ Un árbitro no puede estar asignado dos veces al mismo partido
- ✅ Se valida que el usuario sea tipo `referee`
- ✅ Se valida el tipo de árbitro (main, assistant, fourth_official)

### 2. Inicio del Partido
- ❌ **NO se puede iniciar** sin al menos un árbitro asignado
- ✅ Solo se puede iniciar si el estado es `scheduled`
- ✅ Mensaje de error específico si faltan árbitros

### 3. Remoción de Árbitros
- ✅ Solo se puede remover cuando el partido está `scheduled`
- ✅ Una vez iniciado el partido, no se pueden modificar árbitros

## 🔄 Compatibilidad

### Campo Legacy: `referee_id`
El campo `referee_id` en la tabla `fixtures` se mantiene por compatibilidad. El filtrado en el índice de fixtures verifica ambos:
1. La nueva relación `fixture_referees` (muchos-a-muchos)
2. El campo legacy `referee_id` (uno-a-uno)

```php
// En app/Livewire/Fixtures/Index.php
if ($user->user_type === 'referee') {
    $isAssigned = DB::table('fixture_referees')
        ->where('fixture_id', $fixture->id)
        ->where('user_id', $user->id)
        ->exists();
    
    if (!$isAssigned && $fixture->referee_id !== $user->id) {
        return false;
    }
}
```

## 📱 Flujo de Uso

### Para Admin/Encargado de Liga:

1. **Acceder al partido**: `http://flowfast-saas.test/admin/matches/45/live`

2. **Asignar árbitros** (antes de iniciar):
   - Click en botón "+ Asignar" en la sección de árbitros
   - Seleccionar árbitro del dropdown
   - Seleccionar tipo (Principal, Asistente, Cuarto árbitro)
   - Click en "Asignar"
   - Repetir para asignar múltiples árbitros

3. **Iniciar el partido**:
   - Click en "▶️ Iniciar Partido"
   - ✅ Se inicia si hay al menos un árbitro
   - ❌ Error si no hay árbitros asignados

4. **Durante el partido**:
   - Los árbitros asignados se muestran en el sidebar
   - No se pueden modificar una vez iniciado

### Para Árbitros:

1. **Ver "Mis Partidos"**: `http://flowfast-saas.test/fixtures`
   - Solo aparecen los partidos donde están asignados
   - Filtrado automático basado en `fixture_referees`

2. **Acceder a partido asignado**: Click en cualquier partido de su lista

## 🎯 Características Principales

✅ **Múltiples árbitros por partido**: Puedes asignar árbitro principal + asistentes + cuarto árbitro

✅ **Validación de inicio**: No se puede iniciar sin árbitros

✅ **Interfaz intuitiva**: Modal simple para asignar, vista clara de árbitros asignados

✅ **Restricción por estado**: Solo se modifican árbitros antes de iniciar

✅ **Identificadores visuales**: Emojis de colores para diferenciar tipos

✅ **Mensajes claros**: Errores específicos si falta asignar árbitros

✅ **Protección de duplicados**: Unique constraint en base de datos

## 📝 Notas Técnicas

- La migración se ejecutó el 5 de octubre de 2025
- Compatible con sistema legacy de `referee_id`
- Usa Livewire 3.x para interactividad en tiempo real
- Modal con validaciones del lado del servidor
- Transacciones implícitas en Eloquent para integridad de datos

## 🚀 Próximas Mejoras Sugeridas

- [ ] Notificaciones push a árbitros cuando son asignados
- [ ] Historial de asignaciones de árbitros
- [ ] Reportes de desempeño por árbitro
- [ ] Calendario de disponibilidad de árbitros
- [ ] Confirmación de árbitros (aceptar/rechazar asignación)
