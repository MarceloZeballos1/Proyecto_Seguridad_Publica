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
            <div class="d-flex align-items-center">

                <!-- Dropdown para cargar GeoJSON -->
                <select id="geojsonFilter" class="form-select w-auto" onchange="loadSelectedGeoJSON(this.value)">
                    <option value="">Selecciona un archivo GeoJSON 🗂️</option>
                    <?php
                    $geojsonPath = 'uploads/layers/';
                    $files = array_filter(scandir($geojsonPath), fn($file) => pathinfo($file, PATHINFO_EXTENSION) === 'geojson');
                    foreach ($files as $file): ?>
                        <option value="<?= htmlspecialchars($geojsonPath . $file); ?>"><?= htmlspecialchars($file); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </header>

        <main class="p-4">
            <div id="map"></div>
        </main>
    </div>

    <script>
        var map, markers = [], polygons = [];

        function initMap() {
            const tarija = { lat: -21.5355, lng: -64.7296 };
            map = new google.maps.Map(document.getElementById('map'), { center: tarija, zoom: 13 });

            google.maps.event.addListener(map, 'click', function(event) {
                const lat = event.latLng.lat();
                const lng = event.latLng.lng();
                openAddPointModal(lat, lng);
            });
        }

        function loadSelectedGeoJSON(filePath) {
            clearMarkersAndPolygons();

            if (!filePath) {
                alert('Por favor, selecciona un archivo GeoJSON.');
                return;
            }

            $.getJSON(filePath, function(data) {
                data.features.forEach(function(feature) {
                    const geometry = feature.geometry;

                    if (geometry.type === 'Point') {
                        const coords = geometry.coordinates;
                        const marker = new google.maps.Marker({
                            position: { lat: coords[1], lng: coords[0] },
                            map: map,
                            title: feature.properties.descrip || 'Sin descripción'
                        });
                        markers.push(marker);
                    } else if (geometry.type === 'MultiPolygon') {
                        geometry.coordinates.forEach(function(polygon) {
                            const paths = polygon[0].map(coord => ({ lat: coord[1], lng: coord[0] }));
                            const poly = new google.maps.Polygon({
                                paths: paths,
                                map: map,
                                strokeColor: '#FF0000',
                                strokeOpacity: 0.8,
                                strokeWeight: 2,
                                fillColor: '#FF0000',
                                fillOpacity: 0.35
                            });
                            polygons.push(poly);
                        });
                    }
                });
            }).fail(function() {
                alert('No se pudo cargar el archivo GeoJSON.');
            });
        }

        function clearMarkersAndPolygons() {
            markers.forEach(marker => marker.setMap(null));
            markers = [];
            polygons.forEach(polygon => polygon.setMap(null));
            polygons = [];
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
