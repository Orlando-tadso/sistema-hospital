# Sistema Hospital

Sistema de gestión hospitalaria con panel de pacientes y administración.

## Despliegue en Railway

### Variables de Entorno Requeridas

Configura estas variables en Railway:

```
DB_HOST=tu-host-mysql.railway.app
DB_NAME=railway
DB_USER=root
DB_PASSWORD=tu-password-generado
```

### Base de Datos

1. Railway proveerá una base de datos MySQL automáticamente
2. Importa el archivo `schema.sql` en la base de datos de Railway
3. Ejecuta `public/setup_admin.php` para crear el usuario administrador inicial

### Configuración

El archivo `includes/config.php` está preparado para usar variables de entorno en producción y valores locales en desarrollo.

## Desarrollo Local (XAMPP)

1. Importa `schema.sql` en tu MySQL local
2. Configura la base de datos en `includes/config.php` (ya configurado para localhost)
3. Accede a través de `http://localhost/sistema_hospital/public/`

## Estructura

- `/public/` - Archivos públicos accesibles
- `/includes/` - Archivos de configuración y utilidades
- `schema.sql` - Esquema de base de datos
