<?php
require_once __DIR__ . '/../../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('modules/users/index.php');
}

$id        = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$name      = trim($_POST['name'] ?? '');
$email     = trim($_POST['email'] ?? '');
$profileId = filter_input(INPUT_POST, 'profile_id', FILTER_VALIDATE_INT);
$password  = $_POST['password'] ?? '';

$back = 'modules/users/form.php' . ($id ? "?id=$id" : '');

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$profileId || (!$id && strlen($password) < 6)) {
    flash('danger', 'Complete nombre, correo válido, perfil y contraseña (mín. 6).');
    redirect($back);
}

try {
    if ($id) {
        db_exec(
            "UPDATE users SET name = $1, email = $2, profile_id = $3 WHERE id = $4 AND deleted_at IS NULL",
            [$name, $email, $profileId, $id]
        );
        if ($password !== '') {
            db_exec("UPDATE users SET password = $1 WHERE id = $2",
                    [password_hash($password, PASSWORD_DEFAULT), $id]);
        }
        flash('success', 'Usuario actualizado.');
    } else {
        db_exec(
            "INSERT INTO users (name, email, password, profile_id) VALUES ($1, $2, $3, $4)",
            [$name, $email, password_hash($password, PASSWORD_DEFAULT), $profileId]
        );
        flash('success', 'Usuario creado.');
    }
} catch (RuntimeException $ex) {
    flash('danger', 'El correo ya está registrado.');
    redirect($back);
}
redirect('modules/users/index.php');
