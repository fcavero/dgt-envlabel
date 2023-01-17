-- Revert dgt-envlabel-db:t_vehicle from pg

BEGIN;

DROP TABLE "envlabel".t_vehicle;

COMMIT;
