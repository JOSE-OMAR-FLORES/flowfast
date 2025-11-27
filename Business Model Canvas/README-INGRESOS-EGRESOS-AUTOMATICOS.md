# Sistema de Ingresos/Egresos Automáticos al Finalizar Partidos

## 📋 Resumen de Implementación

Se ha implementado la generación automática de **ingresos** (cobros a equipos) y **egresos** (pagos a árbitros) cuando un partido es finalizado.

## ✅ Cambios Realizados

### 1. **Mostrar Nombres de Árbitros** ✨

#### Problema Original:
- Solo se mostraba "Principal" en lugar del nombre del árbitro

#### Solución:
**Archivo:** `app/Livewire/Matches/Live.php`
```php
public function mount($matchId)
{
    $this->match = Fixture::with([
        'homeTeam',
        'awayTeam',
        'season.league',
        'referees.userable', // ✅ Cargar también el modelo Referee
    ])->findOrFail($matchId);
}
```

**Archivo:** `resources/views/livewire/matches/live.blade.php`
```blade
<div class="font-medium text-gray-900 text-sm">
    {{ $referee->userable->first_name ?? '' }} 
    {{ $referee->userable->last_name ?? '' }}
</div>
<div class="text-xs text-gray-600">
    @if($referee->pivot->referee_type === 'main')
        🟢 Principal
    @elseif($referee->pivot->referee_type === 'assistant')
        🔵 Asistente
    @else
        🟡 Cuarto árbitro
    @endif
</div>
```

**Resultado:**
```
✅ Antes: "Principal"
✅ Ahora: "Juan Pérez"
           🟢 Principal
```

---

### 2. **Generación Automática de Ingresos y Egresos** 💰

#### Flujo Implementado:

```mermaid
graph TD
    A[Admin/League Manager finaliza partido] --> B[finishMatch()]
    B --> C[Cambiar status a 'completed']
    B --> D[generateTeamCharges]
    B --> E[generateRefereePayments]
    D --> F[Crear Income para equipo local]
    D --> G[Crear Income para equipo visitante]
    E --> H[Crear Expense para cada árbitro]
```

#### Archivo: `app/Livewire/Matches/Live.php`

##### **Método Principal: `finishMatch()`**
```php
public function finishMatch()
{
    if (!$this->match->canFinish()) {
        session()->flash('error', 'El partido no puede ser finalizado.');
        return;
    }

    try {
        DB::beginTransaction();

        // 1. Finalizar el partido
        $this->match->finishMatch();

        // 2. Generar ingresos para los equipos (cobros por partido)
        $this->generateTeamCharges();

        // 3. Generar egresos para los árbitros (pagos por arbitraje)
        $this->generateRefereePayments();

        DB::commit();
        
        session()->flash('success', '¡Partido finalizado! Se generaron los cobros a equipos y pagos a árbitros.');
    } catch (\Exception $e) {
        DB::rollBack();
        session()->flash('error', 'Error al finalizar partido: ' . $e->getMessage());
    }
}
```

##### **Método: `generateTeamCharges()`** - Cobros a Equipos
```php
protected function generateTeamCharges()
{
    $league = $this->match->season->league;
    $matchFee = $league->match_fee_per_team ?? $league->match_fee ?? 0;

    if ($matchFee > 0) {
        // Cobro al equipo local
        \App\Models\Income::create([
            'league_id' => $league->id,
            'season_id' => $this->match->season_id,
            'fixture_id' => $this->match->id,
            'team_id' => $this->match->home_team_id,
            'income_type' => 'match_fee',
            'amount' => $matchFee,
            'description' => 'Pago por partido: ' . $this->match->homeTeam->name . ' vs ' . $this->match->awayTeam->name,
            'due_date' => now()->addDays(7), // 7 días para pagar
            'payment_status' => 'pending',
            'generated_by' => auth()->id(),
        ]);

        // Cobro al equipo visitante
        \App\Models\Income::create([
            'league_id' => $league->id,
            'season_id' => $this->match->season_id,
            'fixture_id' => $this->match->id,
            'team_id' => $this->match->away_team_id,
            'income_type' => 'match_fee',
            'amount' => $matchFee,
            'description' => 'Pago por partido: ' . $this->match->homeTeam->name . ' vs ' . $this->match->awayTeam->name,
            'due_date' => now()->addDays(7),
            'payment_status' => 'pending',
            'generated_by' => auth()->id(),
        ]);
    }
}
```

