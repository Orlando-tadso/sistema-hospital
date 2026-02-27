<?php
// Debug login - ELIMINAR después
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$config = require __DIR__ . '/../includes/config.php';
$dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset={$config['db_charset']}";
$pdo = new PDO($dsn, $config['db_user'], $config['db_pass']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

echo "<h2>Debug Login</h2>";

$email = 'medicos@gmail.com';
$password = 'medicos2026';

echo "<p>Buscando admin con email: <strong>$email</strong></p>";

$stmt = $pdo->prepare('SELECT id, full_name, email, password_hash FROM admins WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$admin = $stmt->fetch();

echo "<pre>";
echo "Admin encontrado:\n";
print_r($admin);
echo "</pre>";

if ($admin) {
    echo "<p>✓ Admin encontrado</p>";
    echo "<p>Password hash en BD: " . substr($admin['password_hash'], 0, 20) . "...</p>";
    
    $result = password_verify($password, $admin['password_hash']);
    echo "<p>password_verify result: " . ($result ? 'TRUE ✓' : 'FALSE ❌') . "</p>";
    
    if ($result) {
        echo "<p style='color: green;'><strong>✓ Login debería funcionar!</strong></p>";
        echo "<p>Intenta de nuevo en el login. Si no funciona, puede ser problema de sesión.</p>";
    } else {
        echo "<p style='color: red;'><strong>❌ Password verify falló</strong></p>";
    }
} else {
    echo "<p style='color: red;'>❌ No se encontró el admin</p>";
}

echo "<p><a href='/admin/login.php'>→ Ir al login</a></p>";
?>
