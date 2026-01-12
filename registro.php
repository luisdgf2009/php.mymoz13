<?php include 'conexion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Cuenta</title>
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
            padding: 20px;
        }
        .register-card {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 191, 255, 0.1);
            max-width: 450px;
            width: 100%;
        }
        /* --- SECCIÓN DEL LOGO --- */
        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo-container img {
            max-width: 120px;
            height: auto;
        }
        h2 {
            font-family: 'Syne', sans-serif;
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
            font-size: 0.9rem;
        }
        input { 
            width: 100%; 
            padding: 10px; 
            margin-bottom: 15px; 
            border: 1px solid #e0e0e0; 
            border-radius: 8px; 
            box-sizing: border-box; 
            font-family: 'Inter', sans-serif;
        }
        input:focus {
            border-color: #00BFFF;
            outline: none;
        }
        .btn-register {
            background-color: #00BFFF; /* Azul Celeste */
            color: white; border: none; cursor: pointer;
            padding: 12px; width: 100%; border-radius: 8px;
            font-weight: bold; font-size: 16px;
            margin-top: 10px;
        }
        .btn-register:hover { background-color: #009ACD; }
        .error { color: #FF00FF; font-weight: bold; text-align: center; }
        .success { color: #00BFFF; font-weight: bold; text-align: center; }
        .login-link { text-align: center; margin-top: 15px; font-size: 14px; }
        .login-link a { color: #FF00FF; text-decoration: none; font-weight: 600; }

        /* Responsive Mobile */
        @media (max-width: 480px) {
            .register-card { padding: 30px 20px; margin: 20px; }
            h2 { font-size: 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="register-card">
        <!-- LOGO -->
        <div class="logo-container">
            <img src="image/logo.jpeg" alt="Logo MYMOZ13">
        </div>

        <h2>Registro de Usuario</h2>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $cedula = $_POST['cedula'];
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $telefono = $_POST['telefono'];
        $email = $_POST['email'];
        $direccion = $_POST['direccion'];
        // Encriptamos la contraseña
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Validaciones de seguridad en el servidor (Backend)
        if (!ctype_digit($cedula) || !ctype_digit($telefono)) {
            echo "<p class='error'>Error: Cédula y Teléfono solo pueden contener números.</p>";
        } elseif (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $nombre) || !preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $apellido)) {
            echo "<p class='error'>Error: Nombre y Apellido solo pueden contener letras.</p>";
        } else {
            // Verificar si ya existe
            $check = $conn->query("SELECT cedula FROM usuarios WHERE cedula = '$cedula'");
            if ($check->num_rows > 0) {
                echo "<p class='error'>Error: Ya existe un usuario con esa cédula.</p>";
            } else {
                // Insertar
                $stmt = $conn->prepare("INSERT INTO usuarios (cedula, nombre, apellido, telefono, email, direccion, password, rol) VALUES (?, ?, ?, ?, ?, ?, ?, 'cliente')");
                $stmt->bind_param("sssssss", $cedula, $nombre, $apellido, $telefono, $email, $direccion, $password);
                
                if ($stmt->execute()) {
                    echo "<p class='success'>¡Cuenta creada exitosamente! <a href='login.php'>Iniciar Sesión</a></p>";
                } else {
                    echo "<p class='error'>Error: " . $stmt->error . "</p>";
                }
                $stmt->close();
            }
        }
    }
    ?>

    <form method="post" action="">
        <label>Cédula (Solo números):</label>
        <!-- oninput: Reemplaza cualquier cosa que NO sea número (0-9) por vacío -->
        <input type="text" name="cedula" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>

        <label>Nombre (Solo letras):</label>
        <!-- oninput: Reemplaza cualquier cosa que sea número o símbolo raro por vacío -->
        <input type="text" name="nombre" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')" required>

        <label>Apellido (Solo letras):</label>
        <input type="text" name="apellido" oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')" required>

        <label>Teléfono (Solo números):</label>
        <input type="text" name="telefono" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>

        <label>Correo Electrónico:</label>
        <input type="email" name="email" required>

        <label>Dirección:</label>
        <input type="text" name="direccion" required>

        <label>Contraseña:</label>
        <input type="password" name="password" required>

        <input type="submit" value="Registrarse" class="btn-register">
    </form>
    
    <div class="login-link">
        <a href="login.php">¿Ya tienes cuenta? Inicia Sesión</a>
    </div>
    <a href="principal.php" style="display:block; margin-top:20px; color:#00BFFF; text-decoration:none; font-size: 14px; text-align: center;">← Volver a la Tienda</a>
    </div>
</body>
</html>