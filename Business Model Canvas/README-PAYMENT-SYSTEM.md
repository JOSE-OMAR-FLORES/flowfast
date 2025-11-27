# 💳 Sistema de Pagos Completo - FlowFast SaaS

## 📋 Resumen del Sistema

Se ha implementado un sistema completo de pagos diferenciado por roles con los siguientes métodos:
- **Tarjeta (Stripe)**: Auto-confirmado ✅
- **Efectivo**: Requiere confirmación manual 🔄
- **Transferencia Bancaria**: Requiere confirmación manual 🔄

---

## 👥 Roles y Permisos

### 1. **Entrenadores / Jugadores** (`coach`, `player`)
**Ruta**: `/payments/team`

**Pueden hacer:**
- ✅ Ver pagos de sus equipos (inscripciones, cuotas, etc.)
- ✅ Pagar con tarjeta (confirmación automática)
- ✅ Registrar pago en efectivo (pendiente de confirmación)
- ✅ Registrar transferencia bancaria (pendiente de confirmación)

**No pueden:**
- ❌ Confirmar sus propios pagos en efectivo/transferencia
- ❌ Ver pagos de otros equipos

---

### 2. **Árbitros** (`referee`)
**Ruta**: `/payments/referees`

**Pueden hacer:**
- ✅ Ver pagos que reciben de la liga (gastos/expenses)
- ✅ Confirmar pagos en efectivo que les pagan
- ✅ Confirmar transferencias que les pagan
- ✅ **TAMBIÉN pueden confirmar pagos de equipos** cuando oficien partidos

**No pueden:**
- ❌ Procesar pagos con tarjeta a sí mismos (lo hace el admin)
- ❌ Ver pagos de otros árbitros

---

### 3. **Admin / Liga Manager** (`admin`, `league_manager`)
**Rutas principales**:
- `/admin/financial/income` - Confirmar pagos de equipos
- `/admin/financial/expense` - Procesar pagos a árbitros

**Pueden hacer:**
- ✅ Confirmar pagos en efectivo de equipos
- ✅ Confirmar transferencias de equipos
- ✅ Procesar pagos a árbitros (Stripe, efectivo, transferencia)
- ✅ Ver todos los pagos del sistema

---

## 🔄 Flujos de Pago

### **Flujo 1: Pago de Equipo → Liga (Income)**

#### Opción A: Pago con Tarjeta 💳
```
1. Entrenador → Botón "Pagar Ahora" → Selecciona "Pagar con Tarjeta"
2. Ingresa datos de tarjeta en modal de Stripe
3. Stripe procesa el pago
4. ✅ Estado cambia automáticamente a "confirmed"
5. No requiere acción del admin
```

#### Opción B: Pago en Efectivo 💵
```
1. Entrenador → Botón "Pagar Ahora" → Selecciona "Pagar en Efectivo"
2. Agrega notas opcionales
3. ⏳ Estado cambia a "pending_confirmation"
4. Admin/Liga Manager/Referee → `/admin/financial/income`
5. Confirma el pago en efectivo
6. ✅ Estado cambia a "confirmed"
```

#### Opción C: Transferencia Bancaria 🏦
```
1. Entrenador → Botón "Pagar Ahora" → Selecciona "Pagar por Transferencia"
2. Ingresa: número de referencia, banco, notas
3. ⏳ Estado cambia a "pending_confirmation"
4. Admin/Liga Manager/Referee → `/admin/financial/income`
5. Verifica la transferencia y confirma
6. ✅ Estado cambia a "confirmed"
```

---

### **Flujo 2: Liga → Pago a Árbitro (Expense)**

```
1. Admin/Liga Manager crea gasto para árbitro
2. Aprueba el gasto (status: pending → approved)
3. Marca como "Listo para pagar" (approved → ready_for_payment)
4. En `/admin/financial/expense` aparece botón "Procesar Pago"
5. Selecciona método:
   
   a) Tarjeta 💳:
      - Ingresa tarjeta del árbitro
      - ✅ Se confirma automáticamente
   
   b) Efectivo 💵:
      - Registra el pago
      - Árbitro confirma recepción en su vista
      - ✅ Estado: confirmed
   
   c) Transferencia 🏦:
      - Registra referencia, banco
      - Árbitro confirma recepción en su vista
      - ✅ Estado: confirmed
```

