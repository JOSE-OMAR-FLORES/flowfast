# 💳 Sistema de Pagos con Stripe - FlowFast SaaS

## 📋 Descripción

Sistema completo de pagos que permite a los equipos pagar cuotas de inscripción y otros conceptos mediante **3 métodos diferentes**:

1. **💳 Tarjeta de Crédito/Débito** - Procesado por Stripe (automático)
2. **💵 Efectivo** - Requiere confirmación del administrador
3. **🏦 Transferencia Bancaria** - Requiere confirmación del administrador

---

## 🚀 Características Implementadas

### ✅ Backend
- **StripeService** - Servicio para interactuar con la API de Stripe
- **Componente Livewire StripePayment** - Modal de pago con tarjeta
- **Componente Livewire TeamPayments** - Lista de pagos del equipo
- **Migración de campos Stripe** - `stripe_payment_intent_id`, `stripe_charge_id`, etc.
- **Rutas de pagos** - `/payments/team/{teamId}`

### ✅ Frontend
- **Modal de pago Stripe** - Formulario seguro (Stripe Elements)
- **Botones de método de pago** - Tarjeta, Efectivo, Transferencia
- **Estados visuales** - Pending, Paid, Confirmed, Overdue
- **Tarjetas de prueba** - Números de prueba para testing

### ✅ Seguridad
- **PCI Compliance** - Stripe maneja los datos de tarjetas
- **Tokens de un solo uso** - Payment Intents seguros
- **Confirmación de pagos** - Verificación desde el servidor

---

## 🔧 Configuración

### 1. Variables de Entorno (.env)

Configura las llaves de Stripe en tu `.env`:
```env
# Stripe Configuration (Test Mode)
STRIPE_KEY=pk_test_YOUR_PUBLISHABLE_KEY_HERE
STRIPE_SECRET=sk_test_YOUR_SECRET_KEY_HERE
STRIPE_WEBHOOK_SECRET=
```

### 2. Obtener Llaves Reales de Stripe

