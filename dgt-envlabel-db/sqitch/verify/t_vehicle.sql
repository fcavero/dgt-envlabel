-- Verify dgt-envlabel-db:t_vehicle on pg

BEGIN;

SELECT id
     , txt_plate
     , tms_creation
     , envlabel_id
     FROM "envlabel".t_vehicle
     WHERE false;

ROLLBACK;
