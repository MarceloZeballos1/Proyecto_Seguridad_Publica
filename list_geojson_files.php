<?php
$directory = "uploads/layers/";
$files = array_diff(scandir($directory), array('..', '.'));

if (!empty($files)) {
    header('Content-Type: application/json');
    echo json_encode(array_values($files)); // Asegúrate de que sea un array
} else {
    header('Content-Type: application/json');
    echo json_encode([]); // Devuelve un array vacío si no hay archivos
}
?>
