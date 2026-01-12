<?php
session_start();
include 'conexion.php';

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$data = json_decode(file_get_contents('php://input'), true);

// Si no es JSON, intentar leer POST (para subida de archivos con FormData)
if (is_null($data) && isset($_POST['action'])) {
    $data = $_POST;
}

$action = $data['action'] ?? '';

$response = ['status' => 'error', 'message' => 'Acción no válida'];

if ($action === 'add') {
    $producto = $data['producto'];
    $precio = $data['precio'];
    $imagen = $data['imagen'];
    $cantidad = isset($data['cantidad']) ? intval($data['cantidad']) : 1;
    
    // Verificar si ya existe para aumentar cantidad
    $found = false;
    foreach ($_SESSION['carrito'] as $key => $item) {
        if ($item['nombre'] === $producto) {
            $_SESSION['carrito'][$key]['cantidad'] += $cantidad;
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        $_SESSION['carrito'][] = [
            'nombre' => $producto,
            'precio' => $precio,
            'imagen' => $imagen,
            'cantidad' => $cantidad
        ];
    }
    
    // Guardar en BD si el usuario está logueado
    if (isset($_SESSION['usuario'])) {
        $cedula = $_SESSION['usuario']['cedula'];
        $cart_json = json_encode($_SESSION['carrito'], JSON_UNESCAPED_UNICODE);
        $stmt_upd = $conn->prepare("UPDATE usuarios SET carrito = ? WHERE cedula = ?");
        $stmt_upd->bind_param("ss", $cart_json, $cedula);
        $stmt_upd->execute();
        $stmt_upd->close();
    }
    
    $response = ['status' => 'success', 'cart' => $_SESSION['carrito']];

} elseif ($action === 'remove') {
    $index = $data['index'];
    if (isset($_SESSION['carrito'][$index])) {
        array_splice($_SESSION['carrito'], $index, 1);
        
        // Actualizar BD si el usuario está logueado
        if (isset($_SESSION['usuario'])) {
            $cedula = $_SESSION['usuario']['cedula'];
            $cart_json = json_encode($_SESSION['carrito'], JSON_UNESCAPED_UNICODE);
            $stmt_upd = $conn->prepare("UPDATE usuarios SET carrito = ? WHERE cedula = ?");
            $stmt_upd->bind_param("ss", $cart_json, $cedula);
            $stmt_upd->execute();
            $stmt_upd->close();
        }
    }
    $response = ['status' => 'success', 'cart' => $_SESSION['carrito']];

} elseif ($action === 'get') {
    $response = ['status' => 'success', 'cart' => $_SESSION['carrito']];

} elseif ($action === 'checkout') {
    if (!isset($_SESSION['usuario'])) {
        $response = ['status' => 'login_required'];
    } elseif (empty($_SESSION['carrito'])) {
        $response = ['status' => 'empty'];
    } else {
        // Manejo de subida de comprobante
        $comprobante_path = null;
        if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/comprobantes/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            // Generar nombre único y seguro para evitar errores de URL (espacios, tildes, etc.)
            $ext = pathinfo($_FILES['comprobante']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('pago_', true) . '.' . $ext;
            $target_file = $upload_dir . $filename;
            if (move_uploaded_file($_FILES['comprobante']['tmp_name'], $target_file)) {
                $comprobante_path = $target_file;
            }
        }

        if (!$comprobante_path) {
            $response = ['status' => 'error', 'message' => 'Es obligatorio subir el comprobante de pago.'];
        } else {
            $cedula = $_SESSION['usuario']['cedula'];
            $productos_json = json_encode($_SESSION['carrito'], JSON_UNESCAPED_UNICODE);
            $total = 0;
            foreach ($_SESSION['carrito'] as $item) {
                $total += $item['precio'] * $item['cantidad'];
            }
            
            $metodo_retiro = $_POST['metodo_retiro'] ?? 'Tienda';
            $direccion_envio = $_POST['direccion_envio'] ?? '';

            $stmt = $conn->prepare("INSERT INTO ventas (cedula_usuario, productos, total, comprobante, estado, metodo_retiro, direccion_envio) VALUES (?, ?, ?, ?, 'pendiente', ?, ?)");
            $stmt->bind_param("ssdsss", $cedula, $productos_json, $total, $comprobante_path, $metodo_retiro, $direccion_envio);
            
            if ($stmt->execute()) {
                $_SESSION['carrito'] = []; // Vaciar carrito
                
                // Vaciar carrito en BD también
                $stmt_clear = $conn->prepare("UPDATE usuarios SET carrito = NULL WHERE cedula = ?");
                $stmt_clear->bind_param("s", $cedula);
                $stmt_clear->execute();
                $stmt_clear->close();
                
                $response = ['status' => 'success', 'message' => '¡Pedido enviado! Tu pago está en revisión por el administrador.'];
            } else {
                $response = ['status' => 'error', 'message' => 'Error en la base de datos'];
            }
            $stmt->close();
        }
    }
}

// Calcular total global para devolverlo siempre
$totalGlobal = 0;
foreach ($_SESSION['carrito'] as $item) {
    $totalGlobal += $item['precio'] * $item['cantidad'];
}
$response['totalGlobal'] = $totalGlobal;

header('Content-Type: application/json');
echo json_encode($response);
?>