<?php
require 'db.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $conn->prepare("SELECT geojson_path, icono FROM categorias WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        header('Content-Type: application/json');
        echo json_encode([
            'geojson_path' => $row['geojson_path'],
            'icon_path' => $row['icono']
        ]);
    } else {
        echo json_encode(['error' => 'Categoría no encontrada.']);
    }
}
?>
