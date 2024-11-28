<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargar Capas</title>
</head>
<body>
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
</body>
</html>
