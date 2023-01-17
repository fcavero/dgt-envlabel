-- Revert dgt-envlabel-db:tmp_file from pg

BEGIN;

DROP TABLE "envlabel".tmp_file;

COMMIT;
