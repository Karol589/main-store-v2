<?php
/**
 * GET api/departments.php?country_id=1  =>  [{"id":"1","name":"Antioquia"}, ...]
 */
require_once __DIR__ . '/../includes/auth.php';
require_login();
header('Content-Type: application/json; charset=utf-8');

$countryId = filter_input(INPUT_GET, 'country_id', FILTER_VALIDATE_INT);
if (!$countryId) {
    echo '[]';
    exit;
}

echo json_encode(db_all(
    "SELECT id, name FROM departments
      WHERE country_id = $1 AND deleted_at IS NULL
      ORDER BY name",
    [$countryId]
), JSON_UNESCAPED_UNICODE);
