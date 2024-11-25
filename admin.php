<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Evita que el navegador almacene la página en caché
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['add_user'])) {
    $new_username = $_POST['username'];
    $new_password = md5($_POST['password']);
    $new_role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
    $stmt->execute(['username' => $new_username, 'password' => $new_password, 'role' => $new_role]);
}

if (isset($_POST['update_role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET role = :role WHERE id = :id");
    $stmt->execute(['role' => $new_role, 'id' => $user_id]);
}

$users = $conn->query("SELECT * FROM users")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta http-equiv=”Content-Type” content=”text/html; charset=UTF-8″ />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'sidebar.php'; ?>

    <div class="container py-4" style="margin-left: 70px;">
        <h1 class="mb-4">Panel de Administración</h1>
        <p>Bienvenido, <strong><?= $_SESSION['username']; ?></strong>!</p>

        <div class="card mb-4">
            <div class="card-header">Añadir Usuario</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Rol</label>
                        <select class="form-select" id="role" name="role">
                            <option value="user">Usuario</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" name="add_user" class="btn btn-primary">Añadir Usuario</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Datos Usuarios</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id']; ?></td>
                                <td><?= $user['username']; ?></td>
                                <td><?= $user['role']; ?></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                                        <select name="role" class="form-select d-inline w-auto">
                                            <option value="user" <?= $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                        </select>
                                        <button type="submit" name="update_role" class="btn btn-sm btn-primary">Actualizar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
