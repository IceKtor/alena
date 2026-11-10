<?php
/** Личный кабинет фотографа (ФТ-09 … ФТ-12, ФТ-19). Доступен только роли «фотограф» (ТЗ 8.5). */
declare(strict_types=1);
require __DIR__ . '/server/bootstrap.php';

page_start(['title' => 'Личный кабинет', 'nav' => '', 'noindex' => true]); // закрыт от индексации (ТЗ 16.1)

if (!is_role(ROLE_PHOTOGRAPHER)) {
    http_response_code(current_user() ? 403 : 401);
    require __DIR__ . '/partials/header.php';
    ?>
    <section class="section section--page"><div class="container">
      <div class="card card--pad empty">
        <h1 class="h2">Личный кабинет</h1>
        <?php if (current_user()): ?>
          <p class="muted">Кабинет доступен только фотографам. Вы вошли как заказчик.</p>
          <a class="btn btn--primary" href="photographers.php">Найти фотографа</a>
        <?php else: ?>
          <p class="muted">Войдите как фотограф, чтобы управлять портфолио и заявками.</p>
          <button type="button" class="btn btn--primary" data-open-modal="login" data-autoopen>Войти</button>
        <?php endif; ?>
      </div>
    </div></section>
    <?php
    require __DIR__ . '/partials/footer.php';
    exit;
}

$user    = current_user();
$me      = photographer_by_id((int) $user['id']);   // TODO(DB): профиль текущего пользователя
$sections = [
    'works'    => ['Мои работы', 'image'],
    'profile'  => ['Профиль', 'camera'],
    'requests' => ['Заявки', 'mail'],
    'stats'    => ['Статистика', 'eye'],
    'settings' => ['Настройки', 'sliders'],
];
$section = (string) ($_GET['section'] ?? 'works');
$section = isset($sections[$section]) ? $section : 'works';

$stats    = photographer_stats((int) $user['id']);
$requests = photographer_requests((int) $user['id']);
$newCount = count(array_filter($requests, fn($r) => $r['status'] === 'new'));
$myWorks  = $me ? photographer_works($me) : [];
$statusLabels = ['new' => 'Новая', 'viewed' => 'Просмотрена', 'done' => 'Обработана'];

require __DIR__ . '/partials/header.php';
?>

