# 💰 Sistema Completo de Pagos - FlowFast SaaS

## 🎯 Visión General

Sistema integral de gestión financiera con integración de Stripe para procesar pagos de forma segura tanto para **equipos que pagan inscripciones** como para **administradores que pagan a árbitros**.

---

## 📊 Dos Flujos de Pago Implementados

### 1️⃣ Pagos de Equipos (Incomes)
**Quién paga:** Equipos/Coaches
**A quién:** Liga/Administrador
**Por concepto:** Inscripciones, cuotas de temporada, multas

### 2️⃣ Pagos a Árbitros (Expenses)
**Quién paga:** Administrador/Encargado de Liga
**A quién:** Árbitros
**Por concepto:** Arbitrajes, bonos, viáticos

---

## 🏗️ Arquitectura del Sistema

```
┌─────────────────────────────────────────────────────────────┐
│                    Frontend (Livewire)                      │
├─────────────────────────────────────────────────────────────┤
│  TeamPayments.php          RefereePayments.php             │
│  StripePayment.php         StripeExpensePayment.php        │
└────────────────────┬───────────────────┬────────────────────┘
                     │                   │
                     ▼                   ▼
        ┌────────────────────────────────────────┐
        │      StripeService.php (Shared)        │
        │  - createPaymentIntent()               │
        │  - getPaymentIntent()                  │
        │  - isPaymentSuccessful()               │
        │  - refundPayment()                     │
        └────────────────┬───────────────────────┘
                         │
                         ▼
              ┌──────────────────────┐
              │    Stripe API v3     │
              │   (Modo Prueba)      │
              └──────────────────────┘
                         │
                         ▼
        ┌────────────────────────────────┐
        │   Base de Datos MySQL          │
        ├────────────────────────────────┤
        │  incomes (Pagos de equipos)    │
        │  expenses (Pagos a árbitros)   │
        └────────────────────────────────┘
```

---

## 💳 Métodos de Pago Disponibles

| Método | Icono | Procesamiento | Ambos Flujos |
|--------|-------|---------------|--------------|
| Tarjeta (Stripe) | 💳 | Automático | ✅ Sí |
| Efectivo | 💵 | Manual | ✅ Sí |
| Transferencia | 🏦 | Manual | ✅ Sí |

---

## 🔄 Estados de Pago

### Flujo de Equipos (Income)
```
pending → paid_by_team → confirmed
```

### Flujo de Árbitros (Expense)
```
pending → approved → ready_for_payment → confirmed
```

---

## 📁 Estructura de Archivos

```
flowfast-saas/
├── app/
│   ├── Livewire/
│   │   └── Payments/
│   │       ├── TeamPayments.php              # Lista de pagos de equipos
│   │       ├── StripePayment.php             # Modal Stripe para equipos
│   │       ├── RefereePayments.php           # Lista de pagos a árbitros
│   │       └── StripeExpensePayment.php      # Modal Stripe para árbitros
│   ├── Models/
│   │   ├── Income.php                        # Modelo con campos Stripe
│   │   └── Expense.php                       # Modelo con campos Stripe
│   └── Services/
│       └── StripeService.php                 # Servicio compartido de Stripe
│
├── resources/views/livewire/payments/
│   ├── team-payments.blade.php               # UI lista equipos
│   ├── stripe-payment.blade.php              # UI modal equipos
│   ├── referee-payments.blade.php            # UI lista árbitros
│   └── stripe-expense-payment.blade.php      # UI modal árbitros
│
├── database/migrations/
│   ├── 2025_10_07_055602_add_stripe_fields...php  # Campos Stripe
│   └── 2025_10_07_061035_add_referee_expense...php # Tipos de gasto
│
├── config/
│   └── stripe.php                            # Configuración global
│
├── routes/
│   └── web.php                               # Rutas de pagos
│
├── Scripts de Prueba/
│   ├── create_test_payments.php              # Datos equipos
│   └── create_test_referee_payments.php      # Datos árbitros
│
└── Documentación/
    ├── README-STRIPE-PAYMENTS.md             # Pagos de equipos
    ├── README-REFEREE-PAYMENTS.md            # Pagos a árbitros
    ├── PRUEBA-STRIPE.md                      # Guía de pruebas
    └── README-PAYMENTS-OVERVIEW.md           # Este archivo
```

