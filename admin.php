<?php
require_once 'includes/proteger.php';
protegerRuta(['admin']);
require_once 'includes/conexion.php';

// Handle user activation/deactivation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['toggle_active'], $_POST['user_id'])) {
        $userId = $_POST['user_id'];
        // Get current active status
        $stmt = $pdo->prepare("SELECT active FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $newStatus = $user['active'] ? 0 : 1;
            $update = $pdo->prepare("UPDATE users SET active = :active WHERE id = :id");
            $update->execute(['active' => $newStatus, 'id' => $userId]);
        }
    }

    // Handle patient assignment to therapist
    if (isset($_POST['assign_patient'], $_POST['paciente_id'], $_POST['terapeuta_id'])) {
        $pacienteId = $_POST['paciente_id'];
        $terapeutaId = $_POST['terapeuta_id'];

        // Check if assignment exists
        $stmt = $pdo->prepare("SELECT id FROM asignaciones WHERE paciente_id = :paciente_id");
        $stmt->execute(['paciente_id' => $pacienteId]);
        $exists = $stmt->fetch();

        if ($exists) {
            // Update assignment
            $update = $pdo->prepare("UPDATE asignaciones SET terapeuta_id = :terapeuta_id WHERE paciente_id = :paciente_id");
            $update->execute(['terapeuta_id' => $terapeutaId, 'paciente_id' => $pacienteId]);
        } else {
            // Insert new assignment
            $insert = $pdo->prepare("INSERT INTO asignaciones (paciente_id, terapeuta_id) VALUES (:paciente_id, :terapeuta_id)");
            $insert->execute(['paciente_id' => $pacienteId, 'terapeuta_id' => $terapeutaId]);
        }
    }
    header('Location: admin.php');
    exit;
}

// Fetch all patients
$stmt = $pdo->prepare("SELECT id, email, active FROM users WHERE role = 'paciente'");
$stmt->execute();
$pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all therapists
$stmt = $pdo->prepare("SELECT id, email FROM users WHERE role = 'terapeuta'");
$stmt->execute();
$terapeutas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch current assignments
$stmt = $pdo->prepare("SELECT paciente_id, terapeuta_id FROM asignaciones");
$stmt->execute();
$asignaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
$asignacionesMap = [];
foreach ($asignaciones as $a) {
    $asignacionesMap[$a['paciente_id']] = $a['terapeuta_id'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>NeuroTrack - Panel Admin</title>
    <link rel="stylesheet" href="css/estilo.css" />
    <script src="js/main.js" defer></script>
</head>
<body>
    <nav class="navbar">
        <h1>NeuroTrack - Admin</h1>
        <a href="logout.php">Cerrar sesión</a>
    </nav>
    <main class="container">
        <section class="usuarios">
            <h2>Gestión de Pacientes</h2>
            <table>
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Activo</th>
                        <th>Asignar Terapeuta</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pacientes as $paciente): ?>
                    <tr>
                        <td><?= htmlspecialchars($paciente['email']) ?></td>
                        <td><?= $paciente['active'] ? 'Sí' : 'No' ?></td>
                        <td>
                            <form method="POST" action="admin.php">
                                <input type="hidden" name="paciente_id" value="<?= $paciente['id'] ?>" />
                                <select name="terapeuta_id" required>
                                    <option value="">Seleccione terapeuta</option>
                                    <?php foreach ($terapeutas as $terapeuta): ?>
                                        <option value="<?= $terapeuta['id'] ?>" <?= (isset($asignacionesMap[$paciente['id']]) && $asignacionesMap[$paciente['id']] == $terapeuta['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($terapeuta['email']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" name="assign_patient">Asignar</button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="admin.php">
                                <input type="hidden" name="user_id" value="<?= $paciente['id'] ?>" />
                                <button type="submit" name="toggle_active"><?= $paciente['active'] ? 'Desactivar' : 'Activar' ?></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
        <section class="usuarios">
            <h2>Gestión de Terapeutas</h2>
            <table>
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Activo</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($terapeutas as $terapeuta): ?>
                    <tr>
                        <td><?= htmlspecialchars($terapeuta['email']) ?></td>
                        <td>Sí</td>
                        <td>
                            <!-- For simplicity, therapists cannot be deactivated in this version -->
                            <em>Sin acciones</em>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
