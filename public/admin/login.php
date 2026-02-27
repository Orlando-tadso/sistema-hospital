<?php
require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/admin_auth.php';

if (current_admin()) {
    redirect('/admin/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'La solicitud no es valida. Intente nuevamente.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (!$email || !$password) {
            $error = 'Complete todos los campos.';
        } elseif (!admin_login($email, $password)) {
            $error = 'Credenciales incorrectas.';
        } else {
            redirect('/admin/index.php');
        }
    }
}

$pageTitle = 'Acceso administrativo';
$showAdminNav = false;
include __DIR__ . '/../../includes/admin_header.php';
?>
<section class="auth-grid">
    <div class="card">
        <h2>Acceso administrativo</h2>
        <p class="muted" style="margin-bottom: 1.5rem;">Portal exclusivo para personal autorizado del Hospital San Nicolas de Tolentino.</p>
        <form method="post" class="form">
            <?php echo csrf_field(); ?>
            <label>
                Correo electronico
                <input type="email" name="email" required>
            </label>
            <label>
                Clave
                <input type="password" name="password" required>
            </label>
            <?php if ($error): ?>
                <p class="alert"><?php echo h($error); ?></p>
            <?php endif; ?>
            <button type="submit" class="button">Ingresar</button>
        </form>
    </div>
    <div class="card highlight">
        <h3>Información importante</h3>
        <ul class="list" style="margin-top: 1rem;">
            <li>
                <strong>Confidencialidad:</strong>
                <span class="muted">No comparta sus credenciales de acceso.</span>
            </li>
            <li>
                <strong>Sesión activa:</strong>
                <span class="muted">Cierre sesión al terminar su jornada.</span>
            </li>
            <li>
                <strong>Seguridad:</strong>
                <span class="muted">Use claves seguras. Cambie la clave regularmente.</span>
            </li>
        </ul>
        <a class="button ghost" href="/admin/setup_admin.php" style="margin-top: 1.5rem;">Configurar administrador</a>
    </div>
</section>
<?php include __DIR__ . '/../../includes/admin_footer.php'; ?>
