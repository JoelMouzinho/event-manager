<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

$userId = currentUserId();
$eventId = getCurrentEventId($pdo, $userId);
$data = loadEventData($pdo, $eventId);

$unterhaltungLabels = [
    'sound' => '🎵 Sound / Musik',
    'tv' => '📺 TV / Filme',
    'karaoke' => '🎤 Karaoke',
    'band' => '🎸 Live Band',
    'spiele' => '🎮 Spiele / Quiz',
    'comedy' => '🎭 Comedy / Show',
];
$mobilliarLabels = [
    'stuehle' => '🪑 Stühle',
    'tische' => '📋 Tische',
    'grill' => '🔥 Grill',
    'bar' => '🍹 Bar',
];
$energieLabels = [
    'stromanschluss' => '⚡ Stromanschluss',
    'generator' => '⚙️ Generator',
    'verlaengerung' => '🔌 Verlängerungskabel',
    'beleuchtung' => '💡 Beleuchtung',
    'notstrom' => '🆘 Notstrom',
    'technik' => '🎚️ Technik-Anschlüsse',
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
        <section class="intro">
            <h2><?= htmlspecialchars($data['name']) ?> - Übersicht</h2>
            <p>Alle Infos zu deinem Event auf einen Blick.</p>
        </section>

        <section class="features">

            <div class="overview-card">
                <h3>🎉 Unterhaltung</h3>
                <?php if (!empty($data['unterhaltung'])): ?>
                    <ul class="overview-list">
                        <?php foreach ($data['unterhaltung'] as $item): ?>
                            <li><?= htmlspecialchars($unterhaltungLabels[$item] ?? $item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="overview-empty">Keine Auswahl getroffen</p>
                <?php endif; ?>
            </div>

            <div class="overview-card">
                <h3>🪑 Mobiliar</h3>
                <?php if (!empty($data['mobilliar'])): ?>
                    <ul class="overview-list">
                        <?php foreach ($data['mobilliar'] as $item): ?>
                            <li><?= htmlspecialchars($mobilliarLabels[$item] ?? $item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="overview-empty">Keine Auswahl getroffen</p>
                <?php endif; ?>
            </div>

            <div class="overview-card">
                <h3>📄 Menü</h3>
                <?php if ($data['menue']): ?>
                    <p><?= htmlspecialchars($data['menue']) ?></p>
                <?php else: ?>
                    <p class="overview-empty">Kein Menü gewählt</p>
                <?php endif; ?>
            </div>

            <div class="overview-card">
                <h3>⚡ Energieversorgung</h3>
                <?php if (!empty($data['energie'])): ?>
                    <ul class="overview-list">
                        <?php foreach ($data['energie'] as $item): ?>
                            <li><?= htmlspecialchars($energieLabels[$item] ?? $item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="overview-empty">Keine Auswahl getroffen</p>
                <?php endif; ?>
            </div>

            <div class="overview-card overview-termin">
                <h3>📅 Termin</h3>
                <?php if ($data['termin_date'] && $data['termin_time']): ?>
                    <p>
                        <?= htmlspecialchars($data['termin_date']) ?> um <?= htmlspecialchars($data['termin_time']) ?>
                        <?php if ($data['termin_endtime']): ?>
                            – <?= htmlspecialchars($data['termin_endtime']) ?>
                        <?php endif; ?>
                    </p>
                <?php else: ?>
                    <p class="overview-empty">Kein Termin festgelegt</p>
                <?php endif; ?>

                <?php if ($data['termin_notes']): ?>
                    <div class="overview-notes">📝 <?= nl2br(htmlspecialchars($data['termin_notes'])) ?></div>
                <?php endif; ?>
            </div>

        </section>

        <div class="step-nav">
            <a href="../events/index.php" class="save-btn" style="text-decoration:none; display:inline-block;">✅ Fertig
                – zu Meine Events</a>
        </div>

    </main>

    <?php include '../layout/footer.php'; ?>

    <script src="../js/theme-toggle.js"></script>
</body>

</html>