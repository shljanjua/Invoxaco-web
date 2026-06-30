-- =========================================================
-- Invoxaco - Digital Store: "Pay What You Want" pricing
--
-- Adds Gumroad-style pay-what-you-want pricing to digital_products:
--   pricing_model   = 'fixed' (normal) or 'pwyw' (buyer names the price)
--   suggested_price = optional amount pre-filled for the buyer
--
-- For a PWYW product, `price` is treated as the MINIMUM the buyer may pay
-- (0 allowed = "name your price, including free").
--
-- Idempotent on MariaDB (ADD COLUMN IF NOT EXISTS). Safe to re-run.
-- =========================================================

ALTER TABLE digital_products
  ADD COLUMN IF NOT EXISTS pricing_model ENUM('fixed','pwyw') NOT NULL DEFAULT 'fixed' AFTER sale_price;

ALTER TABLE digital_products
  ADD COLUMN IF NOT EXISTS suggested_price DECIMAL(10,2) NULL AFTER pricing_model;
