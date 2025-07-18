<?php
require_once 'includes/conexion.php';
require_once 'vendor/autoload.php'; // PHPMailer autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $error = 'Por favor ingrese su correo electrónico.';
    } else {
        // Check if user exists and active
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND active = 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Generate token and expiry (1 hour)
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', time() + 3600);

            // Store token and expiry in DB
            $stmt = $pdo->prepare("UPDATE users SET reset_token = :token, reset_expiry = :expiry WHERE id = :id");
            $stmt->execute(['token' => $token, 'expiry' => $expiry, 'id' => $user['id']]);

            // Send email with PHPMailer
            $mail = new PHPMailer(true);
            try {
                // SMTP configuration
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'scarletgirl145@gmail.com'; // TODO: replace with your Gmail
                $mail->Password = 'iqjxlforynphlthk'; // TODO: replace with your app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('scarletgirl145@gmail.com', 'NeuroTrack');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Recuperación de contraseña NeuroTrack';
                // Enlace local para recuperación
                $resetLink = "http://localhost/proyectos/Gabriel/unidadtres/resetear.php?token=$token";
                $mail->Body = "Para restablecer su contraseña, haga clic en el siguiente enlace: <a href=\"$resetLink\">$resetLink</a>. Este enlace es válido por 1 hora.";

                $mail->send();
                $success = 'Se ha enviado un correo con instrucciones para restablecer su contraseña.';
            } catch (Exception $e) {
                $error = "No se pudo enviar el correo. Error: {$mail->ErrorInfo}";
            }
        } else {
            $error = 'Correo no encontrado o usuario inactivo.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>NeuroTrack - Recuperar Contraseña</title>
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
        input[type="email"] {
            padding: 12px 14px;
            border-radius: 10px;
            border: 1.5px solid #e3f0ff;
            font-size: 1.08rem;
            font-family: 'Inter', sans-serif;
            background: #f8faff;
            box-shadow: 0 2px 8px rgba(37,99,235,0.06);
            transition: border 0.2s, box-shadow 0.2s;
        }
        input[type="email"]:focus {
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
    <div class="login-container">
        <h1>Recuperar Contraseña</h1>
        <form id="recuperarForm" method="POST" action="recuperar.php" novalidate>
            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" required />
            <?php if ($error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php elseif ($success): ?>
                <p class="success"><?= htmlspecialchars($success) ?></p>
            <?php endif; ?>
            <button type="submit">Enviar</button>
        </form>
        <p><a href="index.php">Volver al login</a></p>
    </div>
</body>
</html>
