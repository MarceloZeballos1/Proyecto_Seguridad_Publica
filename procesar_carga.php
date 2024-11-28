<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');

    $nombre = $_POST['category_name'] ?? null;
    $uploadDir = 'uploads/';

    // Validar que el nombre de la categoría no esté vacío
    if (!$nombre) {
        echo json_encode(['success' => false, 'message' => 'El nombre de la categoría es obligatorio.']);
        exit();
    }

    try {
        $iconoPath = null;
        $geojsonPath = null;
        $qmdPath = null;

        // Manejar el archivo de ícono, si existe
        if (isset($_FILES['icono']['name']) && $_FILES['icono']['error'] === 0) {
            $iconoPath = $uploadDir . 'iconos/' . basename($_FILES['icono']['name']);
            if (!move_uploaded_file($_FILES['icono']['tmp_name'], $iconoPath)) {
                throw new Exception('Error al subir el ícono.');
            }
        }

        // Manejar el archivo GeoJSON, si existe
        if (isset($_FILES['geojson']['name']) && $_FILES['geojson']['error'] === 0) {
            $geojsonPath = $uploadDir . 'layers/' . basename($_FILES['geojson']['name']);
            if (!move_uploaded_file($_FILES['geojson']['tmp_name'], $geojsonPath)) {
                throw new Exception('Error al subir el archivo GeoJSON.');
            }
        }

        // Manejar el archivo QMD, si existe
        if (isset($_FILES['qmd']['name']) && $_FILES['qmd']['error'] === 0) {
            $qmdPath = $uploadDir . 'layers/' . basename($_FILES['qmd']['name']);
            if (!move_uploaded_file($_FILES['qmd']['tmp_name'], $qmdPath)) {
                throw new Exception('Error al subir el archivo QMD.');
            }
        }

        // Inserción en la base de datos
        $stmt = $conn->prepare("INSERT INTO categorias (nombre, icono, geojson_path, qmd_path) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $iconoPath, $geojsonPath, $qmdPath]);

        echo json_encode(['success' => true, 'message' => 'Categoría y archivos subidos con éxito.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

