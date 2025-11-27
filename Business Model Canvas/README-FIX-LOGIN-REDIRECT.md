# Fix de Redirección en Login

## 🐛 Problema Identificado

Al iniciar sesión como **referee**, el sistema redirigía incorrectamente a `/admin/fixtures` en lugar de `/referee/matches`.

## 🔍 Causa Raíz

Dos archivos tenían redirects hardcodeados o con rutas antiguas:

### 1. **AuthenticatedSessionController.php**
```php
// ❌ ANTES (Incorrecto)
return redirect()->intended(route('admin.dashboard', absolute: false));
```
Siempre redirigía a `admin.dashboard` sin importar el tipo de usuario.

### 2. **RoleMiddleware.php**
```php
// ❌ ANTES (Incorrecto)
'coach', 'player' => route('teams.index'),
'referee' => route('fixtures.index'),
```
Usaba rutas antiguas que no existían en la nueva estructura.

## ✅ Solución Implementada

### 1. **Archivo: `app/Http/Controllers/Auth/AuthenticatedSessionController.php`**

```php
public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    $request->session()->regenerate();

    // Obtener el usuario autenticado
    $user = Auth::user();

    // Redirigir según el tipo de usuario
    $redirectRoute = match($user->user_type) {
        'admin' => route('admin.dashboard'),
        'league_manager' => route('admin.dashboard'),
        'referee' => route('referee.matches.index'),
        'coach' => route('coach.teams.index'),
        'player' => route('player.team.index'),
        default => route('admin.dashboard'),
    };

    return redirect()->intended($redirectRoute);
}
```

### 2. **Archivo: `app/Http/Middleware/RoleMiddleware.php`**

```php
// Verificar si el usuario tiene alguno de los roles permitidos
if (!in_array($user->user_type, $roles)) {
    // Redirigir al área apropiada según su rol
    $redirectRoute = match($user->user_type) {
        'admin', 'league_manager' => route('admin.dashboard'),
        'coach' => route('coach.teams.index'),
        'player' => route('player.team.index'),
        'referee' => route('referee.matches.index'),
        default => route('login'),
    };
    
    return redirect($redirectRoute)
        ->with('error', 'No tienes permiso para acceder a esta área.');
}
```

## 🎯 Comportamiento Después del Fix

### **Al Iniciar Sesión:**

| Tipo Usuario | Ruta de Destino | URL |
|-------------|-----------------|-----|
| **Admin** | `admin.dashboard` | `/admin` |
| **League Manager** | `admin.dashboard` | `/admin` |
| **Referee** | `referee.matches.index` | `/referee/matches` ✅ |
| **Coach** | `coach.teams.index` | `/coach/teams` ✅ |
| **Player** | `player.team.index` | `/player/team` ✅ |

### **Al Intentar Acceder a Área No Autorizada:**

Ejemplo: Un **referee** intenta acceder a `/admin/fixtures`

1. ❌ Middleware detecta que no tiene rol `admin` o `league_manager`
2. ✅ Lo redirige automáticamente a `/referee/matches`
3. ✅ Muestra mensaje: "No tienes permiso para acceder a esta área"

## 🔄 Flujo Completo Corregido

### **Referee Login:**
```
1. Ir a: http://flowfast-saas.test/login
2. Ingresar email y contraseña
3. Click en "Iniciar Sesión"
4. ✅ Redirect automático a: /referee/matches
```

### **Coach Login:**
```
1. Ir a: http://flowfast-saas.test/login
2. Ingresar email y contraseña
3. Click en "Iniciar Sesión"
4. ✅ Redirect automático a: /coach/teams
```

### **Player Login:**
```
1. Ir a: http://flowfast-saas.test/login
2. Ingresar email y contraseña
3. Click en "Iniciar Sesión"
4. ✅ Redirect automático a: /player/team
```

## 📝 Archivos Modificados

1. ✅ `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
   - Método `store()` - Redirect dinámico según `user_type`

2. ✅ `app/Http/Middleware/RoleMiddleware.php`
   - Método `handle()` - Redirects actualizados a nuevas rutas

## 🧪 Testing

### **Probar como Referee:**
```bash
# 1. Logout si estás logueado
http://flowfast-saas.test/logout

# 2. Login
http://flowfast-saas.test/login
Email: referee@example.com
Password: tu_contraseña

# 3. Verificar redirect
Debería ir a: http://flowfast-saas.test/referee/matches ✅
```

### **Probar Intento de Acceso No Autorizado:**
```bash
# 1. Logueado como referee, intentar acceder:
http://flowfast-saas.test/admin/fixtures

# 2. Verificar redirect automático
Debería volver a: http://flowfast-saas.test/referee/matches ✅
Con mensaje: "No tienes permiso para acceder a esta área"
```

## 🚀 Comandos Ejecutados

```bash
php artisan optimize:clear
```

## ⚠️ Notas Importantes

1. **`redirect()->intended()`**: Mantiene la funcionalidad de "intended URL"
   - Si intentaste acceder a una URL antes de login, te llevará ahí
   - Si no, usa el default según tu rol

2. **Middleware Protection**: Cada área está protegida por middleware
   - `/admin/*` → Solo admin y league_manager
   - `/referee/*` → Solo referees
   - `/coach/*` → Solo coaches
   - `/player/*` → Solo players

3. **Caché**: Después de cambios en controladores/middleware, siempre ejecutar:
   ```bash
   php artisan optimize:clear
   ```

## 🎉 Resultado Final

✅ Referees ahora van a `/referee/matches` al login
✅ Coaches ahora van a `/coach/teams` al login
✅ Players ahora van a `/player/team` al login
✅ Admins/League Managers siguen yendo a `/admin`
✅ Intentos de acceso no autorizado redirigen correctamente
✅ Mensajes de error informativos

## 📚 Referencias

- [README-RESTRUCTURACION-RUTAS.md](README-RESTRUCTURACION-RUTAS.md) - Estructura completa de rutas
- [README-AUTH.md](README-AUTH.md) - Sistema de autenticación
