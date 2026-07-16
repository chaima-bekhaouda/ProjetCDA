-- Statut temporaire affiché après le retour d'un livre emprunté.
-- L'utilisateur repasse manuellement à un statut normal (to_read/reading/finished)
-- via le menu de changement de statut existant, ce qui fait disparaître le bandeau.

ALTER TYPE reading_status ADD VALUE IF NOT EXISTS 'returned';
