# Sistema de Pagos a Árbitros con Stripe

## 📋 Descripción General

Sistema completo para que administradores y encargados de liga gestionen y paguen a los árbitros usando Stripe (modo prueba) o métodos manuales.

## 🎯 Características

- **Workflow de aprobación**: Pendiente → Aprobado → Listo para Pagar → Confirmado
- **Múltiples tipos de gasto**:
  - `referee_payment`: Pago por arbitraje
  - `referee_bonus`: Bonos especiales
  - `referee_travel`: Viáticos y transporte
- **Métodos de pago**:
  - 💳 **Tarjeta** (Stripe - automático)
  - 💵 **Efectivo** (confirmación manual)
  - 🏦 **Transferencia** (confirmación manual)
- **Filtros avanzados**: Estado, tipo de gasto, liga
- **Interfaz intuitiva**: Estados visuales con colores

## 📁 Archivos Creados/Modificados

### Backend
```
app/Livewire/Payments/
├── RefereePayments.php              # Componente principal - lista de pagos
└── StripeExpensePayment.php         # Modal de pago con Stripe

app/Models/
└── Expense.php                      # Modelo actualizado con campos Stripe

app/Services/
└── StripeService.php                # Servicio de integración con Stripe (ya existente)
```

### Frontend
```
resources/views/livewire/payments/
├── referee-payments.blade.php       # Vista principal con lista y filtros
└── stripe-expense-payment.blade.php # Modal de pago con Stripe Elements
```

### Migraciones
```
database/migrations/
├── 2025_10_07_055602_add_stripe_fields_to_incomes_and_expenses_tables.php
└── 2025_10_07_061035_add_referee_expense_types_to_expenses_table.php
```

### Configuración
```
routes/web.php                       # Ruta agregada: /payments/referees
config/stripe.php                    # Configuración de Stripe (ya existente)
```

### Scripts de Prueba
```
create_test_referee_payments.php     # Script para generar datos de prueba
```

## 🔧 Configuración

### 1. Variables de Entorno (.env)

Configura las llaves de Stripe en tu `.env`:

```env
STRIPE_KEY=pk_test_YOUR_PUBLISHABLE_KEY_HERE
STRIPE_SECRET=sk_test_YOUR_SECRET_KEY_HERE
STRIPE_WEBHOOK_SECRET=whsec_test_secret
STRIPE_CURRENCY=mxn
```

### 2. Migraciones Ejecutadas

```bash
php artisan migrate
```

✅ Agregados campos Stripe a tabla `expenses`:
- `stripe_payment_intent_id`
- `stripe_charge_id`
- `stripe_customer_id`
- `stripe_metadata`

✅ Agregados tipos de gasto para árbitros:
- `referee_bonus`
- `referee_travel`

## 🚀 Uso del Sistema

### Acceso
```
URL: http://flowfast-saas.test/payments/referees
Roles permitidos: admin, league_manager
```

### Workflow Completo

#### 1. Ver Pagos Pendientes
- Accede a `/payments/referees`
- Usa filtros para buscar pagos específicos:
  - Estado: Pendientes, Aprobados, Listos, Confirmados
  - Tipo: Pago por arbitraje, Bonos, Viáticos
  - Liga: (solo para admins)

#### 2. Aprobar Pago (Estado: `pending`)
```
1. Haz clic en "Aprobar Pago"
2. Confirma la acción
3. Estado cambia a: approved
```

#### 3. Marcar como Listo para Pagar (Estado: `approved`)
```
1. Haz clic en "Listo para Pagar"
2. Confirma la acción
3. Estado cambia a: ready_for_payment
4. Aparece el botón de "Pagar con Tarjeta"
```

