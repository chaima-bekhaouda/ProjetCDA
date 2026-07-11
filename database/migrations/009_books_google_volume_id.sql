-- Permet de relier un livre de l'étagère à sa fiche Google Books,
-- pour afficher le bouton "Lire" (lecteur intégré) sur les livres
-- ajoutés depuis le catalogue libre service.

ALTER TABLE books
    ADD COLUMN IF NOT EXISTS google_volume_id VARCHAR(64);
