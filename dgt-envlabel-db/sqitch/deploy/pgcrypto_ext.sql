-- Deploy dgt-envlabel-db:pgcrypto_ext to pg
-- requires: envlabel_schema

BEGIN;

CREATE EXTENSION IF NOT EXISTS "pgcrypto" SCHEMA "envlabel";

COMMIT;
