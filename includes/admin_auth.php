<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

function admin_login(string $email, string $password): bool
{
    $db = get_db();
    $stmt = $db->prepare('SELECT id, full_name, email, password_hash FROM admins WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if (!$admin || !password_verify($password, $admin['password_hash'])) {
        return false;
    }

    $_SESSION['admin'] = [
        'id' => (int) $admin['id'],
        'full_name' => $admin['full_name'],
        'email' => $admin['email'],
    ];

    return true;
}

function admin_logout(): void
{
    unset($_SESSION['admin']);
}

function current_admin(): ?array
{
    return $_SESSION['admin'] ?? null;
}

function require_admin(): void
{
    if (!current_admin()) {
        redirect('/admin/login.php');
    }
}