---

## 🚀 URLs de Acceso

| Funcionalidad | URL | Roles Permitidos |
|---------------|-----|------------------|
| Pagos de Equipos | `/payments/team/{teamId?}` | Todos autenticados |
| Pagos a Árbitros | `/payments/referees` | admin, league_manager |

---

## 🔐 Seguridad Implementada

### ✅ Autenticación y Autorización
- Middleware de autenticación en todas las rutas
- Control de acceso basado en roles
- Validación de permisos antes de cada acción

### ✅ Stripe Security
- Uso de Payment Intents (PCI compliant)
- Tokens efímeros (un solo uso)
- Client Secret único por transacción
- Confirmación en servidor antes de actualizar BD
- Validación de webhooks (para implementación futura)

### ✅ Validaciones de Negocio
- Verificación de estado antes de transiciones
- Prevención de pagos duplicados
- Validación de montos mínimos/máximos
- Verificación de pertenencia de recursos

---

## 📊 Campos Stripe en Base de Datos

Ambas tablas (`incomes` y `expenses`) comparten los mismos campos Stripe:

```sql
stripe_payment_intent_id  VARCHAR(255) NULLABLE  -- ID del Payment Intent
stripe_charge_id         VARCHAR(255) NULLABLE  -- ID de la carga
stripe_customer_id       VARCHAR(255) NULLABLE  -- ID del cliente
stripe_metadata          JSON NULLABLE          -- Metadatos adicionales
```

---

## 🧪 Pruebas Completas

### Scripts Disponibles
```bash
# Generar pagos de equipos
php create_test_payments.php

# Generar pagos a árbitros
php create_test_referee_payments.php
```

### Tarjetas de Prueba
| Número | Resultado |
|--------|-----------|
| 4242 4242 4242 4242 | ✅ Exitoso |
| 4000 0000 0000 9995 | ❌ Fondos insuficientes |
| 4000 0000 0000 0002 | ❌ Rechazada |

---

## 🎨 Componentes UI Compartidos

### Características Comunes
- ✅ Diseño responsive (mobile-first)
- ✅ Estados visuales con colores (Tailwind)
- ✅ Filtros dinámicos con Livewire
- ✅ Paginación automática
- ✅ Mensajes flash (success/error)
- ✅ Spinners durante procesamiento
- ✅ Confirmaciones de acciones críticas

### Paleta de Estados
```css
pending             → bg-yellow-100 text-yellow-800
approved            → bg-blue-100 text-blue-800
ready_for_payment   → bg-purple-100 text-purple-800
paid_by_team        → bg-green-100 text-green-800
confirmed           → bg-green-100 text-green-800
cancelled           → bg-red-100 text-red-800
```

---

## 📈 Estadísticas del Sistema

### Métricas Disponibles (por implementar en dashboard)
- 💰 Total de ingresos por liga
- 💸 Total de gastos en árbitros
- 📊 Tasa de conversión de pagos
- ⏱️ Tiempo promedio de aprobación
- 💳 Método de pago más usado
- 📅 Pagos pendientes vs completados

---

## 🔧 Configuración Global (.env)

```env
# Stripe Keys (Modo Test)
STRIPE_KEY=pk_test_YOUR_PUBLISHABLE_KEY_HERE
STRIPE_SECRET=sk_test_YOUR_SECRET_KEY_HERE

# Stripe Webhook (para producción)
STRIPE_WEBHOOK_SECRET=whsec_test_secret

# Moneda
STRIPE_CURRENCY=mxn
```

