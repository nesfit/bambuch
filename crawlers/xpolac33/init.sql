-- Table: public."bitAccounts"

-- DROP TABLE public."bitAccounts";

CREATE TABLE public."bitAccounts"
(
    "Adress" "char"[] NOT NULL,
    "Url" "char"[] NOT NULL,
    "Description" "char"[] NOT NULL,
    "Change" date NOT NULL
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public."bitAccounts"
    OWNER to postgres;
