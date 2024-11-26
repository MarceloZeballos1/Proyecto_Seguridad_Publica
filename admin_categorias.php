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

// Manejo de acciones (Crear, Actualizar o Eliminar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        // Crear categoría
        $name = $_POST['categoryName'];

        $stmt = $conn->prepare("INSERT INTO categorias (nombre) VALUES (?)");
        $stmt->execute([$name]);
        $message = "Categoría creada exitosamente.";
    }

    if (isset($_POST['update'])) {
        // Actualizar categoría
        $id = $_POST['categoryId'];
        $name = $_POST['categoryName'];

        $stmt = $conn->prepare("UPDATE categorias SET nombre = ? WHERE ID_categoria = ?");
        $stmt->execute([$name, $id]);
        $message = "Categoría actualizada exitosamente.";
    }

    if (isset($_POST['delete'])) {
        // Eliminar categoría
        $id = $_POST['categoryId'];

        $stmt = $conn->prepare("DELETE FROM categorias WHERE ID_categoria = ?");
        $stmt->execute([$id]);
        $message = "Categoría eliminada exitosamente.";
    }
}

// Obtener categorías
$categorias = $conn->query("SELECT * FROM categorias")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Categorías</title>
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
            <h3>Administración de Categorías</h3>
            <?php if (isset($message)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($message); ?></div>
            <?php endif; ?>
        </header>

        <main class="p-4">
            <!-- Tabla para listar categorías -->
            <h4>Lista de Categorías</h4>
            <table class="table table-striped" id="categoriesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categorias as $categoria): ?>
                        <tr>
                            <td><?= htmlspecialchars($categoria['ID_categoria']); ?></td>
                            <td><?= htmlspecialchars($categoria['nombre']); ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-btn" 
                                    data-id="<?= $categoria['ID_categoria']; ?>" 
                                    data-name="<?= htmlspecialchars($categoria['nombre']); ?>">
                                    Editar
                                </button>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $categoria['ID_categoria']; ?>">Eliminar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Formulario para crear o editar categorías -->
            <div class="form-container">
                <h4>Crear o Editar Categoría</h4>
                <form method="POST" action="">
                    <input type="hidden" name="categoryId" id="categoryId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="categoryName" class="form-label">Nombre de la Categoría</label>
                            <input type="text" name="categoryName" id="categoryName" class="form-control" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" name="create" class="btn btn-success">Crear</button>
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

                document.getElementById('categoryId').value = id;
                document.getElementById('categoryName').value = name;
            });
        });

        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.getAttribute('data-id');

                if (confirm('¿Estás seguro de eliminar esta categoría?')) {
                    // Prepara el formulario para eliminar la categoría
                    document.getElementById('categoryId').value = id;
                    document.querySelector('button[name="delete"]').click();
                }
            });
        });
    </script>
</body>
</html>
