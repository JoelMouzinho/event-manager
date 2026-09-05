-- ============================================
-- Migration: Mehrere Events pro Nutzer
-- In phpMyAdmin im Reiter "SQL" auf der Datenbank "eventplaner" ausführen
-- ============================================

USE eventplaner;

-- 1) Namensfeld für Events hinzufügen
ALTER TABLE events
    ADD COLUMN name VARCHAR(255) NOT NULL DEFAULT 'Mein Event' AFTER id;

-- 2) session_id wird nicht mehr für die Zuordnung gebraucht (das übernimmt jetzt
--    ausschliesslich user_id + die Event-Auswahl über "Meine Events").
--    Falls das Entfernen der UNIQUE-Regel fehlschlägt, prüfe mit
--    `SHOW INDEX FROM events;` den genauen Index-Namen und passe ihn unten an.
ALTER TABLE events DROP INDEX session_id;
ALTER TABLE events MODIFY session_id VARCHAR(64) NULL;

-- 3) Jedes bestehende Event muss ab jetzt einem Nutzer gehören.
--    Falls noch anonyme Events (user_id IS NULL) aus der Zeit vor dem Login
--    existieren, kannst du sie hier löschen (sie können sonst niemandem mehr
--    zugeordnet werden):
DELETE FROM events WHERE user_id IS NULL;

ALTER TABLE events MODIFY user_id INT NOT NULL;
