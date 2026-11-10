<?php
/**
 * POST: заявка фотографу (ФТ-07). Отправить может гость (допущение А-07);
 * фотограф не может отправить заявку на свой профиль (ТЗ 8.5).
 */
declare(strict_types=1);
require __DIR__ . '/../bootstrap.php';
api_guard();

$pid = (int) ($_POST['photographer_id'] ?? 0);
$p   = photographer_by_id($pid);
if (!$p) {
    json_out(['ok' => false, 'error' => 'Фотограф не найден'], 404);
}
if (is_role(ROLE_PHOTOGRAPHER) && (int) current_user()['id'] === $pid) {
    json_out(['ok' => false, 'error' => 'Нельзя отправить заявку на собственный профиль'], 403);
}

$name    = post_str('name');
$contact = post_str('contact');
$date    = post_str('date');
$genre   = post_str('genre');
$message = post_str('message');

$errors = collect_errors([
    'name'    => v_name($name),
    'contact' => v_contact($contact),
    'date'    => v_date($date),
    'genre'   => v_genre($genre),
    'message' => v_message($message),
    'consent' => v_consent($_POST['consent'] ?? null),
]);
if ($errors) {
    json_out(['ok' => false, 'errors' => $errors], 422);
}

if (!str_contains($contact, '@')) {
    $contact = normalize_phone($contact) ?? $contact;
}
save_request([ // ЗАГЛУШКА: TODO(DB) — INSERT INTO requests (status = 'new')
    'photographer_id' => $pid, 'name' => $name, 'contact' => $contact,
    'date' => $date, 'genre' => $genre, 'message' => $message, 'consent' => true,
]);
json_out(['ok' => true, 'message' => 'Заявка отправлена. ' . $p['name'] . ' свяжется с вами по указанным контактам.']);
