-- Revert dgt-envlabel-db:t_downloaded_file_log from pg

BEGIN;

DROP TABLE "envlabel".t_downloaded_file_log;

COMMIT;
