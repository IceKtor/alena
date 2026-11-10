<?php
/** Страница «О платформе» (ФТ-06). */
declare(strict_types=1);
require __DIR__ . '/server/bootstrap.php';

$stats = platform_stats();
page_start([
    'title'       => 'О платформе',
    'description' => 'Объектив — платформа, где кадры говорят за фотографа: бесплатное портфолио, поиск по жанру, городу и цене, заявки напрямую.',
    'nav'         => 'about',
]);
require __DIR__ . '/partials/header.php';
?>

<section class="hero hero--about">
  <div class="container hero__grid">
    <div class="hero__text">
      <span class="badge badge--amber">О платформе</span>
      <h1>Платформа, где кадры говорят за фотографа</h1>
      <p class="lead">Мы соединяем фотографов Якутии и тех, кто ищет исполнителя: портфолио, цены и заявки в одном месте — без посредников и комиссий.</p>
      <div class="hero__buttons">
        <a class="btn btn--primary" href="photographers.php">Найти фотографа</a>
        <button type="button" class="btn btn--ghost" data-open-modal="register">Разместить портфолио</button>
      </div>
    </div>
    <div class="hero__art hero__art--two" aria-hidden="true">
      <figure class="polaroid polaroid--a"><?= img_tag('tree.jpg', '', '', false) ?></figure>
      <figure class="polaroid polaroid--c"><?= img_tag('camera.jpg', '', '', false) ?><figcaption>Кадр</figcaption></figure>
    </div>
  </div>
</section>
<?= stripe() ?>

<section class="section">
  <div class="container">
    <h2 class="sr-only">Платформа в цифрах</h2>
    <ul class="stats">
      <li class="card stat"><strong class="stat__num"><?= $stats['photographers'] ?></strong><span class="muted">фотографов</span></li>
      <li class="card stat"><strong class="stat__num"><?= $stats['works'] ?></strong><span class="muted">работ</span></li>
      <li class="card stat"><strong class="stat__num"><?= $stats['genres'] ?></strong><span class="muted">жанров съёмки</span></li>
      <li class="card stat"><strong class="stat__num">0 ₽</strong><span class="muted">стоимость размещения</span></li>
    </ul>
    <p class="note">Значения условные и будут заменены реальными данными из базы.</p>
  </div>
</section>

<section class="section" id="rules">
  <div class="container">
    <h2>Для кого платформа</h2>
    <div class="grid grid--2">
      <article class="card card--pad audience audience--teal">
        <h3>Заказчикам</h3>
        <ol class="audience__list">
          <li>Ищите по жанру, городу, цене и дополнительным услугам.</li>
          <li>Оценивайте стиль по самим работам, а не по описаниям.</li>
          <li>Пишите фотографу напрямую — без регистрации.</li>
        </ol>
      </article>
      <article class="card card--pad audience audience--orange">
        <h3>Фотографам</h3>
        <ol class="audience__list">
          <li>Создайте страницу с портфолио за минуту и бесплатно.</li>
          <li>Указывайте услуги, цены «от» и свободные даты.</li>
          <li>Получайте заявки и следите за статистикой профиля.</li>
        </ol>
      </article>
    </div>
    <p class="note">Платформа лишь знакомит фотографа и заказчика: не участвует в расчётах, не гарантирует качество и сроки услуг и не заменяет договор. Фото в портфолио принадлежат авторам; при загрузке фотограф подтверждает наличие прав.</p>
  </div>
</section>

<section class="section" id="contacts">
  <div class="container about-grid">
    <div class="card card--pad">
      <h2 class="h3">Контакты</h2>
      <!-- ЗАГЛУШКА: контакты условные, заменить реальными -->
      <ul class="contacts">
        <li><span class="contacts__ico contacts__ico--orange"><?= icon('mail') ?></span><span><strong>Почта</strong><br><a href="mailto:hello@obyektiv.example">hello@obyektiv.example</a></span></li>
        <li><span class="contacts__ico contacts__ico--teal"><?= icon('phone') ?></span><span><strong>Телефон</strong><br>+7 900 000-00-00</span></li>
        <li><span class="contacts__ico contacts__ico--amber"><?= icon('pin') ?></span><span><strong>Адрес</strong><br>г. Якутск, ул. Кулаковского, 48</span></li>
        <li><span class="contacts__ico contacts__ico--teal"><?= icon('clock') ?></span><span><strong>Время ответа</strong><br>В течение рабочего дня</span></li>
      </ul>
    </div>

    <div class="card card--pad">
      <h2 class="h3">Написать нам</h2>
      <form class="form" data-api="server/api/contact.php" data-reset-on-success novalidate>
        <div class="field">
          <label for="c-name">Имя</label>
          <span class="input"><input id="c-name" name="name" type="text" autocomplete="name" maxlength="50" required></span>
          <p class="field__error" data-error-for="name" role="alert"></p>
        </div>
        <div class="field">
          <label for="c-email">Почта</label>
          <span class="input"><?= icon('mail') ?><input id="c-email" name="email" type="email" autocomplete="email" placeholder="name@mail.ru" required></span>
          <p class="field__error" data-error-for="email" role="alert"></p>
        </div>
        <div class="field">
          <label for="c-msg">Сообщение</label>
          <span class="input input--area"><textarea id="c-msg" name="message" rows="4" maxlength="1000" required></textarea></span>
          <p class="field__error" data-error-for="message" role="alert"></p>
        </div>
        <div class="field">
          <label class="check"><input type="checkbox" name="consent" value="1"><span class="check__box"><?= icon('check') ?></span><span>Согласен на <a href="privacy.php">обработку данных</a></span></label>
          <p class="field__error" data-error-for="consent" role="alert"></p>
        </div>
        <p class="form__error" data-form-error role="alert"></p>
        <button type="submit" class="btn btn--primary">Отправить</button>
      </form>
    </div>
  </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