##### **Método: `generateRefereePayments()`** - Pagos a Árbitros
```php
protected function generateRefereePayments()
{
    $league = $this->match->season->league;
    $refereePayment = $league->referee_payment ?? 0;

    if ($refereePayment > 0) {
        // Generar pago para cada árbitro asignado
        foreach ($this->match->referees as $referee) {
            // Calcular monto según el tipo de árbitro
            $amount = match($referee->pivot->referee_type) {
                'main' => $refereePayment,              // 100%
                'assistant' => $refereePayment * 0.7,   // 70%
                'fourth_official' => $refereePayment * 0.5, // 50%
                default => $refereePayment,
            };

            \App\Models\Expense::create([
                'league_id' => $league->id,
                'season_id' => $this->match->season_id,
                'fixture_id' => $this->match->id,
                'referee_id' => $referee->userable_id, // ID del modelo Referee
                'beneficiary_user_id' => $referee->id, // ID del User
                'expense_type' => 'referee_payment',
                'amount' => $amount,
                'description' => 'Pago por arbitraje (' . match($referee->pivot->referee_type) {
                    'main' => 'Principal',
                    'assistant' => 'Asistente',
                    'fourth_official' => 'Cuarto Árbitro',
                    default => 'Árbitro',
                } . '): ' . $this->match->homeTeam->name . ' vs ' . $this->match->awayTeam->name,
                'due_date' => now()->addDays(3), // 3 días para pagar
                'payment_status' => 'pending',
                'requested_by' => auth()->id(),
            ]);
        }
    }
}
```

---

## 💰 Configuración de Montos

### Tabla `leagues` - Configuración Financiera

```sql
-- Monto a cobrar a cada equipo por partido
match_fee_per_team DECIMAL(10,2) DEFAULT 0.00

-- Monto a pagar al árbitro principal por partido
referee_payment DECIMAL(10,2) DEFAULT 0.00
```

### Ejemplo de Configuración:
```php
// En la liga
match_fee_per_team = $500.00  // Cada equipo paga $500
referee_payment = $300.00     // Árbitro principal recibe $300
```

### Distribución de Pagos a Árbitros:
| Tipo | Porcentaje | Ejemplo (si base = $300) |
|------|-----------|-------------------------|
| **Principal** | 100% | $300.00 |
| **Asistente** | 70% | $210.00 |
| **Cuarto Árbitro** | 50% | $150.00 |

---

## 📊 Datos Generados al Finalizar Partido

### **Ingresos Creados** (Tabla `incomes`)

Para el partido: **Equipo A vs Equipo B**

```sql
-- Ingreso 1: Equipo Local
INSERT INTO incomes (
    league_id, season_id, fixture_id, team_id,
    income_type, amount, description,
    due_date, payment_status, generated_by
) VALUES (
    1, 1, 45, 18,
    'match_fee', 500.00,
    'Pago por partido: Equipo A vs Equipo B',
    '2025-10-12', 'pending', 7
);

-- Ingreso 2: Equipo Visitante
INSERT INTO incomes (
    league_id, season_id, fixture_id, team_id,
    income_type, amount, description,
    due_date, payment_status, generated_by
) VALUES (
    1, 1, 45, 16,
    'match_fee', 500.00,
    'Pago por partido: Equipo A vs Equipo B',
    '2025-10-12', 'pending', 7
);
```

### **Egresos Creados** (Tabla `expenses`)

Si hay 3 árbitros asignados:

```sql
-- Egreso 1: Árbitro Principal
INSERT INTO expenses (
    league_id, season_id, fixture_id, referee_id,
    beneficiary_user_id, expense_type, amount, description,
    due_date, payment_status, requested_by
) VALUES (
    1, 1, 45, 3, 7,
    'referee_payment', 300.00,
    'Pago por arbitraje (Principal): Equipo A vs Equipo B',
    '2025-10-08', 'pending', 7
);

-- Egreso 2: Árbitro Asistente
INSERT INTO expenses (
    league_id, season_id, fixture_id, referee_id,
    beneficiary_user_id, expense_type, amount, description,
    due_date, payment_status, requested_by
) VALUES (
    1, 1, 45, 4, 8,
    'referee_payment', 210.00,
    'Pago por arbitraje (Asistente): Equipo A vs Equipo B',
    '2025-10-08', 'pending', 7
);

-- Egreso 3: Cuarto Árbitro
INSERT INTO expenses (
    league_id, season_id, fixture_id, referee_id,
    beneficiary_user_id, expense_type, amount, description,
    due_date, payment_status, requested_by
) VALUES (
    1, 1, 45, 5, 9,
    'referee_payment', 150.00,
    'Pago por arbitraje (Cuarto Árbitro): Equipo A vs Equipo B',
    '2025-10-08', 'pending', 7
);
```

