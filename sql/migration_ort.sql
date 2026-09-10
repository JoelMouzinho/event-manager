-- ============================================
-- Migration: Ort-Feld für Events
-- In phpMyAdmin im Reiter "SQL" auf der Datenbank "eventplaner" ausführen
-- ============================================

USE eventplaner;

ALTER TABLE events
    ADD COLUMN ort VARCHAR(32) DEFAULT NULL AFTER user_id;
