<?php
require 'session_check.php';
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Evita que el navegador almacene la página en caché
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Manejo de acciones (Actualizar o Eliminar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        // Actualizar punto
        $id = $_POST['pointId'];
        $name = $_POST['pointName'];
        $description = $_POST['pointDescription'];
        $category_id = $_POST['pointCategory'];
        $lat = $_POST['pointLat'];
        $lng = $_POST['pointLng'];

        $stmt = $conn->prepare("UPDATE puntos SET nombre = ?, descripcion = ?, ID_categoria = ?, latitud = ?, longitud = ? WHERE ID_punto = ?");
        $stmt->execute([$name, $description, $category_id, $lat, $lng, $id]);
        $message = "Punto actualizado exitosamente.";
    }

    if (isset($_POST['delete'])) {
        // Eliminar punto
        $id = $_POST['pointId'];

        $stmt = $conn->prepare("DELETE FROM puntos WHERE ID_punto = ?");
        $stmt->execute([$id]);
        $message = "Punto eliminado exitosamente.";
    }
}

// Obtener categorías y puntos
$categorias = $conn->query("SELECT * FROM categorias")->fetchAll();
$puntos = $conn->query("SELECT * FROM puntos")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Puntos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            height: 100vh;
            margin: 0;
        }

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

        #sidebar:hover {
            width: 250px;
        }

        .content-wrapper {
            margin-left: 60px;
            flex: 1;
            padding: 20px;
        }

        #sidebar:hover + .content-wrapper {
            margin-left: 250px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        .form-container {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="content-wrapper">
        <header class="p-3 bg-light border-bottom">
            <h3>Administración de Puntos Georreferenciados</h3>
            <?php if (isset($message)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($message); ?></div>
            <?php endif; ?>
        </header>

        <main class="p-4">
            <!-- Tabla para listar puntos -->
            <h4>Lista de Puntos</h4>
            <table class="table table-striped" id="pointsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Categoría</th>
                        <th>Latitud</th>
                        <th>Longitud</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($puntos as $punto): ?>
                        <tr>
                            <td><?= htmlspecialchars($punto['ID_punto']); ?></td>
                            <td><?= htmlspecialchars($punto['nombre']); ?></td>
                            <td><?= htmlspecialchars($punto['descripcion']); ?></td>
                            <td><?= htmlspecialchars($punto['ID_categoria']); ?></td>
                            <td><?= htmlspecialchars($punto['latitud']); ?></td>
                            <td><?= htmlspecialchars($punto['longitud']); ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-btn" 
                                    data-id="<?= $punto['ID_punto']; ?>" 
                                    data-name="<?= htmlspecialchars($punto['nombre']); ?>"
                                    data-description="<?= htmlspecialchars($punto['descripcion']); ?>"
                                    data-category="<?= htmlspecialchars($punto['ID_categoria']); ?>"
                                    data-lat="<?= htmlspecialchars($punto['latitud']); ?>"
                                    data-lng="<?= htmlspecialchars($punto['longitud']); ?>">
                                    Editar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Formulario para editar puntos -->
            <div class="form-container">
                <h4>Editar Punto</h4>
                <form method="POST" action="">
                    <input type="hidden" name="pointId" id="pointId">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="pointName" class="form-label">Nombre</label>
                            <input type="text" name="pointName" id="pointName" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="pointDescription" class="form-label">Descripción</label>
                            <input type="text" name="pointDescription" id="pointDescription" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="pointCategory" class="form-label">Categoría</label>
                            <select name="pointCategory" id="pointCategory" class="form-select" required>
                                <option value="">Selecciona una categoría</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= htmlspecialchars($categoria['ID_categoria']); ?>"><?= htmlspecialchars($categoria['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <label for="pointLat" class="form-label">Latitud</label>
                            <input type="text" name="pointLat" id="pointLat" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="pointLng" class="form-label">Longitud</label>
                            <input type="text" name="pointLng" id="pointLng" class="form-control" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" name="update" class="btn btn-primary">Actualizar</button>
                        <button type="submit" name="delete" class="btn btn-danger">Eliminar</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                const description = button.getAttribute('data-description');
                const category = button.getAttribute('data-category');
                const lat = button.getAttribute('data-lat');
                const lng = button.getAttribute('data-lng');

                document.getElementById('pointId').value = id;
                document.getElementById('pointName').value = name;
                document.getElementById('pointDescription').value = description;
                document.getElementById('pointCategory').value = category;
                document.getElementById('pointLat').value = lat;
                document.getElementById('pointLng').value = lng;
            });
        });
    </script>
</body>
</html>
