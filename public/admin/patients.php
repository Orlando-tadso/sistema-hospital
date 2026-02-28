<?php
require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/admin_auth.php';
require_admin();

$db = get_db();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'La solicitud no es valida.';
    } else {
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $dob = $_POST['dob'] ?? null;
        $phone = trim($_POST['phone'] ?? '');

        if (!$fullName || !$email || !$password) {
            $error = 'Complete nombre, correo y clave.';
        } else {
            $check = $db->prepare('SELECT id FROM patients WHERE email = ? LIMIT 1');
            $check->execute([$email]);
            if ($check->fetch()) {
                $error = 'El correo ya existe.';
            } else {
                $insert = $db->prepare(
                    'INSERT INTO patients (full_name, email, password_hash, dob, phone, created_at) VALUES (?, ?, ?, ?, ?, NOW())'
                );
                $insert->execute([
                    $fullName,
                    $email,
                    password_hash($password, PASSWORD_DEFAULT),
                    $dob ?: null,
                    $phone ?: null,
                ]);
                $message = 'Paciente creado correctamente.';
            }
        }
    }
}

$patients = $db->query('SELECT id, full_name, email, dob, phone, created_at FROM patients ORDER BY created_at DESC LIMIT 200')->fetchAll();

$pageTitle = 'Pacientes';
$activePage = 'admin-patients';
include __DIR__ . '/../../includes/admin_header.php';
?>
<div class="page-head">
    <div>
        <h2>Pacientes</h2>
        <p class="muted">Administre altas y datos principales.</p>
    </div>
</div>

<div class="card">
    <h3>Registrar nuevo paciente</h3>
    <?php if ($message): ?>
        <p class="alert success"><?php echo h($message); ?></p>
    <?php elseif ($error): ?>
        <p class="alert"><?php echo h($error); ?></p>
    <?php endif; ?>
    <form method="post" class="form">
        <?php echo csrf_field(); ?>
        <div class="form-grid">
            <label>
                Nombre completo
                <input type="text" name="full_name" required>
            </label>
            <label>
                Correo electronico
                <input type="email" name="email" required>
            </label>
            <label>
                Clave temporal
                <input type="text" name="password" required>
            </label>
            <label>
                Fecha de nacimiento
                <input type="date" name="dob">
            </label>
            <label>
                Telefono
                <input type="text" name="phone">
            </label>
        </div>
        <button class="button" type="submit">Crear paciente</button>
    </form>
</div>

<div class="card">
    <h3>Listado reciente</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Fecha nacimiento</th>
                <th>Telefono</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($patients): ?>
                <?php foreach ($patients as $patient): ?>
                    <tr>
                        <td><?php echo h($patient['full_name']); ?></td>
                        <td><?php echo h($patient['email']); ?></td>
                        <td><?php echo h(format_date($patient['dob'])); ?></td>
                        <td><?php echo h($patient['phone']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="muted">No hay pacientes registrados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../../includes/admin_footer.php'; ?>
