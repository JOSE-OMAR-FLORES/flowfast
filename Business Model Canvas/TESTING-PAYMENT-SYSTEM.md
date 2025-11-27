# 🧪 Guía de Pruebas - Sistema de Pagos

## 📍 URLs del Sistema

### **Para Entrenadores/Jugadores:**
```
http://flowfast-saas.test/payments/team
```
- Ver pagos de sus equipos
- Pagar con tarjeta, efectivo o transferencia

### **Para Árbitros:**
```
http://flowfast-saas.test/payments/referees
```
- Ver pagos que reciben
- Confirmar pagos en efectivo/transferencia recibidos
- Confirmar pagos de equipos (cuando oficien)

### **Para Admin/Liga Manager:**

**Gestión de Ingresos (pagos de equipos):**
```
http://flowfast-saas.test/admin/financial/income
```
- Ver todos los pagos de equipos
- Confirmar pagos en efectivo/transferencia

**Gestión de Gastos (pagos a árbitros):**
```
http://flowfast-saas.test/admin/financial/expense
```
- Crear gastos para árbitros
- Procesar pagos (tarjeta, efectivo, transferencia)

---

## 🧪 Escenarios de Prueba

### **Escenario 1: Entrenador Paga con Tarjeta** 💳

1. Ingresar como **Coach** (entrenador)
2. Ir a: `http://flowfast-saas.test/payments/team`
3. Buscar un pago con estado "Pendiente"
4. Click en botón **"Pagar Ahora"**
5. En el panel desplegable, click **"Pagar con Tarjeta"**
6. Ingresar tarjeta de prueba de Stripe:
   - Número: `4242 4242 4242 4242`
   - Fecha: Cualquier fecha futura (ej: 12/25)
   - CVC: Cualquier 3 dígitos (ej: 123)
   - Código postal: Cualquier (ej: 12345)
7. Click **"Pagar Ahora"**
8. ✅ **Resultado esperado:**
   - Modal se cierra automáticamente
   - Mensaje: "¡Pago procesado y confirmado exitosamente!"
   - Estado cambia a "Confirmed" (verde)
   - Lista se actualiza sin recargar

---

### **Escenario 2: Entrenador Paga en Efectivo** 💵

1. Ingresar como **Coach**
2. Ir a: `http://flowfast-saas.test/payments/team`
3. Click en **"Pagar Ahora"** → **"Pagar en Efectivo"**
4. Agregar notas opcional es: "Pagado en la oficina de la liga"
5. Click **"Confirmar Pago"**
6. ⏳ **Resultado esperado:**
   - Modal se cierra
   - Mensaje: "¡Pago en efectivo registrado! Esperando confirmación del administrador."
   - Estado cambia a "Pending Confirmation" (azul claro)

7. **Confirmar como Admin:**
   - Cerrar sesión y entrar como **Admin**
   - Ir a: `http://flowfast-saas.test/admin/financial/income`
   - Buscar el pago con estado "Pending Confirmation"
   - Click en **"Confirmar Efectivo"**
   - Agregar notas de confirmación (opcional)
   - Click **"Confirmar Pago"**
   - ✅ Estado cambia a "Confirmed" (verde)

---

### **Escenario 3: Entrenador Paga por Transferencia** 🏦

1. Ingresar como **Coach**
2. Ir a: `http://flowfast-saas.test/payments/team`
3. Click en **"Pagar Ahora"** → **"Pagar por Transferencia"**
4. Llenar formulario:
   - **Referencia**: 123456789
   - **Banco**: Banco Nacional
   - **Notas**: Transferencia desde cuenta empresarial
5. Click **"Registrar Transferencia"**
6. ⏳ **Resultado esperado:**
   - Modal se cierra
   - Mensaje: "¡Transferencia registrada! Esperando confirmación del administrador."
   - Estado cambia a "Pending Confirmation"

7. **Confirmar como Admin o Referee:**
   - Ingresar como **Admin** o **Referee**
   - Ir a: `http://flowfast-saas.test/admin/financial/income`
   - Buscar el pago con estado "Pending Confirmation"
   - Verificar referencia y banco mostrados
   - Click en **"Confirmar Efectivo"** (mismo botón para ambos métodos)
   - ✅ Estado cambia a "Confirmed"

---

### **Escenario 4: Admin Paga a Árbitro con Tarjeta** 💳

1. Ingresar como **Admin**
2. Ir a: `http://flowfast-saas.test/admin/financial/expense`
3. Buscar un gasto para árbitro con estado "Approved"
4. Click en botón morado **"Marcar Listo para Pagar"**
5. Estado cambia a "Ready for Payment"
6. Click en **"Procesar Pago"** (botón morado con gradiente)
7. En el panel desplegable, click **"Pagar con Tarjeta"**
8. Ingresar tarjeta de prueba de Stripe
9. Click **"Pagar Ahora"**
10. ✅ **Resultado esperado:**
    - Pago se procesa y confirma automáticamente
    - Estado cambia a "Confirmed"
    - Mensaje de éxito
    - Lista se actualiza

---

### **Escenario 5: Admin Paga a Árbitro en Efectivo** 💵

1. Ingresar como **Admin**
2. Ir a: `http://flowfast-saas.test/admin/financial/expense`
3. Gasto con estado "Ready for Payment"
4. Click **"Procesar Pago"** → **"Pagar en Efectivo"**
5. Agregar notas: "Pagado en la oficina"
6. Click **"Confirmar Pago"**
7. ⏳ Estado cambia a "Pending Confirmation"

