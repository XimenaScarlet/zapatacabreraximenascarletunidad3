<?php
require_once 'includes/proteger.php';
protegerRuta(['terapeuta']);
require_once 'includes/conexion.php';

$terapeutaId = $_SESSION['user_id'];

// Fetch patients assigned to this therapist
$stmt = $pdo->prepare("SELECT p.id, p.email FROM users p INNER JOIN asignaciones a ON p.id = a.paciente_id WHERE a.terapeuta_id = :terapeuta_id AND p.active = 1");
$stmt->execute(['terapeuta_id' => $terapeutaId]);
$pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$selectedPacienteId = $_GET['paciente'] ?? null;
$registros = [];

if ($selectedPacienteId) {
    // Fetch emotional states and therapist observations for selected patient
    $stmt = $pdo->prepare("SELECT e.id, e.estado, e.comentario, e.fecha, o.observacion FROM estados_emocionales e LEFT JOIN observaciones o ON e.id = o.estado_id AND o.terapeuta_id = :terapeuta_id WHERE e.user_id = :paciente_id ORDER BY e.fecha DESC");
    $stmt->execute(['terapeuta_id' => $terapeutaId, 'paciente_id' => $selectedPacienteId]);
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle adding observation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['estado_id'], $_POST['observacion'])) {
    $estadoId = $_POST['estado_id'];
    $observacion = trim($_POST['observacion']);

    // Check if observation exists
    $stmt = $pdo->prepare("SELECT id FROM observaciones WHERE estado_id = :estado_id AND terapeuta_id = :terapeuta_id");
    $stmt->execute(['estado_id' => $estadoId, 'terapeuta_id' => $terapeutaId]);
    $existe = $stmt->fetch();

    if ($existe) {
        // Update existing observation
        $stmt = $pdo->prepare("UPDATE observaciones SET observacion = :observacion WHERE id = :id");
        $stmt->execute(['observacion' => $observacion, 'id' => $existe['id']]);
    } else {
        // Insert new observation
        $stmt = $pdo->prepare("INSERT INTO observaciones (estado_id, terapeuta_id, observacion) VALUES (:estado_id, :terapeuta_id, :observacion)");
        $stmt->execute(['estado_id' => $estadoId, 'terapeuta_id' => $terapeutaId, 'observacion' => $observacion]);
    }
    header('Location: dashboard_terapeuta.php?paciente=' . $selectedPacienteId);
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>NeuroTrack - Panel Terapeuta</title>
    <link rel="stylesheet" href="css/estilo.css" />
    <script src="js/main.js" defer></script>
</head>
<body>
    <nav class="navbar">
        <h1>NeuroTrack - Terapeuta</h1>
        <a href="logout.php">Cerrar sesión</a>
    </nav>
    <main class="container">
        <section class="pacientes">
            <h2>Pacientes asignados</h2>
            <ul>
                <?php foreach ($pacientes as $paciente): ?>
                    <li>
                        <a href="dashboard_terapeuta.php?paciente=<?= htmlspecialchars($paciente['id']) ?>">
                            <?= htmlspecialchars($paciente['email']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
        <?php if ($selectedPacienteId): ?>
        <section class="registros">
            <h2>Registros de paciente</h2>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Comentario paciente</th>
                        <th>Observación terapeuta</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registros as $registro): ?>
                    <tr>
                        <td><?= htmlspecialchars(date('d-m-Y', strtotime($registro['fecha']))) ?></td>
                        <td><?= htmlspecialchars($registro['estado']) ?></td>
                        <td><?= htmlspecialchars($registro['comentario']) ?></td>
                        <td>
                            <form method="POST" action="dashboard_terapeuta.php?paciente=<?= htmlspecialchars($selectedPacienteId) ?>">
                                <input type="hidden" name="estado_id" value="<?= $registro['id'] ?>" />
                                <textarea name="observacion" rows="2"><?= htmlspecialchars($registro['observacion'] ?? '') ?></textarea>
                                <button type="submit">Guardar</button>
                            </form>
                        </td>
                        <td></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
        <?php endif; ?>
    </main>
</body>
</html>
