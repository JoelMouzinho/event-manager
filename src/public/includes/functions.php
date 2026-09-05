<?php
// ============================================
// Event-Funktionen (mehrere Events pro Nutzer)
// ============================================

/**
 * Legt ein neues Event für den Nutzer an und gibt die neue Event-ID zurück.
 */
function createEvent(PDO $pdo, int $userId, string $name): int
{
    $name = trim($name) !== '' ? trim($name) : 'Mein Event';

    $stmt = $pdo->prepare('INSERT INTO events (user_id, name) VALUES (:user_id, :name)');
    $stmt->execute(['user_id' => $userId, 'name' => $name]);

    return (int) $pdo->lastInsertId();
}

/**
 * Gibt alle Events eines Nutzers zurück (für die "Meine Events"-Liste).
 */
function getUserEvents(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare('SELECT * FROM events WHERE user_id = :user_id ORDER BY updated_at DESC');
    $stmt->execute(['user_id' => $userId]);
    return $stmt->fetchAll();
}

/**
 * Prüft, ob ein Event zu einem bestimmten Nutzer gehört (Zugriffsschutz).
 */
function eventBelongsToUser(PDO $pdo, int $eventId, int $userId): bool
{
    $stmt = $pdo->prepare('SELECT id FROM events WHERE id = :id AND user_id = :user_id');
    $stmt->execute(['id' => $eventId, 'user_id' => $userId]);
    return (bool) $stmt->fetch();
}

/**
 * Löscht ein Event (nur wenn es dem Nutzer gehört).
 */
function deleteEvent(PDO $pdo, int $eventId, int $userId): void
{
    $stmt = $pdo->prepare('DELETE FROM events WHERE id = :id AND user_id = :user_id');
    $stmt->execute(['id' => $eventId, 'user_id' => $userId]);
}

/**
 * Ermittelt die aktuell zu bearbeitende Event-ID:
 * - Falls ?event_id=... in der URL steht, wird geprüft, dass es dem Nutzer gehört,
 *   und in der Session als "aktuelles Event" gemerkt.
 * - Sonst wird das zuletzt in der Session gemerkte Event verwendet.
 * - Ist beides nicht vorhanden/ungültig, geht's zurück zu "Meine Events".
 *
 * So funktionieren die normalen Nav-Links im Header weiterhin ohne dass überall
 * ?event_id an die URL angehängt werden muss.
 */
function getCurrentEventId(PDO $pdo, int $userId): int
{
    if (isset($_GET['event_id'])) {
        $eventId = (int) $_GET['event_id'];
        if (!eventBelongsToUser($pdo, $eventId, $userId)) {
            header('Location: /events/index.php');
            exit;
        }
        $_SESSION['current_event_id'] = $eventId;
        return $eventId;
    }

    if (isset($_SESSION['current_event_id'])) {
        $eventId = (int) $_SESSION['current_event_id'];
        if (eventBelongsToUser($pdo, $eventId, $userId)) {
            return $eventId;
        }
    }

    header('Location: /events/index.php');
    exit;
}

/**
 * Lädt die Daten eines Events (Formularseiten nutzen das zum Vorausfüllen).
 */
function loadEventData(PDO $pdo, int $eventId): array
{
    $stmt = $pdo->prepare('SELECT * FROM events WHERE id = :id');
    $stmt->execute(['id' => $eventId]);
    $row = $stmt->fetch();

    if (!$row) {
        throw new RuntimeException('Event nicht gefunden.');
    }

    $row['unterhaltung'] = json_decode($row['unterhaltung'] ?? '[]', true) ?: [];
    $row['mobilliar']    = json_decode($row['mobilliar'] ?? '[]', true) ?: [];
    $row['energie']      = json_decode($row['energie'] ?? '[]', true) ?: [];

    return $row;
}

/**
 * Speichert ein oder mehrere Felder für ein Event.
 * Beispiel: saveEventData($pdo, $eventId, ['menue' => 'Menü 3']);
 * Beispiel mit mehreren Feldern (z.B. Termin-Seite):
 *   saveEventData($pdo, $eventId, [
 *       'termin_date' => '2026-09-10',
 *       'termin_time' => '18:00',
 *   ]);
 */
