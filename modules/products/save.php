<?php
require_once __DIR__ . '/../../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('modules/products/index.php');
}

$id          = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$storeId     = filter_input(INPUT_POST, 'store_id', FILTER_VALIDATE_INT);
$sku         = strtoupper(trim($_POST['sku'] ?? ''));
$name        = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$price       = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
$stock       = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);
$imageUrl    = trim($_POST['image_url'] ?? '');
$isActive    = isset($_POST['is_active']) ? 't' : 'f';

$back = 'modules/products/form.php' . ($id ? "?id=$id" : '');
$errors = [];

$store = $storeId ? db_one("SELECT id FROM stores WHERE id = $1 AND deleted_at IS NULL", [$storeId]) : null;
if (!$store)                          $errors[] = 'tienda';
if ($sku === '' || $name === '')      $errors[] = 'SKU y nombre';
if ($price === false || $price === null || $price < 0)   $errors[] = 'precio válido';
if ($stock === false || $stock === null || $stock < 0)   $errors[] = 'stock válido';

// La URL es opcional, pero si viene debe ser http(s)
if ($imageUrl !== '') {
    $scheme = parse_url($imageUrl, PHP_URL_SCHEME);
    if (!filter_var($imageUrl, FILTER_VALIDATE_URL) || !in_array($scheme, ['http', 'https'], true)) {
        $errors[] = 'URL de imagen (http/https)';
    }
}

if ($errors) {
    flash('danger', 'Revise: ' . implode(', ', $errors) . '.');
    redirect($back);
}

// '' => NULL en la base de datos
$imageUrl = $imageUrl === '' ? null : $imageUrl;
$params = [$storeId, $sku, $name, $description, $price, $stock, $imageUrl, $isActive];

try {
    if ($id) {
        $params[] = $id;
        db_exec(
            "UPDATE products SET store_id = $1, sku = $2, name = $3, description = $4,
                    price = $5, stock = $6, image_url = $7, is_active = $8
              WHERE id = $9 AND deleted_at IS NULL",
            $params
        );
        flash('success', 'Producto actualizado.');
    } else {
        db_exec(
            "INSERT INTO products (store_id, sku, name, description, price, stock, image_url, is_active)
             VALUES ($1, $2, $3, $4, $5, $6, $7, $8)",
            $params
        );
        flash('success', 'Producto creado.');
    }
} catch (RuntimeException $ex) {
    flash('danger', 'No se pudo guardar: el SKU ya existe.');
    redirect($back);
}
redirect('modules/products/index.php');
