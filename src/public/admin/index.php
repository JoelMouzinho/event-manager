<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin($pdo);

$stats = getAdminStats($pdo);
$earliestEvent = getEarliestEvent($pdo);
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin-Dashboard - Event-Manager</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="icon" type="image/x-icon" href="../assets/icon32x32nameless.png">
</head>

<body>

    <?php include '../layout/header.php'; ?>

    <main>
        <section class="intro">
            <h2>Admin-Dashboard</h2>
            <p>Übersicht über alle Nutzer und Events.</p>
        </section>

        <section class="features">
            <div class="overview-card">
                <h3>👥 Nutzer</h3>
                <p style="font-size:32px; font-weight:bold; color:var(--primary-color);"><?= $stats['userCount'] ?></p>
            </div>

            <div class="overview-card">
                <h3>🎉 Events</h3>
                <p style="font-size:32px; font-weight:bold; color:var(--primary-color);"><?= $stats['eventCount'] ?></p>
            </div>

            <div class="overview-card">
                <h3>🛡️ Admins</h3>
                <p style="font-size:32px; font-weight:bold; color:var(--primary-color);"><?= $stats['adminCount'] ?></p>
            </div>

            <div class="overview-card">
                <h3>✅ Aktive Planer</h3>
                <p style="font-size:32px; font-weight:bold; color:var(--primary-color);">
                    <?= $stats['usersWithEvents'] ?>
                </p>
                <p class="overview-empty">Nutzer mit mind. 1 Event</p>
            </div>

            <?php if ($stats['newestUser']): ?>
                <div class="overview-card">
                    <h3>🆕 Neuster Nutzer</h3>
                    <p><?= htmlspecialchars($stats['newestUser']['email']) ?></p>
                    <p class="overview-empty"><?= htmlspecialchars($stats['newestUser']['created_at']) ?></p>
                </div>
            <?php endif; ?>

            <?php if ($earliestEvent): ?>
                <a href="event-detail.php?event_id=<?= (int) $earliestEvent['id'] ?>" class="feature overview-card"
                    style="text-align:left;">
                    <h3>⏰ Frühestes Event</h3>
                    <p style="font-weight:bold;"><?= htmlspecialchars($earliestEvent['name']) ?></p>
                    <p>
                        <?= htmlspecialchars($earliestEvent['termin_date']) ?>
                        <?php if ($earliestEvent['termin_time']): ?>
                            um <?= htmlspecialchars($earliestEvent['termin_time']) ?>
                        <?php endif; ?>
                    </p>
                    <p class="overview-empty"><?= htmlspecialchars($earliestEvent['owner_email']) ?></p>
                </a>
            <?php else: ?>
                <div class="overview-card">
                    <h3>⏰ Frühestes Event</h3>
                    <p class="overview-empty">Noch kein Event mit Termin vorhanden.</p>
                </div>
            <?php endif; ?>
        </section>

        <div class="admin-nav">
            <a href="users.php" class="save-btn" style="text-decoration:none; display:inline-block;">👥 Nutzer
                verwalten</a>
            <a href="events.php" class="save-btn" style="text-decoration:none; display:inline-block;">🎉 Events
                verwalten</a>
        </div>
    </main>

    <?php include '../layout/footer.php'; ?>

    <script src="../js/theme-toggle.js"></script>
</body>

</html>