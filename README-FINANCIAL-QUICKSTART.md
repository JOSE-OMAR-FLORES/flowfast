# 📖 Guía Rápida - Sistema Financiero FlowFast SaaS

## 🚀 Inicio Rápido

### 1. **Configurar tu Liga**

Antes de usar el sistema financiero, configura las cuotas de tu liga:

```sql
UPDATE leagues 
SET 
    match_fee = 75.00,           -- Cuota por partido (por equipo)
    referee_payment = 40.00,     -- Pago a árbitros
    registration_fee = 200.00    -- Cuota de inscripción
WHERE id = 1;
```

O desde el panel de administración (CRUD de Ligas).

---

## 💰 Gestionar Ingresos

### **Ver Todos los Ingresos**
**URL**: `http://flowfast-saas.test/financial/income`

**Filtrar ingresos**:
1. Buscar por equipo o referencia
2. Filtrar por liga
3. Filtrar por temporada
4. Filtrar por tipo (registro, partido, multa, etc.)
5. Filtrar por estado (pendiente, pagado, vencido, etc.)

**Acciones disponibles**:
- 👁️ **Ver detalles** del ingreso
- ✅ **Confirmar pago** (3 niveles de validación)
- ⏰ **Marcar como vencido** si no se pagó
- ❌ **Cancelar ingreso**

### **Crear Nuevo Ingreso**
**URL**: `http://flowfast-saas.test/financial/income/create`

**Pasos**:
1. **Información Básica**
   - Seleccionar liga (si solo tienes una, se selecciona automáticamente)
   - Seleccionar temporada (opcional)
   - Seleccionar equipo (opcional)
   - Seleccionar partido (opcional)

2. **Detalles del Ingreso**
   - Tipo de ingreso (7 opciones):
     - Cuota de Inscripción
     - Cuota por Partido
     - Multa
     - Venta de Equipamiento
     - Patrocinio
     - Donación
     - Otro
   - Monto ($)
   - Fecha de vencimiento
   - Método de pago esperado
   - Descripción (se auto-completa según el tipo)
   - Referencia o número de transacción

3. **Comprobante y Notas**
   - Subir comprobante de pago (imagen, max 2MB)
   - Notas adicionales (opcional)

4. Click en **"Registrar Ingreso"**

**Resultado**: El ingreso se crea con estado `pending` y aparecerá en la lista.

---

## 💸 Gestionar Gastos

### **Ver Todos los Gastos**
**URL**: `http://flowfast-saas.test/financial/expense`

**Filtrar gastos**:
1. Buscar por beneficiario o descripción
2. Filtrar por liga
3. Filtrar por temporada
4. Filtrar por tipo (árbitro, cancha, equipo, etc.)
5. Filtrar por estado (pendiente, aprobado, pagado, etc.)

**Acciones disponibles** (según rol):

**Como Admin**:
- ✅ **Aprobar gasto** → Modal con campo de notas
- 💰 **Marcar como pagado** → Modal de confirmación
- ❌ **Cancelar gasto**

**Como Beneficiario** (árbitro, etc.):
- ✅ **Confirmar que recibiste el pago**

### **Crear Nuevo Gasto**
**URL**: `http://flowfast-saas.test/financial/expense/create`

**Pasos**:
1. **Información Básica**
   - Seleccionar liga
   - Seleccionar temporada (opcional)
   - Seleccionar beneficiario (árbitros, admin, league manager)
   - Seleccionar partido (opcional)

2. **Detalles del Gasto**
   - Tipo de gasto (9 opciones):
     - Pago a Árbitro
     - Alquiler de Cancha
     - Equipo Deportivo
     - Mantenimiento
     - Servicios
     - Salario Personal
     - Marketing
     - Seguros
     - Otro
   - Monto ($)
   - Fecha de pago programada
   - Método de pago previsto
   - Descripción (se auto-genera según el tipo)
   - Referencia o número de factura

3. **Factura y Notas**
   - Subir factura o comprobante (PDF/imagen, max 5MB)
   - Arrastrar y soltar funciona ✨
   - Notas adicionales (opcional)

4. Click en **"Registrar Gasto"**

**Resultado**: El gasto se crea con estado `pending` y requerirá aprobación del admin.

---

## 📊 Dashboard Financiero

