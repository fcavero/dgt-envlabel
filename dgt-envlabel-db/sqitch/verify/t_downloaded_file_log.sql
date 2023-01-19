-- Verify dgt-envlabel-db:t_downloaded_file_log on pg

BEGIN;

SELECT id
     , tms_creation
     , file_hash
     FROM "envlabel".t_downloaded_file_log
     WHERE false;

ROLLBACK;
