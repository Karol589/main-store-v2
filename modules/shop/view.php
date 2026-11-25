<?php
require_once __DIR__ . '/../../includes/auth.php';
require_access('shop');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$p = $id ? db_one(
    "SELECT p.*, s.name AS store, b.name AS branch, b.address,
            ci.name AS city, d.name AS department, co.name AS country
       FROM products p
       JOIN stores s       ON s.id  = p.store_id
       JOIN branches b     ON b.id  = s.branch_id
       JOIN cities ci      ON ci.id = b.city_id
       JOIN departments d  ON d.id  = ci.department_id
       JOIN countries co   ON co.id = d.country_id
      WHERE p.id = $1 AND p.deleted_at IS NULL AND p.is_active = TRUE",
    [$id]
) : null;

if (!$p) {
    flash('warning', 'Producto no disponible.');
    redirect('modules/shop/index.php');
}

$page_title  = $p['name'];
$active_menu = 'shop';
$noImage     = BASE_URL . 'assets/img/no-image.svg';
require __DIR__ . '/../../includes/header.php';
?>
<a href="index.php" class="btn btn-light btn-sm mb-3"><i class="fas fa-arrow-left"></i> Volver al catálogo</a>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="row">
            <!-- 1. Imagen grande -->
            <div class="col-lg-6 mb-3">
                <img src="<?= e($p['image_url'] ?: $noImage) ?>" class="img-fluid rounded w-100"
                     style="max-height:420px; object-fit:cover; background:#eaecf4"
                     alt="<?= e($p['name']) ?>" onerror="this.src='<?= $noImage ?>'">
            </div>

            <div class="col-lg-6">
                <!-- 2. SKU, nombre, precio y descripción -->
                <span class="badge badge-light text-muted">SKU: <?= e($p['sku']) ?></span>
                <h2 class="h3 font-weight-bold text-gray-900 mt-2"><?= e($p['name']) ?></h2>
                <p class="h3 font-weight-bold text-success"><?= money($p['price']) ?></p>
                <p class="text-gray-700"><?= nl2br(e($p['description'])) ?></p>
                <hr>

                <!-- 3. Trazabilidad tienda > sucursal > ciudad > depto > país -->
                <dl class="row mb-0">
                    <dt class="col-sm-4">Disponibles</dt>
                    <dd class="col-sm-8">
                        <?php if ($p['stock'] > 0): ?>
                            <span class="badge badge-success"><?= e($p['stock']) ?> und.</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Agotado</span>
                        <?php endif; ?>
                    </dd>
                    <dt class="col-sm-4">Tienda</dt>
                    <dd class="col-sm-8"><?= e($p['store']) ?></dd>
                    <dt class="col-sm-4">Sucursal</dt>
                    <dd class="col-sm-8"><?= e($p['branch']) ?> — <?= e($p['address']) ?></dd>
                    <dt class="col-sm-4">Ubicación</dt>
                    <dd class="col-sm-8"><?= e($p['city']) ?>, <?= e($p['department']) ?>, <?= e($p['country']) ?></dd>
                </dl>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
