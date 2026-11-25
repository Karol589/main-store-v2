<?php
/**
 * Catálogo de productos (solo lectura). Lo ven ADMIN y USER.
 */
require_once __DIR__ . '/../../includes/auth.php';
require_access('shop');

$page_title  = 'Catálogo de productos';
$active_menu = 'shop';

$q       = trim($_GET['q'] ?? '');
$storeId = filter_input(INPUT_GET, 'store', FILTER_VALIDATE_INT) ?: null;
$order   = $_GET['order'] ?? 'new';

// Lista blanca para ORDER BY (no se puede parametrizar con $n)
$orders = [
    'new'        => 'p.created_at DESC',
    'price_asc'  => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'name'       => 'p.name ASC',
];
$orderSql = $orders[$order] ?? $orders['new'];

// Filtros opcionales: si el parámetro es NULL la condición se ignora
$products = db_all(
    "SELECT p.id, p.name, p.description, p.price, p.stock, p.image_url,
            s.name AS store, ci.name AS city
       FROM products p
       JOIN stores   s  ON s.id  = p.store_id
       JOIN branches b  ON b.id  = s.branch_id
       JOIN cities   ci ON ci.id = b.city_id
      WHERE p.deleted_at IS NULL AND p.is_active = TRUE
        AND s.deleted_at IS NULL AND s.is_active = TRUE
        AND ($1::text IS NULL OR p.name ILIKE '%' || $1 || '%' OR p.description ILIKE '%' || $1 || '%')
        AND ($2::int  IS NULL OR p.store_id = $2)
      ORDER BY $orderSql",
    [$q === '' ? null : $q, $storeId]
);

$stores = db_all("SELECT id, name FROM stores WHERE deleted_at IS NULL AND is_active = TRUE ORDER BY name");
$noImage = BASE_URL . 'assets/img/no-image.svg';

require __DIR__ . '/../../includes/header.php';
?>
<!-- Barra de filtros -->
<form class="card shadow-sm mb-4" method="get">
    <div class="card-body py-3">
        <div class="form-row align-items-end">
            <div class="col-md-5 mb-2">
                <label class="small text-muted mb-1">Buscar</label>
                <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-search"></i></span></div>
                    <input name="q" class="form-control" placeholder="Nombre o descripción" value="<?= e($q) ?>">
                </div>
            </div>
            <div class="col-md-3 mb-2">
                <label class="small text-muted mb-1">Tienda</label>
                <select name="store" class="form-control">
                    <option value="">Todas</option>
                    <?php foreach ($stores as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= $s['id'] == $storeId ? 'selected' : '' ?>><?= e($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <label class="small text-muted mb-1">Ordenar</label>
                <select name="order" class="form-control">
                    <option value="new"        <?= $order === 'new' ? 'selected' : '' ?>>Recientes</option>
                    <option value="price_asc"  <?= $order === 'price_asc' ? 'selected' : '' ?>>Menor precio</option>
                    <option value="price_desc" <?= $order === 'price_desc' ? 'selected' : '' ?>>Mayor precio</option>
                    <option value="name"       <?= $order === 'name' ? 'selected' : '' ?>>Nombre A-Z</option>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <button class="btn btn-primary btn-block"><i class="fas fa-filter"></i> Filtrar</button>
            </div>
        </div>
    </div>
</form>

<p class="text-muted small mb-3"><?= count($products) ?> producto(s) encontrado(s)</p>

<!-- Grilla de productos -->
<div class="row">
    <?php if (!$products): ?>
        <div class="col-12"><div class="alert alert-light border text-center">No hay productos con esos filtros.</div></div>
    <?php endif; ?>

    <?php foreach ($products as $p): ?>
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-4">
            <div class="card product-card shadow-sm h-100 border-0">
                <img src="<?= e($p['image_url'] ?: $noImage) ?>" class="card-img-top"
                     alt="<?= e($p['name']) ?>" onerror="this.src='<?= $noImage ?>'">
                <div class="card-body d-flex flex-column">
                    <span class="badge badge-light text-primary align-self-start mb-2">
                        <i class="fas fa-store"></i> <?= e($p['store']) ?>
                    </span>
                    <h6 class="font-weight-bold text-gray-900 mb-1"><?= e($p['name']) ?></h6>
                    <p class="small text-muted mb-3"><?= e(short_text($p['description'], 70)) ?></p>
                    <div class="mt-auto d-flex justify-content-between align-items-center">
                        <span class="price"><?= money($p['price']) ?></span>
                        <?php if ($p['stock'] > 0): ?>
                            <span class="badge badge-success"><?= e($p['stock']) ?> disp.</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Agotado</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 pt-0">
                    <a href="view.php?id=<?= $p['id'] ?>" class="btn btn-outline-primary btn-sm btn-block">Ver detalle</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
