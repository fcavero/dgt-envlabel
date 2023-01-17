-- Revert dgt-envlabel-db:tt_envlabel from pg

BEGIN;

DROP TABLE "envlabel".tt_envlabel;

COMMIT;
