<?php
/** Общая инициализация: подключается в начале каждой страницы и API-файла. */
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/data.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/validators.php';
require_once __DIR__ . '/auth.php';

mb_internal_encoding('UTF-8');
date_default_timezone_set('Asia/Yakutsk');

// Технические ошибки пользователю не показываются, а пишутся в журнал (ТЗ 11.2).
ini_set('display_errors', '0');
ini_set('log_errors', '1');

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => !empty($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');

/**
 * Параметры страницы: заголовок, описание, раздел меню, закрыть от индексации.
 * Страницы вызывают page_start([...]) перед подключением шапки.
 */
function page_start(array $meta): void
{
    $GLOBALS['_page'] = $meta + [
        'title'       => APP_NAME,
        'description' => APP_TAGLINE,
        'nav'         => '',
        'noindex'     => false,
        'canonical'   => null,
        'body_class'  => '',
    ];
}
