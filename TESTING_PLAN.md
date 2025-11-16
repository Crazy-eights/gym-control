# 🧪 PLAN DE PRUEBAS - GYM CONTROL SYSTEM
## Verificación Post-Refactoring

### ✅ PRUEBAS CRÍTICAS A REALIZAR

---

## 1. 🏃‍♀️ **FUNCIONALIDAD DE CLASES DE GYM**
**Controller refactorizado: GymClassController**

### Pruebas a realizar:
- [ ] **Crear nueva clase**
  - Ir a `/admin/classes/create`
  - Llenar formulario completo
  - Verificar que se crea correctamente
  
- [ ] **Editar clase existente** 
  - Ir a `/admin/classes/{id}/edit`
  - Modificar datos básicos
  - Agregar/editar horarios
  - Verificar que se actualiza sin errores
  
- [ ] **Gestión de horarios**
  - Agregar múltiples horarios a una clase
  - Editar horarios existentes
  - Eliminar horarios
  - Verificar validaciones

---

## 2. 🔧 **HEALTH CHECK DE MICROSOFT**
**Nuevo endpoint: /microsoft-health-check**

### Pruebas a realizar:
- [ ] **Verificar endpoint**
  - Acceder a `/microsoft-health-check`
  - Comprobar respuesta JSON
  - Verificar diagnósticos de conexión

- [ ] **Fix Microsoft Token**
  - Acceder a `/fix-microsoft-token` 
  - Verificar que las constantes funcionan
  - Comprobar mensajes de estado

---

## 3. ♿ **ACCESIBILIDAD DE FORMULARIOS**
**Archivos modificados: edit.blade.php**

### Pruebas a realizar:
- [ ] **Validar asociación label-control**
  - Hacer clic en labels de formulario
  - Verificar que el cursor va al control correcto
  - Probar con Tab para navegación
  
- [ ] **Formularios dinámicos**
  - En edición de clases, agregar nuevo horario
  - Verificar que labels tienen IDs únicos
  - Probar funcionalidad de eliminar horario

---

## 4. 📧 **CONFIGURACIÓN DE EMAIL**
**Funcionalidad crítica del sistema**

### Pruebas a realizar:
- [ ] **Configuración SMTP**
  - Ir a configuración de email
  - Probar envío de email de prueba
  - Verificar logs de errores

- [ ] **OAuth Microsoft**
  - Configurar/reconectar Microsoft Graph
  - Probar renovación de tokens
  - Verificar estado de conexión

---

## 5. 🎯 **PRUEBAS GENERALES**

### Navegación básica:
- [ ] **Dashboard admin** (`/admin/dashboard`)
- [ ] **Portal socios** (`/portal/dashboard`) 
- [ ] **Login/logout** en ambos portales
- [ ] **Gestión de miembros** (`/admin/socios`)
- [ ] **Planes de membresía** (`/admin/membership-plans`)

### Verificar logs:
- [ ] **Sin errores PHP** en `storage/logs/laravel.log`
- [ ] **Funciones refactorizadas** operando correctamente
- [ ] **Base de datos** sin problemas de conexión

---

## 🚨 **PUNTOS CRÍTICOS A VERIFICAR**

### GymClassController (Refactorizado):
1. **Método update()** - Verificar que sigue actualizando clases
2. **Validaciones** - Comprobar que las reglas se aplican
3. **Horarios múltiples** - Crear/editar/eliminar horarios
4. **Transacciones DB** - No debe haber datos corruptos

### Microsoft Health:
1. **Constantes definidas** - No errores de constantes indefinidas
2. **Diagnósticos** - Información clara del estado
3. **Renovación de tokens** - Funcionalidad intacta

### Formularios:
1. **Navegación por teclado** - Tab entre controles
2. **Lectores de pantalla** - Labels leídos correctamente
3. **JavaScript dinámico** - Agregar/quitar elementos funciona

---

## 📊 **COMANDOS DE VERIFICACIÓN**

```bash
# Verificar que no hay errores de sintaxis PHP
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Verificar logs recientes
tail -f storage/logs/laravel.log

# Verificar rutas funcionando
php artisan route:list | grep -E "(classes|microsoft|health)"
```

---

## ❌ **QUÉ HACER SI HAY PROBLEMAS**

1. **Revisar logs** de errores inmediatamente
2. **Verificar sintaxis PHP** en archivos modificados
3. **Comprobar rutas** y controladores
4. **Validar base de datos** sin corrupción
5. **Reportar errores específicos** para corrección rápida

---

## ✅ **CRITERIOS DE ÉXITO**

- ✅ Todas las funcionalidades principales operativas
- ✅ Sin errores PHP en logs
- ✅ Formularios accesibles y funcionales
- ✅ Health checks de Microsoft operativos
- ✅ Navegación web fluida
- ✅ CRUD operations funcionando

---

**🎯 PRIORIDAD: Probar primero GymClassController y formularios de clases**