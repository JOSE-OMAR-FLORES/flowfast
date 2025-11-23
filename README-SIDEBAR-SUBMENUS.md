# 📂 Sidebar con Submenús Desplegables

## 🎯 Descripción General

El sidebar ahora incluye submenús desplegables (acordeones) para las secciones principales de gestión, permitiendo acceso rápido tanto a las listas principales como a las páginas de creación.

---

## ✨ Características Implementadas

### 1. **Submenús Acordeón**

Cada sección principal de gestión tiene un submenú con:
- ✅ **Ver Todas:** Acceso a la lista/índice
- ✅ **Crear Nueva:** Acceso directo al formulario de creación
- ✅ **Animación suave:** Apertura/cierre con `x-collapse`
- ✅ **Flecha rotativa:** Indicador visual del estado (↓ ↑)

### 2. **Secciones con Submenús**

#### **📁 Ligas**
```
Ligas
├─ 📋 Ver Todas (leagues.index)
└─ ➕ Crear Nueva (leagues.create)
```

#### **📁 Temporadas**
```
Temporadas
├─ 📋 Ver Todas (seasons.index)
└─ ➕ Crear Nueva (seasons.create)
```

#### **📁 Equipos**
```
Equipos
├─ 📋 Ver Todos (teams.index)
└─ ➕ Crear Nuevo (teams.create)
```

#### **📁 Calendario**
```
Calendario
├─ 📋 Ver Calendario (fixtures.index)
└─ 🎲 Generar Fixtures (fixtures.generate)
```

### 3. **Enlaces Funcionales**

| Enlace | Ruta | Estado |
|--------|------|--------|
| Dashboard | `admin.dashboard` | ✅ Funcional |
| Ligas → Ver Todas | `leagues.index` | ✅ Funcional |
| Ligas → Crear | `leagues.create` | ✅ Funcional |
| Temporadas → Ver | `seasons.index` | ✅ Funcional |
| Temporadas → Crear | `seasons.create` | ✅ Funcional |
| Equipos → Ver | `teams.index` | ✅ Funcional |
| Equipos → Crear | `teams.create` | ✅ Funcional |
| Fixtures → Ver | `fixtures.index` | ✅ Funcional |
| Fixtures → Generar | `fixtures.generate` | ✅ Funcional |
| Perfil | `profile.edit` | ✅ Funcional |
| Cerrar Sesión | `logout` | ✅ Funcional |

### 4. **Enlaces Temporales (Próximamente)**

Marcados con estilo deshabilitado (`opacity: 0.5`):
- ⏳ Partidos
- ⏳ Invitaciones
- ⏳ Reportes
- ⏳ Notificaciones

---

## 🏗️ Arquitectura Técnica

### **Alpine.js State Management**

```blade
<ul class="sidebar-links" x-data="{ 
    openMenu: null,
    toggle(menu) {
        this.openMenu = this.openMenu === menu ? null : menu;
    }
}">
```

**Funcionalidad:**
- `openMenu`: Variable que almacena el nombre del menú actualmente abierto
- `toggle(menu)`: Función que abre/cierra menús (solo uno abierto a la vez)

### **Estructura de Submenú**

```blade
<li class="has-submenu">
    <a href="javascript:void(0)" @click="toggle('leagues')">
        <svg>...</svg>
        <span>Ligas</span>
        <svg class="submenu-arrow" :class="{ 'rotate-180': openMenu === 'leagues' }">
            <!-- Flecha hacia abajo -->
        </svg>
    </a>
    <ul class="submenu" x-show="openMenu === 'leagues'" x-collapse>
        <li><a href="{{ route('leagues.index') }}">📋 Ver Todas</a></li>
        <li><a href="{{ route('leagues.create') }}">➕ Crear Nueva</a></li>
    </ul>
</li>
```

