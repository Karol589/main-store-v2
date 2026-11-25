<?php
require_once __DIR__ . '/includes/auth.php';
require_access('dashboard');

$page_title  = 'Dashboard';
$active_menu = 'dashboard';

// Un solo query con subconsultas para los contadores
$stats = db_one(
    "SELECT
        (SELECT COUNT(*) FROM users    WHERE deleted_at IS NULL) AS users,
        (SELECT COUNT(*) FROM branches WHERE deleted_at IS NULL) AS branches,
        (SELECT COUNT(*) FROM stores   WHERE deleted_at IS NULL) AS stores,
        (SELECT COUNT(*) FROM products WHERE deleted_at IS NULL) AS products"
);

$cards = [
    ['Usuarios',   $stats['users'],    'primary', 'fa-users'],
    ['Sucursales', $stats['branches'], 'success', 'fa-map-marker-alt'],
    ['Tiendas',    $stats['stores'],   'info',    'fa-store'],
    ['Productos',  $stats['products'], 'warning', 'fa-box-open'],
];

require __DIR__ . '/includes/header.php';
?>
<div class="row">
    <?php foreach ($cards as [$label, $value, $color, $icon]): ?>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-<?= $color ?> shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-<?= $color ?> text-uppercase mb-1"><?= $label ?></div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= e($value) ?></div>
                        </div>
                        <div class="col-auto"><i class="fas <?= $icon ?> fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
