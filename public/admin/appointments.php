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
        $patientId = (int) ($_POST['patient_id'] ?? 0);
        $appointmentDate = $_POST['appointment_date'] ?? '';
        $department = trim($_POST['department'] ?? '');
        $doctor = trim($_POST['doctor'] ?? '');
        $status = trim($_POST['status'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        if (!$patientId || !$appointmentDate || !$department || !$doctor || !$status) {
            $error = 'Complete todos los campos obligatorios.';
        } else {
            $insert = $db->prepare(
                'INSERT INTO appointments (patient_id, appointment_date, department, doctor, status, notes) VALUES (?, ?, ?, ?, ?, ?)'
            );
            $insert->execute([
                $patientId,
                date('Y-m-d H:i:s', strtotime($appointmentDate)),
                $department,
                $doctor,
                $status,
                $notes ?: null,
            ]);
            $message = 'Cita creada correctamente.';
        }
    }
}

$patients = $db->query('SELECT id, full_name, email FROM patients ORDER BY full_name ASC')->fetchAll();
$appointments = $db->query(
    'SELECT a.appointment_date, a.department, a.doctor, a.status, a.notes, p.full_name FROM appointments a JOIN patients p ON a.patient_id = p.id ORDER BY a.appointment_date DESC LIMIT 200'
)->fetchAll();

$pageTitle = 'Citas';
$activePage = 'admin-appointments';
include __DIR__ . '/../../includes/admin_header.php';
?>
<div class="page-head">
    <div>
        <h2>Citas</h2>
        <p class="muted">Programe y controle las citas de los pacientes.</p>
    </div>
</div>

<div class="card">
    <h3>Crear cita</h3>
    <?php if ($message): ?>
        <p class="alert success"><?php echo h($message); ?></p>
    <?php elseif ($error): ?>
        <p class="alert"><?php echo h($error); ?></p>
    <?php endif; ?>
    <form method="post" class="form">
        <?php echo csrf_field(); ?>
        <div class="form-grid">
            <label>
                Paciente
                <select name="patient_id" required>
                    <option value="">Seleccione</option>
                    <?php foreach ($patients as $patient): ?>
                        <option value="<?php echo (int) $patient['id']; ?>"><?php echo h($patient['full_name'] . ' (' . $patient['email'] . ')'); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                Fecha y hora
                <input type="datetime-local" name="appointment_date" required>
            </label>
            <label>
                Servicio
                <input type="text" name="department" required>
            </label>
            <label>
                Profesional
                <input type="text" name="doctor" required>
            </label>
            <label>
                Estado
                <select name="status" required>
                    <option value="Confirmada">Confirmada</option>
                    <option value="Pendiente">Pendiente</option>
                    <option value="Realizada">Realizada</option>
                    <option value="Cancelada">Cancelada</option>
                </select>
            </label>
            <label>
                Notas
                <input type="text" name="notes">
            </label>
        </div>
        <button class="button" type="submit">Registrar cita</button>
    </form>
</div>

<div class="card">
    <h3>Listado de citas</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Paciente</th>
                <th>Fecha</th>
                <th>Servicio</th>
                <th>Profesional</th>
                <th>Estado</th>
                <th>Notas</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($appointments): ?>
                <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td><?php echo h($appointment['full_name']); ?></td>
                        <td><?php echo h(format_datetime($appointment['appointment_date'])); ?></td>
                        <td><?php echo h($appointment['department']); ?></td>
                        <td><?php echo h($appointment['doctor']); ?></td>
                        <td><?php echo h($appointment['status']); ?></td>
                        <td><?php echo h($appointment['notes']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="muted">No hay citas registradas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../../includes/admin_footer.php'; ?>
