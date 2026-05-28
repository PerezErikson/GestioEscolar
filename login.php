<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión Escolar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f7fa;
        }
        .login-container {
            max-width: 900px;
            margin: 80px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            display: flex;
            overflow: hidden;
        }
        .login-image {
            flex: 1;
            background: url('images/login_escuela.png') center/cover no-repeat;
        }
        .login-form {
            flex: 1;
            padding: 50px;
        }
        .login-form h2 {
            color: #003366;
            font-weight: 700;
        }
        .btn-primary {
            background-color: #0066ff;
            border: none;
        }
        footer {
            text-align: center;
            margin-top: 20px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-image"></div>
        <div class="login-form">
            <h2>SISTEMA DE GESTIÓN ESCOLAR</h2>
            <p>Inicio de sesión</p>
            <form action="validar_login.php" method="POST">
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo electrónico</label>
                    <input type="email" class="form-control" id="correo" name="correo" placeholder="admin@admin.com" required>
                </div>
                <div class="mb-3">
                    <label for="contraseña" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="contraseña" name="contraseña" placeholder="********" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Ingresar</button>
            </form>
            <footer>© 2026 Sistema Escolar - La Vega, RD</footer>
        </div>
    </div>
</body>
</html>


