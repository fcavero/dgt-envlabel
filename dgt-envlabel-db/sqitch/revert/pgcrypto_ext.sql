-- Revert dgt-envlabel-db:pgcrypto_ext from pg

BEGIN;

DROP EXTENSION "pgcrypto";

COMMIT;
