<?php
session_start();
include 'conexion.php';

// Verificar seguridad: Solo admin puede entrar
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// --- LÓGICA DE ACCIONES (POST) ---

// 1. Eliminar Producto
if (isset($_GET['delete_prod'])) {
    $id = intval($_GET['delete_prod']);
    $conn->query("DELETE FROM productos WHERE id=$id");
    header("Location: admin.php#productos");
    exit;
}

// 2. Obtener datos para editar
$prod_edit = null;
if (isset($_GET['edit_prod'])) {
    $id_edit = intval($_GET['edit_prod']);
    $res_edit = $conn->query("SELECT * FROM productos WHERE id=$id_edit");
    $prod_edit = $res_edit->fetch_assoc();
}

// 3. Gestionar Estado de Ventas (Aprobar/Rechazar)
if (isset($_GET['action']) && isset($_GET['id_venta'])) {
    $id_venta = intval($_GET['id_venta']);
    $accion = $_GET['action'];
    
    if ($accion === 'approve') {
        $conn->query("UPDATE ventas SET estado='aprobado' WHERE id=$id_venta");
    } elseif ($accion === 'reject' && isset($_GET['reason'])) {
        $reason = $conn->real_escape_string($_GET['reason']);
        $conn->query("UPDATE ventas SET estado='rechazado', motivo_rechazo='$reason' WHERE id=$id_venta");
    }
    header("Location: admin.php");
    exit;
}

// 3. Guardar Producto (Agregar o Editar)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['add_product']) || isset($_POST['update_product']))) {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $categoria = $_POST['categoria'];
    $descripcion = $_POST['descripcion'];
    $tallas = $_POST['tallas'];
    $stock = intval($_POST['stock']);
    // Nuevos campos de oferta
    $en_oferta = isset($_POST['en_oferta']) ? 1 : 0;
    $porcentaje_oferta = intval($_POST['porcentaje_oferta']);
    // Nuevos campos de detalle
    $colores = $_POST['colores'];
    $stock_por_color = null;

    // Procesar Stock por Color si se enviaron datos
    if (isset($_POST['color_nombre']) && is_array($_POST['color_nombre'])) {
        $stock_map = [];
        $calc_stock = 0;
        $has_colors = false;
        
        for($i=0; $i<count($_POST['color_nombre']); $i++) {
            $c_nom = trim($_POST['color_nombre'][$i]);
            $c_cant = intval($_POST['color_cantidad'][$i]);
            if(!empty($c_nom)) {
                $stock_map[$c_nom] = $c_cant;
                $calc_stock += $c_cant;
                $has_colors = true;
            }
        }
        
        if ($has_colors) {
            // Guardamos el JSON y actualizamos el stock total y el string de colores automáticamente
            $stock_por_color = json_encode($stock_map, JSON_UNESCAPED_UNICODE);
            $colores = implode(", ", array_keys($stock_map));
            $stock = $calc_stock;
        }
    }
    
    // Manejo de subida de imágenes
    $imagenes_urls = [];
    $target_dir = "uploads/";
    
    // Crear carpeta si no existe
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (isset($_FILES['imagen'])) {
        $total_files = count($_FILES['imagen']['name']);
        for ($i = 0; $i < $total_files; $i++) {
            $filename = time() . "_" . basename($_FILES['imagen']['name'][$i]);
            $target_file = $target_dir . $filename;
            if (move_uploaded_file($_FILES['imagen']['tmp_name'][$i], $target_file)) {
                $imagenes_urls[] = $target_file;
            }
        }
    }
    $imagen_db = implode(",", $imagenes_urls);

    if (isset($_POST['update_product'])) {
        $id = $_POST['id'];
        // Si no se subieron nuevas imágenes, mantener las anteriores
        if (empty($imagen_db)) {
            $imagen_db = $_POST['imagen_actual'];
        }
        
        $stmt = $conn->prepare("UPDATE productos SET nombre=?, precio=?, imagen=?, categoria=?, descripcion=?, tallas=?, stock=?, en_oferta=?, porcentaje_oferta=?, colores=?, stock_por_color=? WHERE id=?");
        $stmt->bind_param("sdssssiiisss", $nombre, $precio, $imagen_db, $categoria, $descripcion, $tallas, $stock, $en_oferta, $porcentaje_oferta, $colores, $stock_por_color, $id);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $conn->prepare("INSERT INTO productos (nombre, precio, imagen, categoria, descripcion, tallas, stock, en_oferta, porcentaje_oferta, colores, stock_por_color) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdssssiisss", $nombre, $precio, $imagen_db, $categoria, $descripcion, $tallas, $stock, $en_oferta, $porcentaje_oferta, $colores, $stock_por_color);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: admin.php#productos");
    exit;
}