**URL**: `http://flowfast-saas.test/financial/dashboard/{leagueId}`
- Ejemplo: `http://flowfast-saas.test/financial/dashboard/1`

### **Qué verás**:

**4 Tarjetas de Resumen**:
- 💵 **Ingresos Totales** (con porcentaje de cambio)
- 💸 **Gastos Totales** (con porcentaje de cambio)
- 💰 **Balance** = Ingresos - Gastos (positivo o negativo)
- ⏰ **Pendientes de Cobro** (dinero por recibir)

**Filtros**:
- Por temporada
- Por período (este mes, último mes, últimos 3 meses, este año, todo)

**Gráficos**:
- 📊 Ingresos por tipo
- 📊 Gastos por tipo
- 📊 Estados de pago

**Listas**:
- Ingresos pendientes de confirmación
- Gastos pendientes de aprobación
- Transacciones recientes (últimas 10)

**Alertas**:
- 🔴 Ingresos vencidos (rojo)
- 🟡 Ingresos próximos a vencer (amarillo)
- 🔵 Gastos esperando aprobación (azul)

---

## 🤖 Automatización

### **Cuotas Automáticas por Partido**

Cuando un partido se marca como **"finished"**:
1. ⏰ Espera 5 minutos
2. 💵 Genera 2 ingresos:
   - Uno para el equipo local
   - Uno para el equipo visitante
3. 📅 Fecha de vencimiento: 3 días después del partido
4. 💬 Descripción: "Cuota por partido - Local - Tigres vs Leones"

**No se genera si**:
- Ya existe una cuota para ese partido
- La liga no tiene configurado `match_fee`

### **Pagos Automáticos a Árbitros**

Cuando un partido con árbitro se marca como **"finished"**:
1. ⏰ Espera 5 minutos
2. 💸 Genera 1 gasto a favor del árbitro
3. 📅 Fecha de pago: 7 días después del partido
4. 🔒 Estado: Pendiente de aprobación

**No se genera si**:
- El partido no tiene árbitro asignado
- Ya existe un pago para ese árbitro en ese partido

### **Marcar Vencidos Automáticamente**

Cada día a las **00:00** (medianoche):
1. 🔍 Busca todos los ingresos con estado `pending` o `paid_by_team`
2. ✅ Verifica si la `due_date` ya pasó
3. ⚠️ Los marca como `overdue`
4. 📝 Registra en logs

### **Comandos Manuales**

Si necesitas ejecutar algo manualmente:

```bash
# Generar cuotas de partidos de los últimos 7 días
php artisan financial:generate-match-fees

# Generar cuota de un partido específico
php artisan financial:generate-match-fees --fixture_id=123

# Generar cuotas de una fecha específica
php artisan financial:generate-match-fees --date=2025-10-01

# Marcar ingresos vencidos manualmente
php artisan financial:mark-overdue-incomes
```

---

## 🔄 Flujos de Trabajo

### **Flujo: Confirmar Ingreso (Triple Validación)**

```
1. Equipo paga
   ↓
   Admin/Manager: Click "Confirmar Pago" en tabla
   ↓
   Selecciona: "Pagado por Equipo"
   ↓
   Estado: pending → paid_by_team

2. Admin verifica pago
   ↓
   Admin: Click "Confirmar Pago" en tabla
   ↓
   Selecciona: "Confirmado por Admin"
   ↓
   Estado: paid_by_team → confirmed_by_admin

3. Confirmación final
   ↓
   Admin: Click "Confirmar Pago" en tabla
   ↓
   Selecciona: "Confirmado"
   ↓
   Estado: confirmed_by_admin → confirmed ✅
```

### **Flujo: Aprobar y Pagar Gasto**

```
1. Gasto creado (pending)
   ↓
   Admin: Click "Aprobar" en tabla
   ↓
   Modal: Agregar notas de aprobación (opcional)
   ↓
   Estado: pending → approved

2. Admin marca como pagado
   ↓
   Admin: Click "Marcar como Pagado"
   ↓
   Modal: Confirmar advertencia
   ↓
   Estado: approved → ready_for_payment

3. Beneficiario confirma
   ↓
   Beneficiario: Click "Confirmar Recibido"
   ↓
   Estado: ready_for_payment → paid ✅
```

---

## 👥 Permisos por Rol

