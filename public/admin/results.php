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
        $testName = trim($_POST['test_name'] ?? '');
        $resultValue = trim($_POST['result_value'] ?? '');
        $unit = trim($_POST['unit'] ?? '');
        $referenceRange = trim($_POST['reference_range'] ?? '');
        $resultDate = $_POST['result_date'] ?? '';
        $status = trim($_POST['status'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        if (!$patientId || !$testName || !$resultValue || !$resultDate || !$status) {
            $error = 'Complete los campos obligatorios.';
        } else {
            $insert = $db->prepare(
                'INSERT INTO lab_results (patient_id, test_name, result_value, unit, reference_range, result_date, status, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $insert->execute([
                $patientId,
                $testName,
                $resultValue,
                $unit ?: null,
                $referenceRange ?: null,
                $resultDate,
                $status,
                $notes ?: null,
            ]);
            $message = 'Resultado registrado correctamente.';
        }
    }
}

$patients = $db->query('SELECT id, full_name, email FROM patients ORDER BY full_name ASC')->fetchAll();
$results = $db->query(
    'SELECT r.test_name, r.result_value, r.unit, r.reference_range, r.result_date, r.status, r.notes, p.full_name FROM lab_results r JOIN patients p ON r.patient_id = p.id ORDER BY r.result_date DESC LIMIT 200'
)->fetchAll();

$pageTitle = 'Resultados';
$activePage = 'admin-results';
include __DIR__ . '/../../includes/admin_header.php';
?>
<div class="page-head">
    <div>
        <h2>Resultados de laboratorio</h2>
        <p class="muted">Registre y controle los examenes del hospital.</p>
    </div>
</div>

<div class="card">
    <h3>Registrar resultado</h3>
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
                Prueba
                <input type="text" name="test_name" required>
            </label>
            <label>
                Valor
                <input type="text" name="result_value" required>
            </label>
            <label>
                Unidad
                <input type="text" name="unit">
            </label>
            <label>
                Rango referencia
                <input type="text" name="reference_range">
            </label>
            <label>
                Fecha
                <input type="date" name="result_date" required>
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
        <button class="button" type="submit">Registrar resultado</button>
    </form>
</div>

<div class="card">
    <h3>Listado de resultados</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Paciente</th>
                <th>Prueba</th>
                <th>Resultado</th>
                <th>Rango</th>
                <th>Fecha</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($results): ?>
                <?php foreach ($results as $result): ?>
                    <tr>
                        <td><?php echo h($result['full_name']); ?></td>
                        <td><?php echo h($result['test_name']); ?></td>
                        <td><?php echo h($result['result_value']); ?> <?php echo h($result['unit']); ?></td>
                        <td><?php echo h($result['reference_range']); ?></td>
                        <td><?php echo h(format_date($result['result_date'])); ?></td>
                        <td><?php echo h($result['status']); ?></td>
                    </tr>
                    <?php if ($result['notes']): ?>
                        <tr class="table-note">
                            <td colspan="6">Nota: <?php echo h($result['notes']); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="muted">No hay resultados registrados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../../includes/admin_footer.php'; ?>
