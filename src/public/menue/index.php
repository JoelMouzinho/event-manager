<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

$userId = currentUserId();
$eventId = getCurrentEventId($pdo, $userId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $menue = trim($_POST['menue'] ?? '');
    saveEventData($pdo, $eventId, ['menue' => $menue]);
    header('Location: index.php?saved=1');
    exit;
}

$data = loadEventData($pdo, $eventId);
$menue = $data['menue'];
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menü - Event-Manager</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>

    <?php include '../layout/header.php'; ?>

    <main>
        <section class="intro">
            <h2>Menü erstellen</h2>
            <p>Event: <strong><?= htmlspecialchars($data['name']) ?></strong></p>
        </section>

        <?php if (isset($_GET['saved'])): ?>
            <p class="success" style="text-align:center; margin-bottom:20px;">✅ Gespeichert!</p>
        <?php endif; ?>

        <form method="post" action="index.php">
            <label for="menue-input">Menü:</label>
            <input type="text" id="menue-input" class="menu-input" name="menue"
                value="<?= htmlspecialchars($menue) ?>" placeholder="z.B. Menü 3">

            <div style="text-align:center; margin-top:30px;">
                <button type="submit" class="save-btn">Speichern</button>
                <a href="../energieversorgung/index.php" class="next-link">Weiter zu Energieversorgung →</a>
            </div>
        </form>
    </main>

    <?php include '../layout/footer.php'; ?>

    <script src="../js/theme-toggle.js"></script>
</body>

</html>