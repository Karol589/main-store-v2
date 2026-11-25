<?php
require_once __DIR__ . '/../../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('modules/profiles/index.php');
}

$id          = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$code        = strtoupper(trim($_POST['code'] ?? ''));
$name        = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($code === '' || $name === '') {
    flash('danger', 'Código y nombre son obligatorios.');
    redirect('modules/profiles/form.php' . ($id ? "?id=$id" : ''));
}

try {
    if ($id) {
        db_exec(
            "UPDATE profiles SET code = $1, name = $2, description = $3
              WHERE id = $4 AND deleted_at IS NULL",
            [$code, $name, $description, $id]
        );
        flash('success', 'Perfil actualizado.');
    } else {
        db_exec(
            "INSERT INTO profiles (code, name, description) VALUES ($1, $2, $3)",
            [$code, $name, $description]
        );
        flash('success', 'Perfil creado. Ahora asígnele menús.');
    }
} catch (RuntimeException $ex) {
    flash('danger', 'No se pudo guardar: el código ya existe.');
}
redirect('modules/profiles/index.php');
