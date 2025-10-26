# 🏋️ Gym Control System

Sistema integral de gestión para gimnasios con portal de socios, gestión administrativa y sistema de membresías.

## 🚀 Características Principales

### 👥 Portal de Socios
- **Dashboard personalizado** con resumen de membresía
- **Información de membresía** con historial de pagos
- **Perfil de socio** con configuración de datos personales
- **Sistema de clases** con reservas y horarios
- **Rutinas de ejercicio** personalizadas
- **Configuración** de cuenta y preferencias

### 🔧 Panel Administrativo
- **Dashboard ejecutivo** con métricas clave
- **Gestión de socios** completa
- **Administración de membresías** y planes
- **Sistema de notificaciones** automatizado
- **Configuración de email** con Microsoft OAuth
- **Reportes y estadísticas**

### 🔐 Sistema de Autenticación
- **Login unificado** para socios y administradores
- **Recuperación de contraseña** automática
- **Gestión de sesiones** segura
- **Autenticación dual** (Member/Admin)

## 🛠️ Tecnologías

- **Backend**: Laravel 8.x
- **Frontend**: Bootstrap 5, Blade Templates
- **Base de datos**: MySQL/MariaDB
- **Email**: Microsoft Graph API + SMTP fallback
- **Autenticación**: Laravel Auth + Custom Guards

## 📦 Instalación Rápida

### Opción 1: Script Automatizado (Recomendado)

#### Windows (PowerShell):
```powershell
# Ejecutar como Administrador
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
.\install.ps1
```

#### Linux/Mac:
```bash
chmod +x install.sh
./install.sh
```

### Opción 2: Instalación Manual

```bash
# 1. Instalar dependencias
composer install --no-dev --optimize-autoloader

# 2. Configurar entorno
cp .env.example .env
php artisan key:generate

# 3. Configurar base de datos en .env
# DB_DATABASE=gym_control
# DB_USERNAME=tu_usuario
# DB_PASSWORD=tu_contraseña

# 4. Inicializar sistema
php artisan gym:initialize

# O con credenciales personalizadas
php artisan gym:initialize --admin-email=admin@tugimnasio.com --admin-password=tu_contraseña
```

## 🎯 Acceso al Sistema

### Panel Administrativo
- **URL**: `http://tu-dominio.com/admin/login`
- **Email por defecto**: `admin@gymcontrol.com`
- **Contraseña por defecto**: `admin123`

### Portal de Socios
- **URL**: `http://tu-dominio.com/`
- Los socios acceden con su email y contraseña

## 🔧 Configuración Post-Instalación

### 1. Configurar Email
- Ve a **Configuración → Email** en el panel admin
- Configura Microsoft OAuth o SMTP
- Prueba el envío de emails

### 2. Personalizar Gimnasio
- Actualiza la información en **Configuración → General**
- Configura horarios, teléfonos y dirección
- Personaliza planes de membresía

### 3. Gestión de Usuarios
- Crea socios desde el panel admin
- Configura planes de membresía
- Establece precios y duraciones

## 📚 Documentación Completa

Para instalación detallada y configuración avanzada, consulta:
- 📖 [Guía de Instalación Completa](INSTALACION.md)

## 📚 Documentación Completa

Para instalación detallada y configuración avanzada, consulta:
- 📖 [Guía de Instalación Completa](INSTALACION.md)

## 🔒 Seguridad

### Configuración de Producción
```env
APP_ENV=production
APP_DEBUG=false
```

### Recomendaciones
- Cambia las credenciales por defecto
- Usa contraseñas fuertes
- Configura HTTPS
- Mantén actualizado el sistema

## 🛠️ Comandos Útiles

```bash
# Inicializar sistema desde cero
php artisan gym:initialize --fresh

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Estado de migraciones
php artisan migrate:status

# Crear link simbólico para storage
php artisan storage:link
```

## 📱 Características del Sistema

### Dashboard Administrativo
- Métricas de socios activos/inactivos
- Resumen de ingresos mensual
- Próximos vencimientos de membresías
- Estadísticas de asistencia

### Portal de Socios
- **Dashboard**: Estado de membresía, próximos vencimientos
- **Membresía**: Historial de pagos, opciones de renovación
- **Perfil**: Datos personales, cambio de contraseña
- **Clases**: Horarios, reservas, historial
- **Rutinas**: Ejercicios personalizados, seguimiento
- **Configuración**: Preferencias de cuenta

### Sistema de Email
- Recuperación de contraseña automática
- Notificaciones de vencimiento
- Microsoft Graph API integrado
- Fallback SMTP configurable

## 🎨 Interfaz de Usuario

- **Responsive Design**: Compatible con móviles y tablets
- **Bootstrap 5**: UI moderna y consistente
- **Dark/Light Theme**: Adaptable a preferencias
- **Iconografía**: FontAwesome para claridad visual

## 🔄 Flujo de Trabajo

### Para Administradores:
1. Login en panel admin
2. Gestión de socios y membresías
3. Configuración del sistema
4. Monitoreo de métricas

### Para Socios:
1. Login en portal
2. Consulta de estado de membresía
3. Gestión de perfil personal
4. Reserva de clases y seguimiento

## 📞 Soporte

- Revisa los logs en `storage/logs/laravel.log`
- Consulta la documentación completa
- Verifica configuración de `.env`

---

**🏋️ ¡Transforma la gestión de tu gimnasio con Gym Control System! 💪**

*Sistema desarrollado con Laravel - Diseñado para la eficiencia y facilidad de uso*

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 1500 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
