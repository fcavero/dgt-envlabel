-- Deploy dgt-envlabel-db:t_downloaded_file_log to pg
-- requires: pgcrypto_ext
-- requires: envlabel_schema

BEGIN;

CREATE TABLE IF NOT EXISTS "envlabel".t_downloaded_file_log (
    id              uuid DEFAULT gen_random_uuid() NOT NULL,
    tms_creation    timestamp DEFAULT now() NOT NULL,
    file_hash       text NOT NULL,
    CONSTRAINT pk_t_downloaded_file_log PRIMARY KEY (id)
);

COMMENT ON TABLE "envlabel".t_downloaded_file_log IS 'Table to log the downloaded files and a hash of each of them.';
COMMENT ON COLUMN "envlabel".t_downloaded_file_log.id IS 'Log internal identifier.';
COMMENT ON COLUMN "envlabel".t_downloaded_file_log.tms_creation IS 'Moment of row creation.';
COMMENT ON COLUMN "envlabel".t_downloaded_file_log.file_hash IS 'Hash of the downloaded file.';

COMMIT;
