<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Evita que el navegador almacene la página en caché
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require 'db.php';

// Si no tiene un rol válido, redirigir
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$users = $conn->query("SELECT * FROM users")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta http-equiv=”Content-Type” content=”text/html; charset=UTF-8″ />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA3rSjXWyW5t2AP70wNn5KsTsR925e5GGk&callback=initMap" async defer></script>
    <style>
        body {
            display: flex;
            height: 100vh;
            margin: 0;
        }

        /* Ajuste para el sidebar */
        #sidebar {
            width: 60px;
            background-color: #343a40;
            color: #fff;
            padding-top: 20px;
            position: fixed;
            height: 100%;
            transition: width 0.3s ease;
            z-index: 1000;
        }

        /* Expande el sidebar cuando se hace hover */
        #sidebar:hover {
            width: 250px;
        }

        /* Estilo del contenido principal */
        .content-wrapper {
            margin-left: 60px;
            width: calc(100% - 60px);
            padding: 20px;
        }

        /* Mapa con altura correcta */
        #map {
            height: 80vh;
            width: 100%;
        }

        /* Estilos de los íconos y textos */
        .sidebar-content {
            display: flex;
            flex-direction: column;
            align-items: start;
            padding: 10px 0;
            overflow-y: auto;
        }

        /* Estilo de los elementos del sidebar */
        .sidebar-item {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #fff;
            padding: 10px 20px;
            width: 100%;
            white-space: nowrap;
            transition: background-color 0.3s ease;
        }

        .sidebar-item:hover {
            background-color: #495057;
        }

        .icon {
            margin-right: 10px;
            font-size: 20px;
        }

        .text {
            display: none;
        }

        /* Mostrar texto cuando el sidebar está expandido */
        #sidebar:hover .text {
            display: inline-block;
        }

        header {
            margin-bottom: 20px;
        }

        main {
            height: 80vh;
        }
    </style>
</head>
<body class="bg-light">
    <?php include 'sidebar.php'; ?>

    <div class="content-wrapper">
        <header class="p-3 bg-light border-bottom">
            <h3>Bienvenido al Sistema de Seguridad Pública, <strong><?= $_SESSION['username']; ?></strong>!</h3>
        </header>

        <main class="p-4">
            <div id="map"></div>
        </main>
    </div>

    <script>
        function initMap() {
            const map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: -21.5311, lng: -64.7249 }, // Coordenadas de Tarija
                zoom: 14,
            });
        }
    </script>
</body>
</html>
