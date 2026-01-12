<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sistema_usuarios";

// 1. Crear conexión inicial para verificar si existe la BD
$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// 2. Crear base de datos si no existe
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    $conn->select_db($dbname);
} else {
    die("Error creando base de datos: " . $conn->error);
}

// 3. Crear tabla de usuarios si no existe
$sql_tabla = "CREATE TABLE IF NOT EXISTS usuarios (
    cedula VARCHAR(20) PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    direccion TEXT NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol VARCHAR(20) DEFAULT 'cliente'
)";

if ($conn->query($sql_tabla) !== TRUE) {
    die("Error creando tabla: " . $conn->error);
}

// --- MIGRACIÓN: Asegurar que exista la columna 'rol' si la tabla ya existía ---
$check_col = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'rol'");
if ($check_col->num_rows == 0) {
    $conn->query("ALTER TABLE usuarios ADD COLUMN rol VARCHAR(20) DEFAULT 'cliente'");
}

// --- MIGRACIÓN: Asegurar que exista la columna 'carrito' para persistencia ---
$check_col_cart = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'carrito'");
if ($check_col_cart->num_rows == 0) {
    $conn->query("ALTER TABLE usuarios ADD COLUMN carrito TEXT DEFAULT NULL");
}

// --- CREAR ADMIN POR DEFECTO ---
// Cédula: 0000 | Pass: admin123
$sql_admin = "SELECT * FROM usuarios WHERE rol='admin' LIMIT 1";
if ($conn->query($sql_admin)->num_rows == 0) {
    $pass_admin = password_hash("admin123", PASSWORD_DEFAULT);
    $sql_insert_admin = "INSERT INTO usuarios (cedula, nombre, apellido, telefono, email, direccion, password, rol) 
                         VALUES ('0000', 'Administrador', 'Sistema', '0000000000', 'admin@mymoz13.com', 'Oficina Central', '$pass_admin', 'admin')";
    $conn->query($sql_insert_admin);
}

// 4. Crear tabla de ventas (pedidos)
$sql_ventas = "CREATE TABLE IF NOT EXISTS ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cedula_usuario VARCHAR(20) NOT NULL,
    productos TEXT NOT NULL, -- Guardaremos el JSON de los productos
    total DECIMAL(10,2) NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    comprobante VARCHAR(255) DEFAULT NULL,
    estado VARCHAR(20) DEFAULT 'pendiente',
    motivo_rechazo TEXT DEFAULT NULL
)";

if ($conn->query($sql_ventas) !== TRUE) {
    die("Error creando tabla ventas: " . $conn->error);
}

// --- MIGRACIÓN: Asegurar columnas de comprobante y estado ---
$check_col_comp = $conn->query("SHOW COLUMNS FROM ventas LIKE 'comprobante'");
if ($check_col_comp->num_rows == 0) {
    $conn->query("ALTER TABLE ventas ADD COLUMN comprobante VARCHAR(255) DEFAULT NULL");
    $conn->query("ALTER TABLE ventas ADD COLUMN estado VARCHAR(20) DEFAULT 'pendiente'");
    $conn->query("ALTER TABLE ventas ADD COLUMN motivo_rechazo TEXT DEFAULT NULL");
}

// --- MIGRACIÓN: Asegurar columnas de envío/retiro ---
$check_col_envio = $conn->query("SHOW COLUMNS FROM ventas LIKE 'metodo_retiro'");
if ($check_col_envio->num_rows == 0) {
    $conn->query("ALTER TABLE ventas ADD COLUMN metodo_retiro VARCHAR(100) DEFAULT 'Tienda'");
    $conn->query("ALTER TABLE ventas ADD COLUMN direccion_envio TEXT DEFAULT NULL");
}

// 5. Crear tabla de productos
$sql_productos = "CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    imagen TEXT NOT NULL,
    categoria VARCHAR(50) DEFAULT 'General',
    descripcion TEXT,
    tallas VARCHAR(100) DEFAULT NULL,
    stock INT DEFAULT 0
)";

if ($conn->query($sql_productos) !== TRUE) {
    die("Error creando tabla productos: " . $conn->error);
}

