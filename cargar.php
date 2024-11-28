<?php 
require 'session_check.php'; 
require 'db.php'; 
require 'Category.php'; 

try { 
    $categoryModel = new Category($conn); 
    $categorias = $categoryModel->getAllCategories(); 
} catch (Exception $e) { 
    echo $e->getMessage(); 
    exit(); 
} 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'savePoint':
            $lat = $_POST['lat']; 
            $lng = $_POST['lng']; 
            $name = $_POST['name']; 
            $description = $_POST['description']; 
            $category_id = $_POST['category_id']; 
            try { 
                $stmt = $conn->prepare("INSERT INTO puntos (latitud, longitud, nombre, descripcion, ID_categoria) VALUES (?, ?, ?, ?, ?)"); 
                $stmt->execute([$lat, $lng, $name, $description, $category_id]);  
                echo json_encode(['success' => true]); 
            } catch (Exception $e) { 
                echo json_encode(['success' => false, 'error' => $e->getMessage()]); 
            } 
            break;

        case 'filterPoints':
            $categoryId = $_POST['category_id']; 
            try { 
                $stmt = $conn->prepare("SELECT p.*, c.geojson_path, c.icono 
                        FROM puntos p 
                        JOIN categorias c ON p.ID_categoria = c.id 
                        WHERE p.ID_categoria = ?");
                $stmt->execute([$categoryId]); 
                $points = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode([
                    'points' => $points,
                    'geojson_path' => $points[0]['geojson_path'] ?? null
                ]);
            } catch (Exception $e) { 
                echo json_encode(['success' => false, 'error' => $e->getMessage()]); 
            } 
            break;
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargar Capas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <style>
        body {
            display: flex;
            height: 100vh;
            margin: 0;
        }

        #map {
            height: calc(100vh - 60px);
            width: 100%;
        }

        .content-wrapper {
            margin-left: 60px;
            flex: 1;
            padding: 10px;
        }

        #sidebar:hover + .content-wrapper {
            margin-left: 250px;
        }

        form {
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        form label {
            font-weight: bold;
            margin-bottom: 8px;
        }

        form input, form select, form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        form button {
            background-color: #007bff;
            color: white;
            border: none;
        }

        form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Contenido principal -->
    <div class="content-wrapper">
        <header class="p-3 bg-light border-bottom">
            <h3>Cargar Nueva Categoría</h3>
        </header>

        <main class="p-4">
            <form action="procesar_carga.php" method="POST" enctype="multipart/form-data">
                <label for="category_name">Nombre de la categoría:</label>
                <input type="text" id="category_name" name="category_name" required>

                <label for="icono">Ícono de la categoría:</label>
                <input type="file" id="icono" name="icono" accept="image/*">

                <label for="geojson">Archivo GeoJSON:</label>
                <input type="file" id="geojson" name="geojson" accept=".geojson">

                <label for="qmd">Archivo QMD:</label>
                <input type="file" id="qmd" name="qmd" accept=".qmd">

                <button type="submit">Guardar Categoría</button>
            </form>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
