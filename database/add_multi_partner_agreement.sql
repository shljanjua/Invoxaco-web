-- Adds a Multi-Partner Agreement generator (3+ partners with ownership %,
-- role, and contribution per partner via a repeatable group_list field).
-- Safe to run multiple times: skipped if the slug already exists.

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
