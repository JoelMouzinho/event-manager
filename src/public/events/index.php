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
    } elseif (($_POST['action'] ?? '') === 'update_profile') {
        updateUserProfile(
            $pdo,
            $userId,
            $_POST['first_name'] ?? '',
            $_POST['last_name'] ?? ''
        );

        header('Location: index.php');
        exit;
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
$account = getUserAccountInfo($pdo, $userId);
$dashboardStats = getUserDashboardStats($pdo, $userId);
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mein Dashboard - Event-Manager</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>

    <?php include '../layout/header.php'; ?>

    <main>
        <section class="intro">
            <h2>Mein Dashboard</h2>
            <p>Übersicht über deine Events und dein Konto.</p>
        </section>

        <?php if ($error !== ''): ?>
            <div class="error-message" style="max-width:400px; margin:0 auto 20px;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <section class="features">
            <div class="overview-card">
                <h3>🎉 Meine Events</h3>
                <p style="font-size:32px; font-weight:bold; color:var(--primary-color);">
                    <?= $dashboardStats['eventCount'] ?>
                </p>
            </div>

            <div class="overview-card">
                <h3>🚫 Abgelehnt</h3>
                <p style="font-size:32px; font-weight:bold; color:var(--primary-color);">
                    <?= $dashboardStats['rejectedCount'] ?>
                </p>
            </div>

            <?php if ($dashboardStats['nextEvent']): ?>
                <a href="../home/index.php?event_id=<?= (int) $dashboardStats['nextEvent']['id'] ?>"
                    class="feature overview-card" style="text-align:left;">
                    <h3>⏰ Nächster Termin</h3>
                    <p style="font-weight:bold;"><?= htmlspecialchars($dashboardStats['nextEvent']['name']) ?></p>
                    <p>
                        <?= htmlspecialchars($dashboardStats['nextEvent']['termin_date']) ?>
                        <?php if ($dashboardStats['nextEvent']['termin_time']): ?>
                            um <?= htmlspecialchars($dashboardStats['nextEvent']['termin_time']) ?>
                        <?php endif; ?>
                    </p>
                </a>
            <?php else: ?>
                <div class="overview-card">
                    <h3>⏰ Nächster Termin</h3>
                    <p class="overview-empty">Kein anstehender Termin.</p>
                </div>
            <?php endif; ?>

            <?php if ($account): ?>
                <div class="overview-card">
                    <h3>👤 Mein Konto</h3>

                    <?php if (!empty($account['first_name']) || !empty($account['last_name'])): ?>
                        <p>
                            <?= htmlspecialchars(trim(($account['first_name'] ?? '') . ' ' . ($account['last_name'] ?? ''))) ?>
                        </p>
                    <?php endif; ?>

                    <p class="overview-empty">
                        <?= htmlspecialchars($account['email']) ?>
                    </p>

                    <button type="button" class="profile-edit-btn" id="profile-edit-toggle">
                        Profil bearbeiten
                    </button>

                    <div class="profile-modal" id="profile-modal" aria-hidden="true">
                        <div class="profile-modal-backdrop" id="profile-modal-backdrop"></div>

                        <div class="profile-modal-content" role="dialog" aria-modal="true"
                            aria-labelledby="profile-modal-title">
                            <button type="button" class="profile-modal-close" id="profile-modal-close"
                                aria-label="Modal schließen">
                                &times;
                            </button>

                            <h2 id="profile-modal-title">Profil bearbeiten</h2>
                            <p class="profile-modal-subtitle">
                                Aktualisiere deinen Vor- und Nachnamen.
                            </p>

                            <form method="post" action="index.php" class="profile-form">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                                <input type="hidden" name="action" value="update_profile">

                                <label for="first_name">Vorname</label>
                                <input type="text" id="first_name" name="first_name"
                                    value="<?= htmlspecialchars($account['first_name'] ?? '') ?>" placeholder="Vorname"
                                    maxlength="100">

                                <label for="last_name">Nachname</label>
                                <input type="text" id="last_name" name="last_name"
                                    value="<?= htmlspecialchars($account['last_name'] ?? '') ?>" placeholder="Nachname"
                                    maxlength="100">

                                <div class="profile-form-actions">
                                    <button type="button" class="profile-cancel-btn" id="profile-modal-cancel">
                                        Abbrechen
                                    </button>

                                    <button type="submit" class="save-btn">
                                        Speichern
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
        </section>

        <section class="intro" style="margin-top:50px;">
            <h2 style="font-size:1.4rem;">Alle meine Events</h2>
        </section>

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
                            <?php if ($event['rejected_at']): ?>
                                <p style="margin-top:8px;"><span class="status-badge rejected">Abgelehnt</span></p>
                            <?php endif; ?>
                        </a>
                        <form method="post" action="index.php" class="event-delete-form"
                            onsubmit="return confirm('Dieses Event wirklich löschen?');">
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
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggle = document.getElementById('profile-edit-toggle');
            const modal = document.getElementById('profile-modal');
            const close = document.getElementById('profile-modal-close');
            const cancel = document.getElementById('profile-modal-cancel');
            const backdrop = document.getElementById('profile-modal-backdrop');

            if (!toggle || !modal) {
            return;
        }

            function openModal() {
                modal.classList.add('open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('modal-open');
        }

            function closeModal() {
                modal.classList.remove('open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('modal-open');
        }

            toggle.addEventListener('click', openModal);
            close?.addEventListener('click', closeModal);
            cancel?.addEventListener('click', closeModal);
            backdrop?.addEventListener('click', closeModal);

        // Modal mit Escape schließen
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && modal.classList.contains('open')) {
                closeModal();
            }
        });
    });
    </script>
</body>

</html>