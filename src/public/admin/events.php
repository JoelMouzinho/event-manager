<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin($pdo);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $error = 'Die Anfrage ist abgelaufen. Bitte versuche es erneut.';
    } elseif (($_POST['action'] ?? '') === 'delete' && isset($_POST['event_id'])) {
        deleteEventAsAdmin($pdo, (int) $_POST['event_id']);
    }
}

$events = getAllEventsWithOwner($pdo);
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Party-Verwaltung - Admin</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>

    <?php include '../layout/header.php'; ?>

    <main>
        <section class="intro">
            <h2>Event-Verwaltung</h2>
            <p><a href="index.php" class="back-link">← Zurück zum Dashboard</a></p>
        </section>

        <?php if ($error !== ''): ?>
            <div class="error-message" style="max-width:500px; margin:0 auto 20px;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Besitzer</th>
                        <th>Termin</th>
                        <th>Status</th>
                        <th>Angelegt am</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($events)): ?>
                        <tr>
                            <td colspan="6" class="overview-empty" style="text-align:center;">Es existieren noch keine
                                Events.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td><a href="event-detail.php?event_id=<?= (int) $event['id'] ?>"
                                        style="color:inherit; font-weight:bold;"><?= htmlspecialchars($event['name']) ?></a>
                                </td>
                                <td><?= htmlspecialchars($event['owner_email']) ?></td>
                                <td><?= $event['termin_date'] ? htmlspecialchars($event['termin_date']) : '—' ?></td>
                                <td>
                                    <?php if ($event['rejected_at']): ?>
                                        <span class="status-badge rejected">Abgelehnt</span>
                                    <?php else: ?>
                                        <span class="overview-empty">Aktiv</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($event['created_at']) ?></td>
                                <td class="admin-actions">
                                    <a href="event-detail.php?event_id=<?= (int) $event['id'] ?>"
                                        class="admin-link-btn">Details</a>
                                    <form method="post" action="events.php"
                                        onsubmit="return confirm('Dieses Event wirklich löschen?');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="event_id" value="<?= (int) $event['id'] ?>">
                                        <button type="submit" class="event-delete-btn">Löschen</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php include '../layout/footer.php'; ?>

    <script src="../js/theme-toggle.js"></script>
</body>

</html>