---

## 📂 Estructura de Archivos

### **Componentes Livewire - Pagos de Equipos**
```
app/Livewire/Payments/
├── TeamPayments.php                    # Vista principal entrenadores
├── StripeTeamPayment.php              # Modal pago tarjeta (equipos)
├── CashTeamPayment.php                # Modal pago efectivo (equipos)
├── TransferTeamPayment.php            # Modal transferencia (equipos)
└── ConfirmCashIncome.php              # Confirmar pagos (admin/referee)
```

### **Componentes Livewire - Pagos a Árbitros**
```
app/Livewire/Payments/
├── RefereePayments.php                # Vista principal árbitros
├── StripeExpensePayment.php           # Modal pago tarjeta (árbitros)
├── CashExpensePayment.php             # Modal pago efectivo (árbitros)
└── TransferExpensePayment.php         # Modal transferencia (árbitros)
```

### **Componentes Livewire - Dashboard Financiero**
```
app/Livewire/Financial/
├── Income/Index.php                   # Gestión ingresos (admin)
└── Expense/Index.php                  # Gestión gastos (admin)
```

### **Vistas Blade**
```
resources/views/livewire/payments/
├── team-payments.blade.php
├── stripe-team-payment.blade.php
├── cash-team-payment.blade.php
├── transfer-team-payment.blade.php
├── confirm-cash-income.blade.php
├── referee-payments.blade.php
├── stripe-expense-payment.blade.php
├── cash-expense-payment.blade.php
└── transfer-expense-payment.blade.php
```

---

## 🗄️ Estructura de Base de Datos

### **Tabla: incomes** (Pagos de equipos a liga)
```sql
- payment_status: enum('pending', 'pending_confirmation', 'confirmed', 'cancelled')
- payment_method: enum('card', 'cash', 'transfer')
- payment_reference: varchar (para transferencias)
- bank_name: varchar (para transferencias)
- payment_notes: text (notas adicionales)
- paid_at: timestamp (cuándo se registró el pago)
- paid_by_user: integer (quién registró el pago)
- confirmed_at: timestamp (cuándo se confirmó)
- confirmed_by_user_id: integer (quién confirmó)
- stripe_payment_intent_id: varchar (ID de Stripe)
```

### **Tabla: expenses** (Pagos de liga a árbitros)
```sql
- payment_status: enum('pending', 'approved', 'ready_for_payment', 'confirmed', 'cancelled')
- payment_method: enum('card', 'cash', 'transfer')
- payment_reference: varchar
- bank_name: varchar
- payment_notes: text
- stripe_payment_intent_id: varchar
```

---

## 🎨 Interfaz de Usuario

### **Características:**
- ✅ Botón "Procesar Pago" / "Pagar Ahora" con gradiente
- ✅ Panel desplegable con animaciones (Alpine.js)
- ✅ Modales con diseño moderno y responsivo
- ✅ Badges de estado con colores diferenciados
- ✅ Auto-refresh después de procesar pago (sin recargar)
- ✅ Mensajes flash de éxito/error
- ✅ Integración completa con Stripe Elements

### **Estados visuales:**
- 🟡 **Pendiente**: `pending`
- 🔵 **Esperando confirmación**: `pending_confirmation`
- 🟢 **Confirmado**: `confirmed`
- 🔴 **Vencido**: `overdue`
- ⚫ **Cancelado**: `cancelled`

---

## 🔐 Seguridad y Permisos

### **Validaciones implementadas:**
1. ✅ Los entrenadores solo ven pagos de SUS equipos
2. ✅ Los árbitros solo ven pagos destinados a ELLOS
3. ✅ Solo admin/liga manager pueden confirmar pagos de equipos
4. ✅ Los árbitros pueden confirmar pagos que RECIBEN
5. ✅ Pagos con tarjeta son confirmados automáticamente por Stripe
6. ✅ Todos los pagos quedan registrados con usuario y timestamp

