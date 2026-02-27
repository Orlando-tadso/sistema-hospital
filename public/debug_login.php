<?php
// Debug login - ELIMINAR después
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/admin_auth.php';

echo "<h2>Debug Login Completo</h2>";

$email = 'medicos@gmail.com';
$password = 'medicos2026';

echo "<p>Test 1: Usando get_db() como en admin_login():</p>";

$db = get_db();
$stmt = $db->prepare('SELECT id, full_name, email, password_hash FROM admins WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$admin = $stmt->fetch();

echo "<pre>";
echo "Admin encontrado:\n";
var_dump($admin);
echo "</pre>";

if ($admin) {
    echo "<p>✓ Admin encontrado</p>";
    echo "<p>ID: {$admin['id']}</p>";
    echo "<p>Email: {$admin['email']}</p>";
    echo "<p>Password hash: " . substr($admin['password_hash'], 0, 30) . "...</p>";
    
    $verifyResult = password_verify($password, $admin['password_hash']);
    echo "<p>password_verify('medicos2026', hash): <strong>" . ($verifyResult ? 'TRUE ✓' : 'FALSE ❌') . "</strong></p>";
    
    if (!$admin || !password_verify($password, $admin['password_hash'])) {
        echo "<p style='color: red;'>❌ La condición de admin_login() devolvería FALSE</p>";
    } else {
        echo "<p style='color: green;'>✓ La condición de admin_login() devolvería TRUE</p>";
    }
} else {
    echo "<p style='color: red;'>❌ fetch() devolvió FALSE o NULL</p>";
}

echo "<hr><p>Test 2: Invocar admin_login() directamente:</p>";

$loginResult = admin_login($email, $password);
echo "<p>admin_login() result: <strong>" . ($loginResult ? 'TRUE ✓' : 'FALSE ❌') . "</strong></p>";

if ($loginResult) {
    $currentAdmin = current_admin();
    echo "<p style='color: green;'>✓ Login exitoso. Admin en sesión:</p>";
    echo "<pre>";
    print_r($currentAdmin);
    echo "</pre>";
} else {
    echo "<p style='color: red;'>❌ admin_login() falló</p>";
}

echo "<p><a href='/admin/login.php'>→ Ir al login</a></p>";
echo "<p><strong>⚠️ ELIMINA este archivo después</strong></p>";
?>
