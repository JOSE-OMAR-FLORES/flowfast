# ✅ Sistema de Generación Automática de Ingresos y Egresos

## 📋 Funcionamiento INMEDIATO (Sin Jobs)

### ✨ Cómo Funciona

Cuando marcas un partido como **completado** en la web, **INMEDIATAMENTE** se generan:

1. **2 Ingresos** (Cuotas por partido):
   - Cuota para el equipo local
   - Cuota para el equipo visitante
   - Monto: Configurado en `league->match_fee` (default: $50.00 c/u)
   - Vencimiento: 3 días después del partido
   - Estado: Pendiente

2. **1 Egreso** (Pago al árbitro, si existe):
   - Pago al árbitro asignado al partido
   - Monto: Configurado en `league->referee_payment` (default: $30.00)
   - Vencimiento: 7 días después del partido
   - Estado: Pendiente

3. **Actualización de Standings**:
   - Tabla de posiciones se actualiza automáticamente

---

## 🎯 Flujo en la Web

### **Paso 1**: Ir al partido
```
http://flowfast-saas.test/admin/fixtures
→ Click en "Ver en Vivo" o "Gestionar" en cualquier partido
```

### **Paso 2**: Finalizar el partido
```
→ Click en "Finalizar Partido" o "Marcar como Completado"
```

### **Paso 3**: Ver las transacciones generadas
```
Ingresos:
http://flowfast-saas.test/admin/incomes

Egresos:
http://flowfast-saas.test/admin/expenses
```

---

## ⚙️ Configuración de Montos

### Editar montos por liga:

Los montos se configuran al **crear o editar una liga**:

```
http://flowfast-saas.test/admin/leagues/{id}/edit
```

Campos:
- **`match_fee`**: Cuota que paga cada equipo por partido
- **`referee_payment`**: Pago que se hace al árbitro por partido

Si no están configurados, se usan los valores por defecto:
- Match Fee: $50.00
- Referee Payment: $30.00

---

## 📊 Ejemplo Real

```
Partido: Leones del Sur vs Tigres FC
Liga: Liga Regional (match_fee: $75, referee_payment: $40)
Árbitro: Juan Pérez

Al completar el partido, se crean automáticamente:

INGRESOS:
✓ Cuota por partido - Local - Leones del Sur vs Tigres FC: $75.00
✓ Cuota por partido - Visitante - Leones del Sur vs Tigres FC: $75.00

EGRESOS:
✓ Pago a árbitro - Leones del Sur vs Tigres FC: $40.00

Total Ingresos: $150.00
Total Egresos: $40.00
Balance: $110.00
```

---

## ✅ Ventajas

- ⚡ **Inmediato**: No requiere workers ni comandos
- 🎯 **Automático**: Solo completa el partido
- 💰 **Preciso**: Usa la configuración de cada liga
- 📊 **Trazable**: Cada transacción está vinculada al partido
- 🔒 **Seguro**: Solo se genera una vez por partido

---

## 🧪 Probar el Sistema

Ejecuta el script de prueba:
```bash
php test_immediate_generation.php
```

Esto:
1. Busca un partido disponible
2. Lo marca como completado
3. Muestra las transacciones generadas
4. Confirma que todo funciona

---

## 🔍 Verificar Transacciones

### Ver en la Base de Datos:

```sql
-- Ingresos del último partido completado
SELECT i.*, f.id as fixture_id, 
       CONCAT(ht.name, ' vs ', at.name) as match
FROM incomes i
JOIN fixtures f ON i.fixture_id = f.id
JOIN teams ht ON f.home_team_id = ht.id
JOIN teams at ON f.away_team_id = at.id
WHERE f.status = 'completed'
ORDER BY i.created_at DESC
LIMIT 5;

-- Egresos del último partido completado
SELECT e.*, f.id as fixture_id,
       CONCAT(ht.name, ' vs ', at.name) as match
FROM expenses e
JOIN fixtures f ON e.fixture_id = f.id
JOIN teams ht ON f.home_team_id = ht.id
JOIN teams at ON f.away_team_id = at.id
WHERE f.status = 'completed'
ORDER BY e.created_at DESC
LIMIT 5;
```

---

## 🚀 ¡Listo!

Ya no necesitas:
- ❌ Ejecutar `php artisan queue:work`
- ❌ Instalar NSSM
- ❌ Configurar servicios
- ❌ Preocuparte por jobs

**Todo funciona automáticamente al completar un partido en la web.** 🎉
