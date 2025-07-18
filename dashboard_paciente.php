<?php
require_once 'includes/proteger.php';
protegerRuta(['paciente']);
require_once 'includes/conexion.php';

$userId = $_SESSION['user_id'];

// Handle form submission for new emotional state
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estado = $_POST['estado'] ?? '';
    $comentario = trim($_POST['comentario'] ?? '');

    if ($estado) {
        $stmt = $pdo->prepare("INSERT INTO estados_emocionales (user_id, estado, comentario, fecha) VALUES (:user_id, :estado, :comentario, NOW())");
        $stmt->execute([
            'user_id' => $userId,
            'estado' => $estado,
            'comentario' => $comentario
        ]);
        header('Location: dashboard_paciente.php');
        exit;
    }
}

// Fetch emotional states history
$stmt = $pdo->prepare("SELECT estado, comentario, fecha FROM estados_emocionales WHERE user_id = :user_id ORDER BY fecha DESC");
$stmt->execute(['user_id' => $userId]);
$historial = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for Chart.js
$estados = [];
$fechas = [];
foreach (array_reverse($historial) as $registro) {
    $fechas[] = date('d-m-Y', strtotime($registro['fecha']));
    $estados[] = $registro['estado'];
}

// Map emotional states to numeric values for chart
$estadoMap = [
    'feliz' => 5,
    'triste' => 1,
    'ansioso' => 2,
    'neutral' => 3,
    'enojado' => 1,
    'estresado' => 2,
    'otro' => 3
];
$estadosNumericos = array_map(function($e) use ($estadoMap) {
    return $estadoMap[strtolower($e)] ?? 3;
}, $estados);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>NeuroTrack - Panel Paciente</title>
    <link rel="stylesheet" href="css/estilo.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="js/main.js" defer></script>
    <style>
        body {
            background: linear-gradient(135deg, #e3f0ff 0%, #f8faff 100%);
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }
        /* Fondo interactivo */
        body::before {
            content: '';
            position: absolute;
            pointer-events: none;
            z-index: 0;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: radial-gradient(circle at var(--x, 50%) var(--y, 50%), #b6d4fe 0%, #e3f0ff 80%, transparent 100%);
            opacity: 0.7;
            transition: width 0.3s, height 0.3s, left 0.1s, top 0.1s;
        }
        .navbar {
            width: 100%;
            background: rgba(79,140,255,0.95); /* celeste */
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            height: 72px;
            box-shadow: 0 4px 24px rgba(37,99,235,0.10);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 10;
            backdrop-filter: blur(8px);
        }
        .navbar h1 {
            font-size: 1.6rem;
            font-weight: 900;
            letter-spacing: 1px;
            margin: 0;
            color: #2563eb; /* color más oscuro */
            font-family: 'Inter', sans-serif;
            flex: 1;
            text-align: center;
        }
        .navbar a {
            color: #fff;
            font-weight: 600;
            text-decoration: none;
            background: #4f8cff;
            padding: 8px 22px;
            border-radius: 10px;
            transition: background 0.2s, color 0.2s;
            position: absolute;
            right: 2rem;
            top: 1.25rem;
        }
        .navbar a:hover {
            background: #e3f0ff;
            color: #4f8cff;
        }
        .main-content {
            padding-top: 90px;
            max-width: 1100px;
            margin: 0 auto;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 2fr; /* Formulario a la izquierda, gráfica más grande a la derecha */
            gap: 32px;
            margin-top: 32px;
            align-items: start;
        }
        .card {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 4px 32px rgba(37,99,235,0.08);
            padding: 36px 28px;
            transition: box-shadow 0.3s, transform 0.3s, background 0.3s;
            position: relative;
            z-index: 1;
        }
        .card:hover {
            box-shadow: 0 8px 32px rgba(37,99,235,0.18);
            transform: translateY(-6px) scale(1.03);
            background: #e3f0ff;
        }
        .card h2 {
            color: #4f8cff;
            margin-bottom: 18px;
            font-size: 1.3rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
        }
        label {
            font-size: 1rem;
            font-weight: 600;
            color: #4f8cff;
            margin-bottom: 4px;
            font-family: 'Inter', sans-serif;
            text-align: center; /* Centrar el texto del label */
        }
        select, textarea, input, button[type="submit"] {
            font-family: 'Inter', sans-serif;
            font-size: 1.08rem;
        }
        select, textarea {
            width: 100%;
            margin-bottom: 12px;
            padding: 10px;
            border-radius: 10px;
            border: 1.5px solid #e3f0ff;
            background: #f8faff;
            box-shadow: 0 2px 8px rgba(37,99,235,0.06);
            transition: border 0.2s, box-shadow 0.2s;
        }
        select:focus, textarea:focus {
            border-color: #4f8cff;
            box-shadow: 0 4px 16px rgba(79,140,255,0.10);
            outline: none;
        }
        button[type="submit"], .popup-btn {
            background: #4f8cff;
            color: #fff;
            font-size: 1.15rem;
            font-weight: 700;
            border: none;
            border-radius: 12px;
            padding: 14px 0;
            margin-top: 8px;
            box-shadow: 0 2px 12px rgba(79,140,255,0.10);
            cursor: pointer;
            transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
            display: block;
            text-align: center;
            width: 100%;
        }
        button[type="submit"]:hover, .popup-btn:hover {
            background: #2563eb;
            color: #fff;
        }
        .historial-lista {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .historial-lista ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .historial-lista li {
            background: #f8faff;
            border-radius: 8px;
            margin-bottom: 8px;
            padding: 10px 14px;
            font-size: 1rem;
            color: #222;
            box-shadow: 0 2px 8px rgba(37,99,235,0.04);
            transition: background 0.2s;
        }
        .historial-lista li:hover {
            background: #e3f0ff;
        }
        .historial-grafica {
            margin-top: 24px;
            background: #f8faff;
            border-radius: 16px;
            padding: 18px;
            box-shadow: 0 2px 8px rgba(37,99,235,0.06);
            /* Ajusta el tamaño de la gráfica */
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #graficaEstados {
            width: 100% !important;
            height: 350px !important;
            max-width: 700px;
            max-height: 350px;
        }
        /* Elimina espacio sobrante debajo de la gráfica */
        .historial-grafica {
            margin-bottom: 0;
        }
        /* Popup styles */
        .popup-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(79,140,255,0.18);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
        }
        .popup-content {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(79,140,255,0.18);
            padding: 32px 24px;
            max-width: 340px;
            width: 90vw;
            text-align: center;
            position: relative;
            animation: fadeIn 0.4s;
        }
        .popup-content h3 {
            color: #4f8cff;
            margin-bottom: 18px;
            font-size: 1.2rem;
        }
        .popup-content ul {
            list-style: none;
            padding: 0;
            margin: 0 0 18px 0;
        }
        .popup-content li {
            background: #f8faff;
            border-radius: 8px;
            margin-bottom: 8px;
            padding: 8px 12px;
            font-size: 1rem;
            color: #222;
            text-align: left;
            display: flex;
            flex-direction: column;
        }
        .popup-content .registro-dia {
            margin-bottom: 4px;
        }
        .popup-content .registro-hora {
            font-size: 0.95rem;
            color: #4f8cff;
            margin-left: 8px;
        }
        .popup-close {
            position: absolute;
            top: 12px;
            right: 16px;
            background: #4f8cff;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            padding: 4px 12px;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
        }
        .popup-close:hover {
            background: #2563eb;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px);}
            to { opacity: 1; transform: translateY(0);}
        }
        @media (max-width: 900px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
                gap: 18px;
            }
            .main-content {
                padding-top: 90px;
                max-width: 98vw;
            }
            .historial-grafica {
                height: 260px;
            }
            #graficaEstados {
                height: 200px !important;
                max-height: 200px;
            }
        }
        /* Centrar el formulario de registro de estado emocional */
        .centered-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .centered-form form {
            width: 100%;
            max-width: 350px;
            margin: 0 auto;
        }
        .centered-form h2 {
            text-align: center;
        }
        /* Centrar los labels y el botón dentro del formulario */
        .centered-form label,
        .centered-form button[type="submit"] {
            display: block;
            text-align: center;
            width: 100%;
        }
        /* Opcional: centrar el select y textarea visualmente */
        .centered-form select,
        .centered-form textarea {
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
    <script>
        // Fondo interactivo celeste al mover el mouse
        document.addEventListener('mousemove', function(e) {
            const body = document.body;
            const x = e.clientX;
            const y = e.clientY;
            body.style.setProperty('--x', x + 'px');
            body.style.setProperty('--y', y + 'px');
        });

        // Popup logic
        function showDiasPopup() {
            document.getElementById('popup-overlay').style.display = 'flex';
        }
        function closeDiasPopup() {
            document.getElementById('popup-overlay').style.display = 'none';
        }
    </script>
</head>
<body>
    <nav class="navbar">
        <h1>NeuroTrack</h1>
        <a href="logout.php">Cerrar sesión</a>
    </nav>
    <div class="main-content">
        <div class="dashboard-grid">
            <section class="card registro-estado centered-form">
                <h2>Registrar estado emocional diario</h2>
                <form id="estadoForm" method="POST" action="dashboard_paciente.php" novalidate>
                    <label for="estado">Estado emocional:</label>
                    <select id="estado" name="estado" required>
                        <option value="">Seleccione...</option>
                        <option value="feliz">Feliz</option>
                        <option value="triste">Triste</option>
                        <option value="ansioso">Ansioso</option>
                        <option value="neutral">Neutral</option>
                        <option value="enojado">Enojado</option>
                        <option value="estresado">Estresado</option>
                        <option value="otro">Otro</option>
                    </select>
                    <label for="comentario">Comentario (opcional):</label>
                    <textarea id="comentario" name="comentario" rows="3"></textarea>
                    <button type="submit">Guardar</button>
                </form>
            </section>
            <section class="card historial">
                <h2>Historial de estados emocionales</h2>
                <div class="historial-lista">
                    <!-- Elimina la lista de días aquí, solo muestra el botón centrado -->
                    <button type="button" class="popup-btn" onclick="showDiasPopup()">Ver días</button>
                </div>
                <div class="historial-grafica">
                    <canvas id="graficaEstados"></canvas>
                </div>
            </section>
        </div>
        <!-- Popup para ver días registrados y lo que puso -->
        <div class="popup-overlay" id="popup-overlay">
            <div class="popup-content">
                <button class="popup-close" onclick="closeDiasPopup()">Cerrar</button>
                <h3>Días registrados</h3>
                <ul>
                    <?php
                    // Agrupa por día y muestra estado, comentario y hora
                    $dias = [];
                    foreach ($historial as $registro) {
                        $dia = htmlspecialchars(date('d-m-Y', strtotime($registro['fecha'])));
                        $hora = htmlspecialchars(date('H:i', strtotime($registro['fecha'])));
                        $estado = htmlspecialchars($registro['estado']);
                        $comentario = htmlspecialchars($registro['comentario']);
                        if (!isset($dias[$dia])) $dias[$dia] = [];
                        $dias[$dia][] = [
                            'estado' => $estado,
                            'comentario' => $comentario,
                            'hora' => $hora
                        ];
                    }
                    foreach ($dias as $dia => $registros): ?>
                        <li>
                            <strong><?= $dia ?>:</strong>
                            <?php foreach ($registros as $r): ?>
                                <span class="registro-dia">
                                    <?= $r['estado'] ?><?= $r['comentario'] ? ' - ' . $r['comentario'] : '' ?>
                                    <span class="registro-hora">[<?= $r['hora'] ?>]</span>
                                </span>
                            <?php endforeach; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('graficaEstados').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?= json_encode($fechas) ?>,
                    datasets: [{
                        label: 'Estado emocional',
                        data: <?= json_encode($estadosNumericos) ?>,
                        fill: false,
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            min: 0,
                            max: 5,
                            ticks: {
                                stepSize: 1,
                                callback: function(value) {
                                    const labels = {
                                        1: 'Bajo',
                                        2: 'Medio-Bajo',
                                        3: 'Medio',
                                        4: 'Medio-Alto',
                                        5: 'Alto'
                                    };
                                    return labels[value] || value;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
