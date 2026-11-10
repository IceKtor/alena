<?php
/**
 * Подключение к базе данных (MySQL/MariaDB через PDO).
 *
 * ЗАГЛУШКА: база пока не подключена (DB_ENABLED = false в server/config.php).
 * Когда БД будет готова:
 *   1. Импортировать схему из sql/ через phpMyAdmin.
 *   2. Выставить DB_* и DB_ENABLED = true.
 *   3. Переписать функции в server/data.php на запросы через db().
 *      Запросы — только подготовленные (prepared statements), ТЗ 11.3.
 */
declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        if (!DB_ENABLED) {
            throw new RuntimeException('База данных не подключена (заглушка).');
        }
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}
