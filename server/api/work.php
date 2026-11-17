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

require __DIR__ . '/../images.php';
$isPublic = !empty($_POST['public']);
try {
    save_work([
        'photographer_id' => (int) current_user()['id'], 'title' => $title, 'genre' => $genre,
        'tags' => parse_tags($tags), 'public' => (int) $isPublic, 'download' => (int) !empty($_POST['download']),
    ], $file);
} catch (DomainException $error) {
    json_out(['ok' => false, 'errors' => ['file' => $error->getMessage()]], 422);
}
json_out(['ok' => true, 'redirect' => 'cabinet.php']);
