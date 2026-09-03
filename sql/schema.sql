-- ============================================
-- MyFoods-Eventplaner - Datenbankschema
-- ============================================
CREATE DATABASE IF NOT EXISTS eventplaner CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE eventplaner;

-- Benutzer zuerst anlegen, da events.user_id darauf verweist.
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(64) NOT NULL UNIQUE,
    user_id INT NULL,
    unterhaltung JSON DEFAULT NULL,
    mobilliar JSON DEFAULT NULL,
    menue VARCHAR(255) DEFAULT NULL,
    energie JSON DEFAULT NULL,
    termin_date DATE DEFAULT NULL,
    termin_time TIME DEFAULT NULL,
    termin_endtime TIME DEFAULT NULL,
    termin_notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_events_user_id (user_id),
    CONSTRAINT fk_events_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);