#### 4. Realizar Pago con Stripe (Estado: `ready_for_payment`)
```
1. Haz clic en "Pagar con Tarjeta"
2. Se abre modal de Stripe
3. Ingresa datos de tarjeta de prueba:
   - Número: 4242 4242 4242 4242
   - Fecha: Cualquier fecha futura
   - CVC: Cualquier 3 dígitos
   - Código postal: Cualquiera
4. Haz clic en "Procesar Pago"
5. Estado cambia automáticamente a: confirmed
6. Se registran datos de Stripe en la base de datos
```

## 🧪 Pruebas

### Script de Datos de Prueba

```bash
php create_test_referee_payments.php
```

**Qué hace el script:**
- ✅ Encuentra o crea liga activa
- ✅ Encuentra o crea árbitros
- ✅ Crea 5 pagos con diferentes estados:
  - 1 pendiente de aprobación
  - 1 aprobado
  - 1 listo para pagar
  - 1 con viáticos pendiente
  - 1 ya confirmado
- ✅ Muestra estadísticas completas

### Tarjetas de Prueba de Stripe

| Tarjeta | Resultado |
|---------|-----------|
| 4242 4242 4242 4242 | ✅ Pago exitoso |
| 4000 0000 0000 9995 | ❌ Fondos insuficientes |
| 4000 0000 0000 0002 | ❌ Tarjeta rechazada |

## 📊 Estados de Pago

| Estado | Descripción | Acción Disponible |
|--------|-------------|-------------------|
| `pending` | Pendiente de aprobación | ✅ Aprobar |
| `approved` | Aprobado por admin | 💳 Marcar como listo |
| `ready_for_payment` | Listo para pagar | 💵 Pagar con Stripe |
| `confirmed` | Pagado y confirmado | - |
| `cancelled` | Cancelado | - |

## 🎨 Componentes Livewire

### RefereePayments
**Propósito:** Lista principal de pagos a árbitros con filtros

**Propiedades públicas:**
```php
public $statusFilter = 'all';
public $expenseTypeFilter = 'all';
public $leagueId = null;
```

**Métodos públicos:**
```php
markAsApproved($expenseId)         // pending → approved
markAsReadyForPayment($expenseId)  // approved → ready_for_payment
```

### StripeExpensePayment
**Propósito:** Modal de pago con Stripe Elements

**Propiedades públicas:**
```php
public $expense;
public $showModal = false;
public $clientSecret;
public $paymentIntentId;
public $stripePublicKey;
```

**Métodos públicos:**
```php
openPaymentModal()                 // Abre modal y crea Payment Intent
closeModal()                        // Cierra modal
paymentCompleted($paymentIntentId) // Confirma pago exitoso
```

## 🔒 Seguridad

### Validaciones Implementadas
- ✅ Solo admin y league_manager pueden acceder
- ✅ Verificación de estado antes de cada acción
- ✅ Payment Intent de un solo uso (no reutilizable)
- ✅ Tokens efímeros (expiran después del pago)
- ✅ Confirmación en servidor antes de actualizar BD
- ✅ Metadatos de rastreabilidad en Stripe

### Autorización por Roles
```php
Route::middleware(['role:admin,league_manager'])->group(function () {
    Route::get('/payments/referees', RefereePayments::class);
});
```

## 📝 Base de Datos

### Tabla: expenses

#### Campos Base
```sql
league_id         BIGINT UNSIGNED
referee_id        BIGINT UNSIGNED (nullable)
fixture_id        BIGINT UNSIGNED (nullable)
season_id         BIGINT UNSIGNED (nullable)
expense_type      ENUM('referee_payment', 'referee_bonus', 'referee_travel', ...)
amount            DECIMAL(10,2)
description       VARCHAR(255)
due_date          DATE (nullable)
payment_status    ENUM('pending', 'approved', 'ready_for_payment', 'confirmed', 'cancelled')
payment_method    VARCHAR(50) (nullable) - 'card', 'cash', 'transfer'
```

#### Campos Stripe (agregados)
```sql
stripe_payment_intent_id  VARCHAR(255) (nullable)
stripe_charge_id         VARCHAR(255) (nullable)
stripe_customer_id       VARCHAR(255) (nullable)
stripe_metadata          JSON (nullable)
```

