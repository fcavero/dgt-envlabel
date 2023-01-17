-- Verify dgt-envlabel-db:tt_envlabel on pg

BEGIN;

SELECT id
     , txt_dgt_tag
     , txt_description
     FROM "envlabel".tt_envlabel
     WHERE false;

ROLLBACK;
