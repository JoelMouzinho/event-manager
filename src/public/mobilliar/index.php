<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

$userId = currentUserId();
$eventId = getCurrentEventId($pdo, $userId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected = $_POST['mobilliar'] ?? [];
    saveEventData($pdo, $eventId, ['mobilliar' => $selected]);
    header('Location: index.php?saved=1');
    exit;
}

$data = loadEventData($pdo, $eventId);
$selectedMobilliar = $data['mobilliar'];

function isChecked(array $selected, string $value): string
{
    return in_array($value, $selected, true) ? 'checked' : '';
}
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
            <h2>Mobiliar auswählen</h2>
            <p>Event: <strong><?= htmlspecialchars($data['name']) ?></strong></p>
        </section>

        <?php if (isset($_GET['saved'])): ?>
            <p class="success" style="text-align:center; margin-bottom:20px;">✅ Gespeichert!</p>
        <?php endif; ?>

        <form method="post" action="index.php">
            <section class="features">

                <label class="feature">
                    <input type="checkbox" name="mobilliar[]" value="stuehle" <?= isChecked($selectedMobilliar, 'stuehle') ?>>
                    <h3>Stühle</h3>
                    <p>Sitzgelegenheiten für deine Gäste.</p>
                </label>

                <label class="feature">
                    <input type="checkbox" name="mobilliar[]" value="tische" <?= isChecked($selectedMobilliar, 'tische') ?>>
                    <h3>Tische</h3>
                    <p>Tische für Essen, Getränke und Deko.</p>
                </label>

                <label class="feature">
                    <input type="checkbox" name="mobilliar[]" value="grill" <?= isChecked($selectedMobilliar, 'grill') ?>>
                    <h3>Grill</h3>
                    <p>Grill für Speisen direkt vor Ort.</p>
                </label>

                <label class="feature">
                    <input type="checkbox" name="mobilliar[]" value="bar" <?= isChecked($selectedMobilliar, 'bar') ?>>
                    <h3>Bar</h3>
                    <p>Bar-Theke für Getränke und Ausschank.</p>
                </label>

            </section>

            <div style="text-align:center; margin-top:30px;">
                <button type="submit" class="save-btn">Speichern</button>
                <a href="../menue/index.php" class="next-link">Weiter zu Menü →</a>
            </div>
        </form>
    </main>

    <?php include '../layout/footer.php'; ?>

    <script src="../js/theme-toggle.js"></script>
</body>

</html>