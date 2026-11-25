<?php
/**
 * MAIN-STORE v2.0 - Plantilla de conexión (SIN contraseña real).
 * Copie este archivo como config/database.php y escriba su contraseña.
 */
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'mainstore_v2');
define('DB_USER', 'postgres');
define('DB_PASS', 'CAMBIAR');

// Carpeta del proyecto dentro de htdocs / www (con / al inicio y al final)
define('BASE_URL', '/main-store-v2/');

function db()
{
    static $conn = null;

    if ($conn === null) {
        $connString = sprintf(
            'host=%s port=%s dbname=%s user=%s password=%s',
            DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS
        );
        $conn = @pg_connect($connString);

        if (!$conn) {
            die('Error: no fue posible conectarse a PostgreSQL. Revise config/database.php');
        }
        pg_set_client_encoding($conn, 'UTF8');
    }
    return $conn;
}
