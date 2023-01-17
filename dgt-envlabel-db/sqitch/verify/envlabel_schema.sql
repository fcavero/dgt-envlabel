-- Verify dgt-envlabel-db:envlabel_schema on pg

BEGIN;

SELECT pg_catalog.has_schema_privilege('envlabel', 'usage');

ROLLBACK;
