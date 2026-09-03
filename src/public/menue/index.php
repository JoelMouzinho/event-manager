<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

$sessionId = getOrCreateSessionId();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $menue = trim($_POST['menue'] ?? '');
    saveEventData($pdo, $sessionId, ['menue' => $menue]);
    header('Location: index.php?saved=1');
    exit;
}

$data = loadEventData($pdo, $sessionId);
$menue = $data['menue'];
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
            <h2>Menü erstellen</h2>
            <p>Trage dein individuelles Menü für die Gäste ein.</p>
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
            </div>
        </form>
    </main>

    <?php include '../layout/footer.php'; ?>

    <script src="../js/theme-toggle.js"></script>
</body>

</html>