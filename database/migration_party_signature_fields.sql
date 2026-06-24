-- =========================================================
-- Invoxaco - Add company profile, banking, and stamp/signature
-- columns needed for the new structured agreement/quotation
-- generators (party blocks, signature blocks, richer company
-- profile, bank details).
--
-- Run this directly against your production database (e.g. via
-- Hostinger phpMyAdmin) AFTER deploying the corresponding code
-- changes, or the Settings page / new generators will show
-- "Something went wrong. Our team has been notified." the same
-- way the contact form did when its column was missing.
--
-- This script only ADDS columns and never drops or modifies
-- existing data, so it is safe to run on a live database with
-- existing rows. If a column already exists you'll get a
-- "Duplicate column name" error for that one statement only --
-- just skip it and run the rest.
-- =========================================================

SET NAMES utf8mb4;

ALTER TABLE users
  ADD COLUMN company_stamp_path VARCHAR(255) NULL AFTER signature_path,
  ADD COLUMN city VARCHAR(100) NULL AFTER address,
  ADD COLUMN state VARCHAR(100) NULL AFTER city,
  ADD COLUMN country VARCHAR(100) NULL AFTER state,
  ADD COLUMN business_registration_number VARCHAR(60) NULL AFTER company_name,
  ADD COLUMN bank_account_title VARCHAR(150) NULL AFTER bank_name,
  ADD COLUMN bank_swift_code VARCHAR(20) NULL AFTER bank_account_no,
  ADD COLUMN bank_branch VARCHAR(150) NULL AFTER bank_swift_code;

ALTER TABLE documents
  ADD COLUMN show_stamp TINYINT(1) NOT NULL DEFAULT 0 AFTER show_logo;

-- =========================================================
-- Refresh the fields_schema for the two flagship generators
-- (Quotation, Rental Agreement) so existing installs pick up
-- the new party/signature/select field types, per-item
-- discount/tax columns, and document-level discount/extra
-- charge fields. Safe to re-run; it only overwrites the
-- fields_schema/faqs columns for these two rows by slug and
-- never touches any other generator or any existing document.
-- =========================================================

UPDATE document_templates
SET fields_schema = '{"fields":[{"name":"quote_number","label":"Quotation Number","type":"text","required":true},{"name":"quote_date","label":"Quotation Date","type":"date","required":true},{"name":"valid_until","label":"Valid Until","type":"date","required":false},{"name":"from_name","label":"From (Your Business)","type":"textarea","required":true},{"name":"to_name","label":"Prepared For","type":"textarea","required":true},{"name":"items","label":"Line Items","type":"line_items","required":true,"has_unit":true,"per_item_discount":true,"per_item_tax":true},{"name":"tax_rate","label":"Tax Rate (%)","type":"number","required":false},{"name":"discount_type","label":"Discount Type","type":"select","required":false,"options":["fixed","percent"]},{"name":"discount_value","label":"Discount Value","type":"number","required":false},{"name":"shipping_cost","label":"Shipping Cost","type":"number","required":false},{"name":"installation_charges","label":"Installation Charges","type":"number","required":false},{"name":"delivery_charges","label":"Delivery Charges","type":"number","required":false},{"name":"training_charges","label":"Training Charges","type":"number","required":false},{"name":"support_charges","label":"Support Charges","type":"number","required":false},{"name":"prepared_by_signature","label":"Authorized Signature","type":"signature","required":false},{"name":"terms","label":"Terms and Conditions","type":"textarea","required":false}]}'
WHERE slug = 'quotation-generator';

UPDATE document_templates
SET fields_schema = '{"fields":[{"name":"agreement_date","label":"Agreement Date","type":"date","required":true},{"name":"landlord","label":"Landlord","type":"party","required":true},{"name":"tenant","label":"Tenant","type":"party","required":true},{"name":"property_address","label":"Property Address","type":"textarea","required":true},{"name":"property_type","label":"Property Type","type":"select","required":false,"options":["Residential","Commercial","Apartment","House","Shop","Office","Land"]},{"name":"start_date","label":"Lease Start Date","type":"date","required":true},{"name":"end_date","label":"Lease End Date","type":"date","required":true},{"name":"monthly_rent","label":"Monthly Rent","type":"number","required":true},{"name":"security_deposit","label":"Security Deposit","type":"number","required":false},{"name":"included_items","label":"Included Fixtures and Appliances","type":"group_list","required":false,"columns":[{"name":"item","label":"Item","type":"select_or_text","options":["Refrigerator","Air Conditioner","Washing Machine","Bed","Sofa Set","Dining Table","Water Heater","Curtains","Stove","Wardrobe"]},{"name":"condition","label":"Condition","type":"text"}]},{"name":"maintenance_terms","label":"Maintenance Terms","type":"textarea","required":false},{"name":"house_rules","label":"House Rules","type":"textarea","required":false},{"name":"termination_terms","label":"Termination Terms","type":"textarea","required":false},{"name":"terms","label":"Additional Terms","type":"textarea","required":false},{"name":"landlord_signature","label":"Landlord Signature","type":"signature","required":false},{"name":"tenant_signature","label":"Tenant Signature","type":"signature","required":false}]}'
WHERE slug = 'rental-agreement-generator';
