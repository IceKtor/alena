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
      <h1>Каталог фотографов Якутии</h1>
      <p class="lead">«Объектив» — место, где фотографы выкладывают работы, а заказчики выбирают исполнителя по снимкам, а не по описаниям. Размещение бесплатное, комиссий нет: договариваетесь вы сами.</p>
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
    <dl class="stats">
      <div class="stat"><dt class="muted">фотографов в каталоге</dt><dd class="stat__num"><?= $stats['photographers'] ?></dd></div>
      <div class="stat"><dt class="muted">работ в ленте</dt><dd class="stat__num"><?= $stats['works'] ?></dd></div>
      <div class="stat"><dt class="muted">жанров съёмки</dt><dd class="stat__num"><?= $stats['genres'] ?></dd></div>
      <div class="stat"><dt class="muted">стоит размещение</dt><dd class="stat__num">0 ₽</dd></div>
    </dl>
    <p class="note">Пока это тестовые данные; в рабочей версии числа считаются по базе.</p>
  </div>
</section>

<section class="section" id="rules">
  <div class="container">
    <div class="audience-cols">
      <article class="audience">
        <h3>Если вы ищете фотографа</h3>
        <p>Выберите город и жанр, укажите, сколько готовы потратить, и посмотрите работы. Понравился стиль — напишите прямо из профиля. Регистрироваться для этого не нужно.</p>
        <p>Хотите вернуться к кому-то позже — добавьте его в избранное (для этого нужна регистрация заказчика).</p>
      </article>
      <article class="audience">
        <h3>Если вы фотограф</h3>
        <p>Заведите профиль, загрузите работы, укажите цены «от» и свободные даты. Заявки собираются в личном кабинете, там же видно, сколько человек смотрели ваш профиль.</p>
        <p>Загружайте только то, что снимали сами или на что у вас есть права.</p>
      </article>
    </div>
    <p class="note">Платформа только знакомит фотографа и заказчика: она не участвует в расчётах, не отвечает за качество и сроки и не заменяет договор. Цены — ориентировочные.</p>
  </div>
</section>

<section class="section" id="contacts">
  <div class="container about-grid">
    <div class="card card--pad">
      <h2 class="h3">Контакты</h2>
      <!-- ЗАГЛУШКА: контакты условные, заменить реальными -->
      <ul class="contacts">
        <li><?= icon('mail') ?><span><strong>Почта</strong><br><a href="mailto:hello@obyektiv.example">hello@obyektiv.example</a></span></li>
        <li><?= icon('phone') ?><span><strong>Телефон</strong><br>+7 900 000-00-00</span></li>
        <li><?= icon('pin') ?><span><strong>Адрес</strong><br>г. Якутск, ул. Кулаковского, 48</span></li>
        <li><?= icon('clock') ?><span><strong>Время ответа</strong><br>В течение рабочего дня</span></li>
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
