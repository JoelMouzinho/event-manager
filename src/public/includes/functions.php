<?php
// ============================================
// Hilfsfunktionen: Session + Speichern/Laden
// ============================================

/**
 * Holt die Session-ID des aktuellen Besuchers oder legt eine neue an.
 * Ersetzt die "pro Browser"-Logik, die vorher localStorage übernommen hat.
 */
function getOrCreateSessionId(): string
{
    if (!isset($_SESSION['event_session_id'])) {
        $_SESSION['event_session_id'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['event_session_id'];
}

/**
 * Lädt den Event-Datensatz für die aktuelle Session.
 * Legt automatisch einen leeren Datensatz an, falls noch keiner existiert.
 */
function loadEventData(PDO $pdo, string $sessionId): array
{
    $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;

    if ($userId !== null) {
        $stmt = $pdo->prepare(
            'SELECT * FROM events
             WHERE session_id = :sid
               AND (user_id = :uid OR user_id IS NULL)'
        );
        $stmt->execute(['sid' => $sessionId, 'uid' => $userId]);
    } else {
        $stmt = $pdo->prepare('SELECT * FROM events WHERE session_id = :sid AND user_id IS NULL');
        $stmt->execute(['sid' => $sessionId]);
    }
    $row = $stmt->fetch();

    if (!$row) {
        $insert = $pdo->prepare('INSERT INTO events (session_id, user_id) VALUES (:sid, :uid)');
        $insert->execute(['sid' => $sessionId, 'uid' => $userId]);

        return [
            'unterhaltung'   => [],
            'mobilliar'      => [],
            'menue'          => '',
            'energie'        => [],
            'termin_date'    => '',
            'termin_time'    => '',
            'termin_endtime' => '',
            'termin_notes'   => '',
        ];
    }

    $row['unterhaltung'] = json_decode($row['unterhaltung'] ?? '[]', true) ?: [];
    $row['mobilliar']    = json_decode($row['mobilliar'] ?? '[]', true) ?: [];
    $row['energie']      = json_decode($row['energie'] ?? '[]', true) ?: [];

    return $row;
}

/**
 * Speichert ein oder mehrere Felder für die aktuelle Session.
 * Beispiel: saveEventData($pdo, $sessionId, ['menue' => 'Menü 3']);
 * Beispiel mit mehreren Feldern (z.B. Termin-Seite):
 *   saveEventData($pdo, $sessionId, [
 *       'termin_date' => '2026-09-10',
 *       'termin_time' => '18:00',
 *   ]);
 */
function saveEventData(PDO $pdo, string $sessionId, array $fields): void
{
    $allowed = [
        'unterhaltung', 'mobilliar', 'menue', 'energie',
        'termin_date', 'termin_time', 'termin_endtime', 'termin_notes',
    ];

    $setParts = [];
    $params   = ['sid' => $sessionId];

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

    if (isset($_SESSION['user_id'])) {
        $sql = 'UPDATE events SET ' . implode(', ', $setParts)
             . ' WHERE session_id = :sid AND (user_id = :uid OR user_id IS NULL)';
        $params['uid'] = (int) $_SESSION['user_id'];
    } else {
        $sql = 'UPDATE events SET ' . implode(', ', $setParts)
             . ' WHERE session_id = :sid AND user_id IS NULL';
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Falls ein eingeloggter Benutzer noch ein anonymes Event besitzt, wird
    // dieses nach dem ersten Speichern automatisch seinem Konto zugeordnet.
    if (isset($_SESSION['user_id'])) {
        $claim = $pdo->prepare(
            'UPDATE events SET user_id = :uid
             WHERE session_id = :sid AND user_id IS NULL'
        );
        $claim->execute([
            'uid' => (int) $_SESSION['user_id'],
            'sid' => $sessionId,
        ]);
    }
}
