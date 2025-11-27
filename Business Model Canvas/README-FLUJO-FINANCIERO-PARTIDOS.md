# 💰 FLUJO FINANCIERO AUTOMÁTICO - Partidos y Árbitros

## 📋 Resumen del Flujo

Cuando un partido finaliza, el sistema genera **automáticamente**:
1. ✅ **2 INGRESOS** → Cuotas de partido (uno por cada equipo)
2. ✅ **1 EGRESO** → Pago al árbitro (si fue asignado)

---

## 🔄 Flujo Completo Paso a Paso

### **ANTES DEL PARTIDO** 📝

#### 1. Asignar Árbitro (Opcional pero recomendado)
**¿Quién puede asignarlo?**
- ✅ Admin
- ✅ League Manager

**¿Cuándo?**
- ✅ **ANTES de iniciar el partido** (estado: `scheduled`)
- ✅ También se puede asignar cuando el partido está `in_progress`

**¿Cómo?**
```
/fixtures/{id}/manage
→ Sección "Asignar Árbitro"
→ Dropdown con lista de árbitros
→ Click "Asignar Árbitro"
```

**Resultado**:
```php
fixture->referee_id = [ID del árbitro]
```

---

### **DURANTE EL PARTIDO** ⚽

#### 2. Iniciar Partido
**¿Quién puede iniciarlo?**
- ✅ Admin
- ✅ League Manager
- ✅ **Árbitro asignado** (si fue asignado previamente)

**¿Cuándo?**
- Solo si el partido está en estado `scheduled`

**¿Cómo?**
```
/fixtures/{id}/manage
→ Click "Iniciar Partido"
```

**Resultado**:
```php
fixture->status = 'in_progress'
```

#### 3. Actualizar Marcador (Durante el partido)
**¿Quién puede actualizarlo?**
- ✅ Admin
- ✅ League Manager
- ✅ **Árbitro asignado**

**¿Cómo?**
```
/fixtures/{id}/manage
→ Inputs de marcador (Home Score / Away Score)
→ Cambiar valores en tiempo real
→ Click "Actualizar Marcador"
```

**Resultado**:
```php
fixture->home_score = [goles local]
fixture->away_score = [goles visitante]
```

---

### **AL FINALIZAR EL PARTIDO** 🏁

#### 4. Finalizar Partido
**¿Quién puede finalizarlo?**
- ✅ Admin
- ✅ League Manager
- ✅ **Árbitro asignado**

**¿Qué hace el sistema?**
```php
// 1. Cambiar estado del partido
fixture->status = 'completed'

// 2. Disparar Job para generar cuotas (2 ingresos)
GenerateMatchFeesJob::dispatch($fixture)->delay(now()->addMinutes(5))

// 3. Disparar Job para pagar árbitro (1 egreso) - SOLO SI HAY ÁRBITRO
if ($fixture->referee_id) {
    GenerateRefereePaymentsJob::dispatch($fixture)->delay(now()->addMinutes(5))
}

// 4. Actualizar Standings (inmediato)
StandingsService->updateStandingsForFixture($fixture)
```

---

## 💵 Generación Automática de Transacciones

### **1. Ingresos - Cuotas de Partido** (2 transacciones)

**Job**: `GenerateMatchFeesJob`

**¿Cuándo se ejecuta?**
- 5 minutos después de finalizar el partido

**¿Qué crea?**
```php
// Ingreso 1: Equipo Local
Income::create([
    'league_id' => $league->id,
    'season_id' => $season->id,
    'match_id' => $fixture->id,
    'payer_id' => $homeTeam->id,
    'income_type' => 'match_fee',
    'amount' => $league->match_fee,
    'description' => "Cuota de partido - {$homeTeam->name}",
    'due_date' => Carbon::now()->addDays(7),
    'payment_status' => 'pending'
])

// Ingreso 2: Equipo Visitante
Income::create([
    'league_id' => $league->id,
    'season_id' => $season->id,
    'match_id' => $fixture->id,
    'payer_id' => $awayTeam->id,
    'income_type' => 'match_fee',
    'amount' => $league->match_fee,
    'description' => "Cuota de partido - {$awayTeam->name}",
    'due_date' => Carbon::now()->addDays(7),
    'payment_status' => 'pending'
])
```

**Valor**: Se toma de `leagues.match_fee` (configurable por liga)

---

### **2. Egreso - Pago al Árbitro** (1 transacción)

**Job**: `GenerateRefereePaymentsJob`

**¿Cuándo se ejecuta?**
- 5 minutos después de finalizar el partido
- **SOLO SI** el partido tiene `referee_id` asignado

**¿Qué crea?**
```php
Expense::create([
    'league_id' => $league->id,
    'season_id' => $season->id,
    'match_id' => $fixture->id,
    'beneficiary_id' => $referee->id,
    'expense_type' => 'referee_payment',
    'amount' => $league->referee_payment,
    'description' => "Pago a árbitro {$referee->name} - {$homeTeam} vs {$awayTeam}",
    'due_date' => Carbon::now()->addDays(7),
    'approval_status' => 'pending',
    'payment_status' => 'pending',
    'created_by' => 1, // Sistema
    'notes' => 'Generado automáticamente después del partido'
])
```

**Valor**: Se toma de `leagues.referee_payment` (configurable por liga)

**Validaciones**:
1. ✅ Verifica que exista `referee_id`
2. ✅ Verifica que no exista pago duplicado
3. ✅ Verifica que el árbitro exista en la BD

