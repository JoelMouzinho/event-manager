<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

$userId = currentUserId();
$eventId = getCurrentEventId($pdo, $userId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    saveEventData($pdo, $eventId, [
        'termin_date'    => $_POST['date'] ?? '',
        'termin_time'    => $_POST['time'] ?? '',
        'termin_endtime' => $_POST['endTime'] ?? '',
        'termin_notes'   => trim($_POST['notes'] ?? ''),
    ]);
    header('Location: index.php?saved=1');
    exit;
}

$data = loadEventData($pdo, $eventId);
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>

    <?php include '../layout/header.php'; ?>

    <main>
        <section class="intro">
            <h2>Termin festlegen</h2>
            <p>Event: <strong><?= htmlspecialchars($data['name']) ?></strong></p>
        </section>

        <?php if (isset($_GET['saved'])): ?>
            <p class="success" style="text-align:center; margin-bottom:20px;">✅ Gespeichert!</p>
        <?php endif; ?>

        <form method="post" action="index.php">
            <div class="form-row">
                <div class="form-group">
                    <label for="date">Datum:</label>
                    <input type="date" id="date" class="date-input" name="date"
                        value="<?= htmlspecialchars($data['termin_date']) ?>">
                </div>

                <div class="form-group">
                    <label for="time">Startzeit:</label>
                    <input type="time" id="time" class="time-input" name="time"
                        value="<?= htmlspecialchars($data['termin_time']) ?>">
                </div>

                <div class="form-group">
                    <label for="endTime">Endzeit:</label>
                    <input type="time" id="endTime" class="time-input" name="endTime"
                        value="<?= htmlspecialchars($data['termin_endtime']) ?>">
                </div>
            </div>

            <label for="notes">Notizen:</label>
            <textarea id="notes" class="note-input" name="notes" rows="4"><?= htmlspecialchars($data['termin_notes']) ?></textarea>

            <div style="text-align:center; margin-top:30px;">
                <button type="submit" class="save-btn">Speichern</button>
                <a href="../uebersicht/index.php" class="next-link">Weiter zu Übersicht →</a>
            </div>
        </form>
    </main>

    <?php include '../layout/footer.php'; ?>

    <script src="../js/theme-toggle.js"></script>
</body>

</html>