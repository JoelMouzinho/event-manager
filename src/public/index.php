<?php
session_start();
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: home/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event-Manager</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" type="image/x-icon" href="assets/icon32x32nameless.png">
</head>
<body class="landing-page">
    <main class="landing">
        <section class="landing-card">
            <img class="landing-logo" src="assets/icon32x32.png" alt="Event-Manager">
            <p class="landing-eyebrow">EVENT-MANAGER</p>
            <h1>Plane dein Event.<br><span>Einfach & übersichtlich.</span></h1>
            <p class="landing-text">
                Erstelle dein persönliches Event, wähle Unterhaltung, Mobiliar,
                Menü, Energieversorgung und Termin und behalte alles an einem Ort.
            </p>
            <div class="landing-actions">
                <a class="landing-btn primary" href="login/index.php">Einloggen</a>
                <a class="landing-btn secondary" href="register/index.php">Kostenlos registrieren</a>
            </div>
            <p class="landing-note">Ein Benutzerkonto ist erforderlich, um den Event-Manager zu nutzen.</p>
        </section>
    </main>
    <script src="js/theme-toggle.js"></script>
</body>
</html>
