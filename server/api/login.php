<?php
/** POST: вход (ФТ-08). Любая ошибка пары «почта — пароль» → одно общее сообщение. */
declare(strict_types=1);
require __DIR__ . '/../bootstrap.php';
api_guard();

if (($wait = login_locked()) > 0) {
    json_out(['ok' => false, 'error' => 'Слишком много попыток входа. Повторите через ' . ceil($wait / 60) . ' мин.'], 429);
}

$email = post_str('email');
$pass  = $_POST['password'] ?? '';
$errors = collect_errors([
    'email'    => v_email($email),
    'password' => (is_string($pass) && $pass !== '') ? null : 'Введите пароль',
]);
if ($errors) {
    json_out(['ok' => false, 'errors' => $errors], 422);
}

$user = attempt_login($email, (string) $pass);
if (!$user) {
    login_failed();
    json_out(['ok' => false, 'error' => 'Неверная почта или пароль'], 401);
}
login_reset_fails();
login_user($user);
json_out(['ok' => true, 'redirect' => $user['role'] === ROLE_PHOTOGRAPHER ? 'cabinet.php' : null]);
