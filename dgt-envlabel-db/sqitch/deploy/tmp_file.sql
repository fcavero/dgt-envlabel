-- Deploy dgt-envlabel-db:tmp_file to pg
-- requires: envlabel_schema

BEGIN;

CREATE TABLE IF NOT EXISTS "envlabel".tmp_file (
    txt_plate   text NOT NULL,
    txt_dgt_tag text NOT NULL,
    CONSTRAINT pk_tmp_file_txt_plate PRIMARY KEY (txt_plate)
);

COMMIT;
