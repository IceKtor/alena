<?php
/**
 * Аутентификация, роли и защита от подделки запросов.
 *
 * ЗАГЛУШКА: пока нет таблицы users, вход работает только для двух демо-аккаунтов
 * (пароль demo12345), а регистрация лишь проверяет данные и «входит» в сессию,
 * ничего не сохраняя. Роли и проверки прав уже настоящие и работают на сервере (ТЗ 8.5).
 *
 * TODO(DB): demo_users() → SELECT по таблице users; пароли — только password_hash/password_verify.
 */
declare(strict_types=1);

const ROLE_PHOTOGRAPHER = 'photographer';
const ROLE_CLIENT       = 'client';

/** TODO(DB): удалить, когда появится таблица users. */
function demo_users(): array
{
    return [
        ['id' => 1,   'name' => 'Алексей Петров', 'email' => 'foto@demo.ru',   'role' => ROLE_PHOTOGRAPHER, 'hash' => DEMO_PASSWORD_HASH],
        ['id' => 101, 'name' => 'Мария Заказчик', 'email' => 'client@demo.ru', 'role' => ROLE_CLIENT,       'hash' => DEMO_PASSWORD_HASH],
    ];
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
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
    $_SESSION['user'] = ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'role' => $user['role']];
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
    foreach (demo_users() as $u) { // TODO(DB): найти пользователя по почте
        if (mb_strtolower($u['email']) === mb_strtolower($email) && password_verify($password, $u['hash'])) {
            return $u;
        }
    }
    password_verify($password, DEMO_PASSWORD_HASH); // выравниваем время ответа
    return null;
}

/* ── Ограничение числа неудачных попыток входа (ТЗ 11.3) ──
 * Сейчас счётчик в сессии. TODO(DB): хранить по IP и почте в таблице login_attempts. */
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

/* ── Избранное ──
 * ЗАГЛУШКА: список хранится в сессии. TODO(DB): таблица favorites (user_id, photographer_id). */
function favorites(): array
{
    return array_map('intval', $_SESSION['fav'] ?? []);
}

function is_favorite(int $photographerId): bool
{
    return in_array($photographerId, favorites(), true);
}

function toggle_favorite(int $photographerId): bool
{
    $fav = favorites();
    if (in_array($photographerId, $fav, true)) {
        $fav = array_values(array_diff($fav, [$photographerId]));
        $on = false;
    } else {
        $fav[] = $photographerId;
        $on = true;
    }
    $_SESSION['fav'] = $fav;
    return $on;
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