---

## 🎯 Casos de Uso Principales

### 1. Equipo Paga Inscripción
```
1. Coach entra a /payments/team/{teamId}
2. Ve su pago pendiente de inscripción
3. Hace clic en "Pagar con Tarjeta"
4. Ingresa datos en modal Stripe
5. Pago procesado → estado: paid_by_team
6. Admin confirma → estado: confirmed
```

### 2. Admin Paga a Árbitro
```
1. Admin entra a /payments/referees
2. Ve lista de pagos pendientes
3. Aprueba pago → approved
4. Marca listo → ready_for_payment
5. Hace clic en "Pagar con Tarjeta"
6. Ingresa datos en modal Stripe
7. Pago procesado → estado: confirmed
```

---

## 🚦 Estado de Implementación

| Componente | Estado | Notas |
|-----------|---------|-------|
| StripeService | ✅ Completo | Servicio compartido |
| TeamPayments | ✅ Completo | Lista y filtros |
| StripePayment | ✅ Completo | Modal para equipos |
| RefereePayments | ✅ Completo | Lista y filtros |
| StripeExpensePayment | ✅ Completo | Modal para árbitros |
| Migraciones | ✅ Ejecutadas | Ambas tablas actualizadas |
| Rutas | ✅ Configuradas | Middleware aplicado |
| Documentación | ✅ Completa | 4 archivos README |
| Scripts de Prueba | ✅ Funcionales | Generan datos de prueba |
| Webhooks | ⏳ Pendiente | Para producción |
| Dashboard Financiero | ⏳ Pendiente | Para estadísticas |

---

## 🌟 Ventajas del Sistema

### Para Equipos/Coaches
- ✅ Pago rápido y seguro de inscripciones
- ✅ Visibilidad del estado del pago
- ✅ Múltiples opciones de pago
- ✅ Sin necesidad de efectivo

### Para Administradores
- ✅ Control total del flujo financiero
- ✅ Aprobación sistemática de pagos
- ✅ Trazabilidad completa
- ✅ Reducción de errores manuales

### Para Árbitros
- ✅ Pagos puntuales y transparentes
- ✅ Visibilidad del proceso
- ✅ Seguridad en transacciones

### Técnicas
- ✅ Código reutilizable (StripeService)
- ✅ Componentes Livewire reactivos
- ✅ Separación de responsabilidades
- ✅ Integración robusta con API externa
- ✅ Manejo de errores completo

---

## 🔄 Flujo de Integración con Stripe

```javascript
// Frontend (Blade + JavaScript)
1. Usuario hace clic en "Pagar con Tarjeta"
   ↓
2. Livewire abre modal
   ↓
3. Se llama a openPaymentModal()
   ↓
4. Backend (StripeService)
5. createPaymentIntent($amount, $description, $metadata)
   ↓
6. Stripe API crea Payment Intent
   ↓
7. Retorna Client Secret
   ↓
8. Frontend muestra Stripe Elements
9. Usuario ingresa datos de tarjeta
   ↓
10. Stripe.js confirma pago (stripe.confirmPayment)
    ↓
11. Success → Frontend llama paymentCompleted($paymentIntentId)
    ↓
12. Backend verifica con isPaymentSuccessful($paymentIntentId)
    ↓
13. Actualiza BD con estado 'confirmed' y datos Stripe
    ↓
14. Muestra mensaje de éxito
```

---

## 📚 Documentación Complementaria

| Documento | Descripción |
|-----------|-------------|
| [README-STRIPE-PAYMENTS.md](README-STRIPE-PAYMENTS.md) | Sistema de pagos de equipos |
| [README-REFEREE-PAYMENTS.md](README-REFEREE-PAYMENTS.md) | Sistema de pagos a árbitros |
| [PRUEBA-STRIPE.md](PRUEBA-STRIPE.md) | Guía completa de pruebas |
| [README-FINANCIAL-PART1.md](README-FINANCIAL-PART1.md) | Base del sistema financiero |

