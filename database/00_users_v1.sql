-- =====================================================================
-- MAIN-STORE v1.0 (referencia) | Tabla users
-- Úselo SOLO si no tiene la base 'mainstore' de la v1.0:
--   1) CREATE DATABASE mainstore_v2;
--   2) Conectado a mainstore_v2, ejecute este script y luego 01 y 02.
-- Si ya tiene 'mainstore', use: CREATE DATABASE mainstore_v2 TEMPLATE mainstore;
-- Usuario inicial: admin@mainstore.com / admin123  (cámbiela después)
-- =====================================================================
CREATE TABLE IF NOT EXISTS users (
    id       SERIAL PRIMARY KEY,
    name     VARCHAR(100) NOT NULL,
    email    VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

INSERT INTO users (name, email, password) VALUES
('Administrador', 'admin@mainstore.com', '$2y$10$HyFUMxFjNh87LPfGwZSxO.kbbbcEA.meVCRL6lVKifto8G8WiSw9a'),
('Ana Usuario',   'ana@mainstore.com',   '$2y$10$HyFUMxFjNh87LPfGwZSxO.kbbbcEA.meVCRL6lVKifto8G8WiSw9a');
