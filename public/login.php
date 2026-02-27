<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if (current_user()) {
    redirect('/dashboard.php');
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
        } elseif (!login($email, $password)) {
            $error = 'Credenciales incorrectas. Verifique su correo y clave.';
        } else {
            redirect('/dashboard.php');
        }
    }
}

$pageTitle = 'Acceso del Paciente';
include __DIR__ . '/../includes/header.php';
?>
<section class="auth-grid">
    <div class="card">
        <h2>Portal del Paciente</h2>
        <p class="muted" style="margin-bottom: 1.5rem;">Acceda de forma segura a su información de salud, citas y medicamentos.</p>
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
        <h3>Beneficios de nuestro portal</h3>
        <ul class="list" style="margin-top: 1rem;">
            <li>
                <strong>Citas:</strong>
                <span class="muted">Vea todas sus citas agendadas en un solo lugar.</span>
            </li>
            <li>
                <strong>Resultados:</strong>
                <span class="muted">Acceda a sus analisis clinicos digitalmente.</span>
            </li>
            <li>
                <strong>Medicamentos:</strong>
                <span class="muted">Recordatorios personalizados de su tratamiento.</span>
            </li>
        </ul>
        <a class="button ghost" href="/setup.php" style="margin-top: 1.5rem;">Crear cuenta demo</a>
        <a class="button ghost" href="/admin/login.php" style="margin-top: 0.75rem;">Acceso administrativo</a>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
