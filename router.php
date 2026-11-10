<?php
/**
 * Роутер для локального сервера разработки:
 *   php -S localhost:8000 router.php
 * Закрывает служебные каталоги и отдаёт страницу 404 (на Apache это делает .htaccess).
 */
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (preg_match('#^/(server|sql|partials)/(?!api/)#', $path) || str_starts_with($path, '/.') || str_starts_with($path, '/uploads/') && preg_match('/\.(php|phtml)$/i', $path)) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    return true;
}
$file = __DIR__ . $path;
if ($path !== '/' && is_file($file)) {
    return false; // отдать как есть (css, js, картинки, php-страницы)
}
if ($path === '/' ) {
    require __DIR__ . '/index.php';
    return true;
}
http_response_code(404);
require __DIR__ . '/404.php';
return true;
