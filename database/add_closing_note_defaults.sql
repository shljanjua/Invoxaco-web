-- Adds a default "Thank you for doing business with Us!" value to the
-- existing editable Notes field on invoice-style/billing generators, so a
-- closing note appears near the bottom of the document by default while
-- remaining fully editable per-document. Safe to run multiple times.

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
