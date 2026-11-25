<?php
/**
 * MAIN-STORE v2.0 - Funciones de apoyo
 * Todas las consultas usan pg_query_params ($1, $2...) => evita inyección SQL.
 */

/** Escapa texto para imprimirlo en HTML (evita XSS). */
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Redirige a una ruta relativa al proyecto y detiene el script. */
function redirect($path)
{
    header('Location: ' . BASE_URL . $path);
    exit;
}

/** Ejecuta una consulta parametrizada y devuelve el recurso resultado. */
function db_exec($sql, array $params = [])
{
    $result = @pg_query_params(db(), $sql, $params);
    if ($result === false) {
        throw new RuntimeException(pg_last_error(db()));
    }
    return $result;
}

/** Devuelve todas las filas como arreglo asociativo. */
function db_all($sql, array $params = [])
{
    $rows = pg_fetch_all(db_exec($sql, $params));
    return $rows ?: [];
}

/** Devuelve una sola fila o null. */
function db_one($sql, array $params = [])
{
    $row = pg_fetch_assoc(db_exec($sql, $params));
    return $row ?: null;
}

/** Mensajes flash (se muestran una sola vez después de redirigir). */
function flash($type, $message)
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function show_flash()
{
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="alert alert-' . e($f['type']) . ' alert-dismissible fade show">'
           . e($f['message'])
           . '<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
    }
}

/** Convierte el booleano de PostgreSQL ('t' / 'f') a bool de PHP. */
function pg_bool($value)
{
    return $value === 't' || $value === true;
}

/** Formato de moneda colombiana: 189900 => $ 189.900 */
function money($value)
{
    return '$ ' . number_format((float) $value, 0, ',', '.');
}

/** Recorta un texto largo agregando '...' (usa mbstring si está disponible). */
function short_text($text, $length = 70)
{
    $text = (string) $text;
    if (function_exists('mb_strimwidth')) {
        return mb_strimwidth($text, 0, $length, '...');
    }
    return strlen($text) > $length ? substr($text, 0, $length - 3) . '...' : $text;
}
