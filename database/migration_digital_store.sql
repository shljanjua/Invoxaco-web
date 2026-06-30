-- =========================================================
-- Invoxaco - Digital Products Store
--
-- Adds a complete digital-products e-commerce store on top of
-- the existing platform: admin-managed products (e-books,
-- templates, documents, books), a public storefront, cart,
-- one-time Stripe checkout (guest-friendly), and secure
-- post-payment download delivery.
--
-- Safe to run multiple times (CREATE TABLE IF NOT EXISTS +
-- idempotent seed inserts).
-- =========================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------
-- store_categories
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS store_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    slug VARCHAR(140) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- digital_products
-- file_path points at a file stored OUTSIDE the public webroot
-- (storage/products) and is only ever served through the
-- authenticated/tokenised download controller.
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS digital_products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT UNSIGNED NULL,
    name VARCHAR(190) NOT NULL,
    slug VARCHAR(210) NOT NULL UNIQUE,
    type ENUM('ebook','template','document','book','course','bundle','other') NOT NULL DEFAULT 'ebook',
    short_description VARCHAR(255) NULL,
    description TEXT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    sale_price DECIMAL(10,2) NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'USD',
    cover_image VARCHAR(255) NULL,
    file_path VARCHAR(255) NULL,
    file_name VARCHAR(255) NULL,
    file_size BIGINT UNSIGNED NULL,
    file_mime VARCHAR(120) NULL,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    downloads_count INT UNSIGNED NOT NULL DEFAULT 0,
    sort_order INT NOT NULL DEFAULT 0,
    meta_title VARCHAR(190) NULL,
    meta_description VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES store_categories(id) ON DELETE SET NULL,
    INDEX idx_products_active (is_active, sort_order),
    INDEX idx_products_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- orders  (user_id NULL = guest checkout)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    customer_name VARCHAR(190) NULL,
    customer_email VARCHAR(190) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    currency VARCHAR(10) NOT NULL DEFAULT 'USD',
    status ENUM('pending','paid','failed','refunded','free') NOT NULL DEFAULT 'pending',
    gateway VARCHAR(40) NULL,
    gateway_payment_id VARCHAR(190) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    paid_at DATETIME NULL,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_orders_email (customer_email),
    INDEX idx_orders_status (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- order_items  (price/name snapshotted at purchase time)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NULL,
    product_name VARCHAR(190) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES digital_products(id) ON DELETE SET NULL,
    INDEX idx_order_items_order (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- download_grants  (one per purchased item; token = unguessable
-- secret used by both the emailed link and the account library)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS download_grants (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    order_item_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NULL,
    user_id BIGINT UNSIGNED NULL,
    email VARCHAR(190) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    download_count INT UNSIGNED NOT NULL DEFAULT 0,
    max_downloads INT UNSIGNED NOT NULL DEFAULT 0,
    expires_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_grants_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_grants_item FOREIGN KEY (order_item_id) REFERENCES order_items(id) ON DELETE CASCADE,
    CONSTRAINT fk_grants_product FOREIGN KEY (product_id) REFERENCES digital_products(id) ON DELETE SET NULL,
    INDEX idx_grants_user (user_id),
    INDEX idx_grants_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Seed store categories (idempotent)
-- ---------------------------------------------------------
INSERT INTO store_categories (name, slug, description, sort_order)
SELECT * FROM (SELECT 'E-Books' AS name, 'ebooks' AS slug, 'Professional business and finance e-books' AS description, 1 AS sort_order) AS t
WHERE NOT EXISTS (SELECT 1 FROM store_categories WHERE slug = 'ebooks');

INSERT INTO store_categories (name, slug, description, sort_order)
SELECT * FROM (SELECT 'Document Templates', 'document-templates', 'Ready-to-use business document and contract templates', 2) AS t
WHERE NOT EXISTS (SELECT 1 FROM store_categories WHERE slug = 'document-templates');

INSERT INTO store_categories (name, slug, description, sort_order)
SELECT * FROM (SELECT 'Spreadsheets & Tools', 'spreadsheets-tools', 'Financial models, trackers and spreadsheet toolkits', 3) AS t
WHERE NOT EXISTS (SELECT 1 FROM store_categories WHERE slug = 'spreadsheets-tools');

INSERT INTO store_categories (name, slug, description, sort_order)
SELECT * FROM (SELECT 'Guides & Courses', 'guides-courses', 'In-depth guides and self-paced business courses', 4) AS t
WHERE NOT EXISTS (SELECT 1 FROM store_categories WHERE slug = 'guides-courses');