---

## 🎯 Casos de Uso

### Caso 1: Partido SIN Árbitro Asignado
```
1. Manager inicia partido
2. Manager actualiza marcador
3. Manager finaliza partido
   ↓
Resultado:
✅ 2 ingresos (cuotas de equipos)
❌ 0 egresos (no hay árbitro)
✅ Standings actualizados
```

### Caso 2: Partido CON Árbitro Asignado
```
1. Manager asigna árbitro ANTES de iniciar
2. Árbitro inicia partido
3. Árbitro actualiza marcador
4. Árbitro finaliza partido
   ↓
Resultado:
✅ 2 ingresos (cuotas de equipos)
✅ 1 egreso (pago al árbitro)
✅ Standings actualizados
```

### Caso 3: Asignar Árbitro Durante el Partido
```
1. Manager inicia partido (sin árbitro)
2. Manager asigna árbitro durante el partido
3. Árbitro actualiza marcador
4. Árbitro finaliza partido
   ↓
Resultado:
✅ 2 ingresos (cuotas de equipos)
✅ 1 egreso (pago al árbitro)
✅ Standings actualizados
```

---

## ⚙️ Configuración de Montos

### ¿Dónde se configuran los montos?

**En la tabla `leagues`**:

```sql
match_fee          DECIMAL(10,2)  -- Cuota por partido (por equipo)
referee_payment    DECIMAL(10,2)  -- Pago al árbitro por partido
```

### ¿Cómo modificar los montos?

**Opción 1: Desde código (al crear liga)**
```php
League::create([
    'name' => 'Liga Premier',
    'match_fee' => 50.00,          // $50 por equipo
    'referee_payment' => 30.00,    // $30 al árbitro
])
```

**Opción 2: Desde el CRUD de Ligas**
```
/leagues/{id}/edit
→ Sección "Configuración Financiera"
→ Match Fee: [50.00]
→ Referee Payment: [30.00]
→ Guardar
```

**Opción 3: Directamente en BD**
```sql
UPDATE leagues 
SET match_fee = 50.00, 
    referee_payment = 30.00 
WHERE id = 1;
```

---

## 🔍 Verificación del Flujo

### ¿Cómo verificar que funcionó?

#### 1. Verificar Ingresos
```
/financial/income
→ Filtrar por liga/temporada
→ Buscar tipo "match_fee"
→ Debe haber 2 ingresos del partido
```

#### 2. Verificar Egreso (si había árbitro)
```
/financial/expense
→ Filtrar por liga/temporada
→ Buscar tipo "referee_payment"
→ Debe haber 1 egreso del árbitro
```

#### 3. Verificar Standings
```
/standings
→ Seleccionar liga y temporada
→ Ver tabla actualizada con el resultado del partido
```

#### 4. Ver Dashboard Financiero
```
/financial/dashboard/{leagueId}
→ Ver métricas actualizadas
→ Ingresos Totales +2
→ Egresos Totales +1 (si había árbitro)
```

---

## ⏱️ Timeline

```
Tiempo 0:00 → Usuario finaliza partido
              ↓
Tiempo 0:00 → fixture->status = 'completed'
              ↓
Tiempo 0:00 → Standings actualizados (INMEDIATO)
              ↓
Tiempo 5:00 → GenerateMatchFeesJob ejecutado
              → 2 ingresos creados
              ↓
Tiempo 5:00 → GenerateRefereePaymentsJob ejecutado (si hay árbitro)
              → 1 egreso creado
              ↓
Tiempo 5:01 → Sistema completo actualizado ✅
```

---

## 🚨 Manejo de Errores

### Si el árbitro no existe
```php
Log::warning("Referee not found for fixture {$fixture->id}");
// No se crea el egreso
// Los ingresos sí se crean
```

### Si ya existe el pago al árbitro
```php
Log::info("Referee payment already exists for fixture {$fixture->id}");
// No se duplica
```

### Si no hay configuración de montos
```php
$matchFee = $league->match_fee ?? 0.00;      // Default: 0
$refereePayment = $league->referee_payment ?? 30.00;  // Default: 30
```

---

## ✅ Checklist de Verificación

Antes de finalizar un partido, verifica:

- [ ] La liga tiene `match_fee` configurado (o será $0)
- [ ] La liga tiene `referee_payment` configurado (o será $30 default)
- [ ] El árbitro fue asignado (opcional, pero recomendado)
- [ ] Los equipos existen y están activos
- [ ] El marcador está actualizado
- [ ] La temporada está activa

Después de finalizar un partido, verifica:

- [ ] Estado cambió a `completed`
- [ ] Después de 5 minutos: 2 ingresos creados
- [ ] Después de 5 minutos: 1 egreso creado (si había árbitro)
- [ ] Standings actualizados correctamente
- [ ] Dashboard financiero actualizado

---

## 📖 Archivos Relacionados

```
app/Livewire/Fixtures/Manage.php           → Gestión del partido
app/Jobs/GenerateMatchFeesJob.php          → Job de ingresos
app/Jobs/GenerateRefereePaymentsJob.php    → Job de egreso
app/Observers/FixtureObserver.php          → Trigger automático
app/Services/StandingsService.php          → Actualización de standings
database/migrations/*financial_config*      → Configuración de montos
```

---

**Última actualización**: 2 de octubre de 2025  
**Estado**: ✅ Funcionando correctamente  
**Configuración**: ✅ Completa y validada
