<?php
// ============================================
// Authentifizierung: Registrierung + Login
// ============================================

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']) && (int) $_SESSION['user_id'] > 0;
}

function currentUserId(): ?int
{
    return isLoggedIn() ? (int) $_SESSION['user_id'] : null;
}

function currentUserEmail(): ?string
{
    return isset($_SESSION['user_email']) ? (string) $_SESSION['user_email'] : null;
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: /index.php');
        exit;
    }
}

/**
 * Prüft, ob ein Nutzer Admin-Rechte hat.
 */
function isAdmin(PDO $pdo, int $userId): bool
{
    $stmt = $pdo->prepare('SELECT is_admin FROM users WHERE id = :id');
    $stmt->execute(['id' => $userId]);
    $row = $stmt->fetch();

    return $row && (bool) $row['is_admin'];
}

/**
 * Erzwingt Login + Admin-Rechte. Nicht-Admins landen zurück auf "Meine Events".
 */
function requireAdmin(PDO $pdo): void
{
    requireLogin();

    if (!isAdmin($pdo, (int) currentUserId())) {
        header('Location: /events/index.php');
        exit;
    }
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function logoutUser(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}
