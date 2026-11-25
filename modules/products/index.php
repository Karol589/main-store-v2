<?php
require_once __DIR__ . '/../../includes/auth.php';
require_access('products');

$page_title  = 'Productos';
$active_menu = 'products';

$products = db_all(
    "SELECT p.id, p.sku, p.name, p.price, p.stock, p.image_url, p.is_active,
            s.name AS store
       FROM products p
       JOIN stores s ON s.id = p.store_id
      WHERE p.deleted_at IS NULL
      ORDER BY p.created_at DESC"
);

require __DIR__ . '/../../includes/header.php';
?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Listado de productos</h6>
        <a href="form.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nuevo producto</a>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="thead-light">
            <tr><th style="width:72px">Foto</th><th>SKU</th><th>Producto</th><th>Tienda</th>
                <th class="text-right">Precio</th><th class="text-center">Stock</th>
                <th class="text-center">Estado</th><th class="text-center" style="width:130px">Acciones</th></tr>
            </thead>
            <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><img src="<?= e($p['image_url'] ?: BASE_URL . 'assets/img/no-image.svg') ?>" class="thumb" alt=""
                             onerror="this.src='<?= BASE_URL ?>assets/img/no-image.svg'"></td>
                    <td><code><?= e($p['sku']) ?></code></td>
                    <td class="font-weight-bold"><?= e($p['name']) ?></td>
                    <td><?= e($p['store']) ?></td>
                    <td class="text-right"><?= money($p['price']) ?></td>
                    <td class="text-center">
                        <span class="badge badge-<?= $p['stock'] > 0 ? 'light' : 'danger' ?>"><?= e($p['stock']) ?></span>
                    </td>
                    <td class="text-center">
                        <?= pg_bool($p['is_active'])
                            ? '<span class="badge badge-success">Visible</span>'
                            : '<span class="badge badge-secondary">Oculto</span>' ?>
                    </td>
                    <td class="text-center">
                        <a href="form.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        <form action="delete.php" method="post" class="d-inline"
                              onsubmit="return confirm('¿Eliminar el producto?')">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