<section class="section section--page">
  <div class="container cabinet">
    <aside class="cabinet__side card" aria-label="Разделы кабинета">
      <nav>
        <ul>
          <?php foreach ($sections as $key => [$label, $ico]): ?>
            <li>
              <a class="side-link<?= $section === $key ? ' is-active' : '' ?>" href="<?= e(url('cabinet.php', ['section' => $key !== 'works' ? $key : null])) ?>"<?= $section === $key ? ' aria-current="page"' : '' ?>>
                <?= icon($ico) ?><span><?= e($label) ?></span>
                <?php if ($key === 'requests' && $newCount): ?><span class="count" aria-label="новых: <?= $newCount ?>"><?= $newCount ?></span><?php endif; ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </nav>
    </aside>

    <div class="cabinet__main">
      <?php if ($section === 'works'): ?>
        <div class="page-head">
          <h1>Мои работы</h1>
          <button type="button" class="btn btn--primary" data-open-modal="work">Загрузить работу</button>
        </div>
        <ul class="stat-cards">
          <li class="card stat-card"><strong class="stat-card__num"><?= number_format($stats['views'], 0, ',', "\u{00A0}") ?></strong><span class="stat-card__bar stat-card__bar--orange"></span><span class="muted">Просмотров профиля за месяц</span></li>
          <li class="card stat-card"><strong class="stat-card__num"><?= $stats['requests'] ?></strong><span class="stat-card__bar stat-card__bar--teal"></span><span class="muted">Новых заявок</span></li>
          <li class="card stat-card"><strong class="stat-card__num"><?= $stats['favorites'] ?></strong><span class="stat-card__bar stat-card__bar--amber"></span><span class="muted">Добавили в избранное</span></li>
        </ul>
        <h2 class="h3">Опубликовано <?= count($myWorks) ?></h2>
        <div class="grid grid--4">
          <?php foreach ($myWorks as $w): ?>
            <article class="work work--own">
              <span class="work__img frame"><?= img_tag($w['file'], $w['title']) ?>
                <button type="button" class="icon-btn work__edit" data-stub="Изменение и удаление работ появятся вместе с базой данных (ФТ-11)" aria-label="Изменить работу «<?= e($w['title']) ?>»"><?= icon('pencil') ?></button>
              </span>
              <span class="work__title"><?= e($w['title']) ?></span>
              <span class="work__meta">Просмотров: <?= $w['views'] ?></span>
            </article>
          <?php endforeach; ?>
        </div>

        <div class="dropzone" data-dropzone>
          <?= icon('upload', 'dropzone__ico') ?>
          <div class="dropzone__text"><strong>Перетащите фото сюда</strong><span class="muted">JPG или PNG до 10 МБ. Можно выбрать сразу несколько файлов.</span></div>
          <label class="btn btn--ghost" for="dz-files">Выбрать файлы</label>
          <input class="sr-only" id="dz-files" type="file" accept="image/jpeg,image/png" multiple>
        </div>

      <?php elseif ($section === 'profile'): ?>
        <h1>Профиль</h1>
        <!-- ЗАГЛУШКА (ФТ-11): редактирование профиля — форма без сохранения. TODO(DB): UPDATE photographer_profiles. -->
        <form class="card card--pad form" data-stub-form="Сохранение профиля появится вместе с базой данных (ФТ-11)">
          <div class="field-row">
            <div class="field"><label for="p-name">Имя</label><span class="input"><input id="p-name" value="<?= e($me['name'] ?? $user['name']) ?>"></span></div>
            <div class="field"><label for="p-city">Город</label><span class="input input--select"><select id="p-city"><?php foreach (cities() as $c): ?><option<?= ($me['city'] ?? '') === $c ? ' selected' : '' ?>><?= e($c) ?></option><?php endforeach; ?></select><?= icon('chevron', 'icon--sm') ?></span></div>
          </div>
          <div class="field"><label for="p-price">Цена «от», ₽ за час</label><span class="input"><input id="p-price" type="number" min="<?= PRICE_MIN ?>" max="<?= PRICE_MAX ?>" value="<?= (int) ($me['price'] ?? 3000) ?>"></span></div>
          <div class="field"><label for="p-about">Об авторе</label><span class="input input--area"><textarea id="p-about" rows="4"><?= e($me['about'] ?? '') ?></textarea></span></div>
          <p class="hint">Аватар, обложка, услуги и свободные даты — загрузка появится вместе с базой данных.</p>
          <div><button type="submit" class="btn btn--primary">Сохранить</button></div>
        </form>

      <?php elseif ($section === 'requests'): ?>
        <h1>Заявки</h1>
        <?php if (!$requests): ?>
          <div class="card card--pad empty"><p class="muted">Заявок пока нет.</p></div>
        <?php else: ?>
          <ul class="requests">
            <?php foreach ($requests as $r): ?>
              <li class="card card--pad request">
                <div class="request__head">
                  <strong><?= e($r['name']) ?></strong>
                  <span class="status status--<?= e($r['status']) ?>"><?= e($statusLabels[$r['status']]) ?></span>
                </div>
                <p class="muted small"><?= e($r['created']) ?> · <?= e(genre_name($r['genre'])) ?> · съёмка: <?= e($r['date']) ?></p>
                <p><?= e($r['message']) ?></p>
                <p class="small"><?= icon('phone', 'icon--sm') ?> <?= e($r['contact']) ?></p>
                <!-- ЗАГЛУШКА (ФТ-12): смена статуса. TODO(DB): UPDATE requests SET status = ? WHERE id = ? AND profile_id = текущий -->
                <label class="small" for="st-<?= $r['id'] ?>">Статус</label>
                <span class="input input--select input--pill input--sm"><select id="st-<?= $r['id'] ?>" data-stub-change="Смена статуса сохранится вместе с базой данных (ФТ-12)">
                  <?php foreach ($statusLabels as $k => $l): ?><option value="<?= $k ?>"<?= $r['status'] === $k ? ' selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?>
                </select><?= icon('chevron', 'icon--sm') ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>

      <?php elseif ($section === 'stats'): ?>
        <h1>Статистика</h1>
        <ul class="stat-cards">
          <li class="card stat-card"><strong class="stat-card__num"><?= number_format($stats['views'], 0, ',', "\u{00A0}") ?></strong><span class="stat-card__bar stat-card__bar--orange"></span><span class="muted">Просмотров профиля за месяц</span></li>
          <li class="card stat-card"><strong class="stat-card__num"><?= $stats['requests'] ?></strong><span class="stat-card__bar stat-card__bar--teal"></span><span class="muted">Заявок за месяц</span></li>
          <li class="card stat-card"><strong class="stat-card__num"><?= $stats['favorites'] ?></strong><span class="stat-card__bar stat-card__bar--amber"></span><span class="muted">Добавили в избранное</span></li>
        </ul>
        <p class="note">Учитывается только факт просмотра, сторонняя аналитика не подключена. Значения условные.</p>

      <?php else: ?>
        <h1>Настройки</h1>
        <!-- ЗАГЛУШКА (ФТ-19, желательное): пароль, уведомления, удаление аккаунта -->
        <div class="card card--pad">
          <label class="switch"><input type="checkbox" checked data-stub-change="Настройки уведомлений появятся вместе с базой данных (ФТ-17, ФТ-19)"><span class="switch__track"></span><span>Уведомлять о новых заявках по почте</span></label>
          <div class="settings-actions">
            <button type="button" class="btn btn--ghost" data-stub="Смена пароля появится вместе с базой данных (ФТ-19)">Сменить пароль</button>
            <button type="button" class="btn btn--ghost" data-stub="Удаление аккаунта появится вместе с базой данных (ФТ-19)">Удалить аккаунт</button>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
