-- Adds professional legal fields (witness, notarization, governing law) to
-- the core Legal Documents category so agreements/contracts read like real
-- signed legal documents, not bare forms. Idempotent: each statement only
-- appends a field if one with that name doesn't already exist.

-- Witness line for agreements signed between two or more parties.
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'non-disclosure-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'contract-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'service-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'partnership-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'power-of-attorney-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'lease-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'memorandum-of-understanding-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','witness_name','label','Witness Name (optional)','type','text','required',false)) WHERE slug = 'release-of-liability-generator' AND JSON_SEARCH(fields_schema, 'one', 'witness_name', NULL, '$.fields[*].name') IS NULL;

-- Affidavits are sworn statements: add notarization fields instead of a witness.
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','sworn_location','label','Sworn/Signed At (City, State)','type','text','required',false)) WHERE slug = 'affidavit-generator' AND JSON_SEARCH(fields_schema, 'one', 'sworn_location', NULL, '$.fields[*].name') IS NULL;
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','notary_name','label','Notary Public Name (optional)','type','text','required',false)) WHERE slug = 'affidavit-generator' AND JSON_SEARCH(fields_schema, 'one', 'notary_name', NULL, '$.fields[*].name') IS NULL;

-- Lease agreements should specify governing law/jurisdiction like other contracts.
UPDATE document_templates SET fields_schema = JSON_ARRAY_APPEND(fields_schema, '$.fields', JSON_OBJECT('name','governing_law','label','Governing Law / Jurisdiction','type','text','required',false)) WHERE slug = 'lease-agreement-generator' AND JSON_SEARCH(fields_schema, 'one', 'governing_law', NULL, '$.fields[*].name') IS NULL;
