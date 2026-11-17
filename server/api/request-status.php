<?php
/** Фотограф может менять статус только своих заявок. */
declare(strict_types=1);
require __DIR__ . '/../bootstrap.php';
api_guard();

if (!is_role(ROLE_PHOTOGRAPHER)) {
    json_out(['ok' => false, 'error' => 'Управление заявками доступно фотографу.'], 403);
}

$id = filter_var(post_str('id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$status = post_str('status');
if (!$id || !in_array($status, ['new', 'viewed', 'done'], true)) {
    json_out(['ok' => false, 'error' => 'Укажите заявку и статус из списка.'], 422);
}

$owner = current_user()['id'];
$changed = db_query('UPDATE requests SET status = ? WHERE id = ? AND photographer_id = ?', [$status, $id, $owner]);
if (!$changed->rowCount() && !db_query('SELECT 1 FROM requests WHERE id = ? AND photographer_id = ?', [$id, $owner])->fetchColumn()) {
    json_out(['ok' => false, 'error' => 'Заявка не найдена.'], 404);
}
json_out(['ok' => true, 'redirect' => 'cabinet.php?section=requests']);
