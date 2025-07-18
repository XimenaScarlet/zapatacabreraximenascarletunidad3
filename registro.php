<?php
require_once 'includes/conexion.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Por favor complete todos los campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Correo electrónico inválido.';
    } elseif ($password !== $confirm_password) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            $error = 'El correo electrónico ya está registrado.';
        } else {
            // Insert new patient user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (email, password, role, active) VALUES (:email, :password, 'paciente', 1)");
            $stmt->execute(['email' => $email, 'password' => $hashed_password]);
            $success = 'Registro exitoso. Puede iniciar sesión ahora.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>NeuroTrack - Registro</title>
    <link rel="stylesheet" href="css/estilo.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;900&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e3f0ff 0%, #f8faff 100%);
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 8px 32px rgba(37,99,235,0.10);
            padding: 48px 36px 36px 36px;
            max-width: 400px;
            width: 100%;
            margin: 32px auto;
        }
        h1 {
            text-align: center;
            font-size: 2.2rem;
            font-weight: 900;
            color: #4f8cff;
            margin-bottom: 28px;
            font-family: 'Inter', sans-serif;
            letter-spacing: 1px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        label {
            font-size: 1rem;
            font-weight: 600;
            color: #4f8cff;
            margin-bottom: 4px;
            font-family: 'Inter', sans-serif;
        }
        input[type="email"], input[type="password"] {
            padding: 12px 14px;
            border-radius: 10px;
            border: 1.5px solid #e3f0ff;
            font-size: 1.08rem;
            font-family: 'Inter', sans-serif;
            background: #f8faff;
            box-shadow: 0 2px 8px rgba(37,99,235,0.06);
            transition: border 0.2s, box-shadow 0.2s;
        }
        input[type="email"]:focus, input[type="password"]:focus {
            border-color: #4f8cff;
            box-shadow: 0 4px 16px rgba(79,140,255,0.10);
            outline: none;
        }
        button[type="submit"] {
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
            font-family: 'Inter', sans-serif;
        }
        button[type="submit"]:hover {
            background: #2563eb;
            transform: scale(1.04);
            box-shadow: 0 6px 24px rgba(79,140,255,0.18);
        }
        .error {
            color: #e53935;
            background: #ffeaea;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 8px;
            text-align: center;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
        }
        .success {
            color: #4f8cff;
            background: #e3f0ff;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 8px;
            text-align: center;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
        }
        a {
            color: #4f8cff;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }
        a:hover {
            color: #2563eb;
            text-decoration: underline;
        }
        p {
            text-align: center;
            margin-top: 12px;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
        }
    </style>
    <script src="js/main.js" defer></script>
</head>
<body>
    <div class="login-container" role="main" aria-labelledby="registroTitle">
        <h1 id="registroTitle">Registro de Paciente</h1>
        <form id="registroForm" method="POST" action="registro.php" novalidate>
            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" required aria-required="true" />
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required aria-required="true" />
            <label for="confirm_password">Confirmar contraseña:</label>
            <input type="password" id="confirm_password" name="confirm_password" required aria-required="true" />
            <?php if ($error): ?>
                <p class="error" role="alert" aria-live="assertive"><?= htmlspecialchars($error) ?></p>
            <?php elseif ($success): ?>
                <p class="success"><?= htmlspecialchars($success) ?></p>
            <?php endif; ?>
            <button type="submit" aria-label="Registrarse">Registrarse</button>
        </form>
        <p><a href="index.php">Volver al login</a></p>
    </div>
</body>
</html>
