<?php
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$isUser = isset($_SESSION['role']) && $_SESSION['role'] === 'user';
?>

<div id="sidebar" class="sidebar">
    <div class="sidebar-content">
        <a href="dashboard.php" class="sidebar-item">
            <span class="icon">📊</span>
            <span class="text">Dashboard</span>
        </a>
        <?php if ($isUser): ?>
        <a href="user.php" class="sidebar-item">
            <span class="icon">🗺</span>
            <span class="text">Mapa</span>
        </a>
        <?php endif; ?>
        <?php if ($isAdmin): ?>
        <a href="index.php" class="sidebar-item">
            <span class="icon">🗺</span>
            <span class="text">Mapa</span>
        </a>
        <?php endif; ?>
        <?php if ($isAdmin): ?>
            <a href="admin.php" class="sidebar-item">
                <span class="icon">👥</span>
                <span class="text">Gestión de Usuarios</span>
            </a>
        <?php endif; ?>
        <?php if ($isAdmin): ?>
            <a href="admin_puntos.php" class="sidebar-item">
                <span class="icon">📍</span>
                <span class="text">Puntos</span>
            </a>
        <?php endif; ?>
        <?php if ($isAdmin): ?>
        <a href="admin_categorias.php" class="sidebar-item">
            <span class="icon">🗃</span>
            <span class="text">Categorías</span>
        </a>
        <?php endif; ?>
        <?php if ($isAdmin): ?>
        <a href="cargar.php" class="sidebar-item">
            <span class="icon">💾</span>
            <span class="text">Cargar Puntos</span>
        </a>
        <?php endif; ?>
        <a href="logout.php" class="sidebar-item">
            <span class="icon">🔓</span>
            <span class="text">Cerrar Sesión</span>
        </a>
    </div>
</div>

<style>
    /* Estilo general del sidebar */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        width: 60px;
        background-color: #343a40;
        overflow-x: hidden;
        transition: width 0.3s ease;
        z-index: 1000;
    }

    .sidebar:hover {
        width: 250px;
    }

    .sidebar-content {
        display: flex;
        flex-direction: column;
        align-items: start;
        padding: 10px 0;
        overflow-y: auto;
    }

    /* Estilo de los elementos del sidebar */
    .sidebar-item {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: #fff;
        padding: 10px 20px;
        width: 100%;
        white-space: nowrap;
        transition: background-color 0.3s ease;
    }

    .sidebar-item:hover {
        background-color: #495057;
    }

    /* Iconos y texto */
    .icon {
        margin-right: 10px;
        font-size: 20px;
    }

    .text {
        display: none;
    }

    /* Mostrar texto cuando el sidebar está expandido */
    .sidebar:hover .text {
        display: inline-block;
    }
</style>