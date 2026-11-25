<?php
require_once __DIR__ . '/../../includes/auth.php';
require_admin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$product = ['id' => '', 'store_id' => '', 'sku' => '', 'name' => '', 'description' => '',
            'price' => '', 'stock' => 0, 'image_url' => '', 'is_active' => 't'];

if ($id) {
    $product = db_one(
        "SELECT id, store_id, sku, name, description, price, stock, image_url, is_active
           FROM products WHERE id = $1 AND deleted_at IS NULL",
        [$id]
    );
    if (!$product) {
        flash('danger', 'El producto no existe.');
        redirect('modules/products/index.php');
    }
}

$stores = db_all(
    "SELECT s.id, s.name, b.name AS branch
       FROM stores s JOIN branches b ON b.id = s.branch_id
      WHERE s.deleted_at IS NULL AND s.is_active = TRUE
      ORDER BY s.name"
);

$page_title  = $id ? 'Editar producto' : 'Nuevo producto';
$active_menu = 'products';
$noImage     = BASE_URL . 'assets/img/no-image.svg';

// Vista previa en vivo de la imagen mientras se escribe la URL
$extra_js = <<<JS
<script>
  const urlInput = document.getElementById('image_url');
  const preview  = document.getElementById('preview');
  preview.onerror = () => { preview.src = '{$noImage}'; };
  urlInput.addEventListener('input', () => {
      preview.src = urlInput.value.trim() || '{$noImage}';
  });
</script>
JS;

require __DIR__ . '/../../includes/header.php';
?>
<form action="save.php" method="post">
    <input type="hidden" name="id" value="<?= e($product['id']) ?>">
    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="form-group">
                        <label>Tienda *</label>
                        <select name="store_id" class="form-control" required>
                            <option value="">Seleccione la tienda</option>
                            <?php foreach ($stores as $s): ?>
                                <option value="<?= $s['id'] ?>" <?= $s['id'] == $product['store_id'] ? 'selected' : '' ?>>
                                    <?= e($s['name']) ?> — <?= e($s['branch']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>SKU *</label>
                            <input name="sku" class="form-control text-uppercase" maxlength="40" required
                                   value="<?= e($product['sku']) ?>">
                        </div>
                        <div class="form-group col-md-8">
                            <label>Nombre *</label>
                            <input name="name" class="form-control" maxlength="150" required
                                   value="<?= e($product['name']) ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="description" class="form-control" rows="3"><?= e($product['description']) ?></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Precio (COP) *</label>
                            <input type="number" name="price" class="form-control" min="0" step="0.01" required
                                   value="<?= e($product['price']) ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Stock *</label>
                            <input type="number" name="stock" class="form-control" min="0" step="1" required
                                   value="<?= e($product['stock']) ?>">
                        </div>
                    </div>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                               <?= pg_bool($product['is_active']) ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="is_active">Visible en el catálogo</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Foto del producto</h6></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>URL de la imagen</label>
                        <input type="url" id="image_url" name="image_url" class="form-control" maxlength="500"
                               placeholder="https://..." value="<?= e($product['image_url']) ?>">
                        <small class="form-text text-muted">Pegue el enlace directo a una imagen (.jpg, .png, .webp).</small>
                    </div>
                    <img id="preview" class="img-preview" alt="Vista previa"
                         src="<?= e($product['image_url'] ?: $noImage) ?>">
                </div>
            </div>
        </div>
    </div>
    <button class="btn btn-success"><i class="fas fa-save"></i> Guardar</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
</form>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
