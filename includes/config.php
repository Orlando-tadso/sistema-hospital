<?php
// Configuración que funciona tanto en local (XAMPP) como en Railway
return [
    'db_host' => getenv('DB_HOST') ?: 'localhost',
    'db_name' => getenv('DB_NAME') ?: 'sistema_hospital',
    'db_user' => getenv('DB_USER') ?: 'root',
    'db_pass' => getenv('DB_PASSWORD') ?: '',
    'db_charset' => 'utf8mb4',
];
