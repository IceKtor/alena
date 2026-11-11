<?php
/** Шапка сайта (дизайн «Тёмная галерея»). Ожидает, что page_start() уже вызван. */
require_once __DIR__ . '/components.php';
$pg   = $GLOBALS['_page'];
$user = current_user();
$nav  = [
    'home'    => ['index.php', 'Главная'],
    'catalog' => ['photographers.php', 'Фотографы'],
    'works'   => ['works.php', 'Работы'],
    'about'   => ['about.php', 'О платформе'],
];
$title = $pg['title'] === APP_NAME ? APP_NAME : $pg['title'] . ' — ' . APP_NAME;
?><!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title) ?></title>
  <meta name="description" content="<?= e($pg['description']) ?>">
  <?php if ($pg['noindex']): ?><meta name="robots" content="noindex, nofollow"><?php endif; ?>
  <?php if ($pg['canonical']): ?><link rel="canonical" href="<?= e($pg['canonical']) ?>"><?php endif; ?>
  <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
  <meta name="theme-color" content="#0F0F0E">
  <link rel="icon" href="img/favicon.svg" type="image/svg+xml">
  <!-- Шрифты: Cormorant Garamond (заголовки), Inter (текст), IBM Plex Mono (цены и номера кадров). Источник: Google Fonts. -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=Inter:wght@400;500;600&display=swap">
  <link rel="stylesheet" href="css/style.css">
  <script src="js/app.js" defer></script>
</head>
<body class="<?= e($pg['body_class']) ?>">
<?php require __DIR__ . '/icons.php'; ?>
<a class="skip-link" href="#main">К содержимому</a>

<header class="hd<?= !empty($pg['over']) ? ' over' : '' ?>" id="site-header">
  <a class="logo" href="index.php" aria-label="<?= e(APP_NAME) ?> — на главную"><?= logo() ?></a>

  <button type="button" class="burger menu-toggle" aria-expanded="false" aria-controls="site-nav" aria-label="Открыть меню">
    <?= icon('menu') ?>
  </button>

  <div class="hd-panel site-header__panel" id="site-nav">
    <nav class="nav" aria-label="Основное меню">
      <?php foreach ($nav as $key => [$href, $label]): ?>
        <a href="<?= e($href) ?>"<?= $pg['nav'] === $key ? ' class="on"' : '' ?><?= $pg['nav'] === $key ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
      <?php endforeach; ?>
    </nav>

    <div class="hd-r">
      <?php if ($user): ?>
        <?php $newRequests = is_role(ROLE_PHOTOGRAPHER) ? 3 : 0; /* TODO(DB): число новых заявок */ ?>
        <a class="bell" href="<?= is_role(ROLE_PHOTOGRAPHER) ? 'cabinet.php?section=requests' : 'favorites.php' ?>" aria-label="Уведомления<?= $newRequests ? ': новых ' . $newRequests : '' ?>">
          <?= icon('bell') ?><?php if ($newRequests): ?><b aria-hidden="true"></b><?php endif; ?>
        </a>
        <div class="user-menu">
          <button type="button" class="user-menu__btn" aria-expanded="false" aria-haspopup="true">
            <?php $ua = $user['role'] === ROLE_PHOTOGRAPHER ? (photographer_by_id((int) $user['id'])['avatar'] ?? null) : null; ?>
            <?php if ($ua): ?><img class="avatar avatar--sm" src="<?= e(photo_url($ua)) ?>" alt="" width="36" height="36"><?php else: ?><span class="avatar avatar--sm avatar--blank"><?= icon('user', 'icon--sm') ?></span><?php endif; ?>
            <span><?= e(short_name($user['name'])) ?></span>
            <?= icon('chevron', 'icon--sm') ?>
          </button>
          <div class="user-menu__list" role="menu" hidden>
            <?php if (is_role(ROLE_PHOTOGRAPHER)): ?>
              <a role="menuitem" href="cabinet.php"><?= icon('image') ?>Личный кабинет</a>
            <?php endif; ?>
            <a role="menuitem" href="favorites.php"><?= icon('heart') ?>Избранное</a>
            <button type="button" role="menuitem" data-logout><?= icon('logout') ?>Выйти</button>
          </div>
        </div>
      <?php else: ?>
        <button type="button" class="link-btn" data-open-modal="login">Войти</button>
        <button type="button" class="btn btn--sm" data-open-modal="register">Разместить портфолио</button>
      <?php endif; ?>
    </div>
  </div>
</header>

<main id="main">
