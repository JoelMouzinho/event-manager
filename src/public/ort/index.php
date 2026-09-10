<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

$userId = currentUserId();
$eventId = getCurrentEventId($pdo, $userId);

if (!$eventId) {
    header('Location: ../home/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (verifyCsrfToken($_POST['csrf_token'] ?? null)) {

        $selectedOrt = $_POST['ort'] ?? '';

        $allowedOrte = [
            'zuhause',
            'veranstaltungsraum',
            'restaurant',
            'draussen',
            'hotel',
            'anderer_ort'
        ];

        if (in_array($selectedOrt, $allowedOrte, true)) {

            saveEventData($pdo, $eventId, [
                'ort' => $selectedOrt
            ]);

            header('Location: index.php?saved=1');
            exit;
        }
    }
}

$data = loadEventData($pdo, $eventId);
$selectedOrt = $data['ort'] ?? '';

function isChecked(string $selected, string $value): string
{
    return $selected === $value ? 'checked' : '';
}
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
            <h2>Ort auswählen</h2>
            <p>Event: <strong><?= htmlspecialchars($data['name']) ?></strong></p>
        </section>

        <?php if (isset($_GET['saved'])): ?>
            <p class="success" style="text-align:center; margin-bottom:20px;">
                ✅ Gespeichert!
            </p>
        <?php endif; ?>

        <form method="post" action="index.php">

            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">

            <section class="features">

                <label class="feature">
                    <input type="radio" name="ort" value="zuhause" <?= isChecked($selectedOrt, 'zuhause') ?>>

                    <h3>🏠 Zuhause</h3>
                    <p>Das Event findet bei dir oder an einer privaten Adresse statt.</p>
                </label>

                <label class="feature">
                    <input type="radio" name="ort" value="veranstaltungsraum" <?= isChecked($selectedOrt, 'veranstaltungsraum') ?>>

                    <h3>🏢 Veranstaltungsraum</h3>
                    <p>Ein gemieteter Raum oder eine spezielle Eventlocation.</p>
                </label>

                <label class="feature">
                    <input type="radio" name="ort" value="restaurant" <?= isChecked($selectedOrt, 'restaurant') ?>>

                    <h3>🍽️ Restaurant</h3>
                    <p>Das Event findet in einem Restaurant statt.</p>
                </label>

                <label class="feature">
                    <input type="radio" name="ort" value="draussen" <?= isChecked($selectedOrt, 'draussen') ?>>

                    <h3>🌳 Draußen</h3>
                    <p>Zum Beispiel im Park, Garten oder auf einer Wiese.</p>
                </label>

                <label class="feature">
                    <input type="radio" name="ort" value="hotel" <?= isChecked($selectedOrt, 'hotel') ?>>

                    <h3>🏨 Hotel</h3>
                    <p>Das Event findet in einem Hotel statt.</p>
                </label>

                <label class="feature">
                    <input type="radio" name="ort" value="anderer_ort" <?= isChecked($selectedOrt, 'anderer_ort') ?>>

                    <h3>📍 Anderer Ort</h3>
                    <p>Der Veranstaltungsort passt zu keiner der anderen Kategorien.</p>
                </label>

            </section>

            <div class="step-nav">

                <button type="submit" class="save-btn">
                    Speichern
                </button>

                <a href="../unterhaltung/index.php" class="next-link">
                    Weiter zu Unterhaltung →
                </a>

            </div>

        </form>

    </main>

    <?php include '../layout/footer.php'; ?>

    <script src="../js/theme-toggle.js"></script>

</body>

</html>
