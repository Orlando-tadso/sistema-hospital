<?php
// Script temporal de debug - ELIMINAR después de verificar
$config = require __DIR__ . '/../includes/config.php';

try {
    $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset={$config['db_charset']}";
    $pdo = new PDO($dsn, $config['db_user'], $config['db_pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Verificación de Admin</h2>";
    
    $stmt = $pdo->query("SELECT id, full_name, email FROM admins");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($admins)) {
        echo "<p style='color: red;'>❌ No hay administradores en la base de datos.</p>";
        echo "<p><a href='/admin/setup_admin.php'>→ Crear administrador</a></p>";
    } else {
        echo "<p style='color: green;'>✓ Administradores encontrados:</p>";
        echo "<ul>";
        foreach ($admins as $admin) {
            echo "<li>ID: {$admin['id']} - {$admin['full_name']} ({$admin['email']})</li>";
        }
        echo "</ul>";
        
        echo "<h3>Credenciales esperadas:</h3>";
        echo "<p>Email: <strong>medicos@gmail.com</strong></p>";
        echo "<p>Password: <strong>medicos2026</strong></p>";
        
        // Verificar si existe el admin esperado
        $stmt = $pdo->prepare("SELECT id, password_hash FROM admins WHERE email = ?");
        $stmt->execute(['medicos@gmail.com']);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin) {
            echo "<p style='color: green;'>✓ Admin con email medicos@gmail.com existe</p>";
            
            // Verificar password
            $passwordCorrect = password_verify('medicos2026', $admin['password_hash']);
            
            if ($passwordCorrect) {
                echo "<p style='color: green;'>✓ La contraseña 'medicos2026' es correcta</p>";
            } else {
                echo "<p style='color: red;'>❌ La contraseña 'medicos2026' NO coincide con el hash almacenado</p>";
                echo "<p>Necesitas volver a crear el admin.</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ No existe admin con email medicos@gmail.com</p>";
        }
    }
    
    echo "<p><a href='/admin/login.php'>→ Ir al login</a></p>";
    echo "<p><strong>⚠️ ELIMINA este archivo después de verificar</strong></p>";
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>Error:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