---

## 🎯 Resultado Final

### **Al finalizar un partido se genera:**

✅ **2 Ingresos (Incomes):**
- 1 cobro al equipo local
- 1 cobro al equipo visitante
- Estado: `pending`
- Vencimiento: 7 días

✅ **N Egresos (Expenses):**
- 1 pago por cada árbitro asignado
- Montos variables según tipo de árbitro
- Estado: `pending`
- Vencimiento: 3 días

✅ **Mensaje de Confirmación:**
```
¡Partido finalizado! 
Se generaron los cobros a equipos y pagos a árbitros.
```

---

## 🔄 Flujo Completo

```
1. Admin va a: /admin/matches/45/live

2. Admin asigna árbitros:
   - Juan Pérez (Principal)
   - María López (Asistente)
   - Carlos García (Cuarto Árbitro)

3. Admin inicia partido:
   ✅ Verifica que hay al menos 1 árbitro

4. Partido se juega...

5. Admin finaliza partido:
   ✅ Status cambia a 'completed'
   ✅ Se generan 2 ingresos (equipos)
   ✅ Se generan 3 egresos (árbitros)

6. En módulo financiero:
   - Ingresos pendientes: $1,000 ($500 × 2 equipos)
   - Egresos pendientes: $660 ($300 + $210 + $150)
```

---

## ⚙️ Configuración Requerida

### **En la Liga:**

Para que se generen automáticamente, la liga debe tener configurados:

1. **`match_fee_per_team`**: Monto a cobrar por partido a cada equipo
2. **`referee_payment`**: Monto base a pagar a árbitros

**Ejemplo:**
```php
// Editar liga y configurar:
$league->match_fee_per_team = 500.00;
$league->referee_payment = 300.00;
$league->save();
```

### **Si los montos son $0:**
- ⚠️ No se generan ingresos ni egresos automáticamente
- El partido se finaliza normalmente

---

## 🛡️ Validaciones y Seguridad

✅ **Transacciones:** Usa `DB::transaction()` - si algo falla, se revierte todo

✅ **Validación de Estado:** Solo se puede finalizar si `status === 'in_progress'`

✅ **Try/Catch:** Captura errores y muestra mensaje específico

✅ **Usuario Registrado:** Guarda quién generó los registros (`generated_by`, `requested_by`)

---

## 📄 Archivos Modificados

1. ✅ `app/Livewire/Matches/Live.php`
   - Método `finishMatch()` con generación automática
   - Método `generateTeamCharges()`
   - Método `generateRefereePayments()`
   - Import `Illuminate\Support\Facades\DB`

2. ✅ `resources/views/livewire/matches/live.blade.php`
   - Mostrar `first_name + last_name` en lugar de `name`

---

## 🧪 Testing

### **Probar Generación Automática:**

1. **Configurar liga:**
   ```sql
   UPDATE leagues 
   SET match_fee_per_team = 500.00,
       referee_payment = 300.00
   WHERE id = 1;
   ```

2. **Asignar árbitros al partido**

3. **Iniciar partido**

4. **Finalizar partido**

5. **Verificar registros:**
   ```sql
   -- Ver ingresos generados
   SELECT * FROM incomes 
   WHERE fixture_id = 45;

   -- Ver egresos generados
   SELECT * FROM expenses 
   WHERE fixture_id = 45;
   ```

**Resultado Esperado:**
- 2 registros en `incomes` (equipos)
- N registros en `expenses` (árbitros asignados)

---

## 📚 Referencias

- [README-ASIGNACION-ARBITROS.md](README-ASIGNACION-ARBITROS.md) - Sistema de asignación de árbitros
- Sistema financiero: `/admin/financial/*`
