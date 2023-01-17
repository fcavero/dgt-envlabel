-- Revert dgt-envlabel-db:envlabel_schema from pg

BEGIN;

DROP SCHEMA "envlabel";

COMMIT;
