<?php
/** Личный кабинет фотографа (ФТ-09 … ФТ-12, ФТ-19). Доступен только роли «фотограф» (ТЗ 8.5). */
declare(strict_types=1);
require __DIR__ . '/server/bootstrap.php';

page_start(['title' => 'Личный кабинет', 'nav' => '', 'noindex' => true]); // закрыт от индексации (ТЗ 16.1)

if (!is_role(ROLE_PHOTOGRAPHER)) {
    http_response_code(current_user() ? 403 : 401);
    require __DIR__ . '/partials/header.php';
    ?>
    <section class="ph">
      <div class="empty">
        <h1>Личный кабинет</h1>
        <?php if (current_user()): ?>
          <p class="mt">Кабинет доступен только фотографам. Вы вошли как заказчик.</p>
          <a class="btn btn--primary" href="photographers.php">Найти фотографа</a>
        <?php else: ?>
          <p class="mt">Войдите как фотограф, чтобы управлять портфолио и заявками.</p>
          <button type="button" class="btn btn--primary" data-open-modal="login" data-autoopen>Войти</button>
        <?php endif; ?>
      </div>
    </section>
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

<div class="cab">
  <nav class="cnav" aria-label="Разделы кабинета">
    <?php foreach ($sections as $key => [$label, $ico]): ?>
      <a href="<?= e(url('cabinet.php', ['section' => $key !== 'works' ? $key : null])) ?>"<?= $section === $key ? ' class="is-active" aria-current="page"' : '' ?>>
        <span><?= e($label) ?></span>
        <?php if ($key === 'requests' && $newCount): ?><b class="count" aria-label="новых: <?= $newCount ?>"><?= $newCount ?></b><?php endif; ?>
      </a>
    <?php endforeach; ?>
  </nav>

  <div class="cab__main">
    <?php if ($section === 'works'): ?>
      <div class="chead">
        <h1>Мои работы</h1>
        <button type="button" class="btn btn--primary" data-open-modal="work">Загрузить работу</button>
      </div>
      <ul class="met">
        <li><strong><?= number_format($stats['views'], 0, ',', "\u{00A0}") ?></strong><span>просмотров профиля за месяц</span></li>
        <li><strong><?= $stats['requests'] ?></strong><span>новых заявок</span></li>
        <li><strong><?= $stats['favorites'] ?></strong><span>добавили в избранное</span></li>
      </ul>
      <h2 class="sh3">Опубликовано <?= count($myWorks) ?></h2>
      <div class="wg">
        <?php foreach (array_slice($myWorks, 0, 8) as $w): ?>
          <figure>
            <div class="im"><?= img_tag($w['file'], $w['title']) ?>
              <button type="button" class="ed" data-stub="Изменение и удаление работ появятся вместе с базой данных (ФТ-11)" aria-label="Изменить работу «<?= e($w['title']) ?>»"><?= icon('pencil') ?></button>
            </div>
            <figcaption><?= e($w['title']) ?><span>Просмотров: <?= $w['views'] ?></span></figcaption>
          </figure>
        <?php endforeach; ?>
      </div>

      <div class="dz" data-dropzone>
        <?= icon('upload') ?>
        <div class="dz__text"><b>Перетащите фото сюда</b><span>JPG или PNG до 10 МБ. Можно выбрать сразу несколько файлов.</span></div>
        <label class="btn" for="dz-files">Выбрать файлы</label>
        <input class="sr-only" id="dz-files" type="file" accept="image/jpeg,image/png" multiple>
      </div>

    <?php elseif ($section === 'profile'): ?>
      <div class="chead"><h1>Профиль</h1></div>
      <!-- ЗАГЛУШКА (ФТ-11): редактирование профиля — форма без сохранения. TODO(DB): UPDATE photographer_profiles. -->
      <form class="form" style="max-width:640px" data-stub-form="Сохранение профиля появится вместе с базой данных (ФТ-11)">
        <div class="form-row">
          <div class="field"><label for="p-name"><small>Имя</small></label><div class="v"><input id="p-name" value="<?= e($me['name'] ?? $user['name']) ?>"></div></div>
          <div class="field"><label for="p-city"><small>Город</small></label><div class="v v--select"><select id="p-city"><?php foreach (cities() as $c): ?><option<?= ($me['city'] ?? '') === $c ? ' selected' : '' ?>><?= e($c) ?></option><?php endforeach; ?></select><?= icon('chevron') ?></div></div>
        </div>
        <div class="field"><label for="p-price"><small>Цена «от», ₽ за час</small></label><div class="v"><input id="p-price" type="number" min="<?= PRICE_MIN ?>" max="<?= PRICE_MAX ?>" value="<?= (int) ($me['price'] ?? 3000) ?>"></div></div>
        <div class="field"><label for="p-about"><small>Об авторе</small></label><div class="v v--area"><textarea id="p-about" rows="4"><?= e($me['about'] ?? '') ?></textarea></div></div>
        <p class="hint" style="text-align:left">Аватар, обложка, услуги и свободные даты — загрузка появится вместе с базой данных.</p>
        <div><button type="submit" class="btn btn--primary">Сохранить</button></div>
      </form>

    <?php elseif ($section === 'requests'): ?>
      <div class="chead"><h1>Заявки</h1><span class="mt"><?= $newCount ?> <?= plural($newCount, 'новая', 'новых', 'новых') ?> из <?= count($requests) ?></span></div>
      <?php if (!$requests): ?>
        <div class="empty"><p class="mt">Заявок пока нет.</p></div>
      <?php else: ?>
        <div class="rq">
          <?php foreach ($requests as $r): ?>
            <article>
              <div class="who"><b><?= e($r['name']) ?></b><span><?= e($r['created']) ?></span></div>
              <div class="msg">
                <p><?= e($r['message']) ?></p>
                <small><span><?= e(genre_name($r['genre'])) ?></span><span>съёмка: <?= e($r['date']) ?></span><span><?= e($r['contact']) ?></span></small>
              </div>
              <div class="st">
                <span class="tag tag--<?= e($r['status']) ?>"><i></i><?= e($statusLabels[$r['status']]) ?></span>
                <!-- ЗАГЛУШКА (ФТ-12): смена статуса. TODO(DB): UPDATE requests SET status = ? WHERE id = ? AND profile_id = текущий -->
                <div class="field">
                  <label class="sr-only" for="st-<?= $r['id'] ?>">Статус заявки</label>
                  <div class="v v--select"><select id="st-<?= $r['id'] ?>" data-stub-change="Смена статуса сохранится вместе с базой данных (ФТ-12)">
                    <?php foreach ($statusLabels as $k => $l): ?><option value="<?= $k ?>"<?= $r['status'] === $k ? ' selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?>
                  </select><?= icon('chevron') ?></div>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    <?php elseif ($section === 'stats'): ?>
      <div class="chead"><h1>Статистика</h1></div>
      <ul class="met">
        <li><strong><?= number_format($stats['views'], 0, ',', "\u{00A0}") ?></strong><span>просмотров профиля за месяц</span></li>
        <li><strong><?= $stats['requests'] ?></strong><span>заявок за месяц</span></li>
        <li><strong><?= $stats['favorites'] ?></strong><span>добавили в избранное</span></li>
      </ul>
      <p class="note">Учитывается только факт просмотра, сторонняя аналитика не подключена. Значения условные.</p>

    <?php else: ?>
      <div class="chead"><h1>Настройки</h1></div>
      <!-- ЗАГЛУШКА (ФТ-19, желательное): пароль, уведомления, удаление аккаунта -->
      <label class="tg"><input type="checkbox" checked data-stub-change="Настройки уведомлений появятся вместе с базой данных (ФТ-17, ФТ-19)"><s></s><span>Уведомлять о новых заявках по почте</span></label>
      <div class="settings-actions">
        <button type="button" class="btn" data-stub="Смена пароля появится вместе с базой данных (ФТ-19)">Сменить пароль</button>
        <button type="button" class="btn" data-stub="Удаление аккаунта появится вместе с базой данных (ФТ-19)">Удалить аккаунт</button>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
