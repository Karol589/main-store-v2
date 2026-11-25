<?php
require_once __DIR__ . '/../../includes/auth.php';
require_admin();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    redirect('modules/profiles/index.php');
}

$profile = db_one("SELECT code FROM profiles WHERE id = $1 AND deleted_at IS NULL", [$id]);
$inUse   = db_one("SELECT 1 FROM users WHERE profile_id = $1 AND deleted_at IS NULL LIMIT 1", [$id]);

if (!$profile || $profile['code'] === 'ADMIN') {
    flash('danger', 'El perfil ADMIN no se puede eliminar.');
} elseif ($inUse) {
    flash('warning', 'No se puede eliminar: hay usuarios con este perfil.');
} else {
    // Borrado lógico: solo se marca deleted_at
    db_exec("UPDATE profiles SET deleted_at = NOW() WHERE id = $1", [$id]);
    flash('success', 'Perfil eliminado.');
}
redirect('modules/profiles/index.php');
