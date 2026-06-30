-- =========================================================
-- Invoxaco - Per-document design controls
--
-- Adds typography and colour controls to each generated document so the
-- new rich generators (Company Premium Brochure, Company Profile,
-- CV/Resume, SOP, etc.) can be styled: font family, font size scale,
-- heading colour and body text colour (accent_color already exists).
--
-- Idempotent on MariaDB (ADD COLUMN IF NOT EXISTS). Safe to re-run.
-- =========================================================

ALTER TABLE documents
  ADD COLUMN IF NOT EXISTS font_family VARCHAR(20) NOT NULL DEFAULT 'sans' AFTER template_style;

ALTER TABLE documents
  ADD COLUMN IF NOT EXISTS font_scale VARCHAR(10) NOT NULL DEFAULT 'normal' AFTER font_family;

ALTER TABLE documents
  ADD COLUMN IF NOT EXISTS heading_color VARCHAR(7) NOT NULL DEFAULT '#111827' AFTER font_scale;

ALTER TABLE documents
  ADD COLUMN IF NOT EXISTS body_color VARCHAR(7) NOT NULL DEFAULT '#1f2937' AFTER heading_color;
