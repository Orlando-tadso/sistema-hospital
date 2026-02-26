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
        $conditionName = trim($_POST['condition_name'] ?? '');
        $diagnosedDate = $_POST['diagnosed_date'] ?? '';
        $status = trim($_POST['status'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        if (!$patientId || !$conditionName || !$status) {
            $error = 'Complete los campos obligatorios.';
        } else {
            $insert = $db->prepare(
                'INSERT INTO medical_history (patient_id, condition_name, diagnosed_date, status, notes) VALUES (?, ?, ?, ?, ?)'
            );
            $insert->execute([
                $patientId,
                $conditionName,
                $diagnosedDate ?: null,
                $status,
                $notes ?: null,
            ]);
            $message = 'Historial actualizado.';
        }
    }
}

$patients = $db->query('SELECT id, full_name, email FROM patients ORDER BY full_name ASC')->fetchAll();
$history = $db->query(
    'SELECT h.condition_name, h.diagnosed_date, h.status, h.notes, p.full_name FROM medical_history h JOIN patients p ON h.patient_id = p.id ORDER BY h.diagnosed_date DESC LIMIT 200'
)->fetchAll();

$pageTitle = 'Historial medico';
$activePage = 'admin-history';
include __DIR__ . '/../../includes/admin_header.php';
?>
<div class="page-head">
    <div>
        <h2>Historial medico</h2>
        <p class="muted">Agregue diagnosticos y observaciones clinicas.</p>
    </div>
</div>

<div class="card">
    <h3>Agregar condicion</h3>
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
                Condicion
                <input type="text" name="condition_name" required>
            </label>
            <label>
                Fecha diagnostico
                <input type="date" name="diagnosed_date">
            </label>
            <label>
                Estado
                <input type="text" name="status" required>
            </label>
            <label>
                Notas
                <input type="text" name="notes">
            </label>
        </div>
        <button class="button" type="submit">Guardar</button>
    </form>
</div>

<div class="card">
    <h3>Listado de historial</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Paciente</th>
                <th>Condicion</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Notas</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($history): ?>
                <?php foreach ($history as $item): ?>
                    <tr>
                        <td><?php echo h($item['full_name']); ?></td>
                        <td><?php echo h($item['condition_name']); ?></td>
                        <td><?php echo h(format_date($item['diagnosed_date'])); ?></td>
                        <td><?php echo h($item['status']); ?></td>
                        <td><?php echo h($item['notes']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="muted">No hay registros de historial.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../../includes/admin_footer.php'; ?>
