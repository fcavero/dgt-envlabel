-- Deploy dgt-envlabel-db:tt_envlabel to pg
-- requires: envlabel_schema

BEGIN;

CREATE TABLE IF NOT EXISTS "envlabel".tt_envlabel (
    id              smallint NOT NULL,
    txt_dgt_tag     text NOT NULL,
    txt_description text,
    CONSTRAINT pk_tt_envlabel PRIMARY KEY (id),
    CONSTRAINT uniq_tt_envlabel_txt_dgt_tag UNIQUE (txt_dgt_tag)
);

COMMENT ON TABLE "envlabel".tt_envlabel IS 'Table of official DGT types of environment labels.';
COMMENT ON COLUMN "envlabel".tt_envlabel.id IS 'Label internal identifier.';
COMMENT ON COLUMN "envlabel".tt_envlabel.txt_dgt_tag IS 'Official label of the environmental label.';
COMMENT ON COLUMN "envlabel".tt_envlabel.txt_description IS 'Common description of the environmental label.';

COMMIT;


BEGIN;

INSERT INTO "envlabel".tt_envlabel (id, txt_dgt_tag, txt_description) VALUES
 (1,'16T0','CERO EMISIONES')
,(2,'16TE','ECO')
,(3,'16TC','C')
,(4,'16TB','B')
,(5,'16M0','CERO EMISIONES')
,(6,'16ME','ECO')
,(7,'16MC','C')
,(8,'16MB','B')
,(9,'SIN DISTINTIVO','SIN DISTINTIVO')
ON CONFLICT DO NOTHING
;

COMMIT;
