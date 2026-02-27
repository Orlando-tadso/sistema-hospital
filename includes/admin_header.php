<?php
require_once __DIR__ . '/admin_auth.php';

$pageTitle = $pageTitle ?? 'Panel Administrativo';
$activePage = $activePage ?? '';
$showAdminNav = $showAdminNav ?? true;
$admin = current_admin();
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
                    <h1 style="margin: 0.25rem 0 0;">Panel Administrativo</h1>
                </div>
            </div>
            <?php if ($admin): ?>
                <div class="user-meta">
                    <span><?php echo h($admin['full_name']); ?></span>
                    <a class="button ghost" href="/admin/logout.php">Salir</a>
                </div>
            <?php endif; ?>
        </div>
        <?php if ($showAdminNav && $admin): ?>
        <nav class="site-nav">
            <div class="container">
                <a class="<?php echo $activePage === 'admin-dashboard' ? 'active' : ''; ?>" href="/admin/index.php">Resumen</a>
                <a class="<?php echo $activePage === 'admin-patients' ? 'active' : ''; ?>" href="/admin/patients.php">Pacientes</a>
                <a class="<?php echo $activePage === 'admin-appointments' ? 'active' : ''; ?>" href="/admin/appointments.php">Citas</a>
                <a class="<?php echo $activePage === 'admin-results' ? 'active' : ''; ?>" href="/admin/results.php">Resultados</a>
                <a class="<?php echo $activePage === 'admin-history' ? 'active' : ''; ?>" href="/admin/history.php">Historial</a>
                <a class="<?php echo $activePage === 'admin-medications' ? 'active' : ''; ?>" href="/admin/medications.php">Medicamentos</a>
            </div>
        </nav>
        <?php endif; ?>
    </header>
    <main class="site-main">
        <div class="container">
