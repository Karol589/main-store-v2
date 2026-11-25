<?php
require_once __DIR__ . '/includes/auth.php';

// Si ya inició sesión, lo enviamos a su página de inicio
if (current_user()) {
    redirect(home_path());
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = db_one(
        "SELECT u.id, u.name, u.email, u.password, u.profile_id,
                p.code AS profile_code, p.name AS profile_name
           FROM users u
           JOIN profiles p ON p.id = u.profile_id
          WHERE u.email = $1
            AND u.deleted_at IS NULL
            AND p.deleted_at IS NULL",
        [$email]
    );

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);       // evita fijación de sesión
        unset($user['password']);          // nunca guardar el hash en sesión
        $_SESSION['user'] = $user;
        redirect(home_path());
    }
    $error = 'Correo o contraseña incorrectos.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Ingresar | Main Store 2.0</title>
    <link href="<?= BASE_URL ?>assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>assets/css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-primary">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-5 col-lg-6 col-md-8">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-store fa-3x text-primary mb-2"></i>
                        <h1 class="h4 text-gray-900">Main Store 2.0</h1>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= e($error) ?></div>
                    <?php endif; ?>

                    <form method="post" class="user">
                        <div class="form-group">
                            <input type="email" name="email" class="form-control form-control-user"
                                   placeholder="Correo electrónico" value="<?= e($email) ?>" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" class="form-control form-control-user"
                                   placeholder="Contraseña" required>
                        </div>
                        <button class="btn btn-primary btn-user btn-block">Ingresar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
