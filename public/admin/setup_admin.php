<?php
require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/admin_auth.php';

$db = get_db();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $message = 'La solicitud no es valida.';
    } else {
        $email = 'medicos@gmail.com';
        $password = 'medicos2026';
        $fullName = 'Administrador Hospital';

        $stmt = $db->prepare('SELECT id FROM admins WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $existing = $stmt->fetch();

        if (!$existing) {
            $insert = $db->prepare(
                'INSERT INTO admins (full_name, email, password_hash, created_at) VALUES (?, ?, ?, NOW())'
            );
            $insert->execute([
                $fullName,
                $email,
                password_hash($password, PASSWORD_DEFAULT),
            ]);
        }

        $message = 'Administrador configurado: ' . $email . ' / ' . $password . '.';
    }
}

$pageTitle = 'Configurar administrador';
$showAdminNav = false;
include __DIR__ . '/../../includes/admin_header.php';
?>
<div class="card">
    <h2>Configurar administrador</h2>
    <p class="muted">Use este asistente una sola vez y luego elimine este archivo.</p>
    <?php if ($message): ?>
        <p class="alert success"><?php echo h($message); ?></p>
    <?php endif; ?>
    <form method="post">
        <?php echo csrf_field(); ?>
        <button class="button" type="submit">Crear admin</button>
    </form>
</div>
<?php include __DIR__ . '/../../includes/admin_footer.php'; ?>
