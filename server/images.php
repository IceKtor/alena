<?php
declare(strict_types=1);

/** Перекодирование убирает метаданные и посторонние данные из файла. */
function encode_work_image(GdImage $image, string $mime): string
{
    ob_start();
    try {
        $ok = $mime === 'image/png' ? imagepng($image, null, 6) : imagejpeg($image, null, 90);
        $data = ob_get_contents();
    } finally {
        ob_end_clean();
    }
    if (!$ok || !$data || strlen($data) >= 16 * 1024 * 1024) {
        throw new DomainException('Не удалось обработать изображение. Выберите файл меньшего размера.');
    }
    return $data;
}

function save_work(array $work, array $file): void
{
    [$width, $height, $type] = getimagesize($file['tmp_name']);
    if ($width * $height > 12000000 || max($width, $height) > 8000) {
        throw new DomainException('Допустимо до 12 Мп и до 8000 пикселей по каждой стороне.');
    }
    $mime = $type === IMAGETYPE_PNG ? 'image/png' : 'image/jpeg';
    $image = $type === IMAGETYPE_PNG ? @imagecreatefrompng($file['tmp_name']) : @imagecreatefromjpeg($file['tmp_name']);
    if (!$image) {
        throw new DomainException('Не удалось прочитать изображение.');
    }
    if ($mime === 'image/png') {
        imagesavealpha($image, true);
    }
    $scale = min(1, 720 / max($width, $height));
    $thumbnail = imagescale($image, max(1, (int) round($width * $scale)), max(1, (int) round($height * $scale)));
    if (!$thumbnail) {
        throw new DomainException('Не удалось создать миниатюру.');
    }
    if ($mime === 'image/png') {
        imagesavealpha($thumbnail, true);
    }
    $original = encode_work_image($image, $mime);
    $preview = encode_work_image($thumbnail, $mime);

    $pdo = db();
    $pdo->beginTransaction();
    try {
        $work['tags'] = json_encode($work['tags'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        db_query('INSERT INTO works (photographer_id, title, genre, tags, is_public, allow_download)
                  VALUES (:photographer_id, :title, :genre, :tags, :public, :download)', $work);
        db_query('INSERT INTO work_images (work_id, mime, width, height, image_data, thumbnail)
                  VALUES (?, ?, ?, ?, ?, ?)', [$pdo->lastInsertId(), $mime, $width, $height, $original, $preview]);
        $pdo->commit();
    } catch (Throwable $error) {
        $pdo->rollBack();
        throw $error;
    }
}