---

## 🐛 Troubleshooting Común

### Problema: "Stripe key not found"
```bash
# Solución
php artisan config:cache
php artisan cache:clear
```

### Problema: "Payment Intent creation failed"
```
Verificar:
1. STRIPE_SECRET correcta en .env
2. Usar llaves de test (comienzan con sk_test_)
3. Monto válido (mayor a 0)
```

### Problema: "No se puede aprobar el pago"
```
Verificar:
1. Usuario tiene rol correcto (admin o league_manager)
2. Estado del pago es el correcto
3. No hay restricciones de liga (league_manager solo su liga)
```

---

## 🎓 Conceptos Clave de Stripe

### Payment Intent
- **Qué es:** Objeto que representa la intención de cobrar
- **Ventaja:** Maneja autenticación 3D Secure automáticamente
- **Seguridad:** Token de un solo uso, no reutilizable

### Client Secret
- **Qué es:** Clave temporal para confirmar el pago
- **Uso:** Solo en frontend, expira tras uso
- **Seguridad:** No permite reembolsos ni otros usos

### Stripe Elements
- **Qué es:** Componentes UI seguros para capturar tarjetas
- **Ventaja:** PCI compliant, sin almacenar datos sensibles
- **Personalización:** Estilos customizables

---

## 📊 Próximas Mejoras Sugeridas

### Corto Plazo
- [ ] Dashboard financiero con gráficas
- [ ] Exportar reportes a Excel/PDF
- [ ] Notificaciones por email
- [ ] Comprobantes de pago descargables

### Mediano Plazo
- [ ] Webhooks de Stripe para automatización
- [ ] Pagos recurrentes (cuotas mensuales)
- [ ] Múltiples métodos de pago (OXXO, SPEI)
- [ ] Reembolsos desde la UI

### Largo Plazo
- [ ] Multi-tenant por liga
- [ ] Múltiples divisas
- [ ] Integración con contabilidad
- [ ] App móvil para pagos

---

## ✅ Checklist de Implementación Completa

### Backend
- [x] StripeService creado
- [x] Modelos actualizados (Income, Expense)
- [x] Migraciones ejecutadas
- [x] Componentes Livewire (4 archivos)
- [x] Validaciones implementadas
- [x] Manejo de errores

### Frontend
- [x] Vistas Blade (4 archivos)
- [x] JavaScript para Stripe Elements
- [x] Estilos Tailwind
- [x] Componentes responsive
- [x] Mensajes de feedback

### Configuración
- [x] config/stripe.php
- [x] Variables en .env
- [x] Rutas con middleware
- [x] Composer dependencies

### Pruebas
- [x] Scripts de datos de prueba (2)
- [x] Tarjetas de prueba validadas
- [x] Flujos completos probados

### Documentación
- [x] README principal (este)
- [x] README pagos equipos
- [x] README pagos árbitros
- [x] Guía de pruebas

---

## 🎉 Conclusión

Sistema de pagos completo, seguro y funcional implementado con:
- ✅ **Dos flujos de pago** (incomes y expenses)
- ✅ **Integración robusta con Stripe**
- ✅ **UI moderna y responsive**
- ✅ **Código reutilizable y mantenible**
- ✅ **Documentación exhaustiva**
- ✅ **Scripts de prueba funcionales**

**El sistema está listo para usar en modo prueba** y preparado para pasar a producción con configuraciones mínimas.

---

## 📞 Soporte

Para cualquier duda o problema:
1. Revisar la documentación relevante
2. Verificar configuración en `.env`
3. Consultar logs en `storage/logs/laravel.log`
4. Revisar dashboard de Stripe: https://dashboard.stripe.com/test

---

**Desarrollado con ❤️ para FlowFast SaaS**
