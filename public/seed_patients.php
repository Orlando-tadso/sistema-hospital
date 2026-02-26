<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$db = get_db();
$message = '';

$firstNames = [
    'Ana', 'Luis', 'Carlos', 'Maria', 'Jose', 'Lucia', 'Pedro', 'Sofia', 'Diego', 'Elena',
    'Jorge', 'Paula', 'Miguel', 'Laura', 'Andres', 'Camila', 'Rafael', 'Valeria', 'Fernando', 'Gabriela',
    'Hector', 'Isabel', 'Julian', 'Natalia', 'Ricardo', 'Daniela'
];
$lastNames = [
    'Gomez', 'Perez', 'Rodriguez', 'Diaz', 'Fernandez', 'Sanchez', 'Ramirez', 'Torres', 'Flores', 'Vargas',
    'Castro', 'Ruiz', 'Morales', 'Herrera', 'Mendoza', 'Ortiz', 'Gutierrez', 'Rojas', 'Silva', 'Navarro',
    'Lopez', 'Cruz', 'Romero', 'Suarez'
];
$departments = ['Cardiologia', 'Pediatria', 'Dermatologia', 'Neurologia', 'Ginecologia', 'Traumatologia'];
$doctors = ['Dr. Paula Rios', 'Dr. Javier Luna', 'Dra. Teresa Moya', 'Dr. Diego Vega', 'Dra. Laura Ortiz'];
$tests = ['Hemograma', 'Glucosa', 'Perfil lipidico', 'TSH', 'Vitamina D', 'Creatinina'];
$conditions = ['Hipertension', 'Diabetes tipo 2', 'Alergia estacional', 'Asma', 'Gastritis', 'Migrana'];
$meds = ['Enalapril', 'Metformina', 'Loratadina', 'Omeprazol', 'Salbutamol', 'Atorvastatina'];

function normalize_email_part(string $value): string
{
    $value = trim($value);
    $value = iconv('UTF-8', 'ASCII//TRANSLIT', $value);
    $value = preg_replace('/[^a-zA-Z0-9]/', '', $value);
    $value = strtolower($value);

    return $value !== '' ? $value : 'paciente';
}

function build_email(string $first, string $last, int $seed, array &$usedEmails): string
{
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

function random_date_between(DateTime $start, DateTime $end): string
{
    $min = $start->getTimestamp();
    $max = $end->getTimestamp();
    $rand = mt_rand($min, $max);
    return date('Y-m-d', $rand);
}

function random_datetime_between(DateTime $start, DateTime $end): string
{
    $min = $start->getTimestamp();
    $max = $end->getTimestamp();
    $rand = mt_rand($min, $max);
    return date('Y-m-d H:i:s', $rand);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $message = 'La solicitud no es valida.';
    } else {
        $year = (int) date('Y');
        $startYear = new DateTime($year . '-01-01');
        $today = new DateTime('now');
        $future = new DateTime('now');
        $future->modify('+60 days');

        $patientStmt = $db->prepare(
            'INSERT INTO patients (full_name, email, password_hash, dob, phone, created_at) VALUES (?, ?, ?, ?, ?, NOW())'
        );
        $appointmentStmt = $db->prepare(
            'INSERT INTO appointments (patient_id, appointment_date, department, doctor, status, notes) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $resultStmt = $db->prepare(
            'INSERT INTO lab_results (patient_id, test_name, result_value, unit, reference_range, result_date, status, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $historyStmt = $db->prepare(
            'INSERT INTO medical_history (patient_id, condition_name, diagnosed_date, status, notes) VALUES (?, ?, ?, ?, ?)'
        );
        $medStmt = $db->prepare(
            'INSERT INTO medication_reminders (patient_id, medication_name, dosage, frequency, start_date, end_date, instructions, next_refill_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $selectStmt = $db->prepare('SELECT id FROM patients WHERE email = ? LIMIT 1');

        $created = 0;
        $usedEmails = [];
        for ($i = 1; $i <= 100; $i++) {
            $first = $firstNames[array_rand($firstNames)];
            $second = $firstNames[array_rand($firstNames)];
            $last1 = $lastNames[array_rand($lastNames)];
            $last2 = $lastNames[array_rand($lastNames)];
            $fullName = $first . ' ' . $second . ' ' . $last1 . ' ' . $last2;

            $email = build_email($first, $last1, $i, $usedEmails);

            $selectStmt->execute([$email]);
            $existing = $selectStmt->fetch();
            if ($existing) {
                continue;
            }

            $dobYear = mt_rand(1968, 2005);
            $dob = $dobYear . '-' . str_pad((string) mt_rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad((string) mt_rand(1, 28), 2, '0', STR_PAD_LEFT);
            $phone = '3' . mt_rand(100000000, 999999999);

            $patientStmt->execute([
                $fullName,
                $email,
                password_hash('Demo1234', PASSWORD_DEFAULT),
                $dob,
                $phone,
            ]);

            $patientId = (int) $db->lastInsertId();

            $appointmentStmt->execute([
                $patientId,
                random_datetime_between($startYear, $today),
                $departments[array_rand($departments)],
                $doctors[array_rand($doctors)],
                'Realizada',
                'Consulta de control anual',
            ]);
            $appointmentStmt->execute([
                $patientId,
                random_datetime_between($today, $future),
                $departments[array_rand($departments)],
                $doctors[array_rand($doctors)],
                'Confirmada',
                'Seguimiento programado',
            ]);

            $resultDate1 = random_date_between($startYear, $today);
            $resultDate2 = random_date_between($startYear, $today);
            $resultStmt->execute([
                $patientId,
                $tests[array_rand($tests)],
                (string) mt_rand(70, 160),
                'mg/dL',
                '70 - 110',
                $resultDate1,
                'Normal',
                'Resultado dentro de rango',
            ]);
            $resultStmt->execute([
                $patientId,
                $tests[array_rand($tests)],
                (string) mt_rand(10, 17),
                'g/dL',
                '12 - 16',
                $resultDate2,
                'Control',
                'Repetir en 6 meses',
            ]);

            $historyStmt->execute([
                $patientId,
                $conditions[array_rand($conditions)],
                random_date_between($startYear, $today),
                'Activa',
                'Seguimiento por clinica',
            ]);

            $startMed = random_date_between($startYear, $today);
            $nextRefill = random_date_between($today, $future);
            $medStmt->execute([
                $patientId,
                $meds[array_rand($meds)],
                '10 mg',
                '1 vez al dia',
                $startMed,
                null,
                'Tomar con agua',
                $nextRefill,
            ]);

            $created++;
        }

        $message = 'Pacientes creados: ' . $created . '. Clave: Demo1234.';
    }
}

$pageTitle = 'Cargar pacientes demo';
include __DIR__ . '/../includes/header.php';
?>
<div class="card">
    <h2>Crear 100 pacientes demo</h2>
    <p class="muted">Genera pacientes con nombre completo, correo personalizado y celular.</p>
    <?php if ($message): ?>
        <p class="alert success"><?php echo h($message); ?></p>
    <?php endif; ?>
    <form method="post">
        <?php echo csrf_field(); ?>
        <button class="button" type="submit">Crear pacientes</button>
    </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
