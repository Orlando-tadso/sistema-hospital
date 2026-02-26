<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_login();

$db = get_db();
$user = current_user();

$stmt = $db->prepare(
    'SELECT medication_name, dosage, frequency, start_date, end_date, instructions, next_refill_date FROM medication_reminders WHERE patient_id = ? ORDER BY start_date DESC'
);
$stmt->execute([$user['id']]);
$medications = $stmt->fetchAll();

$pageTitle = 'Medicamentos';
$activePage = 'medications';
include __DIR__ . '/../includes/header.php';
?>
<div class="page-head">
    <div>
        <h2>Recordatorios de medicacion</h2>
        <p class="muted">Mantenga su tratamiento al dia con recordatorios claros.</p>
    </div>
</div>
<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Medicamento</th>
                <th>Dosis</th>
                <th>Frecuencia</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th>Proximo refill</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($medications): ?>
                <?php foreach ($medications as $medication): ?>
                    <tr>
                        <td><?php echo h($medication['medication_name']); ?></td>
                        <td><?php echo h($medication['dosage']); ?></td>
                        <td><?php echo h($medication['frequency']); ?></td>
                        <td><?php echo h(format_date($medication['start_date'])); ?></td>
                        <td><?php echo h(format_date($medication['end_date'])); ?></td>
                        <td><?php echo h(format_date($medication['next_refill_date'])); ?></td>
                    </tr>
                    <?php if ($medication['instructions']): ?>
                        <tr class="table-note">
                            <td colspan="6">Indicaciones: <?php echo h($medication['instructions']); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="muted">No hay recordatorios registrados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
