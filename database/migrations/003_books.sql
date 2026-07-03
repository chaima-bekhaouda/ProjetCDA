DO $$ BEGIN
    CREATE TYPE reading_status AS ENUM ('to_read', 'reading', 'finished', 'lent');
EXCEPTION
    WHEN duplicate_object THEN null;
END $$;

CREATE TABLE IF NOT EXISTS books (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(180) NOT NULL,
    year INT,
    pages INT,
    genre VARCHAR(80),
    isbn VARCHAR(20),
    cover_color VARCHAR(7) DEFAULT '#7a2e2a',
    status reading_status NOT NULL DEFAULT 'to_read',
    notes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);