CREATE EXTENSION IF NOT EXISTS vector;

CREATE TABLE IF NOT EXISTS incidents (
    id           BIGSERIAL PRIMARY KEY,
    incident_id  TEXT        NOT NULL,
    text         TEXT,
    device       TEXT,
    location     TEXT,
    symptom      TEXT,
    language     TEXT,
    email_from   TEXT,
    email_domain TEXT,
    created_at   TIMESTAMPTZ,
    embedding    vector
);

CREATE INDEX IF NOT EXISTS idx_incidents_incident_id  ON incidents (incident_id);
CREATE INDEX IF NOT EXISTS idx_incidents_language     ON incidents (language);
CREATE INDEX IF NOT EXISTS idx_incidents_email_from   ON incidents (email_from);
CREATE INDEX IF NOT EXISTS idx_incidents_email_domain ON incidents (email_domain);