function saveEventData(PDO $pdo, int $eventId, array $fields): void
{
    $allowed = [
        'name', 'unterhaltung', 'mobilliar', 'menue', 'energie',
        'termin_date', 'termin_time', 'termin_endtime', 'termin_notes',
    ];

    $setParts = [];
    $params   = ['id' => $eventId];

    foreach ($fields as $key => $value) {
        if (!in_array($key, $allowed, true)) {
            continue;
        }
        if (in_array($key, ['unterhaltung', 'mobilliar', 'energie'], true)) {
            $value = json_encode($value);
        }
        $setParts[] = "$key = :$key";
        $params[$key] = $value;
    }

    if (empty($setParts)) {
        return;
    }

    $sql  = 'UPDATE events SET ' . implode(', ', $setParts) . ' WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
}

// ============================================
// Admin-Funktionen
// ============================================

/**
 * Kennzahlen für das Admin-Dashboard.
 */
function getAdminStats(PDO $pdo): array
{
    $userCount = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    $eventCount = (int) $pdo->query('SELECT COUNT(*) FROM events')->fetchColumn();
    $adminCount = (int) $pdo->query('SELECT COUNT(*) FROM users WHERE is_admin = 1')->fetchColumn();
    $usersWithEvents = (int) $pdo->query('SELECT COUNT(DISTINCT user_id) FROM events')->fetchColumn();
    $newestUser = $pdo->query('SELECT email, created_at FROM users ORDER BY created_at DESC LIMIT 1')->fetch();

    return [
        'userCount' => $userCount,
        'eventCount' => $eventCount,
        'adminCount' => $adminCount,
        'usersWithEvents' => $usersWithEvents,
        'newestUser' => $newestUser ?: null,
    ];
}

/**
 * Alle Nutzer mit Anzahl ihrer Events, für die Nutzerverwaltung.
 */
function getAllUsersWithEventCounts(PDO $pdo): array
{
    $sql = 'SELECT u.id, u.email, u.is_admin, u.created_at, COUNT(e.id) AS event_count
            FROM users u
            LEFT JOIN events e ON e.user_id = u.id
            GROUP BY u.id, u.email, u.is_admin, u.created_at
            ORDER BY u.created_at DESC';

    return $pdo->query($sql)->fetchAll();
}

/**
 * Alle Events aller Nutzer mit Besitzer-E-Mail, für die Event-Verwaltung.
 */
function getAllEventsWithOwner(PDO $pdo): array
{
    $sql = 'SELECT ev.id, ev.name, ev.termin_date, ev.created_at, u.email AS owner_email
            FROM events ev
            JOIN users u ON u.id = ev.user_id
            ORDER BY ev.created_at DESC';

    return $pdo->query($sql)->fetchAll();
}

/**
 * Setzt oder entfernt Admin-Rechte für einen Nutzer.
 */
function setUserAdminStatus(PDO $pdo, int $userId, bool $isAdmin): void
{
    $stmt = $pdo->prepare('UPDATE users SET is_admin = :is_admin WHERE id = :id');
    $stmt->execute(['is_admin' => $isAdmin ? 1 : 0, 'id' => $userId]);
}

/**
 * Löscht einen Nutzer (Admin-Aktion, ignoriert Besitzverhältnisse).
 * Events des Nutzers werden per ON DELETE CASCADE automatisch mitgelöscht.
 */
function deleteUserAsAdmin(PDO $pdo, int $userId): void
{
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
    $stmt->execute(['id' => $userId]);
}

/**
 * Löscht ein beliebiges Event (Admin-Aktion, ignoriert Besitzverhältnisse).
 */
function deleteEventAsAdmin(PDO $pdo, int $eventId): void
{
    $stmt = $pdo->prepare('DELETE FROM events WHERE id = :id');
    $stmt->execute(['id' => $eventId]);
}
