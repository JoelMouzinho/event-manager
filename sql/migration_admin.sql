-- ============================================
-- Migration: Admin-Rolle für Nutzer
-- In phpMyAdmin im Reiter "SQL" auf der Datenbank "eventplaner" ausführen
-- ============================================

USE eventplaner;

ALTER TABLE users
    ADD COLUMN is_admin TINYINT(1) NOT NULL DEFAULT 0 AFTER password_hash;

-- ============================================
-- Dich selbst zum ersten Admin machen:
-- E-Mail-Adresse unten anpassen und ausführen.
-- ============================================
-- UPDATE users SET is_admin = 1 WHERE email = 'deine@email.ch';
