<?php
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin($pdo);

$currentUserId = currentUserId();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $error = 'Die Anfrage ist abgelaufen. Bitte versuche es erneut.';
    } else {
        $targetId = (int) ($_POST['user_id'] ?? 0);
        $action = $_POST['action'] ?? '';

        if ($action === 'toggle_admin') {
            if ($targetId === $currentUserId) {
                $error = 'Du kannst dir selbst nicht die Admin-Rechte entziehen.';
            } else {
                $makeAdmin = ($_POST['make_admin'] ?? '0') === '1';
                setUserAdminStatus($pdo, $targetId, $makeAdmin);
            }
        } elseif ($action === 'delete') {
            if ($targetId === $currentUserId) {
                $error = 'Du kannst dein eigenes Konto hier nicht löschen.';
            } else {
                deleteUserAsAdmin($pdo, $targetId);
            }
        }
    }
}

$users = getAllUsersWithEventCounts($pdo);
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutzerverwaltung - Admin</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="icon" type="image/x-icon" href="../assets/icon32x32nameless.png">
</head>

<body>

    <?php include '../layout/header.php'; ?>

    <main>
        <section class="intro">
            <h2>Nutzerverwaltung</h2>
            <p><a href="index.php" class="back-link">← Zurück zum Dashboard</a></p>
        </section>

        <?php if ($error !== ''): ?>
            <div class="error-message" style="max-width:500px; margin:0 auto 20px;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>E-Mail</th>
                        <th>Events</th>
                        <th>Registriert am</th>
                        <th>Rolle</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= (int) $user['event_count'] ?></td>
                            <td><?= htmlspecialchars($user['created_at']) ?></td>
                            <td>
                                <?php if ($user['is_admin']): ?>
                                    <span class="admin-badge">Admin</span>
                                <?php else: ?>
                                    <span class="overview-empty">Nutzer</span>
                                <?php endif; ?>
                            </td>
                            <td class="admin-actions">
                                <?php if ((int) $user['id'] !== $currentUserId): ?>
                                    <form method="post" action="users.php">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                                        <input type="hidden" name="action" value="toggle_admin">
                                        <input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>">
                                        <input type="hidden" name="make_admin" value="<?= $user['is_admin'] ? '0' : '1' ?>">
                                        <button type="submit" class="admin-link-btn">
                                            <?= $user['is_admin'] ? 'Admin entziehen' : 'Zu Admin machen' ?>
                                        </button>
                                    </form>
                                    <form method="post" action="users.php" onsubmit="return confirm('Diesen Nutzer inkl. all seiner Events wirklich löschen?');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>">
                                        <button type="submit" class="event-delete-btn">Löschen</button>
                                    </form>
                                <?php else: ?>
                                    <span class="overview-empty">(du)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php include '../layout/footer.php'; ?>

    <script src="../js/theme-toggle.js"></script>
</body>

</html>