**Elementos Clave:**
1. **Trigger:** `@click="toggle('leagues')"` - Abre/cierra el submenú
2. **Flecha:** `:class="{ 'rotate-180': openMenu === 'leagues' }"` - Rota cuando está abierto
3. **Submenú:** `x-show` y `x-collapse` para animación suave
4. **Enlaces:** Rutas Laravel con `route()`

---

## 🎨 Estilos CSS

### **Clase `.has-submenu`**

```css
.sidebar-links li.has-submenu > a {
  display: flex;
  align-items: center;
  justify-content: space-between;
  cursor: pointer;
}
```

### **Submenú `.submenu`**

```css
.sidebar-links .submenu {
  list-style: none;
  padding-left: 0;
  margin: 0;
  overflow: hidden;
}

.sidebar-links .submenu li a {
  padding: 10px 10px 10px 56px;
  font-size: 0.875rem;
  color: rgba(255, 255, 255, 0.8);
}

.sidebar-links .submenu li a:hover {
  background: rgba(102, 126, 234, 0.15);
  color: #fff;
  padding-left: 60px;
}
```

**Características:**
- **Indentación:** 56px para diferenciar visualmente
- **Hover:** Aumenta indentación a 60px
- **Color:** Tono más claro para sub-elementos
- **Tamaño:** 0.875rem (14px)

### **Flecha Rotativa**

```css
.submenu-arrow {
  transition: transform 0.3s ease;
}

.rotate-180 {
  transform: rotate(180deg);
}
```

### **Responsive: Ocultar en Collapsed**

```css
@media screen and (min-width: 1025px) {
  .modern-sidebar:not(:hover) .submenu {
    display: none;
  }
  
  .modern-sidebar:not(:hover) .submenu-arrow {
    display: none;
  }
}
```

En desktop, cuando el sidebar está colapsado (100px), los submenús se ocultan completamente.

---

## 🔧 Cómo Agregar un Nuevo Submenú

### Paso 1: Estructura HTML

```blade
<li class="has-submenu">
    <a href="javascript:void(0)" @click="toggle('nombre_menu')">
        <svg><!-- Icono --></svg>
        <span>Título</span>
        <svg class="submenu-arrow" :class="{ 'rotate-180': openMenu === 'nombre_menu' }">
            <path d="M19 9l-7 7-7-7"></path>
        </svg>
    </a>
    <ul class="submenu" x-show="openMenu === 'nombre_menu'" x-collapse>
        <li><a href="{{ route('ruta.index') }}">📋 Ver</a></li>
        <li><a href="{{ route('ruta.create') }}">➕ Crear</a></li>
    </ul>
</li>
```

### Paso 2: Reemplazar Valores

1. **`nombre_menu`:** Identificador único (ej: `'users'`, `'matches'`)
2. **Icono SVG:** Cambiar por el icono apropiado
3. **Título:** Cambiar "Título" por el nombre del menú
4. **Rutas:** Actualizar `route('ruta.index')` y `route('ruta.create')`
5. **Emojis:** Cambiar 📋 y ➕ por los que prefieras

---

## 🎯 Comportamiento

### **Desktop (> 1024px)**

**Sidebar Colapsado (100px):**
- ✅ Solo íconos visibles
- ❌ Submenús ocultos
- ❌ Flechas ocultas

**Sidebar Expandido (Hover 280px):**
- ✅ Títulos visibles
- ✅ Flechas visibles
- ✅ Submenús clickeables
- ✅ Animación de acordeón

### **Móvil (≤ 1024px)**

**Sidebar Abierto:**
- ✅ Siempre expandido (280px)
- ✅ Títulos y flechas visibles
- ✅ Submenús funcionan igual que en desktop expandido
- ✅ Auto-cierre al seleccionar un enlace

---

## 📱 Experiencia de Usuario

### **Flujo de Navegación:**

1. **Usuario pasa el mouse** sobre el sidebar (desktop)
2. **Sidebar se expande** a 280px
3. **Usuario ve los títulos** y flechas de submenús
4. **Usuario hace clic** en "Temporadas"
5. **Submenú se despliega** con animación suave
6. **Flecha rota 180°** para indicar estado abierto
7. **Usuario hace clic** en "➕ Crear Nueva"
8. **Navega a** `seasons.create`
9. **Sidebar se contrae** automáticamente al quitar el mouse