### **Admin**
- ✅ Ve todos los ingresos y gastos
- ✅ Puede crear ingresos y gastos
- ✅ Puede confirmar pagos (todos los niveles)
- ✅ Puede aprobar y marcar gastos como pagados
- ✅ Puede cancelar cualquier transacción
- ✅ Acceso completo al dashboard

### **League Manager**
- ✅ Ve ingresos y gastos de SU liga
- ✅ Puede crear ingresos y gastos
- ✅ Puede confirmar pagos de equipos
- ✅ Puede aprobar gastos
- ❌ No puede marcar gastos como pagados (solo admin)
- ✅ Acceso al dashboard de su liga

### **Coach/Team**
- ✅ Ve solo los ingresos de SU equipo
- ❌ No puede crear ni modificar
- ❌ No accede al dashboard financiero

### **Referee/Beneficiario**
- ✅ Ve solo SUS pagos (gastos a su favor)
- ✅ Puede confirmar que recibió el pago
- ❌ No puede crear ni aprobar

---

## 🎨 Indicadores Visuales

### **Estados de Ingresos**
- 🔴 **Pending** (Pendiente) - Rojo
- 🟡 **Paid by Team** (Pagado por Equipo) - Amarillo
- 🔵 **Confirmed by Admin** (Confirmado por Admin) - Azul
- 🟢 **Confirmed** (Confirmado) - Verde
- ⚫ **Cancelled** (Cancelado) - Gris
- 🟠 **Overdue** (Vencido) - Naranja

### **Estados de Gastos**
- 🔴 **Pending** (Pendiente) - Rojo
- 🟡 **Approved** (Aprobado) - Amarillo
- 🔵 **Ready for Payment** (Listo para Pagar) - Azul
- 🟢 **Paid** (Pagado) - Verde
- ⚫ **Cancelled** (Cancelado) - Gris

---

## 📱 Responsive Design

El sistema es **100% responsive**:

- **Mobile** (< 640px): 1 columna, botones full-width, menús colapsables
- **Tablet** (640px - 1024px): 2 columnas, botones auto-width
- **Desktop** (> 1024px): Layouts optimizados, más información visible

**Funciona perfecto en**:
- 📱 iPhone / Android
- 📱 iPad / Tablets
- 💻 Laptops
- 🖥️ Desktops

---

## ⚙️ Configuración Inicial Recomendada

### **1. Configurar Cuotas de Liga**
```sql
UPDATE leagues 
SET 
    match_fee = 50.00,
    referee_payment = 30.00,
    registration_fee = 150.00
WHERE id = 1;
```

### **2. Activar Queue Worker** (Producción)
```bash
php artisan queue:work --daemon
```

### **3. Activar Scheduler** (Producción)
Agregar al crontab:
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### **4. Para Testing Local**
```bash
# Terminal 1: Iniciar servidor
php artisan serve

# Terminal 2: Procesar queues
php artisan queue:work

# Terminal 3: Ejecutar scheduler
php artisan schedule:work
```

---

## 🆘 Solución de Problemas

### **No veo ingresos/gastos**
- Verifica que tengas el rol correcto (admin/league_manager)
- Verifica que hayas seleccionado la liga correcta en filtros
- Verifica que existan datos en la tabla (crea uno de prueba)

### **No se generan cuotas automáticas**
- Verifica que el partido tenga estado `finished`
- Verifica que la liga tenga configurado `match_fee`
- Verifica los logs: `storage/logs/laravel.log`
- Ejecuta manualmente: `php artisan financial:generate-match-fees`

### **El scheduler no funciona**
- En local: Usa `php artisan schedule:work` en lugar de cron
- En producción: Verifica que el cron esté configurado
- Ejecuta manualmente: `php artisan financial:mark-overdue-incomes`

### **Upload de archivos falla**
- Verifica permisos de `storage/app/public`
- Ejecuta: `php artisan storage:link`
- Verifica tamaño máximo: 2MB imágenes, 5MB PDFs

---

## 📞 Soporte

Para más información, consulta:
- `README-FINANCIAL-PROGRESS.md` - Estado del proyecto
- `README-FINANCIAL-AUTOMATION.md` - Documentación técnica de automatización
- `README-FINANCIAL-PART1.md` a `PART4.md` - Documentación técnica completa

---

**¡Disfruta gestionando las finanzas de tu liga! ⚽💰**
