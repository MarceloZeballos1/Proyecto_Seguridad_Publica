<?php
require 'session_check.php';
require 'db.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
    

// Consultar datos de las categorías
$categorias = $conn->query("SELECT * FROM categorias")->fetchAll();

// Consultar puntos y sus recursos
$puntos = $conn->query("
    SELECT 
        p.ID_punto, 
        p.nombre AS nombre_punto, 
        p.tipo_punto, 
        p.latitud, 
        p.longitud, 
        p.descripcion, 
        c.nombre AS categoria_nombre, 
        r.descripcion_recursos, 
        r.zona_riesgo, 
        r.frecuencia_incidentes
    FROM puntos p
    LEFT JOIN categorias c ON p.ID_categoria = c.ID_categoria
    LEFT JOIN recursos_riesgo r ON p.ID_punto = r.ID_punto
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv=”Content-Type” content=”text/html; charset=UTF-8″ />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <SCRIPT LANGUAGE="JavaScript">
    history.forward()
    </SCRIPT>
    <style>
        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        main {
            margin-left: 60px; /* Espacio inicial para el sidebar */
            padding: 20px;
            width: 100%;
            transition: margin-left 0.3s ease;
        }
        .sidebar:hover + main {
            margin-left: 250px; /* Ajuste cuando el sidebar se expande */
        }
        header {
            background-color: #343a40;
            color: white;
            padding: 15px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Contenido Principal -->
    <main>
        <header>
            <h1>Dashboard</h1>
            <p>Bienvenido, <strong><?= $_SESSION['username']; ?></strong></p>
        </header>

        <div class="container mt-4">
            <!-- Sección de Categorías -->
            <h2>Categorías</h2>
            <div class="row">
                <?php foreach ($categorias as $categoria): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?= $categoria['nombre']; ?></h5>
                                <p><img src="<?= $categoria['icono']; ?>" alt="<?= $categoria['nombre']; ?>" style="width: 50px;"></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Sección de Puntos y Recursos -->
            <h2>Puntos de Seguridad</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Categoría</th>
                        <th>Descripción</th>
                        <th>Coordenadas</th>
                        <th>Recursos</th>
                        <th>Zona de Riesgo</th>
                        <th>Frecuencia de Incidentes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($puntos as $punto): ?>
                        <tr>
                            <td><?= $punto['nombre_punto']; ?></td>
                            <td><?= $punto['tipo_punto']; ?></td>
                            <td><?= $punto['categoria_nombre']; ?></td>
                            <td><?= $punto['descripcion']; ?></td>
                            <td><?= $punto['latitud']; ?>, <?= $punto['longitud']; ?></td>
                            <td><?= $punto['descripcion_recursos'] ?: 'N/A'; ?></td>
                            <td><?= $punto['zona_riesgo'] ?: 'N/A'; ?></td>
                            <td><?= $punto['frecuencia_incidentes'] ?: 'N/A'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