### **Estados Visuales:**

| Estado | Flecha | Submenú | Color |
|--------|--------|---------|-------|
| Cerrado | ↓ | Oculto | Normal |
| Abierto | ↑ | Visible | Activo |
| Hover Item | - | - | Highlight morado |
| Active Route | - | - | Highlight + Bold |

---

## 🐛 Troubleshooting

### Problema: Submenú no se despliega

**Posibles causas:**
1. Alpine.js no está cargado
2. `x-data` no está en el elemento padre
3. `toggle()` no está definido

**Solución:**
```blade
<!-- Verificar que el <ul> tenga x-data -->
<ul class="sidebar-links" x-data="{ openMenu: null, toggle(menu) {...} }">
```

### Problema: Animación entrecortada

**Causa:** Alpine.js `x-collapse` no está disponible

**Solución:**
Alpine.js v3 incluye `x-collapse` por defecto con Livewire 3. Si no funciona, verificar versión.

### Problema: Flecha no rota

**Causa:** Clases CSS no aplicadas

**Solución:**
```blade
<svg class="submenu-arrow" :class="{ 'rotate-180': openMenu === 'leagues' }">
```

Verificar que el CSS tenga:
```css
.rotate-180 { transform: rotate(180deg); }
```

---

## 🚀 Mejoras Futuras

### Posibles Features:

1. **Multi-nivel:** Submenús dentro de submenús
2. **Iconos personalizados:** Diferentes emojis por sección
3. **Badges:** Contadores en elementos (ej: "3 nuevas invitaciones")
4. **Búsqueda:** Input de búsqueda que filtra menús
5. **Favoritos:** Sistema de starred/pinned links
6. **Reordenar:** Drag & drop para personalizar orden
7. **Persistencia:** Guardar estado abierto/cerrado en localStorage
8. **Atajos de teclado:** Abrir menús con shortcuts

---

## 📝 Notas de Desarrollo

### **Alpine.js Directives Usadas:**

| Directive | Propósito |
|-----------|-----------|
| `x-data` | Definir estado reactivo |
| `@click` | Event listener para clicks |
| `x-show` | Mostrar/ocultar elemento |
| `x-collapse` | Animación de colapso suave |
| `:class` | Binding de clases dinámicas |

### **Performance:**

- **Solo un submenú abierto:** Evita sobrecarga visual
- **CSS transitions:** Animaciones con GPU acceleration
- **Lazy show:** `x-show` solo renderiza cuando es visible
- **No JavaScript pesado:** Solo Alpine.js (ya incluido con Livewire)

### **Accesibilidad:**

- ✅ Click handlers en enlaces válidos
- ✅ `href="javascript:void(0)"` para triggers
- ✅ Indicadores visuales claros
- ⚠️ **TODO:** Agregar ARIA attributes para screen readers

---

## 📚 Referencias

- [Alpine.js Docs](https://alpinejs.dev/)
- [Alpine.js x-collapse](https://alpinejs.dev/plugins/collapse)
- [Livewire 3 Alpine Integration](https://livewire.laravel.com/docs/alpine)
- [Laravel Named Routes](https://laravel.com/docs/routing#named-routes)

---

## 📊 Estadísticas

- **Archivos modificados:** 2
  - `resources/views/layouts/partials/sidebar-nav.blade.php`
  - `public/css/sidebar.css`
- **Líneas de código agregadas:** ~150
- **Secciones con submenús:** 4
- **Enlaces funcionales:** 11
- **Enlaces temporales:** 4
- **Tecnologías:** Alpine.js, CSS3, Laravel Blade

---

**Fecha de Implementación:** 2 de octubre de 2025  
**Autor:** GitHub Copilot + Usuario  
**Versión:** 1.0  
**Estado:** ✅ Completado y Funcional
