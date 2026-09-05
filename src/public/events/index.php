<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

$userId = currentUserId();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $error = 'Die Anfrage ist abgelaufen. Bitte versuche es erneut.';
    } elseif (($_POST['action'] ?? '') === 'create') {
        $eventId = createEvent($pdo, $userId, $_POST['event_name'] ?? '');
        $_SESSION['current_event_id'] = $eventId;
        header('Location: ../home/index.php');
        exit;
    } elseif (($_POST['action'] ?? '') === 'delete' && isset($_POST['event_id'])) {
        deleteEvent($pdo, (int) $_POST['event_id'], $userId);
        header('Location: index.php');
        exit;
    }
}

$events = getUserEvents($pdo, $userId);
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meine Events - Event-Manager</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="icon" type="image/x-icon" href="../assets/icon32x32nameless.png">
</head>

<body>

    <?php include '../layout/header.php'; ?>

    <main>
        <section class="intro">
            <h2>Meine Events</h2>
            <p>Wähle ein bestehendes Event oder lege ein neues an.</p>
        </section>

        <?php if ($error !== ''): ?>
            <div class="error-message" style="max-width:400px; margin:0 auto 20px;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="index.php" class="auth-form" style="max-width:400px; margin:0 auto 40px;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
            <input type="hidden" name="action" value="create">

            <label for="event_name">Neues Event:</label>
            <input type="text" id="event_name" name="event_name" placeholder="z.B. Geburtstag Lisa">

            <button type="submit" class="save-btn">➕ Event anlegen</button>
        </form>

        <section class="features">
            <?php if (empty($events)): ?>
                <p style="text-align:center; color:#999; grid-column:1/-1;">Du hast noch keine Events angelegt.</p>
            <?php else: ?>
                <?php foreach ($events as $event): ?>
                    <div class="feature event-card">
                        <a href="../home/index.php?event_id=<?= (int) $event['id'] ?>" class="event-card-link">
                            <h3><?= htmlspecialchars($event['name']) ?></h3>
                            <p>
                                <?php if ($event['termin_date']): ?>
                                    📅 <?= htmlspecialchars($event['termin_date']) ?>
                                <?php else: ?>
                                    Noch kein Termin
                                <?php endif; ?>
                            </p>
                        </a>
                        <form method="post" action="index.php" class="event-delete-form" onsubmit="return confirm('Dieses Event wirklich löschen?');">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="event_id" value="<?= (int) $event['id'] ?>">
                            <button type="submit" class="event-delete-btn">Löschen</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>

    <?php include '../layout/footer.php'; ?>

    <script src="../js/theme-toggle.js"></script>
</body>

</html>
