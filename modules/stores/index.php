<?php
require_once __DIR__ . '/../../includes/auth.php';
require_access('stores');

$page_title  = 'Tiendas';
$active_menu = 'stores';

$stores = db_all(
    "SELECT s.id, s.name, s.email, s.phone, s.is_active,
            b.name AS branch, ci.name AS city,
            (SELECT COUNT(*) FROM products p
              WHERE p.store_id = s.id AND p.deleted_at IS NULL) AS total_products
       FROM stores s
       JOIN branches b ON b.id  = s.branch_id
       JOIN cities  ci ON ci.id = b.city_id
      WHERE s.deleted_at IS NULL
      ORDER BY b.name, s.name"
);

require __DIR__ . '/../../includes/header.php';
?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Listado de tiendas</h6>
        <a href="form.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nueva tienda</a>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
            <tr><th>Tienda</th><th>Sucursal</th><th>Contacto</th>
                <th class="text-center">Productos</th><th class="text-center">Estado</th>
                <th class="text-center" style="width:130px">Acciones</th></tr>
            </thead>
            <tbody>
            <?php foreach ($stores as $s): ?>
                <tr>
                    <td class="font-weight-bold"><?= e($s['name']) ?></td>
                    <td><?= e($s['branch']) ?> <small class="text-muted">(<?= e($s['city']) ?>)</small></td>
                    <td><?= e($s['email']) ?><br><small><?= e($s['phone']) ?></small></td>
                    <td class="text-center"><?= e($s['total_products']) ?></td>
                    <td class="text-center">
                        <?= pg_bool($s['is_active'])
                            ? '<span class="badge badge-success">Activa</span>'
                            : '<span class="badge badge-secondary">Inactiva</span>' ?>
                    </td>
                    <td class="text-center">
                        <a href="form.php?id=<?= $s['id'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        <form action="delete.php" method="post" class="d-inline"
                              onsubmit="return confirm('¿Eliminar la tienda?')">
                            <input type="hidden" name="id" value="<?= $s['id'] ?>">
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
