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

    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $error = 'Die Anfrage ist abgelaufen. Bitte versuche es erneut.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Bitte gib eine gültige E-Mail-Adresse und dein Passwort ein.';
    } else {
        $stmt = $pdo->prepare('SELECT id, email, password_hash FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $error = 'E-Mail-Adresse oder Passwort ist falsch.';
        } else {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['user_email'] = $user['email'];

            header('Location: ../events/index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Party-Organizer</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<?php include '../layout/header.php'; ?>

<main>
    <section class="auth-card">
        <div class="intro">
            <h2>Einloggen</h2>
            <p>Melde dich an, um deine Eventplanung wieder aufzurufen.</p>
        </div>

        <?php if ($error !== ''): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="index.php" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">

            <label for="email">E-Mail-Adresse</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" autocomplete="email" required>

            <label for="password">Passwort</label>
            <input type="password" id="password" name="password" autocomplete="current-password" required>

            <button type="submit" class="save-btn">Einloggen</button>
        </form>

        <p class="auth-switch">Noch kein Konto? <a href="../register/index.php">Jetzt registrieren</a></p>
    </section>
</main>

<?php include '../layout/footer.php'; ?>
<script src="../js/theme-toggle.js"></script>
</body>
</html>
