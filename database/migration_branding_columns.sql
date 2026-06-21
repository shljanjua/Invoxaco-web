-- =========================================================
-- Invoxaco - Add branding & per-document design columns
-- Run this directly against your production database (e.g. via
-- Hostinger phpMyAdmin) if you deployed the code from commit
-- 4e763e1 ("Add company branding settings and per-document design
-- controls") or later WITHOUT also updating the database schema.
--
-- Symptom this fixes: clicking "Save Draft" or "Save & Finalize" on
-- ANY generator shows "Something went wrong. Our team has been
-- notified." This happens because the app code inserts/updates the
-- new accent_color, template_style, and show_logo columns on every
-- document save, but those columns don't exist yet on a database
-- that was set up before this feature was added.
--
-- This script only ADDS columns and never drops or modifies existing
-- data, so it is safe to run on a live database with existing rows.
-- If a column already exists you'll get a "Duplicate column name"
-- error for that one statement only -- just skip it and run the rest.
-- =========================================================

SET NAMES utf8mb4;

ALTER TABLE documents
  ADD COLUMN accent_color VARCHAR(7) NOT NULL DEFAULT '#2563eb' AFTER watermarked,
  ADD COLUMN template_style VARCHAR(20) NOT NULL DEFAULT 'modern' AFTER accent_color,
  ADD COLUMN show_logo TINYINT(1) NOT NULL DEFAULT 1 AFTER template_style;

ALTER TABLE users
  ADD COLUMN address TEXT NULL AFTER phone,
  ADD COLUMN tax_number VARCHAR(60) NULL AFTER address,
  ADD COLUMN currency VARCHAR(10) NOT NULL DEFAULT 'USD' AFTER tax_number,
  ADD COLUMN bank_name VARCHAR(150) NULL AFTER currency,
  ADD COLUMN bank_account_no VARCHAR(60) NULL AFTER bank_name,
  ADD COLUMN website VARCHAR(190) NULL AFTER bank_account_no;