---

## 🚀 Rutas del Sistema

```php
// Para Entrenadores/Jugadores
Route::get('/payments/team/{teamId?}', TeamPayments::class)
    ->name('payments.team');

// Para Árbitros
Route::middleware(['role:admin,league_manager,referee'])
    ->get('/payments/referees', RefereePayments::class)
    ->name('payments.referees');

// Para Admin/Liga Manager - Ingresos
Route::middleware(['role:admin,league_manager'])
    ->get('/admin/financial/income', Income\Index::class)
    ->name('financial.income');

// Para Admin/Liga Manager - Gastos
Route::middleware(['role:admin,league_manager'])
    ->get('/admin/financial/expense', Expense\Index::class)
    ->name('financial.expense');
```

---

## 📊 Reportes y Métricas

El sistema permite rastrear:
- 💰 Total de pagos por método (tarjeta, efectivo, transferencia)
- 📅 Pagos pendientes vs confirmados
- 👤 Quién procesó/confirmó cada pago
- ⏰ Tiempo promedio de confirmación
- 🏦 Pagos por liga/temporada/equipo

---

## 🛠️ Configuración de Stripe

### **Variables de entorno (.env):**
```env
STRIPE_KEY=pk_test_xxxxxxxxxxxxx
STRIPE_SECRET=sk_test_xxxxxxxxxxxxx
```

### **Archivos de configuración:**
```php
// config/services.php
'stripe' => [
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
],
```

---

## ✅ Checklist de Implementación

- [x] Migración de campos de confirmación en `incomes`
- [x] Componentes de pago para equipos (Stripe, Cash, Transfer)
- [x] Componentes de pago para árbitros (Stripe, Cash, Transfer)
- [x] Vista de entrenadores con panel desplegable
- [x] Vista de árbitros con gestión de pagos recibidos
- [x] Dashboard financiero con botones de confirmación
- [x] Auto-refresh después de pagos
- [x] Validaciones de permisos por rol
- [x] Integración completa con Stripe Elements
- [x] Mensajes flash y notificaciones
- [x] Estados de pago diferenciados

---

## 🎯 Próximos Pasos (Opcional)

1. **Notificaciones por email**:
   - Enviar email al entrenador cuando su pago es confirmado
   - Enviar email al árbitro cuando recibe un pago

2. **Reportes PDF**:
   - Generar recibos de pago en PDF
   - Historial de transacciones descargable

3. **Webhook de Stripe**:
   - Configurar webhook para confirmación automática
   - Manejo de pagos fallidos/rechazados

4. **Dashboard de métricas**:
   - Gráficas de pagos por mes
   - Análisis de morosidad
   - Reporte de ingresos por liga

---

## 📝 Notas Importantes

- ⚠️ Los pagos con **tarjeta** se confirman **automáticamente** por Stripe
- ⚠️ Los pagos en **efectivo** y **transferencia** requieren **confirmación manual**
- ⚠️ Los **árbitros** pueden confirmar pagos de **equipos** cuando oficien partidos
- ⚠️ Solo **admin/liga manager** pueden procesar pagos a árbitros

---

## 🐛 Troubleshooting

### **Problema: No aparecen los métodos de pago**
- Verificar que el usuario tiene el rol correcto
- Verificar que el payment_status es 'pending' o 'ready_for_payment'

### **Problema: Stripe no carga**
- Verificar variables de entorno (STRIPE_KEY, STRIPE_SECRET)
- Verificar que se carga el script: `<script src="https://js.stripe.com/v3/"></script>`

### **Problema: No se actualiza después de pagar**
- Verificar que el evento 'payment-successful' se dispara correctamente
- Verificar que el componente padre tiene el listener configurado

---

**✨ Sistema completamente funcional y listo para producción! ✨**
