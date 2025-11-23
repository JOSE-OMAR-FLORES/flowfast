# 💰 Generación Automática de Pagos de Inscripción

## ✅ Implementación Completada

### 📋 Resumen

Ahora cuando se crea un equipo, **automáticamente se genera un pago de inscripción pendiente** si:
1. El equipo NO está marcado como "Registro pagado"
2. La liga tiene configurada una cuota de inscripción mayor a $0

---

## 🔧 Cambios Realizados

### 1. **Componente Livewire: Teams\Create.php**

**Ubicación:** `app/Livewire/Teams/Create.php`

**Nuevo método:** `generateRegistrationFee()`

```php
protected function generateRegistrationFee(Team $team)
{
    try {
        $season = Season::find($team->season_id);
        $league = $season ? League::find($season->league_id) : null;
        
        if (!$league || !$season) {
            return;
        }

        $registrationFee = $league->registration_fee ?? 0;

        // Solo crear si hay un monto configurado y el equipo no está marcado como pagado
        if ($registrationFee > 0 && !$this->registration_paid) {
            Income::create([
                'league_id' => $league->id,
                'season_id' => $season->id,
                'team_id' => $team->id,
                'income_type' => 'registration_fee',
                'amount' => $registrationFee,
                'description' => 'Cuota de inscripción - ' . $season->name,
                'due_date' => now()->addDays(15),
                'payment_status' => 'pending',
                'generated_by' => auth()->id(),
            ]);

            Log::info("Pago de inscripción generado para equipo {$team->name}: \${$registrationFee}");
        }
    } catch (\Exception $e) {
        Log::error("Error al generar pago de inscripción: " . $e->getMessage());
    }
}
```

**Se ejecuta automáticamente en:** Método `save()` después de crear el equipo.

---

### 2. **Vista: teams/create.blade.php**

**Ubicación:** `resources/views/livewire/teams/create.blade.php`

**Cambios:**
- Se agregó información al campo "Registro pagado"
- Se añadió aviso en el panel de información

```blade
<p class="mt-1 text-xs text-gray-500">
    💡 Si no marcas esto, se creará automáticamente un pago pendiente de inscripción
</p>

<!-- ... -->

<p class="text-sm text-blue-700 font-medium">
    💰 Se generará automáticamente un pago de inscripción según la cuota configurada en la liga.
</p>
```

---

## 🎯 Cómo Funciona

### Flujo al crear un equipo:

1. Usuario llena el formulario de creación de equipo
2. **NO marca** el checkbox "Registro pagado"
3. Hace clic en "Crear Equipo"
4. El sistema:
   - ✅ Crea el equipo
   - ✅ Verifica la cuota de inscripción de la liga
   - ✅ Si hay cuota configurada > $0, crea automáticamente:
     - Un registro en la tabla `incomes`
     - Tipo: `registration_fee`
     - Estado: `pending`
     - Vencimiento: 15 días
     - Monto: según `leagues.registration_fee`

---

## 📊 Datos Generados

Cada pago de inscripción creado contiene:

| Campo | Valor |
|-------|-------|
| `league_id` | ID de la liga |
| `season_id` | ID de la temporada |
| `team_id` | ID del equipo recién creado |
| `income_type` | `registration_fee` |
| `amount` | Monto de `leagues.registration_fee` |
| `description` | "Cuota de inscripción - [Nombre Temporada]" |
| `due_date` | Hoy + 15 días |
| `payment_status` | `pending` |
| `generated_by` | ID del usuario que creó el equipo |

---

## 🧪 Pruebas

### Script de prueba incluido:
```bash
php test_team_registration_fee.php
```

Este script:
1. Busca una liga con cuota configurada
2. Crea un equipo de prueba
3. Genera el pago de inscripción
4. Verifica que se haya creado correctamente

---

## 🔍 Verificación Manual

### 1. Configurar cuota en la liga:
```
http://flowfast-saas.test/admin/leagues/{id}/edit
```
- Asegúrate de tener un valor > 0 en "Cuota de Inscripción"

### 2. Crear un equipo:
```
http://flowfast-saas.test/admin/teams/create
```
- Selecciona liga y temporada
- Ingresa nombre del equipo
- **NO marques** "Registro pagado"
- Haz clic en "Crear Equipo"

### 3. Verificar el pago creado:
```
http://flowfast-saas.test/admin/incomes
```
- Busca por tipo "Cuota Inscripción"
- Verifica que aparezca el pago del equipo recién creado
- Estado: Pendiente
- Monto: el configurado en la liga

---

## ⚙️ Configuración Requerida

### En la tabla `leagues`:

```sql
-- Configurar cuota de inscripción
UPDATE leagues 
SET registration_fee = 500.00 
WHERE id = 1;
```

O desde el panel de administración al crear/editar una liga.

---

## 🚨 Casos Especiales

### NO se genera pago cuando:

1. ❌ El checkbox "Registro pagado" está marcado
2. ❌ La liga NO tiene cuota de inscripción configurada
3. ❌ La cuota de inscripción es $0.00

### Logs:

Los eventos se registran en `storage/logs/laravel.log`:
- ✅ Éxito: "Pago de inscripción generado para equipo..."
- ❌ Error: "Error al generar pago de inscripción: ..."

---

## 📌 Migración Adicional Creada

### Tabla `match_officials` (requerida para eliminar ligas):

**Archivo:** `database/migrations/2025_11_22_203721_create_match_officials_table.php`

Esta tabla se creó para solucionar el error al eliminar ligas que tienen fixtures con oficiales asignados.

```bash
php artisan migrate  # Ya ejecutada ✅
```

---

## 🎉 Resultado Final

Ahora los equipos se crean con su pago de inscripción automáticamente, ahorrando tiempo y evitando olvidos en el registro manual de pagos.

**Beneficios:**
- ✅ Automático y consistente
- ✅ No requiere intervención manual
- ✅ Registra quién generó el pago
- ✅ Establece fecha de vencimiento automática
- ✅ Estado inicial: pendiente
- ✅ Facilita el seguimiento financiero
