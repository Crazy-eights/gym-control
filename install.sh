#!/bin/bash

# 🏋️ Script de Instalación Automatizada - Gym Control
# Este script automatiza la instalación completa del sistema

echo "🏋️  Gym Control - Instalación Automatizada"
echo "=========================================="
echo ""

# Verificar si PHP está instalado
if ! command -v php &> /dev/null; then
    echo "❌ Error: PHP no está instalado o no está en el PATH"
    exit 1
fi

# Verificar si Composer está instalado
if ! command -v composer &> /dev/null; then
    echo "❌ Error: Composer no está instalado o no está en el PATH"
    exit 1
fi

echo "✅ PHP y Composer detectados"
echo ""

# Instalar dependencias
echo "📦 Instalando dependencias de PHP..."
composer install --no-dev --optimize-autoloader

if [ $? -ne 0 ]; then
    echo "❌ Error al instalar dependencias de PHP"
    exit 1
fi

echo "✅ Dependencias de PHP instaladas"
echo ""

# Crear archivo .env si no existe
if [ ! -f .env ]; then
    echo "📄 Creando archivo de configuración .env..."
    
    if [ -f .env.example ]; then
        cp .env.example .env
        echo "✅ Archivo .env creado desde .env.example"
    else
        echo "⚠️  Advertencia: .env.example no encontrado, creando .env básico"
        cat > .env << EOL
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
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
EOL
    fi
else
    echo "✅ Archivo .env ya existe"
fi
echo ""

# Generar clave de aplicación
echo "🔑 Generando clave de aplicación..."
php artisan key:generate
echo "✅ Clave de aplicación generada"
echo ""

# Configurar base de datos
echo "🗄️  Configuración de Base de Datos"
echo "=================================="
echo ""
echo "Por favor, configura los datos de tu base de datos:"
echo ""

read -p "Host de la base de datos [127.0.0.1]: " db_host
db_host=${db_host:-127.0.0.1}

read -p "Puerto de la base de datos [3306]: " db_port
db_port=${db_port:-3306}

read -p "Nombre de la base de datos [gym_control]: " db_name
db_name=${db_name:-gym_control}

read -p "Usuario de la base de datos [root]: " db_user
db_user=${db_user:-root}

read -s -p "Contraseña de la base de datos: " db_password
echo ""

# Actualizar archivo .env con datos de la base de datos
sed -i "s/DB_HOST=.*/DB_HOST=$db_host/" .env
sed -i "s/DB_PORT=.*/DB_PORT=$db_port/" .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$db_name/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$db_user/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$db_password/" .env

echo ""
echo "✅ Configuración de base de datos actualizada"
echo ""

# Configurar credenciales de administrador
echo "👤 Configuración del Administrador"
echo "=================================="
echo ""

read -p "Email del administrador [admin@gymcontrol.com]: " admin_email
admin_email=${admin_email:-admin@gymcontrol.com}

read -s -p "Contraseña del administrador [admin123]: " admin_password
admin_password=${admin_password:-admin123}
echo ""
echo ""

# Preguntar si hacer fresh migration
echo "⚠️  ¿Deseas hacer una instalación limpia (eliminar datos existentes)? [y/N]: "
read -r fresh_install

# Ejecutar inicialización
echo "🚀 Iniciando configuración del sistema..."
echo ""

if [[ $fresh_install =~ ^[Yy]$ ]]; then
    php artisan gym:initialize --fresh --admin-email="$admin_email" --admin-password="$admin_password"
else
    php artisan gym:initialize --admin-email="$admin_email" --admin-password="$admin_password"
fi

if [ $? -eq 0 ]; then
    echo ""
    echo "🎉 ¡Instalación completada exitosamente!"
    echo ""
    echo "📋 Información de acceso:"
    echo "========================"
    echo "URL Admin: http://localhost/admin/login"
    echo "Email: $admin_email"
    echo "Contraseña: $admin_password"
    echo ""
    echo "🔧 Próximos pasos:"
    echo "1. Configura tu servidor web para apuntar al directorio 'public'"
    echo "2. Configura el email en el panel de administración"
    echo "3. Personaliza la información de tu gimnasio"
    echo "4. ¡Comienza a gestionar tu gimnasio!"
    echo ""
else
    echo ""
    echo "❌ Error durante la instalación"
    echo "Revisa los mensajes de error anteriores"
    exit 1
fi