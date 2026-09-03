<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

$sessionId = getOrCreateSessionId();
$data = loadEventData($pdo, $sessionId);

$unterhaltungLabels = [
    'sound'   => '🎵 Sound / Musik',
    'tv'      => '📺 TV / Filme',
    'karaoke' => '🎤 Karaoke',
    'band'    => '🎸 Live Band',
    'spiele'  => '🎮 Spiele / Quiz',
    'comedy'  => '🎭 Comedy / Show',
];
$mobilliarLabels = [
    'stuehle' => '🪑 Stühle',
    'tische'  => '📋 Tische',
    'grill'   => '🔥 Grill',
    'bar'     => '🍹 Bar',
];
$energieLabels = [
    'stromanschluss' => '⚡ Stromanschluss',
    'generator'      => '⚙️ Generator',
    'verlaengerung'  => '🔌 Verlängerungskabel',
    'beleuchtung'    => '💡 Beleuchtung',
    'notstrom'       => '🆘 Notstrom',
    'technik'        => '🎚️ Technik-Anschlüsse',
];
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Übersicht - Eventplaner</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="icon" type="image/x-icon" href="../assets/icon32x32nameless.png">
</head>

<body>

    <?php include '../layout/header.php'; ?>

    <main>
        <h2>Übersicht</h2>

        <section>
            <h3>Unterhaltung</h3>
            <ul>
                <?php if (!empty($data['unterhaltung'])): ?>
                    <?php foreach ($data['unterhaltung'] as $item): ?>
                        <li><?= htmlspecialchars($unterhaltungLabels[$item] ?? $item) ?></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li style="color:#999;">Keine Auswahl getroffen</li>
                <?php endif; ?>
            </ul>
        </section>

        <section>
            <h3>Mobilliar</h3>
            <ul>
                <?php if (!empty($data['mobilliar'])): ?>
                    <?php foreach ($data['mobilliar'] as $item): ?>
                        <li><?= htmlspecialchars($mobilliarLabels[$item] ?? $item) ?></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li style="color:#999;">Keine Auswahl getroffen</li>
                <?php endif; ?>
            </ul>
        </section>

        <section>
            <h3>Menü</h3>
            <p><?= $data['menue'] ? '📄 Menü: ' . htmlspecialchars($data['menue']) : '❌ Kein Menü gewählt' ?></p>
        </section>

        <section>
            <h3>Energieversorgung</h3>
            <ul>
                <?php if (!empty($data['energie'])): ?>
                    <?php foreach ($data['energie'] as $item): ?>
                        <li><?= htmlspecialchars($energieLabels[$item] ?? $item) ?></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li style="color:#999;">Keine Auswahl getroffen</li>
                <?php endif; ?>
            </ul>
        </section>

        <section>
            <h3>Termin</h3>
            <?php if ($data['termin_date'] && $data['termin_time']): ?>
                <p>
                    📅 <?= htmlspecialchars($data['termin_date']) ?> um <?= htmlspecialchars($data['termin_time']) ?>
                    <?php if ($data['termin_endtime']): ?>
                        - <?= htmlspecialchars($data['termin_endtime']) ?>
                    <?php endif; ?>
                </p>
            <?php else: ?>
                <p>❌ Kein Termin festgelegt</p>
            <?php endif; ?>
            <?php if ($data['termin_notes']): ?>
                <p>📝 Notizen: <?= nl2br(htmlspecialchars($data['termin_notes'])) ?></p>
            <?php endif; ?>
        </section>
    </main>

    <?php include '../layout/footer.php'; ?>

    <script src="../js/theme-toggle.js"></script>
</body>

</html>
