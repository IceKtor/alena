<?php
/** POST: регистрация (ФТ-08). Фотограф после регистрации попадает в личный кабинет. */
declare(strict_types=1);
require __DIR__ . '/../bootstrap.php';
api_guard();

$role  = post_str('role');
$name  = post_str('name');
$email = post_str('email');
$pass  = $_POST['password'] ?? '';
$pass  = is_string($pass) ? $pass : '';

$errors = collect_errors([
    'name'     => v_name($name),
    'email'    => v_email($email) ?? (user_email_exists($email) ? MSG_EMAIL_DUP : null),
    'password' => v_password($pass),
    'consent'  => v_consent($_POST['consent'] ?? null),
]);
if (!in_array($role, [ROLE_PHOTOGRAPHER, ROLE_CLIENT], true)) {
    $errors['role'] = 'Выберите роль';
}
if ($errors) {
    json_out(['ok' => false, 'errors' => $errors], 422);
}

try {
    $user = save_user([
        'name' => $name, 'email' => mb_strtolower($email), 'role' => $role,
        'hash' => password_hash($pass, PASSWORD_DEFAULT),
    ]);
} catch (PDOException $error) {
    if ((int) ($error->errorInfo[1] ?? 0) === 1062 && user_email_exists($email)) {
        json_out(['ok' => false, 'errors' => ['email' => MSG_EMAIL_DUP]], 422);
    }
    throw $error;
}
login_user($user);
json_out(['ok' => true, 'redirect' => $role === ROLE_PHOTOGRAPHER ? 'cabinet.php' : null]);
