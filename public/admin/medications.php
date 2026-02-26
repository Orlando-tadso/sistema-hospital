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
        $medicationName = trim($_POST['medication_name'] ?? '');
        $dosage = trim($_POST['dosage'] ?? '');
        $frequency = trim($_POST['frequency'] ?? '');
        $startDate = $_POST['start_date'] ?? '';
        $endDate = $_POST['end_date'] ?? '';
        $instructions = trim($_POST['instructions'] ?? '');
        $nextRefillDate = $_POST['next_refill_date'] ?? '';

        if (!$patientId || !$medicationName || !$dosage || !$frequency || !$startDate) {
            $error = 'Complete los campos obligatorios.';
        } else {
            $insert = $db->prepare(
                'INSERT INTO medication_reminders (patient_id, medication_name, dosage, frequency, start_date, end_date, instructions, next_refill_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $insert->execute([
                $patientId,
                $medicationName,
                $dosage,
                $frequency,
                $startDate,
                $endDate ?: null,
                $instructions ?: null,
                $nextRefillDate ?: null,
            ]);
            $message = 'Medicamento registrado.';
        }
    }
}

$patients = $db->query('SELECT id, full_name, email FROM patients ORDER BY full_name ASC')->fetchAll();
$medications = $db->query(
    'SELECT m.medication_name, m.dosage, m.frequency, m.start_date, m.end_date, m.next_refill_date, m.instructions, p.full_name FROM medication_reminders m JOIN patients p ON m.patient_id = p.id ORDER BY m.start_date DESC LIMIT 200'
)->fetchAll();

$pageTitle = 'Medicamentos';
$activePage = 'admin-medications';
include __DIR__ . '/../../includes/admin_header.php';
?>
<div class="page-head">
    <div>
        <h2>Medicamentos</h2>
        <p class="muted">Controle tratamientos y recordatorios activos.</p>
    </div>
</div>

<div class="card">
    <h3>Registrar tratamiento</h3>
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
                Medicamento
                <input type="text" name="medication_name" required>
            </label>
            <label>
                Dosis
                <input type="text" name="dosage" required>
            </label>
            <label>
                Frecuencia
                <input type="text" name="frequency" required>
            </label>
            <label>
                Inicio
                <input type="date" name="start_date" required>
            </label>
            <label>
                Fin
                <input type="date" name="end_date">
            </label>
            <label>
                Indicaciones
                <input type="text" name="instructions">
            </label>
            <label>
                Proximo refill
                <input type="date" name="next_refill_date">
            </label>
        </div>
        <button class="button" type="submit">Registrar medicamento</button>
    </form>
</div>

<div class="card">
    <h3>Listado de medicamentos</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Paciente</th>
                <th>Medicamento</th>
                <th>Dosis</th>
                <th>Frecuencia</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th>Refill</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($medications): ?>
                <?php foreach ($medications as $medication): ?>
                    <tr>
                        <td><?php echo h($medication['full_name']); ?></td>
                        <td><?php echo h($medication['medication_name']); ?></td>
                        <td><?php echo h($medication['dosage']); ?></td>
                        <td><?php echo h($medication['frequency']); ?></td>
                        <td><?php echo h(format_date($medication['start_date'])); ?></td>
                        <td><?php echo h(format_date($medication['end_date'])); ?></td>
                        <td><?php echo h(format_date($medication['next_refill_date'])); ?></td>
                    </tr>
                    <?php if ($medication['instructions']): ?>
                        <tr class="table-note">
                            <td colspan="7">Indicaciones: <?php echo h($medication['instructions']); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="muted">No hay tratamientos registrados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../../includes/admin_footer.php'; ?>
