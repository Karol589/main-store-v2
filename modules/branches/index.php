<?php
require_once __DIR__ . '/../../includes/auth.php';
require_access('branches');

$page_title  = 'Sucursales';
$active_menu = 'branches';

$branches = db_all(
    "SELECT b.id, b.name, b.address, b.phone,
            ci.name AS city, d.name AS department, co.name AS country,
            (SELECT COUNT(*) FROM stores s
              WHERE s.branch_id = b.id AND s.deleted_at IS NULL) AS total_stores
       FROM branches b
       JOIN cities      ci ON ci.id = b.city_id
       JOIN departments d  ON d.id  = ci.department_id
       JOIN countries   co ON co.id = d.country_id
      WHERE b.deleted_at IS NULL
      ORDER BY co.name, d.name, ci.name, b.name"
);

require __DIR__ . '/../../includes/header.php';
?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Listado de sucursales</h6>
        <a href="form.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nueva sucursal</a>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
            <tr><th>Sucursal</th><th>Dirección</th><th>Teléfono</th><th>Ubicación</th>
                <th class="text-center">Tiendas</th><th class="text-center" style="width:130px">Acciones</th></tr>
            </thead>
            <tbody>
            <?php if (!$branches): ?>
                <tr><td colspan="6" class="text-center text-muted">No hay sucursales registradas.</td></tr>
            <?php endif; ?>
            <?php foreach ($branches as $b): ?>
                <tr>
                    <td class="font-weight-bold"><?= e($b['name']) ?></td>
                    <td><?= e($b['address']) ?></td>
                    <td><?= e($b['phone']) ?></td>
                    <td><?= e($b['city']) ?>, <?= e($b['department']) ?>
                        <span class="badge badge-light"><?= e($b['country']) ?></span></td>
                    <td class="text-center"><?= e($b['total_stores']) ?></td>
                    <td class="text-center">
                        <a href="form.php?id=<?= $b['id'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                        <form action="delete.php" method="post" class="d-inline"
                              onsubmit="return confirm('¿Eliminar la sucursal?')">
                            <input type="hidden" name="id" value="<?= $b['id'] ?>">
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
