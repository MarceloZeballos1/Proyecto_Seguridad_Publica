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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'savePoint') { 
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
    exit(); 
} 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'filterPoints') { 
    $categoryId = $_POST['category_id']; 
    try { 
        $stmt = $conn->prepare("SELECT * FROM puntos WHERE ID_categoria = ?"); 
        $stmt->execute([$categoryId]); 
        $points = $stmt->fetchAll(PDO::FETCH_ASSOC); 
        echo json_encode($points);  
    } catch (Exception $e) { 
        echo json_encode(['success' => false, 'error' => $e->getMessage()]); 
    } 
    exit(); 
} 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa Tarijita Filtros y Puntos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA3rSjXWyW5t2AP70wNn5KsTsR925e5GGk&callback=initMap" async defer></script>
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
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Contenido principal -->
    <div class="content-wrapper">
        <header class="p-3 bg-light border-bottom">
            <h3>Mapa de Seguridad Pública Boliviana</h3>
            <select id="categoryFilter" class="form-select w-auto" onchange="filterPointsByCategory(this.value)">
                <option value="">Selecciona una categoría 🧭</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= htmlspecialchars($categoria['id']); ?>"><?= htmlspecialchars($categoria['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </header>

        <main class="p-4">
            <div id="map"></div>
        </main>
    </div>

    <script>
        var map, markers = [];

        function initMap() {
            const tarija = { lat: -21.5355, lng: -64.7296 };
            map = new google.maps.Map(document.getElementById('map'), { center: tarija, zoom: 13 });

            google.maps.event.addListener(map, 'click', function(event) {
                const lat = event.latLng.lat();
                const lng = event.latLng.lng();
                openAddPointModal(lat, lng);
            });
        }

        function openAddPointModal(lat, lng) {
            $('#pointLat').val(lat);
            $('#pointLng').val(lng);
            $('#addPointModal').modal('show');
        }

        $('#addPointForm').on('submit', function(e) {
            e.preventDefault();

            const name = $('#pointName').val();
            const description = $('#pointDescription').val();
            const categoryId = $('#pointCategory').val();
            const lat = $('#pointLat').val();
            const lng = $('#pointLng').val();

            if (name && description && categoryId) {
                $.post('index.php?action=savePoint', {
                    lat: lat,
                    lng: lng,
                    name: name,
                    description: description,
                    category_id: categoryId
                }, function(response) {
                    const res = JSON.parse(response);
                    alert(res.success ? 'Punto guardado exitosamente.' : `Error: ${res.error}`);
                    $('#addPointModal').modal('hide');
                });
            }
        });

        function filterPointsByCategory(categoryId) {
            $.post('index.php?action=filterPoints', { category_id: categoryId }, function(data) {
                const points = JSON.parse(data);
                clearMarkers();
                if (points.length > 0) {
                    points.forEach(function(point) {
                        const marker = new google.maps.Marker({
                            position: { lat: parseFloat(point.latitud), lng: parseFloat(point.longitud) },
                            map: map,
                            title: point.nombre || point.descripcion
                        });
                        markers.push(marker);
                    });
                } else {
                    alert("No se encontraron puntos para esta categoría.");
                }
            });
        }

        function clearMarkers() {
            markers.forEach(marker => marker.setMap(null));
            markers = [];
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
