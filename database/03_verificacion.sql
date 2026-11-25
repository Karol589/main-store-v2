-- Consultas de verificación (Paso 5.3 del taller)

-- Usuarios y su perfil
SELECT u.email, p.code FROM users u JOIN profiles p ON p.id = u.profile_id;

-- Menús por perfil
SELECT p.code, m.code, m.url
FROM profile_menu pm
JOIN profiles p ON p.id = pm.profile_id
JOIN menus m    ON m.id = pm.menu_id
WHERE pm.deleted_at IS NULL
ORDER BY p.code, m.sort_order;

-- Jerarquía de ubicación
SELECT co.name AS pais, d.name AS departamento, ci.name AS ciudad
FROM cities ci
JOIN departments d ON d.id = ci.department_id
JOIN countries co  ON co.id = d.country_id
ORDER BY 1, 2, 3;

-- Probar el trigger: updated_at debe cambiar
UPDATE products SET stock = stock + 1 WHERE sku = 'TEC-001';
SELECT sku, created_at, updated_at FROM products WHERE sku = 'TEC-001';
