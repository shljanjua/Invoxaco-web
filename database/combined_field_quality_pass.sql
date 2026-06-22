-- Combined migration: runs every field-quality-pass SQL file from this
-- round in one paste, in the correct order. Equivalent to running, in
-- sequence:
--   1. add_closing_note_defaults.sql
--   2. add_terms_tab_fields.sql
--   3. add_multi_partner_agreement.sql
--   4. add_legal_professional_fields.sql
--   5. add_hr_business_legal_fields.sql
-- Every statement below is idempotent (checked via JSON_SEARCH / WHERE NOT
-- EXISTS), so this file is safe to run more than once.

-- =====================================================================
-- 1. add_closing_note_defaults.sql
-- Adds a default "Thank you for doing business with Us!" value to the
-- existing editable Notes field on invoice-style/billing generators, so a
-- closing note appears near the bottom of the document by default while
-- remaining fully editable per-document. Safe to run multiple times.
-- =====================================================================

UPDATE document_templates SET fields_schema = JSON_SET(fields_schema, '$.fields[7].default', 'Thank you for doing business with Us!') WHERE slug = 'invoice-generator';
UPDATE document_templates SET fields_schema = JSON_SET(fields_schema, '$.fields[6].default', 'Thank you for doing business with Us!') WHERE slug = 'receipt-generator';
UPDATE document_templates SET fields_schema = JSON_SET(fields_schema, '$.fields[7].default', 'Thank you for doing business with Us!') WHERE slug = 'credit-note-generator';
UPDATE document_templates SET fields_schema = JSON_SET(fields_schema, '$.fields[7].default', 'Thank you for doing business with Us!') WHERE slug = 'debit-note-generator';
UPDATE document_templates SET fields_schema = JSON_SET(fields_schema, '$.fields[6].default', 'Thank you for doing business with Us!') WHERE slug = 'payment-receipt-generator';
UPDATE document_templates SET fields_schema = JSON_SET(fields_schema, '$.fields[7].default', 'Thank you for doing business with Us!') WHERE slug = 'proforma-invoice-generator';
UPDATE document_templates SET fields_schema = JSON_SET(fields_schema, '$.fields[6].default', 'Thank you for doing business with Us!') WHERE slug = 'purchase-order-generator';
UPDATE document_templates SET fields_schema = JSON_SET(fields_schema, '$.fields[7].default', 'Thank you for doing business with Us!') WHERE slug = 'sales-order-generator';
UPDATE document_templates SET fields_schema = JSON_SET(fields_schema, '$.fields[6].default', 'Thank you for doing business with Us!') WHERE slug = 'order-confirmation-generator';
UPDATE document_templates SET fields_schema = JSON_SET(fields_schema, '$.fields[7].default', 'Thank you for doing business with Us!') WHERE slug = 'delivery-note-generator';

-- =====================================================================
-- 2. add_terms_tab_fields.sql
-- Adds a Terms/Notes field to generators that were missing one, so every
-- generator gets a Terms tab in the editor. Idempotent: each statement only
-- appends the field if a field with that name doesn't already exist.
-- =====================================================================

UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','additional_terms','label','Additional Terms & Conditions','type','textarea','required',false)) WHERE slug = 'non-disclosure-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'additional_terms', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','additional_terms','label','Additional Terms & Conditions','type','textarea','required',false)) WHERE slug = 'partnership-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'additional_terms', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','additional_terms','label','Additional Terms & Conditions','type','textarea','required',false)) WHERE slug = 'power-of-attorney-generator' AND JSON_SEARCH(fields_schema, 'one', 'additional_terms', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','additional_terms','label','Additional Terms & Conditions','type','textarea','required',false)) WHERE slug = 'cease-and-desist-letter-generator' AND JSON_SEARCH(fields_schema, 'one', 'additional_terms', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','additional_terms','label','Additional Terms & Conditions','type','textarea','required',false)) WHERE slug = 'demand-letter-generator' AND JSON_SEARCH(fields_schema, 'one', 'additional_terms', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','additional_terms','label','Additional Terms & Conditions','type','textarea','required',false)) WHERE slug = 'termination-letter-generator' AND JSON_SEARCH(fields_schema, 'one', 'additional_terms', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','additional_terms','label','Additional Terms & Conditions','type','textarea','required',false)) WHERE slug = 'employee-warning-letter-generator' AND JSON_SEARCH(fields_schema, 'one', 'additional_terms', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','additional_terms','label','Additional Terms & Conditions','type','textarea','required',false)) WHERE slug = 'non-compete-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'additional_terms', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','additional_terms','label','Additional Terms & Conditions','type','textarea','required',false)) WHERE slug = 'retainer-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'additional_terms', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','additional_terms','label','Additional Terms & Conditions','type','textarea','required',false)) WHERE slug = 'property-management-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'additional_terms', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','additional_terms','label','Additional Terms & Conditions','type','textarea','required',false)) WHERE slug = 'equity-vesting-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'additional_terms', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','additional_terms','label','Additional Terms & Conditions','type','textarea','required',false)) WHERE slug = 'advisory-board-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'additional_terms', NULL, '$.fields[*].name') IS NULL;

UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','notes','label','Additional Notes','type','textarea','required',false)) WHERE slug = 'affidavit-generator' AND JSON_SEARCH(fields_schema, 'one', 'notes', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','notes','label','Additional Notes','type','textarea','required',false)) WHERE slug = 'release-of-liability-generator' AND JSON_SEARCH(fields_schema, 'one', 'notes', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','notes','label','Additional Notes','type','textarea','required',false)) WHERE slug = 'resignation-letter-generator' AND JSON_SEARCH(fields_schema, 'one', 'notes', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','notes','label','Additional Notes','type','textarea','required',false)) WHERE slug = 'performance-review-form-generator' AND JSON_SEARCH(fields_schema, 'one', 'notes', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','notes','label','Additional Notes','type','textarea','required',false)) WHERE slug = 'change-order-generator' AND JSON_SEARCH(fields_schema, 'one', 'notes', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','notes','label','Additional Notes','type','textarea','required',false)) WHERE slug = 'punch-list-generator' AND JSON_SEARCH(fields_schema, 'one', 'notes', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','notes','label','Additional Notes','type','textarea','required',false)) WHERE slug = 'property-listing-sheet-generator' AND JSON_SEARCH(fields_schema, 'one', 'notes', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','notes','label','Additional Notes','type','textarea','required',false)) WHERE slug = 'eviction-notice-generator' AND JSON_SEARCH(fields_schema, 'one', 'notes', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','notes','label','Additional Notes','type','textarea','required',false)) WHERE slug = 'rent-receipt-generator' AND JSON_SEARCH(fields_schema, 'one', 'notes', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','notes','label','Additional Notes','type','textarea','required',false)) WHERE slug = 'business-proposal-generator' AND JSON_SEARCH(fields_schema, 'one', 'notes', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','notes','label','Additional Notes','type','textarea','required',false)) WHERE slug = 'project-brief-generator' AND JSON_SEARCH(fields_schema, 'one', 'notes', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','notes','label','Additional Notes','type','textarea','required',false)) WHERE slug = 'standard-operating-procedure-generator' AND JSON_SEARCH(fields_schema, 'one', 'notes', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','notes','label','Additional Notes','type','textarea','required',false)) WHERE slug = 'business-plan-generator' AND JSON_SEARCH(fields_schema, 'one', 'notes', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','notes','label','Additional Notes','type','textarea','required',false)) WHERE slug = 'memo-generator' AND JSON_SEARCH(fields_schema, 'one', 'notes', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','notes','label','Additional Notes','type','textarea','required',false)) WHERE slug = 'marketing-plan-generator' AND JSON_SEARCH(fields_schema, 'one', 'notes', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','notes','label','Additional Notes','type','textarea','required',false)) WHERE slug = 'sponsorship-proposal-generator' AND JSON_SEARCH(fields_schema, 'one', 'notes', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','notes','label','Additional Notes','type','textarea','required',false)) WHERE slug = 'event-marketing-plan-generator' AND JSON_SEARCH(fields_schema, 'one', 'notes', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','notes','label','Additional Notes','type','textarea','required',false)) WHERE slug = 'pitch-deck-outline-generator' AND JSON_SEARCH(fields_schema, 'one', 'notes', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','notes','label','Additional Notes','type','textarea','required',false)) WHERE slug = 'business-model-canvas-generator' AND JSON_SEARCH(fields_schema, 'one', 'notes', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','notes','label','Additional Notes','type','textarea','required',false)) WHERE slug = 'cap-table-generator' AND JSON_SEARCH(fields_schema, 'one', 'notes', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','notes','label','Additional Notes','type','textarea','required',false)) WHERE slug = 'executive-summary-generator' AND JSON_SEARCH(fields_schema, 'one', 'notes', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','notes','label','Additional Notes','type','textarea','required',false)) WHERE slug = 'cv-resume-generator' AND JSON_SEARCH(fields_schema, 'one', 'notes', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','notes','label','Additional Notes','type','textarea','required',false)) WHERE slug = 'company-profile-generator' AND JSON_SEARCH(fields_schema, 'one', 'notes', NULL, '$.fields[*].name') IS NULL;

-- =====================================================================
-- 3. add_multi_partner_agreement.sql
-- Adds a Multi-Partner Agreement generator (3+ partners with ownership %,
-- role, and contribution per partner via a repeatable group_list field).
-- Safe to run multiple times: skipped if the slug already exists.
-- =====================================================================

INSERT INTO document_templates (category_id, name, slug, short_description, description, icon, plan_required, fields_schema, faqs, is_built, is_active, meta_title, meta_description, sort_order)
SELECT 3, 'Multi-Partner Agreement Generator', 'multi-partner-agreement-generator',
  'Create a professional multi-partner business agreement online in minutes — add any number of partners with their ownership, role, and contribution, preview instantly, and download as PDF or DOCX.',
  'Create a professional multi-partner business agreement online in minutes — add any number of partners with their ownership, role, and contribution, preview instantly, and download as PDF or DOCX.',
  'bi-bank', 'free',
  '{"fields":[{"name":"agreement_date","label":"Agreement Date","type":"date","required":true},{"name":"business_name","label":"Business / Venture Name","type":"text","required":true},{"name":"business_purpose","label":"Business Purpose","type":"textarea","required":true},{"name":"partners","label":"Partners","type":"group_list","required":true,"columns":[{"name":"name","label":"Partner Name","type":"text"},{"name":"ownership_percentage","label":"Ownership %","type":"text"},{"name":"role","label":"Role / Title","type":"text"},{"name":"contribution","label":"Capital / Asset Contribution","type":"textarea"}]},{"name":"profit_loss_distribution","label":"Profit & Loss Distribution","type":"textarea","required":false},{"name":"management_structure","label":"Management & Decision-Making","type":"textarea","required":false},{"name":"dispute_resolution","label":"Dispute Resolution","type":"textarea","required":false},{"name":"governing_law","label":"Governing Law / Jurisdiction","type":"text","required":false},{"name":"witness_name","label":"Witness Name (optional)","type":"text","required":false},{"name":"additional_terms","label":"Additional Terms & Conditions","type":"textarea","required":false}]}',
  '[{"q":"Is the Multi-Partner Agreement Generator free to use?","a":"Yes, every Invoxaco plan includes access to the multi-partner agreement tool. Free plan documents are watermarked and limited to 10 per month; Pro and Premium plans remove the watermark and raise or remove the limit."},{"q":"How many partners can I add?","a":"You can add as many partners as your agreement needs — each with their own ownership percentage, role, and contribution."},{"q":"Can I download the agreement as PDF and Word?","a":"Yes. Every document you create can be exported as a print-ready PDF, and Pro/Premium plans can also export an editable DOCX file."}]',
  1, 1,
  'Multi-Partner Agreement Generator - Free Online Multi-Partner Agreement Generator | Invoxaco',
  'Generate a Multi-Partner Agreement online free with Invoxaco. Add any number of partners, fill a simple form, preview instantly, and export as PDF or Word.',
  12
WHERE NOT EXISTS (SELECT 1 FROM document_templates WHERE slug = 'multi-partner-agreement-generator');

-- =====================================================================
-- 4. add_legal_professional_fields.sql
-- Adds professional legal fields (witness, notarization, governing law) to
-- the core Legal Documents category so agreements/contracts read like real
-- signed legal documents, not bare forms. Idempotent: each statement only
-- appends a field if one with that name doesn't already exist.
-- =====================================================================

UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'non-disclosure-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'contract-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'service-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'partnership-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'power-of-attorney-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'lease-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'memorandum-of-understanding-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'release-of-liability-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;

UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','sworn_location','label','Sworn/Signed At (City, State)','type','text','required',false)) WHERE slug = 'affidavit-generator' AND JSON_SEARCH(fields_schema, 'one', 'sworn_location', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','notary_name','label','Notary Public Name (optional)','type','text','required',false)) WHERE slug = 'affidavit-generator' AND JSON_SEARCH(fields_schema, 'one', 'notary_name', NULL, '$.fields[*].name') IS NULL;

UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','governing_law','label','Governing Law / Jurisdiction','type','text','required',false)) WHERE slug = 'lease-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'governing_law', NULL, '$.fields[*].name') IS NULL;

-- =====================================================================
-- 5. add_hr_business_legal_fields.sql
-- Extends the witness/governing-law field-quality pass from the core Legal
-- Documents category to every other "agreement"/"contract" generator across
-- HR, Construction, Real Estate, Freelancer, Business Operations, Marketing,
-- and Startup categories. Idempotent: each statement only appends a field
-- if one with that name doesn't already exist.
-- =====================================================================

UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'employment-contract-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'construction-contract-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'freelance-contract-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'independent-contractor-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'vendor-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'non-compete-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'founders-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'advisory-board-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'sales-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;

UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','governing_law','label','Governing Law / Jurisdiction','type','text','required',false)) WHERE slug = 'subcontractor-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'governing_law', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'subcontractor-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;

UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','governing_law','label','Governing Law / Jurisdiction','type','text','required',false)) WHERE slug = 'rental-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'governing_law', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'rental-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;

UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','governing_law','label','Governing Law / Jurisdiction','type','text','required',false)) WHERE slug = 'real-estate-purchase-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'governing_law', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'real-estate-purchase-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;

UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','governing_law','label','Governing Law / Jurisdiction','type','text','required',false)) WHERE slug = 'property-management-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'governing_law', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'property-management-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;

UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','governing_law','label','Governing Law / Jurisdiction','type','text','required',false)) WHERE slug = 'retainer-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'governing_law', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'retainer-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;

UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','governing_law','label','Governing Law / Jurisdiction','type','text','required',false)) WHERE slug = 'influencer-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'governing_law', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'influencer-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;

UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','governing_law','label','Governing Law / Jurisdiction','type','text','required',false)) WHERE slug = 'advertising-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'governing_law', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'advertising-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;

UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','governing_law','label','Governing Law / Jurisdiction','type','text','required',false)) WHERE slug = 'equity-vesting-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'governing_law', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'equity-vesting-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;
