<?php
session_start();
require_once 'includes/conexion.php';

// Limit login attempts by IP (basic example)
$ip = $_SERVER['REMOTE_ADDR'];
$max_attempts = 5;
$lockout_time = 15 * 60; // 15 minutes

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = [];
}

if (isset($_SESSION['login_attempts'][$ip])) {
    $attempts = $_SESSION['login_attempts'][$ip]['count'];
    $last_attempt = $_SESSION['login_attempts'][$ip]['last'];
    if ($attempts >= $max_attempts && (time() - $last_attempt) < $lockout_time) {
        die("Too many login attempts. Please try again later.");
    } elseif ((time() - $last_attempt) >= $lockout_time) {
        // Reset attempts after lockout time
        $_SESSION['login_attempts'][$ip] = ['count' => 0, 'last' => time()];
    }
} else {
    $_SESSION['login_attempts'][$ip] = ['count' => 0, 'last' => time()];
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        // Check user in database
        $stmt = $pdo->prepare("SELECT id, email, password, role FROM users WHERE email = :email AND active = 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Successful login
            session_regenerate_id(true);
            $session_token = bin2hex(random_bytes(32));
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['session_token'] = $session_token;

            // Store session_token in DB
            $update = $pdo->prepare("UPDATE users SET session_token = :token WHERE id = :id");
            $update->execute(['token' => $session_token, 'id' => $user['id']]);

            // Redirect based on role
            if ($user['role'] === 'paciente') {
                header('Location: dashboard_paciente.php');
                exit;
            } elseif ($user['role'] === 'terapeuta') {
                header('Location: dashboard_terapeuta.php');
                exit;
            } elseif ($user['role'] === 'admin') {
                header('Location: admin.php');
                exit;
            } else {
                $error = 'Invalid user role.';
            }
        } else {
            // Failed login
            $_SESSION['login_attempts'][$ip]['count']++;
            $_SESSION['login_attempts'][$ip]['last'] = time();
            $error = 'Invalid email or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>NeuroTrack - Login</title>
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
            color: #4f8cff; /* Cambia a celeste */
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
            color: #2563eb;
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
            border-color: #2563eb;
            box-shadow: 0 4px 16px rgba(37,99,235,0.10);
            outline: none;
        }
        button[type="submit"] {
            background: #2563eb;
            color: #fff;
            font-size: 1.15rem;
            font-weight: 700;
            border: none;
            border-radius: 12px;
            padding: 14px 0;
            margin-top: 8px;
            box-shadow: 0 2px 12px rgba(37,99,235,0.10);
            cursor: pointer;
            transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
            font-family: 'Inter', sans-serif;
        }
        button[type="submit"]:hover {
            background: #1742a6;
            transform: scale(1.04);
            box-shadow: 0 6px 24px rgba(37,99,235,0.18);
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
            animation: shake 0.4s;
        }
        @keyframes shake {
            0% { transform: translateX(0);}
            25% { transform: translateX(-6px);}
            50% { transform: translateX(6px);}
            75% { transform: translateX(-6px);}
            100% { transform: translateX(0);}
        }
        a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }
        a:hover {
            color: #1742a6;
            text-decoration: underline;
        }
        p {
            text-align: center;
            margin-top: 12px;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
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
            body.style.setProperty('--circle-width', '320px');
            body.style.setProperty('--circle-height', '320px');
            body.style.setProperty('--circle-left', (x - 160) + 'px');
            body.style.setProperty('--circle-top', (y - 160) + 'px');
            body.style.setProperty('--circle-opacity', '0.7');
            body.style.setProperty('--circle-bg', '#b6d4fe');
            // Actualiza el pseudo-elemento
            document.body.style.setProperty('--x', x + 'px');
            document.body.style.setProperty('--y', y + 'px');
        });
    </script>
</head>
<body>
    <div class="login-container" role="main" aria-labelledby="loginTitle">
        <h1 id="loginTitle">NeuroTrack</h1>
        <form id="loginForm" method="POST" action="login.php" novalidate>
            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" required aria-required="true" />
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required aria-required="true" />
            <?php if ($error): ?>
                <p class="error" role="alert" aria-live="assertive"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <button type="submit" aria-label="Iniciar sesión">Iniciar sesión</button>
        </form>
        <p><a href="recuperar.php">¿Olvidaste tu contraseña?</a></p>
        <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
    </div>
</body>
</html>
