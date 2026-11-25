<?php
/**
 * Variables que define cada página ANTES de incluir este archivo:
 *   $page_title  => título de la pestaña y del encabezado
 *   $active_menu => code del menú a resaltar en el sidebar
 */
$page_title  = $page_title  ?? 'Main Store';
$active_menu = $active_menu ?? '';
$user        = current_user();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= e($page_title) ?> | Main Store 2.0</title>
    <link href="<?= BASE_URL ?>assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,600,700,800" rel="stylesheet">
    <link href="<?= BASE_URL ?>assets/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>assets/css/app.css" rel="stylesheet">
</head>
<body id="page-top">
<div id="wrapper">

    <?php require __DIR__ . '/sidebar.php'; ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item d-flex align-items-center mr-3">
                        <span class="badge badge-<?= is_admin() ? 'danger' : 'info' ?>">
                            <?= e($user['profile_name']) ?>
                        </span>
                    </li>
                    <li class="nav-item d-flex align-items-center mr-3 text-gray-600 small">
                        <i class="fas fa-user-circle fa-lg mr-2"></i><?= e($user['name']) ?>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>logout.php" title="Cerrar sesión">
                            <i class="fas fa-sign-out-alt fa-fw text-gray-500"></i>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="container-fluid">
                <h1 class="h3 mb-4 text-gray-800"><?= e($page_title) ?></h1>
                <?php show_flash(); ?>
