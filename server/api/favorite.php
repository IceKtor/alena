<?php
/** POST: добавить/убрать фотографа из избранного (ФТ-13). Только заказчик; гостю предлагается войти. */
declare(strict_types=1);
require __DIR__ . '/../bootstrap.php';
api_guard();

if (!current_user()) {
    json_out(['ok' => false, 'login' => true, 'error' => 'Войдите, чтобы добавлять в избранное'], 401);
}
if (!is_role(ROLE_CLIENT)) {
    json_out(['ok' => false, 'error' => 'Избранное доступно заказчикам'], 403);
}
$pid = (int) ($_POST['id'] ?? 0);
if (!photographer_by_id($pid)) {
    json_out(['ok' => false, 'error' => 'Фотограф не найден'], 404);
}
json_out(['ok' => true, 'on' => toggle_favorite($pid)]);
