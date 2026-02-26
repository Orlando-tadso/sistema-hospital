<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_login();

$db = get_db();
$user = current_user();

$stmt = $db->prepare(
    'SELECT appointment_date, department, doctor, status, notes FROM appointments WHERE patient_id = ? ORDER BY appointment_date DESC'
);
$stmt->execute([$user['id']]);
$appointments = $stmt->fetchAll();

$pageTitle = 'Citas';
$activePage = 'appointments';
include __DIR__ . '/../includes/header.php';
?>
<div class="page-head">
    <div>
        <h2>Citas y agenda</h2>
        <p class="muted">Mantenga el control de sus proximas consultas.</p>
    </div>
</div>
<div class="card">
    <table class="table">
        <thead>
            <tr>
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
                        <td><?php echo h(format_datetime($appointment['appointment_date'])); ?></td>
                        <td><?php echo h($appointment['department']); ?></td>
                        <td><?php echo h($appointment['doctor']); ?></td>
                        <td><?php echo h($appointment['status']); ?></td>
                        <td><?php echo h($appointment['notes']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="muted">No hay citas registradas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
