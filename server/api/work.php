<?php
/** POST: загрузка новой работы (ФТ-10). Только фотограф и только в свой профиль (ТЗ 8.5). */
declare(strict_types=1);
require __DIR__ . '/../bootstrap.php';
api_guard();

if (!is_role(ROLE_PHOTOGRAPHER)) {
    json_out(['ok' => false, 'error' => 'Загружать работы может только фотограф. Войдите в аккаунт фотографа.'], 403);
}

$title = post_str('title');
$genre = post_str('genre');
$tags  = post_str('tags');
$file  = $_FILES['file'] ?? null;

$errors = collect_errors([
    'file'  => v_image_upload(is_array($file) ? $file : null),
    'title' => v_title($title),
    'genre' => v_genre($genre),
    'tags'  => v_tags($tags),
]);
if ($errors) {
    json_out(['ok' => false, 'errors' => $errors], 422);
}

/*
 * ЗАГЛУШКА: файл проверен, но не сохраняется. Когда будет БД:
 *  1. имя файла = bin2hex(random_bytes(16)) + расширение по содержимому (не по имени от клиента);
 *  2. move_uploaded_file() в UPLOAD_DIR, создать миниатюру (GD/Imagick), удалить EXIF-геометки;
 *  3. INSERT INTO works … (пути к файлу и миниатюре) в одной транзакции.
 */
save_work([
    'photographer_id' => (int) current_user()['id'], 'title' => $title, 'genre' => $genre,
    'tags' => parse_tags($tags), 'public' => !empty($_POST['public']), 'download' => !empty($_POST['download']),
], $file);
json_out(['ok' => true, 'message' => 'Работа «' . $title . '» опубликована (демо: файл пока не сохраняется).']);
