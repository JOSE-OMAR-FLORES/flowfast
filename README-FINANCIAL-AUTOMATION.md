# 🤖 Sistema de Automatización Financiera

## 📋 Descripción General

Este documento describe el sistema de automatización financiera implementado en FlowFast SaaS, incluyendo jobs, observers, y comandos artisan para la gestión automática de ingresos y gastos.

---

## 🔄 Jobs Implementados

### 1. **GenerateMatchFeesJob**

**Propósito**: Genera automáticamente las cuotas por partido para ambos equipos después de que un partido finalice.

**Disparadores**:
- Automático: Cuando un partido cambia a estado `finished` (vía FixtureObserver)
- Manual: Comando `php artisan financial:generate-match-fees`

**Funcionamiento**:
- Verifica que no exista ya una cuota generada para ese partido
- Lee la configuración de `match_fee` de la liga (default: $50.00)
- Crea 2 ingresos (uno por equipo: local y visitante)
- Estado inicial: `pending`
- Fecha de vencimiento: 3 días después del partido
- Nota: "Generado automáticamente después del partido"

**Ejemplo de Datos Generados**:
```php
Income::create([
    'league_id' => 1,
    'season_id' => 5,
    'team_id' => 10,
    'match_id' => 234,
    'income_type' => 'match_fee',
    'amount' => 50.00,
    'description' => 'Cuota por partido - Local - Tigres vs Leones',
    'due_date' => '2025-10-05',
    'payment_status' => 'pending',
    'created_by' => 1,
    'notes' => 'Generado automáticamente después del partido'
]);
```

---

### 2. **GenerateRefereePaymentsJob**

**Propósito**: Genera automáticamente los pagos para árbitros después de que un partido finalice.

**Disparadores**:
- Automático: Cuando un partido con árbitro asignado cambia a estado `finished`
- Manual: Puede implementarse un comando similar

**Funcionamiento**:
- Verifica que el partido tenga un árbitro asignado (`referee_id`)
- Verifica que no exista ya un pago generado
- Lee la configuración de `referee_payment` de la liga (default: $30.00)
- Crea 1 gasto a favor del árbitro
- Estado inicial: `pending` (requiere aprobación)
- Fecha de pago programada: 7 días después del partido

**Ejemplo de Datos Generados**:
```php
Expense::create([
    'league_id' => 1,
    'season_id' => 5,
    'match_id' => 234,
    'beneficiary_id' => 15, // ID del árbitro
    'expense_type' => 'referee_payment',
    'amount' => 30.00,
    'description' => 'Pago a árbitro Juan Pérez - Tigres vs Leones',
    'due_date' => '2025-10-12',
    'approval_status' => 'pending',
    'payment_status' => 'pending',
    'created_by' => 1,
    'notes' => 'Generado automáticamente después del partido'
]);
```

---

### 3. **MarkOverdueIncomesJob**

**Propósito**: Marca automáticamente como vencidos los ingresos que ya pasaron su fecha de vencimiento.

**Disparadores**:
- Automático: Programado diariamente a las 00:00 (medianoche)
- Manual: Comando `php artisan financial:mark-overdue-incomes`

**Funcionamiento**:
- Busca todos los ingresos con estado `pending` o `paid_by_team`
- Que tengan una `due_date` menor a la fecha actual
- Llama al método `markAsOverdue()` del modelo Income
- Registra en logs cada ingreso marcado
- Retorna el conteo total de ingresos procesados

**Estados Afectados**:
- `pending` → `overdue`
- `paid_by_team` → `overdue`

---

## 👀 Observer Implementado

### **FixtureObserver**

**Propósito**: Observar cambios en los partidos y disparar jobs automáticamente.

**Evento Observado**: `updated`

**Lógica**:
```php
public function updated(Fixture $fixture): void
{
    // Detectar si el partido cambió a estado "finished"
    if ($fixture->isDirty('status') && $fixture->status === 'finished') {
        // Disparar jobs con delay de 5 minutos
        GenerateMatchFeesJob::dispatch($fixture)->delay(now()->addMinutes(5));
        
        if ($fixture->referee_id) {
            GenerateRefereePaymentsJob::dispatch($fixture)->delay(now()->addMinutes(5));
        }
    }
}
```

**Registro**: En `AppServiceProvider::boot()`
```php
Fixture::observe(FixtureObserver::class);
```

---

## 🎯 Comandos Artisan

### 1. **financial:generate-match-fees**

**Descripción**: Genera cuotas de partidos finalizados manualmente.

**Uso**:
```bash
# Generar cuotas de todos los partidos finalizados de los últimos 7 días
php artisan financial:generate-match-fees

# Generar cuota de un partido específico
php artisan financial:generate-match-fees --fixture_id=123

# Generar cuotas de una fecha específica
php artisan financial:generate-match-fees --date=2025-10-01
```

**Opciones**:
- `--fixture_id=ID`: Procesar solo un partido específico
- `--date=YYYY-MM-DD`: Procesar solo partidos de una fecha específica

**Salida**:
```
🏆 Generating match fees...
 4/4 [============================] 100%

✅ Successfully dispatched 4 match fee generation jobs.
```

---

### 2. **financial:mark-overdue-incomes**

**Descripción**: Marca ingresos vencidos manualmente.

**Uso**:
```bash
php artisan financial:mark-overdue-incomes
```

