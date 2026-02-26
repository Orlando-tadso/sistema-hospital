<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_login();

$db = get_db();
$user = current_user();

$nextAppointmentStmt = $db->prepare(
    'SELECT appointment_date, department, doctor, status, notes FROM appointments WHERE patient_id = ? AND appointment_date >= NOW() ORDER BY appointment_date ASC LIMIT 1'
);
$nextAppointmentStmt->execute([$user['id']]);
$nextAppointment = $nextAppointmentStmt->fetch();

$latestResultStmt = $db->prepare(
    'SELECT test_name, result_value, unit, reference_range, result_date, status FROM lab_results WHERE patient_id = ? ORDER BY result_date DESC LIMIT 1'
);
$latestResultStmt->execute([$user['id']]);
$latestResult = $latestResultStmt->fetch();

$activeMedStmt = $db->prepare(
    'SELECT medication_name, dosage, frequency, start_date, end_date FROM medication_reminders WHERE patient_id = ? AND (end_date IS NULL OR end_date >= CURDATE()) ORDER BY start_date DESC LIMIT 5'
);
$activeMedStmt->execute([$user['id']]);
$activeMeds = $activeMedStmt->fetchAll();

$statUpcoming = $db->prepare(
    'SELECT COUNT(*) FROM appointments WHERE patient_id = ? AND appointment_date >= NOW()'
);
$statUpcoming->execute([$user['id']]);

$statResults = $db->prepare('SELECT COUNT(*) FROM lab_results WHERE patient_id = ?');
$statResults->execute([$user['id']]);

$statMeds = $db->prepare(
    'SELECT COUNT(*) FROM medication_reminders WHERE patient_id = ? AND (end_date IS NULL OR end_date >= CURDATE())'
);
$statMeds->execute([$user['id']]);

$pageTitle = 'Resumen del Paciente';
$activePage = 'dashboard';
include __DIR__ . '/../includes/header.php';
?>
<section class="hero">
    <div>
        <p class="eyebrow">Hola, <?php echo h($user['full_name']); ?></p>
        <h2>Su salud en un solo lugar</h2>
        <p class="muted">Revise su agenda, resultados recientes y tratamientos activos.</p>
    </div>
    <div class="stats">
        <div class="stat">
            <span><?php echo (int) $statUpcoming->fetchColumn(); ?></span>
            <p>Citas proximas</p>
        </div>
        <div class="stat">
            <span><?php echo (int) $statResults->fetchColumn(); ?></span>
            <p>Resultados</p>
        </div>
        <div class="stat">
            <span><?php echo (int) $statMeds->fetchColumn(); ?></span>
            <p>Medicamentos activos</p>
        </div>
    </div>
</section>

<section class="grid">
    <div class="card">
        <h3>Proxima cita</h3>
        <?php if ($nextAppointment): ?>
            <p class="metric"><?php echo h(format_datetime($nextAppointment['appointment_date'])); ?></p>
            <p><?php echo h($nextAppointment['department']); ?> · <?php echo h($nextAppointment['doctor']); ?></p>
            <p class="muted">Estado: <?php echo h($nextAppointment['status']); ?></p>
            <p class="muted">Notas: <?php echo h($nextAppointment['notes']); ?></p>
        <?php else: ?>
            <p class="muted">No hay citas programadas.</p>
        <?php endif; ?>
    </div>
    <div class="card">
        <h3>Ultimo resultado</h3>
        <?php if ($latestResult): ?>
            <p class="metric"><?php echo h($latestResult['test_name']); ?></p>
            <p><?php echo h($latestResult['result_value']); ?> <?php echo h($latestResult['unit']); ?></p>
            <p class="muted">Rango: <?php echo h($latestResult['reference_range']); ?></p>
            <p class="muted">Fecha: <?php echo h(format_date($latestResult['result_date'])); ?></p>
            <p class="muted">Estado: <?php echo h($latestResult['status']); ?></p>
        <?php else: ?>
            <p class="muted">Sin resultados disponibles.</p>
        <?php endif; ?>
    </div>
    <div class="card">
        <h3>Medicacion activa</h3>
        <?php if ($activeMeds): ?>
            <ul class="list">
                <?php foreach ($activeMeds as $med): ?>
                    <li>
                        <strong><?php echo h($med['medication_name']); ?></strong>
                        <span><?php echo h($med['dosage']); ?> · <?php echo h($med['frequency']); ?></span>
                        <span class="muted">Desde <?php echo h(format_date($med['start_date'])); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="muted">No hay tratamientos activos.</p>
        <?php endif; ?>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
