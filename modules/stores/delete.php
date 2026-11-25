<?php
require_once __DIR__ . '/../../includes/auth.php';
require_admin();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if ($id) {
    $hasProducts = db_one("SELECT 1 FROM products WHERE store_id = $1 AND deleted_at IS NULL LIMIT 1", [$id]);
    if ($hasProducts) {
        flash('warning', 'No se puede eliminar: la tienda tiene productos.');
    } else {
        db_exec("UPDATE stores SET deleted_at = NOW() WHERE id = $1", [$id]);
        flash('success', 'Tienda eliminada.');
    }
}
redirect('modules/stores/index.php');
