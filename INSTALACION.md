# 🏋️ Gym Control - Guía de Instalación

Esta guía te ayudará a instalar y configurar el sistema Gym Control en un nuevo entorno.

## 📋 Requisitos Previos

- PHP 8.0 o superior
- Composer
- MySQL 5.7 o superior / MariaDB 10.3 o superior
- Node.js y NPM (para assets)
- Servidor web (Apache/Nginx)

## 🚀 Instalación Rápida

### 1. Clonar o copiar el proyecto
```bash
# Si es desde repositorio
git clone [url-del-repositorio] gym-control
cd gym-control

# Si es desde archivos
# Copia todos los archivos a tu directorio de proyecto
```

### 2. Instalar dependencias
```bash
# Dependencias de PHP
composer install

# Dependencias de Node.js (opcional, para desarrollo)
npm install
```

### 3. Configurar archivo de entorno
```bash
# Copiar archivo de ejemplo
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

### 4. Configurar base de datos
Edita el archivo `.env` con tus datos de base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gym_control
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

### 5. Configurar otros parámetros en .env
```env
APP_NAME="Gym Control"
APP_URL=http://localhost/gym-control

# Configuración de email (opcional, se puede configurar después)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=tu_contraseña_app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tugimnasio.com
MAIL_FROM_NAME="Tu Gimnasio"

# Configuración Microsoft OAuth (opcional)
MS_CLIENT_ID=tu_client_id
MS_CLIENT_SECRET=tu_client_secret
MS_REDIRECT_URI="${APP_URL}/admin/mail-config/oauth/microsoft/callback"
```

### 6. Inicializar el sistema
```bash
# Inicialización completa del sistema
php artisan gym:initialize

# O con credenciales personalizadas
php artisan gym:initialize --admin-email=admin@tugimnasio.com --admin-password=tu_contraseña_segura

# O inicialización desde cero (elimina datos existentes)
php artisan gym:initialize --fresh --admin-email=admin@tugimnasio.com --admin-password=tu_contraseña_segura
```

## 🎯 ¡Listo para usar!

Después de ejecutar el comando de inicialización, tendrás:

- ✅ Base de datos creada con todas las tablas
- ✅ Usuario administrador configurado
- ✅ Planes de membresía básicos
- ✅ Configuraciones del sistema
- ✅ Configuración básica de email

### Acceso al sistema:
- **URL Admin**: `http://tu-dominio.com/admin/login`
- **Email**: El que configuraste (por defecto: admin@gymcontrol.com)
- **Contraseña**: La que configuraste (por defecto: admin123)

### Acceso al Portal de Socios:
- **URL**: `http://tu-dominio.com/`
- Los socios pueden acceder con su email y contraseña una vez registrados

## ⚙️ Configuración Adicional

### Configurar Email (Microsoft OAuth - Recomendado)
1. Ve a **Configuración → Email** en el panel admin
2. Selecciona "Microsoft OAuth"
3. Configura las credenciales de Microsoft
4. Conecta tu cuenta de Microsoft

### Configurar Información del Gimnasio
1. Ve a **Configuración → General** en el panel admin
2. Actualiza la información de tu gimnasio
3. Configura horarios, teléfonos, etc.

### Crear Planes de Membresía Personalizados
1. Ve a **Planes de Membresía** en el panel admin
2. Edita los planes existentes o crea nuevos
3. Configura precios y duraciones según tu negocio

## 🛠️ Comandos Útiles

```bash
# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Ejecutar migraciones
php artisan migrate

# Reinicializar sistema (¡CUIDADO! Elimina todos los datos)
php artisan gym:initialize --fresh

# Ver estado de migraciones
php artisan migrate:status

# Crear link simbólico para storage (si es necesario)
php artisan storage:link
```

## 🔒 Seguridad

### Recomendaciones de Producción:
1. Cambia las credenciales por defecto
2. Usa contraseñas fuertes
3. Configura HTTPS
4. Actualiza regularmente las dependencias
5. Mantén actualizado PHP y MySQL

### Variables de entorno críticas:
```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:tu_clave_generada_aleatoriamente
```

## 🆘 Solución de Problemas

### Error de permisos:
```bash
# En Linux/Mac
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# En Windows con XAMPP normalmente no es necesario
```

### Error de base de datos:
- Verifica que el servicio MySQL esté ejecutándose
- Confirma que las credenciales en `.env` sean correctas
- Asegúrate de que la base de datos exista

### Error de dependencias:
```bash
# Reinstalar dependencias
composer install --no-dev --optimize-autoloader
```

## 📞 Soporte

Para soporte adicional o reportar problemas:
- Revisa la documentación completa
- Verifica los logs en `storage/logs/laravel.log`
- Contacta al desarrollador del sistema

---

**¡Tu sistema Gym Control está listo para transformar la gestión de tu gimnasio! 💪**