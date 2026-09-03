<?php
// ============================================
// Datenbankverbindung (XAMPP-Standardwerte)
// ============================================

$host    = 'localhost';
$db      = 'eventplaner';
$user    = 'root';
$pass    = '';           // XAMPP-Standard: leeres Passwort für root
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // In Produktion NIE die echte Fehlermeldung anzeigen!
    die('Datenbankverbindung fehlgeschlagen: ' . $e->getMessage());
}