// 4. Actualizar Secciones de Portada
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_seccion'])) {
    $id = intval($_POST['id']);
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $filtro = $_POST['filtro'];
    
    // Manejo de imágenes (Múltiples)
    $imagen_db = $_POST['imagen_actual'];
    $imagenes_urls = [];
    $target_dir = "uploads/";
    
    if (isset($_FILES['imagen']) && count($_FILES['imagen']['name']) > 0 && !empty($_FILES['imagen']['name'][0])) {
        $total_files = count($_FILES['imagen']['name']);
        for ($i = 0; $i < $total_files; $i++) {
            if ($_FILES['imagen']['error'][$i] === UPLOAD_ERR_OK) {
                $filename = time() . "_banner_" . $i . "_" . basename($_FILES['imagen']['name'][$i]);
                $target_file = $target_dir . $filename;
                if (move_uploaded_file($_FILES['imagen']['tmp_name'][$i], $target_file)) {
                    $imagenes_urls[] = $target_file;
                }
            }
        }
        if (!empty($imagenes_urls)) {
            $imagen_db = implode(",", $imagenes_urls);
        }
    }
    
    $stmt = $conn->prepare("UPDATE secciones_home SET titulo=?, descripcion=?, imagen=?, filtro=? WHERE id=?");
    $stmt->bind_param("ssssi", $titulo, $descripcion, $imagen_db, $filtro, $id);
    $stmt->execute();
    header("Location: admin.php#secciones"); // Volver a la misma sección (requiere JS para abrirla, ver abajo)
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - MYMOZ13</title>
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Syne:wght@700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #111;
            color: #e0e0e0;
            margin: 0;
            display: flex;
            min-height: 100vh;
        }
        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #1a1a1a;
            border-right: 1px solid #333;
            padding: 20px;
            position: fixed;
            height: 100%;
        }
        .brand {
            font-family: 'Syne', sans-serif;
            font-size: 1.5rem;
            color: #fff;
            margin-bottom: 40px;
            display: block;
            text-decoration: none;
        }
        .menu-item {
            display: block;
            padding: 15px;
            color: #aaa;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: 0.3s;
        }
        .menu-item:hover, .menu-item.active {
            background-color: #333;
            color: #00BFFF;
        }
        .logout {
            margin-top: auto;
            color: #FF00FF;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 40px;
            width: 100%;
        }
        h2 { font-family: 'Syne', sans-serif; color: #fff; border-bottom: 1px solid #333; padding-bottom: 15px; }
        
        /* Tables */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #1e1e1e; border-radius: 10px; overflow: hidden; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #333; }
        th { background-color: #252525; color: #fff; }
        tr:hover { background-color: #2a2a2a; }
        
        /* Forms */
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #aaa; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; padding: 10px; background: #2a2a2a; border: 1px solid #444; color: #fff; border-radius: 5px;
            box-sizing: border-box; /* Ajuste clave para móviles: evita que se salga de la pantalla */
            font-family: 'Inter', sans-serif;
        }
        .btn {
            padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;
        }
        .btn-primary { background-color: #00BFFF; color: white; }
        .btn-danger { background-color: #ff4444; color: white; text-decoration: none; font-size: 0.8rem; padding: 5px 10px; }
        
        /* Cards */
        .card { background: #1e1e1e; padding: 20px; border-radius: 10px; margin-bottom: 30px; border: 1px solid #333; }
        
        .status-badge {
            background: #00BFFF; color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;
        }

        /* Responsive Mobile */
        @media (max-width: 768px) {
            body { flex-direction: column; }
            .sidebar { 
                width: 100%; 
                height: auto; 
                position: sticky; /* Menú fijo arriba */
                top: 0;
                z-index: 1000;
                padding: 10px 15px;
                box-sizing: border-box;
                display: flex;
                flex-wrap: nowrap; /* No envolver */
                overflow-x: auto; /* Scroll horizontal suave */
                justify-content: flex-start;
                gap: 10px;
                background: #1a1a1a;
                border-bottom: 1px solid #333;
                border-right: none;
                box-shadow: 0 4px 15px rgba(0,0,0,0.5);
                -webkit-overflow-scrolling: touch; /* Scroll nativo en iOS */
            }
            .sidebar::-webkit-scrollbar { display: none; } /* Ocultar barra scroll */
            
            .brand { display: none; } /* Ocultar logo texto para ganar espacio */
            
            .menu-item { 
                display: inline-flex; 
                align-items: center;
                margin: 0; 
                font-size: 0.85rem; 
                white-space: nowrap; 
                padding: 8px 15px;
                background: #252525;
                border: 1px solid #333;
                border-radius: 20px; /* Botones redondeados */
            }
            .menu-item.active { background: #00BFFF; color: white; border-color: #00BFFF; }
            .logout { margin-top: 0; color: #ff4444; border-color: #ff4444; }

            .main-content { margin-left: 0; width: 100%; padding: 15px; box-sizing: border-box; }
            
            /* --- TABLAS RESPONSIVAS TIPO TARJETA --- */
            table, thead, tbody, th, td, tr { display: block; }
            thead tr { position: absolute; top: -9999px; left: -9999px; } /* Ocultar cabecera */
            
            tr { 
                margin-bottom: 20px; 
                border: 1px solid #333; 
                border-radius: 12px; 
                background: #222; 
                padding: 15px;
                box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            }
            
            td { 
                border: none; 
                border-bottom: 1px solid #333; 
                position: relative; 
                padding: 12px 0; 
                text-align: right; /* Contenido a la derecha */
                display: flex; 
                justify-content: space-between; 
                align-items: center;
                font-size: 0.95rem;
            }
            
            /* Usamos data-label para poner el título a la izquierda */
            td:before { 
                content: attr(data-label); 
                font-weight: 700; 
                color: #00BFFF; 
                margin-right: auto;
                text-transform: uppercase;
                font-size: 0.75rem;
                letter-spacing: 0.5px;
            }
            
            td:last-child { border-bottom: none; padding-bottom: 0; }
            td img { height: 60px !important; width: 60px !important; border-radius: 8px; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <a href="principal.php" class="brand">MYMOZ13 Admin</a>
        <a href="#ventas" class="menu-item active" onclick="showSection('ventas')">📦 Ventas</a>
        <a href="#productos" class="menu-item" onclick="showSection('productos')">👕 Productos</a>
        <a href="#secciones" class="menu-item" onclick="showSection('secciones')">🏠 Portada</a>
        <a href="principal.php" class="menu-item">🏠 Ir a Tienda</a>
        <a href="login.php?logout=true" class="menu-item logout">Cerrar Sesión</a>
    </div>

    <div class="main-content">
        
        <!-- SECCIÓN VENTAS -->
        <div id="section-ventas">
            <h2>Historial de Pedidos</h2>
            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario (Cédula)</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Envío/Retiro</th>
                            <th>Total</th>
                            <th>Detalles</th>
                            <th>Comprobante / Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql_ventas = "SELECT * FROM ventas ORDER BY fecha DESC";
                        $res_ventas = $conn->query($sql_ventas);
                        if ($res_ventas->num_rows > 0) {
                            while ($venta = $res_ventas->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td data-label='ID'>#{$venta['id']}</td>";
                                echo "<td data-label='Usuario'>{$venta['cedula_usuario']}</td>";
                                echo "<td data-label='Fecha'>{$venta['fecha']}</td>";
                                
                                // Estado con colores
                                $color_estado = '#aaa';
                                if ($venta['estado'] == 'pendiente') $color_estado = '#FFD700'; // Dorado
                                if ($venta['estado'] == 'aprobado') $color_estado = '#00FF00'; // Verde
                                if ($venta['estado'] == 'rechazado') $color_estado = '#FF0000'; // Rojo
                                echo "<td data-label='Estado' style='color:$color_estado; font-weight:bold; text-transform:uppercase;'>{$venta['estado']}</td>";
                                
                                // Columna de Envío
                                echo "<td data-label='Envío/Retiro'>";
                                echo "<span style='font-weight:bold; color:#fff;'>{$venta['metodo_retiro']}</span>";
                                if (!empty($venta['direccion_envio'])) {
                                    echo "<br><span style='font-size:0.8rem; color:#aaa;'>Dir: {$venta['direccion_envio']}</span>";
                                }
                                echo "</td>";

                                echo "<td data-label='Total' style='color:#00BFFF; font-weight:bold;'>$" . number_format($venta['total'], 2) . "</td>";
                                
                                // Parsear JSON de productos
                                $prods = json_decode($venta['productos'], true);
                                $detalles = "";
                                foreach ($prods as $p) {
                                    $detalles .= "• {$p['nombre']} (x{$p['cantidad']})<br>";
                                }
                                echo "<td data-label='Detalles' style='font-size:0.9rem; color:#aaa; display:block; text-align:right;'>$detalles</td>";
                                
                                // Columna de Acciones y Comprobante
                                echo "<td data-label='Acciones' style='display:block;'>";
                                if ($venta['comprobante']) {
                                    // Codificar URL para evitar errores con espacios en archivos antiguos
                                    $url_comprobante = str_replace(' ', '%20', htmlspecialchars($venta['comprobante']));
                                    echo "<a href='$url_comprobante' target='_blank' style='color:#00BFFF; text-decoration:underline; display:block; margin-bottom:5px;'>Ver Comprobante</a>";
                                } else {
                                    echo "<span style='color:#666;'>Sin comprobante</span><br>";
                                }

                                if ($venta['estado'] == 'pendiente') {
                                    echo "<div style='display:flex; flex-direction:column; gap:8px; margin-top:8px;'>";
                                    echo "<a href='admin.php?action=approve&id_venta={$venta['id']}' class='btn btn-primary' style='padding:8px 12px; font-size:0.85rem; text-align:center; text-decoration:none; border-radius:6px; display:block;'>✓ Aprobar</a>";
                                    echo "<button onclick='rechazarPedido({$venta['id']})' class='btn btn-danger' style='padding:8px 12px; font-size:0.85rem; width:100%; cursor:pointer; border-radius:6px; border:none;'>✗ Rechazar</button>";
                                    echo "</div>";
                                } elseif ($venta['estado'] == 'rechazado') {
                                    echo "<span style='font-size:0.8rem; color:#ff4444;'>Motivo: {$venta['motivo_rechazo']}</span>";
                                }
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align:center;'>No hay ventas registradas.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SECCIÓN PRODUCTOS -->
        <div id="section-productos" style="display:none;">
            <h2>Gestión de Productos</h2>
            
            <!-- Formulario Agregar -->
            <div class="card">
                <h3 style="margin-top:0;"><?php echo $prod_edit ? 'Editar Producto' : 'Agregar Nuevo Producto'; ?></h3>
                <form method="POST" action="" enctype="multipart/form-data">
                    <?php if ($prod_edit): ?>
                        <input type="hidden" name="update_product" value="1">
                        <input type="hidden" name="id" value="<?php echo $prod_edit['id']; ?>">
                        <input type="hidden" name="imagen_actual" value="<?php echo htmlspecialchars($prod_edit['imagen']); ?>">
                    <?php else: ?>
                        <input type="hidden" name="add_product" value="1">
                    <?php endif; ?>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Nombre del Producto</label>
                            <input type="text" name="nombre" required placeholder="Ej. Camisa Negra" value="<?php echo $prod_edit ? htmlspecialchars($prod_edit['nombre']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Precio ($)</label>
                            <input type="number" step="0.01" name="precio" required placeholder="0.00" value="<?php echo $prod_edit ? $prod_edit['precio'] : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Stock Total (Se calcula solo si agregas colores abajo)</label>
                            <input type="number" name="stock" id="total_stock" required placeholder="0" value="<?php echo $prod_edit ? $prod_edit['stock'] : '0'; ?>">
                        </div>
                </div>

                <!-- Sección de Ofertas -->
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px; padding: 15px; background: #252525; border-radius: 8px; border: 1px solid #333;">
                    <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" name="en_oferta" id="check_oferta" value="1" <?php echo ($prod_edit && $prod_edit['en_oferta']) ? 'checked' : ''; ?> style="width: auto; transform: scale(1.5);">
                        <label for="check_oferta" style="margin:0; cursor:pointer; color: #FFD700; font-weight: bold;">🔥 ¿Producto en Oferta?</label>
                    </div>
                    <div class="form-group">
                        <label>Porcentaje de Descuento (%)</label>
                        <select name="porcentaje_oferta" style="background: #2a2a2a; color: white; border: 1px solid #444; padding: 10px; border-radius: 5px; width: 100%;">
                            <option value="0">Sin descuento</option>
                            <?php
                            for($i=5; $i<=90; $i+=5) {
                                $sel = ($prod_edit && $prod_edit['porcentaje_oferta'] == $i) ? 'selected' : '';
                                echo "<option value='$i' $sel>$i% OFF</option>";
                            }
                            ?>
                        </select>
                    </div>
                    </div>

                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="descripcion" rows="6" style="resize: vertical;" placeholder="Descripción del producto..."><?php echo $prod_edit ? htmlspecialchars($prod_edit['descripcion']) : ''; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Imágenes del Producto <?php echo $prod_edit ? '(Dejar vacío para mantener actuales)' : '(Seleccionar varias)'; ?></label>
                        <input type="file" name="imagen[]" multiple accept="image/*" <?php echo $prod_edit ? '' : 'required'; ?> style="padding: 10px; background: #2a2a2a; color: white; border: 1px solid #444; width: 100%;">
                    </div>
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Categoría</label>
                        <select name="categoria">
                            <?php 
                            $cats = ['General', 'Damas', 'Caballeros', 'Niños', 'Lencería'];
                            foreach($cats as $c) {
                                $selected = ($prod_edit && $prod_edit['categoria'] == $c) ? 'selected' : '';
                                echo "<option value='$c' $selected>$c</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tallas Disponibles (Separar por comas)</label>
                        <input type="text" name="tallas" placeholder="Ej. S,M,L,XL" value="<?php echo $prod_edit ? htmlspecialchars($prod_edit['tallas']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label style="color:#00BFFF; font-weight:bold;">Gestión de Stock por Color</label>
                        <p style="font-size:0.8rem; color:#888; margin-top:0;">Agrega los colores y su cantidad. El sistema sumará el total automáticamente.</p>
                        
                        <div id="colors-container">
                            <?php 
                            $has_color_data = false;
                            if ($prod_edit && !empty($prod_edit['stock_por_color'])) {
                                $stock_map = json_decode($prod_edit['stock_por_color'], true);
                                if (is_array($stock_map)) {
                                    $has_color_data = true;
                                    foreach ($stock_map as $color => $qty) {
                                        echo '<div class="color-row" style="display:flex; gap:10px; margin-bottom:10px;">
                                            <input type="text" name="color_nombre[]" placeholder="Color (Ej: Rojo)" value="'.htmlspecialchars($color).'" style="flex:2;">
                                            <input type="number" name="color_cantidad[]" placeholder="Cant." value="'.$qty.'" style="flex:1;" onchange="updateTotalStock()">
                                            <button type="button" class="btn-danger" onclick="this.parentElement.remove(); updateTotalStock()">X</button>
                                        </div>';
                                    }
                                }
                            }
                            ?>
                        </div>
                        <button type="button" class="btn" style="background:#333; color:white; border:1px solid #555; font-size:0.9rem;" onclick="addColorRow()">+ Agregar Color</button>
                        
                        <!-- Input oculto para compatibilidad si no se usa el gestor -->
                        <input type="hidden" name="colores" id="colores_legacy" value="<?php echo $prod_edit ? htmlspecialchars($prod_edit['colores'] ?? '') : ''; ?>">
                    </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary"><?php echo $prod_edit ? 'Actualizar Producto' : 'Guardar Producto'; ?></button>
                    <?php if ($prod_edit): ?>
                        <a href="admin.php" class="btn btn-danger" style="margin-left: 10px;">Cancelar Edición</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Lista Productos -->
            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th>Img</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Categoría</th>
                            <th>Stock</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql_prods = "SELECT * FROM productos ORDER BY id DESC";
                        $res_prods = $conn->query($sql_prods);
                        while ($prod = $res_prods->fetch_assoc()) {
                            $img_show = explode(',', $prod['imagen'])[0];
                            echo "<tr>";
                            echo "<td data-label='Imagen'><img src='$img_show' style='width:40px; height:40px; object-fit:cover; border-radius:4px;'></td>";
                            echo "<td data-label='Nombre'>{$prod['nombre']}</td>";
                            
                            // Mostrar precio con oferta si aplica
                            if ($prod['en_oferta'] && $prod['porcentaje_oferta'] > 0) {
                                $precio_desc = $prod['precio'] - ($prod['precio'] * $prod['porcentaje_oferta'] / 100);
                                echo "<td data-label='Precio'><span style='text-decoration:line-through; color:#aaa; font-size:0.8rem;'>$" . number_format($prod['precio'], 2) . "</span><br><span style='color:#00FF00; font-weight:bold;'>$" . number_format($precio_desc, 2) . "</span> <span style='font-size:0.7rem; background:#FF00FF; padding:2px 4px; border-radius:3px;'>-{$prod['porcentaje_oferta']}%</span></td>";
                            } else {
                                echo "<td data-label='Precio'>$" . number_format($prod['precio'], 2) . " BCV</td>";
                            }
                            
                            echo "<td data-label='Categoría'><span class='status-badge'>{$prod['categoria']}</span></td>";
                            echo "<td data-label='Stock'>{$prod['stock']}</td>";
                            echo "<td data-label='Acciones'>
                                    <a href='admin.php?edit_prod={$prod['id']}#productos' class='btn btn-primary' style='padding:5px 10px; font-size:0.8rem; text-decoration:none;'>Editar</a>
                                    <a href='admin.php?delete_prod={$prod['id']}' class='btn-danger' onclick='return confirm(\"¿Seguro que deseas eliminar este producto?\")'>Eliminar</a>
                                  </td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SECCIÓN PORTADA (SECCIONES HOME) -->
        <div id="section-secciones" style="display:none;">
            <h2>Gestión de Portada (Banners)</h2>
            <p style="color:#aaa; margin-bottom:20px;">Edita los textos e imágenes de los cuadros de la página principal.</p>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <?php
                $res_sec = $conn->query("SELECT * FROM secciones_home ORDER BY id ASC");
                while ($sec = $res_sec->fetch_assoc()) {
                ?>
                <div class="card">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="update_seccion" value="1">
                        <input type="hidden" name="id" value="<?php echo $sec['id']; ?>">
                        <input type="hidden" name="imagen_actual" value="<?php echo htmlspecialchars($sec['imagen']); ?>">
                        
                        <div style="margin-bottom:15px;">
                            <label style="color:#00BFFF; font-weight:bold;"><?php echo ($sec['identificador'] == 'oferta_principal') ? 'Cuadro Principal (Grande)' : 'Cuadro Secundario'; ?></label>
                        </div>

                        <div class="form-group">
                            <label>Título</label>
                            <input type="text" name="titulo" value="<?php echo htmlspecialchars($sec['titulo']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea name="descripcion" rows="4" style="resize: vertical;"><?php echo htmlspecialchars($sec['descripcion']); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Filtro de Búsqueda (Al hacer clic)</label>
                            <input type="text" name="filtro" value="<?php echo htmlspecialchars($sec['filtro']); ?>" placeholder="Ej: Damas, Caballeros...">
                        </div>

                        <div class="form-group">
                            <label>Imágenes de Fondo (Puedes seleccionar varias)</label>
                            <div style="display:flex; gap:5px; overflow-x:auto; margin-bottom:10px;">
                                <?php 
                                $imgs = explode(',', $sec['imagen']);
                                foreach($imgs as $img) echo "<img src='".htmlspecialchars(trim($img))."' style='width:100px; height:60px; object-fit:cover; border-radius:4px;'>";
                                ?>
                            </div>
                            <input type="file" name="imagen[]" multiple accept="image/*" style="width:100%; background:#2a2a2a; color:white; border:1px solid #444;">
                        </div>

                        <button type="submit" class="btn btn-primary" style="width:100%;">Guardar Cambios</button>
                    </form>
                </div>
                <?php } ?>
            </div>
        </div>

    </div>

    <script>
        function showSection(sectionId) {
            // Ocultar todo
            document.getElementById('section-ventas').style.display = 'none';
            document.getElementById('section-productos').style.display = 'none';
            document.getElementById('section-secciones').style.display = 'none';
            
            // Mostrar seleccionado
            document.getElementById('section-' + sectionId).style.display = 'block';
            
            // Actualizar menú activo
            document.querySelectorAll('.menu-item').forEach(el => el.classList.remove('active'));
            
            // Buscar el enlace del menú correspondiente y activarlo
            const activeLink = document.querySelector(`.menu-item[href="#${sectionId}"]`);
            if (activeLink) activeLink.classList.add('active');
        }

        function rechazarPedido(id) {
            let motivo = prompt("Por favor ingresa el motivo del rechazo:");
            if (motivo) {
                window.location.href = `admin.php?action=reject&id_venta=${id}&reason=${encodeURIComponent(motivo)}`;
            }
        }

        // Detectar hash o parámetros en URL para abrir pestaña correcta
        const hash = window.location.hash.replace('#', '');
        if (hash === 'secciones' || hash === 'productos' || hash === 'ventas') {
            showSection(hash);
        }

        // Si estamos editando un producto (parametro GET), forzar la vista de productos
        <?php if(isset($_GET['edit_prod'])): ?>
            showSection('productos');
        <?php endif; ?>

        // Funciones para gestión de colores
        function addColorRow() {
            const container = document.getElementById('colors-container');
            const div = document.createElement('div');
            div.className = 'color-row';
            div.style.cssText = 'display:flex; gap:10px; margin-bottom:10px;';
            div.innerHTML = `
                <input type="text" name="color_nombre[]" placeholder="Color (Ej: Rojo)" style="flex:2;">
                <input type="number" name="color_cantidad[]" placeholder="Cant." value="0" style="flex:1;" onchange="updateTotalStock()">
                <button type="button" class="btn-danger" onclick="this.parentElement.remove(); updateTotalStock()">X</button>
            `;
            container.appendChild(div);
        }

        function updateTotalStock() {
            let total = 0;
            document.querySelectorAll('input[name="color_cantidad[]"]').forEach(input => {
                total += parseInt(input.value) || 0;
            });
            if (total > 0) document.getElementById('total_stock').value = total;
        }
    </script>
</body>
</html>