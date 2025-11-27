# 🧪 GUÍA DE PRUEBA - Sistema de Pagos con Stripe

## 🎯 ¡Todo está listo para probar!

---

## 📋 Checklist de Verificación

- [x] ✅ Stripe SDK instalado
- [x] ✅ Configuración en `.env`
- [x] ✅ Migración ejecutada
- [x] ✅ Componentes Livewire creados
- [x] ✅ Rutas configuradas
- [x] ✅ Pagos de prueba generados (5 pagos de $500 c/u)

---

## 🚀 PASO 1: Acceder a la Interfaz de Pagos

Abre tu navegador y ve a:

```
http://flowfast-saas.test/payments/team
```

O para un equipo específico:

```
http://flowfast-saas.test/payments/team/29
```

---

## 💳 PASO 2: Probar Pago con Tarjeta (Stripe)

### Instrucciones:

1. **Click** en el botón azul **"Pagar con Tarjeta"**

2. Se abrirá un **modal de Stripe** con un formulario seguro

3. **Ingresa estos datos de prueba:**
   ```
   Número de tarjeta:     4242 4242 4242 4242
   Fecha de expiración:   12/25
   CVC:                   123
   Código postal:         12345
   ```

4. **Click** en el botón **"Pagar $500.00"**

5. **Verás un spinner de carga** mientras procesa

6. **✅ ¡ÉXITO!** 
   - Mensaje verde: "¡Pago exitoso!"
   - El modal se cierra automáticamente
   - El pago cambia a estado "Confirmed" (verde)
   - La página se recarga mostrando el pago confirmado

### 🧪 Otras Tarjetas para Probar:

**Fondos Insuficientes:**
```
4000 0000 0000 9995
```
❌ Verás un error: "Your card has insufficient funds"

**Tarjeta Declinada:**
```
4000 0000 0000 0002
```
❌ Verás un error: "Your card was declined"

---

## 💵 PASO 3: Probar Pago en Efectivo

1. **Click** en el botón verde **"Pagar en Efectivo"**

2. Confirma la acción en el diálogo

3. **El estado cambia a:**
   - 🔵 "Paid by team" (Azul)
   - Mensaje: "Esperando confirmación"

4. **Nota:** El administrador debe confirmar este pago manualmente

---

## 🏦 PASO 4: Probar Transferencia Bancaria

1. **Click** en el botón morado **"Pagar por Transferencia"**

2. Confirma la acción en el diálogo

3. **El estado cambia a:**
   - 🔵 "Paid by team" (Azul)
   - Mensaje: "Esperando confirmación"

4. **Nota:** El administrador debe confirmar este pago manualmente

---

## 🎨 PASO 5: Probar Filtros

En la parte superior de la página verás un dropdown:

```
[ Todos los pagos ▼ ]
```

Prueba seleccionar:
- **Pendientes** - Solo pagos sin pagar
- **Esperando confirmación** - Pagos marcados por el equipo
- **Confirmados** - Pagos ya procesados
- **Vencidos** - Pagos pasados de fecha límite

---

## 🔍 PASO 6: Verificar en la Base de Datos

Ejecuta este script para ver los pagos:

```bash
php check_season.php
```

Verás:
- Equipos de la temporada
- Jugadores por equipo
- **Pagos de inscripción** con sus estados

Para ver un pago específico procesado con Stripe:

```bash
php artisan tinker
```

Luego en tinker:

```php
$income = App\Models\Income::find(28);
echo "Estado: " . $income->payment_status . "\n";
echo "Método: " . $income->payment_method . "\n";
echo "Payment Intent ID: " . $income->stripe_payment_intent_id . "\n";
echo "Pagado el: " . $income->paid_at . "\n";
```

---

## 🎯 Resultados Esperados

### Pago con Tarjeta (Stripe)
```
✅ Estado: confirmed
✅ Método: card
✅ stripe_payment_intent_id: pi_xxxxxxxxxxxxx
✅ paid_at: 2025-10-06 12:34:56
✅ confirmed_at: 2025-10-06 12:34:56
```

### Pago Efectivo/Transferencia
```
🔵 Estado: paid_by_team
✅ Método: cash o transfer
✅ paid_at: 2025-10-06 12:34:56
⏳ confirmed_at: null (pendiente confirmación admin)
```

---

## 🐛 Troubleshooting

### Error: "Class StripeService not found"
```bash
composer dump-autoload
```

### Error: "Column stripe_payment_intent_id doesn't exist"
```bash
php artisan migrate
```

### Error: "Invalid API Key"
Verifica que las llaves en `.env` estén correctas:
```
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
```

### Modal de Stripe no se abre
1. Verifica la consola del navegador (F12)
2. Asegúrate que Stripe.js se carga correctamente
3. Revisa que `STRIPE_KEY` esté en el `.env`

---

## 📊 Dashboard de Stripe (Opcional)

1. Ve a: https://dashboard.stripe.com/test/payments

2. Inicia sesión con tu cuenta de Stripe

3. **Verás todos los pagos de prueba** que hagas desde la aplicación

4. Puedes ver detalles completos:
   - Monto
   - Tarjeta usada
   - Metadata (team_id, league_id, etc.)
   - Timeline del pago

---

## 🎉 ¡Prueba Completada!

Si todo funciona correctamente, deberías poder:

- [x] Ver la lista de pagos
- [x] Pagar con tarjeta de Stripe (confirmación automática)
- [x] Marcar pago en efectivo (confirmación manual)
- [x] Marcar transferencia (confirmación manual)
- [x] Ver diferentes estados con colores
- [x] Filtrar pagos por estado
- [x] Ver detalles completos de cada pago

---

## 🚀 Próximo Paso: Administrador

Ahora necesitas crear una interfaz para que el **administrador**:

1. Vea pagos pendientes de confirmación
2. Pueda confirmar pagos en efectivo/transferencia
3. Pueda ver reportes de pagos
4. Pueda hacer reembolsos si es necesario

¿Quieres que implemente esto también? 😊

---

## 💡 Tips

- Usa siempre **modo test** mientras desarrollas
- Stripe no cobra nada en modo test
- Las tarjetas de prueba nunca cobran dinero real
- Puedes hacer **ilimitadas transacciones** en modo test
- Cuando pases a producción, solo cambia las llaves

---

**¿Alguna pregunta o problema? ¡Házlo saber!** 🎯
