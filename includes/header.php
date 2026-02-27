<?php
$user = current_user();
$pageTitle = $pageTitle ?? 'Portal del Paciente';
$activePage = $activePage ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($pageTitle); ?></title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <header class="site-header">
        <div class="container header-content">
            <div class="brand">
                <img class="logo" src="/assets/logo.jpg" alt="Hospital San Nicolas de Tolentino">
                <div>
                    <p class="eyebrow">Hospital San Nicolas de Tolentino</p>
                    <h1 style="margin: 0.25rem 0 0;">Portal del Paciente</h1>
                </div>
            </div>
            <?php if ($user): ?>
                <div class="user-meta">
                    <span><?php echo h($user['full_name']); ?></span>
                    <a class="button ghost" href="/sistema_hospital/public/logout.php">Salir</a>
                </div>
            <?php endif; ?>
        </div>
        <?php if ($user): ?>
        <nav class="site-nav">
            <div class="container">
                <a class="<?php echo $activePage === 'dashboard' ? 'active' : ''; ?>" href="/sistema_hospital/public/dashboard.php">Resumen</a>
                <a class="<?php echo $activePage === 'appointments' ? 'active' : ''; ?>" href="/sistema_hospital/public/appointments.php">Citas</a>
                <a class="<?php echo $activePage === 'results' ? 'active' : ''; ?>" href="/sistema_hospital/public/results.php">Resultados</a>
                <a class="<?php echo $activePage === 'history' ? 'active' : ''; ?>" href="/sistema_hospital/public/history.php">Historial</a>
                <a class="<?php echo $activePage === 'medications' ? 'active' : ''; ?>" href="/sistema_hospital/public/medications.php">Medicamentos</a>
            </div>
        </nav>
        <?php endif; ?>
    </header>
    <main class="site-main">
        <div class="container">
