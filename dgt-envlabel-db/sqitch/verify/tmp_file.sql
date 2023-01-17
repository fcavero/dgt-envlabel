-- Verify dgt-envlabel-db:tmp_file on pg

BEGIN;

SELECT txt_plate
      , txt_dgt_tag
      FROM "envlabel".tmp_file
      WHERE false;

ROLLBACK;
