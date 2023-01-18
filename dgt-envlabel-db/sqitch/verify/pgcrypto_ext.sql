-- Verify dgt-envlabel-db:pgcrypto_ext on pg

BEGIN;

SELECT 1 / COUNT(*)
    FROM pg_extension
    WHERE extname = 'pgcrypto';

ROLLBACK;
