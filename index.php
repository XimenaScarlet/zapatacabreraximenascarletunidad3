<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>NeuroTrack - Inicio</title>
    <link rel="stylesheet" href="css/estilo.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e3f0ff 0%, #f8faff 100%);
            color: #222;
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
            transition: background 0.5s;
        }
        .navbar {
            width: 100%;
            background: rgba(37,99,235,0.95);
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
        .navbar-title {
            font-size: 2rem;
            font-weight: 900;
            letter-spacing: 1.5px;
            font-family: 'Inter', sans-serif;
        }
        .navbar-buttons {
            display: flex;
            gap: 16px;
        }
        .navbar .btn, .navbar .btn-secondary {
            padding: 10px 28px;
            font-size: 1.08rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(37,99,235,0.08);
            margin: 0;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
        }
        .navbar .btn {
            background: #fff;
            color: #2563eb;
            border: none;
        }
        .navbar .btn:hover {
            background: #e3f0ff;
            color: #1742a6;
            box-shadow: 0 4px 16px rgba(37,99,235,0.12);
        }
        .navbar .btn-secondary {
            background: transparent;
            color: #fff;
            border: 2px solid #fff;
        }
        .navbar .btn-secondary:hover {
            background: #fff;
            color: #2563eb;
            box-shadow: 0 4px 16px rgba(37,99,235,0.12);
        }
        .main-content {
            padding-top: 96px;
        }
        .hero-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 48px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 64px 40px 40px 40px;
            background: rgba(255,255,255,0.85);
            border-radius: 32px;
            box-shadow: 0 8px 48px rgba(37,99,235,0.10);
            backdrop-filter: blur(2px);
            animation: fadeIn 1s ease;
        }
        .hero-content {
            flex: 1 1 520px;
        }
        .hero-title {
            font-size: 2.8rem;
            font-weight: 900;
            margin-bottom: 22px;
            color: #2563eb;
            font-family: 'Inter', sans-serif;
            letter-spacing: 1px;
        }
        .hero-desc {
            font-size: 1.22rem;
            margin-bottom: 32px;
            color: #222;
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
        }
        .hero-actions {
            display: flex;
            gap: 22px;
            margin-bottom: 18px;
        }
        .hero-actions .btn, .hero-actions .btn-secondary {
            font-size: 1.08rem;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            padding: 12px 32px;
        }
        .hero-img {
            flex: 1 1 340px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .hero-img img {
            width: 340px;
            max-width: 100%;
            border-radius: 24px;
            box-shadow: 0 8px 48px rgba(37,99,235,0.12);
            animation: fadeInSection 1.2s;
            background: rgba(255,255,255,0.7);
        }
        .features-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 0 0 0;
            text-align: center;
        }
        .features-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 12px;
            font-family: 'Inter', sans-serif;
            color: #2563eb;
        }
        .features-desc {
            color: #555;
            font-size: 1.15rem;
            margin-bottom: 40px;
            font-family: 'Inter', sans-serif;
        }
        .cards-section {
            display: flex;
            gap: 32px;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 56px;
        }
        .card {
            flex: 1 1 260px;
            background: rgba(255,255,255,0.7);
            border-radius: 24px;
            padding: 36px 28px;
            box-shadow: 0 4px 32px rgba(37,99,235,0.08);
            transition: box-shadow 0.3s, transform 0.3s, background 0.3s;
            animation: fadeInSection 1.2s;
            cursor: pointer;
            max-width: 340px;
            border: 1.5px solid #e3f0ff;
            backdrop-filter: blur(2px);
        }
        .card:hover {
            box-shadow: 0 8px 32px rgba(37,99,235,0.18);
            transform: translateY(-6px) scale(1.04);
            background: #e3f0ff;
            border-color: #b6d4fe;
        }
        .card h2 {
            color: #2563eb;
            margin-bottom: 16px;
            font-size: 1.35rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
        }
        .card p {
            font-size: 1.07rem;
            color: #222;
            font-family: 'Inter', sans-serif;
            line-height: 1.5;
        }
        .cta-section {
            background: linear-gradient(90deg, #2563eb 60%, #4f8cff 100%);
            color: #fff;
            text-align: center;
            padding: 64px 0 56px 0;
            margin-top: 40px;
            border-radius: 32px;
            box-shadow: 0 8px 48px rgba(37,99,235,0.10);
        }
        .cta-title {
            font-size: 2rem;
            font-weight: 900;
            margin-bottom: 18px;
            font-family: 'Inter', sans-serif;
        }
        .cta-desc {
            font-size: 1.18rem;
            margin-bottom: 32px;
            font-family: 'Inter', sans-serif;
        }
        .cta-btn {
            background: #fff;
            color: #2563eb;
            border: none;
            border-radius: 14px;
            padding: 16px 44px;
            font-size: 1.18rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 24px rgba(37,99,235,0.12);
            transition: background 0.3s, color 0.3s, box-shadow 0.2s;
            text-decoration: none;
            font-family: 'Inter', sans-serif;
        }
        .cta-btn:hover {
            background: #e3f0ff;
            color: #1742a6;
            box-shadow: 0 8px 32px rgba(37,99,235,0.18);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px);}
            to { opacity: 1; transform: translateY(0);}
        }
        @keyframes fadeInSection {
            from { opacity: 0; transform: scale(0.95);}
            to { opacity: 1; transform: scale(1);}
        }
        @media (max-width: 1200px) {
            .hero-section, .features-section {
                max-width: 98vw;
                padding-left: 12px;
                padding-right: 12px;
            }
        }
        @media (max-width: 900px) {
            .hero-section, .cards-section {
                flex-direction: column;
                gap: 22px;
            }
            .hero-img {
                margin-top: 22px;
            }
        }
        @media (max-width: 600px) {
            .navbar {
                padding: 0 12px;
                height: 62px;
            }
            .main-content {
                padding-top: 72px;
            }
            .hero-section {
                padding: 24px 8px 16px 8px;
                border-radius: 18px;
            }
            .features-section {
                padding: 18px 0 0 0;
            }
            .cta-section {
                padding: 32px 0 28px 0;
                border-radius: 18px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-title">NeuroTrack</div>
        <div class="navbar-buttons">
            <a href="login.php" class="btn">Iniciar Sesión</a>
            <a href="registro.php" class="btn-secondary">Registrarse</a>
        </div>
    </nav>
    <div class="main-content">
        <section class="hero-section">
            <div class="hero-content">
                <div class="hero-title">Tu bienestar mental comienza aquí</div>
                <div class="hero-desc">
                    Descubre cómo NeuroTrack te ayuda a registrar tus emociones, conectar con tu terapeuta y mejorar tu salud mental.<br>
                    Lleva un seguimiento diario, recibe recomendaciones y accede a herramientas para tu bienestar.
                </div>
                <div class="hero-actions">
                    <a href="registro.php" class="btn">Comenzar Gratis</a>
                    <a href="#features" class="btn btn-secondary">Ver Características</a>
                </div>
            </div>
            <div class="hero-img">
                <img src="https://cdn-icons-png.flaticon.com/512/2991/2991148.png" alt="Cuaderno NeuroTrack" />
            </div>
        </section>
        <section class="features-section" id="features">
            <div class="features-title">Características principales</div>
            <div class="features-desc">
                Todo lo que necesitas para cuidar tu salud mental en una sola plataforma.
            </div>
            <div class="cards-section">
                <div class="card">
                    <h2>Registro Emocional</h2>
                    <p>
                        Anota tus emociones y actividades cada día para llevar un control de tu bienestar y detectar patrones importantes.
                    </p>
                </div>
                <div class="card">
                    <h2>Seguimiento Terapéutico</h2>
                    <p>
                        Permite a los terapeutas monitorear el progreso de sus pacientes y brindar un acompañamiento personalizado.
                    </p>
                </div>
                <div class="card">
                    <h2>Gestión de Usuarios</h2>
                    <p>
                        Los administradores pueden gestionar cuentas, asignar terapeutas y mantener la seguridad de la información.
                    </p>
                </div>
                <div class="card">
                    <h2>Comunicación Segura</h2>
                    <p>
                        Facilita la comunicación entre paciente y terapeuta de forma privada y protegida.
                    </p>
                </div>
                <div class="card">
                    <h2>Visualización de Progreso</h2>
                    <p>
                        Gráficas y reportes para ver tu evolución emocional y compartirla con tu terapeuta.
                    </p>
                </div>
                <div class="card">
                    <h2>Privacidad y Seguridad</h2>
                    <p>
                        Tus datos están protegidos con los más altos estándares de seguridad y privacidad.
                    </p>
                </div>
            </div>
        </section>
        <section class="cta-section">
            <div class="cta-title">¿Listo para mejorar tu salud mental?</div>
            <div class="cta-desc">
                Únete a NeuroTrack y comienza a transformar tu bienestar con ayuda profesional y herramientas inteligentes.
            </div>
            <a href="registro.php" class="cta-btn">Crear mi cuenta gratis</a>
        </section>
    </div>
</body>
</html>
