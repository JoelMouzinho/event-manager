<?php
session_start();

require_once __DIR__ . '/../includes/auth.php';

requireLogin();
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="icon" type="image/x-icon" href="../assets/icon32x32nameless.png">
</head>

<body>

    <?php include '../layout/header.php'; ?>

    <main>
        <section class="intro">
            <h2>Willkommen beim Eventplaner</h2>
            <p>Organisiere deine Events einfach und übersichtlich. Nutze die Navigation, um alle Bereiche deines Events
                zu planen.</p>
        </section>

        <section class="features">
            <a href="../unterhaltung/index.php" class="feature">
                <h3>Unterhaltung</h3>
                <p>Wähle passende Unterhaltung für dein Event aus.</p>
            </a>
            <a href="../mobilliar/index.php" class="feature">
                <h3>Mobilliar</h3>
                <p>Plane die Tischordnung und Sitzplätze.</p>
            </a>
            <a href="../menue/index.php" class="feature">
                <h3>Menü</h3>
                <p>Erstelle ein individuelles Menü für deine Gäste.</p>
            </a>
            <a href="../energieversorgung/index.php" class="feature">
                <h3>Energieversorgung</h3>
                <p>Stelle sicher, dass dein Event ausreichend Energie hat.</p>
            </a>
            <a href="../termin/index.php" class="feature">
                <h3>Termin</h3>
                <p>Finde den perfekten Termin für dein Event.</p>
            </a>
            <a href="../uebersicht/index.php" class="feature">
                <h3>Übersicht</h3>
                <p>Behalte alle Informationen auf einen Blick.</p>
            </a>
        </section>
    </main>

    <?php include '../layout/footer.php'; ?>

    <script src="../js/theme-toggle.js"></script>
</body>

</html>