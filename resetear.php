<?php
session_start();
require_once 'includes/conexion.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

if (!$token) {
    die('Token inválido o no proporcionado.');
}

// Verify token
$stmt = $pdo->prepare("SELECT id, email FROM users WHERE reset_token = :token AND reset_expires > NOW()");
$stmt->execute(['token' => $token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('Token inválido o expirado.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($password) || empty($confirm_password)) {
        $error = 'Por favor complete todos los campos.';
    } elseif ($password !== $confirm_password) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (strlen($password) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres.';
    } else {
        // Update password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = :password, reset_token = NULL, reset_expires = NULL WHERE id = :id");
        $stmt->execute([
            'password' => $hashed_password,
            'id' => $user['id']
        ]);
        
        $success = 'Contraseña actualizada exitosamente. <a href="login.php">Iniciar sesión</a>';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>NeuroTrack - Restablecer Contraseña</title>
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
        input[type="password"] {
            padding: 12px 14px;
            border-radius: 10px;
            border: 1.5px solid #e3f0ff;
            font-size: 1.08rem;
            font-family: 'Inter', sans-serif;
            background: #f8faff;
            box-shadow: 0 2px 8px rgba(37,99,235,0.06);
            transition: border 0.2s, box-shadow 0.2s;
        }
        input[type="password"]:focus {
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
    <div class="login-container" role="main" aria-labelledby="resetTitle">
        <h1 id="resetTitle">Restablecer Contraseña</h1>
        <?php if ($success): ?>
            <p class="success" role="alert"><?= $success ?></p>
        <?php else: ?>
            <form method="POST" action="resetear.php?token=<?= htmlspecialchars($token) ?>" novalidate>
                <label for="password">Nueva contraseña:</label>
                <input type="password" id="password" name="password" required minlength="8" aria-required="true" />
                <label for="confirm_password">Confirmar nueva contraseña:</label>
                <input type="password" id="confirm_password" name="confirm_password" required aria-required="true" />
                <?php if ($error): ?>
                    <p class="error" role="alert" aria-live="assertive"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>
                <button type="submit">Restablecer contraseña</button>
            </form>
        <?php endif; ?>
        <p><a href="login.php">Volver al inicio de sesión</a></p>
    </div>
</body>
</html>
