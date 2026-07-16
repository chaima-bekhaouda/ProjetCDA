-- Permet de suivre les livres que l'utilisateur emprunte à quelqu'un
-- d'autre (sens inverse de "loans", qui suit les livres qu'il prête).

ALTER TYPE reading_status ADD VALUE IF NOT EXISTS 'borrowed';

ALTER TABLE books
    ADD COLUMN IF NOT EXISTS borrowed_from VARCHAR(180),
    ADD COLUMN IF NOT EXISTS return_due_at DATE;
