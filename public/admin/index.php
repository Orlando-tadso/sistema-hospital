<?php
require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/admin_auth.php';
require_admin();

$db = get_db();

$statPatients = (int) $db->query('SELECT COUNT(*) FROM patients')->fetchColumn();
$statAppointments = (int) $db->query('SELECT COUNT(*) FROM appointments')->fetchColumn();
$statResults = (int) $db->query('SELECT COUNT(*) FROM lab_results')->fetchColumn();
$statMeds = (int) $db->query('SELECT COUNT(*) FROM medication_reminders')->fetchColumn();

$recentAppointments = $db->query(
    'SELECT a.appointment_date, a.department, a.status, p.full_name FROM appointments a JOIN patients p ON a.patient_id = p.id ORDER BY a.appointment_date DESC LIMIT 5'
)->fetchAll();

$pageTitle = 'Resumen administrativo';
$activePage = 'admin-dashboard';
include __DIR__ . '/../../includes/admin_header.php';
?>
<section class="hero">
    <div>
        <p class="eyebrow">Bienvenida</p>
        <h2>Panel de control administrativo</h2>
        <p class="muted">Visualice indicadores principales y la actividad reciente del hospital.</p>
    </div>
    <div class="stats">
        <div class="stat">
            <span><?php echo $statPatients; ?></span>
            <p>Pacientes registrados</p>
        </div>
        <div class="stat">
            <span><?php echo $statAppointments; ?></span>
            <p>Citas totales</p>
        </div>
        <div class="stat">
            <span><?php echo $statResults; ?></span>
            <p>Resultados lab.</p>
        </div>
        <div class="stat">
            <span><?php echo $statMeds; ?></span>
            <p>Tratamientos activos</p>
        </div>
    </div>
</section>

<div class="card">
    <h3>Actividad reciente</h3>
    <p class="muted" style="margin-bottom: 1.25rem; font-size: 0.9rem;">Ultimas citas registradas en el sistema.</p>
    <table class="table">
        <thead>
            <tr>
                <th>Paciente</th>
                <th>Fecha</th>
                <th>Servicio</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($recentAppointments): ?>
                <?php foreach ($recentAppointments as $appointment): ?>
                    <tr>
                        <td><?php echo h($appointment['full_name']); ?></td>
                        <td><?php echo h(format_datetime($appointment['appointment_date'])); ?></td>
                        <td><?php echo h($appointment['department']); ?></td>
                        <td><?php echo h($appointment['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="muted">No hay citas recientes.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../../includes/admin_footer.php'; ?>
