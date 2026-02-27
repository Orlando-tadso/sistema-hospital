<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

function login(string $email, string $password): bool
{
    $db = get_db();
    $stmt = $db->prepare('SELECT id, full_name, email, password_hash FROM patients WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return false;
    }

    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'full_name' => $user['full_name'],
        'email' => $user['email'],
    ];

    return true;
}

function logout(): void
{
    $_SESSION = [];
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_login(): void
{
    if (!current_user()) {
        redirect('/login.php');
    }
}
