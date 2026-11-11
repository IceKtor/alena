<?php
/**
 * Настройки платформы «Объектив».
 * Параметры взяты из ТЗ (приложение Б).
 */
declare(strict_types=1);

const APP_NAME        = 'Объектив';
const APP_TAGLINE     = 'Каталог фотографов Якутии: смотрите работы, сравнивайте цены, пишите напрямую.';
const DEFAULT_CITY    = 'Якутск';

const CARDS_PER_PAGE  = 6;        // каталог: карточек на страницу (ФТ-04)
const WORKS_PER_STEP  = 9;        // лента работ: карточек за один раз (ФТ-06)
const GALLERY_STEP    = 9;        // профиль: работ в галерее за один раз
const PRICE_MIN       = 300;      // диапазон цены за час, ₽ (раздел 8.3)
const PRICE_MAX       = 12000;

const UPLOAD_MAX_BYTES = 10 * 1024 * 1024; // JPG/PNG до 10 МБ (раздел 8.4)
const UPLOAD_DIR       = __DIR__ . '/../uploads';
const MESSAGE_MAX      = 1000;

const LOGIN_MAX_ATTEMPTS = 5;     // защита от перебора пароля (раздел 11.3)
const LOGIN_LOCK_SECONDS = 300;

/*
 * ─────────────────────────────────────────────────────────────────────────
 * ЗАГЛУШКА: подключение к базе данных (MySQL/MariaDB, phpMyAdmin).
 * Когда появится БД — заполнить эти значения (лучше брать из переменных
 * окружения) и реализовать server/db.php.
 * ─────────────────────────────────────────────────────────────────────────
 */
const DB_HOST    = 'localhost';
const DB_NAME    = 'obyektiv';
const DB_USER    = 'root';
const DB_PASS    = '';
const DB_CHARSET = 'utf8mb4';
const DB_ENABLED = false;         // пока false — используются данные из server/data.php

/*
 * ЗАГЛУШКА: демо-аккаунты, пока нет таблицы users.
 * Пароль обоих: demo12345.  Удалить вместе с server/auth.php::demo_users().
 */
const DEMO_PASSWORD_HASH = '$2y$10$.0TgJ.UygSu6qE2JGSoxq.Tjm8TJdceCNzXR6Xi9BtyJlYNtNtv1C';
