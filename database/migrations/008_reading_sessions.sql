-- Table de suivi des sessions de lecture.
-- Une session peut concerner :
--   - un livre déjà présent dans l'étagère de l'utilisateur (book_id renseigné)
--   - un livre "libre service" pas encore ajouté à l'étagère (google_volume_id renseigné)

CREATE TABLE IF NOT EXISTS reading_sessions (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    book_id UUID REFERENCES books(id) ON DELETE CASCADE,
    google_volume_id VARCHAR(64),
    title VARCHAR(255) NOT NULL,
    author VARCHAR(180),
    started_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    ended_at TIMESTAMPTZ
);

CREATE INDEX IF NOT EXISTS idx_reading_sessions_user ON reading_sessions(user_id);