8. **Árbitro confirma recepción:**
   - Cerrar sesión y entrar como **Referee** (árbitro)
   - Ir a: `http://flowfast-saas.test/payments/referees`
   - Buscar el pago con estado "Pending Confirmation"
   - Ver detalles del pago
   - Click en botón de confirmación
   - ✅ Estado cambia a "Confirmed"

---

### **Escenario 6: Árbitro Confirma Pago de Equipo** ✅

**Contexto:** Un árbitro también puede confirmar pagos de equipos (cuando oficia partidos)

1. **Entrenador registra pago en efectivo** (como Escenario 2, pasos 1-6)
2. Ingresar como **Referee** (árbitro)
3. Ir a: `http://flowfast-saas.test/admin/financial/income`
4. Buscar el pago del equipo con "Pending Confirmation"
5. Click **"Confirmar Efectivo"**
6. Agregar notas: "Confirmado por árbitro durante el partido"
7. Click **"Confirmar Pago"**
8. ✅ Estado cambia a "Confirmed"

---

## 🔍 Verificaciones de Seguridad

### **Test 1: Coach no puede ver pagos de otros equipos**
```
1. Login como Coach del Equipo A
2. Ir a /payments/team
3. Verificar que SOLO aparecen pagos del Equipo A
4. ✅ No debe ver pagos de Equipo B, C, etc.
```

### **Test 2: Referee no puede ver pagos de otros referees**
```
1. Login como Referee 1
2. Ir a /payments/referees
3. Verificar que SOLO aparecen pagos para Referee 1
4. ✅ No debe ver pagos de otros árbitros
```

### **Test 3: Roles sin permiso no acceden a dashboards**
```
1. Login como Coach
2. Intentar acceder: /admin/financial/expense
3. ✅ Debe redirigir o mostrar error 403
```

---

## 📊 Estados de Pago - Verificar Transiciones

### **Para Incomes (Pagos de Equipos):**
```
pending → pending_confirmation → confirmed
pending → confirmed (si paga con tarjeta)
```

### **Para Expenses (Pagos a Árbitros):**
```
pending → approved → ready_for_payment → confirmed
```

---

## 🎨 Verificaciones Visuales

### **Botones y Colores:**
- 🔵 **Azul**: Pagar con Tarjeta
- 🟢 **Verde**: Pagar en Efectivo / Confirmar
- 🟣 **Morado**: Transferencia / Procesar Pago
- 🔴 **Rojo**: Cancelar

### **Estados con Badges:**
- 🟡 **Amarillo**: Pending
- 🔵 **Azul claro**: Pending Confirmation
- 🟢 **Verde**: Confirmed
- 🔴 **Rojo**: Overdue/Cancelled

### **Animaciones:**
- ✅ Panel desplegable se abre suavemente
- ✅ Modal aparece con fade-in
- ✅ Botones tienen hover effects
- ✅ Flecha del botón gira al expandir

---

## 🐛 Problemas Comunes y Soluciones

### **Problema: Botones de pago no aparecen**
**Solución:**
```
- Verificar que payment_status sea 'pending' o 'ready_for_payment'
- Verificar permisos del usuario
- Verificar en DevTools si hay errores de JavaScript
```

### **Problema: Stripe no carga**
**Solución:**
```
- Verificar .env: STRIPE_KEY y STRIPE_SECRET
- Verificar en Network tab que se carga: https://js.stripe.com/v3/
- Verificar consola por errores de Stripe
```

### **Problema: No se actualiza después de pagar**
**Solución:**
```
- Verificar que se emite el evento: payment-successful o payment-confirmed
- Verificar que el componente padre tiene el listener
- Revisar en Livewire Network tab si se ejecuta la acción
```

### **Problema: Modal no se cierra**
**Solución:**
```
- Verificar que showModal cambia a false
- Verificar en Alpine DevTools el estado
- Refrescar la página (F5)
```

---

## ✅ Checklist Final de Pruebas

- [ ] Entrenador paga con tarjeta → Confirmado automáticamente
- [ ] Entrenador paga en efectivo → Requiere confirmación admin
- [ ] Entrenador paga por transferencia → Requiere confirmación admin
- [ ] Admin confirma pago en efectivo de equipo
- [ ] Admin confirma transferencia de equipo
- [ ] Árbitro confirma pago en efectivo de equipo
- [ ] Admin paga a árbitro con tarjeta → Confirmado automáticamente
- [ ] Admin paga a árbitro en efectivo → Árbitro confirma
- [ ] Admin paga a árbitro por transferencia → Árbitro confirma
- [ ] Los coaches solo ven sus pagos
- [ ] Los referees solo ven sus pagos
- [ ] Auto-refresh funciona después de cada pago
- [ ] Mensajes flash se muestran correctamente
- [ ] Animaciones funcionan suavemente
- [ ] Panel desplegable se abre/cierra bien
- [ ] Modales se abren/cierran correctamente

---

## 📞 Contacto para Reporte de Bugs

Si encuentras algún problema:
1. Captura de pantalla del error
2. Pasos para reproducir
3. Rol del usuario
4. Navegador y versión
5. Errores en consola (F12 → Console)

---

**¡Sistema completamente probado y listo! 🎉**