// --- MIGRACIÓN: Asegurar que exista la columna 'descripcion' ---
$check_col_desc = $conn->query("SHOW COLUMNS FROM productos LIKE 'descripcion'");
if ($check_col_desc->num_rows == 0) {
    $conn->query("ALTER TABLE productos ADD COLUMN descripcion TEXT");
}

// --- MIGRACIÓN: Asegurar que exista la columna 'tallas' ---
$check_col_tallas = $conn->query("SHOW COLUMNS FROM productos LIKE 'tallas'");
if ($check_col_tallas->num_rows == 0) {
    $conn->query("ALTER TABLE productos ADD COLUMN tallas VARCHAR(100) DEFAULT NULL");
}

// --- MIGRACIÓN: Asegurar que exista la columna 'stock' ---
$check_col_stock = $conn->query("SHOW COLUMNS FROM productos LIKE 'stock'");
if ($check_col_stock->num_rows == 0) {
    $conn->query("ALTER TABLE productos ADD COLUMN stock INT DEFAULT 50");
}

// --- MIGRACIÓN: Asegurar columnas de oferta ---
$check_col_oferta = $conn->query("SHOW COLUMNS FROM productos LIKE 'en_oferta'");
if ($check_col_oferta->num_rows == 0) {
    $conn->query("ALTER TABLE productos ADD COLUMN en_oferta TINYINT(1) DEFAULT 0");
    $conn->query("ALTER TABLE productos ADD COLUMN porcentaje_oferta INT DEFAULT 0");
}

// --- MIGRACIÓN: Asegurar columnas de colores ---
$check_col_colores = $conn->query("SHOW COLUMNS FROM productos LIKE 'colores'");
if ($check_col_colores->num_rows == 0) {
    $conn->query("ALTER TABLE productos ADD COLUMN colores VARCHAR(255) DEFAULT NULL");
}

// --- MIGRACIÓN: Asegurar columna de stock por color (JSON) ---
$check_col_stock_color = $conn->query("SHOW COLUMNS FROM productos LIKE 'stock_por_color'");
if ($check_col_stock_color->num_rows == 0) {
    $conn->query("ALTER TABLE productos ADD COLUMN stock_por_color TEXT DEFAULT NULL");
}

// --- MIGRACIÓN DE DATOS: Asignar tallas a productos existentes si están vacías ---
$conn->query("UPDATE productos SET tallas='S,M,L,XL' WHERE nombre LIKE '%Franelas%' AND (tallas IS NULL OR tallas = '')");
$conn->query("UPDATE productos SET tallas='S,M,L' WHERE categoria='Lencería' AND (tallas IS NULL OR tallas = '')");
$conn->query("UPDATE productos SET tallas='28,30,32,34' WHERE nombre LIKE '%Jeans%' AND (tallas IS NULL OR tallas = '')");
$conn->query("UPDATE productos SET tallas='36,37,38,39,40' WHERE (nombre LIKE '%Zapatos%' OR nombre LIKE '%Tacones%') AND (tallas IS NULL OR tallas = '')");
$conn->query("UPDATE productos SET tallas='S,M,L' WHERE categoria IN ('Damas', 'Caballeros', 'Niños') AND (tallas IS NULL OR tallas = '')");

// Insertar productos por defecto si la tabla está vacía (Seed Data)
$check_prods = $conn->query("SELECT count(*) as total FROM productos");
$row_prods = $check_prods->fetch_assoc();

