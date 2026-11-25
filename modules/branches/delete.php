<?php
require_once __DIR__ . '/../../includes/auth.php';
require_admin();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if ($id) {
    $hasStores = db_one("SELECT 1 FROM stores WHERE branch_id = $1 AND deleted_at IS NULL LIMIT 1", [$id]);
    if ($hasStores) {
        flash('warning', 'No se puede eliminar: la sucursal tiene tiendas activas.');
    } else {
        db_exec("UPDATE branches SET deleted_at = NOW() WHERE id = $1", [$id]);
        flash('success', 'Sucursal eliminada.');
    }
}
redirect('modules/branches/index.php');
