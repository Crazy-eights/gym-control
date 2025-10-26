# 🏋️ Script de Instalación Automatizada - Gym Control (Windows PowerShell)
# Este script automatiza la instalación completa del sistema en Windows

Write-Host "🏋️  Gym Control - Instalación Automatizada (Windows)" -ForegroundColor Green
Write-Host "====================================================" -ForegroundColor Green
Write-Host ""

# Verificar si PHP está instalado
try {
    $phpVersion = php -v 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ PHP detectado" -ForegroundColor Green
    } else {
        throw "PHP no encontrado"
    }
} catch {
    Write-Host "❌ Error: PHP no está instalado o no está en el PATH" -ForegroundColor Red
    Write-Host "Instala PHP y asegúrate de que esté en el PATH del sistema" -ForegroundColor Yellow
    Read-Host "Presiona Enter para continuar..."
    exit 1
}

# Verificar si Composer está instalado
try {
    $composerVersion = composer --version 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Composer detectado" -ForegroundColor Green
    } else {
        throw "Composer no encontrado"
    }
} catch {
    Write-Host "❌ Error: Composer no está instalado o no está en el PATH" -ForegroundColor Red
    Write-Host "Descarga e instala Composer desde https://getcomposer.org/" -ForegroundColor Yellow
    Read-Host "Presiona Enter para continuar..."
    exit 1
}

Write-Host ""

# Instalar dependencias
Write-Host "📦 Instalando dependencias de PHP..." -ForegroundColor Yellow
composer install --no-dev --optimize-autoloader

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Error al instalar dependencias de PHP" -ForegroundColor Red
    Read-Host "Presiona Enter para continuar..."
    exit 1
}

Write-Host "✅ Dependencias de PHP instaladas" -ForegroundColor Green
Write-Host ""

# Crear archivo .env si no existe
if (!(Test-Path .env)) {
    Write-Host "📄 Creando archivo de configuración .env..." -ForegroundColor Yellow
    
    if (Test-Path .env.example) {
        Copy-Item .env.example .env
        Write-Host "✅ Archivo .env creado desde .env.example" -ForegroundColor Green
    } else {
        Write-Host "⚠️  Advertencia: .env.example no encontrado, creando .env básico" -ForegroundColor Yellow
        
        $envContent = @"
APP_NAME="Gym Control"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gym_control
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DRIVER=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME=`"Gym Control`"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY="`${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="`${PUSHER_APP_CLUSTER}"
"@
        
        $envContent | Out-File -FilePath .env -Encoding UTF8
    }
} else {
    Write-Host "✅ Archivo .env ya existe" -ForegroundColor Green
}
Write-Host ""

# Generar clave de aplicación
Write-Host "🔑 Generando clave de aplicación..." -ForegroundColor Yellow
php artisan key:generate
Write-Host "✅ Clave de aplicación generada" -ForegroundColor Green
Write-Host ""

# Configurar base de datos
Write-Host "🗄️  Configuración de Base de Datos" -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Por favor, configura los datos de tu base de datos:" -ForegroundColor White
Write-Host ""

$db_host = Read-Host "Host de la base de datos [127.0.0.1]"
if ([string]::IsNullOrEmpty($db_host)) { $db_host = "127.0.0.1" }

$db_port = Read-Host "Puerto de la base de datos [3306]"
if ([string]::IsNullOrEmpty($db_port)) { $db_port = "3306" }

$db_name = Read-Host "Nombre de la base de datos [gym_control]"
if ([string]::IsNullOrEmpty($db_name)) { $db_name = "gym_control" }

$db_user = Read-Host "Usuario de la base de datos [root]"
if ([string]::IsNullOrEmpty($db_user)) { $db_user = "root" }

$db_password = Read-Host "Contraseña de la base de datos" -AsSecureString
$db_password = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($db_password))

# Actualizar archivo .env con datos de la base de datos
$envContent = Get-Content .env
$envContent = $envContent -replace "DB_HOST=.*", "DB_HOST=$db_host"
$envContent = $envContent -replace "DB_PORT=.*", "DB_PORT=$db_port"
$envContent = $envContent -replace "DB_DATABASE=.*", "DB_DATABASE=$db_name"
$envContent = $envContent -replace "DB_USERNAME=.*", "DB_USERNAME=$db_user"
$envContent = $envContent -replace "DB_PASSWORD=.*", "DB_PASSWORD=$db_password"
$envContent | Set-Content .env

Write-Host ""
Write-Host "✅ Configuración de base de datos actualizada" -ForegroundColor Green
Write-Host ""

# Configurar credenciales de administrador
Write-Host "👤 Configuración del Administrador" -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor Cyan
Write-Host ""

$admin_email = Read-Host "Email del administrador [admin@gymcontrol.com]"
if ([string]::IsNullOrEmpty($admin_email)) { $admin_email = "admin@gymcontrol.com" }

$admin_password = Read-Host "Contraseña del administrador [admin123]" -AsSecureString
$admin_password_plain = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($admin_password))
if ([string]::IsNullOrEmpty($admin_password_plain)) { $admin_password_plain = "admin123" }

Write-Host ""

# Preguntar si hacer fresh migration
$fresh_install = Read-Host "⚠️  ¿Deseas hacer una instalación limpia (eliminar datos existentes)? [y/N]"

# Ejecutar inicialización
Write-Host ""
Write-Host "🚀 Iniciando configuración del sistema..." -ForegroundColor Yellow
Write-Host ""

if ($fresh_install -match "^[Yy]$") {
    php artisan gym:initialize --fresh --admin-email="$admin_email" --admin-password="$admin_password_plain"
} else {
    php artisan gym:initialize --admin-email="$admin_email" --admin-password="$admin_password_plain"
}

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "🎉 ¡Instalación completada exitosamente!" -ForegroundColor Green
    Write-Host ""
    Write-Host "📋 Información de acceso:" -ForegroundColor Cyan
    Write-Host "========================" -ForegroundColor Cyan
    Write-Host "URL Admin: http://localhost/admin/login" -ForegroundColor White
    Write-Host "Email: $admin_email" -ForegroundColor White
    Write-Host "Contraseña: $admin_password_plain" -ForegroundColor White
    Write-Host ""
    Write-Host "🔧 Próximos pasos:" -ForegroundColor Cyan
    Write-Host "1. Configura tu servidor web (XAMPP/WAMP) para apuntar al directorio 'public'" -ForegroundColor White
    Write-Host "2. Configura el email en el panel de administración" -ForegroundColor White
    Write-Host "3. Personaliza la información de tu gimnasio" -ForegroundColor White
    Write-Host "4. ¡Comienza a gestionar tu gimnasio!" -ForegroundColor White
    Write-Host ""
} else {
    Write-Host ""
    Write-Host "❌ Error durante la instalación" -ForegroundColor Red
    Write-Host "Revisa los mensajes de error anteriores" -ForegroundColor Yellow
}

Read-Host "Presiona Enter para continuar..."