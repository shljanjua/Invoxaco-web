-- =========================================================
-- Invoxaco - MySQL Schema
-- Charset: utf8mb4 / Engine: InnoDB
-- Import this file via phpMyAdmin or `mysql -u user -p db < schema.sql`
-- =========================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -------------------------------------------------
-- users
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user','admin') NOT NULL DEFAULT 'user',
    plan ENUM('free','pro','premium') NOT NULL DEFAULT 'free',
    plan_billing_cycle ENUM('monthly','yearly') NULL,
    plan_expires_at DATETIME NULL,
    stripe_customer_id VARCHAR(190) NULL,
    company_name VARCHAR(190) NULL,
    company_logo VARCHAR(255) NULL,
    signature_path VARCHAR(255) NULL,
    company_stamp_path VARCHAR(255) NULL,
    phone VARCHAR(40) NULL,
    address TEXT NULL,
    city VARCHAR(100) NULL,
    state VARCHAR(100) NULL,
    country VARCHAR(100) NULL,
    business_registration_number VARCHAR(60) NULL,
    tax_number VARCHAR(60) NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'USD',
    bank_name VARCHAR(150) NULL,
    bank_account_title VARCHAR(150) NULL,
    bank_account_no VARCHAR(60) NULL,
    bank_swift_code VARCHAR(20) NULL,
    bank_branch VARCHAR(150) NULL,
    website VARCHAR(190) NULL,
    email_verified_at DATETIME NULL,
    verification_token VARCHAR(100) NULL,
    verification_expires_at DATETIME NULL,
    remember_token VARCHAR(255) NULL,
    is_banned TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_users_role (role),
    INDEX idx_users_plan (plan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------
-- password_resets
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS password_resets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(190) NOT NULL,
    token VARCHAR(100) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_password_resets_email (email),
    INDEX idx_password_resets_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------
-- rate_limits
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS rate_limits (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    key_name VARCHAR(190) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_rate_limits_key (key_name, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------
-- categories (document generator categories)
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    slug VARCHAR(140) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    icon VARCHAR(60) NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------
-- document_templates (the 110 generators catalog)
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS document_templates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(170) NOT NULL UNIQUE,
    short_description VARCHAR(255) NULL,
    description TEXT NULL,
    icon VARCHAR(60) NULL,
    plan_required ENUM('free','pro','premium') NOT NULL DEFAULT 'free',
    fields_schema JSON NULL,
    faqs JSON NULL,
    is_built TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    meta_title VARCHAR(190) NULL,
    meta_description VARCHAR(255) NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_templates_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_templates_built (is_built, is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------
-- clients
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS clients (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NULL,
    phone VARCHAR(40) NULL,
    company VARCHAR(150) NULL,
    address TEXT NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_clients_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_clients_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------
-- documents
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS documents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    template_id BIGINT UNSIGNED NOT NULL,
    client_id BIGINT UNSIGNED NULL,
    title VARCHAR(190) NOT NULL,
    document_number VARCHAR(60) NULL,
    data JSON NOT NULL,
    status ENUM('draft','final') NOT NULL DEFAULT 'draft',
    share_token VARCHAR(64) NULL UNIQUE,
    watermarked TINYINT(1) NOT NULL DEFAULT 0,
    accent_color VARCHAR(7) NOT NULL DEFAULT '#2563eb',
    template_style VARCHAR(20) NOT NULL DEFAULT 'modern',
    font_family VARCHAR(20) NOT NULL DEFAULT 'sans',
    font_scale VARCHAR(10) NOT NULL DEFAULT 'normal',
    heading_color VARCHAR(7) NOT NULL DEFAULT '#111827',
    body_color VARCHAR(7) NOT NULL DEFAULT '#1f2937',
    show_logo TINYINT(1) NOT NULL DEFAULT 1,
    show_stamp TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_documents_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_documents_template FOREIGN KEY (template_id) REFERENCES document_templates(id) ON DELETE CASCADE,
    CONSTRAINT fk_documents_client FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL,
    INDEX idx_documents_user (user_id),
    INDEX idx_documents_created (user_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------
-- teams / team_members
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS teams (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    owner_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_teams_owner FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS team_members (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    team_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    email VARCHAR(190) NOT NULL,
    role ENUM('owner','admin','member') NOT NULL DEFAULT 'member',
    status ENUM('invited','active') NOT NULL DEFAULT 'invited',
    invite_token VARCHAR(100) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_team_members_team FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    CONSTRAINT fk_team_members_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_team_members_team (team_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------
-- subscriptions / payments
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS subscriptions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    plan ENUM('free','pro','premium') NOT NULL,
    billing_cycle ENUM('monthly','yearly') NULL,
    status ENUM('active','cancelled','expired') NOT NULL DEFAULT 'active',
    gateway VARCHAR(40) NULL,
    gateway_subscription_id VARCHAR(190) NULL,
    current_period_end DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_subscriptions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_subscriptions_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    subscription_id BIGINT UNSIGNED NULL,
    gateway VARCHAR(40) NOT NULL,
    gateway_payment_id VARCHAR(190) NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'USD',
    status ENUM('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_payments_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_payments_subscription FOREIGN KEY (subscription_id) REFERENCES subscriptions(id) ON DELETE SET NULL,
    INDEX idx_payments_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------
-- blog
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS blog_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    slug VARCHAR(140) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS blog_posts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT UNSIGNED NULL,
    author_id BIGINT UNSIGNED NULL,
    title VARCHAR(190) NOT NULL,
    slug VARCHAR(210) NOT NULL UNIQUE,
    excerpt VARCHAR(255) NULL,
    content LONGTEXT NOT NULL,
    featured_image VARCHAR(255) NULL,
    tags VARCHAR(255) NULL,
    status ENUM('draft','published') NOT NULL DEFAULT 'draft',
    meta_title VARCHAR(190) NULL,
    meta_description VARCHAR(255) NULL,
    published_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_blog_posts_category FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL,
    CONSTRAINT fk_blog_posts_author FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_blog_posts_status (status, published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------
-- seo_settings / smtp_settings / settings
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS seo_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    page_key VARCHAR(190) NOT NULL UNIQUE,
    meta_title VARCHAR(190) NULL,
    meta_description VARCHAR(255) NULL,
    og_image VARCHAR(255) NULL,
    canonical_url VARCHAR(255) NULL,
    robots VARCHAR(60) NOT NULL DEFAULT 'index,follow',
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS smtp_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    host VARCHAR(190) NOT NULL,
    port INT NOT NULL DEFAULT 587,
    encryption VARCHAR(10) NOT NULL DEFAULT 'tls',
    username VARCHAR(190) NULL,
    password VARCHAR(255) NULL,
    from_address VARCHAR(190) NOT NULL,
    from_name VARCHAR(190) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    key_name VARCHAR(120) NOT NULL UNIQUE,
    value LONGTEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------
-- contact_messages / support_tickets
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS contact_messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL,
    subject VARCHAR(190) NULL,
    message TEXT NOT NULL,
    status ENUM('new','read','replied') NOT NULL DEFAULT 'new',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS support_tickets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    subject VARCHAR(190) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('open','pending','closed') NOT NULL DEFAULT 'open',
    priority ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_tickets_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS support_ticket_replies (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ticket_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_replies_ticket FOREIGN KEY (ticket_id) REFERENCES support_tickets(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------
-- activity_logs
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(100) NOT NULL,
    description VARCHAR(255) NULL,
    ip_address VARCHAR(45) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_activity_logs_user (user_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------
-- waitlist_entries (notify-me for not-yet-built generators)
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS waitlist_entries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    template_id BIGINT UNSIGNED NOT NULL,
    email VARCHAR(190) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_waitlist_template FOREIGN KEY (template_id) REFERENCES document_templates(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_waitlist (template_id, email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------
-- Digital products store
-- -------------------------------------------------
CREATE TABLE IF NOT EXISTS store_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    slug VARCHAR(140) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
    pricing_model ENUM('fixed','pwyw') NOT NULL DEFAULT 'fixed',
    suggested_price DECIMAL(10,2) NULL,
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

SET FOREIGN_KEY_CHECKS = 1;
