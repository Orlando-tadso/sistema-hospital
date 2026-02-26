<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$db = get_db();
$message = '';

function normalize_email_part(string $value): string
{
    $value = trim($value);
    $value = iconv('UTF-8', 'ASCII//TRANSLIT', $value);
    $value = preg_replace('/[^a-zA-Z0-9]/', '', $value);
    $value = strtolower($value);

    return $value !== '' ? $value : 'paciente';
}

function build_email_from_name(string $fullName, int $seed, array &$usedEmails): string
{
    $parts = preg_split('/\s+/', trim($fullName));
    $first = $parts[0] ?? 'paciente';
    $last = $parts ? $parts[count($parts) - 1] : 'demo';

    $base = normalize_email_part($last) . normalize_email_part($first);
    $suffix = ($seed % 90) + 10;

    for ($attempt = 0; $attempt < 90; $attempt++) {
        $candidate = $base . str_pad((string) $suffix, 2, '0', STR_PAD_LEFT) . '@gmail.com';
        if (!isset($usedEmails[$candidate])) {
            $usedEmails[$candidate] = true;
            return $candidate;
        }
        $suffix++;
        if ($suffix > 99) {
            $suffix = 10;
        }
    }

    $fallback = $base . str_pad((string) $seed, 4, '0', STR_PAD_LEFT) . '@gmail.com';
    $usedEmails[$fallback] = true;
    return $fallback;
}

function make_mobile(): string
{
    return '3' . mt_rand(100000000, 999999999);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $message = 'La solicitud no es valida.';
    } else {
        $patients = $db->query('SELECT id, full_name FROM patients ORDER BY id ASC')->fetchAll();
        $updateStmt = $db->prepare('UPDATE patients SET email = ?, phone = ? WHERE id = ?');

        $updated = 0;
        $usedEmails = [];
        foreach ($patients as $patient) {
            $id = (int) $patient['id'];
            $fullName = (string) $patient['full_name'];
            $email = build_email_from_name($fullName, $id, $usedEmails);
            $phone = make_mobile();

            $updateStmt->execute([$email, $phone, $id]);
            $updated++;
        }

        $message = 'Pacientes actualizados: ' . $updated . '.';
    }
}

$pageTitle = 'Actualizar pacientes';
include __DIR__ . '/../includes/header.php';
?>
<div class="card">
    <h2>Actualizar pacientes existentes</h2>
    <p class="muted">Actualiza correo a formato apellido+nombre+2 digitos y celular colombiano.</p>
    <?php if ($message): ?>
        <p class="alert success"><?php echo h($message); ?></p>
    <?php endif; ?>
    <form method="post">
        <?php echo csrf_field(); ?>
        <button class="button" type="submit">Actualizar pacientes</button>
    </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
