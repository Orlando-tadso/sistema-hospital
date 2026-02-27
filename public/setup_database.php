<?php
// Script de inicialización de base de datos para Railway
// Ejecutar una sola vez: https://tu-dominio.up.railway.app/setup_database.php
// NOTA: Este script NO requiere autenticación

// NO incluir bootstrap.php para evitar redirecciones de autenticación
$config = require __DIR__ . '/../includes/config.php';

try {
    // Conectar sin seleccionar base de datos específica
    $dsn = "mysql:host={$config['db_host']};charset={$config['db_charset']}";
    $pdo = new PDO($dsn, $config['db_user'], $config['db_pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Configurando Base de Datos...</h2>";
    
    // Leer el schema.sql
    $schema = file_get_contents(__DIR__ . '/../schema.sql');
    
    // Remover el CREATE DATABASE y USE porque la BD ya existe en Railway
    $schema = preg_replace('/CREATE DATABASE.*?;/i', '', $schema);
    $schema = preg_replace('/USE.*?;/i', '', $schema);
    
    // Conectar a la base de datos específica
    $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset={$config['db_charset']}";
    $pdo = new PDO($dsn, $config['db_user'], $config['db_pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Ejecutar el schema
    $pdo->exec($schema);
    
    echo "<p style='color: green;'>✓ Tablas creadas correctamente</p>";
    
    // Verificar tablas creadas
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Tablas creadas:</h3><ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";
    
    echo "<h3>✅ Base de datos configurada correctamente</h3>";
    echo "<p><strong>IMPORTANTE:</strong> Por seguridad, elimina este archivo después de ejecutarlo.</p>";
    echo "<p><a href='/admin/setup_admin.php'>→ Ir a crear usuario administrador</a></p>";
    echo "<p><a href='/login.php'>→ Ir al login</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>Error:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Verifica que las variables de entorno DB_HOST, DB_NAME, DB_USER y DB_PASSWORD estén configuradas correctamente en Railway.</p>";
}
?>
