-- Ajoute la colonne cover_path manquante à la table books
-- (utilisée par l'application pour stocker le chemin de la couverture uploadée,
-- oubliée lors de la migration initiale 003_books.sql)

ALTER TABLE books
    ADD COLUMN IF NOT EXISTS cover_path VARCHAR(255);
