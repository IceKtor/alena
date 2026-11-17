<?php
/** Выдаёт изображения работ с проверкой видимости. */
declare(strict_types=1);
require __DIR__ . '/server/bootstrap.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$download = ($_GET['download'] ?? '') === '1';
$column = $download || ($_GET['full'] ?? '') === '1' ? 'image_data' : 'thumbnail';
$image = $id ? db_query("SELECT i.mime, i.$column AS payload
    FROM work_images i JOIN works w ON w.id = i.work_id
    WHERE w.id = ? AND (w.is_public = 1 OR w.photographer_id = ?)
      AND (? = 0 OR w.allow_download = 1 OR w.photographer_id = ?)",
    [$id, current_user()['id'] ?? 0, (int) $download, current_user()['id'] ?? 0])->fetch() : false;

if (!$image) {
    http_response_code(404);
    exit;
}
header('Cache-Control: private, no-store');
header('Content-Type: ' . $image['mime']);
if ($download) {
    $extension = $image['mime'] === 'image/png' ? 'png' : 'jpg';
    header("Content-Disposition: attachment; filename=work-$id.$extension");
}
echo $image['payload'];