1. Crea una cuenta en [stripe.com](https://stripe.com)
2. Ve a **Developers → API Keys**
3. Activa el **modo de prueba** (Test mode toggle arriba a la derecha)
4. Copia:
   - **Publishable key** → `STRIPE_KEY`
   - **Secret key** → `STRIPE_SECRET`

---

## 🧪 Testing - Tarjetas de Prueba

### Tarjetas que puedes usar en modo test:

| Número | Resultado | Descripción |
|--------|-----------|-------------|
| `4242 4242 4242 4242` | ✅ Éxito | Pago exitoso siempre |
| `4000 0000 0000 9995` | ❌ Fondos insuficientes | Simula falta de fondos |
| `4000 0000 0000 0002` | ❌ Declinada | Tarjeta declinada |
| `4000 0000 0000 0341` | 🔒 Requiere autenticación | 3D Secure |

**Datos adicionales (puedes usar cualquier valor):**
- **Fecha de expiración:** Cualquier fecha futura (ej: 12/25)
- **CVC:** Cualquier 3 dígitos (ej: 123)
- **Código postal:** Cualquier código (ej: 12345)

---

## 📖 Uso

### 1. Crear Pagos de Prueba

Ejecuta el script para generar pagos de inscripción:

```bash
php create_test_payments.php
```

Esto creará pagos pendientes para todos los equipos de la última temporada.

### 2. Ver Pagos del Equipo

Accede a la interfaz de pagos:

```
http://flowfast-saas.test/payments/team
```

O para un equipo específico:

```
http://flowfast-saas.test/payments/team/{team_id}
```

### 3. Proceso de Pago

#### Método 1: Pagar con Tarjeta (Stripe) 💳

1. Click en **"Pagar con Tarjeta"**
2. Se abre modal de Stripe
3. Ingresa número de tarjeta de prueba: `4242 4242 4242 4242`
4. Fecha: `12/25`, CVC: `123`
5. Click en **"Pagar $500.00"**
6. **✅ Pago confirmado automáticamente**

#### Método 2: Pagar en Efectivo 💵

1. Click en **"Pagar en Efectivo"**
2. Confirma la acción
3. Estado cambia a: **"Esperando confirmación"**
4. **El administrador debe confirmar manualmente**

#### Método 3: Pagar por Transferencia 🏦

1. Click en **"Pagar por Transferencia"**
2. Confirma la acción
3. Estado cambia a: **"Esperando confirmación"**
4. **El administrador debe confirmar manualmente**

---

## 🔄 Estados de Pago

| Estado | Descripción | Color |
|--------|-------------|-------|
| `pending` | Pago pendiente | 🟡 Amarillo |
| `paid_by_team` | Pagado, esperando confirmación | 🔵 Azul |
| `confirmed` | Pago confirmado | 🟢 Verde |
| `overdue` | Pago vencido | 🔴 Rojo |

---

## 💰 Flujo de Pagos

### Pago con Tarjeta (Automático)
```
pending → [Stripe Payment] → confirmed
```

### Pago Efectivo/Transferencia (Manual)
```
pending → [Usuario marca como pagado] → paid_by_team → [Admin confirma] → confirmed
```

---

## 🛠️ Archivos Principales

### Backend
```
app/Services/StripeService.php              # Servicio de Stripe
app/Livewire/Payments/StripePayment.php     # Modal de pago
app/Livewire/Payments/TeamPayments.php      # Lista de pagos
app/Models/Income.php                        # Modelo actualizado
config/stripe.php                            # Configuración de Stripe
```

### Frontend
```
resources/views/livewire/payments/stripe-payment.blade.php  # Modal Stripe
resources/views/livewire/payments/team-payments.blade.php   # Vista de pagos
```

### Migraciones
```
database/migrations/2025_10_07_055602_add_stripe_fields_to_incomes_and_expenses_tables.php
```

### Scripts de Prueba
```
create_test_payments.php    # Crear pagos de prueba
check_season.php            # Verificar temporada
```

---

## 🎨 Componentes UI

### Modal de Stripe
- Formulario seguro de Stripe Elements
- Información del pago
- Spinner de carga
- Mensajes de éxito/error
- Tarjetas de prueba listadas

### Lista de Pagos
- Filtros por estado
- Botones de método de pago
- Estados visuales con colores
- Información detallada del pago
- Responsive design

---

## 📊 Base de Datos

### Campos Stripe Agregados

**Tabla: `incomes`**
```sql
stripe_payment_intent_id    VARCHAR(255) NULL
stripe_charge_id            VARCHAR(255) NULL
stripe_customer_id          VARCHAR(255) NULL
stripe_metadata             JSON NULL
```

**Tabla: `expenses`** (mismos campos)

---

## 🔐 Seguridad

### ✅ Implementado
- Tokens de un solo uso (Payment Intents)
- Verificación de pago desde el servidor
- Stripe maneja datos sensibles de tarjetas
- HTTPS requerido en producción

### ⚠️ Recomendaciones para Producción
1. **Obtener llaves reales** de Stripe
2. **Configurar webhooks** para confirmación de pagos
3. **Habilitar HTTPS** en el dominio
4. **Configurar URL de webhook** en Stripe Dashboard
5. **Agregar `STRIPE_WEBHOOK_SECRET`** al `.env`

---

## 🌐 Próximos Pasos

### Para Producción

1. **Crear cuenta real de Stripe**
   - Verificar negocio
   - Conectar cuenta bancaria

2. **Cambiar a llaves de producción**
   - Desactivar modo test
   - Copiar llaves de producción

3. **Configurar Webhooks**
   ```
   URL: https://tu-dominio.com/stripe/webhook
   Eventos: payment_intent.succeeded, payment_intent.failed
   ```

4. **Testing con dinero real**
   - Empezar con transacciones pequeñas
   - Verificar depósitos en cuenta bancaria

---

## 📞 Soporte

### Recursos de Stripe
- [Documentación oficial](https://stripe.com/docs)
- [Tarjetas de prueba](https://stripe.com/docs/testing)
- [Dashboard de Stripe](https://dashboard.stripe.com)

### Preguntas Frecuentes

**¿Los pagos de prueba son reales?**
No, el modo test no mueve dinero real. Solo para desarrollo.

**¿Cuánto cuesta Stripe?**
3.6% + $3 MXN por transacción exitosa en México. Sin cuota mensual.

**¿Necesito cuenta bancaria?**
Solo para producción. En modo test no es necesario.

---

## ✅ Checklist de Implementación

- [x] Instalar Stripe SDK
- [x] Configurar variables de entorno
- [x] Crear servicio de Stripe
- [x] Migración de campos
- [x] Componente de pago con tarjeta
- [x] Componente de lista de pagos
- [x] Rutas de pagos
- [x] UI responsive
- [x] Tarjetas de prueba
- [ ] Webhooks (producción)
- [ ] Llaves reales (producción)

---

## 🎉 ¡Listo para Probar!

1. Ejecuta: `php create_test_payments.php`
2. Ve a: `http://flowfast-saas.test/payments/team`
3. Usa tarjeta: `4242 4242 4242 4242`
4. ¡Disfruta del sistema de pagos! 🚀
