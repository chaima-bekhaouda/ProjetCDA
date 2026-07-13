-- Permet à l'utilisateur d'ajouter manuellement des étagères vides
-- supplémentaires (au-delà de celles générées automatiquement selon
-- le nombre de livres), pour préparer de la place à l'avance.

ALTER TABLE users
    ADD COLUMN IF NOT EXISTS extra_shelves INT NOT NULL DEFAULT 0;