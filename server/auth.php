<?php
/** Аутентификация, роли, избранное и защита от подделки запросов. */
declare(strict_types=1);

const ROLE_PHOTOGRAPHER = 'photographer';
const ROLE_CLIENT       = 'client';

function current_user(): ?array
{
    return $_SESSION['account'] ?? null;
}

function is_role(string $role): bool
{
    return (current_user()['role'] ?? null) === $role;
}

/** «Алексей Петров» → «Алексей П.» */
function short_name(string $name): string
{
    $parts = preg_split('/\s+/u', trim($name));
    if (count($parts) < 2) {
        return $name;
    }
    return $parts[0] . ' ' . mb_substr($parts[1], 0, 1) . '.';
}

function login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['account'] = ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'role' => $user['role']];
}

function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

/** Попытка входа. Возвращает пользователя или null (всегда одно общее сообщение). */
function attempt_login(string $email, string $password): ?array
{
    $user = db_query('SELECT id, name, email, role, password_hash FROM users WHERE email = ?',
        [mb_strtolower($email)])->fetch();
    // Проверяем хеш и для неизвестного адреса, чтобы не выдавать наличие аккаунта по времени ответа.
    $hash = $user['password_hash'] ?? '$2y$10$.0TgJ.UygSu6qE2JGSoxq.Tjm8TJdceCNzXR6Xi9BtyJlYNtNtv1C';
    $valid = password_verify($password, $hash);
    return $valid && isset($user['password_hash']) ? $user : null;
}

/* Ограничение неудачных попыток входа в текущей сессии. */
function login_locked(): int
{
    $until = (int) ($_SESSION['login_lock_until'] ?? 0);
    return max(0, $until - time());
}

function login_failed(): void
{
    $_SESSION['login_fails'] = (int) ($_SESSION['login_fails'] ?? 0) + 1;
    if ($_SESSION['login_fails'] >= LOGIN_MAX_ATTEMPTS) {
        $_SESSION['login_lock_until'] = time() + LOGIN_LOCK_SECONDS;
        $_SESSION['login_fails'] = 0;
    }
}

function login_reset_fails(): void
{
    unset($_SESSION['login_fails'], $_SESSION['login_lock_until']);
}

/* ── CSRF ── */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_check(): bool
{
    $sent = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf'] ?? '');
    return is_string($sent) && hash_equals(csrf_token(), $sent);
}

function favorites(): array
{
    if (!is_role(ROLE_CLIENT)) {
        return [];
    }
    static $ids;
    return $ids ??= array_map('intval', db_query(
        'SELECT photographer_id FROM favorites WHERE user_id = ? ORDER BY photographer_id',
        [current_user()['id']]
    )->fetchAll(PDO::FETCH_COLUMN));
}

function is_favorite(int $photographerId): bool
{
    return in_array($photographerId, favorites(), true);
}

function toggle_favorite(int $photographerId): bool
{
    $userId = current_user()['id'];
    $pdo = db();
    $pdo->beginTransaction();
    try {
        db_query('SELECT id FROM users WHERE id = ? FOR UPDATE', [$userId]);
        $deleted = db_query('DELETE FROM favorites WHERE user_id = ? AND photographer_id = ?',
            [$userId, $photographerId])->rowCount();
        if (!$deleted) {
            db_query('INSERT INTO favorites (user_id, photographer_id) VALUES (?, ?)', [$userId, $photographerId]);
        }
        $pdo->commit();
        return !$deleted;
    } catch (Throwable $error) {
        $pdo->rollBack();
        throw $error;
    }
}

/** Общая подготовка API-эндпоинта: только POST, CSRF, JSON-ответ. */
function api_guard(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        json_out(['ok' => false, 'error' => 'Метод не поддерживается'], 405);
    }
    if (!csrf_check()) {
        json_out(['ok' => false, 'error' => 'Сессия устарела. Обновите страницу и повторите.'], 403);
    }
}

/** Значение из POST как обрезанная строка. */
function post_str(string $key): string
{
    $v = $_POST[$key] ?? '';
    return is_string($v) ? trim($v) : '';
}
