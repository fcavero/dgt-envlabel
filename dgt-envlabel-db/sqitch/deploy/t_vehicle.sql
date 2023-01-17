-- Deploy dgt-envlabel-db:t_vehicle to pg
-- requires: tt_envlabel
-- requires: envlabel_schema

BEGIN;

CREATE TABLE IF NOT EXISTS "envlabel".t_vehicle (
    id              text NOT NULL,
    tms_creation    timestamp DEFAULT now() NOT NULL,
    envlabel_id     smallint NOT NULL,
    CONSTRAINT pk_t_vehicle PRIMARY KEY (id),
    CONSTRAINT uniq_t_vehicle_category_id_envlabel_id UNIQUE (id, envlabel_id),
    CONSTRAINT fk_t_vehicle_tt_envlabel FOREIGN KEY (envlabel_id) REFERENCES "envlabel".tt_envlabel (id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_t_vehicle_envlabel_id ON "envlabel".t_vehicle (envlabel_id);

COMMENT ON TABLE "envlabel".t_vehicle IS 'Table of the vehicles with their environment labels.';
COMMENT ON COLUMN "envlabel".t_vehicle.id IS 'License plate of the vehicle.';
COMMENT ON COLUMN "envlabel".t_vehicle.tms_creation IS 'Moment of row creation.';
COMMENT ON COLUMN "envlabel".t_vehicle.envlabel_id IS 'Internal identifier of the environmental label of the vehicle.';

COMMIT;
