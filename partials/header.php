<?php
/** Шапка сайта. Ожидает, что page_start() уже вызван. */
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
  <meta name="theme-color" content="#F26B3A">
  <link rel="icon" href="img/favicon.svg" type="image/svg+xml">
  <!-- Шрифты по ТЗ 9.2: Playfair Display (заголовки), Montserrat (текст), IBM Plex Mono (цены). Источник: Google Fonts. -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=Montserrat:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,700;0,800;1,700&display=swap">
  <link rel="stylesheet" href="css/style.css">
  <script src="js/app.js" defer></script>
</head>
<body class="<?= e($pg['body_class']) ?>">
<?php require __DIR__ . '/icons.php'; ?>
<a class="skip-link" href="#main">К содержимому</a>

<header class="site-header">
  <div class="container site-header__inner">
    <a class="logo" href="index.php" aria-label="<?= e(APP_NAME) ?> — на главную"><?= logo() ?></a>

    <button type="button" class="menu-toggle icon-btn" aria-expanded="false" aria-controls="site-nav" aria-label="Открыть меню">
      <?= icon('menu') ?>
    </button>

    <div class="site-header__panel" id="site-nav">
      <nav class="main-nav" aria-label="Основное меню">
        <?php foreach ($nav as $key => [$href, $label]): ?>
          <a href="<?= e($href) ?>" class="main-nav__link<?= $pg['nav'] === $key ? ' is-active' : '' ?>"<?= $pg['nav'] === $key ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
        <?php endforeach; ?>
      </nav>

      <div class="header-actions">
        <?php if ($user): ?>
          <?php $newRequests = is_role(ROLE_PHOTOGRAPHER) ? 3 : 0; /* TODO(DB): число новых заявок */ ?>
          <a class="icon-btn bell" href="<?= is_role(ROLE_PHOTOGRAPHER) ? 'cabinet.php?section=requests' : 'favorites.php' ?>" aria-label="Уведомления<?= $newRequests ? ': новых ' . $newRequests : '' ?>">
            <?= icon('bell') ?><?php if ($newRequests): ?><span class="bell__dot" aria-hidden="true"></span><?php endif; ?>
          </a>
          <div class="user-menu">
            <button type="button" class="user-menu__btn" aria-expanded="false" aria-haspopup="true">
              <?php $ua = $user['role'] === ROLE_PHOTOGRAPHER ? (photographer_by_id((int) $user['id'])['avatar'] ?? null) : null; ?>
              <?php if ($ua): ?><img class="avatar avatar--sm" src="<?= e(photo_url($ua)) ?>" alt="" width="40" height="40"><?php else: ?><span class="avatar avatar--sm avatar--blank"><?= icon('user') ?></span><?php endif; ?>
              <span class="user-menu__name"><?= e(short_name($user['name'])) ?></span>
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
          <button type="button" class="btn btn--primary btn--sm" data-open-modal="register">Разместить портфолио</button>
        <?php endif; ?>
      </div>
    </div>
  </div>
</header>

<main id="main">
