<?php include 'conexion.php'; session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <!-- Estilos y Fuentes del sitio principal -->
    <link href="https://cdn.prod.website-files.com/6963d93b52120388fb7edc0c/css/mymoz13.webflow.shared.19c81f106.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js" type="text/javascript"></script>
    <script type="text/javascript">WebFont.load({  google: {    families: ["Inter:regular","Syne:regular"]  }});</script>

    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: white; /* Fondo blanco */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-card { 
            background: white; 
            padding: 40px; 
            border-radius: 16px; 
            box-shadow: 0 10px 30px rgba(0, 191, 255, 0.1); 
            max-width: 380px; 
            width: 100%; 
            border: 1px solid rgba(0,0,0,0.05);
            text-align: center;
        }
        /* --- SECCIÓN DEL LOGO --- */
        .logo-container {
            margin-bottom: 25px;
        }
        .logo-container img {
            max-width: 150px;
            height: auto;
            /* Estilo temporal para el placeholder */
            border-radius: 8px;
        }
        h2 {
            font-family: 'Syne', sans-serif;
            color: #333;
            margin-bottom: 25px;
            font-weight: 700;
        }
        label {
            display: block;
            text-align: left;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        input { 
            width: 100%; 
            padding: 12px; 
            margin-bottom: 20px; 
            border: 1px solid #e0e0e0; 
            border-radius: 8px; 
            box-sizing: border-box; 
            font-family: 'Inter', sans-serif;
            transition: border-color 0.3s;
            color: #333;
            background-color: #ffffff;
        }
        input:focus {
            border-color: #00BFFF; /* Azul Celeste */
            outline: none;
        }
        .btn-login {
            background-color: #00BFFF; /* Azul Celeste */
            color: white;
            border: none;
            padding: 14px;
            width: 100%;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .btn-login:hover {
            background-color: #009ACD;
        }
        .link-register {
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
        .link-register a {
            color: #FF00FF; /* Fucsia */
            text-decoration: none;
            font-weight: 600;
        }
        .link-register a:hover {
            text-decoration: underline;
        }
        .msg-error { color: #FF00FF; margin-bottom: 15px; font-weight: 500; }

        /* Responsive Mobile */
        @media (max-width: 480px) {
            .login-card { padding: 30px 20px; margin: 20px; }
            h2 { font-size: 1.5rem; }
        }
    </style>
</head>
<body>

    <?php
    // Lógica de Logout
    if (isset($_GET['logout'])) {
        session_destroy();
        header("Location: login.php");
        exit;
    }

    // Lógica de Login
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $cedula = $_POST['cedula'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE cedula = ?");
        $stmt->bind_param("s", $cedula);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Verificar contraseña encriptada
            if (password_verify($password, $row['password'])) {
                $_SESSION['usuario'] = $row; // Guardamos todos los datos en sesión
                
                // --- RECUPERAR CARRITO GUARDADO ---
                $db_cart = !empty($row['carrito']) ? json_decode($row['carrito'], true) : [];
                $session_cart = $_SESSION['carrito'] ?? [];
                
                // Lógica de fusión:
                // 1. Si hay carrito en BD, lo traemos. Si también había cosas en la sesión (invitado), las unimos.
                // 2. Si no hay en BD pero sí en sesión, guardamos lo de la sesión en la BD.
                if (!empty($db_cart)) {
                    if (!empty($session_cart)) {
                        $_SESSION['carrito'] = array_merge($db_cart, $session_cart);
                    } else {
                        $_SESSION['carrito'] = $db_cart;
                    }
                    // Actualizar la BD con la versión fusionada
                    $new_cart_json = json_encode($_SESSION['carrito'], JSON_UNESCAPED_UNICODE);
                    $conn->query("UPDATE usuarios SET carrito = '$new_cart_json' WHERE cedula = '{$row['cedula']}'");
                } elseif (!empty($session_cart)) {
                    $new_cart_json = json_encode($session_cart, JSON_UNESCAPED_UNICODE);
                    $conn->query("UPDATE usuarios SET carrito = '$new_cart_json' WHERE cedula = '{$row['cedula']}'");
                }

                // Redirección según rol
                if (isset($row['rol']) && $row['rol'] === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: principal.php");
                }
                exit;
            } else {
                $error = "❌ Contraseña incorrecta.";
            }
        } else {
            $error = "❌ Usuario no encontrado.";
        }
        $stmt->close();
    }
    ?>

    <?php if (!isset($_SESSION['usuario'])): ?>
        <div class="login-card">
            <!-- AQUÍ VA TU LOGO -->
            <div class="logo-container">
                <img src="image/logo.jpeg" alt="Logo MYMOZ13">
            </div>

            <h2>Iniciar Sesión</h2>
            
            <?php if(isset($error)) echo "<p class='msg-error'>$error</p>"; ?>

            <form method="post" action="">
                <label>Cédula:</label>
                <input type="text" name="cedula" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                
                <label>Contraseña:</label>
                <input type="password" name="password" required>
                
                <input type="submit" value="Ingresar" class="btn-login">
            </form>
            <p class="link-register">¿No tienes una cuenta? <a href="registro.php">Créala aquí</a></p>
            <a href="principal.php" style="display:block; margin-top:20px; color:#00BFFF; text-decoration:none; font-size: 14px;">← Volver a la Tienda</a>
        </div>
    <?php endif; ?>

</body>
</html>