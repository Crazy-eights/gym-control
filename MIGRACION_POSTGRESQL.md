# 🐘 Guía de Migración a PostgreSQL

## ✅ Pasos Completados Automáticamente:
1. ✅ Actualizado `.env` con configuración PostgreSQL
   - DB_CONNECTION=pgsql
   - DB_PORT=5432
   - DB_USERNAME=postgres

## 📋 Pasos Manuales Requeridos:

### 1. Habilitar Extensiones PostgreSQL en PHP
Edita el archivo: `D:\xampp\php\php.ini`

Busca y descomenta (quita el `;`):
```ini
;extension=pgsql          → extension=pgsql
;extension=pdo_pgsql      → extension=pdo_pgsql
```

**Reinicia Apache/XAMPP después de hacer estos cambios**

### 2. Instalar PostgreSQL
Si no lo tienes instalado:
1. Descarga: https://www.postgresql.org/download/windows/
2. Durante instalación, establece contraseña para usuario `postgres`
3. Usa el puerto por defecto: `5432`
4. Anota la contraseña que establezcas

### 3. Actualizar contraseña en .env
Edita `.env` y agrega la contraseña de PostgreSQL:
```
DB_PASSWORD=tu_contraseña_aqui
```

### 4. Crear la Base de Datos
Abre **PostgreSQL SQL Shell (psql)** y ejecuta:
```sql
CREATE DATABASE gym_control;
\l  -- Para verificar que se creó
\q  -- Para salir
```

O desde PowerShell:
```powershell
& "C:\Program Files\PostgreSQL\XX\bin\psql.exe" -U postgres -c "CREATE DATABASE gym_control;"
```
(Reemplaza XX con tu versión de PostgreSQL)

### 5. Verificar Conexión
```powershell
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Conexión exitosa a PostgreSQL!';"
```

### 6. Ejecutar Migraciones
```powershell
php artisan migrate:fresh
```

## ⚠️ Notas Importantes:

### Diferencias entre MySQL y PostgreSQL:

1. **Campos ENUM**: PostgreSQL no soporta ENUM de MySQL. Las migraciones usan `string` en su lugar.
   - Validación se hace en el modelo/controlador

2. **Auto-increment**: PostgreSQL usa `SERIAL` automáticamente con `id()`

3. **Nombres de campos**: PostgreSQL es case-sensitive en nombres de tablas/campos

4. **Cadenas vacías vs NULL**: PostgreSQL distingue entre `''` y `NULL`

## 🔄 Exportar Datos desde MySQL (Opcional)

Si quieres migrar los datos existentes:

### Opción 1: Usando pgloader (Recomendado)
```bash
# Instalar pgloader
# Luego ejecutar:
pgloader mysql://root@localhost/gym_control postgresql://postgres:password@localhost/gym_control
```

### Opción 2: Manual con Seeders
1. Exporta datos de MySQL a SQL
2. Adapta el SQL para PostgreSQL
3. Importa en PostgreSQL

## 🧪 Verificación Final

Ejecuta estos comandos para verificar:

```powershell
# 1. Verificar extensión PHP
php -m | Select-String "pgsql"

# 2. Verificar conexión
php artisan tinker --execute="echo DB::connection()->getDatabaseName();"

# 3. Listar tablas
php artisan tinker --execute="print_r(DB::select('SELECT tablename FROM pg_tables WHERE schemaname = \\'public\\''));"

# 4. Limpiar caché
php artisan config:clear
php artisan cache:clear
```

## 🐛 Problemas Comunes:

### Error: "could not find driver"
- Solución: Asegúrate de habilitar `pdo_pgsql` en php.ini y reiniciar Apache

### Error: "Connection refused"
- Solución: Verifica que PostgreSQL esté corriendo en el puerto 5432

### Error: "authentication failed"
- Solución: Verifica usuario/contraseña en .env

### Error: "database does not exist"
- Solución: Crea la base de datos con `CREATE DATABASE gym_control;`

## 📞 ¿Necesitas Ayuda?

Si encuentras algún error durante la migración, proporciona:
1. El mensaje de error completo
2. El paso en el que estás
3. La salida del comando que falló
