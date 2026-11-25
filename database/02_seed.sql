-- =====================================================================
-- MAIN-STORE v2.0  |  Script 02: Datos iniciales (DML)
-- TdeA 2026 - Construcción de Software II
-- =====================================================================

-- 1. Perfiles
INSERT INTO profiles (code, name, description) VALUES
('ADMIN', 'Administrador', 'Acceso total: CRUD de todos los módulos'),
('USER',  'Usuario',       'Solo consulta el catálogo de productos');

-- 2. Menús del sidebar (code = clave que valida PHP)
INSERT INTO menus (code, name, icon, url, sort_order) VALUES
('dashboard', 'Dashboard',  'fas fa-tachometer-alt', 'dashboard.php',              1),
('users',     'Usuarios',   'fas fa-users',          'modules/users/index.php',    2),
('profiles',  'Perfiles',   'fas fa-id-badge',       'modules/profiles/index.php', 3),
('branches',  'Sucursales', 'fas fa-map-marker-alt', 'modules/branches/index.php', 4),
('stores',    'Tiendas',    'fas fa-store',          'modules/stores/index.php',   5),
('products',  'Productos',  'fas fa-box-open',       'modules/products/index.php', 6),
('shop',      'Catálogo',   'fas fa-shopping-bag',   'modules/shop/index.php',     7);

-- 3. Permisos: ADMIN ve todos los menús, USER solo el catálogo
INSERT INTO profile_menu (profile_id, menu_id)
SELECT p.id, m.id FROM profiles p CROSS JOIN menus m WHERE p.code = 'ADMIN';

INSERT INTO profile_menu (profile_id, menu_id)
SELECT p.id, m.id FROM profiles p JOIN menus m ON m.code = 'shop' WHERE p.code = 'USER';

-- 4. Usuarios de la v1.0: todos quedan como USER y luego se promueve a ADMIN
--    al usuario indicado (cambie el correo por el suyo).
UPDATE users SET profile_id = (SELECT id FROM profiles WHERE code = 'USER')
WHERE profile_id IS NULL;

UPDATE users SET profile_id = (SELECT id FROM profiles WHERE code = 'ADMIN')
WHERE email = 'admin@mainstore.com';            -- <<< CAMBIE POR SU CORREO

ALTER TABLE users ALTER COLUMN profile_id SET NOT NULL;
ALTER TABLE users ADD CONSTRAINT fk_users_profile
    FOREIGN KEY (profile_id) REFERENCES profiles(id);

-- 5. Países
INSERT INTO countries (name, iso_code) VALUES
('Colombia', 'CO'),
('Ecuador',  'EC'),
('Perú',     'PE');

-- 6. Departamentos / provincias (se enlazan por iso_code)
INSERT INTO departments (country_id, name)
SELECT c.id, d.name
FROM (VALUES
    ('CO', 'Antioquia'), ('CO', 'Bogotá D.C.'), ('CO', 'Cundinamarca'),
    ('CO', 'Valle del Cauca'), ('CO', 'Atlántico'),
    ('EC', 'Pichincha'), ('EC', 'Guayas'),
    ('PE', 'Lima'), ('PE', 'Arequipa')
) AS d(iso, name)
JOIN countries c ON c.iso_code = d.iso;

-- 7. Ciudades (se enlazan por nombre de departamento)
INSERT INTO cities (department_id, name)
SELECT dp.id, x.city
FROM (VALUES
    ('Antioquia', 'Medellín'), ('Antioquia', 'Envigado'), ('Antioquia', 'Itagüí'),
    ('Antioquia', 'Bello'), ('Antioquia', 'Rionegro'),
    ('Bogotá D.C.', 'Bogotá'),
    ('Cundinamarca', 'Soacha'), ('Cundinamarca', 'Chía'), ('Cundinamarca', 'Zipaquirá'),
    ('Valle del Cauca', 'Cali'), ('Valle del Cauca', 'Palmira'), ('Valle del Cauca', 'Buga'),
    ('Atlántico', 'Barranquilla'), ('Atlántico', 'Soledad'),
    ('Pichincha', 'Quito'), ('Pichincha', 'Cayambe'),
    ('Guayas', 'Guayaquil'), ('Guayas', 'Durán'),
    ('Lima', 'Lima'), ('Lima', 'Huaral'),
    ('Arequipa', 'Arequipa'), ('Arequipa', 'Camaná')
) AS x(dep, city)
JOIN departments dp ON dp.name = x.dep;

-- 8. Datos de ejemplo: 1 sucursal, 2 tiendas, 6 productos
INSERT INTO branches (city_id, name, address, phone)
SELECT id, 'Sucursal Centro Medellín', 'Cra. 50 # 50-20', '604 444 0000'
FROM cities WHERE name = 'Medellín';

INSERT INTO stores (branch_id, name, description, email, phone)
SELECT b.id, s.name, s.descr, s.email, s.phone
FROM branches b,
     (VALUES ('Tecno Store', 'Tecnología y accesorios', 'tecno@mainstore.com', '300 000 0001'),
             ('Home Store',  'Hogar y cocina',          'home@mainstore.com',  '300 000 0002')
     ) AS s(name, descr, email, phone)
WHERE b.name = 'Sucursal Centro Medellín';

INSERT INTO products (store_id, sku, name, description, price, stock, image_url)
SELECT st.id, p.sku, p.name, p.descr, p.price, p.stock, p.img
FROM (VALUES
    ('Tecno Store', 'TEC-001', 'Audífonos inalámbricos', 'Bluetooth 5.3, 30 h de batería', 189900, 25, 'https://picsum.photos/seed/audifonos/600/400'),
    ('Tecno Store', 'TEC-002', 'Teclado mecánico',       'Switches rojos, retroiluminado',  259900, 12, 'https://picsum.photos/seed/teclado/600/400'),
    ('Tecno Store', 'TEC-003', 'Mouse ergonómico',       '6 botones, 4000 DPI',             89900,  40, 'https://picsum.photos/seed/mouse/600/400'),
    ('Home Store',  'HOM-001', 'Cafetera de goteo',      '12 tazas, filtro permanente',     149900, 10, 'https://picsum.photos/seed/cafetera/600/400'),
    ('Home Store',  'HOM-002', 'Juego de ollas x5',      'Acero inoxidable',                329900,  8, 'https://picsum.photos/seed/ollas/600/400'),
    ('Home Store',  'HOM-003', 'Lámpara de escritorio',  'LED, 3 tonos de luz',             79900,  30, 'https://picsum.photos/seed/lampara/600/400')
) AS p(store, sku, name, descr, price, stock, img)
JOIN stores st ON st.name = p.store;
