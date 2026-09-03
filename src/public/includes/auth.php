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

/**
 * Verknüpft das aktuelle Event mit einem Benutzer.
 * Ein bereits einem anderen Benutzer gehörendes Event wird niemals übernommen.
 */
function claimCurrentEvent(PDO $pdo, int $userId, string $sessionId): void
{
    $stmt = $pdo->prepare(
        'UPDATE events
         SET user_id = :user_id
         WHERE session_id = :session_id
           AND (user_id IS NULL OR user_id = :user_id)'
    );
    $stmt->execute([
        'user_id' => $userId,
        'session_id' => $sessionId,
    ]);
}

/**
 * Beim Login: Falls die aktuelle Browser-Session noch ein anonymes Event enthält,
 * wird dieses Event dem eingeloggten Benutzer zugeordnet.
 * Falls dort noch kein Event existiert, wird das zuletzt gespeicherte Event des
 * Benutzers als aktuelles Event in der Browser-Session verwendet.
 */
function restoreUserEventSession(PDO $pdo, int $userId): void
{
    $sessionId = $_SESSION['event_session_id'] ?? null;

    if ($sessionId !== null) {
        $stmt = $pdo->prepare('SELECT id, user_id FROM events WHERE session_id = :session_id LIMIT 1');
        $stmt->execute(['session_id' => $sessionId]);
        $currentEvent = $stmt->fetch();

        if ($currentEvent && ($currentEvent['user_id'] === null || (int) $currentEvent['user_id'] === $userId)) {
            claimCurrentEvent($pdo, $userId, $sessionId);
            return;
        }
    }

    $stmt = $pdo->prepare(
        'SELECT session_id
         FROM events
         WHERE user_id = :user_id
         ORDER BY updated_at DESC, id DESC
         LIMIT 1'
    );
    $stmt->execute(['user_id' => $userId]);
    $latestEvent = $stmt->fetch();

    if ($latestEvent) {
        $_SESSION['event_session_id'] = $latestEvent['session_id'];
    } else {
        unset($_SESSION['event_session_id']);
    }
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