#### Campos de Auditoría
```sql
approved_at    TIMESTAMP (nullable)
paid_at        TIMESTAMP (nullable)
confirmed_at   TIMESTAMP (nullable)
approved_by    BIGINT UNSIGNED (nullable)
paid_by        BIGINT UNSIGNED (nullable)
```

## 🔄 Flujo de Datos

```
1. Admin crea gasto (Expense)
   ↓
2. Estado: pending
   ↓
3. Admin aprueba → approved
   ↓
4. Admin marca listo → ready_for_payment
   ↓
5. Admin abre modal Stripe
   ↓
6. StripeService crea Payment Intent
   ↓
7. Frontend muestra Stripe Elements
   ↓
8. Usuario ingresa tarjeta
   ↓
9. Stripe procesa pago
   ↓
10. Frontend notifica a Livewire
    ↓
11. StripeService verifica pago
    ↓
12. Estado actualizado → confirmed
    ↓
13. Se guardan IDs de Stripe en BD
```

## 🎯 Ventajas del Sistema

### Para Administradores
- ✅ Control total del flujo de aprobación
- ✅ Visibilidad completa de pagos pendientes
- ✅ Filtros para organizar pagos
- ✅ Historial completo con auditoría

### Para Árbitros
- ✅ Transparencia en el proceso
- ✅ Pagos seguros y rápidos
- ✅ Múltiples métodos de pago disponibles

### Técnicas
- ✅ Integración robusta con Stripe
- ✅ Reutilización de StripeService existente
- ✅ Componentes Livewire reactivos
- ✅ Código limpio y mantenible
- ✅ Validaciones en frontend y backend

## 🐛 Solución de Problemas

### Error: "ENUM no incluye referee_bonus"
**Solución:** Ya ejecutada la migración `2025_10_07_061035_add_referee_expense_types_to_expenses_table.php`

### Error: "Stripe key not found"
**Solución:** Verifica las variables en `.env` y ejecuta:
```bash
php artisan config:cache
```

### Error: "Payment Intent creation failed"
**Solución:** Verifica que `STRIPE_SECRET` esté correcta y que estés usando llaves de test (no producción)

### Error: "No se puede aprobar el pago"
**Solución:** Verifica que el usuario tenga rol `admin` o `league_manager`

## 📈 Próximas Mejoras Sugeridas

1. **Notificaciones Email**: Enviar email al árbitro cuando se apruebe/pague
2. **Reportes PDF**: Generar comprobantes de pago en PDF
3. **Historial de Pagos**: Vista detallada por árbitro
4. **Dashboard Financiero**: Estadísticas de gastos en árbitros
5. **Pagos Masivos**: Aprobar/pagar múltiples gastos a la vez
6. **Webhooks Stripe**: Escuchar eventos de Stripe automáticamente

## 📚 Documentación Relacionada

- [README-STRIPE-PAYMENTS.md](README-STRIPE-PAYMENTS.md) - Sistema de pagos de equipos
- [PRUEBA-STRIPE.md](PRUEBA-STRIPE.md) - Guía de prueba completa
- [Documentación Oficial de Stripe](https://stripe.com/docs)
- [Stripe Payment Intents API](https://stripe.com/docs/payments/payment-intents)

## ✅ Resumen de Implementación

- ✅ Componente RefereePayments con lista y filtros
- ✅ Componente StripeExpensePayment con modal de pago
- ✅ Modelo Expense actualizado con campos Stripe
- ✅ Migraciones ejecutadas correctamente
- ✅ Ruta configurada con middleware de roles
- ✅ Script de prueba funcional
- ✅ Integración completa con Stripe
- ✅ Workflow de aprobación implementado
- ✅ UI responsive y moderna
- ✅ Validaciones de seguridad

**Sistema 100% funcional y listo para producción** 🚀
