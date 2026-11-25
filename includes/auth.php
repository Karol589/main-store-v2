<?php
/**
 * MAIN-STORE v2.0 - Sesión, autenticación y permisos por perfil
 * Se incluye al inicio de TODAS las páginas.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/helpers.php';

/** Usuario en sesión (o null). */
function current_user()
{
    return $_SESSION['user'] ?? null;
}

/** Obliga a iniciar sesión. */
function require_login()
{
    if (!current_user()) {
        redirect('login.php');
    }
}

/** ¿El usuario es administrador? */
function is_admin()
{
    return (current_user()['profile_code'] ?? '') === 'ADMIN';
}

/** Muestra la página 403 y detiene. */
function deny()
{
    http_response_code(403);
    require __DIR__ . '/403.php';
    exit;
}

/** Solo ADMIN puede continuar (crear, editar, eliminar). */
function require_admin()
{
    require_login();
    if (!is_admin()) {
        deny();
    }
}

/** Menús asignados al perfil del usuario (alimenta el sidebar). */
function user_menus()
{
    $user = current_user();
    if (!$user) {
        return [];
    }
    return db_all(
        "SELECT m.code, m.name, m.icon, m.url
           FROM menus m
           JOIN profile_menu pm ON pm.menu_id = m.id
          WHERE pm.profile_id = $1
            AND m.deleted_at IS NULL
            AND pm.deleted_at IS NULL
          ORDER BY m.sort_order",
        [$user['profile_id']]
    );
}

/** ¿El perfil del usuario tiene asignado el menú $code? */
function can_access($code)
{
    $user = current_user();
    if (!$user) {
        return false;
    }
    $row = db_one(
        "SELECT 1
           FROM menus m
           JOIN profile_menu pm ON pm.menu_id = m.id
          WHERE pm.profile_id = $1 AND m.code = $2
            AND m.deleted_at IS NULL AND pm.deleted_at IS NULL",
        [$user['profile_id'], $code]
    );
    return $row !== null;
}

/** Bloquea la página si el perfil no tiene el menú asignado. */
function require_access($code)
{
    require_login();
    if (!can_access($code)) {
        deny();
    }
}

/** Página de inicio según el perfil. */
function home_path()
{
    return is_admin() ? 'dashboard.php' : 'modules/shop/index.php';
}
