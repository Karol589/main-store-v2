-- =====================================================================
-- MAIN-STORE v2.0  |  Script 01: Estructura (DDL)
-- TdeA 2026 - Construcción de Software II
-- Motor: PostgreSQL 14+   |   Base de datos: mainstore_v2
-- =====================================================================

-- 0. Función reutilizable: actualiza updated_at en cada UPDATE
CREATE OR REPLACE FUNCTION set_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at := NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- =====================================================================
-- MÓDULO SEGURIDAD: perfiles, menús y permisos
-- =====================================================================
CREATE TABLE profiles (
    id          SERIAL PRIMARY KEY,
    code        VARCHAR(20)  NOT NULL UNIQUE,      -- ADMIN, USER
    name        VARCHAR(80)  NOT NULL,
    description VARCHAR(255),
    created_at  TIMESTAMP    NOT NULL DEFAULT NOW(),
    updated_at  TIMESTAMP    NOT NULL DEFAULT NOW(),
    deleted_at  TIMESTAMP    NULL
);

CREATE TABLE menus (
    id          SERIAL PRIMARY KEY,
    parent_id   INT          NULL REFERENCES menus(id),
    code        VARCHAR(40)  NOT NULL UNIQUE,      -- clave usada en PHP: require_access('products')
    name        VARCHAR(80)  NOT NULL,             -- texto que se ve en el sidebar
    icon        VARCHAR(60)  NOT NULL DEFAULT 'fas fa-circle',
    url         VARCHAR(150) NOT NULL,             -- ruta relativa a BASE_URL
    sort_order  INT          NOT NULL DEFAULT 0,
    created_at  TIMESTAMP    NOT NULL DEFAULT NOW(),
    updated_at  TIMESTAMP    NOT NULL DEFAULT NOW(),
    deleted_at  TIMESTAMP    NULL
);

CREATE TABLE profile_menu (
    id          SERIAL PRIMARY KEY,
    profile_id  INT          NOT NULL REFERENCES profiles(id),
    menu_id     INT          NOT NULL REFERENCES menus(id),
    created_at  TIMESTAMP    NOT NULL DEFAULT NOW(),
    updated_at  TIMESTAMP    NOT NULL DEFAULT NOW(),
    deleted_at  TIMESTAMP    NULL,
    CONSTRAINT uq_profile_menu UNIQUE (profile_id, menu_id)
);

-- Tabla users (viene de la v1.0): se agregan profile_id y auditoría.
-- La FK se crea en 02_seed.sql, después de asignar perfil a los usuarios existentes.
ALTER TABLE users ADD COLUMN IF NOT EXISTS profile_id INT;
ALTER TABLE users ADD COLUMN IF NOT EXISTS created_at TIMESTAMP NOT NULL DEFAULT NOW();
ALTER TABLE users ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NOT NULL DEFAULT NOW();
ALTER TABLE users ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL;

-- =====================================================================
-- MÓDULO UBICACIÓN: países > departamentos > ciudades
-- =====================================================================
CREATE TABLE countries (
    id          SERIAL PRIMARY KEY,
    name        VARCHAR(100) NOT NULL UNIQUE,
    iso_code    CHAR(2)      NOT NULL UNIQUE,      -- CO, EC, PE
    created_at  TIMESTAMP    NOT NULL DEFAULT NOW(),
    updated_at  TIMESTAMP    NOT NULL DEFAULT NOW(),
    deleted_at  TIMESTAMP    NULL
);

CREATE TABLE departments (
    id          SERIAL PRIMARY KEY,
    country_id  INT          NOT NULL REFERENCES countries(id),
    name        VARCHAR(100) NOT NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT NOW(),
    updated_at  TIMESTAMP    NOT NULL DEFAULT NOW(),
    deleted_at  TIMESTAMP    NULL,
    CONSTRAINT uq_department UNIQUE (country_id, name)
);

CREATE TABLE cities (
    id            SERIAL PRIMARY KEY,
    department_id INT          NOT NULL REFERENCES departments(id),
    name          VARCHAR(100) NOT NULL,
    created_at    TIMESTAMP    NOT NULL DEFAULT NOW(),
    updated_at    TIMESTAMP    NOT NULL DEFAULT NOW(),
    deleted_at    TIMESTAMP    NULL,
    CONSTRAINT uq_city UNIQUE (department_id, name)
);