if ($row_prods['total'] == 0) {
    $sql_insert = "INSERT INTO productos (nombre, precio, imagen, categoria, tallas, stock) VALUES 
    ('Conjunto Lencería Encaje', 45.00, 'https://images.unsplash.com/photo-1596483152376-f8a6e7a233b0?w=500', 'Lencería', 'S,M,L', 50),
    ('Brasier Push-Up', 30.00, 'https://images.unsplash.com/photo-1620799140408-ed5341cd2431?w=500', 'Lencería', '32B,34B,36B', 50),
    ('Panties Algodón (Pack x3)', 20.00, 'https://images.unsplash.com/photo-1582716401301-b2407dc7563d?w=500', 'Lencería', 'S,M,L', 50),
    ('Bata de Seda', 55.00, 'https://images.unsplash.com/photo-1584276337587-432204786d7e?w=500', 'Lencería', 'Única', 20),
    ('Conjunto Deportivo Niño', 35.00, 'https://images.unsplash.com/photo-1519238263496-63f7245af27d?w=500', 'Niños', '4,6,8,10', 30),
    ('Camiseta Estampada Niño', 18.00, 'https://images.unsplash.com/photo-1519457431-44ccd64a579b?w=500', 'Niños', '4,6,8,10', 40),
    ('Jeans Ajustables Niño', 28.00, 'https://images.unsplash.com/photo-1519238919318-d9a2e63595b4?w=500', 'Niños', '4,6,8,10', 35),
    ('Sudadera Niño', 32.00, 'https://images.unsplash.com/photo-1503944583220-79d8926ad5e2?w=500', 'Niños', 'S,M,L', 25),
    ('Camisa Oxford Hombre', 45.00, 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=500', 'Caballeros', 'S,M,L,XL', 60),
    ('Pantalón Chino Hombre', 50.00, 'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?w=500', 'Caballeros', '30,32,34,36', 45),
    ('Chaqueta Cuero Hombre', 120.00, 'https://images.unsplash.com/photo-1487222477894-8943e31ef7b2?w=500', 'Caballeros', 'M,L,XL', 15),
    ('Vestido de Gala Mujer', 95.00, 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?w=500', 'Damas', 'S,M,L', 10),
    ('Blusa Elegante Mujer', 40.00, 'https://images.unsplash.com/photo-1551163943-3f6a29e39426?w=500', 'Damas', 'S,M,L', 50),
    ('Jeans Skinny Mujer', 55.00, 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?w=500', 'Damas', '26,28,30,32', 55),
    ('Tacones Clásicos', 75.00, 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=500', 'Damas', '36,37,38,39', 20),
    ('Franelas 100% Algodón (S-XL)', 25.00, 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=500,https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=500,https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=500', 'Caballeros', 'S,M,L,XL', 100)";
    
    if ($conn->query($sql_insert) !== TRUE) {
        echo "Error insertando productos iniciales: " . $conn->error;
    }
}

// 6. Crear tabla de secciones del home (Ofertas/Portada)
$sql_secciones = "CREATE TABLE IF NOT EXISTS secciones_home (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identificador VARCHAR(50) UNIQUE NOT NULL,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT,
    imagen TEXT,
    filtro VARCHAR(50) DEFAULT '' -- Palabra clave para filtrar el catálogo al hacer clic
)";

if ($conn->query($sql_secciones) !== TRUE) {
    die("Error creando tabla secciones_home: " . $conn->error);
}

// Insertar datos por defecto si está vacía (Seed Data)
$check_secciones = $conn->query("SELECT count(*) as total FROM secciones_home");
$row_secciones = $check_secciones->fetch_assoc();

if ($row_secciones['total'] == 0) {
    $sql_insert_secciones = "INSERT INTO secciones_home (identificador, titulo, descripcion, imagen, filtro) VALUES 
    ('oferta_principal', 'Tendencias para Damas', 'Descubre lo último en moda femenina. Elegancia y estilo que definen tu personalidad.', 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?q=80&w=2070&auto=format&fit=crop', 'Damas'),
    ('oferta_secundaria_1', 'Caballeros: Poder y Estilo', 'Chaquetas de cuero y outfits que imponen respeto.', 'https://images.unsplash.com/photo-1487222477894-8943e31ef7b2?q=80&w=1000&auto=format&fit=crop', 'Caballeros'),
    ('oferta_secundaria_2', 'Mundo Kids', 'Comodidad para sus grandes aventuras.', 'https://images.unsplash.com/photo-1519238263496-63f7245af27d?q=80&w=1000&auto=format&fit=crop', 'Niños')";
    
    $conn->query($sql_insert_secciones);
}

// La variable $conn quedará lista para usarse en otros archivos
?>