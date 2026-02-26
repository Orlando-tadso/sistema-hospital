<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_login();

$db = get_db();
$user = current_user();

$stmt = $db->prepare(
    'SELECT test_name, result_value, unit, reference_range, result_date, status, notes FROM lab_results WHERE patient_id = ? ORDER BY result_date DESC'
);
$stmt->execute([$user['id']]);
$results = $stmt->fetchAll();

$pageTitle = 'Resultados de Pruebas';
$activePage = 'results';
include __DIR__ . '/../includes/header.php';
?>
<div class="page-head">
    <div>
        <h2>Resultados de laboratorio</h2>
        <p class="muted">Acceda a sus analisis clinicos en un solo lugar.</p>
    </div>
</div>
<div class="card">
    <table class="table">
        <thead>
            <tr>
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
                        <td><?php echo h($result['test_name']); ?></td>
                        <td><?php echo h($result['result_value']); ?> <?php echo h($result['unit']); ?></td>
                        <td><?php echo h($result['reference_range']); ?></td>
                        <td><?php echo h(format_date($result['result_date'])); ?></td>
                        <td><?php echo h($result['status']); ?></td>
                    </tr>
                    <?php if ($result['notes']): ?>
                        <tr class="table-note">
                            <td colspan="5">Nota: <?php echo h($result['notes']); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="muted">No hay resultados registrados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