-- =====================================================================
-- MÓDULO COMERCIAL: sucursales > tiendas > productos
-- =====================================================================
CREATE TABLE branches (
    id          SERIAL PRIMARY KEY,
    city_id     INT          NOT NULL REFERENCES cities(id),
    name        VARCHAR(120) NOT NULL,
    address     VARCHAR(200) NOT NULL,
    phone       VARCHAR(30),
    created_at  TIMESTAMP    NOT NULL DEFAULT NOW(),
    updated_at  TIMESTAMP    NOT NULL DEFAULT NOW(),
    deleted_at  TIMESTAMP    NULL
);

CREATE TABLE stores (
    id          SERIAL PRIMARY KEY,
    branch_id   INT          NOT NULL REFERENCES branches(id),
    name        VARCHAR(120) NOT NULL,
    description VARCHAR(255),
    email       VARCHAR(120),
    phone       VARCHAR(30),
    is_active   BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at  TIMESTAMP    NOT NULL DEFAULT NOW(),
    updated_at  TIMESTAMP    NOT NULL DEFAULT NOW(),
    deleted_at  TIMESTAMP    NULL
);

CREATE TABLE products (
    id          SERIAL PRIMARY KEY,
    store_id    INT            NOT NULL REFERENCES stores(id),
    sku         VARCHAR(40)    NOT NULL UNIQUE,
    name        VARCHAR(150)   NOT NULL,
    description TEXT,
    price       NUMERIC(12,2)  NOT NULL CHECK (price >= 0),
    stock       INT            NOT NULL DEFAULT 0 CHECK (stock >= 0),
    image_url   VARCHAR(500),                       -- URL de la foto del producto
    is_active   BOOLEAN        NOT NULL DEFAULT TRUE,
    created_at  TIMESTAMP      NOT NULL DEFAULT NOW(),
    updated_at  TIMESTAMP      NOT NULL DEFAULT NOW(),
    deleted_at  TIMESTAMP      NULL
);

-- Índices para las llaves foráneas
CREATE INDEX idx_users_profile       ON users(profile_id);
CREATE INDEX idx_departments_country ON departments(country_id);
CREATE INDEX idx_cities_department   ON cities(department_id);
CREATE INDEX idx_branches_city       ON branches(city_id);
CREATE INDEX idx_stores_branch       ON stores(branch_id);
CREATE INDEX idx_products_store      ON products(store_id);

-- Triggers updated_at para TODAS las tablas
CREATE TRIGGER trg_profiles_upd     BEFORE UPDATE ON profiles     FOR EACH ROW EXECUTE FUNCTION set_updated_at();
CREATE TRIGGER trg_menus_upd        BEFORE UPDATE ON menus        FOR EACH ROW EXECUTE FUNCTION set_updated_at();
CREATE TRIGGER trg_profile_menu_upd BEFORE UPDATE ON profile_menu FOR EACH ROW EXECUTE FUNCTION set_updated_at();
CREATE TRIGGER trg_users_upd        BEFORE UPDATE ON users        FOR EACH ROW EXECUTE FUNCTION set_updated_at();
CREATE TRIGGER trg_countries_upd    BEFORE UPDATE ON countries    FOR EACH ROW EXECUTE FUNCTION set_updated_at();
CREATE TRIGGER trg_departments_upd  BEFORE UPDATE ON departments  FOR EACH ROW EXECUTE FUNCTION set_updated_at();
CREATE TRIGGER trg_cities_upd       BEFORE UPDATE ON cities       FOR EACH ROW EXECUTE FUNCTION set_updated_at();
CREATE TRIGGER trg_branches_upd     BEFORE UPDATE ON branches     FOR EACH ROW EXECUTE FUNCTION set_updated_at();
CREATE TRIGGER trg_stores_upd       BEFORE UPDATE ON stores       FOR EACH ROW EXECUTE FUNCTION set_updated_at();
CREATE TRIGGER trg_products_upd     BEFORE UPDATE ON products     FOR EACH ROW EXECUTE FUNCTION set_updated_at();
