<?php
/**
 * Серверная проверка ввода — ТЗ, раздел 8.4. Это обязательная проверка;
 * проверка в браузере (js/app.js) — только подсказка.
 * Каждая функция возвращает текст ошибки или null.
 */
declare(strict_types=1);

const MSG_NAME     = 'Укажите имя от 2 до 50 букв';
const MSG_EMAIL    = 'Укажите почту в формате name@mail.ru';
const MSG_EMAIL_DUP = 'Эта почта уже зарегистрирована';
const MSG_PASSWORD = 'Пароль не короче 8 символов, с буквой и цифрой';
const MSG_CONTACT  = 'Укажите телефон или почту для связи';
const MSG_DATE     = 'Укажите дату не раньше сегодняшней';
const MSG_GENRE    = 'Выберите жанр из списка';
const MSG_MESSAGE  = 'Сообщение не длиннее 1000 символов';
const MSG_CONSENT  = 'Для отправки необходимо согласие';
const MSG_FILE     = 'Допустимы файлы JPG и PNG до 10 МБ';
const MSG_TITLE    = 'Укажите название до 100 символов';
const MSG_TAGS     = 'Не более 10 тегов по 30 символов';

function v_name(string $v): ?string
{
    $len = mb_strlen($v);
    if ($len < 2 || $len > 50 || !preg_match('/^\p{L}[\p{L} \-]*$/u', $v)) {
        return MSG_NAME;
    }
    return null;
}

function v_email(string $v): ?string
{
    return (mb_strlen($v) <= 254 && filter_var($v, FILTER_VALIDATE_EMAIL)) ? null : MSG_EMAIL;
}

function v_password(string $v): ?string
{
    if (strlen($v) < 8 || strlen($v) > 200 || !preg_match('/\p{L}/u', $v) || !preg_match('/\d/', $v)) {
        return MSG_PASSWORD;
    }
    return null;
}

/** Телефон в формате +7 XXX XXX-XX-XX (допускаем 8 в начале и разные разделители). */
function normalize_phone(string $v): ?string
{
    $d = preg_replace('/\D+/', '', $v);
    if (strlen($d) === 11 && ($d[0] === '7' || $d[0] === '8')) {
        $d = substr($d, 1);
    }
    if (strlen($d) !== 10) {
        return null;
    }
    return sprintf('+7 %s %s-%s-%s', substr($d, 0, 3), substr($d, 3, 3), substr($d, 6, 2), substr($d, 8, 2));
}

/** Телефон или почта: достаточно одного способа связи. */
function v_contact(string $v): ?string
{
    if ($v === '' || mb_strlen($v) > 254) {
        return MSG_CONTACT;
    }
    if (str_contains($v, '@')) {
        return filter_var($v, FILTER_VALIDATE_EMAIL) ? null : MSG_CONTACT;
    }
    return normalize_phone($v) !== null ? null : MSG_CONTACT;
}

function v_date(string $v): ?string
{
    $d = DateTimeImmutable::createFromFormat('!Y-m-d', $v);
    if (!$d || $d->format('Y-m-d') !== $v || $d < new DateTimeImmutable('today')) {
        return MSG_DATE;
    }
    return null;
}

function v_genre(string $v): ?string
{
    return isset(genres()[$v]) ? null : MSG_GENRE;
}

function v_message(string $v, bool $required = false): ?string
{
    if (mb_strlen($v) > MESSAGE_MAX || ($required && trim($v) === '')) {
        return MSG_MESSAGE;
    }
    return null;
}

function v_consent(mixed $v): ?string
{
    return !empty($v) ? null : MSG_CONSENT;
}

function v_title(string $v): ?string
{
    $len = mb_strlen($v);
    return ($len >= 1 && $len <= 100) ? null : MSG_TITLE;
}

/** Теги через запятую: не более 10, каждый до 30 символов. */
function parse_tags(string $v): array
{
    $tags = array_filter(array_map('trim', explode(',', $v)), fn($t) => $t !== '');
    return array_values(array_unique($tags));
}

function v_tags(string $v): ?string
{
    $tags = parse_tags($v);
    if (count($tags) > 10) {
        return MSG_TAGS;
    }
    foreach ($tags as $t) {
        if (mb_strlen($t) > 30) {
            return MSG_TAGS;
        }
    }
    return null;
}

/**
 * Файл работы: JPG или PNG до 10 МБ, формат определяется по содержимому,
 * а не по расширению (раздел 8.4, 11.3).
 */
function v_image_upload(?array $file): ?string
{
    if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
        return MSG_FILE;
    }
    if ($file['size'] <= 0 || $file['size'] > UPLOAD_MAX_BYTES) {
        return MSG_FILE;
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!in_array($mime, ['image/jpeg', 'image/png'], true) || @getimagesize($file['tmp_name']) === false) {
        return MSG_FILE;
    }
    // TODO: минимальная сторона изображения — «[определить] px» в ТЗ 8.4; проверить, когда будет решено.
    return null;
}

/** Собрать ошибки: ['поле' => 'сообщение'], пропуская null. */
function collect_errors(array $checks): array
{
    return array_filter($checks, fn($m) => $m !== null);
}
