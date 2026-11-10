<?php
/** POST: форма «Написать нам» на странице «О платформе» (ФТ-06, А-16). */
declare(strict_types=1);
require __DIR__ . '/../bootstrap.php';
api_guard();

$name    = post_str('name');
$email   = post_str('email');
$message = post_str('message');

$errors = collect_errors([
    'name'    => v_name($name),
    'email'   => v_email($email),
    'message' => v_message($message, true),
    'consent' => v_consent($_POST['consent'] ?? null),
]);
if ($errors) {
    json_out(['ok' => false, 'errors' => $errors], 422);
}
save_feedback(['name' => $name, 'email' => $email, 'message' => $message]); // ЗАГЛУШКА, TODO(DB)
json_out(['ok' => true, 'message' => 'Спасибо! Сообщение отправлено администрации платформы.']);
