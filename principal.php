<?php
session_start();
include 'conexion.php';
$is_logged_in = isset($_SESSION['usuario']);

// Cargar secciones de la portada desde la BD
$secciones = [];
$res_secciones = $conn->query("SELECT * FROM secciones_home");
while($row = $res_secciones->fetch_assoc()) {
    $secciones[$row['identificador']] = $row;
}
?>
<!DOCTYPE html>
<html lang="es" data-wf-page="6963d93c52120388fb7edc1f" data-wf-site="6963d93b52120388fb7edc0c">
<head>
    <meta charset="UTF-8">
    <title>Principal - MYMOZ13</title>
    <meta content="width=device-width, initial-scale=1" name="viewport">
    
    <!-- Webflow CSS -->
    <link href="https://cdn.prod.website-files.com/6963d93b52120388fb7edc0c/css/mymoz13.webflow.shared.19c81f106.css" rel="stylesheet" type="text/css">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js" type="text/javascript"></script>
    <script type="text/javascript">WebFont.load({  google: {    families: ["Inter:regular,500,600","Syne:700"]  }});</script>

    <style>
        /* Fix Webflow Visibility Issue */
        .w-mod-js:not(.w-mod-ix3) [data-w-id] { opacity: 1 !important; transform: none !important; visibility: visible !important; }
        html.w-mod-js:not(.w-mod-ix3) :where([class*="heading"], [class*="card"], [class*="content"]) { visibility: visible !important; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #111111;
            color: #e0e0e0;
            margin: 0;
            -webkit-font-smoothing: antialiased;
        }
        
        /* Header */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 5%;
            background-color: rgba(18, 18, 18, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #333;
            position: sticky;
            height: 80px;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .nav-brand img {
            height: 50px;
            width: auto;
        }
        .nav-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        /* Dropdown & Search */
        .nav-links {
            display: flex;
            gap: 25px;
            margin-left: 40px;
            transition: all 0.3s ease;
        }
        /* Botón Menú Móvil */
        .mobile-toggle {
            display: none;
            font-size: 1.8rem;
            background: none;
            border: none;
            cursor: pointer;
            color: #e0e0e0;
        }
        .dropdown {
            position: relative;
            display: inline-block;
        }
        .dropbtn {
            background: none;
            border: none;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            color: #e0e0e0;
            padding: 10px 0;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #1e1e1e;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.1);
            z-index: 1;
            border-radius: 8px;
            padding: 10px 0;
            top: 100%;
        }
        .dropdown-content a {
            color: #e0e0e0;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            font-size: 0.9rem;
        }
        .dropdown-content a:hover { background-color: #333; color: #00BFFF; }
        .dropdown:hover .dropdown-content { display: block; }
        
        .search-bar {
            display: flex;
            align-items: center;
            background: #2a2a2a;
            padding: 8px 15px;
            border-radius: 20px;
            margin-right: 20px;
        }
        .search-bar input {
            border: none;
            background: transparent;
            outline: none;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            width: 200px;
            color: #fff;
        }
        
        /* Cart Icon */
        .cart-trigger {
            position: relative;
            cursor: pointer;
            margin-left: 15px;
            font-size: 1.2rem;
        }
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #FF00FF;
            color: white;
            font-size: 0.7rem;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        /* Hero Section */
        .section-hero {
            position: relative;
            padding: 100px 20px;
            text-align: center;
            color: white;
            overflow: hidden;
            min-height: 500px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .hero-overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); z-index: 1;
        }
        .heading-hero {
            font-family: 'Syne', sans-serif;
            font-size: 4rem;
            font-weight: 700;
            line-height: 1.1;
            margin-bottom: 24px;
            color: #fff;
            z-index: 2;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        .subheading {
            font-size: 1.25rem;
            color: #f0f0f0;
            max-width: 600px;
            margin: 0 auto 40px;
            line-height: 1.6;
            z-index: 2;
        }
        .hero-slider {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0;
        }
        .slide {
            position: absolute; width: 100%; height: 100%; opacity: 0; transition: opacity 1s ease-in-out;
            background-size: cover; background-position: center;
        }
        .slide.active { opacity: 1; }

        /* Marketing Bar */
        .marketing-bar {
            background: #FF00FF;
            color: white;
            text-align: center;
            padding: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .countdown {
            font-weight: 800;
            margin-left: 10px;
        }

        /* Marketing / Offers Section */
        .section-offers {
            padding: 0 5% 80px;
            max-width: 1400px;
            margin: 100px auto 0;
        }
        .offers-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            height: 800px; /* Aumentado para mayor tamaño */
        }
        .offer-card {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            align-items: flex-end;
            padding: 40px;
            color: white;
            transition: transform 0.3s ease;
            z-index: 1;
        }
        .offer-card:hover { transform: scale(1.01); }
        
        /* Estilos para el slider de fondo en ofertas */
        .offer-bg-wrapper {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: -2;
        }
        .offer-bg-img {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;
            opacity: 0; transition: opacity 1.5s ease-in-out;
        }
        .offer-bg-img.active { opacity: 1; }
        .offer-overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
            z-index: -1;
            pointer-events: none;
        }
        
        .offer-text h3 { font-family: 'Syne', sans-serif; font-size: 2rem; margin: 0 0 10px; }
        .offer-text p { font-size: 1rem; margin: 0 0 20px; opacity: 0.9; }
        .offer-text { position: relative; z-index: 2; width: 100%; }
        
        /* Catálogo */
        .section-catalog {
            padding: 0 5% 100px;
            max-width: 1400px;
            margin: 0 auto;
        }
        .catalog-header {
            text-align: center;
            margin-bottom: 50px;
        }
        .catalog-header h2 {
            font-family: 'Syne', sans-serif;
            font-size: 2.5rem;
            color: #fff;
        }
        .catalog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 30px;
        }
        .product-card {
            background: #1e1e1e;
            border: 1px solid #333;
            border-radius: 16px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        }
        .product-image-wrapper {
            height: 320px;
            background-color: #2a2a2a;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ccc;
            font-size: 1rem;
            position: relative; /* Necesario para el slider */
        }
        .product-slider-img {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;
            opacity: 0; transition: opacity 0.5s ease-in-out;
        }
        .product-slider-img.active {
            opacity: 1;
        }
        .product-details {
            padding: 24px;
            text-align: left;
        }
        .product-title {
            font-family: 'Syne', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 0 8px 0;
            color: #fff;
        }
        .product-price {
            font-size: 1.25rem;
            color: #FF00FF; /* Fucsia */
            font-weight: 600;
            display: block;
            margin-bottom: 20px;
        }
        
        /* Botones y Enlaces */
        .button-primary {
            background-color: #00BFFF; /* Azul Celeste */
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }
        .button-primary:hover {
            background-color: #009ACD;
            transform: translateY(-2px);
        }
        .button-secondary {
            background-color: transparent;
            border: 2px solid #00BFFF;
            color: #00BFFF;
            padding: 10px 22px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .button-secondary:hover {
            background-color: #f0fbff;
        }

        /* Cart Sidebar */
        .cart-sidebar {
            position: fixed;
            top: 0;
            right: -400px;
            width: 350px;
            height: 100%;
            background: #1e1e1e;
            box-shadow: -5px 0 30px rgba(0,0,0,0.1);
            z-index: 2000;
            transition: right 0.3s ease;
            display: flex;
            flex-direction: column;
            color: #e0e0e0;
        }
        .cart-sidebar.open { right: 0; }
        .cart-header {
            padding: 20px;
            border-bottom: 1px solid #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .cart-items {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }
        .cart-item {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid #333;
            padding-bottom: 10px;
        }
        .cart-item img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; margin-right: 15px; }
        .cart-footer {
            padding: 20px;
            background: #1a1a1a;
            border-top: 1px solid #333;
        }
        .close-cart { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #fff; }
        .cart-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); z-index: 1999; display: none;
        }
        .cart-overlay.open { display: block; }
        .remove-item { color: red; font-size: 0.8rem; cursor: pointer; margin-top: 5px; display: block; }
        
        /* Footer */
        .footer {
            background-color: #0a0a0a;
            padding: 60px 5% 30px;
            border-top: 1px solid #333;
            font-size: 0.9rem;
            color: #888;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto 40px;
        }
        .footer-col h4 {
            font-family: 'Syne', sans-serif;
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: #fff;
        }
        .footer-col p, .footer-col a {
            color: #aaa; /* Color más claro para mejor lectura */
            line-height: 1.8;
            display: block;
            text-decoration: none;
        }
        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid #333;
            color: #999;
        }
        
        .text-fucsia { color: #FF00FF; }
        .w-full { width: 100%; }
        
        /* Responsive Media Queries */
        @media (max-width: 991px) {
            .navbar { flex-wrap: wrap; padding: 15px 20px; height: auto; min-height: 80px; }
            .nav-brand { margin-right: auto; }
            .mobile-toggle { display: block; color: #fff; }
            
            .nav-links {
                display: none; width: 100%; flex-direction: column; 
                align-items: center; gap: 0; margin: 0; 
                background: #1e1e1e; border-top: 1px solid #333; order: 3;
            }
            .nav-links.active { display: flex; }
            
            .dropdown { width: 100%; text-align: center; }
            .dropbtn { width: 100%; padding: 15px 0; border-bottom: 1px solid #333; color: #fff; }
            .dropdown-content { position: static; box-shadow: none; background: #1a1a1a; width: 100%; }
            
            .nav-menu { display: none; width: 100%; flex-direction: column; gap: 15px; order: 4; margin: 0; padding: 20px; background: #1e1e1e; border-top: 1px solid #333; align-items: center; }
            .nav-menu.active { display: flex; }
            
            .search-bar { margin: 0; width: 100%; max-width: 100%; box-sizing: border-box; }
            .search-bar input { width: 100%; }
        }

        @media (max-width: 767px) {
            .heading-hero { font-size: 2.2rem; } /* Texto un poco más pequeño para que no sature */
            .section-catalog { padding: 0 15px 60px; }
            .offers-grid { grid-template-columns: 1fr; height: auto; gap: 15px; }
            .offer-card { height: 350px; padding: 25px; } /* Altura más cómoda para ver en pantallas pequeñas */
            .footer-grid { grid-template-columns: 1fr; text-align: center; gap: 30px; }
            .footer { padding-bottom: 80px; } /* Ajuste de espacio para que se vea completo */
            .cart-sidebar { width: 100%; right: -100%; }
            .featured-slider-container { height: 350px; } /* Ajuste para móviles */
            
            /* Ajustes Navbar */
            .navbar { padding: 10px 15px; }
            .nav-brand img { height: 40px; }
        }

        /* Select de Tallas en Tarjeta */
        .card-size-select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            background: #2a2a2a;
            color: #fff;
            border: 1px solid #444;
            border-radius: 5px;
        }
        /* Featured Section (Franelas) */
        .section-featured {
            padding: 80px 5%;
            background-color: #0f0f0f;
            border-bottom: 1px solid #333;
        }
        .featured-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 50px;
            max-width: 1200px;
            margin: 0 auto;
            align-items: center;
        }
        .featured-slider-container {
            flex: 1;
            min-width: 300px;
            height: 500px; /* Altura ajustada para mejor impacto visual */
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            cursor: pointer; /* Indica que es interactivo */
        }
        .featured-info {
            flex: 1;
            min-width: 300px;
        }
        .featured-tag {
            color: #00BFFF;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 10px;
            display: block;
        }
        .size-selector {
            margin: 25px 0;
        }
        .size-btn {
            background: transparent;
            border: 1px solid #444;
            color: #fff;
            padding: 10px 20px;
            margin-right: 10px;
            cursor: pointer;
            border-radius: 5px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s;
            min-width: 45px;
            text-align: center;
        }
        .size-btn:hover, .size-btn.active {
            background: #00BFFF;
            border-color: #00BFFF;
        }

        /* Modal Styles */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 3000; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0,0,0,0.85); 
            backdrop-filter: blur(5px);
        }
        .modal-content {
            background-color: #0f0f0f;
            margin: 5% auto; 
            padding: 0;
            border: 1px solid #333;
            width: 90%; 
            max-width: 1200px;
            border-radius: 20px;
            position: relative;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .close-modal {
            color: #fff;
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 30px;
            font-weight: bold;
            cursor: pointer;
            z-index: 100;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }
        .close-modal:hover { color: #00BFFF; }
        
        /* Quantity Selector */
        .qty-selector { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
        .qty-btn {
            background: #333; color: white; border: 1px solid #444; width: 35px; height: 35px;
            border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 1.2rem;
        }
        .qty-btn:hover { background: #00BFFF; border-color: #00BFFF; }
        .qty-input {
            width: 60px; text-align: center; background: #222; border: 1px solid #444; color: white; border-radius: 5px; padding: 8px; font-size: 1rem;
        }

        /* --- NUEVO: Estilos para Chat y Precisión --- */
        /* Chat Widget */
        .chat-widget { position: fixed; bottom: 20px; right: 20px; z-index: 9999; font-family: 'Inter', sans-serif; display: flex; flex-direction: column; align-items: flex-end; }
        .chat-btn { width: 60px; height: 60px; background: #25D366; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.3); transition: transform 0.3s; }
        .chat-btn:hover { transform: scale(1.1); }
        .chat-btn svg { width: 32px; height: 32px; fill: white; }
        
        .chat-box { position: absolute; bottom: 80px; right: 0; width: 300px; background: #fff; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.3); overflow: hidden; display: none; flex-direction: column; animation: slideUp 0.3s ease; border: 1px solid #ccc; }
        .chat-box.open { display: flex; }
        .chat-header { background: #075E54; color: white; padding: 15px; font-weight: bold; display: flex; justify-content: space-between; align-items: center; }
        .chat-body { padding: 20px; background: #e5ddd5; height: 200px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; }
        .chat-msg { background: white; padding: 10px 15px; border-radius: 0 15px 15px 15px; max-width: 85%; font-size: 0.9rem; color: #333; align-self: flex-start; box-shadow: 0 1px 2px rgba(0,0,0,0.1); line-height: 1.4; }
        .chat-footer { padding: 10px; background: #f0f0f0; display: flex; gap: 10px; align-items: center; }
        .chat-input { flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 20px; outline: none; font-size: 0.9rem; }
        .chat-send { background: #25D366; color: white; border: none; padding: 8px 12px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* Modal Extras (Precisión) */
        .modal-extras { margin-top: 20px; border-top: 1px solid #333; padding-top: 20px; }
        .size-guide-btn { color: #00BFFF; text-decoration: underline; cursor: pointer; font-size: 0.9rem; margin-bottom: 15px; display: inline-block; font-weight: 500; }
        .size-guide-btn:hover { color: #fff; }
        
        .size-guide-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; display: none; font-size: 0.85rem; color: #ccc; background: #1a1a1a; border-radius: 8px; overflow: hidden; }
        .size-guide-table th, .size-guide-table td { border: 1px solid #333; padding: 10px; text-align: center; }
        .size-guide-table th { background: #252525; color: #fff; font-weight: 600; }
        
        .custom-note-area {
            width: 100%; background: #222; border: 1px solid #444; color: #fff;
            padding: 12px; border-radius: 8px; margin-bottom: 15px;
            font-family: 'Inter', sans-serif; resize: vertical; font-size: 0.95rem;
            box-sizing: border-box;
        }

        /* Estilos de Oferta */
        .badge-offer {
            position: absolute; top: 10px; right: 10px; background: #FF0000; color: white; 
            padding: 5px 10px; border-radius: 5px; font-weight: bold; font-size: 0.8rem; z-index: 10;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <a href="principal.php" class="nav-brand">
            <img src="image/logo.jpeg" alt="Logo MYMOZ13" loading="lazy">
        </a>
        <button class="mobile-toggle" aria-label="Menú">☰</button>
        
            <div class="nav-links">
                <div class="dropdown">
                    <button class="dropbtn">Categorías ▾</button>
                    <div class="dropdown-content">
                        <a href="#catalogo" onclick="setFilter('Damas')">Damas</a>
                        <a href="#catalogo" onclick="setFilter('Caballeros')">Caballeros</a>
                        <a href="#catalogo" onclick="setFilter('Niños')">Niños</a>
                        <a href="#catalogo" onclick="setFilter('Lencería')">Lencería</a>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="dropbtn">Colecciones ▾</button>
                    <div class="dropdown-content">
                        <a href="#catalogo" onclick="setFilter('')">Verano 2024</a>
                        <a href="#catalogo" onclick="setFilter('')">Ofertas</a>
                    </div>
                </div>
            </div>
        
        <div class="nav-menu">
            <div class="search-bar">
                <span style="margin-right: 8px; color: #999;">🔍</span>
                <input type="text" id="search-input" placeholder="Buscar..." onkeyup="filterProducts()" autocomplete="off">
            </div>
            <?php if ($is_logged_in): ?>
                <span style="font-weight: 500; display: none; @media(min-width:768px){display:inline;}">Hola, <?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?></span>
                <?php if (isset($_SESSION['usuario']['rol']) && $_SESSION['usuario']['rol'] === 'admin'): ?>
                    <a href="admin.php" class="button-secondary" style="margin-left: 10px; padding: 8px 15px; font-size: 0.9rem;">Panel Admin</a>
                <?php endif; ?>
                <a href="login.php?logout=true" class="button-primary">Cerrar Sesión</a>
            <?php else: ?>
                <a href="login.php" class="button-secondary">Iniciar Sesión</a>
                <a href="registro.php" class="button-primary">Registrarse</a>
            <?php endif; ?>
            
            <!-- Cart Trigger -->
            <div class="cart-trigger" onclick="toggleCart()">
                🛒 <span class="cart-count" id="cart-count">0</span>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="section-hero">
        <div class="hero-slider">
            <div class="slide active" style="background-image: url('https://images.unsplash.com/photo-1469334031218-e382a71b716b?q=80&w=2070&auto=format&fit=crop');"></div>
            <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=2070&auto=format&fit=crop');"></div>
            <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1445205170230-053b83016050?q=80&w=2071&auto=format&fit=crop');"></div>
        </div>
        <div class="hero-overlay"></div>
        <h1 class="heading-hero">Viste tu actitud, conquista Caracas. <span class="text-fucsia">MYMOZ13</span></h1>
        <p class="subheading">Ropa única, estilo sin límites. Encuentra lo último en moda y destaca en cada momento. ¡Haz que tu look hable por ti!</p>
        <a href="#catalogo" class="button-primary" style="z-index: 2; font-size: 1.2rem; padding: 15px 30px;">Ver Colección</a>
    </section>

    <!-- Marketing / Offers Section -->
    <section class="section-offers">
        <div class="offers-grid">
            <!-- Oferta Principal -->
            <div class="offer-card offer-main">
                <div class="offer-bg-wrapper auto-slider">
                    <?php 
                    $imgs = explode(',', $secciones['oferta_principal']['imagen']);
                    foreach($imgs as $i => $img) echo '<img src="'.htmlspecialchars(trim($img)).'" class="offer-bg-img '.($i==0?'active':'').'">';
                    ?>
                </div>
                <div class="offer-overlay"></div>
                <div class="offer-text">
                    <h3><?php echo htmlspecialchars($secciones['oferta_principal']['titulo']); ?></h3>
                    <p><?php echo htmlspecialchars($secciones['oferta_principal']['descripcion']); ?></p>
                    <a href="#catalogo" class="button-primary" onclick="document.getElementById('search-input').value='<?php echo $secciones['oferta_principal']['filtro']; ?>'; filterProducts();">Explorar <?php echo $secciones['oferta_principal']['filtro']; ?></a>
                </div>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <!-- Oferta Secundaria 1 -->
                <div class="offer-card offer-secondary" style="flex: 1;" onclick="document.getElementById('search-input').value='<?php echo $secciones['oferta_secundaria_1']['filtro']; ?>'; filterProducts(); window.location.href='#catalogo';">
                    <div class="offer-bg-wrapper auto-slider">
                        <?php 
                        $imgs = explode(',', $secciones['oferta_secundaria_1']['imagen']);
                        foreach($imgs as $i => $img) echo '<img src="'.htmlspecialchars(trim($img)).'" class="offer-bg-img '.($i==0?'active':'').'">';
                        ?>
                    </div>
                    <div class="offer-overlay"></div>
                    <div class="offer-text">
                        <h3><?php echo htmlspecialchars($secciones['oferta_secundaria_1']['titulo']); ?></h3>
                        <p><?php echo htmlspecialchars($secciones['oferta_secundaria_1']['descripcion']); ?></p>
                    </div>
                </div>
                <!-- Oferta Secundaria 2 -->
                <div class="offer-card offer-secondary" style="flex: 1;" onclick="document.getElementById('search-input').value='<?php echo $secciones['oferta_secundaria_2']['filtro']; ?>'; filterProducts(); window.location.href='#catalogo';">
                    <div class="offer-bg-wrapper auto-slider">
                        <?php 
                        $imgs = explode(',', $secciones['oferta_secundaria_2']['imagen']);
                        foreach($imgs as $i => $img) echo '<img src="'.htmlspecialchars(trim($img)).'" class="offer-bg-img '.($i==0?'active':'').'">';
                        ?>
                    </div>
                    <div class="offer-overlay"></div>
                    <div class="offer-text">
                        <h3><?php echo htmlspecialchars($secciones['oferta_secundaria_2']['titulo']); ?></h3>
                        <p><?php echo htmlspecialchars($secciones['oferta_secundaria_2']['descripcion']); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Product Section (Franelas) -->
    <?php
    $sql_feat = "SELECT * FROM productos WHERE nombre LIKE '%Franelas%' LIMIT 1";
    $res_feat = $conn->query($sql_feat);
    if ($res_feat->num_rows > 0):
        $feat_row = $res_feat->fetch_assoc();
        // Imágenes de Marketing: Alegres, coloridas e interactivas
        // Imágenes de Marketing: Usando SOLO las imágenes proporcionadas
        $feat_imgs = [
            'uploads/1768162156_f2.jpeg',
            'uploads/1768162156_f3.jpeg',
            'uploads/1768162156_f4.jpeg',
            'uploads/1768162156_f5.jpeg',
            'uploads/1768162156_f6.jpeg',
            'uploads/1768162156_f7.jpeg',
            'uploads/1768162156_f8.jpeg',
            'uploads/1768162156_f9.jpeg'
        ];
        $feat_imgs_str = implode(',', $feat_imgs);
    ?>
    <section class="section-featured">
        <div class="featured-wrapper">
            <div class="featured-slider-container mini-slider" onclick='openProductModal({
                                id: <?php echo $feat_row['id']; ?>,
                                nombre: <?php echo htmlspecialchars(json_encode($feat_row['nombre']), ENT_QUOTES); ?>,
                                precio: <?php echo $feat_row['precio']; ?>,
                                imagenes: <?php echo htmlspecialchars(json_encode($feat_imgs_str), ENT_QUOTES); ?>,
                                descripcion: <?php echo htmlspecialchars(json_encode($feat_row['descripcion'] ?? "Franelas de algodón premium. Colores disponibles: Negro, Blanco, Beige, Marrón, Lila, Azul Cielo, Verde Oliva."), ENT_QUOTES); ?>,
                                tallas: <?php echo htmlspecialchars(json_encode($feat_row['tallas'] ?? "S,M,L,XL"), ENT_QUOTES); ?>,
                                categoria: <?php echo htmlspecialchars(json_encode($feat_row['categoria']), ENT_QUOTES); ?>,
                                colores: <?php echo htmlspecialchars(json_encode($feat_row['colores'] ?? ""), ENT_QUOTES); ?>,
                                stock_por_color: <?php echo htmlspecialchars(json_encode($feat_row['stock_por_color'] ?? "{}"), ENT_QUOTES); ?>,
                                stock: <?php echo intval($feat_row['stock']); ?>
                            })'>
                <?php 
                foreach ($feat_imgs as $i => $img) {
                    $active = ($i === 0) ? 'active' : '';
                    echo '<img src="'.$img.'" class="product-slider-img '.$active.'" alt="Franelas" style="object-fit: cover;">';
                }
                ?>
            </div>
            <div class="featured-info">
                <span class="featured-tag">Producto Destacado</span>
                <h2 style="font-family: 'Syne', sans-serif; font-size: 3rem; margin: 10px 0; color: white;">Franelas 100% Algodón</h2>
                <p style="color: #aaa; font-size: 1.1rem; line-height: 1.6;">
                    ¡Dale color a tu vida! Descubre la comodidad absoluta con nuestras franelas de alta calidad. Diseños vibrantes para gente vibrante.
                </p>
                <div class="size-selector">
                    <p style="margin-bottom: 10px; font-weight: 600;">Tallas Disponibles:</p>
                    <!-- Visualización estética de tallas (la selección real se hace en el modal) -->
                    <div style="display:flex; gap:10px;">
                        <span class="size-btn">S</span><span class="size-btn">M</span><span class="size-btn">L</span><span class="size-btn">XL</span>
                    </div>
                </div>
                <span class="product-price" style="font-size: 2rem; margin-bottom: 30px;">$<?php echo number_format($feat_row['precio'], 2); ?> BCV</span>
                <button class="button-primary" style="padding: 15px 40px; font-size: 1.1rem;" 
                    onclick='openProductModal({
                                id: <?php echo $feat_row['id']; ?>,
                                nombre: <?php echo htmlspecialchars(json_encode($feat_row['nombre']), ENT_QUOTES); ?>,
                                precio: <?php echo $feat_row['precio']; ?>,
                                imagenes: <?php echo htmlspecialchars(json_encode($feat_imgs_str), ENT_QUOTES); ?>,
                                descripcion: <?php echo htmlspecialchars(json_encode($feat_row['descripcion'] ?? "Franelas de algodón premium. Colores disponibles: Negro, Blanco, Beige, Marrón, Lila, Azul Cielo, Verde Oliva."), ENT_QUOTES); ?>,
                                tallas: <?php echo htmlspecialchars(json_encode($feat_row['tallas'] ?? "S,M,L,XL"), ENT_QUOTES); ?>,
                                categoria: <?php echo htmlspecialchars(json_encode($feat_row['categoria']), ENT_QUOTES); ?>,
                                colores: <?php echo htmlspecialchars(json_encode($feat_row['colores'] ?? ""), ENT_QUOTES); ?>,
                                stock_por_color: <?php echo htmlspecialchars(json_encode($feat_row['stock_por_color'] ?? "{}"), ENT_QUOTES); ?>,
                                stock: <?php echo intval($feat_row['stock']); ?>
                            })'>
                    Ver Detalles y Comprar
                </button>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Catálogo de Productos -->
    <section class="section-catalog" id="catalogo">
        <div class="catalog-header">
            <h2>Tendencias de la Semana</h2>
        </div>
        <div class="catalog-grid">
            <?php
            $sql = "SELECT * FROM productos";
            $result = $conn->query($sql);

            if ($result->num_rows > 0):
                while($row = $result->fetch_assoc()):
                    // Cálculos de oferta
                    $precio_original = $row['precio'];
                    $en_oferta = $row['en_oferta'];
                    $porcentaje = $row['porcentaje_oferta'];
                    $precio_final = $precio_original;

                    if ($en_oferta && $porcentaje > 0) {
                        $descuento = ($precio_original * $porcentaje) / 100;
                        $precio_final = $precio_original - $descuento;
                    }
            ?>
                <div class="product-card" data-search="<?php echo strtolower(htmlspecialchars($row['nombre'] . ' ' . $row['categoria'])); ?>">
                    <?php if($en_oferta && $porcentaje > 0): ?>
                        <div class="badge-offer">-<?php echo $porcentaje; ?>% OFF</div>
                    <?php endif; ?>
                    <div class="product-image-wrapper mini-slider">
                        <?php 
                        // Verificar si hay múltiples imágenes separadas por coma
                        $imagenes = explode(',', $row['imagen']);
                        if (count($imagenes) > 1) {
                            foreach ($imagenes as $index => $img) {
                                $activeClass = ($index === 0) ? 'active' : '';
                                echo '<img src="'.htmlspecialchars(trim($img)).'" class="product-slider-img '.$activeClass.'" alt="'.htmlspecialchars($row['nombre']).'">';
                            }
                        } else {
                            // Imagen única normal
                            echo '<img src="'.htmlspecialchars($row['imagen']).'" style="width:100%; height:100%; object-fit:cover;" alt="'.htmlspecialchars($row['nombre']).'">';
                        }
                        ?>
                    </div>
                    <div class="product-details">
                        <h3 class="product-title"><?php echo htmlspecialchars($row['nombre']); ?></h3>
                        
                        <?php if($en_oferta && $porcentaje > 0): ?>
                            <span class="product-price" style="margin-bottom: 5px;">
                                <span style="text-decoration: line-through; color: #888; font-size: 1rem; margin-right: 10px;">$<?php echo number_format($precio_original, 2); ?></span>
                                <span style="color: #FF00FF; font-size: 1.25rem;">$<?php echo number_format($precio_final, 2); ?> BCV</span>
                            </span>
                        <?php else: ?>
                            <span class="product-price">$<?php echo number_format($precio_original, 2); ?> BCV</span>
                        <?php endif; ?>

                        <button class="button-primary w-full" 
                            onclick='openProductModal({
                                id: <?php echo $row['id']; ?>,
                                nombre: <?php echo htmlspecialchars(json_encode($row['nombre']), ENT_QUOTES); ?>,
                                precio: <?php echo $precio_final; ?>,
                                precio_original: <?php echo $precio_original; ?>,
                                en_oferta: <?php echo $en_oferta; ?>,
                                porcentaje: <?php echo $porcentaje; ?>,
                                imagenes: <?php echo htmlspecialchars(json_encode($row['imagen']), ENT_QUOTES); ?>,
                                descripcion: <?php echo htmlspecialchars(json_encode($row['descripcion'] ?? "Sin descripción."), ENT_QUOTES); ?>,
                                tallas: <?php echo htmlspecialchars(json_encode($row['tallas'] ?? ""), ENT_QUOTES); ?>,
                                categoria: <?php echo htmlspecialchars(json_encode($row['categoria']), ENT_QUOTES); ?>,
                                colores: <?php echo htmlspecialchars(json_encode($row['colores'] ?? ""), ENT_QUOTES); ?>,
                                stock_por_color: <?php echo htmlspecialchars(json_encode($row['stock_por_color'] ?? "{}"), ENT_QUOTES); ?>,
                                stock: <?php echo intval($row['stock']); ?>
                            })'>
                            Ver Detalles
                        </button>
                    </div>
                </div>
            <?php 
                endwhile;
            else:
                echo "<p style='color:white; text-align:center; width:100%;'>No hay productos disponibles en este momento.</p>";
            endif;
            ?>
        </div>
    </section>

    <!-- Cart Sidebar UI -->
    <div class="cart-overlay" onclick="toggleCart()"></div>
    <div class="cart-sidebar">
        <div class="cart-header">
            <h3 style="margin:0;">Tu Carrito</h3>
            <button class="close-cart" onclick="toggleCart()">×</button>
        </div>
        <div class="cart-items" id="cart-items-container">
            <!-- Items inyectados por JS -->
            <p style="text-align:center; color:#999;">Tu carrito está vacío.</p>
        </div>
        <div class="cart-footer">
            <div style="display:flex; justify-content:space-between; margin-bottom:15px; font-weight:bold;">
                <span>Total:</span>
                <span id="cart-total">$0.00 BCV</span>
            </div>
            <button class="button-primary w-full" onclick="checkout()">Finalizar Compra</button>
        </div>
    </div>

    <!-- Product Detail Modal -->
    <div id="product-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeProductModal()">&times;</span>
            <div class="featured-wrapper" style="margin: 0; padding: 40px;">
                <div class="featured-slider-container mini-slider" id="modal-slider">
                    <!-- Images injected by JS -->
                </div>
                <div class="featured-info">
                    <span class="featured-tag" id="modal-category">Categoría</span>
                    <h2 id="modal-title" style="font-family: 'Syne', sans-serif; font-size: 3rem; margin: 10px 0; color: white;">Nombre Producto</h2>
                    <p id="modal-desc" style="color: #aaa; font-size: 1.1rem; line-height: 1.6;">Descripción...</p>
                    
                    <!-- NUEVO: Selector de Color -->
                    <div class="color-selector" style="margin-bottom: 20px;">
                        <p style="margin-bottom: 10px; font-weight: 600;">Color / Modelo:</p>
                        <p id="modal-colors-display" style="font-size: 0.9rem; color: #00BFFF; margin-bottom: 8px;"></p>
                        <div id="modal-color-btns" style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:10px;"></div>
                        <input type="text" id="modal-color-input" placeholder="Escribe el color (Ej: Negro, Azul...)" style="width: 100%; padding: 12px; background: #222; border: 1px solid #444; color: white; border-radius: 8px; font-family: 'Inter', sans-serif;">
                    </div>

                    <div class="size-selector" id="modal-sizes-container" style="display:none;">
                        <p style="margin-bottom: 10px; font-weight: 600;">Tallas Disponibles:</p>
                        <div id="modal-sizes-btns"></div>
                    </div>

                    <div class="quantity-selector" style="margin-bottom: 20px;">
                        <p style="margin-bottom: 10px; font-weight: 600;">Cantidad:</p>
                        <div class="qty-selector">
                            <button class="qty-btn" onclick="changeQty(-1)">-</button>
                            <input type="number" id="modal-qty" class="qty-input" value="1" min="1" readonly>
                            <button class="qty-btn" onclick="changeQty(1)">+</button>
                        </div>
                        <p id="modal-stock-display" style="color: #00BFFF; font-size: 0.9rem; margin-top: 5px;"></p>
                    </div>

                    <!-- NUEVO: Apartado de Precisión -->
                    <div class="modal-extras">
                        <span class="size-guide-btn" onclick="toggleSizeGuide()">📏 Ver Guía de Tallas y Medidas</span>
                        <table class="size-guide-table" id="size-guide">
                            <thead><tr><th>Talla</th><th>Pecho (cm)</th><th>Cintura (cm)</th><th>Largo (cm)</th></tr></thead>
                            <tbody>
                                <tr><td>S</td><td>90-96</td><td>76-81</td><td>70</td></tr>
                                <tr><td>M</td><td>96-102</td><td>81-87</td><td>72</td></tr>
                                <tr><td>L</td><td>102-108</td><td>87-93</td><td>74</td></tr>
                                <tr><td>XL</td><td>108-114</td><td>93-99</td><td>76</td></tr>
                            </tbody>
                        </table>
                        
                        <label style="display:block; margin-bottom:8px; font-weight:600; color:#eee; font-size:0.95rem;">Personalización / Notas (Opcional):</label>
                        <textarea id="modal-note" class="custom-note-area" rows="2" placeholder="Ej: Mis medidas exactas son 100cm de pecho. Prefiero un ajuste holgado."></textarea>
                    </div>

                    <div id="modal-price-container" style="margin-bottom: 30px;"></div>
                    <button class="button-primary" id="modal-add-btn" style="padding: 15px 40px; font-size: 1.1rem;">Agregar al Carrito</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Pago (Comprobante) -->
    <div id="payment-modal" class="modal">
        <div class="modal-content" style="max-width: 500px; padding: 30px; text-align: center;">
            <span class="close-modal" onclick="closePaymentModal()">&times;</span>
            <h2 style="font-family: 'Syne', sans-serif; margin-bottom: 20px; color: white;">Confirmar Pago</h2>
            <p style="color: #ccc; margin-bottom: 20px;">
                Por favor realiza tu pago móvil o transferencia y sube la captura de pantalla para procesar tu pedido.
            </p>
            
            <div style="background: #222; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: left; font-size: 0.9rem; color: #aaa;">
                <strong>Datos Bancarios:</strong><br>
                Banco: Banesco<br>
                Teléfono: 04243257757<br>
                Cédula: 19846153
            </div>

            <div style="text-align: left; margin-bottom: 20px;">
                <label style="color: #fff; display: block; margin-bottom: 8px; font-weight: 600;">Opciones de Retiro / Envío:</label>
                <select id="shipping-method" onchange="toggleShippingAddress()" style="width: 100%; padding: 10px; background: #2a2a2a; border: 1px solid #444; color: white; border-radius: 5px; margin-bottom: 10px;">
                    <option value="Tienda">🏪 Retiro en Tienda Física (Gratis)</option>
                    <option value="Nacional">🚚 Envíos a nivel nacional (MRW, ZOOM - Costo Adicional)</option>
                    <option value="Delivery">🛵 Envíos locales a Caracas (Delivery - Costo Adicional)</option>
                </select>
                
                <div id="shipping-address-container" style="display:none;">
                    <label style="color: #aaa; display: block; margin-bottom: 5px; font-size: 0.9rem;">Dirección de Envío:</label>
                    <textarea id="shipping-address" rows="2" placeholder="Indica tu dirección exacta o agencia de envío..." style="width: 100%; padding: 10px; background: #222; border: 1px solid #444; color: white; border-radius: 5px; resize: vertical;"></textarea>
                </div>
                <p id="pickup-note" style="color: #00BFFF; font-size: 0.85rem; margin-top: 5px;">📍 ¡No olvides llevar tu comprobante de pago al retirar!</p>
                
                <div style="margin-top: 15px; padding: 15px; background: #1a1a1a; border-radius: 8px; border: 1px solid #333; text-align: center;">
                    <p style="color: #ccc; font-size: 0.9rem; margin-bottom: 10px;">Para más información contacte con nosotros:</p>
                    <a href="https://wa.me/584128068267?text=Hola,%20me%20gustar%C3%ADa%20saber%20m%C3%A1s%20sobre%20mi%20pedido" target="_blank" style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; background: #25D366; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-weight: bold; font-size: 0.9rem; transition: transform 0.2s;">
                        <svg viewBox="0 0 24 24" fill="white" width="20" height="20"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></path></svg>
                        Consultar por WhatsApp
                    </a>
                </div>
            </div>

            <input type="file" id="payment-file" accept="image/*" style="width: 100%; padding: 10px; background: #2a2a2a; border: 1px solid #444; color: white; border-radius: 5px; margin-bottom: 20px;">
            
            <button class="button-primary w-full" onclick="submitPayment()">Enviar Comprobante y Finalizar</button>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-grid">
            <div class="footer-col">
                <h4>MYMOZ13</h4>
                <p>Tu tienda de moda favorita en Caracas. Ofrecemos estilo, calidad y las últimas tendencias para que siempre luzcas increíble.</p>
            </div>
            <div class="footer-col">
                <h4>Contacto</h4>
                <p>📍 Ubicados en Caracas en la Av. principal del cementerio, mercado merponorte, (al lado del mercado las flores 💐) Pasillo aguila al final. Entrada al anexo Local 21.</p>
                <p>� +58 412-806-8267</p>
                <p>✉️ contacto@mymoz13.com</p>
            </div>
            <div class="footer-col">
                <h4>Enlaces Rápidos</h4>
                <p><a href="#">Sobre Nosotros</a></p>
                <p><a href="#">Política de Envíos</a></p>
                <p><a href="#">Términos y Condiciones</a></p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 MYMOZ13. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- NUEVO: Widget de Chat Flotante -->
    <div class="chat-widget">
        <div class="chat-box" id="chat-box">
            <div class="chat-header">
                <span>Soporte MYMOZ13</span>
                <span style="cursor:pointer;" onclick="toggleChat()">×</span>
            </div>
            <div class="chat-body">
                <div class="chat-msg">¡Hola! 👋<br>¿Tienes dudas con tu talla o necesitas un pedido personalizado? Escríbenos.</div>
            </div>
            <div class="chat-footer">
                <input type="text" class="chat-input" id="chat-input-text" placeholder="Escribe tu mensaje..." onkeypress="handleChatKey(event)">
                <button class="chat-send" onclick="sendToWhatsApp()">➤</button>
            </div>
        </div>
        <div class="chat-btn" onclick="toggleChat()">
            <!-- Icono WhatsApp SVG -->
            <svg viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
        </div>
    </div>

    <script>
        // --- Lógica del Carrito ---
        let selectedSize = '';
        let selectedColor = '';
        let currentModalProduct = {};
        let modalSliderInterval;

        function openProductModal(product) {
            currentModalProduct = product;
            selectedSize = ''; // Reset size
            selectedColor = ''; // Reset color
            document.getElementById('modal-qty').value = 1; // Reset qty
            document.getElementById('modal-color-input').value = ''; // Reset color input
            document.getElementById('modal-note').value = ''; // Reset note
            document.getElementById('size-guide').style.display = 'none'; // Hide guide

            // Populate Data
            document.getElementById('modal-title').innerText = product.nombre;
            document.getElementById('modal-desc').innerText = product.descripcion || "Sin descripción disponible.";
            
            // Precio en Modal
            const priceContainer = document.getElementById('modal-price-container');
            if (product.en_oferta == 1 && product.porcentaje > 0) {
                priceContainer.innerHTML = `
                    <span style="text-decoration: line-through; color: #888; font-size: 1.2rem; margin-right: 15px;">$${parseFloat(product.precio_original).toFixed(2)}</span>
                    <span style="color: #FF00FF; font-size: 2rem; font-weight: bold;">$${parseFloat(product.precio).toFixed(2)} BCV</span>
                    <span style="background: #FF0000; color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.9rem; vertical-align: middle; margin-left: 10px;">-${product.porcentaje}% OFF</span>
                `;
            } else {
                priceContainer.innerHTML = `<span style="color: #FF00FF; font-size: 2rem; font-weight: bold;">$${parseFloat(product.precio).toFixed(2)} BCV</span>`;
            }
            
            document.getElementById('modal-category').innerText = product.categoria;
            document.getElementById('modal-stock-display').innerText = "Disponibles: " + product.stock;

            // Mostrar Colores Disponibles
            const colorsEl = document.getElementById('modal-colors-display');
            const colorBtnsContainer = document.getElementById('modal-color-btns');
            const colorInput = document.getElementById('modal-color-input');
            
            colorsEl.innerText = "";
            colorBtnsContainer.innerHTML = "";
            
            // Parsear stock por color
            let stockMap = {};
            try {
                stockMap = JSON.parse(product.stock_por_color || '{}');
            } catch(e) { stockMap = {}; }

            if (Object.keys(stockMap).length > 0) {
                // Si hay stock por color configurado, mostrar botones
                colorInput.style.display = 'none';
                for (const [color, qty] of Object.entries(stockMap)) {
                    if (qty > 0) {
                        colorBtnsContainer.innerHTML += `<button class="size-btn color-btn" onclick="selectColor(this, '${color}', ${qty})">${color}</button>`;
                    }
                }
                colorsEl.innerText = "Selecciona un color para ver disponibilidad:";
            } else {
                // Modo antiguo (texto libre)
                colorInput.style.display = 'block';
                if (product.colores) {
                    colorsEl.innerText = "Disponibles: " + product.colores;
                }
            }

            // Images
            const sliderContainer = document.getElementById('modal-slider');
            sliderContainer.innerHTML = '';
            const imgs = product.imagenes.split(',');
            imgs.forEach((img, index) => {
                const activeClass = index === 0 ? 'active' : '';
                sliderContainer.innerHTML += `<img src="${img.trim()}" class="product-slider-img ${activeClass}" alt="${product.nombre}">`;
            });

            // Restart Slider Animation
            if (modalSliderInterval) clearInterval(modalSliderInterval);
            if (imgs.length > 1) {
                let idx = 0;
                const slides = sliderContainer.getElementsByClassName('product-slider-img');
                modalSliderInterval = setInterval(() => {
                    slides[idx].classList.remove('active');
                    idx = (idx + 1) % slides.length;
                    slides[idx].classList.add('active');
                }, 3000);
            }

            // Sizes
            const sizeContainer = document.getElementById('modal-sizes-container');
            const sizeBtns = document.getElementById('modal-sizes-btns');
            sizeBtns.innerHTML = '';
            if (product.tallas && product.tallas.trim() !== "") {
                sizeContainer.style.display = 'block';
                // Separa por comas, espacios, barras o pipes en JS
                const tallas = product.tallas.split(/[, \/|]+/);
                tallas.forEach(t => {
                    if (t.trim() !== "") {
                        sizeBtns.innerHTML += `<button class="size-btn" onclick="selectSize(this, '${t.trim()}')">${t.trim()}</button>`;
                    }
                });
            } else {
                sizeContainer.style.display = 'none';
            }

            // Add Button Action
            document.getElementById('modal-add-btn').onclick = function() {
                let finalName = product.nombre;
                
                // Capturar Color
                let color = "";
                if (Object.keys(stockMap).length > 0) {
                    if (!selectedColor) {
                        alert("Por favor selecciona un color.");
                        return;
                    }
                    color = selectedColor;
                } else {
                    color = document.getElementById('modal-color-input').value.trim();
                }

                if (color) {
                    finalName += " | Color: " + color;
                }

                if (product.tallas && product.tallas.trim() !== "") {
                    if (!selectedSize) {
                        alert("Por favor selecciona una talla.");
                        return;
                    }
                    finalName += " (Talla " + selectedSize + ")";
                }
                const qty = parseInt(document.getElementById('modal-qty').value);
                
                // Validar stock máximo (global o por color seleccionado)
                const maxStock = (selectedColor && stockMap[selectedColor]) ? stockMap[selectedColor] : product.stock;
                if (qty > maxStock) {
                    alert("No hay suficiente stock disponible. Máximo: " + maxStock);
                    return;
                }
                
                // Capturar nota personalizada
                const note = document.getElementById('modal-note').value.trim();
                if (note) {
                    finalName += " | Nota: " + note;
                }
                
                addToCart(finalName, product.precio, imgs[0].trim(), qty);
                closeProductModal();
            };

            document.getElementById('product-modal').style.display = 'block';
        }

        function closeProductModal() {
            document.getElementById('product-modal').style.display = 'none';
            if (modalSliderInterval) clearInterval(modalSliderInterval);
        }

        function changeQty(delta) {
            const input = document.getElementById('modal-qty');
            let val = parseInt(input.value) + delta;
            // Si hay color seleccionado, usar su stock, si no, el global
            let max = (selectedColor && currentModalProduct.stock_por_color) ? JSON.parse(currentModalProduct.stock_por_color)[selectedColor] : (currentModalProduct.stock || 999);
            if (val < 1) val = 1;
            if (val > max) val = max;
            input.value = val;
        }

        function selectSize(btn, size) {
            document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            selectedSize = size;
        }

        function selectColor(btn, color, qty) {
            document.querySelectorAll('.color-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            selectedColor = color;
            document.getElementById('modal-stock-display').innerText = "Disponibles: " + qty;
            document.getElementById('modal-qty').value = 1; // Reset qty al cambiar color
        }

        function toggleSizeGuide() {
            const guide = document.getElementById('size-guide');
            guide.style.display = (guide.style.display === 'none' || guide.style.display === '') ? 'table' : 'none';
        }

        function addFeaturedToCart(nombre, precio, imagen) {
            if (!selectedSize) {
                alert("Por favor selecciona una talla antes de agregar al carrito.");
                return;
            }
            addToCart(nombre + " (Talla " + selectedSize + ")", precio, imagen);
        }

        function toggleCart() {
            document.querySelector('.cart-sidebar').classList.toggle('open');
            document.querySelector('.cart-overlay').classList.toggle('open');
            updateCartUI();
        }

        async function addToCart(producto, precio, imagen, cantidad = 1) {
            const res = await fetch('ajax_cart.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ action: 'add', producto, precio, imagen, cantidad })
            });
            const data = await res.json();
            if(data.status === 'success') {
                updateCartCount(data.cart);
                toggleCart(); // Abrir carrito para mostrar el item
            }
        }

        async function removeFromCart(index) {
            const res = await fetch('ajax_cart.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ action: 'remove', index })
            });
            const data = await res.json();
            if(data.status === 'success') {
                updateCartUI(); // Recargar UI
            }
        }

        async function updateCartUI() {
            const res = await fetch('ajax_cart.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ action: 'get' })
            });
            const data = await res.json();
            const container = document.getElementById('cart-items-container');
            const totalEl = document.getElementById('cart-total');
            const countEl = document.getElementById('cart-count');
            
            container.innerHTML = '';
            let total = 0;
            let count = 0;

            if (data.cart && data.cart.length > 0) {
                data.cart.forEach((item, index) => {
                    total += item.precio * item.cantidad;
                    count += item.cantidad;
                    container.innerHTML += `
                        <div class="cart-item">
                            <img src="${item.imagen}" alt="${item.nombre}">
                            <div style="flex:1;">
                                <div style="font-weight:600;">${item.nombre}</div>
                                <div style="color:#666; font-size:0.9rem;">$${item.precio} BCV x ${item.cantidad}</div>
                                <span class="remove-item" onclick="removeFromCart(${index})">Eliminar</span>
                            </div>
                            <div style="font-weight:bold;">$${(item.precio * item.cantidad).toFixed(2)} BCV</div>
                        </div>
                    `;
                });
            } else {
                container.innerHTML = '<p style="text-align:center; color:#999; margin-top:20px;">Tu carrito está vacío.</p>';
            }
            
            totalEl.innerText = '$' + total.toFixed(2) + ' BCV';
            countEl.innerText = count;
        }

        function updateCartCount(cart) {
            let count = 0;
            cart.forEach(item => count += item.cantidad);
            document.getElementById('cart-count').innerText = count;
        }

        function checkout() {
            // Verificar sesión antes de abrir modal (chequeo rápido en cliente, el servidor valida igual)
            <?php if (!$is_logged_in): ?>
                alert("Debes iniciar sesión para completar la compra.");
                window.location.href = 'login.php';
                return;
            <?php endif; ?>
            
            const count = parseInt(document.getElementById('cart-count').innerText);
            if (count === 0) {
                alert("Tu carrito está vacío.");
                return;
            }

            // Abrir modal de pago
            document.getElementById('payment-modal').style.display = 'block';
        }

        function closePaymentModal() {
            document.getElementById('payment-modal').style.display = 'none';
        }

        function toggleShippingAddress() {
            const method = document.getElementById('shipping-method').value;
            const addrContainer = document.getElementById('shipping-address-container');
            const pickupNote = document.getElementById('pickup-note');

            if (method === 'Tienda') {
                addrContainer.style.display = 'none';
                pickupNote.style.display = 'block';
            } else {
                addrContainer.style.display = 'block';
                pickupNote.style.display = 'none';
            }
        }

        async function submitPayment() {
            const fileInput = document.getElementById('payment-file');
            if (fileInput.files.length === 0) {
                alert("Por favor selecciona la imagen del comprobante.");
                return;
            }

            const method = document.getElementById('shipping-method').value;
            const address = document.getElementById('shipping-address').value;

            if (method !== 'Tienda' && address.trim() === '') {
                alert("Por favor ingresa la dirección de envío.");
                return;
            }

            const formData = new FormData();
            formData.append('action', 'checkout');
            formData.append('comprobante', fileInput.files[0]);
            formData.append('metodo_retiro', method);
            formData.append('direccion_envio', address);

            // Usamos fetch sin Content-Type header manual para que el navegador configure el boundary del FormData
            const res = await fetch('ajax_cart.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await res.json();

            if (data.status === 'success') {
                alert("✅ " + data.message);
                closePaymentModal();
                updateCartUI();
                toggleCart();
            } else {
                alert("Error: " + data.message);
            }
        }

        // Cargar carrito al inicio
        updateCartUI();

        // --- Slider Automático ---
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slide');
        setInterval(() => {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
        }, 5000); // Cambia cada 5 segundos

        // --- Slider Automático para Ofertas (Portada) ---
        document.querySelectorAll('.auto-slider').forEach(slider => {
            let bgSlides = slider.querySelectorAll('.offer-bg-img');
            if (bgSlides.length > 1) {
                let idx = 0;
                setInterval(() => {
                    bgSlides[idx].classList.remove('active');
                    idx = (idx + 1) % bgSlides.length;
                    bgSlides[idx].classList.add('active');
                }, 4000); // Cambia cada 4 segundos
            }
        });

        // --- Mini Slider Productos (Franelas) ---
        // Rota las imágenes de las tarjetas que tienen múltiples fotos cada 1.5s
        document.querySelectorAll('.mini-slider').forEach(slider => {
            let productSlides = slider.querySelectorAll('.product-slider-img');
            if (productSlides.length > 1) {
                let index = 0;
                setInterval(() => {
                    productSlides[index].classList.remove('active');
                    index = (index + 1) % productSlides.length;
                    productSlides[index].classList.add('active');
                }, 3000); // 3 segundos
            }
        });

        // Lógica del Menú Móvil
        document.querySelector('.mobile-toggle').addEventListener('click', function() {
            document.querySelector('.nav-links').classList.toggle('active');
            document.querySelector('.nav-menu').classList.toggle('active');
        });

        // --- Buscador en Tiempo Real ---
        function setFilter(category) {
            document.getElementById('search-input').value = category;
            filterProducts();
        }

        function filterProducts() {
            const input = document.getElementById('search-input');
            const filter = input.value.toLowerCase();
            const grid = document.querySelector('.catalog-grid');
            const cards = grid.getElementsByClassName('product-card');
            let visibleCount = 0;
            
            // Si el usuario empieza a buscar y está muy arriba, hacer scroll suave hacia el catálogo
            if (filter.length > 0 && window.scrollY < grid.offsetTop - 300) {
                window.scrollTo({ top: grid.offsetTop - 150, behavior: 'smooth' });
            }

            for (let i = 0; i < cards.length; i++) {
                const searchData = cards[i].getAttribute('data-search');
                if (searchData && searchData.indexOf(filter) > -1) {
                    cards[i].style.display = "";
                    visibleCount++;
                } else {
                    cards[i].style.display = "none";
                }
            }
            
            // Mensaje de "No hay resultados"
            let noResults = document.getElementById('no-results-msg');
            if (visibleCount === 0) {
                if (!noResults) {
                    noResults = document.createElement('p');
                    noResults.id = 'no-results-msg';
                    noResults.style.cssText = 'color: #aaa; text-align: center; width: 100%; grid-column: 1 / -1; padding: 20px; font-size: 1.1rem;';
                    noResults.innerText = 'No se encontraron productos que coincidan con tu búsqueda.';
                    grid.appendChild(noResults);
                }
                noResults.style.display = 'block';
            } else if (noResults) {
                noResults.style.display = 'none';
            }
        }

        // --- Lógica del Chat ---
        function toggleChat() {
            document.getElementById('chat-box').classList.toggle('open');
        }

        function handleChatKey(e) {
            if (e.key === 'Enter') sendToWhatsApp();
        }

        function sendToWhatsApp() {
            const input = document.getElementById('chat-input-text');
            const msg = input.value.trim();
            // Número de teléfono del admin (Reemplazar con el real)
            const phone = "584128068267"; 
            let url = `https://wa.me/${phone}?text=`;
            if (msg) {
                url += encodeURIComponent("Hola, estoy en la tienda y tengo una consulta: " + msg);
            } else {
                url += encodeURIComponent("Hola, quisiera asesoría sobre un producto.");
            }
            
            window.open(url, '_blank');
            input.value = '';
            toggleChat();
        }
    </script>
</body>
</html>