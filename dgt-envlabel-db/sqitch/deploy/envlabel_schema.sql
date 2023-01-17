-- Deploy dgt-envlabel-db:envlabel_schema to pg

BEGIN;

CREATE SCHEMA IF NOT EXISTS "envlabel";

COMMIT;
