<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_login();

$db = get_db();
$user = current_user();

$stmt = $db->prepare(
    'SELECT condition_name, diagnosed_date, status, notes FROM medical_history WHERE patient_id = ? ORDER BY diagnosed_date DESC'
);
$stmt->execute([$user['id']]);
$history = $stmt->fetchAll();

$pageTitle = 'Historial Medico';
$activePage = 'history';
include __DIR__ . '/../includes/header.php';
?>
<div class="page-head">
    <div>
        <h2>Historial medico</h2>
        <p class="muted">Conserve un registro claro de diagnos y tratamientos.</p>
    </div>
</div>
<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Condicion</th>
                <th>Fecha diagnostico</th>
                <th>Estado</th>
                <th>Notas</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($history): ?>
                <?php foreach ($history as $item): ?>
                    <tr>
                        <td><?php echo h($item['condition_name']); ?></td>
                        <td><?php echo h(format_date($item['diagnosed_date'])); ?></td>
                        <td><?php echo h($item['status']); ?></td>
                        <td><?php echo h($item['notes']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="muted">No hay historial registrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