**Salida**:
```
⏰ Marking overdue incomes...
✅ Overdue incomes marked successfully.
```

---

## ⚙️ Configuración de Tareas Programadas

En `routes/console.php`:

```php
use Illuminate\Support\Facades\Schedule;
use App\Jobs\MarkOverdueIncomesJob;

Schedule::job(new MarkOverdueIncomesJob())
    ->daily()
    ->at('00:00')
    ->name('mark-overdue-incomes')
    ->withoutOverlapping();
```

**Para activar el scheduler en producción**:

Agregar al crontab:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

**Para testing local**:
```bash
php artisan schedule:work
```

---

## 🗄️ Configuración de Ligas

La tabla `leagues` ahora incluye campos de configuración financiera:

| Campo | Tipo | Descripción | Default |
|-------|------|-------------|---------|
| `match_fee` | decimal(10,2) | Cuota por partido por equipo | 50.00 |
| `referee_payment` | decimal(10,2) | Pago estándar a árbitros | 30.00 |
| `registration_fee` | decimal(10,2) | Cuota de inscripción por equipo | NULL |

**Configurar en el CRUD de Ligas** o directamente en base de datos:

```sql
UPDATE leagues 
SET match_fee = 75.00, 
    referee_payment = 40.00, 
    registration_fee = 200.00
WHERE id = 1;
```

---

## 🔍 Flujo Completo de Automatización

### **Escenario: Partido Finalizado**

```
1. Admin marca partido como "finished" en la UI
   ↓
2. FixtureObserver detecta el cambio
   ↓
3. Se disparan 2 jobs con delay de 5 minutos:
   - GenerateMatchFeesJob
   - GenerateRefereePaymentsJob (si hay árbitro)
   ↓
4. GenerateMatchFeesJob crea 2 ingresos:
   - Ingreso para equipo local (pending)
   - Ingreso para equipo visitante (pending)
   ↓
5. GenerateRefereePaymentsJob crea 1 gasto:
   - Gasto a favor del árbitro (pending)
   ↓
6. Cada día a las 00:00, MarkOverdueIncomesJob:
   - Revisa ingresos con due_date < hoy
   - Marca como "overdue" los que no estén confirmados
   ↓
7. Dashboard muestra alertas de vencidos
```

---

## 📊 Logs y Monitoreo

Todos los jobs registran eventos en `storage/logs/laravel.log`:

```
[2025-10-02 16:45:00] INFO: Match fees generated successfully for fixture 234
[2025-10-02 16:45:05] INFO: Referee payment generated successfully for fixture 234
[2025-10-03 00:00:00] INFO: Marked 12 incomes as overdue
```

**Monitorear logs**:
```bash
tail -f storage/logs/laravel.log | grep -i financial
```

---

## 🧪 Testing Manual

### **1. Probar GenerateMatchFeesJob**

```bash
# En tinker
php artisan tinker

$fixture = \App\Models\Fixture::find(1);
\App\Jobs\GenerateMatchFeesJob::dispatch($fixture);

# Verificar ingresos generados
\App\Models\Income::where('match_id', 1)->get();
```

### **2. Probar GenerateRefereePaymentsJob**

```bash
php artisan tinker

$fixture = \App\Models\Fixture::find(1);
\App\Jobs\GenerateRefereePaymentsJob::dispatch($fixture);

# Verificar gastos generados
\App\Models\Expense::where('match_id', 1)->get();
```

### **3. Probar MarkOverdueIncomesJob**

```bash
# Crear un ingreso con fecha vencida
php artisan tinker

\App\Models\Income::create([
    'league_id' => 1,
    'income_type' => 'match_fee',
    'amount' => 50,
    'description' => 'Test',
    'due_date' => now()->subDays(1),
    'payment_status' => 'pending',
    'created_by' => 1
]);

# Ejecutar comando
php artisan financial:mark-overdue-incomes

# Verificar cambio
\App\Models\Income::where('payment_status', 'overdue')->get();
```

---

## ⚠️ Consideraciones Importantes

1. **Duplicación**: Los jobs verifican si ya existe un registro antes de crear uno nuevo para evitar duplicados.

2. **Delay**: Los jobs se ejecutan con 5 minutos de delay para dar tiempo a que se complete el proceso de finalización del partido.

3. **Queue**: Asegúrate de tener un worker de queue corriendo en producción:
   ```bash
   php artisan queue:work --daemon
   ```

4. **Fallback**: Si un job falla, se registra en logs pero no detiene el proceso.

5. **Configuración**: Las ligas sin configuración de `match_fee` o `referee_payment` usarán valores por defecto ($50 y $30 respectivamente).

---

## 🚀 Próximas Mejoras

- [ ] Notificaciones por email cuando se generan ingresos/gastos
- [ ] Dashboard de monitoreo de jobs
- [ ] Configuración de delays personalizables
- [ ] Generación masiva de cuotas de inscripción al inicio de temporada
- [ ] Recordatorios automáticos de pagos próximos a vencer
- [ ] Integración con webhooks de pasarelas de pago

---

## 📝 Changelog

- **2025-10-02**: Implementación inicial de sistema de automatización
  - GenerateMatchFeesJob
  - GenerateRefereePaymentsJob
  - MarkOverdueIncomesJob
  - FixtureObserver
  - Comandos Artisan
  - Migración de configuración financiera en leagues
