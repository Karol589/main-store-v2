<?php
/**
 * GET api/cities.php?department_id=1  =>  [{"id":"1","name":"Medellín"}, ...]
 */
require_once __DIR__ . '/../includes/auth.php';
require_login();
header('Content-Type: application/json; charset=utf-8');

$departmentId = filter_input(INPUT_GET, 'department_id', FILTER_VALIDATE_INT);
if (!$departmentId) {
    echo '[]';
    exit;
}

echo json_encode(db_all(
    "SELECT id, name FROM cities
      WHERE department_id = $1 AND deleted_at IS NULL
      ORDER BY name",
    [$departmentId]
), JSON_UNESCAPED_UNICODE);
