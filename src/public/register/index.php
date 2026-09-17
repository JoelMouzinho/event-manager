<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn()) {
    header('Location: ../events/index.php');
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $error = 'Die Anfrage ist abgelaufen. Bitte versuche es erneut.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Bitte gib eine gültige E-Mail-Adresse ein.';
    } elseif (strlen($password) < 8) {
        $error = 'Das Passwort muss mindestens 8 Zeichen lang sein.';
    } elseif ($password !== $passwordConfirm) {
        $error = 'Die Passwörter stimmen nicht überein.';
    } else {
        $check = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $check->execute(['email' => $email]);

        if ($check->fetch()) {
            $error = 'Für diese E-Mail-Adresse existiert bereits ein Konto.';
        } else {
            try {
                $pdo->beginTransaction();

                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $insert = $pdo->prepare(
                    'INSERT INTO users (email, password_hash) VALUES (:email, :password_hash)'
                );
                $insert->execute([
                    'email' => $email,
                    'password_hash' => $passwordHash,
                ]);

                $userId = (int) $pdo->lastInsertId();
                $pdo->commit();

                session_regenerate_id(true);
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_email'] = $email;

                header('Location: ../events/index.php');
                exit;
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                $error = 'Das Konto konnte nicht erstellt werden. Bitte versuche es erneut.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrieren - Party-Organizer</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<?php include '../layout/header.php'; ?>

<main>
    <section class="auth-card">
        <div class="intro">
            <h2>Konto erstellen</h2>
            <p>Registriere dich, damit deine Eventplanung deinem Benutzerkonto zugeordnet wird.</p>
        </div>

        <?php if ($error !== ''): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="index.php" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">

            <label for="email">E-Mail-Adresse</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" autocomplete="email" required>

            <label for="password">Passwort</label>
            <input type="password" id="password" name="password" minlength="8" autocomplete="new-password" required>

            <label for="password_confirm">Passwort wiederholen</label>
            <input type="password" id="password_confirm" name="password_confirm" minlength="8" autocomplete="new-password" required>

            <button type="submit" class="save-btn">Registrieren</button>
        </form>

        <p class="auth-switch">Du hast bereits ein Konto? <a href="../login/index.php">Jetzt einloggen</a></p>
    </section>
</main>

<?php include '../layout/footer.php'; ?>
<script src="../js/theme-toggle.js"></script>
</body>
</html>
